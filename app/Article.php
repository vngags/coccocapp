<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Helper;
use LRedis;
use App\Traits\Blogable;
use App\User;
use Auth;

class Article extends Model
{
    use Blogable;
    protected $fillable = ['id', 'user_id', 'title', 'slug', 'content', 'thumbnail', 'status'];
    protected $hidden = ['pivot'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachments()
    {
        return $this->belongsToMany(Attachment::class)->withPivot('attachment_id');
    }

    public function getCreatedAtAttribute($date)
    {
        //  return $this->get_created_at($date);
        return $date;
    }

    /**
     * Fetch All records
     */
    static public function fetchAll()
    {
        return self::with(array('user'=>function($query){
            $query->select('id','name','avatar', 'slug');
        }))->orderBy('created_at', 'desc')->get();
    }

    /**
     * fetch record by slug and id
     */
    static public function fetch($slug)
    {
        $data = Helper::extract_id_slug($slug);
        return json_decode(self::buildPost('article_' . $data['id']));
        // if(self::hash_exists('articles_cached', 'article_' . $data['id'])) {
        //     //return self::hash_get('articles_cached', 'article_' . $data['id']);
        //     return self::buildPost('article_' . $data['id']);
        // }else{
        //     $post = self::with(array('user'=>function($query){
        //                 $query->select('id','name','avatar', 'slug', 'gender');
        //             }))
        //             ->where('id', $data['id'])
        //             ->where('slug', $data['slug'])
        //             ->firstOrFail();
        //     $attachments = $post->attachments; //Thêm dòng này sẽ show ra pivot table
        //     return $post;
        // }
    }

    static public function fetchById($id)
    {
        return self::buildPost('article_' . $id);
    }

    static public function _buildPost($key)
    {
        return self::buildPost($key);
    }

    /**
     * fetch all records but get fer paginate
     */
    static public function fetchPaginate($start = 0, $type = null)
    {
        // return LRedis::HVALS('articles_cached');

        $end = $start + 10;
        if($type == 'views') {
            $posts = LRedis::ZREVRANGE('articleView', $start, $end);
            $total = LRedis::ZCARD('articleView');
        }elseif($type === 'this_week') {
            $this_week = Helper::this_week_range();
            $key = 'WEEK_VIEW:' . $this_week['start'] . ':' . $this_week['end'];
            $posts = LRedis::ZREVRANGE($key, $start, $end);
            $total = LRedis::ZCARD($key);

            if($total == 0) {
                $last_week = Helper::last_week_range();
                $key_last_week = 'WEEK_VIEW:' . $last_week['start'] . ':' . $last_week['end'];
                $posts = LRedis::ZREVRANGE($key_last_week, $start, $end);
                $total = LRedis::ZCARD($key_last_week);
            }
        }elseif($type === 'this_month') {
            $data = Helper::get_this_month();
            $key = 'MONTH_VIEW:' . $data['start'] . ':' . $data['end'];
            $posts = LRedis::ZREVRANGE($key, $start, $end);
            $total = LRedis::ZCARD($key);
        }elseif($type === 'feed') {
            $u1 = array();
            $u2 = array();
            $followers = User::find(Auth::user()->id)->__get_followers_from_redis();
            foreach ($followers as $follower) {
                array_push($u1, $follower);
            }
            foreach ($u1 as $f) {
                $u2 = User::find($f)->__get_followings_from_redis();
            }
            $u = array_merge($u1, $u2);
            $p = self::with(array('user'=>function($query){
                $query->select('id','name','avatar', 'slug');
            }))
            ->whereIn('user_id', $u)
            ->orderBy('created_at', 'desc')->paginate(10);
            $posts = array();
            foreach ($p->pluck('id') as $value) {
                array_push($posts, 'article_' . $value);
            }
            $total = $p->total();
        }else{
            $posts = LRedis::ZREVRANGE('articles_sort_by_id', $start, $end);
            $total = LRedis::ZCARD('articles_sort_by_id');
        }
        $arr = array();
        $arr['data'] = array();
        foreach ($posts as $key) {
            array_push($arr['data'], json_decode(self::buildPost($key)));
        }
        $arr['cursor'] = ($end + 1) < $total ? $end + 1 : $total;
        $arr['total'] = $total;
        return $arr;

        // $scan = LRedis::HSCAN('articles_cached', $cursor, 'MATCH', 'article_*', 'count', 5);
        // return $scan;

        // return self::with(array('user'=>function($query){
        //     $query->select('id','name','avatar', 'slug');
        // }))->orderBy('created_at', 'desc')->paginate(5);
    }

    /**
     * Submit data
     */
     static public function _store($data)
     {
        return self::__store($data);
     }

     /**
      * like_post
      */
      static public function _like_post($post_id)
      {
          return self::__like_post($post_id);
      }

      static public function _get_likes_of_post($post_id)
      {
          return self::__get_likes_of_post($post_id);
      }

     /**
      * Update method
      */
      static public function _update_to_redis($data)
      {
          $uid = \Auth::user()->id;
          LRedis::SET($uid . '_draft', json_encode($data));
          return response()->json([
              'data' => json_decode(LRedis::GET($uid . '_draft'))
          ]);
      }



}
