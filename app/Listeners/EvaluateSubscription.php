<?php

namespace App\Listeners;

use App\Enums\SubscriptionStatus;
use App\Events\PaymentSucceeded;
use App\Models\Payment;
use Brick\Money\Money;
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
     * @param  PaymentSucceeded  $event
     * @return void
     */
    public function handle(PaymentSucceeded $event)
    {
        if (is_null($event->payment->subscription_id)) {
            return;
        }

        $event->payment->load('subscription.payments');
        $cost = Money::of($event->payment->subscription->amount, 'GHS');
        $paid = Money::of('0', 'GHS');

        $event->payment->subscription->payments->each(function (Payment $payment) use (&$paid) {
            $paid = $paid->plus($payment->amount);
        });

        $event->payment->subscription->update([
            'status' => $paid->isGreaterThanOrEqualTo($cost) ? SubscriptionStatus::PAID : SubscriptionStatus::PART_PAID
        ]);
    }
}
