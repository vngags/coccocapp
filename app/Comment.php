<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Helper;

class Comment extends Model
{
    protected $fillable = ['id', 'comment', 'votes', 'spam', 'reply_id', 'page_id', 'user_id'];
    // protected $dates = ['created_at', 'updated_at'];

    public function getCreatedAtAttribute($date)
    {
        return $date;
    }

    public function replies()
    {
       return $this->hasMany(Comment::class,'reply_id', 'id');
    }

    public function user()
    {
       return $this->belongsTo(User::class, 'user_id');
    }

    public function comment_spam()
    {
       return $this->hasOne(CommentSpam::class, 'comment_id');
    }
}
