<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Events\MessagePosted;
use App\Events\MessageTyping;

class ChatController extends Controller
{

    public function get_messages(Request $request, $user_code)
    {
        return $request->user()->__get_messages($user_code);
    }

    public function store(Request $request, $user_code)
    {
        $this->validate($request, [
            'message' => 'required|max:5000'
        ]);
        //Send Notifications to $user_code;
        $user = User::where('code', $user_code)->first();
        broadcast(new MessagePosted($request->message, [
            'sender' => \Auth::user(),
            'reciept' => $user
        ]))->toOthers();
        return $request->user()->__store_message($user_code, $request->message);
    }

    public function get_users(Request $request)
    {
        $user = json_decode($request->user()->_index());
        $member = array_merge($user->followings, $user->followers);
        $member = array_unique($member);
        $users = array();
        foreach ($member as $key) {
            array_push($users, json_decode(User::find($key)->_private_index(1)));
        }
        return $users;
    }

    public function get_user($code)
    {
        $user = User::where('code', $code)->first();
        return $user->_private_index(1);
    }

    public function typing(Request $request)
    {
        $this->validate($request, [
            'user_code' => 'required|numeric'
        ]);
        $user = User::where('code', $request->user_code)->first();
        broadcast(new MessageTyping('typing', [
            'sender' => \Auth::user(),
            'reciept' => $user
        ]))->toOthers();
    }

}
