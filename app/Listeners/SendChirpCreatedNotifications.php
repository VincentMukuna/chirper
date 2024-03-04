<?php

namespace App\Listeners;

use App\Events\ChirpCreated;
use App\Models\User;
use App\Notifications\NewChirp;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendChirpCreatedNotifications implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ChirpCreated $event): void
    {
        $event
            ->chirp
            ->user
            ->followers()
            ->chunk(100, function ($followers) use ($event) {
                $followers->each(function (User $follower) use ($event) {
                    $follower->notify(new NewChirp($event->chirp));
                });
            })
        ;
    }
}
