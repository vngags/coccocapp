<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Article;
use App\User;
use Auth;
use Cache;
use LRedis;
use Helper;
use Input;
use App\Profile;

class ProfileController extends Controller
{
    public function index(Request $request, $slug)
    {
        $user = User::where('slug', $slug)->first();
        $posts = $user->_get_articles(0, -1);
        //$user = json_decode($user->_index());
        $user = json_decode($user->_private_index(1));
        if($user) {
            if($request->ajax()) {
                return [
                    'user' => view('auth.ajax_author_popup')->with(compact('user'))->render(),
                ];
            }else{
                // $posts = Helper::get_articles_by_user($user->id, 0, -1);
                return view('auth.profile')->withUser($user)->withPosts($posts);
            }
        }
        return redirect('/');
    }

    public function members()
    {
        $members = Profile::_fetchAll();
        return view('auth.members')->withMembers($members);
    }

    public function update(Request $request)
    {
        $type = Input::get('type');
        $this->validate($request, [
            'quote' => 'nullable|max:255',
            'facebook_url' => 'nullable|max:255',
            'phone_number' => 'nullable|max:15',
        ]);

        $profile = Profile::_update($request->all(), $type);
        $request->user()->_update_user_to_redis();
        
        return redirect()->back();
    }

}
