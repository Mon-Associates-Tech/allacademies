<?php

namespace App\Http\Controllers;

use App\Models\Chat\OpenAiTokenPackage;
use App\Models\Chat\PricingTier;
use App\Models\Chat\SubscriptionCycle;
use App\Models\Chat\UserTokenSubscription;
use App\Services\TokenSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokenSubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(TokenSubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;

        /*        // Only subscribers can access token subscription management
                $this->middleware(function ($request, $next) {
                    if (auth()->user()->role !== 'subscriber') {
                        return redirect()->route('dashboard')
                            ->with('info', 'Token subscriptions are only available for subscriber accounts.');
                    }
                    return $next($request);
                });*/
    }

    public function index()
    {
        $user = Auth::user();

        $activeSubscription = $user->activeTokenSubscription;
        $subscriptionHistory = $user->subscriptionHistory;
        $pendingSubscription = $user->tokenSubscriptions()->where('status', 'pending')->first();

        // Get current active subscription cycle
        $currentCycle = $user->subscriptionCycles()
            ->where('status', 'active')
            ->latest()
            ->first();

        $stats = $this->subscriptionService->getUserSubscriptionStats($user);

        return view('token-subscriptions.index', compact(
            'activeSubscription',
            'subscriptionHistory',
            'pendingSubscription',
            'currentCycle',
            'stats'
        ));
    }
    public function create(Request $request)
    {
        $user = Auth::user();
        $currentSubscription = $user->activeTokenSubscription;

        // Check for pending payment
        $pendingSubscription = $user->tokenSubscriptions()->where('status', 'pending')->first();
        if ($pendingSubscription) {
           // return redirect()
           //     ->route('payment.token.initialize', $pendingSubscription->id)
           //     ->with('info', 'You have a pending payment. Complete it to activate your new subscription.');
        }

        $pricingTierId = $request->get('pricing_tier');
        $pricingTier = $pricingTierId ? PricingTier::findOrFail($pricingTierId) : null;

        $pricingTiers = PricingTier::active()
            ->orderBy('initial_price')
            ->get();

        return view('token-subscriptions.create', compact(
                'pricingTiers',
                'pricingTier',
                'currentSubscription'
            )
        );
    }


    public function store(Request $request)
    {
        $request->validate([
            'pricing_tier_id' => 'required|exists:pricing_tiers,id',
        ]);

        $user = Auth::user();
        $pricingTier = PricingTier::findOrFail($request->pricing_tier_id);

        // Check if user already has an active subscription
        $activeSubscription = $user->subscriptionCycles()
            ->where('status', 'active')
            ->latest()
            ->first();

        if ($activeSubscription && $activeSubscription->pricing_tier_id === $pricingTier->id) {
            // Same tier - treat as top-up
            return redirect()
                ->route('token-subscriptions.index')
                ->with('info', 'You already have an active subscription to this tier.');
        }

        // Create or upgrade subscription cycle
        $subscriptionCycle = \App\Services\SubscriptionCycleService::createCycle($user, $pricingTier);

        return redirect()
            ->route('token-subscriptions.show', $subscriptionCycle->id)
            ->with('success', 'Subscription initiated successfully!');
    }
    public function show(SubscriptionCycle $subscriptionCycle)
    {
        // Verify the cycle belongs to the authenticated user
        $user = Auth::user();
        if ($subscriptionCycle->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this subscription cycle.');
        }

        return view('token-subscriptions.show', compact('subscriptionCycle'));
    }
}
