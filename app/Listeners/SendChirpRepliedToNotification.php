<?php

namespace App\Listeners;

use App\Events\ChirpRepliedTo;
use App\Models\User;
use App\Notifications\LikeChirp;
use App\Notifications\ReplyChirp;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendChirpRepliedToNotification
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
    public function handle(ChirpRepliedTo $event): void
    {
        $user = User::find($event->originalChirp->user_id);
        $user->notify(new ReplyChirp($event->originalChirp,$event->reply, $event->replier));
    }
}
