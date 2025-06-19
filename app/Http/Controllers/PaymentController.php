<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\BookSubscription;
use App\Events\SubscriptionUpdated;
use Brick\Money\Money;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Exception;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
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
     */
    public function create()
    {
        $this->authorize('administrate');

        return view('payments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PaymentRequest $request)
    {
        $this->authorize('administrate');

        $reference = Str::beforeLast($request->validated('reference'), '_1326001');

        // Check if it's a book subscription payment
        $bookSubscription = BookSubscription::where('reference', $reference)->first();

        if ($bookSubscription) {
            return $this->processBookSubscriptionPayment($request, $bookSubscription);
        }

        // Original subscription payment logic
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

    private function processBookSubscriptionPayment(PaymentRequest $request, BookSubscription $bookSubscription)
    {
        try {
            $amount = Money::of($request->validated('amount'), 'GHS');

            // Verify amount matches subscription fee
            if ($amount->getAmount()->toFloat() != $bookSubscription->annual_fee) {
                throw ValidationException::withMessages([
                    'amount' => 'Payment amount does not match subscription fee',
                ]);
            }

            // Update subscription status to active
            $bookSubscription->update([
                'status' => 'active',
                'payment_completed_at' => now(),
                'start_date' => now(),
                'end_date' => now()->addYear(),
            ]);

            // Log the payment completion
            activity()
                ->performedOn($bookSubscription)
                ->causedBy(auth()->user())
                ->withProperties([
                    'action' => 'book_subscription_payment_completed',
                    'book_id' => $bookSubscription->book_id,
                    'amount' => $amount->getAmount()->toFloat(),
                    'reference' => $bookSubscription->reference,
                ])
                ->log('Book subscription payment completed');

        } catch (Exception $e) {
            throw ValidationException::withMessages([
                'amount' => 'Invalid payment amount: ' . $e->getMessage(),
            ]);
        }

        return to_route('payments.index')
            ->with('success', 'Book subscription payment processed successfully. Reference: ' . $bookSubscription->reference);
    }
}
