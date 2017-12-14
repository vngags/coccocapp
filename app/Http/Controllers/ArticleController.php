<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Article;
use Auth;
use DB;
use Helper;
use CalendarHelper;
use Input;
use App\User;

class ArticleController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')
        ->except('show', 'popular', 'trending_week', 'trending_month', 'get_post_likes');
    }
    
    public function show(Request $request, $slug)
    {
        // DB::connection()->enableQueryLog();
        $data = Helper::extract_id_slug($slug);
        if($request->ajax()) {
            if(\LRedis::EXISTS(Auth::user()->id . '_draft')) {
                return \LRedis::GET(Auth::user()->id . '_draft');
            }
            $post = Article::where('id', $data['id'])->where('slug', $data['slug'])->where('user_id', $request->user()->id)->first();
            $post_attachs = $post->attachments;//To get attachments
            if($post) {
                return $post;
            }else{
                return ['status' => 'error'];
            }
        }

        $post = Article::fetch($slug);        
        if($data['slug'] === $post->slug) {
            //Add view_post to redis
            \Event::fire('posts.view', $post);
            return view('articles.show')->withPost($post);
        }else{
            return redirect('/');
        }
        // $log = DB::getQueryLog();
        // print_r($log);
    }

    public function create()
    {
        $key = Auth::user()->id . '_draft';
        if(\LRedis::EXISTS($key)) {
            $slug = json_decode(\LRedis::GET($key))->slug;
            return view('articles.edit')->withSlug($slug)->withDraft(0);
        }else{
            return view('articles.create');
        }
    }

    public function edit(Request $request, $slug)
    {
        $data = Helper::extract_id_slug($slug);
        $post = Article::where('id', $data['id'])->where('slug', $data['slug'])->where('user_id', $request->user()->id)->first();
        if($post) {
            return view('articles.edit')->withSlug($slug)->withDraft(0);
        }else{
            return redirect('/write');
        }
    }



    public function popular(Request $request)
    {
        $top_users = User::withCount('articles')->orderBy('articles_count', 'desc')->paginate(10);
        if($request->ajax()) {
            $start = Input::get('start');
            $posts = Article::fetchPaginate($start, 'views');
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
            $posts = Article::fetchPaginate(0, 'views');
            //print_r($log);
            return view('home', [
                'users_online' => $users_logged,
                'today' => $today,
                'lunar' => $date2,
                'dayofweek' => $dayofweek,
                'posts' => $posts['data'],
                'cursor' => $posts['cursor'],
                'total' => $posts['total'],
                'top_users' => $top_users,
                'trending' => 'most_view'
            ]);
        }
    }

    public function trending_week(Request $request)
    {
        $top_users = User::withCount('articles')->orderBy('articles_count', 'desc')->paginate(10);
        if($request->ajax()) {
            $start = Input::get('start');
            $posts = Article::fetchPaginate($start, 'this_week');
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
            $posts = Article::fetchPaginate(0, 'this_week');
            //print_r($log);
            return view('home', [
                'users_online' => $users_logged,
                'today' => $today,
                'lunar' => $date2,
                'dayofweek' => $dayofweek,
                'posts' => $posts['data'],
                'cursor' => $posts['cursor'],
                'total' => $posts['total'],
                'top_users' => $top_users,
                'trending' => 'week'
            ]);
        }
    }

    public function trending_month(Request $request)
    {
        $top_users = User::withCount('articles')->orderBy('articles_count', 'desc')->paginate(10);
        if($request->ajax()) {
            $start = Input::get('start');
            $posts = Article::fetchPaginate($start, 'this_month');
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
            $posts = Article::fetchPaginate(0, 'this_month');
            //print_r($log);
            return view('home', [
                'users_online' => $users_logged,
                'today' => $today,
                'lunar' => $date2,
                'dayofweek' => $dayofweek,
                'posts' => $posts['data'],
                'cursor' => $posts['cursor'],
                'total' => $posts['total'],
                'top_users' => $top_users,
                'trending' => 'month'
            ]);
        }
    }

    public function feed(Request $request)
    {
        $top_users = User::withCount('articles')->orderBy('articles_count', 'desc')->paginate(10);
        if($request->ajax()) {
            $start = Input::get('start');
            $posts = Article::fetchPaginate($start, 'feed');
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
            $posts = Article::fetchPaginate(0, 'feed');
            //print_r($log);
            return view('home', [
                'users_online' => $users_logged,
                'today' => $today,
                'lunar' => $date2,
                'dayofweek' => $dayofweek,
                'posts' => $posts['data'],
                'cursor' => $posts['cursor'],
                'total' => $posts['total'],
                'top_users' => $top_users,
                'trending' => 'month'
            ]);
        }
    }
}
