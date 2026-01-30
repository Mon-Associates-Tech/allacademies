<?php

namespace App\Events;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateSubscription
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function handle(SubscriptionUpdated $event)
    {
        $subscription = $event->subscription;

        // Proceed only if the subscription is paid and has a duration.
        if ($subscription->status === SubscriptionStatus::PAID
            && $subscription->duration_in_months) {
            // If it's a renewal of an active subscription, extend from its current expiry date.
            // Otherwise, start the subscription from now.
            $currentExpiry = $subscription->expires_at;
            $startDate = ($currentExpiry && $currentExpiry->isFuture()) ? $currentExpiry : now();

            $subscription->expires_at = $startDate->addMonths($subscription->duration_in_months)->toDateTimeString();
            $subscription->save();
        }

    }
}
