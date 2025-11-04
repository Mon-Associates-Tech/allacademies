<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Verified;

class CreateTrialSubscriptionOnVerification
{
    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        $event->user->createFreeTrialSubscription();
    }
}
