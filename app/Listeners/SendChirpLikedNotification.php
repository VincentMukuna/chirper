<?php

namespace App\Listeners;

use App\Events\ChirpLiked;
use App\Models\User;
use App\Notifications\LikeChirp;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendChirpLikedNotification implements ShouldQueue
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
    public function handle(ChirpLiked $event): void
    {
      $user = User::find($event->chirp->user_id);
      $user->notify(new LikeChirp($event->chirp, $event->liker));

    }
}
