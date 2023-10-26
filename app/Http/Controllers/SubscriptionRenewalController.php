<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Support\Carbon;
use App\Enums\SubscriptionStatus;
use App\Models\SubscriptionRenewal;

class SubscriptionRenewalController extends Controller
{

    /**
     * Display a listing of the resource that are less than 1 month to expiration.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $now = Carbon::now();

        $subscriptions = Subscription::query()
            ->where('team_id', auth()->user()->current_team_id)
            ->whereRaw("TIMESTAMPDIFF(MONTH, expires_at, ?) = 0", [$now])
            ->where('status', SubscriptionStatus::PAID)
            ->latest('id')->paginate();

        return view('expiring-subscriptions.index', [
            'subscriptions' => $subscriptions,
        ]);
    }


    /**
     * Renew resource/subscription renewal in storage.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function store(Subscription $expiring_subscription)
    {
        $renewal = new SubscriptionRenewal();

        $reference = uniqid();
        $renewal->reference = $reference;
        $renewal = $expiring_subscription->renewals()->save($renewal);

        return to_route('expiring-subscriptions.renewals')
            ->with('success', __('status.subscription.renewed', [
                'reference' => $expiring_subscription->reference,
                'new' => $reference,
            ]));
    }

    /**
     * Display a listing of renewals
     *
     * @return \Illuminate\Http\Response
     */
    public function renewals()
    {

        $subscriptions = SubscriptionRenewal::whereHas('subscription')
            ->with(['subscription' => function ($query) {
                $query->where('subscriber_id', auth()->user()->id);
            }])
            ->latest('id')
            ->paginate();

        // dd($subscriptions);

        return view('expiring-subscriptions.renewals', [
            'subscriptions' => $subscriptions,
        ]);
    }
}
