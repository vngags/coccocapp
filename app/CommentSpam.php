<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommentSpam extends Model
{
    protected $fillable = ['comment_id', 'user_id'];
    public $timestamps = false;
}
