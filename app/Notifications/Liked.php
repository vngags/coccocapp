<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class Liked extends Notification
{
    use Queueable;
    public $user;
    public $post;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $post)
    {
        $this->user = $user;
        $this->post = $post;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'user' => [
                'name' => $this->user->name,
                'avatar' => $this->user->avatar,
                'slug' => $this->user->slug
            ],
            'message' => "đã thích bài viết <strong><a href=". route('article.show', ['slug' => $this->post->slug . '-' . $this->post->id]) . " target='_blank'>" . $this->post->title ."</a></strong> của bạn",
            'url' => route('article.show', ['slug' => $this->post->slug . '-' . $this->post->id]),
            'created_at' => date('Y-m-d H:m:s')
        ];
    }
}
