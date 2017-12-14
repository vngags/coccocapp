<?php
namespace App\Traits;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Helper;
use LRedis;
use App\Article;
use App\User;
use Auth;
use App\Events\ArticlePostedHandler;

trait Blogable
{
    /**
     * Chuyển ngày tháng mặc định thành dạng TimeAgo
     * @param $date
     */
    public function get_created_at($date)
    {
        $timeago = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->diffForHumans();
        $day = date('d', strtotime($date));
        $month = date('m', strtotime($date));
        $year = date('Y', strtotime($date));
        $time = date('h:i', strtotime($date));
        $date_time = date('Y-m-d', strtotime($date));
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', time()-86400);
        if($date_time == $today) {
            return Helper::timeago_en_vi($timeago);
        }elseif($date_time == $yesterday) {
            return 'Hôm qua lúc ' . $time;
        }else{
            if($year == date("Y")) {
             $res = $day . ' tháng ' . $month . ' lúc ' . $time;
             return $res;
            }
            $resp = $day . ' tháng ' . $month . ' năm ' . $year . ' lúc ' . $time;
            return $resp;
        }
    }


    /**
     * Hàm ghi dữ liệu create article,
     * nếu tồn tại thì cập nhật, ngược lại thì tạo mới
     * @param array data => request->all()
     */
    private static function __store($data) {
        $s = Helper::extract_id_slug($data['slug']);
        $post = Article::where('id', $s['id'])
                ->where('slug', $s['slug'])
                ->where('user_id', Auth::user()->id)
                ->first();
        if($post) {
            return self::update_post($post, $data);
        }else{
            return self::create_post($data);
        }
    }

    /**
     * Cập nhật dữ liệu bài viết
     * @param $post : Article::find()
     * @param $data: request->all()
     */
    private static function update_post($post, $data)
    {
        $post->update([
            'title' => $data['title'],
            'slug' => str_slug($data['title']),
            'content' => $data['content'],
            'thumbnail' => $data['thumbnail'] ? Helper::save_image_version(293, 184, $data['thumbnail']) : '',
        ]);
        if($data['attachments'] && count($data['attachments']) > 0) {
            $post->attachments()->sync($data['attachments']);
        }        


        //Xóa bản nháp trong redis
        self::redis_del_key(Auth::user()->id . '_draft');
        //Sau khi xóa bản nháp cần thêm hoặc sửa dữ liệu vào cache redis
        self::add_post_to_redis($post->id);
        //Thêm post mới vào danh sách dành cho User
        self::store_post_by_user_to_redis(Auth::user()->id, $post->id);
        LRedis::ZADD('articles_by_date', strtotime($post->created_at), 'article_' . $post->id);
        return response()->json([
            'status' => 'success',
            'url' => $post->slug . '-' . $post->id . '.html'
        ]);
    }


    /**
     * Tạo mới bài viết
     * @param $data : request->all()
     */
    private static function create_post($data) {
        $post = Article::create([
            'user_id' => Auth::user()->id,
            'title' => $data['title'],
            'slug' => str_slug($data['title']),
            'content' => $data['content'],
            'thumbnail' => $data['thumbnail'] ? Helper::save_image_version(293, 184, $data['thumbnail']) : '',
            'status' => 0
        ]);
        if($data['attachments'] && count($data['attachments']) > 0) {
            $post->attachments()->sync($data['attachments']);
        }
        //Send notifications
        $user = \App\User::find($post->user_id)->_simple_user_index();     
        $author = json_decode($user);   
        broadcast(new ArticlePostedHandler($post, $author))->toOthers();

        //Xóa bản nháp trong redis
        self::redis_del_key(Auth::user()->id . '_draft');
        //Sau khi xóa bản nháp cần thêm hoặc sửa dữ liệu vào cache redis
        self::add_post_to_redis($post->id);
        //Thêm post mới vào danh sách dành cho User
        self::store_post_by_user_to_redis(Auth::user()->id, $post->id);
        LRedis::ZADD('articles_by_date', strtotime($post->created_at), 'article_' . $post->id);
        return response()->json([
            'status' => 'success',
            'url' => $post->slug . '-' . $post->id . '.html'
        ]);
    }

    /**
     * Delete redis key if exists
     * @param redis_key ex: 1_draft
     */
    private static function redis_del_key($key) {
        if(LRedis::EXISTS($key)) {
            LRedis::DEL($key);
        }
    }

    private static function store_post_by_user_to_redis($user_id, $post_id)
    {
        LRedis::ZADD('articles_by_user:' . $user_id, $post_id, 'article_' . $post_id);
    }

    /**
     * @param $post
     */
    private static function add_post_to_redis($id) {
        $article = Article::with(array('user'=>function($query){
            $query->select('id','name','avatar', 'slug', 'gender');
        }))->where('id', $id)->first();
        $attachments = $article->attachments;
        LRedis::HSET('articles_cached', 'article_' . $id, json_encode($article));
        LRedis::ZADD('articles_sort_by_id', $id, 'article_' . $id);
    }


    /**
     * @param: key of redis ex: article_1
     */
    private static function buildPost($key) {
        $article = LRedis::HGET('articles_cached', $key);
        if(!self::hash_exists('articles_cached', $key)) {
            $article = self::buildPostFromSql($key);
        }
        $value = json_decode($article);

        $views = (LRedis::ZSCORE('articleView', $key) != null) ? LRedis::ZSCORE('articleView', $key) : 0;
        $count_articles = LRedis::ZCARD('articles_by_user:' . $value->user->id);
        $comments = LRedis::HSCAN('comments', 0, 'MATCH', 'comment:' . $value->id . ":*:*");
        $comments = count($comments[1]);
        $user = User::find($value->user->id)->_private_index(false, true);
        $user = json_decode($user);
        //like post
        $likes = self::__get_likes_of_post($value->id);
        $data = array();
        array_push($data, [
            'id' => $value->id,
            'title' => $value->title,
            'slug' => $value->slug,
            'content' => $value->content,
            'thumbnail' => $value->thumbnail,
            'status' => $value->status,
            'created_at' => $value->created_at,
            'updated_at' => $value->updated_at,
            'user' => $user,
            'attachments' => $value->attachments,
            'views' => $views,
            'articles_count' => $count_articles,
            'comments' => $comments,
            'likes' => $likes
        ]);
        return json_encode($data[0]);
    }

    /**
     * @param article_1
     */
     private static function buildPostFromSql($key)
     {
         $id = explode('_', $key)[1];
         $article = Article::with('user')->where('id', $id)->first();
         $attachments = $article->attachments; //Thêm dòng này sẽ show ra pivot table
         return json_encode($article);
     }


     /**
      * get redis by key
      */
      static private function get_redis_by_key($key)
      {
          if(LRedis::EXISTS($key)) {
              return LRedis::GET($key);
          }
      }

      static private function redis_key_exists($key)
      {
          if(LRedis::EXISTS($key)) {
              return 1;
          }else{
              return 0;
          }
      }

      static private function redis_del($key)
      {
          return self::redis_del_key($key);
      }

      static private function hash_exists($key, $field) {
          if(LRedis::HEXISTS($key, $field)) {
              return 1;
          }else{
              return 0;
          }
      }

      static private function hash_get($key, $field)
      {
          return LRedis::HGET($key, $field);
      }

      static private function article_is_exists($post_id) {
          if(self::hash_exists('articles_cached', 'article_' . $post_id) == 1) {
              return 1;
          }else{
              return 0;
          }
      }

      static private function __like_post($post_id) {
          //Check is_liked
          if(self::article_is_exists( $post_id) == 1) {
              if(LRedis::EXISTS('article_likes:' . $post_id) &&
                    LRedis::EXISTS('user_likes:' . Auth::user()->id, $post_id) &&
                    LRedis::SISMEMBER('article_likes:' . $post_id, Auth::user()->id) &&
                    LRedis::SISMEMBER('user_likes:' . Auth::user()->id, $post_id)) {

                  LRedis::SREM('article_likes:' . $post_id, Auth::user()->id);
                  LRedis::SREM('user_likes:' . Auth::user()->id, $post_id);
                  return [
                      'status' => 'disliked',
                      'likes' => self::__get_likes_of_post($post_id)
                  ];

              }else{
                  LRedis::SADD('article_likes:' . $post_id, Auth::user()->id);
                  LRedis::SADD('user_likes:' . Auth::user()->id, $post_id);
                  //Notification
                  $post = LRedis::HGET('articles_cached', 'article_' . $post_id);

                  User::find(json_decode($post)->user_id)->notify(new \App\Notifications\Liked(Auth::user(), json_decode($post)));
                  return [
                      'status' => 'liked',
                      'likes' => self::__get_likes_of_post($post_id)
                  ];
              }
          }else{
              return [
                    'status' => 'error'
              ];
          }

      }


      static private function __get_likes_of_post($post_id) {
            if(self::article_is_exists( $post_id) == 1) {
                  $user_ids = LRedis::SMEMBERS('article_likes:' . $post_id);//return [1,2,3]
                  $users = array();
                  foreach ($user_ids as $id) {
                      $user = LRedis::HGET('users', 'user:' . $id);
                      $j_user = json_decode($user);
                      $users = Helper::buildUser($j_user, 1);
                  }
                  return $users;
            }else{
                return ['status' => 'error'];
            }
      }

      static private function __get_likes_of_user($user_id) {
          $articles_ids = LRedis::SMEMBERS('user_likes:' . $user_id);
          $articles = array();
          foreach ($articles_ids as $id) {
              $article = LRedis::HGET('articles_cached', 'article_' . $id);
              array_push($articles, json_decode($article));
          }
          return $articles;
      }
}
