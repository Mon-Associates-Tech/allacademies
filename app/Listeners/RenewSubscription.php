<?php

namespace App\Listeners;

use Brick\Money\Money;
use App\Models\Payment;
use Illuminate\Support\Carbon;
use App\Enums\SubscriptionStatus;
use App\Events\SubscriptionRenewed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RenewSubscription
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
     * @param SubsriptionRenewed  $event
     * @return void
     */
    public function handle(SubscriptionRenewed $event)
    {
        $event->subscription->load('payments');
        $cost = Money::of($event->subscription->subscription->amount, 'GHS');
        $paid = Money::of('0', 'GHS');

        $event->subscription->payments->each(function (Payment $payment) use (&$paid) {
            $paid = $paid->plus($payment->amount);
        });

        $status = $paid->isGreaterThanOrEqualTo($cost) ? SubscriptionStatus::PAID : SubscriptionStatus::PART_PAID;
        $event->subscription->update([
            'status' => $status
        ]);

        if ($status === SubscriptionStatus::PAID) {
            $subscription = $event->subscription->subscription;
            $subscription->update([
                'expires_at' => $subscription->expires_at->addMonths((int)($subscription->period)),
                'renewed_at' => Carbon::now()
            ]);
        }
    }
}
