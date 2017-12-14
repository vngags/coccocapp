<?php
namespace App\Traits;
use App\Friendship;
use LRedis;
use App\User;

trait Friendable
{
    function __add_follow($user_id)
    {
        if($this->__is_following($user_id) || $this->__is_following_in_redis($user_id)) {
            return 0;
        }
        if($user_id === $this->id) {
            return 0;
        }
        $following = Friendship::create([
            'follower' => $this->id,
            'following' => $user_id
        ]);
        if($following) {
            $this->__add_follow_to_redis($user_id);
            return 1;
        }
        return 0;
    }

    public function __is_following($user_id)
    {
        $following = Friendship::where('follower', $this->id)
                            ->where('following', $user_id)
                            ->first();
        if($following) {
            return 1;
        }
        return 0;
    }

    public function __remove_follow($user_id)
    {
        if($this->__is_following($user_id)) {
            $following = Friendship::where('follower', $this->id)
                        ->where('following', $user_id)
                        ->first();
            if($following) {
                $following->delete();
                $this->__remove_follow_from_redis($user_id);
                return 1;
            }
            return 0;
        }
        return 0;
    }

    public function __followings()
    {
        $followings = Friendship::where('follower', $this->id)->get();
        return $followings;
    }

    public function __followers()
    {
        return Friendship::where('following', $this->id)->get();
    }

    public function __add_follow_to_redis($user_id)
    {
        LRedis::SADD('following:' . $this->id, $user_id);
        LRedis::SADD('follower:' . $user_id, $this->id);
    }

    public function __remove_follow_from_redis($user_id)
    {
        LRedis::SREM('following:' . $this->id, $user_id);
        LRedis::SREM('follower:' . $user_id, $this->id);
    }

    public function __get_followings_from_redis()
    {
        return LRedis::SMEMBERS('following:' . $this->id);
    }

    public function __get_followers_from_redis_by_user($user_id)
    {
        return LRedis::SMEMBERS('follower:' . $user_id);
    }

    public function __get_followers_from_redis()
    {
        return LRedis::SMEMBERS('follower:' . $this->id);
    }

    public function __is_following_in_redis($user_id)
    {
        if(LRedis::SISMEMBER('following:'. $this->id, $user_id)) {
            return 1;
        }
        return 0;
    }

    public function __get_followings_together($user_id)
    {
        return LRedis::SINTER('following:' . $this->id, 'following:' . $user_id);
    }


}
