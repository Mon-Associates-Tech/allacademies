<?php

namespace App\BookShop\Services;

use App\BookShop\Enums\PaymentStatus;
use App\BookShop\Exceptions\OrderPlacementException;
use App\BookShop\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderPaymentService
{
    public function __construct(private readonly PaystackService $paystack)
    {
    }

    /**
     * @throws OrderPlacementException
     */
    public function initialize(Order $order, string $callbackUrl): string
    {
        if ($order->isPaid()) {
            throw new OrderPlacementException('This order has already been paid for.');
        }

        $reference = $this->generateReference($order);

        $data = [
            'email' => $order->customer->email,
            'amount' => (int) round($order->subtotal * 100), // Paystack expects the smallest currency unit (pesewas)
            'currency' => 'GHS',
            'reference' => $reference,
            'callback_url' => $callbackUrl,
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'branch_id' => $order->branch_id,
            ],
        ];

        // If the branch has no subaccount configured yet, payment still
        // goes through - it just settles to the platform's main Paystack
        // account instead of being split. Not blocking checkout on
        // payment setup being complete keeps a branch without banking
        // details on file yet from being unable to sell at all.
        $paymentAccount = $order->branch?->paymentAccount;
        if ($paymentAccount && $paymentAccount->isReadyForPayments()) {
            $data['subaccount'] = $paymentAccount->subaccount_code;
        }

        try {
            $response = $this->paystack->initializeTransaction($data);
        } catch (\Throwable $e) {
            Log::error('BookShop: Paystack transaction initialize failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            throw new OrderPlacementException('Could not start payment. Please try again in a moment.');
        }

        if (empty($response['status']) || ! $response['status']) {
            throw new OrderPlacementException($response['message'] ?? 'Could not start payment. Please try again.');
        }

        // Overwrites any previous reference on retry - only the latest
        // payment attempt is tracked per order, not a full attempt
        // history. See SETUP.md if a retry audit trail turns out to matter.
        $order->update(['payment_reference' => $reference]);

        return $response['data']['authorization_url'];
    }

    /**
     * @throws OrderPlacementException
     */
    public function verify(string $reference): Order
    {
        $order = Order::where('payment_reference', $reference)->first();

        if (! $order) {
            throw new OrderPlacementException('No matching order found for this payment.');
        }

        if ($order->isPaid()) {
            return $order; // already processed - avoid double-handling a repeat callback hit
        }

        try {
            $response = $this->paystack->verifyTransaction($reference);
        } catch (\Throwable $e) {
            Log::error('BookShop: Paystack transaction verify failed', [
                'order_id' => $order->id,
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            throw new OrderPlacementException('Could not verify payment. Please contact support with your order number.');
        }

        $success = ! empty($response['status']) && $response['status']
            && ($response['data']['status'] ?? null) === 'success';

        $order->update([
            'payment_status' => $success ? PaymentStatus::PAID : PaymentStatus::FAILED,
            'paid_at' => $success ? now() : null,
        ]);

        return $order->fresh();
    }

    private function generateReference(Order $order): string
    {
        return 'BKSHP-'.$order->order_number.'-'.strtoupper(Str::random(6));
    }
}
