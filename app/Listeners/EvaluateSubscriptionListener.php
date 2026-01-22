<?php

namespace App\Listeners;

use App\Enums\SubscriptionStatus;
use App\Events\SubscriptionUpdated;
use App\Models\Payment;
use App\Notifications\SubscriptionPaidNotification;
use Brick\Money\Money;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class EvaluateSubscriptionListener implements ShouldQueue
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
     * @return void
     */
    public function handle(SubscriptionUpdated $event)
    {
        $event->subscription->load('payments', 'subscriber');
        $cost = Money::of($event->subscription->amount, 'GHS');
        $paid = Money::of('0', 'GHS');

        $event->subscription->payments->each(function (Payment $payment) use (&$paid) {
            $paid = $paid->plus($payment->amount);
        });

        $event->subscription->update([
            'status' => $paid->isGreaterThanOrEqualTo($cost) ? SubscriptionStatus::PAID : SubscriptionStatus::PART_PAID,
        ]);

        if ($event->subscription->status === SubscriptionStatus::PAID) {
            Notification::send($event->subscription->subscriber, new SubscriptionPaidNotification($event->subscription));
        }
    }
}
