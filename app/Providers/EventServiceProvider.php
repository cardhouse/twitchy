<?php

namespace App\Providers;

use App\Events\MessageDemoted;
use App\Events\MessagePromoted;
use App\Listeners\MessageDemotedListener;
use App\Listeners\MessagePromotedListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        MessagePromoted::class => [
            MessagePromotedListener::class,
        ],
        MessageDemoted::class => [
            MessageDemotedListener::class,
        ],
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
