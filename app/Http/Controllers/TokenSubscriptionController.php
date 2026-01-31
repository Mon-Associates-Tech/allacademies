<?php

namespace App\Http\Controllers;

use App\Models\Chat\PricingTier;
use App\Models\Chat\SubscriptionCycle;
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

        // Prevent caching of checkout pages to avoid browser cache miss errors
        $this->middleware(function ($request, $next) {
            $response = $next($request);

            return $response->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        })->only(['checkout', 'topup', 'processPayment', 'processTopup']);
    }

    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $activeSubscription = $user->activeSubscriptionCycle;
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
            'pendingSubscription',
            'currentCycle',
            'stats'
        ));
    }

    public function history()
    {
        /** @var User $user */
        $user = Auth::user();

        $subscriptionHistory = $user->subscriptionHistory;

        return view('token-subscriptions.history', compact('subscriptionHistory'));
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
        // If accessed via GET (e.g., coming back from Paystack), redirect to subscriptions
        if ($request->isMethod('get')) {
            session()->forget(['pending_payment', 'payment_reference', 'payment_timestamp', 'subscription_checkout']);

            return redirect()
                ->route('token-subscriptions.index')
                ->with('info', 'Checkout cancelled. You can try again anytime.');
        }

        $request->validate([
            'pricing_tier_id' => 'required|exists:pricing_tiers,id',
            'months' => 'required|integer|min:1|max:12',
        ]);

        // Clear any stale session data from previous payment attempts
        session()->forget(['pending_payment', 'payment_reference', 'subscription_checkout']);

        /** @var User $user */
        $user = Auth::user();
        $pricingTier = PricingTier::findOrFail($request->pricing_tier_id);
        $months = (int) $request->input('months');

        // Calculate pricing using PricingTier's getMonthlyPriceIncrement method
        // Cycle 1: base_price, Cycles 2-6: initial_price, Cycle 7+: subsequent_price
        $totalPrice = 0;
        $priceBreakdown = [];

        for ($cycleNumber = 1; $cycleNumber <= $months; $cycleNumber++) {
            $monthlyPrice = $pricingTier->getMonthlyPriceIncrement($cycleNumber);
            $totalPrice += $monthlyPrice;

            $priceBreakdown[$cycleNumber] = [
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

    /**
     * Show subscription data (called after user navigates back from payment page)
     * This validates that session data still exists and is valid
     */
    public function validateCheckout()
    {
        $subscriptionCheckout = session('subscription_checkout');

        // If session data is missing, clear and redirect
        if (! $subscriptionCheckout) {
            session()->forget(['pending_payment', 'payment_reference', 'payment_timestamp', 'subscription_checkout']);

            return redirect()
                ->route('token-subscriptions.index')
                ->with('info', 'Your checkout session has ended. Please start a new subscription.');
        }

        return null; // Session is valid
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'pricing_tier_id' => 'required|exists:pricing_tiers,id',
            'months' => 'required|integer|min:1|max:12',
        ]);

        // Clear any stale session data before processing new payment
        session()->forget(['payment_timestamp']);

        /** @var User $user */
        $user = Auth::user();
        $pricingTier = PricingTier::findOrFail($request->pricing_tier_id);
        $months = (int) $request->input('months');

        // Calculate total price using same logic as checkout
        $totalPrice = 0;
        for ($cycleNumber = 1; $cycleNumber <= $months; $cycleNumber++) {
            $totalPrice += $pricingTier->getMonthlyPriceIncrement($cycleNumber);
        }

        // Create subscription cycles as PENDING (no tokens assigned yet)
        $cycles = $this->cycleService->createSubscriptionCycles($user, $pricingTier, $months, true);
        $groupId = $cycles[0]->subscription_group_id;

        \Log::info('Processing payment for multi-month subscription', [
            'user_id' => $user->id,
            'pricing_tier_id' => $pricingTier->id,
            'pricing_tier_name' => $pricingTier->name,
            'months' => $months,
            'cycles_created' => count($cycles),
            'group_id' => $groupId,
            'total_price' => $totalPrice,
        ]);

        // Store payment info in session
        session([
            'pending_payment' => [
                'group_id' => $groupId,
                'pricing_tier_id' => $pricingTier->id,
                'amount' => $totalPrice,
                'type' => 'subscription',
            ],
        ]);

        // Redirect to payment with group ID using 303 to convert POST to GET
        // This prevents 'Form Resubmission' errors when user cancels payment
        return redirect()->route('token-payments.initialize', ['group_id' => $groupId], 303)
            ->with('success', 'Ready for payment. Please complete your transaction.');
    }

    public function show(SubscriptionCycle $subscription)
    {
        /** @var User $user */
        $user = Auth::user();

        // Ensure the subscription belongs to the current user
        if ($subscription->user_id !== $user->id) {
            abort(403, 'Unauthorized access to subscription.');
        }

        // Load relationships
        $subscription->load('usageLogs', 'pricingTier');

        return view('token-subscriptions.show-cycle', [
            'subscriptionCycle' => $subscription,
            'usageLogs' => $subscription->usageLogs()->latest()->paginate(15),
        ]);
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

        // Clear any stale session data from previous payment attempts
        session()->forget(['pending_payment', 'payment_reference']);

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

        // Store topup info in session for payment processing
        session([
            'pending_payment' => [
                'cycle_id' => $cycle->id,
                'pricing_tier_id' => $cycle->pricing_tier_id,
                'amount' => $amount,
                'type' => 'topup',
            ],
        ]);

        // Redirect to payment using 303 to convert POST to GET
        // This prevents 'Form Resubmission' errors when user cancels payment
        return redirect()->route('token-payments.initialize', 303)
            ->with('success', 'Ready for topup payment. Please complete your transaction.');
    }

    public function create(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $currentSubscription = $user->activeSubscriptionCycle;

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
}
