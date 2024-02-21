<?php

namespace App\Notifications;

use App\Models\Chirp;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class RechirpChirp extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Chirp $chirp,
        public Chirp $rechirp,
        public User $chirper,
        public User $rechirper
    )
    {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Rechirp from {$this->rechirper->name}")
            ->greeting(
                "{$this->rechirper->name} has rechirped your chirp")
            ->line(Str::limit($this->chirp->message, 50))
            ->action('Go to chirp', url(route('chirps.show', ['chirp'=>$this->chirp->id])))
            ->line('Thank you for using our application!');
    }

    public function toDatabase(): array
    {

        return [
            'rechirp'=>$this->rechirp,
            'rechirper'=>$this->rechirper
        ];
    }

    public function shouldSend(object $notifiable):bool
    {
        return $notifiable->id===$this->chirper->id&&
            $notifiable->id!==$this->rechirper->id;
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
