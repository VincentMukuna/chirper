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
    public function __construct(public Chirp $originalChirp, public Chirp $reply, public User $replier)
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
            ->subject($this->replier->name." replied to your chirp : ".Str::limit($this->originalChirp->message, 20))
            ->greeting("Reply from ".$this->replier->name)
            ->line(Str::limit($this->reply->message, 50))
            ->action('Go to Chirp', url(route('chirps.show',[
                'chirp'=>$this->originalChirp->id
            ])))
            ->line('Thank you for using our application!');
    }

    public function toDatabase(object $notifiable):array
    {
        return [
            'replier'=>$this->replier,
            'originalChirp'=>$this->originalChirp,
            'reply'=>$this->reply
        ];

    }

    public function shouldSend(object $notifiable): bool
    {
        return $notifiable->id !== $this->replier->id;
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
