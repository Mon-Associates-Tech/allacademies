<?php

namespace App\Traits;

use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Events\SubscriptionUpdated;
use App\Http\Requests\PaymentRequest;
use App\Models\BookSubscription;
use App\Models\Payment;
use App\Models\Subscription;
use Brick\Money\Money;
use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait HandlesPayments
{
    public function payForBookSubscription(FormRequest|Request|array|PaymentRequest $request, BookSubscription $bookSubscription): array|\Illuminate\Http\RedirectResponse
    {
        // Check if already paid
        if ($bookSubscription->status === SubscriptionStatus::PAID) {
            throw ValidationException::withMessages([
                'reference' => 'This book subscription has already been paid for.',
            ]);
        }
        $payment = null;
        try {
            // Extract and validate data based on request type
            $validatedData = $this->extractAndValidatePaymentData($request);

            DB::transaction(function () use ($validatedData, $bookSubscription) {
                $money = Money::of($validatedData['amount'], $validatedData['currency'] ?? 'GHS');
               // dd($money->getAmount()->getIntegralPart());

                $payment = new Payment([
                    'reference' => $validatedData['reference'],
                    'amount' => (string)$money->getAmount(),
                    'status' => PaymentStatus::from($validatedData['status'] ?? 'succeeded'),
                    'currency' => $validatedData['currency'] ?? 'GHS',
                    'book_subscription_id' => $bookSubscription->id,
                    'gateway_reference' => $validatedData['gateway_reference'] ?? null,
                    'notes' => $validatedData['notes'] ?? null,
                ]);
                $payment->save();

                $bookSubscription->status = SubscriptionStatus::PAID;
                $bookSubscription->annual_fee = (float)$bookSubscription->annual_fee += (float)$money->getAmount()->getIntegralPart();
                $bookSubscription->payment_completed_at = now();
                $bookSubscription->save();
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            logError($e);
            return back()->with('error', 'An error occurred while processing the payment. Please try again.');
        }


        return [
            'payment' => $payment,
            'subscription' => $bookSubscription,
        ];
    }

    public function payForSubscription(FormRequest|Request|array|PaymentRequest $request, Subscription $subscription)
    {
        // Check if already paid
        if ($subscription->status === SubscriptionStatus::PAID) {
            throw ValidationException::withMessages([
                'reference' => 'This subscription has already been paid for.',
            ]);
        }

        try {
            // Extract and validate data based on request type
            $validatedData = $this->extractAndValidatePaymentData($request);

            DB::transaction(function () use ($validatedData, $subscription) {
                $amount = Money::of($validatedData['amount'], $validatedData['currency'] ?? 'GHS');

                $payment = new Payment([
                    'reference' => $validatedData['reference'],
                    'amount' => (string)$amount->getAmount(),
                    'status' => PaymentStatus::from($validatedData['status'] ?? 'succeeded'),
                    'currency' => $validatedData['currency'] ?? 'GHS',
                    'gateway_reference' => $validatedData['gateway_reference'] ?? null,
                    'notes' => $validatedData['notes'] ?? null,
                ]);

                // Associate with subscription
                $payment->subscription()->associate($subscription);
                $payment->save();

                $subscription->status = SubscriptionStatus::PAID;
                $subscription->save();

                // Dispatch the SubscriptionUpdated event
                SubscriptionUpdated::dispatch($subscription);
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred while processing the payment. Please try again.');
        }

        return to_route('payments.index')->with('success', 'Payment for subscription has been manually recorded.');
    }

    /**
     * Extract and validate payment data from different request types
     *
     * @param FormRequest|Request|array|PaymentRequest $request
     * @return array
     * @throws ValidationException
     */
    private function extractAndValidatePaymentData(FormRequest|Request|array|PaymentRequest $request): array
    {
        // If it's already a PaymentRequest (validated FormRequest), just return validated data
        if ($request instanceof PaymentRequest) {
            return $request->validated();
        }

        // If it's a FormRequest, get validated data
        if ($request instanceof FormRequest) {
            return $request->validated();
        }

        // If it's an array, validate it manually
        if (is_array($request)) {
            return $this->validatePaymentArray($request);
        }

        // If it's a regular Request, validate the input
        if ($request instanceof Request) {
            return $this->validatePaymentRequest($request);
        }

        throw new Exception('Invalid request type provided');
    }

    /**
     * Validate payment data from array
     *
     * @param array $data
     * @return array
     * @throws ValidationException
     */
    private function validatePaymentArray(array $data): array
    {
        $validator = Validator::make($data, $this->getPaymentValidationRules(), $this->getPaymentValidationMessages());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return array_merge($this->getPaymentDefaults(), $validator->validated());
    }

    /**
     * Validate payment data from Request
     *
     * @param Request $request
     * @return array
     * @throws ValidationException
     */
    private function validatePaymentRequest(Request $request): array
    {
        $validatedData = $request->validate($this->getPaymentValidationRules(), $this->getPaymentValidationMessages());

        return array_merge($this->getPaymentDefaults(), $validatedData);
    }

    /**
     * Get payment validation rules
     *
     * @return array
     */
    private function getPaymentValidationRules(): array
    {
        return [
            'reference' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.00'],
            'currency' => ['sometimes', 'string', 'in:GHS,USD,EUR'],
            'status' => ['sometimes', 'string', 'in:pending,succeeded,failed'],
            'gateway_reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get payment validation messages
     *
     * @return array
     */
    private function getPaymentValidationMessages(): array
    {
        return [
            'reference.required' => 'Payment reference is required.',
            'reference.max' => 'Payment reference cannot exceed 255 characters.',
            'amount.required' => 'Payment amount is required.',
            'amount.numeric' => 'Payment amount must be a valid number.',
            'amount.min' => 'Payment amount must be at least 0.00.',
            'currency.in' => 'Currency must be one of: GHS, USD, EUR.',
            'status.in' => 'Status must be one of: pending, succeeded, failed.',
            'gateway_reference.max' => 'Gateway reference cannot exceed 255 characters.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }

    /**
     * Get default payment values
     *
     * @return array
     */
    private function getPaymentDefaults(): array
    {
        return [
            'currency' => 'GHS',
            'status' => 'succeeded',
        ];
    }
}
