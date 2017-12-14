<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Helper;
use LRedis;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'gender' => 'required|max:6'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $avatar = Helper::create_avatar_by_name($data['name']);
        $user = User::create([
            'name' => $data['name'],
            'slug' => str_slug($data['name']),
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'avatar' => $avatar,
            'gender' => $data['gender'],
            'code' => mt_rand(11111111, 99999999)
        ]);
        $profile = \App\Profile::create([
            'user_id' => $user->id
        ]);
        //Store to Redis
        $dataUser = [
            'id' => $user->id,
            'name' => $user->name,
            'slug' => $user->slug,
            'avatar' => $user->avatar,
            'gender' => $user->gender,
            'code' => $user->code,
            'profile' => $profile
        ];
        LRedis::HSET('users', 'user:' . $user->id, json_encode($dataUser));
        return $user;
    }
}
