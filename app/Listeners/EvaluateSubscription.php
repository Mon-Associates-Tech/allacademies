<?php

namespace App\Listeners;

use Brick\Money\Money;
use App\Models\Payment;
use App\Enums\SubscriptionStatus;
use App\Events\SubscriptionUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class EvaluateSubscription implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SubscriptionUpdated  $event
     * @return void
     */
    public function handle(SubscriptionUpdated $event)
    {
        $event->subscription->load('payments');
        $cost = Money::of($event->subscription->amount, 'GHS');
        $paid = Money::of('0', 'GHS');

        $event->subscription->payments->each(function (Payment $payment) use (&$paid) {
            $paid = $paid->plus($payment->amount);
        });

        $event->subscription->update([
            'status' => $paid->isGreaterThanOrEqualTo($cost) ? SubscriptionStatus::PAID : SubscriptionStatus::PART_PAID
        ]);
    }
}
