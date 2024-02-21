<?php

namespace App\Notifications;

use App\Models\Chirp;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewChirp extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Chirp $chirp)
    {}

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
                    ->subject("New Chirp from {$this->chirp->user->name}")
                    ->greeting("New Chirp from {$this->chirp->user->name}")
                    ->line(Str::limit($this->chirp->message, 50))
                    ->action('Go to Chirper', url('/'))
                    ->line('Thank you for using our application!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'chirp' => $this->chirp,
            'user'=>$this->chirp->user
        ];
    }

    public function shouldSend(User $notifiable): bool
    {
        return $notifiable->isFollowing($this->chirp->user->id);
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
