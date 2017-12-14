<?php

namespace App\Events;

use App\Article;
use LRedis;
use Helper;
use Illuminate\Session\Store;

class ViewPostHandler
{
    private $session;

    public function __construct(Store $session)
    {
        $this->session = $session;
    }

    public function handle($post)
    {
        if(!$this->isPostViewed($post)) {
            $this->storePost($post);
        }else{
            $timestamp = $this->getTimeViewed($post);
            if($timestamp != null) {
                if ($timestamp <= strtotime('-1 hour')) {
                    $this->storePost($post);
                }
            }
        }
    }


    private function isPostViewed($post)
    {
        $viewed = $this->session->get('viewed_posts', []);
        return array_key_exists($post->id, $viewed);
    }

    private function getTimeViewed($post)
    {
        $viewed = $this->session->get('viewed_posts', []);
        if(array_key_exists($post->id, $viewed)) {
            return $viewed[$post->id];
        }
    }

    private function storePost($post)
    {
        $key = 'viewed_posts.' . $post->id;
        $this->session->put($key, time());
        LRedis::ZINCRBY('articleView', 1, 'article_' . $post->id);
        //Save to this_week
        $this_week = Helper::this_week_range();
        $this_week_key = 'WEEK_VIEW:' . $this_week['start'] . ':' . $this_week['end'];
        LRedis::ZINCRBY($this_week_key, 1, 'article_' . $post->id);
        //Save to this month
        $this_month = Helper::get_this_month();
        $this_month_key = 'MONTH_VIEW:' . $this_month['start'] . ':' . $this_month['end'];
        LRedis::ZINCRBY($this_month_key, 1, 'article_' . $post->id);
    }

}
