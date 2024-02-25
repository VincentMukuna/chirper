<?php

namespace App\Notifications;

use App\Models\Chirp;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ReplyChirp extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Chirp $reply)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->reply->user->name." replied to your chirp : ".Str::limit($this->reply->inReplyTo()->first()->message, 20))
            ->greeting("Reply from ".$this->reply->user->name)
            ->line(Str::limit($this->reply->message, 50))
            ->action('Go to Chirp', url(route('chirps.show',[
                'chirp'=>$this->reply->inReplyTo()->first()->id
            ])))
            ->line('Thank you for using our application!');
    }

    public function toDatabase(object $notifiable):array
    {
        return [
            'replier'=>$this->reply->user,
            'chirp'=>$this->reply->inReplyTo()->first(),
            'reply'=>$this->reply
        ];

    }

    public function shouldSend(object $notifiable): bool
    {
        return $notifiable->id === $this->reply->inReplyTo()->first()->user->id &&
            $notifiable->id !== $this->reply->user->id;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
