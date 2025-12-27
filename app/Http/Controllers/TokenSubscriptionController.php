<?php

namespace App\Http\Controllers;

use App\Models\Chat\OpenAiTokenPackage;
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

        // Get paid packages only
        $packages = OpenAiTokenPackage::active()
            ->where('is_free', false)
            ->orderBy('price')
            ->get();

        // Check if user is eligible for trial
        $isEligibleForTrial = !$user->hasEverHadTrial();
        $trialPackage = null;

        if ($isEligibleForTrial) {
            $trialPackage = OpenAiTokenPackage::active()
                ->where('is_free', true)
                ->first();
        }

        $stats = $this->subscriptionService->getUserSubscriptionStats($user);

        return view('token-subscriptions.index', compact(
            'activeSubscription',
            'subscriptionHistory',
            'pendingSubscription',
            'packages',
            'stats',
            'trialPackage',
            'isEligibleForTrial'
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

        $packageId = $request->get('package');
        $package = $packageId ? OpenAiTokenPackage::findOrFail($packageId) : null;

        $packages = OpenAiTokenPackage::active()
            ->where('is_free', false)
            ->orderBy('price')
            ->get();

        // Get trial package if user has never had one
        $trialPackage = null;
        $isEligibleForTrial = !$user->hasEverHadTrial();

        if ($isEligibleForTrial) {
            $trialPackage = OpenAiTokenPackage::active()
                ->where('is_free', true)
                ->first();
        }

        return view('token-subscriptions.create', compact(
                'packages',
                'package',
                'currentSubscription',
                'trialPackage',
                'isEligibleForTrial'
            )
        );
    }


    public function store(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:openai_token_packages,id',
        ]);

        $user = Auth::user();
        $package = OpenAiTokenPackage::findOrFail($request->package_id);

        // Handle FREE TRIAL activation FIRST (before any other checks)
        if ($package->isFree()) {
            // Check if user has already used their trial
            if ($user->hasEverHadTrial()) {
                return redirect()
                    ->route('token-subscriptions.create')
                    ->with('error', 'You have already used your free trial.');
            }

            // Check if user already has any active subscription
            if ($user->activeTokenSubscription) {
                return redirect()
                    ->route('token-subscriptions.index')
                    ->with('info', 'You already have an active subscription. Trial cannot be activated.');
            }

            // Activate trial immediately WITHOUT creating pending record
            try {
                \Log::info('Activating free trial', [
                    'user_id' => $user->id,
                    'package_id' => $package->id
                ]);

                $user->createFreeTrialSubscription(true);

                return redirect()
                    ->route('token-subscriptions.index')
                    ->with('success', '🎉 Free trial activated successfully! You have ' . number_format($package->token_limit) . ' tokens for 7 days.');
            } catch (\Exception $e) {
                \Log::error('Trial activation failed', [
                    'user_id' => $user->id,
                    'package_id' => $package->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return redirect()
                    ->route('token-subscriptions.create')
                    ->with('error', 'Failed to activate free trial. Please try again or contact support.');
            }
        }

        // For PAID packages, continue with normal flow
        // Check if user has pending subscription
        $pendingSubscription = $user->tokenSubscriptions()
            ->where('status', 'pending')
            ->where('package_id', $package->id)
            ->first();

        if ($pendingSubscription) {
            // Reuse existing pending subscription
            \Log::info('Reusing existing pending subscription', [
                'subscription_id' => $pendingSubscription->id,
                'user_id' => $user->id
            ]);

            return redirect()->route('payment.token.initialize', $pendingSubscription->id);
        }

        // Get current active subscription
        $currentSubscription = $user->activeTokenSubscription;

        // Determine if this is a top-up or upgrade
        $isTopUp = $currentSubscription &&
            $currentSubscription->package_id == $package->id &&
            $currentSubscription->action_type !== 'trial';

        // Create a new subscription (will replace or top-up the current one)
        $subscription = $this->subscriptionService->changeSubscription($user, $package, $isTopUp);

        // Redirect to payment
        return redirect()->route('payment.token.initialize', $subscription->id);
    }
    public function show(UserTokenSubscription $subscription)
    {
        $this->authorize('view', $subscription);

        $subscription->load(['package', 'payment', 'usageLogs' => function ($query) {
            $query->latest()->limit(50);
        }]);

        return view('token-subscriptions.show', compact('subscription'));
    }
}
