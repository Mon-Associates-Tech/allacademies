<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\Chat\UserTokenSubscription;
use App\Models\Payment;
use App\Services\PaystackService;
use App\Services\TokenSubscriptionService;
use App\Support\TokenSubscriptionStatus;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
        $user = Auth::user();

        // Authorize: Only the subscription owner can initialize payment
        if ($subscription->user_id !== $user->id) {
            throw new AuthorizationException('Unauthorized access to this subscription.');
        }

        // Validate subscription status - must be pending
        if ($subscription->status !== TokenSubscriptionStatus::PENDING) {
            \Log::warning('Invalid subscription status for payment', [
                'subscription_id' => $subscription->id,
                'status' => $subscription->status,
                'user_id' => $user->id,
            ]);

            return redirect()
                ->route('token-subscriptions.index')
                ->with('error', 'This subscription cannot be paid for in its current state.');
        }

        // Check if it's a free package - this should NOT reach here for free packages
        if ($subscription->package && $subscription->package->isFree()) {
            \Log::warning('Free package reached payment initialization', [
                'subscription_id' => $subscription->id,
                'user_id' => $user->id,
            ]);

            return redirect()
                ->route('token-subscriptions.index')
                ->with('error', 'Free trials do not require payment. Please contact support if you see this message.');
        }

        // Check if reference already exists in a completed payment
        if ($subscription->reference) {
            $existingPayment = Payment::where('reference', $subscription->reference)
                ->where('status', PaymentStatus::SUCCEEDED)
                ->first();

            if ($existingPayment) {
                \Log::warning('Duplicate payment attempt', [
                    'subscription_id' => $subscription->id,
                    'reference' => $subscription->reference,
                    'user_id' => $user->id,
                ]);

                return redirect()
                    ->route('token-subscriptions.index')
                    ->with('error', 'This subscription has already been paid for.');
            }
        }

        // Generate a fresh unique reference for Paystack
        $paystackReference = 'TOKEN-'.$subscription->id.'-'.time().'-'.strtoupper(Str::random(6));

        // Calculate the correct amount to charge
        $amount = $this->calculateSubscriptionAmount($subscription);

        if ($amount <= 0) {
            \Log::error('Invalid subscription amount', [
                'subscription_id' => $subscription->id,
                'amount' => $amount,
                'user_id' => $user->id,
            ]);

            return redirect()
                ->route('token-subscriptions.index')
                ->with('error', 'Unable to determine subscription price. Please contact support.');
        }

        $packageName = $subscription->package
            ? $subscription->package->name
            : ($subscription->pricingTier?->name ?? 'Token Subscription');

        $data = [
            'email' => $user->email,
            'amount' => (int) ($amount * 100), // convert to kobo
            'reference' => $paystackReference,
            'metadata' => [
                'subscription_id' => $subscription->id,
                'type' => 'token_subscription',
                'package_name' => $packageName,
                'name' => $user->name,
                'phone' => $user->phone ?? '0000000000',
            ],
            'callback_url' => route('token-payments.callback'),
        ];

        try {
            $response = $this->paystack->initializeTransaction($data);

            // Store the Paystack reference for verification later
            $subscription->update(['reference' => $paystackReference]);

            \Log::info('Payment initialized successfully', [
                'subscription_id' => $subscription->id,
                'reference' => $paystackReference,
                'amount' => $amount,
                'user_id' => $user->id,
            ]);

            return redirect($response['data']['authorization_url']);
        } catch (Exception $e) {
            \Log::error('Paystack initialization failed', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
                'user_id' => $user->id,
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

        if (! $reference) {
            \Log::warning('Payment callback without reference');

            return redirect()
                ->route('token-subscriptions.index')
                ->with('error', 'Invalid payment callback.');
        }

        try {
            $response = $this->paystack->verifyTransaction($reference);

            if (! $response['status'] || $response['data']['status'] !== 'success') {
                \Log::warning('Payment verification failed', [
                    'reference' => $reference,
                    'response_status' => $response['data']['status'] ?? 'unknown',
                ]);

                return redirect()
                    ->route('token-subscriptions.index')
                    ->with('error', 'Payment verification failed. Please try again.');
            }

            $paymentDetails = $response['data'];
            $subscriptionId = $paymentDetails['metadata']['subscription_id'] ?? null;

            if (! $subscriptionId) {
                \Log::error('Payment callback missing subscription ID', [
                    'reference' => $reference,
                ]);

                return redirect()
                    ->route('token-subscriptions.index')
                    ->with('error', 'Invalid payment metadata. Please contact support.');
            }

            $subscription = UserTokenSubscription::findOrFail($subscriptionId);
            $user = Auth::user();

            // Authorize: Only the subscription owner can process payment
            if ($subscription->user_id !== $user->id) {
                \Log::warning('Unauthorized payment callback attempt', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $user->id,
                    'reference' => $reference,
                ]);

                return redirect()
                    ->route('token-subscriptions.index')
                    ->with('error', 'Unauthorized access to this subscription.');
            }

            // Verify the amount paid matches expected
            $expectedAmount = $this->calculateSubscriptionAmount($subscription);
            $paidAmount = $paymentDetails['amount'] / 100; // Convert from kobo

            if (abs($paidAmount - $expectedAmount) > 0.01) { // Allow 1 pesewa difference
                \Log::warning('Payment amount mismatch', [
                    'subscription_id' => $subscription->id,
                    'expected' => $expectedAmount,
                    'paid' => $paidAmount,
                    'reference' => $reference,
                ]);

                // Still process but log the discrepancy
            }

            DB::transaction(function () use ($subscription, $paymentDetails, $expectedAmount) {
                // 1. Record payment
                Payment::create([
                    'reference' => $paymentDetails['reference'],
                    'amount' => $expectedAmount,
                    'currency' => $paymentDetails['currency'] ?? 'GHS',
                    'status' => PaymentStatus::SUCCEEDED,
                    'token_subscription_id' => $subscription->id,
                ]);

                // 2. Activate subscription (handles both top-ups and regular subscriptions)
                $this->subscriptionService->activateSubscription($subscription);
            });

            \Log::info('Payment processed successfully', [
                'subscription_id' => $subscription->id,
                'reference' => $reference,
                'amount' => $expectedAmount,
            ]);

            // Check if it was a top-up (linked to another active subscription)
            if ($subscription->replaced_by_id) {
                $mainSubscription = UserTokenSubscription::find($subscription->replaced_by_id);

                if ($mainSubscription && $mainSubscription->status === TokenSubscriptionStatus::ACTIVE) {
                    // Refresh to get the latest data from database
                    $mainSubscription->refresh();

                    return redirect()
                        ->route('token-subscriptions.show', $mainSubscription)
                        ->with('success', 'Tokens added successfully! New balance: '.number_format($mainSubscription->tokens_remaining).' tokens');
                }
            }

            // For regular subscriptions (not top-ups), refresh the subscription
            $subscription->refresh();

            return redirect()
                ->route('token-subscriptions.show', $subscription)
                ->with('success', 'Token subscription activated successfully! Your tokens are now available.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Subscription not found in callback', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('token-subscriptions.index')
                ->with('error', 'Subscription not found. Please contact support.');
        } catch (Exception $e) {
            \Log::error('Payment callback processing failed', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('token-subscriptions.index')
                ->with('error', 'An error occurred processing your payment. Please contact support.');
        }
    }

    /**
     * Calculate the amount to charge for a subscription
     */
    private function calculateSubscriptionAmount(UserTokenSubscription $subscription): float
    {
        // If amount is explicitly set (for multi-month purchases), use it
        if ($subscription->amount) {
            return (float) $subscription->amount;
        }

        // Pricing from package
        if ($subscription->package) {
            return (float) $subscription->package->price;
        }

        // Pricing from pricing tier
        if ($subscription->pricingTier) {
            return (float) $subscription->pricingTier->initial_price;
        }

        // Fallback - should not happen
        \Log::warning('Could not determine subscription price', [
            'subscription_id' => $subscription->id,
            'package_id' => $subscription->package_id,
            'pricing_tier_id' => $subscription->pricing_tier_id,
        ]);

        return 0;
    }
}
