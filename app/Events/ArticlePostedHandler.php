<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ArticlePostedHandler implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $post;
    public $author;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($post, $author)
    {
        $this->post = $post;
        $this->author = $author;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {        
        $mchannels = array();
        $followers = \App\User::find($this->author->id)->__get_followers_from_redis();
        foreach ($followers as $follower) {
            $mchannels[] = new PrivateChannel('App.User.' . $follower);
        }
        return $mchannels;
        // return new PresenceChannel('App.User.' . $);
    }
}
