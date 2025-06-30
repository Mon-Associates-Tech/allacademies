<?php

namespace App\Http\Controllers;

use App\Enums\SubscriptionStatus;
use App\Http\Requests\PaymentRequest;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\BookSubscription;
use App\Events\SubscriptionUpdated;
use App\Enums\PaymentStatus;
use Brick\Money\Money;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\DB;

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
                    'reference' => 'The provided reference is invalid.',
                ]);
            }
        }

        // Handle regular subscription payment
        $subscription = Subscription::where('reference', $reference)->first();

        if ($subscription) {
            return $this->processSubscriptionPayment($request, $subscription);
        } else {
            throw ValidationException::withMessages([
                'reference' => 'The provided reference is invalid.',
            ]);
        }
    }

    /**
     * Process the payment for a regular subscription.
     *
     * @param PaymentRequest $request
     * @param Subscription $subscription
     * @return RedirectResponse
     * @throws ValidationException|\Throwable
     */
    private function processSubscriptionPayment(PaymentRequest $request, Subscription $subscription)
    {
        if ($subscription->status === SubscriptionStatus::PAID
        ) {
            throw ValidationException::withMessages([
                'reference' => 'This subscription has already been paid for.',
            ]);
        }

        try {
            DB::transaction(function () use ($request, $subscription) {
                $amount = Money::of($request->validated('amount'), 'GHS');
                $payment = new Payment([
                    'reference' => $request->validated('reference'),
                    'amount' => (string) $amount->getAmount(),
                    'status' => PaymentStatus::SUCCEEDED,
                    'currency' => 'GHS',
                ]);
                $payment->subscription()->associate($subscription);
                $payment->save();

                $subscription->status = SubscriptionStatus::PAID;
                $subscription->save();

                // Dispatch the SubscriptionUpdated event
                SubscriptionUpdated::dispatch($subscription);
            });
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred while processing the payment. Please try again.');
        }

        return to_route('payments.index')->with('success', 'Payment for subscription has been manually recorded.');
    }

    /**
     * Process the payment for a book subscription.
     *
     * @param PaymentRequest $request
     * @param BookSubscription $bookSubscription
     * @return RedirectResponse
     */
    private function processBookSubscriptionPayment(PaymentRequest $request, BookSubscription $bookSubscription)
    {
        try {
            DB::transaction(function () use ($request, $bookSubscription) {
                $amount = Money::of($request->validated('amount'), 'GHS');
                $payment = new Payment([
                    'reference' => $request->validated('reference'),
                    'amount' => (string) $amount->getAmount(),
                    'status' => PaymentStatus::SUCCEEDED,
                    'currency' => 'GHS',
                    'book_subscription_id' => $bookSubscription->id,
                ]);
                $payment->save();

                $bookSubscription->status = SubscriptionStatus::PAID;
                $bookSubscription->save();
            });
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred while processing the payment. Please try again.');
        }

        return to_route('payments.index')->with('success', 'Payment for book subscription has been manually recorded.');
    }
}
