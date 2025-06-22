<?php

namespace App\Http\Requests;

use App\Enums\PaymentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'reference' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['sometimes', 'string', Rule::in(['GHS', 'USD', 'EUR'])],
            'status' => ['sometimes', 'string', Rule::in(['pending', 'succeeded', 'failed'])],
            'gateway_reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'payment_type' => ['sometimes', 'string', Rule::in(['subscription', 'book_subscription'])],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'reference.required' => 'Payment reference is required.',
            'reference.max' => 'Payment reference cannot exceed 255 characters.',
            'amount.required' => 'Payment amount is required.',
            'amount.numeric' => 'Payment amount must be a valid number.',
            'amount.min' => 'Payment amount must be at least 0.01.',
            'currency.in' => 'Currency must be one of: GHS, USD, EUR.',
            'status.in' => 'Status must be one of: pending, succeeded, failed.',
            'gateway_reference.max' => 'Gateway reference cannot exceed 255 characters.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
            'payment_type.in' => 'Payment type must be either subscription or book_subscription.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Set default values if not provided
        $this->merge([
            'currency' => $this->currency ?? 'GHS',
            'status' => $this->status ?? 'succeeded',
            'payment_type' => $this->payment_type ?? 'subscription',
        ]);
    }
}
