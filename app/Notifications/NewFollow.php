<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewFollow extends Notification implements ShouldQueue
{
    use Queueable;
    public $user;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'broadcast', 'database'];
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
                    ->line($this->user->name . ' đang theo dõi bạn trên Cốc Cốc!')
                    ->action('Xem thông tin', route('user.index', ['slug' => $this->user->slug]))
                    ->line('Cảm ơn bạn đã sử dụng coccoc.me!');
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
                'name'    => $this->user->name,
                'avatar'  => $this->user->avatar,
                'slug'    => $this->user->slug,
            ],
            'message' => 'đang theo dõi bạn!',
            'url' => route('user.index', ['slug' => $this->user->slug]),
            'created_at' => date('Y-m-d H:m:s')
        ];
    }

}
