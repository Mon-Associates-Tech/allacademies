<?php

namespace App\Http\Controllers;

use Exception;
use Brick\Money\Money;
use App\Models\Payment;
use Illuminate\Support\Str;
use App\Enums\PaymentStatus;
use App\Models\Subscription;
use App\Models\SubscriptionRenewal;
use App\Events\SubscriptionUpdated;
use App\Events\SubscriptionRenewed;
use App\Http\Requests\PaymentRequest;
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
        $subscription = Subscription::query()->where('reference', $reference)->firstOr(callback: function () use ($reference) {
            return SubscriptionRenewal::query()->where('reference', $reference)->with('subscription')->firstOr(callback: function () {
                throw ValidationException::withMessages([
                    'reference' => 'No subscriptions found for payment',
                ]);
            });
        });

        try {
            $amount = Money::of($request->validated('amount'), 'GHS');

            $payment = $subscription->payments()->create([
                'reference' => $reference,
                'amount' => (string) $amount->getAmount(),
                'status' => PaymentStatus::SUCCEEDED,
            ])->refresh();

            if (!$subscription->subscription) {
                event(new SubscriptionUpdated($subscription));
            } else {
                /** @var \App\Models\SubscriptionRenewal $subscription */
                event(new SubscriptionRenewed($subscription));
            }
        } catch (Exception) {
            throw ValidationException::withMessages([
                'amount' => 'Invalid amount',
            ]);
        }
        // dd($subscription->subscription->expires_at->addMonths((int)($subscription->subscription->duration)));
        return to_route('payments.index')
            ->with('success', __('status.payment.created', [
                'currency' => $payment->currency,
                'amount' => $payment->amount,
                'reference' => $payment->reference,
            ]));
    }
}
