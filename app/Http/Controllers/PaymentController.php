<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Events\PaymentSucceeded;
use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\PaymentRequest;
use App\Models\Subscription;
use Brick\Money\Money;
use Exception;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payments = Payment::query()->get();

        return view('payments.index', [
            'payments' => $payments,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('payments.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PaymentRequest $request)
    {
        $reference = Str::beforeLast($request->validated('reference'), '_1326001');

        /** @var \App\Models\Subscription $subscription */
        $subscription = Subscription::query()->where('reference', $reference)->firstOr(callback: function () {
            throw ValidationException::withMessages([
                'reference' => 'No subscriptions found for payment',
            ]);
        });

        try {
            $amount = Money::of($request->validated('amount'), 'GHS');

            $payment = $subscription->payments()->create([
                'reference' => $reference,
                'amount' => (string) $amount->getAmount(),
                'status' => PaymentStatus::SUCCEEDED,
            ]);

            event(new PaymentSucceeded($payment));
        } catch (Exception) {
            throw ValidationException::withMessages([
                'amount' => 'Invalid amount',
            ]);
        }

        return to_route('payments.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
