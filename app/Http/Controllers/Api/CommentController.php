<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CommentVote;
use App\CommentSpam;
use App\Comment;
use App\Article;
use App\User;
use Auth;
use Helper;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'comment' => 'required',
            'reply_id' => 'filled',
            'page_id' => 'filled',
            'user_id' => 'required'
        ]);
        $comment = Comment::create($request->all());
        \Event::fire('store.comment', $comment);
        $commentData = [];
        if($comment) {
            $user = User::select('id', 'name', 'slug', 'avatar')
                    ->where('id', $comment->user_id)
                    ->firstOrFail();
            array_push($commentData, [
                'id' => $comment->id,
                'comment' => $comment->comment,
                'votes' => 0,
                'reply' => 0,
                'reply_id' => $comment->reply_id,
                'voteByUser' => 0,
                'vote' => 0,
                'spam' => 0,
                'replies' => [],
                'count_replies' => 0,
                'created_at' => Helper::get_created_at($comment->created_at),
                'user' => $user
            ]);
            $collection = collect($commentData);
            return [
                'status' => true,
                'new_comment' => $collection->sortBy('votes')
            ];
        }
    }

    public function index($pageId)
    {
        $comments = Comment::where('page_id', $pageId)->orderBy('created_at', 'DESC')->paginate(10);
        $post = Article::find($pageId);
        $commentsData = [];
        $commentsData['total']        = $comments->total();
        $commentsData['last_page']    = $comments->lastPage();
        $commentsData['per_page']     = $comments->perPage();
        $commentsData['current_page'] = $comments->currentPage();
        $commentsData['author_id'] = $post->user_id;
        $commentsData['data'] = [];
        foreach ($comments as $key) {
            $user = User::select('id', 'name', 'slug', 'avatar')
                    ->where('id', $key->user_id)
                    ->firstOrFail();
            $replies = $this->replies($key->id);
            $reply = 0;
            $vote = 0;
            $voteStatus = 0;
            $spam = 0;
            if(Auth::user()) {
                $voteByUser = CommentVote::where('comment_id', $key->id)
                                ->where('user_id', Auth::user()->id)
                                ->first();
                $commentSpam = CommentSpam::where('comment_id', $key->id)
                                ->where('user_id', Auth::user()->id)
                                ->first();
                if($voteByUser) {
                    $vote = 1;
                    $voteStatus = $voteByUser->vote;
                }
                if($commentSpam) {
                    $spam = 1;
                }
            }
            if(sizeof($replies) > 0) {
                $reply = 1;
            }
            if(!$spam) {
                array_push($commentsData['data'], [
                    'id' => $key->id,
                    'comment' => $key->comment,
                    'votes' => $key->votes,
                    'reply' => $reply,
                    'reply_id' => $key->reply_id,
                    'voteByUser' => $vote,
                    'vote' => $voteStatus,
                    'spam' => $spam,
                    'replies' => $replies,
                    'created_at' => Helper::get_created_at($key->created_at),
                    'user' => $user
                ]);
            }
        }
        $collect = collect($commentsData);
        return $collect->sortBy('votes');
    }

    public function replies($commentId)
    {
        $comments = Comment::where('reply_id', $commentId)->get();
        $repliesData = [];
        foreach ($comments as $key) {
            $user = User::select('id', 'name', 'slug', 'avatar')
                    ->where('id', $key->user_id)
                    ->firstOrFail();
            $vote = 0;
            $reply = 0;
            $voteStatus = 0;
            $spam = 0;
            $replies = $this->replies_to_replies($key->id);
            if(Auth::user()) {
                $voteByUser = CommentVote::where('comment_id', $key->id)
                                ->where('user_id', Auth::user()->id)
                                ->first();
                $commentSpam = CommentSpam::where('comment_id', $key->id)
                                ->where('user_id', Auth::user()->id)
                                ->first();
                if($voteByUser) {
                    $vote = 1;
                    $voteStatus = $voteByUser->vote;
                }
                if($commentSpam) {
                    $spam = 1;
                }
            }
            if(!$spam) {
                array_push($repliesData, [
                    'id' => $key->id,
                    'comment' => $key->comment,
                    'votes' => $key->votes,
                    'reply' => $reply,
                    'reply_id' => $key->reply_id,
                    'voteByUser' => $vote,
                    'vote' => $voteStatus,
                    'spam' => $spam,
                    'replies' => $replies,
                    'created_at' => Helper::get_created_at($key->created_at),
                    'user' => $user
                ]);
            }
        }
        return $repliesData;
    }

    public function replies_to_replies($commentId)
    {
        $comments = Comment::where('reply_id', $commentId)->get();
        $replies_to_replies = [];
        foreach ($comments as $key) {
            $user = User::select('id', 'name', 'slug', 'avatar')
                    ->where('id', $key->user_id)
                    ->firstOrFail();
            $vote = 0;
            $reply = 0;
            $voteStatus = 0;
            $spam = 0;
            if(Auth::user()) {
                $voteByUser = CommentVote::where('comment_id', $key->id)
                                ->where('user_id', Auth::user()->id)
                                ->first();
                $commentSpam = CommentSpam::where('comment_id', $key->id)
                                ->where('user_id', Auth::user()->id)
                                ->first();
                if($voteByUser) {
                    $vote = 1;
                    $voteStatus = $voteByUser->vote;
                }
                if($commentSpam) {
                    $spam = 1;
                }
            }
            if(!$spam) {
                array_push($replies_to_replies, [
                    'id' => $key->id,
                    'comment' => $key->comment,
                    'votes' => $key->votes,
                    'reply' => $reply,
                    'reply_id' => $key->reply_id,
                    'voteByUser' => $vote,
                    'vote' => $voteStatus,
                    'spam' => $spam,
                    'created_at' => Helper::get_created_at($key->created_at),
                    'user' => $user
                ]);
            }
        }
        return $replies_to_replies;
    }
}
