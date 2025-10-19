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

        $packages = OpenAiTokenPackage::active()
            ->where('is_free', false) // Don't show free trial in upgrade options
            ->orderBy('price')
            ->get();

        $stats = $this->subscriptionService->getUserSubscriptionStats($user);

        return view('token-subscriptions.index', compact(
            'activeSubscription',
            'subscriptionHistory',
            'pendingSubscription',
            'packages',
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
            return redirect()
                ->route('payment.token.initialize', $pendingSubscription->id)
                ->with('info', 'You have a pending payment. Complete it to activate your new subscription.');
        }

        $packageId = $request->get('package');
        $package = $packageId ? OpenAiTokenPackage::findOrFail($packageId) : null;

        $packages = OpenAiTokenPackage::active()
            ->where('is_free', false)
            ->orderBy('price')
            ->get();

        return view('token-subscriptions.create', compact('packages', 'package', 'currentSubscription'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:openai_token_packages,id',
        ]);

        $user = Auth::user();
        $package = OpenAiTokenPackage::findOrFail($request->package_id);

        // Check if user has pending subscription
        $pendingSubscription = $user->tokenSubscriptions()->where('status', 'pending')->first();
        if ($pendingSubscription) {
            return redirect()
                ->route('payment.token.initialize', $pendingSubscription->id)
                ->with('info', 'Complete your pending payment first.');
        }

        // Don't allow selecting free trial manually
        if ($package->isFree()) {
            return redirect()
                ->route('token-subscriptions.create')
                ->with('error', 'Free trial is automatically assigned to new users.');
        }

        // Get current active subscription
        $currentSubscription = $user->activeTokenSubscription;

        // Determine if this is a top-up or upgrade
        // It's a top-up if:
        // 1. User has an active subscription
        // 2. It's the same package OR they want to add tokens
        // 3. It's not a trial
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
