<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = ['id', 'user_id', 'url', 'type'];

    // public function getUrlAttribute($url)
    // {
    //     return url('images/' . $url);
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function posts()
    {
        return $this->belongsToMany(Blog::class);
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }

}
