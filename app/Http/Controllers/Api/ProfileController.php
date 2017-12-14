<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Profile;

class ProfileController extends Controller
{

    public function get_user_data(Request $request)
    {
        return $request->user()->_index();
    }

    public function get_user_by_slug(Request $request, $slug)
    {
        $user = \App\User::where('slug', $slug)->first();
        return $user->_private_index();
    }

    public function get_unread(Request $request)
    {
        return $request->user()->unreadNotifications;
    }

    public function update_avatar(Request $request)
    {
        $this->validate($request, [
            'avatar' => 'required|max:50'
        ]);
        $updated = $request->user()->update([
            'avatar' => $request->avatar
        ]);
        if($updated) {
            $request->user()->_update_user_to_redis();
            return response()->json([
                'status' => 'success',
                'message' => 'You have successfully update your avatar'
            ]);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'This request has errors'
        ]);
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

        return redirect()->back();
    }
}
