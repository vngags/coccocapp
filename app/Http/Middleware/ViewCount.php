<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Session\Store;
use Session;

class ViewCount
{
    private $session;

    public function __construct(Store $session)
    {
        $this->session = $session;
    }

    public function handle($request, Closure $next)
    {
        $posts = $this->getViewedPosts();
        if(!is_null($posts)) {
            $posts = $this->cleanExpiredViews($posts);
            $this->storePosts($posts);
        }
        return $next($request);
    }

    private function getViewedPosts()
    {
        return $this->session->get('viewed_posts', null);
    }

    private function cleanExpiredViews($posts)
    {
        $time = time();
        $expired = 3600;//1h

        return array_filter($posts, function($timestamp) use ($time, $expired) {
            return ($timestamp + $expired) > $time;
        });
    }

    private function storePosts($posts)
    {
        $this->session->put('viewed_posts', $posts);
        LRedis::ZINCRBY('articleView', 1, 'article_' . $posts->id);
    }
}
