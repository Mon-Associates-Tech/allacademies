<?php

namespace App\Providers;

use App\Events\SubscriptionUpdated;
use App\Events\UpdateSubscription;
use App\Listeners\CreateTrialSubscriptionOnVerification;
use App\Listeners\EvaluateSubscriptionListener;
use App\Services\UserLoginService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

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
        SubscriptionUpdated::class => [
            EvaluateSubscriptionListener::class,
            UpdateSubscription::class,
        ],
        Login::class => [
         //   StoreUserLoginHistory::class,
        ],
        Logout::class => [
         //   StoreUserLogoutHistory::class,
        ],
        Verified::class => [
            CreateTrialSubscriptionOnVerification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen(Login::class, static function (Login $event) {
            app(UserLoginService::class)->handleLogin($event->user);
        });

        Event::listen(Logout::class, static function (Logout $event) {
            app(UserLoginService::class)->handleLogout($event->user, 'manual');
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
