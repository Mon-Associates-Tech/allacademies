<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\BookSubscription;
use App\Events\SubscriptionUpdated;
use App\Enums\PaymentStatus;
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
    public function index(Request $request)
    {
        $this->authorize('administrate');

        $payments = Payment::query()
            ->with(['subscription.user', 'bookSubscription.user', 'bookSubscription.book'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('reference', 'LIKE', "%{$search}%")
                      ->orWhere('amount', 'LIKE', "%{$search}%")
                      ->orWhereHas('subscription.user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'LIKE', "%{$search}%")
                                   ->orWhere('email', 'LIKE', "%{$search}%");
                      });
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->when($request->filled('currency'), function ($query) use ($request) {
                $query->where('currency', $request->input('currency'));
            })
            ->when($request->filled('type'), function ($query) use ($request) {
                if ($request->input('type') === 'subscription') {
                    $query->whereNotNull('subscription_id');
                } elseif ($request->input('type') === 'book_subscription') {
                    $query->whereNotNull('book_subscription_id');
                }
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->input('date_from'));
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->input('date_to'));
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        // Calculate statistics
        $stats = [
            'total_payments' => Payment::count(),
            'total_amount' => Payment::where('status', 'succeeded')->sum('amount'),
            'this_month' => Payment::whereMonth('created_at', now()->month)->count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
        ];

        return view('payments.index', [
            'payments' => $payments,
            'stats' => $stats,
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
        $paymentType = $request->validated('payment_type', 'subscription');

        // Check if it's a book subscription payment
        if ($paymentType === 'book_subscription') {
            $bookSubscription = BookSubscription::where('reference', $reference)->first();
            if ($bookSubscription) {
                return $this->processBookSubscriptionPayment($request, $bookSubscription);
            } else {
                throw ValidationException::withMessages([
                    'reference' => 'No book subscription found for this reference.',
                ]);
            }
        }

        // Regular subscription payment logic
        /** @var \App\Models\Subscription $subscription */
        $subscription = Subscription::query()->where('reference', $reference)->firstOr(callback: function () {
            throw ValidationException::withMessages([
                'reference' => 'No subscriptions found for payment reference.',
            ]);
        });

        try {
            $currency = $request->validated('currency', 'GHS');
            $amount = Money::of($request->validated('amount'), $currency);
            $status = $request->validated('status', 'succeeded');

            $payment = $subscription->payments()->create([
                'reference' => $reference,
                'amount' => (string) $amount->getAmount(),
                'currency' => $currency,
                'status' => PaymentStatus::from($status),
                'gateway_reference' => $request->validated('gateway_reference'),
                'notes' => $request->validated('notes'),
            ])->refresh();

            // Only trigger subscription update if payment succeeded
            if ($status === 'succeeded') {
                event(new SubscriptionUpdated($subscription));
            }

            // Log the payment creation
            activity()
                ->performedOn($payment)
                ->causedBy(auth()->user())
                ->withProperties([
                    'action' => 'payment_created',
                    'subscription_id' => $subscription->id,
                    'amount' => $amount->getAmount()->toFloat(),
                    'currency' => $currency,
                    'status' => $status,
                ])
                ->log('Payment created manually');

        } catch (Exception $e) {
            throw ValidationException::withMessages([
                'amount' => 'Invalid payment amount: ' . $e->getMessage(),
            ]);
        }

        return to_route('payments.index')
            ->with('success', "Payment created successfully. Reference: {$payment->reference}, Amount: {$payment->currency} {$payment->amount}");
    }

    private function processBookSubscriptionPayment(PaymentRequest $request, BookSubscription $bookSubscription)
    {
        try {
            $currency = $request->validated('currency', 'GHS');
            $amount = Money::of($request->validated('amount'), $currency);
            $status = $request->validated('status', 'succeeded');

            // Verify amount matches subscription fee for succeeded payments
            if ($status === 'succeeded' && $amount->getAmount()->toFloat() != $bookSubscription->annual_fee) {
                throw ValidationException::withMessages([
                    'amount' => 'Payment amount does not match the book subscription fee of ' . $bookSubscription->annual_fee,
                ]);
            }

            // Create payment record
            $payment = $bookSubscription->payments()->create([
                'reference' => $bookSubscription->reference,
                'amount' => (string) $amount->getAmount(),
                'currency' => $currency,
                'status' => PaymentStatus::from($status),
                'gateway_reference' => $request->validated('gateway_reference'),
                'notes' => $request->validated('notes'),
            ]);

            // Only update subscription status if payment succeeded
            if ($status === 'succeeded') {
                $bookSubscription->update([
                    'status' => 'active',
                    'payment_completed_at' => now(),
                    'start_date' => now(),
                    'end_date' => now()->addYear(),
                ]);
            }

            // Log the payment completion
            activity()
                ->performedOn($bookSubscription)
                ->causedBy(auth()->user())
                ->withProperties([
                    'action' => 'book_subscription_payment_created',
                    'book_id' => $bookSubscription->book_id,
                    'amount' => $amount->getAmount()->toFloat(),
                    'currency' => $currency,
                    'status' => $status,
                    'reference' => $bookSubscription->reference,
                ])
                ->log('Book subscription payment created manually');

        } catch (Exception $e) {
            throw ValidationException::withMessages([
                'amount' => 'Invalid payment amount: ' . $e->getMessage(),
            ]);
        }

        return to_route('payments.index')
            ->with('success', "Book subscription payment created successfully. Reference: {$bookSubscription->reference}, Status: {$status}");
    }
}
