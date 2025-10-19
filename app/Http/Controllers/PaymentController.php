<?php

namespace App\Http\Controllers;

use App\Enums\SubscriptionStatus;
use App\Http\Requests\PaymentRequest;
use App\Models\Chat\UserTokenSubscription;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\BookSubscription;
use App\Events\SubscriptionUpdated;
use App\Enums\PaymentStatus;
use App\Services\TokenSubscriptionService;
use App\Traits\HandlesPayments;
use Brick\Money\Money;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Services\PaystackService;
use App\Models\Student;
use App\Models\StudentPayment;
use App\Models\SchoolFee;
use App\Models\School;
use Illuminate\Support\Facades\Auth;


class PaymentController extends Controller
{
    use HandlesPayments;

     protected $paystack;
     protected $subscriptionService;

    public function __construct(PaystackService $paystack, TokenSubscriptionService $subscriptionService)
    {
        $this->paystack = $paystack;
        $this->subscriptionService = $subscriptionService;
    }

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
                $this->payForBookSubscription($request, $bookSubscription); // $this->processBookSubscriptionPayment($request, $bookSubscription);
                return to_route('payments.index')->with('success', 'Payment for book subscription has been manually recorded.');
            } else {
                throw ValidationException::withMessages([
                    'reference' => 'The provided reference is invalid.',
                ]);
            }
        }

        // Handle regular subscription payment
        $subscription = Subscription::where('reference', $reference)->first();

        if ($subscription) {
            $this->payForSubscription($request, $subscription);
            return to_route('payments.index')->with('success', 'Payment for book subscription has been manually recorded.');
        } else {
            throw ValidationException::withMessages([
                'reference' => 'The provided reference is invalid.',
            ]);
        }
    }

    /**
     * Process the payment for a book subscription.
     *
     * @param PaymentRequest $request
     * @param BookSubscription $bookSubscription
     * @return RedirectResponse
     */
    public function processBookSubscriptionPayment(PaymentRequest $request, BookSubscription $bookSubscription)
    {
        try {
            DB::transaction(function () use ($request, $bookSubscription) {
                $amount = Money::of($request->validated('amount'), 'GHS');
                $payment = new Payment([
                    'reference' => $request->validated('reference'),
                    'amount' => (string)$amount->getAmount(),
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
        if (
            $subscription->status === SubscriptionStatus::PAID
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
                    'amount' => (string)$amount->getAmount(),
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




    public function initialize(Request $request)
    {

        $subscription = Subscription::findOrFail($request->get('subscription'));

        $data = [
            'email' => auth()->user()->email,
            'amount' => (int) $subscription->amount * 100, // Amount in kobo
            'metadata' => [
                'subscription_id' => $subscription->id,
                'name' => auth()->user()->name,
                'phone' => auth()->user()->phone ?? '0000000000',
            ],
            'callback_url' => route('payment.callback'),
        ];

        $response = $this->paystack->initializeTransaction($data);

        return redirect($response['data']['authorization_url']);
    }



    /**
     * Book subscription payment
     */
    public function initializeBook($subscriptionId)
    {
        $subscription = BookSubscription::findOrFail($subscriptionId);

        $data = [
            'email' => auth()->user()->email,
            'amount' => (int) $subscription->annual_fee * 100, // convert to kobo
            'metadata' => [
                'subscription_id' => $subscription->id,
                'type' => 'book',
                'book_id' => $subscription->book_id,
                'name' => auth()->user()->name,
                'phone' => auth()->user()->phone ?? '0000000000',
            ],
            'callback_url' => route('payment.book.callback'),
        ];

        $response = $this->paystack->initializeTransaction($data);

        return redirect($response['data']['authorization_url']);
    }




    public function callback(Request $request)
    {
        $reference = $request->query('reference');
        $response = $this->paystack->verifyTransaction($reference);

        if ($response['status'] && $response['data']['status'] === 'success') {
            $paymentDetails = $response['data'];
            $subscriptionId = $paymentDetails['metadata']['subscription_id'];

            // Find subscription
            $subscription = Subscription::findOrFail($subscriptionId);

            DB::transaction(function () use ($subscription, $paymentDetails) {
                // 1. Update subscription
                $subscription->update([
                    'status' => 'paid',
                    'expires_at' => now()->addMonths($subscription->duration_in_months),
                ]);

                // 2. Record payment
                Payment::create([
                    'reference'       => $paymentDetails['reference'],
                    'amount'          => $subscription->amount,  //$paymentDetails['amount'],
                    'currency'        => $paymentDetails['currency'] ?? 'GHS',
                    'status'          => 'succeeded',
                    'subscription_id' => $subscription->id,
                ]);
            });

            return redirect()
                ->route('subscriptions.index')
                ->with('success', 'Subscription paid successfully! Ref: ' . $subscription->reference);
        }

        return redirect()
            ->route('subscriptions.index')
            ->with('error', 'Payment failed or was cancelled.');
    }


    public function bookCallback(Request $request)
    {
        $reference = $request->query('reference');
        $response = $this->paystack->verifyTransaction($reference);

        if ($response['status'] && $response['data']['status'] === 'success') {
            $paymentDetails = $response['data'];
            $subscriptionId = $paymentDetails['metadata']['subscription_id'];

            // Find the BookSubscription
            $subscription = BookSubscription::findOrFail($subscriptionId);

            DB::transaction(function () use ($subscription, $paymentDetails) {
                // 1. Update book subscription
                $subscription->update([
                    'status' => 'paid',
                    'payment_completed_at' => now(),
                    'end_date' => now()->addYear(),
                ]);

                // 2. Record payment
                Payment::create([
                    'reference'            => $paymentDetails['reference'],
                    'amount'               => $subscription->annual_fee,
                    'currency'             => $paymentDetails['currency'] ?? 'GHS',
                    'status'               => 'succeeded',
                    'subscription_id'      => null, // because this is not a regular subscription
                    'book_subscription_id' => $subscription->id,

                ]);
            });

            return redirect()
                ->to("/books/{$subscription->book_id}")
                ->with('success', 'Book subscription paid successfully! Ref: ' . $subscription->reference);
        }

        return redirect()
            ->to("/books/{$subscription->book_id}")
            ->with('error', 'Book subscription payment failed or was cancelled.');
    }


    //Sub Account
    public function initializeSubAccount(Request $request)
    {
        $data = [
            'email' => auth()->user()->email,
            'amount' => (int) $subscription->amount * 100, // Amount in kobo
            'metadata' => [
                'subscription_id' => $subscription->id,
                'name' => auth()->user()->name,
                'phone' => auth()->user()->phone ?? '0000000000',
            ],
            'callback_url' => route('payment.callback'),
        ];
    }


    /**
     * Initialize token subscription payment
     */
    public function initializeTokenSubscription($subscriptionId)
    {
        $subscription = UserTokenSubscription::findOrFail($subscriptionId);

        // Check if it's a free package
        if ($subscription->package->isFree()) {
            // Activate immediately for free packages
            $subscription->update([
                'status' => 'active',
                'purchased_at' => now(),
            ]);

            return redirect()
                ->route('token-subscriptions.index')
                ->with('success', 'Free token package activated successfully!');
        }

        $data = [
            'email' => auth()->user()->email,
            'amount' => (int) ($subscription->package->price * 100), // convert to kobo
            'reference' => $subscription->reference,
            'metadata' => [
                'subscription_id' => $subscription->id,
                'type' => 'token_subscription',
                'package_name' => $subscription->package->name,
                'name' => auth()->user()->name,
                'phone' => auth()->user()->phone ?? '0000000000',
            ],
            'callback_url' => route('payment.token.callback'),
        ];

        $response = $this->paystack->initializeTransaction($data);

        return redirect($response['data']['authorization_url']);
    }

    public function tokenCallback(Request $request)
    {
        $reference = $request->query('reference');
        $response = $this->paystack->verifyTransaction($reference);

        if ($response['status'] && $response['data']['status'] === 'success') {
            $paymentDetails = $response['data'];
            $subscriptionId = $paymentDetails['metadata']['subscription_id'];

            $subscription = UserTokenSubscription::findOrFail($subscriptionId);

            DB::transaction(function () use ($subscription, $paymentDetails) {
                // 1. Record payment
                Payment::create([
                    'reference' => $paymentDetails['reference'],
                    'amount' => $subscription->package->price,
                    'currency' => $paymentDetails['currency'] ?? 'GHS',
                    'status' => 'succeeded',
                    'token_subscription_id' => $subscription->id,
                ]);

                // 2. Activate subscription (handles both top-ups and regular subscriptions)
                $this->subscriptionService->activateSubscription($subscription);
            });

            // Check if it was a top-up (linked to another active subscription)
            if ($subscription->replaced_by_id) {
                // Refresh the main subscription to get updated token counts
                $mainSubscription = UserTokenSubscription::find($subscription->replaced_by_id);

                if ($mainSubscription && $mainSubscription->status === 'active') {
                    // Refresh to get the latest data from database
                    $mainSubscription->refresh();

                    return redirect()
                        ->route('token-subscriptions.show', $mainSubscription)
                        ->with('success', 'Tokens added successfully! New balance: ' . number_format($mainSubscription->tokens_remaining) . ' tokens');
                }
            }

            // For regular subscriptions (not top-ups), refresh the subscription
            $subscription->refresh();

            return redirect()
                ->route('token-subscriptions.show', $subscription)
                ->with('success', 'Token subscription activated successfully! Ref: ' . $subscription->reference);
        }

        return redirect()
            ->route('token-subscriptions.index')
            ->with('error', 'Token subscription payment failed or was cancelled.');
    }
}
