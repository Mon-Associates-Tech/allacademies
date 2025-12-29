<?php

namespace App\Http\Controllers;

use App\Models\Chat\OpenAiTokenPackage;
use App\Models\Chat\PricingTier;
use App\Models\Chat\SubscriptionCycle;
use App\Models\Chat\UserTokenSubscription;
use App\Services\TokenSubscriptionService;
use App\Services\SubscriptionCycleService;
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
        /** @var \App\Models\User $user */
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
        /** @var \App\Models\User $user */
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

        /** @var \App\Models\User $user */
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

        /** @var \App\Models\User $user */
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

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $pricingTier = PricingTier::findOrFail($request->pricing_tier_id);
        $months = $request->input('months');

        // Calculate total price
        $monthlyRate = $pricingTier->initial_price;
        $totalPrice = $monthlyRate * $months;

        // Get the next cycle number for this user
        $lastCycle = $user->subscriptionCycles()
            ->where('pricing_tier_id', $pricingTier->id)
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
                $totalPrice / $months  // Price per cycle
            );
        }

        // Redirect to payment with amount
        return redirect()->route('payment.initialize')
            ->with([
                'amount' => $totalPrice * 100,  // Convert to kobo for Paystack
                'reference' => 'SUB-' . $user->id . '-' . time(),
                'email' => $user->email,
                'type' => 'token_subscription',
                'pricing_tier_id' => $pricingTier->id,
                'months' => $months,
            ]);
    }

    public function show($subscription)
    {
       
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Try to get as SubscriptionCycle first
        $subscriptionCycle = SubscriptionCycle::find($subscription);
        if ($subscriptionCycle) {
            if ($subscriptionCycle->user_id !== $user->id) {
                abort(403, 'Unauthorized access to this subscription cycle.');
            }
            return view('token-subscriptions.show', ['subscriptionCycle' => $subscriptionCycle]);
        }

        // Try to get as UserTokenSubscription (legacy system)
        $userSubscription = UserTokenSubscription::find($subscription);
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
