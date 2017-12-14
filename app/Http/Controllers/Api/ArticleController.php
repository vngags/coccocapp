<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Article;

class ArticleController extends Controller
{
    public function like_article(Request $request)
    {
        $this->validate($request, [
            'article_id' => 'required|numeric'
        ]);
        return Article::_like_post($request->article_id);
    }

    public function get_post_likes($post_id)
    {
        return Article::_get_likes_of_post($post_id);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'content' => 'required|max:20000',
            'slug' => 'required',
            'attachments' => 'nullable|array'
        ]);
        return Article::_store($request->all());
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'content' => 'required|max:20000',
            'slug' => 'required',
            'attachment_ids' => 'nullable|array'
        ]);
        return Article::_update_to_redis($request->all());
    }

    public function store_draft(Request $request)
    {
        $this->validate($request, [
            'title' => 'nullable|max:255',
            'content' => 'nullable|max:20000',
            'slug' => 'nullable|max:255',
            'attachments' => 'nullable|array',
            'thumbnail' => 'nullable|max:255'
        ]);
        $slug = '';
        if($request->slug) {
            $slug = $request->slug;
        }else{
            $slug = str_random(16);
        }
        $id = $request->user()->id;
        $data = [
            'title' => $request->title,
            'content' => $request->content,
            'slug' => $slug,
            'attachments' => $request->attachments,
            'thumbnail' => $request->thumbnail
        ];
        return Article::_update_to_redis($data);
    }

    public function delete_draft(Request $request)
    {
        $uid = $request->user()->id;
        \LRedis::DEL($uid . '_draft');
        return response()->json([
            'status' => 'removed'
        ]);
    }

    public function get_article($id)
    {
        return Article::fetchById($id);
    }

    
}
