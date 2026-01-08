<?php

namespace App\Http\Controllers;

use App\Models\Chat\PricingTier;
use App\Models\Chat\SubscriptionCycle;
use App\Models\Chat\UserTokenSubscription;
use App\Models\User;
use App\Services\SubscriptionCycleService;
use App\Services\TokenSubscriptionService;
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
        $months = (int) $request->input('months');

        // Calculate pricing based on the months being purchased (not cycle_number from DB)
        // Months 1-6 use initial_price, 7+ use subsequent_price
        $totalPrice = 0;
        $priceBreakdown = [];

        for ($monthPos = 1; $monthPos <= $months; $monthPos++) {
            $monthlyPrice = $monthPos <= $pricingTier->initial_period_months
                ? (float) $pricingTier->initial_price
                : (float) $pricingTier->subsequent_price;

            $totalPrice += $monthlyPrice;

            $priceBreakdown[$monthPos] = [
                'monthly_increment' => $monthlyPrice,
                'cumulative' => $totalPrice,
            ];
        }

        // Store in session for payment processing
        session([
            'subscription_checkout' => [
                'pricing_tier_id' => $pricingTier->id,
                'pricing_tier_name' => $pricingTier->name,
                'months' => $months,
                'total_price' => $totalPrice,
                'monthly_token_limit' => $pricingTier->monthly_token_limit,
                'price_breakdown' => $priceBreakdown,
            ],
        ]);

        // Show payment page
        return view('token-subscriptions.checkout', [
            'pricingTier' => $pricingTier,
            'months' => $months,
            'totalPrice' => $totalPrice,
            'priceBreakdown' => $priceBreakdown,
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

        // Generate a group ID to link all cycles from this purchase
        $subscriptionGroupId = \Illuminate\Support\Str::uuid()->toString();

        // Get the next cycle number for this user
        $lastCycle = $user->subscriptionCycles()
            ->orderBy('cycle_number', 'desc')
            ->first();
        $nextCycleNumber = ($lastCycle ? $lastCycle->cycle_number : 0) + 1;

        // Create subscription cycles for each month using anniversary dates
        // Pricing is based on position within this purchase (1-10), not cycle_number from DB
        $cycleStartDate = $lastCycle ? $lastCycle->cycle_end_date : now();
        $totalPrice = 0;

        for ($i = 0; $i < $months; $i++) {
            $currentCycleNumber = $nextCycleNumber + $i;
            $monthPos = $i + 1; // Position within this purchase (1-10)

            // Calculate price for this month position (1-6 = initial, 7+ = subsequent)
            $monthlyPrice = $monthPos <= $pricingTier->initial_period_months
                ? (float) $pricingTier->initial_price
                : (float) $pricingTier->subsequent_price;

            $totalPrice += $monthlyPrice;

            // Calculate the start date for this cycle (30 days from previous cycle end)
            $cycleStart = $cycleStartDate->copy()->addDays($i * 30);

            $this->cycleService->createCycle(
                $user,
                $pricingTier,
                $cycleStart,
                $currentCycleNumber,
                $totalPrice,
                $subscriptionGroupId
            );
        }

        // Find or create the pending subscription
        $pendingSubscription = $user->tokenSubscriptions()
            ->where('status', TokenSubscriptionStatus::PENDING->value)
            ->where('pricing_tier_id', $pricingTier->id)
            ->first();

        if (! $pendingSubscription) {
            $tokensPurchased = $pricingTier->monthly_token_limit * $months;
            $pendingSubscription = UserTokenSubscription::create([
                'user_id' => $user->id,
                'pricing_tier_id' => $pricingTier->id,
                'amount' => $totalPrice,
                'status' => TokenSubscriptionStatus::PENDING->value,
                'reference' => 'TOKEN-'.$user->id.'-'.time(),
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
                'amount' => $totalPrice,
                'reference' => 'TOKEN-'.$user->id.'-'.time(),
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

        // Priority: Try to get as SubscriptionCycle first (new system)
        $subscriptionCycle = SubscriptionCycle::with('usageLogs', 'pricingTier')
            ->forUser($user->id)
            ->find($subscription);

        if ($subscriptionCycle) {
            return view('token-subscriptions.show-cycle', [
                'subscriptionCycle' => $subscriptionCycle,
                'usageLogs' => $subscriptionCycle->usageLogs()->latest()->paginate(15),
            ]);
        }

        // Fallback: Try to get as UserTokenSubscription (legacy system)
        $userSubscription = UserTokenSubscription::with('usageLogs', 'pricingTier')
            ->find($subscription);
        if ($userSubscription && $userSubscription->user_id === $user->id) {
            return view('token-subscriptions.show-legacy', [
                'subscription' => $userSubscription,
                'usageLogs' => $userSubscription->usageLogs()->latest()->paginate(15),
            ]);
        }

        abort(404, 'Subscription not found.');
    }

    /**
     * Show topup options for an existing subscription cycle
     */
    public function topup($cycleId)
    {
        /** @var User $user */
        $user = Auth::user();

        $cycle = SubscriptionCycle::with('pricingTier')
            ->forUser($user->id)
            ->find($cycleId);

        if (! $cycle) {
            abort(404, 'Subscription cycle not found.');
        }

        return view('token-subscriptions.topup', [
            'cycle' => $cycle,
            'pricingTier' => $cycle->pricingTier,
        ]);
    }

    /**
     * Process a topup purchase for an existing subscription
     * Simple topup: user purchases an amount and tokens are added to current cycle
     */
    public function processTopup(Request $request)
    {
        $request->validate([
            'cycle_id' => 'required|exists:subscription_cycles,id',
            'amount' => 'required|numeric|min:10',
        ]);

        /** @var User $user */
        $user = Auth::user();

        $cycle = SubscriptionCycle::with('pricingTier')
            ->forUser($user->id)
            ->find($request->cycle_id);

        if (! $cycle) {
            abort(404, 'Subscription cycle not found.');
        }

        if (! $cycle->isActive()) {
            return redirect()
                ->route('token-subscriptions.show', $cycle->id)
                ->with('error', 'Can only topup an active cycle.');
        }

        $amount = (float) $request->input('amount');

        // Create pending topup subscription for payment processing
        $topupSubscription = UserTokenSubscription::create([
            'user_id' => $user->id,
            'pricing_tier_id' => $cycle->pricing_tier_id,
            'amount' => $amount,
            'status' => TokenSubscriptionStatus::PENDING->value,
            'reference' => 'TOPUP-'.$user->id.'-'.time().'-'.strtoupper(\Illuminate\Support\Str::random(6)),
            'package_id' => null,
            'purchased_at' => now(),
            'expires_at' => now()->addMonths(1),
            'tokens_purchased' => 0,
            'tokens_used' => 0,
            'tokens_remaining' => 0,
            'action_type' => 'topup',
        ]);

        // Store topup info in session for payment processing
        session([
            'topup_info' => [
                'subscription_id' => $topupSubscription->id,
                'cycle_id' => $cycle->id,
                'amount' => $amount,
                'pricing_tier_id' => $cycle->pricing_tier_id,
            ],
        ]);

        // Redirect to payment
        return redirect()->route('token-payments.initialize', $topupSubscription->id)
            ->with('success', 'Ready for topup payment. Please complete your transaction.');
    }
}
