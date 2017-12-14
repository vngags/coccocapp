<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\Friendable;
use App\Traits\Chatable;
use Helper;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, Friendable, Chatable;

    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'gender', 'slug', 'code'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function _index()
    {
        $user = array();
        if(\LRedis::HEXISTS('users', 'user:' . $this->id)) {
            $user = json_decode(\LRedis::Hget('users', 'user:' . $this->id), true);
        }else{
            $user = $this->with('profile')->select('id', 'name', 'slug', 'code', 'avatar', 'gender')->where('id', $this->id)->firstOrFail();
        }
        $followings = $this->__get_followings_from_redis();
        $followers = $this->__get_followers_from_redis();
        $user['followers'] = $followers;
        $user['followings'] = $followings;
        return json_encode($user);
    }

    public function _get_index($id) 
    {
        $user = array();
        if(\LRedis::HEXISTS('users', 'user:' . $id)) {
            $u = json_decode(\LRedis::Hget('users', 'user:' . $id), true);
            $user = [
                'id' => $u['id'],
                'name' => $u['name'],
                'slug' => $u['slug'],
                'code' => $u['code'],
                'gender' => $u['gender'],
                'avatar' => $u['avatar'],
                'profile' => $u['profile']
            ];
        }else{
            $user = $this->with('profile')->select('id', 'name', 'slug', 'code', 'avatar', 'gender')->where('id', $id)->firstOrFail();
            \LRedis::HSET('users', 'user:' . $user->id, json_encode($user));
        }

        return $user;
    }

    public function _private_index($follow_detail = false, $profile_detail = false)
    {
        $user = $this->_get_index($this->id);
        $followings = $this->__get_followings_from_redis();
        $followers = $this->__get_followers_from_redis();
        if($follow_detail != false) {
            $f1 = array();
            $f2 = array();
            foreach ($followings as $following) {
                $f1[] = $this->_get_index($following);
            }
            foreach ($followers as $follower) {
                $f2[] = $this->_get_index($follower);
            }
            $user['followers'] = $f2;
            $user['followings'] = $f1;
            $user['followers_ids'] = $followers;
            $user['followings_ids'] = $followings;
        }else{
            $user['followers'] = $followers;
            $user['followings'] = $followings;
        }
        
        if($profile_detail == false) {
            $user['profile'] = null;
        }else{
            $user['profile'] = [
                'quote' => $user['profile']['quote'],
                'facebook_url' => $user['profile']['facebook_url'],
                'phone_number' => $user['profile']['phone_number']
            ];
        }

        return json_encode($user);
    }

    public function _simple_user_index()
    {
        $user = $this->_get_index($this->id);
        $data = [
            'id' => $user['id'],
            'name' => $user['name'],
            'slug' => $user['slug'],
            'avatar' => $user['avatar']
        ];
        return json_encode($data);
    }

    public function _get_articles($start, $end)
    {
        $articles = \LRedis::ZREVRANGE('articles_by_user:' . $this->id, $start, $end);
        $posts = array();
        foreach ($articles as $value) {
            array_push($posts, json_decode(\App\Article::_buildPost($value)));
        }
        return $posts;
    }

    public function _update_user_to_redis()
    {
        $user = $this->with('profile')->where('id', $this->id)->firstOrFail();
        return \LRedis::HSET('users', 'user:' . $user->id, $user);
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'user_id');
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
}
