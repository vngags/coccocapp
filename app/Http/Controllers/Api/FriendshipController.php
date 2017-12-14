<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Auth;

class FriendshipController extends Controller
{
    public function check_follow(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|numeric'
        ]);
        if(Auth::user()->id != $request->user_id) {
            if(User::find(Auth::user()->id)->__is_following_in_redis($request->user_id) == 1) {
                return 'following';
            }else{
                return 0;
            }
        }
        return 'error';
    }

    public function add_follow(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|numeric'
        ]);
        $fl = User::find(Auth::user()->id)->__add_follow($request->user_id);
        User::find($request->user_id)->notify(new \App\Notifications\NewFollow(Auth::user()));
        return $fl;
    }

    public function remove_follow(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|numeric'
        ]);
        if(User::find(Auth::user()->id)->__is_following_in_redis($request->user_id) == 1) {
            return User::find(Auth::user()->id)->__remove_follow($request->user_id);
        }
        return 0;
    }
}
