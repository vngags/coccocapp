<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;
use LRedis;

class MessengerController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function messenger()
    {
        return view('chats.index');
    }

    public function show($code)
    {
        return view('chats.index', ['chatter' => $code]);
    }
}
