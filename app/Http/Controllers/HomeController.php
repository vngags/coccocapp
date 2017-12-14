<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LRedis;
use CalendarHelper;
use Helper;
use App\Article;
use DB;
use Image;
use Input;
use App\User;
use Carbon\Carbon;
use DateTime;
use FunctionHelper;

class HomeController extends Controller
{
    // public string getClientIp(Boolean $proxy = false);

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
      //   $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

     public function fetch()
     {
         $posts = \App\Article::all();
         foreach ($posts as $post) {
             LRedis::ZADD('articles_by_date', strtotime($post->created_at), 'article_' . $post->id);
         }
     }

     public function last_week()
     {
         $last_7_days = Carbon::today()->subDays(1);
         $today = Carbon::now();
         $posts = LRedis::ZRANGEBYSCORE('articles_by_date', strtotime($last_7_days), strtotime($today));//ZRANGEBYSCORE('keys', 'start', 'end')
         return $posts;
     }

    public function index(Request $request)
    {        
        // $storage = Redis::Connection();
        // $range = $storage->zRevrange('articleView', 0, -1);
        // foreach ($range as $key => $value) {
        //     $view = $storage->get($value . ':view');
        //     echo $key+1 . ' => ' . $value . ' has view: ' . $view . "<br>";
        // }
        // $img = Image::make('https://kenh14cdn.com/2017/c7d0c3c2003df8450bf9bc978a91f391-1510547684531.jpg');
        //
        // $text = '©daison - coccoc.me';
        // $font_size = 22;
        // $size = intval(ceil($font_size * 0.75));
        // $font_file = public_path('/fonts/coccoc.ttf');
        // $box = imagettfbbox($size, 0, $font_file, $text);
        // $box['width'] = intval(abs($box[4] - $box[0] - ($box[4] * 0.228)));
        // $box['height'] = intval(abs($box[5] - $box[1]));
        //
        // $img = Image::canvas(150, 120, '#f1f1f1');

        // $watermark = Image::canvas($box['width'], $box['height'], 'rgba(0,0,0,0.02)');
        // for( $x = -1; $x <= 1; $x++ ) {
        //     for( $y = -1; $y <= 1; $y++ ) {
        //         $watermark->text($text, 3 + $x, 3 + $y, function($font) use($size) {
        //             $font->file(public_path('/fonts/coccoc.ttf'));
        //             $font->size($size);
        //             $font->color('rgba(0,0,0,0.15)'); // Glow color
        //             $font->align('left');
        //             $font->valign('top');
        //         });
        //     }
        // }
        // $watermark->text($text, 3, 3, function($font) use($size) {
        //     $font->file(public_path('/fonts/coccoc.ttf'));
        //     $font->size($size);
        //     $font->color('rgba(255,255,255,1)');
        //     $font->align('left');
        //     $font->valign('top');
        // });
        //
        // $img->insert($watermark, 'bottom-right', 5, 5);
        //
        // return $img->response('png');


        // $request = \Request::instance();
        // $request->setTrustedProxies(array('127.0.0.1')); // only trust proxy headers coming from the IP addresses on the array (change this to suit your needs)
        // $ip = $request->getClientIp();
        // dd($ip);
        // DB::connection()->enableQueryLog();
        // $log = DB::getQueryLog();
        // print_r($log);

        /////////////////////////////////////
        // $day = date('w');
        // $week_start = date('Y-m-d', strtotime('-'.$day.' days')) . ' 00:00:00';//return ngay chu nhat
        // $week_end = date('Y-m-d', strtotime('+'.(6-$day).' days')) . ' 23:23:23';//return ngay thu 7 ke tiep
        //
        // $paymentDate = strtotime(date("Y-m-d H:i:s"));
        // $contractDateBegin = strtotime($week_start);
        // $contractDateEnd = strtotime($week_end);
        //
        // if($paymentDate > $contractDateBegin && $paymentDate <= $contractDateEnd) {//from thu 2 - to thu 7
        //    echo "is between";
        // } else {
        //     echo "NO GO!";
        // }
        // dd($paymentDate);
        //////////////////////////////////////////

        // dd(Helper::get_next_month());
        //
        // $d = strtotime(date("Y-m-d H:i:s"));
        // $this_week = Helper::get_strtotime_last_month();
        // if(Helper::is_between_to_date($d, $this_week['start'], $this_week['end'])) {
        //     dd(1);
        // }else{
        //     dd(0);
        // }
        //
        //
        // $start_week = strtotime("this sunday midnight",$d);//Trả về chủ nhật tuần này
        // // $start_week = strtotime("last sunday midnight",$d); Trả về chủ nhật tuần trước
        // // $end_week = strtotime("next saturday",$d);//Trả về thứ 7 tuần sau
        // $end_week = strtotime("next saturday",$d);
        // $start = date("Y-m-d 00:00:00",$start_week);
        // $end = date("Y-m-d 23:23:23",$end_week);
        // if($d >= strtotime($start) && $d <= strtotime($end)) {//Từ chủ nhật đến thứ 7
        //     echo 'is_week';
        // }else{
        //     echo 'not week';
        // }
        // dd('start: '.$start.' - end: '.$end);

        // $user = User::all();
        // foreach ($user->pluck('id') as $value) {
        //     $posts_ids = Article::where('user_id', $value)->get()->pluck('id');
        //     foreach ($posts_ids as $id) {
        //         LRedis::ZADD('articles_by_user:' . $value, $id, 'article_' . $id);
        //     }
        // }
        // $dt = new DateTime();
        // dd($dt->format('Y-m-d H:i:s'));


        //search history chat key
        // $keys1 = LRedis::keys('private_messages:55770568:*');
        // $keys2 = LRedis::keys('private_messages:*:55770568');


        $top_users = User::withCount('articles')->orderBy('articles_count', 'desc')->paginate(5);

        if($request->ajax()) {
            $start = Input::get('start');
            $posts = Article::fetchPaginate($start);
            return [
                'posts' => view('ajax-home', ['posts' => $posts['data'], 'top_users' => $top_users])->render(),
                'cursor' => $posts['cursor'],
                'total' => $posts['total']
            ];
        }else{
            //Doi sang ngay am lich
            $today = date('d-m-Y');
            $date = CalendarHelper::convertSolar2Lunar(date("j" , strtotime($today)), date("n" , strtotime($today)), date("Y" , strtotime($today)), 7.0);
            $date2 = $date[0] . '-' . $date[1];
            $dayofweek = CalendarHelper::getWeekday($today);
            $users_logged = Helper::users_logged();
            $posts = Article::fetchPaginate(0);

            return view('home', [
                'users_online' => $users_logged,
                'today' => $today,
                'lunar' => $date2,
                'dayofweek' => $dayofweek,
                'posts' => $posts['data'],
                'cursor' => $posts['cursor'],
                'total' => $posts['total'],
                'top_users' => $top_users
            ]);
        }



    }



    public function showArticle($id)
    {
        $this->id = $id;
        $storage = LRedis::Connection();
        if($storage->zScore('articleView', 'article:' . $this->id)) {
            //Lenh zStore lay ra gia tri cua item article:id, Nếu có thì thực hiện lệnh dưới
            $storage->pipeline(function($pipe) {
                $pipe->zIncrBy('articleView', 1, 'article:' . $this->id);
                //Cộng thêm 1 giá trị vào article:id trong articleView
                $pipe->incr('article:' . $this->id . ':view');
            });
        }else{
            //Nếu không tồn tại giá trị article:id trong AritcleView thì tạo mới với giá trị của article:id
            $view = $storage->incr('article:' . $this->id . ':view');
            $storage->zIncrBy('articleView', $view, 'article:' . $this->id);
        }
        $view = $storage->get('article:' . $this->id . ':view');
        return 'This is view: ' . $this->id . ' is has ' . $view . ' views';
    }

    public function clear_cache()
    {
        return LRedis::Connection()->flushall();
    }

    public function iconfont()
    {
        return view('iconfont');
    }
}
