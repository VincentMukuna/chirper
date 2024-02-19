<?php

namespace App\Listeners;

use App\Events\ChirpRechirped;
use App\Notifications\RechirpChirp;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendRechirpNotification
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
    public function handle(ChirpRechirped $event): void
    {
        $event
            ->chirper
            ->notify(new RechirpChirp(
                $event->chirp,
                $event->rechirp,
                $event->chirper,
                $event->rechirper
            ));
    }
}
