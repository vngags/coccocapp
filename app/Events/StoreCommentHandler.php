<?php

namespace App\Events;

use LRedis;

class StoreCommentHandler
{

    public function __construct()
    {
        //
    }

    public function handle($comment)
    {
        $this->storeComment($comment);
    }

    private function storeComment($comment)
    {
        if($comment->reply_id) {
            LRedis::HSET('child-comments', 'comment:' . $comment->id . ':' . $comment->user_id, json_encode($comment));
        }else{
            LRedis::HSET('comments', 'comment:' . $comment->page_id . ':' . $comment->id . ':' . $comment->user_id, json_encode($comment));
            $post = \App\Article::where('id', $comment->page_id)->first();
            \App\User::find($post->user_id)->notify(new \App\Notifications\NewComment(\Auth::user(), $post));
        }
    }

}
