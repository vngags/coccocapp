<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;
use LRedis;
use App\User;


class Profile extends Model
{
    protected $fillable = ['user_id', 'quote', 'facebook_url', 'phone_number'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    static public function _update($data, $type)
    {
        $profile = '';
        switch ($type) {
            case 'quote':
                $profile = self::where('user_id', Auth::user()->id)->first();
                if($profile) {
                    $profile->update([
                        'quote' => $data['quote']
                    ]);
                }else{
                    $profile = self::create([
                        'user_id' => Auth::user()->id,
                        'quote' => $data['quote']
                    ]);
                }
                break;
            default:
                # code...
                break;
        }
        $user = User::with('profile')->where('id', Auth::user()->id)->first();
        self::_store_user_to_redis($user);
        return $profile;
    }

    static public function _store_user_to_redis($user)
    {
        LRedis::HSET('users', 'user:' . $user->id, json_encode($user));
    }

    static public function _fetchAll($start = 0)
    {
        $members = array();
        $users = LRedis::ZREVRANGE('top_members', 0, -1);
        foreach ($users as $user) {
            $get_user = LRedis::HGET('users', $user);
            array_push($members, json_decode($get_user));
        }
        self::_sort_top_members();
        return $members;
    }

    static public function _sort_top_members($start = 0)
    {
        $users = LRedis::HSCAN('users', $start, 'MATCH', 'user:*');
        foreach ($users[1] as $user) {
            $posts = LRedis::ZCARD('articles_by_user:' . json_decode($user)->id);
            LRedis::ZADD('top_members', $posts, 'user:' . json_decode($user)->id);
        }
    }

}
