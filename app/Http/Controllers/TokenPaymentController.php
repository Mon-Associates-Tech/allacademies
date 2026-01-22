<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\Chat\PricingTier;
use App\Models\Payment;
use App\Services\PaystackService;
use App\Services\TokenSubscriptionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    public function initialize()
    {
        $user = Auth::user();
        $pendingPayment = session('pending_payment');

        if (! $pendingPayment) {
            return redirect()
                ->route('token-subscriptions.index')
                ->with('error', 'No pending payment found.');
        }

        $amount = (float) $pendingPayment['amount'];
        $groupId = $pendingPayment['group_id'] ?? null;
        $cycleId = $pendingPayment['cycle_id'] ?? null;
        $paymentType = $pendingPayment['type'] ?? 'subscription';
        $pricingTier = PricingTier::find($pendingPayment['pricing_tier_id']);

        if ($amount <= 0) {
            return redirect()
                ->route('token-subscriptions.index')
                ->with('error', 'Invalid payment amount.');
        }

        // Generate unique reference
        $paystackReference = 'TOKEN-'.time().'-'.strtoupper(Str::random(8));

        $data = [
            'email' => $user->email,
            'amount' => (int) ($amount * 100),
            'reference' => $paystackReference,
            'metadata' => [
                'user_id' => $user->id,
                'group_id' => $groupId,
                'cycle_id' => $cycleId,
                'type' => $paymentType,
                'pricing_tier_id' => $pendingPayment['pricing_tier_id'],
                'name' => $user->name,
                'phone' => $user->phone ?? '0000000000',
            ],
            'callback_url' => route('token-payments.callback'),
        ];

        try {
            $response = $this->paystack->initializeTransaction($data);

            // Store reference in session
            session(['payment_reference' => $paystackReference]);

            \Log::info('Payment initialized successfully', [
                'reference' => $paystackReference,
                'amount' => $amount,
                'group_id' => $groupId,
                'user_id' => $user->id,
            ]);

            return redirect($response['data']['authorization_url']);
        } catch (Exception $e) {
            \Log::error('Paystack initialization failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return redirect()
                ->route('token-subscriptions.index')
                ->with('error', 'Failed to initialize payment. Please try again.');
        }
    }

    /**
     * Handle token subscription payment callback from Paystack
     */
    public function callback(Request $request)
    {
        $reference = $request->query('reference');

        if (! $reference) {
            return redirect()
                ->route('token-subscriptions.index')
                ->with('error', 'Invalid payment callback.');
        }

        try {
            $response = $this->paystack->verifyTransaction($reference);

            if (! $response['status'] || $response['data']['status'] !== 'success') {
                return redirect()
                    ->route('token-subscriptions.index')
                    ->with('error', 'Payment verification failed.');
            }

            $paymentDetails = $response['data'];
            $groupId = $paymentDetails['metadata']['group_id'] ?? null;
            $cycleId = $paymentDetails['metadata']['cycle_id'] ?? null;
            $pricingTierId = $paymentDetails['metadata']['pricing_tier_id'] ?? null;
            $paymentType = $paymentDetails['metadata']['type'] ?? 'subscription';
            $user = Auth::user();

            if (! $pricingTierId) {
                return redirect()
                    ->route('token-subscriptions.index')
                    ->with('error', 'Invalid payment metadata.');
            }

            $pricingTier = PricingTier::find($pricingTierId);
            $paidAmount = $paymentDetails['amount'] / 100;

            DB::transaction(function () use ($paymentDetails, $paidAmount, $user, $groupId, $cycleId, $pricingTier, $paymentType) {
                // 1. Record payment
                Payment::create([
                    'reference' => $paymentDetails['reference'],
                    'amount' => $paidAmount,
                    'currency' => $paymentDetails['currency'] ?? 'GHS',
                    'status' => PaymentStatus::SUCCEEDED,
                ]);

                // 2. Deactivate expired cycles
                $user->subscriptionCycles()
                    ->where('status', 'active')
                    ->where('cycle_end_date', '<=', now())
                    ->update(['status' => 'expired']);

                // 3. Handle based on payment type
                if ($paymentType === 'topup' && $cycleId) {
                    // Topup: add tokens to existing cycle
                    $cycle = \App\Models\Chat\SubscriptionCycle::find($cycleId);
                    if ($cycle && $pricingTier) {
                        $tokensToAdd = $pricingTier->calculateTokensFromAmount($paidAmount);
                        $cycle->topup_tokens_allocated += $tokensToAdd;
                        $cycle->tokens_allocated += $tokensToAdd;
                        $cycle->is_topup = true;
                        $cycle->save();

                        \Log::info('Topup tokens added to cycle', [
                            'cycle_id' => $cycle->id,
                            'tokens_added' => $tokensToAdd,
                            'amount' => $paidAmount,
                        ]);
                    }
                } elseif ($groupId) {
                    // Subscription: activate cycles
                    $activatedCount = app(\App\Services\SubscriptionCycleService::class)
                        ->activatePendingCycles($groupId, $pricingTier);

                    \Log::info('Cycles activated after payment', [
                        'group_id' => $groupId,
                        'activated_count' => $activatedCount,
                    ]);
                }
            });

            session()->forget(['pending_payment', 'payment_reference']);

            return redirect()
                ->route('token-subscriptions.index')
                ->with('success', 'Payment successful! Your subscription is now active.');
        } catch (Exception $e) {
            \Log::error('Payment callback processing failed', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('token-subscriptions.index')
                ->with('error', 'An error occurred processing your payment.');
        }
    }
}
