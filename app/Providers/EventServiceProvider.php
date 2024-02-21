<?php

namespace App\Providers;

use App\Events\ChirpCreated;
use App\Events\ChirpLiked;
use App\Events\ChirpRechirped;
use App\Events\ChirpRepliedTo;
use App\Events\UserFollowed;
use App\Listeners\SendChirpCreatedNotifications;
use App\Listeners\SendChirpLikedNotification;
use App\Listeners\SendChirpRepliedToNotification;
use App\Listeners\SendNewFollowerNotification;
use App\Listeners\SendRechirpNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ChirpCreated::class => [
            SendChirpCreatedNotifications::class,
        ],
        ChirpLiked::class=>[
            SendChirpLikedNotification::class,
        ],
        ChirpRepliedTo::class=>[
            SendChirpRepliedToNotification::class,
        ],
        UserFollowed::class=>[
            SendNewFollowerNotification::class,
        ],
        ChirpRechirped::class=>[
            SendRechirpNotification::class
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
