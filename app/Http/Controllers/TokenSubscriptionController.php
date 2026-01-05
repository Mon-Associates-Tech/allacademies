<?php

namespace App\Http\Controllers;

use App\Models\Chat\OpenAiTokenPackage;
use App\Models\Chat\PricingTier;
use App\Models\Chat\SubscriptionCycle;
use App\Models\Chat\UserTokenSubscription;
use App\Models\User;
use App\Services\TokenSubscriptionService;
use App\Services\SubscriptionCycleService;
use App\Support\TokenSubscriptionStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokenSubscriptionController extends Controller
{
    protected TokenSubscriptionService $subscriptionService;
    protected SubscriptionCycleService $cycleService;

    public function __construct(TokenSubscriptionService $subscriptionService, SubscriptionCycleService $cycleService)
    {
        $this->subscriptionService = $subscriptionService;
        $this->cycleService = $cycleService;
    }

    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $activeSubscription = $user->activeTokenSubscription;
        $subscriptionHistory = $user->subscriptionHistory;
        $pendingSubscription = $user->tokenSubscriptions()
            ->where('status', TokenSubscriptionStatus::PENDING->value)
            ->first();

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
        /** @var User $user */
        $user = Auth::user();
        $currentSubscription = $user->activeTokenSubscription;

        // Check for pending payment
        $pendingSubscription = $user->tokenSubscriptions()
            ->where('status', TokenSubscriptionStatus::PENDING->value)
            ->first();

        if ($pendingSubscription) {
            return redirect()
                ->route('token-payments.initialize', $pendingSubscription->id)
                ->with('info', 'You have a pending payment. Complete it to activate your new subscription.');
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
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pricing_tier_id' => 'required|exists:pricing_tiers,id',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $pricingTier = PricingTier::findOrFail($request->pricing_tier_id);

        // Check if user already has an active subscription to the same tier
        $activeSubscription = $user->subscriptionCycles()
            ->where('status', 'active')
            ->where('pricing_tier_id', $pricingTier->id)
            ->latest()
            ->first();

        if ($activeSubscription) {
            return redirect()
                ->route('token-subscriptions.index')
                ->with('info', 'You already have an active subscription to this tier.');
        }

        // Create subscription cycle
        $subscriptionCycle = $this->cycleService->createCycle(
            $user,
            $pricingTier,
            now(),
            1
        );

        return redirect()
            ->route('token-subscriptions.show', $subscriptionCycle->id)
            ->with('success', 'Subscription initiated successfully!');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'pricing_tier_id' => 'required|exists:pricing_tiers,id',
            'months' => 'required|integer|min:1|max:12',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $pricingTier = PricingTier::findOrFail($request->pricing_tier_id);
        $months = $request->input('months');

        // Calculate total price based on months
        $monthlyRate = $pricingTier->initial_price;
        $totalPrice = $monthlyRate * $months;

        // Store in session for payment processing
        session([
            'subscription_checkout' => [
                'pricing_tier_id' => $pricingTier->id,
                'pricing_tier_name' => $pricingTier->name,
                'months' => $months,
                'monthly_rate' => $monthlyRate,
                'total_price' => $totalPrice,
                'monthly_token_limit' => $pricingTier->monthly_token_limit,
            ]
        ]);

        // Show payment page
        return view('token-subscriptions.checkout', [
            'pricingTier' => $pricingTier,
            'months' => $months,
            'monthlyRate' => $monthlyRate,
            'totalPrice' => $totalPrice,
            'user' => $user,
        ]);
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'pricing_tier_id' => 'required|exists:pricing_tiers,id',
            'months' => 'required|integer|min:1|max:12',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $pricingTier = PricingTier::findOrFail($request->pricing_tier_id);
        $months = (int) $request->input('months');

        // Calculate total price
        $monthlyRate = $pricingTier->initial_price;
        $totalPrice = $monthlyRate * $months;

        // Get the next cycle number for this user
        $lastCycle = $user->subscriptionCycles()
            ->orderBy('cycle_number', 'desc')
            ->first();

        $nextCycleNumber = ($lastCycle ? $lastCycle->cycle_number : 0) + 1;

        // Create subscription cycles for each month
        $cycleStartDate = now();
        for ($i = 0; $i < $months; $i++) {
            $this->cycleService->createCycle(
                $user,
                $pricingTier,
                $cycleStartDate->copy()->addMonths($i),
                $nextCycleNumber + $i,
                $totalPrice / $months
            );
        }

        // Find or create the pending subscription
        $pendingSubscription = $user->tokenSubscriptions()
            ->where('status', TokenSubscriptionStatus::PENDING->value)
            ->where('pricing_tier_id', $pricingTier->id)
            ->first();

        if (!$pendingSubscription) {
            $tokensPurchased = $pricingTier->monthly_token_limit * $months;
            $pendingSubscription = UserTokenSubscription::create([
                'user_id' => $user->id,
                'pricing_tier_id' => $pricingTier->id,
                'status' => TokenSubscriptionStatus::PENDING->value,
                'reference' => 'TOKEN-' . $user->id . '-' . time(),
                'package_id' => null,
                'purchased_at' => now(),
                'expires_at' => now()->addMonths($months),
                'tokens_purchased' => $tokensPurchased,
                'tokens_used' => 0,
                'tokens_remaining' => $tokensPurchased,
            ]);
        } else {
            // Update existing pending subscription
            $tokensPurchased = $pricingTier->monthly_token_limit * $months;
            $pendingSubscription->update([
                'reference' => 'TOKEN-' . $user->id . '-' . time(),
                'expires_at' => now()->addMonths($months),
                'tokens_purchased' => $tokensPurchased,
                'tokens_remaining' => $tokensPurchased,
            ]);
        }

        // Redirect to payment
        return redirect()->route('token-payments.initialize', $pendingSubscription->id)
            ->with('success', 'Ready for payment. Please complete your transaction.');
    }

public function show($subscription)
{
    /** @var User $user */
    $user = Auth::user();

    // Try to get as SubscriptionCycle first
    $subscriptionCycle = SubscriptionCycle::with('usageLogs', 'pricingTier')->find($subscription);
    if ($subscriptionCycle) {
        if ($subscriptionCycle->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this subscription cycle.');
        }
        return view('token-subscriptions.show', ['subscriptionCycle' => $subscriptionCycle]);
    }

    // Try to get as UserTokenSubscription (legacy system)
    $userSubscription = UserTokenSubscription::with('usageLogs', 'pricingTier')->find($subscription);
    if ($userSubscription) {
        if ($userSubscription->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this subscription.');
        }
        return view('token-subscriptions.show-legacy', ['subscription' => $userSubscription]);
    }

    // Model not found
    abort(404, 'Subscription not found.');
}

}
