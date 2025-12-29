<?php

namespace App\Http\Controllers;

use App\Models\Chat\UserTokenSubscription;
use App\Services\TokenSubscriptionService;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;
use App\Enums\PaymentStatus;
use Exception;

class TokenPaymentController extends Controller
{
    protected $paystack;
    protected $subscriptionService;

    public function __construct(PaystackService $paystack, TokenSubscriptionService $subscriptionService)
    {
        $this->paystack = $paystack;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Initialize token subscription payment with Paystack
     */
    public function initialize($subscriptionId)
    {
        $subscription = UserTokenSubscription::findOrFail($subscriptionId);

        // Check if it's a free package - this should NOT reach here for free packages
        if ($subscription->package && $subscription->package->isFree()) {
            \Log::warning('Free package reached payment initialization', [
                'subscription_id' => $subscription->id,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('token-subscriptions.index')
                ->with('error', 'Free trials do not require payment. Please contact support if you see this message.');
        }

        // Check if reference already exists in a completed payment
        $existingPayment = Payment::where('reference', $subscription->reference)
            ->where('status', PaymentStatus::SUCCEEDED)
            ->first();

        if ($existingPayment) {
            \Log::warning('Duplicate payment attempt', [
                'subscription_id' => $subscription->id,
                'reference' => $subscription->reference,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('token-subscriptions.index')
                ->with('error', 'This subscription has already been paid for.');
        }

        // Generate a fresh unique reference for Paystack
        $paystackReference = 'TOKEN-' . $subscription->id . '-' . time() . '-' . strtoupper(Str::random(6));

        // Get price from package or pricing tier
        $price = $subscription->package ? $subscription->package->price : $subscription->pricingTier?->initial_price ?? 0;
        $packageName = $subscription->package ? $subscription->package->name : $subscription->pricingTier?->name ?? 'Token Subscription';

        $data = [
            'email' => auth()->user()->email,
            'amount' => (int) ($price * 100), // convert to kobo
            'reference' => $paystackReference, // Use fresh reference
            'metadata' => [
                'subscription_id' => $subscription->id,
                'type' => 'token_subscription',
                'package_name' => $packageName,
                'name' => auth()->user()->name,
                'phone' => auth()->user()->phone ?? '0000000000',
            ],
            'callback_url' => route('token-payments.callback'),
        ];

        try {
            $response = $this->paystack->initializeTransaction($data);

            // Store the Paystack reference for verification later
            $subscription->update(['reference' => $paystackReference]);

            return redirect($response['data']['authorization_url']);
        } catch (Exception $e) {
            \Log::error('Paystack initialization failed', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            return redirect()
                ->route('token-subscriptions.index')
                ->with('error', 'Failed to initialize payment. Please try again or contact support.');
        }
    }

    /**
     * Handle token subscription payment callback from Paystack
     */
    public function callback(Request $request)
    {
        $reference = $request->query('reference');
        $response = $this->paystack->verifyTransaction($reference);

        if ($response['status'] && $response['data']['status'] === 'success') {
            $paymentDetails = $response['data'];
            $subscriptionId = $paymentDetails['metadata']['subscription_id'];

            $subscription = UserTokenSubscription::findOrFail($subscriptionId);

            DB::transaction(function () use ($subscription, $paymentDetails) {
                // 1. Record payment
                $price = $subscription->package ? $subscription->package->price : $subscription->pricingTier?->initial_price ?? 0;
                Payment::create([
                    'reference' => $paymentDetails['reference'],
                    'amount' => $price,
                    'currency' => $paymentDetails['currency'] ?? 'GHS',
                    'status' => PaymentStatus::SUCCEEDED,
                    'token_subscription_id' => $subscription->id,
                ]);

                // 2. Activate subscription (handles both top-ups and regular subscriptions)
                $this->subscriptionService->activateSubscription($subscription);
            });

            // Check if it was a top-up (linked to another active subscription)
            if ($subscription->replaced_by_id) {
                // Refresh the main subscription to get updated token counts
                $mainSubscription = UserTokenSubscription::find($subscription->replaced_by_id);

                if ($mainSubscription && $mainSubscription->status === 'active') {
                    // Refresh to get the latest data from database
                    $mainSubscription->refresh();

                    return redirect()
                        ->route('token-subscriptions.show', $mainSubscription)
                        ->with('success', 'Tokens added successfully! New balance: ' . number_format($mainSubscription->tokens_remaining) . ' tokens');
                }
            }

            // For regular subscriptions (not top-ups), refresh the subscription
            $subscription->refresh();

            return redirect()
                ->route('token-subscriptions.show', $subscription)
                ->with('success', 'Token subscription activated successfully! Ref: ' . $subscription->reference);
        }

        return redirect()
            ->route('token-subscriptions.index')
            ->with('error', 'Token subscription payment failed or was cancelled.');
    }
}

