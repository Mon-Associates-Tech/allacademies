<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionRenewal;

class SubscriptionRenewalController extends Controller
{
    /**
     * Renew resource/subscription in storage.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function renew(Subscription $subscription)
    {

        // $renewal = new SubscriptionRenewal();

        // $renewal->reference = uniqid();
        // $renewal->subscription_id = $subscription->id;
        // $renewal->save();


        // // $remainingDays = $subscription->updated_at->diffInMonths($subscription->expires_at);
        // // dd($remainingDays);

        // return to_route('subscriptions.index')
        //     ->with('success', __('status.resource.created', ['name' => $subscription->reference]));

        $renewal = new SubscriptionRenewal();

        $renewal->reference = uniqid();
        $renewal = $subscription->renewals()->save($renewal);

        return to_route('subscriptions.index')
            ->with('success', __('status.resource.created', ['name' => $subscription->reference]));
    }
}
