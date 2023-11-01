<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Events\SubscriptionUpdated;
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
        $this->authorize('administrate');

        $payments = Payment::query()->latest('id')->paginate();

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
        $this->authorize('administrate');

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
        $this->authorize('administrate');

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
            ])->refresh();

            event(new SubscriptionUpdated($subscription));
        } catch (Exception) {
            throw ValidationException::withMessages([
                'amount' => 'Invalid amount',
            ]);
        }

        return to_route('payments.index')
            ->with('success', __('status.payment.created', [
                'currency' => $payment->currency,
                'amount' => $payment->amount,
                'reference' => $payment->reference,
            ]));
    }
}
