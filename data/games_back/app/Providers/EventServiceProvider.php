<?php

namespace App\Providers;

use App\Events\EventCreate;
use App\Events\UpdateGamesRawgEvent;
use App\Events\UpdateGamesSteamEvent;
use App\Listeners\EventListener;
use App\Listeners\UpdateGamesRawgListener;
use App\Listeners\UpdateGamesSteamListener;
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
//        Registered::class => [
//            SendEmailVerificationNotification::class,
//        ],
        EventCreate::class => [
            EventListener::class,
        ],
        UpdateGamesRawgEvent::class => [
            UpdateGamesRawgListener::class,
        ],
        UpdateGamesSteamEvent::class => [
            UpdateGamesSteamListener::class,
        ],
    ];
}
