<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BlogAttachment;
use LRedis;
use App\Blog;
use Carbon\Carbon;
use Cache;
use DB;
use Auth;
use Helper;

class BlogController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except('show');
    }

    public function create()
    {
        $result = LRedis::SCAN(0, 'match', 'draft:' . Auth::user()->id . ':*');
        if($result) {
            $slug = '';
            foreach ($result[1] as $key => $value) {
                if($key == 0) {
                    $parts = explode(':',$value);
                    $slug = array_pop($parts);
                }
            }
            return view('blogs.edit')->withSlug($slug)->withDraft(true);
        }
        return view('blogs.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'content' => 'required|max:20000',
            'slug' => 'required',
            'attachment_ids' => 'nullable|array'
        ]);
        $thumbnail = '';
        $post = '';
        if($request->thumbnail) {
            $thumbnail = $request->thumbnail;
        }
        //Check post exists
        $data = Helper::extract_id_slug($request->slug);
        $check = Blog::where('slug', $data['slug'])->where('id', $data['id'])->first();
        if($check) {
            $check->update([
                'user_id' => $request->user()->id,
                'title' => $request->title,
                'slug' => str_slug($request->title),
                'content' => $request->content,
                'thumbnail' => $request->thumbnail ? Helper::save_image_version(293, 184, $thumbnail) : '',
                'status' => 0
            ]);
            //Save attachment_ids to blog_attachments table
            if($request->attachment_ids && count($request->attachment_ids) > 0) {
                $check->attachments()->sync($request->attachment_ids);
            }
            //Save to Redis
            $id = $request->user()->id;
            LRedis::DEL('draft:' . $id . ':' . $request->slug);
            //create new redis post
            $url = $check->slug . '-' . $check->id . '.html';
            //Luu cache voi hieu luc 7 ngay
            $expiresAt = Carbon::now()->addDays(7);
            Cache::add('post:'.$url, $check, $expiresAt);
            //Clear all cache of HOME
            Cache::pull('blog_posts_cache');
            //$redis->set('post:' . $post->slug . '-' . $post->id . '.html', $post);
            return response()->json([
                'status' => 'success',
                'url' => $url
            ]);
        }else{
            $post = Blog::create([
                'user_id' => $request->user()->id,
                'title' => $request->title,
                'slug' => str_slug($request->title),
                'content' => $request->content,
                'thumbnail' => $request->thumbnail ? Helper::save_image_version(293, 184, $thumbnail) : '',
                'status' => 0
            ]);
            if($post) {
                //Save attachment_ids to blog_attachments table
                if($request->attachment_ids && count($request->attachment_ids) > 0) {
                    foreach ($request->attachment_ids as $attach_id) {
                        $is_attach_exits = \App\Attachment::find($attach_id);
                        if($is_attach_exits) {
                            BlogAttachment::create([
                                'blog_id' => $post->id,
                                'attachment_id' => $attach_id
                            ]);
                        }
                    }
                }
                //Save to Redis
                $id = $request->user()->id;
                LRedis::DEL('draft:' . $id . ':' . $request->slug);
                //create new redis post
                $url = $post->slug . '-' . $post->id . '.html';
                //Luu cache voi hieu luc 7 ngay
                $expiresAt = Carbon::now()->addDays(7);
                Cache::add('post:'.$url, $post, $expiresAt);
                //Clear all cache of HOME
                Cache::pull('blog_posts_cache');
                //$redis->set('post:' . $post->slug . '-' . $post->id . '.html', $post);
                return response()->json([
                    'status' => 'success',
                    'url' => $url
                ]);
            }
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Có lỗi trong quá trình đăng tin'
        ], 401);
    }

    public function draft(Request $request)
    {
        $this->validate($request, [
            'title' => 'nullable|max:255',
            'content' => 'nullable|max:20000',
        ]);
        $slug = str_random(16);
        $id = $request->user()->id;
        LRedis::SET('draft:' . $id . ':' . $slug, json_encode($request->all()));

        return response()->json([
            'data' => json_decode(LRedis::GET('draft:' . $id . ':' . $slug)),
            'slug' => $slug
        ]);
    }

    public function update(Request $request, $slug)
    {
        $this->validate($request, [
            'title'          => 'nullable|max:255',
            'content'        => 'nullable|max:20000',
            'attachment_ids' => 'nullable|array'
        ]);
        $id = $request->user()->id;
        LRedis::SET('draft:' . $id . ':' . $slug, json_encode($request->all()));
        return response()->json([
            'data' => json_decode(LRedis::GET('draft:' . $id . ':' . $slug)),
            'slug' => $slug
        ]);
    }


    public function get_draft(Request $request, $slug)
    {
        $id = $request->user()->id;
        $post = LRedis::GET('draft:' . $id . ':' . $slug);
        return $post;
    }

    public function show_draft($slug)
    {
        if(Auth::check() && LRedis::EXISTS('draft:'.Auth::user()->id.':'.$slug)) {
            return view('blogs.edit')->withSlug($slug)->withDraft(true);
        }
        return redirect('/');
    }

    public function show($slug)
    {
        DB::connection()->enableQueryLog();
        $post = Blog::fetch($slug);
        $log = DB::getQueryLog();
        // print_r($log);
        return view('blogs.show')->withPost($post);
    }

    public function get_edit(Request $request, $slug)
    {
        $id = $request->user()->id;
        $post = Blog::fetch($slug);
        $attachment_ids = BlogAttachment::where('blog_id', $post->id)->get()->pluck('attachment_id');

        $data = [
            'title' => $post->title,
            'content' => $post->content,
            'attachment_ids' => $attachment_ids
        ];
        LRedis::SET('draft:' . $id . ':' . $slug, json_encode($data));
        return view('blogs.edit')->withSlug($slug)->withDraft(0);

    }

    public function post_edit(Request $request, $slug)
    {
        $id = $request->user()->id;
        $post = LRedis::GET('draft:' . $id . ':' . $slug);
        return $post;
    }


}
