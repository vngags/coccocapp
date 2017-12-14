<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Support\Facades\Redis;
class RefreshRedis
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::check()) {
            $id = Auth::user()->id;
            $browser = $request->server('HTTP_USER_AGENT');
            $namespace = 'users:' . $id . '_' . $browser;

            $expire = config('session.lifetime') * 60;
            Redis::EXPIRE($namespace, $expire);
        }
        return $next($request);
    }
}
