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
use App\Models\AcademicFeeStructure;
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
use App\Models\Subaccount;
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
     * Initialize book subscription payment with split payment to author
     */
    public function initializeBook($subscriptionId)
    {
        $subscription = BookSubscription::with(['book.author.subaccount'])->findOrFail($subscriptionId);
        $book = $subscription->book;

        // Check if book is free
        if ($book->is_free) {
            return redirect()->route('books.show', $book)
                ->with('info', 'This book is free. No payment required.');
        }

        // Check if author has a subaccount for paid books
        $author = $book->author;
        $authorSubaccount = $author->subaccount;

        $paymentData = [
            'email' => auth()->user()->email,
            'amount' => (int) $subscription->annual_fee * 100, // convert to kobo
            'metadata' => [
                'subscription_id' => $subscription->id,
                'type' => 'book',
                'book_id' => $subscription->book_id,
                'book_title' => $book->title,
                'author_id' => $author->id,
                'author_name' => $author->name,
                'name' => auth()->user()->name,
                'phone' => auth()->user()->phone ?? '0000000000',
            ],
            'callback_url' => route('payment.book.callback'),
        ];

        // Add subaccount for revenue split if author has one configured
        if ($authorSubaccount && $authorSubaccount->subaccount_code) {
            $paymentData['subaccount'] = $authorSubaccount->subaccount_code;

            // Add bearer information for transaction charges
            // 'account' means subaccount bears the charge
            // 'subaccount' means the main account bears the charge
            $paymentData['bearer'] = 'account';

            // Add metadata about revenue split
            $paymentData['metadata']['revenue_split'] = [
                'platform_percentage' => $authorSubaccount->percentage_charge,
                'author_percentage' => 100 - $authorSubaccount->percentage_charge,
                'subaccount_code' => $authorSubaccount->subaccount_code,
            ];
        } else {
            // Log warning if author doesn't have subaccount for paid book
            \Log::warning('Author does not have subaccount configured for paid book', [
                'author_id' => $author->id,
                'book_id' => $book->id,
                'subscription_id' => $subscription->id,
            ]);

            // You can either:
            // 1. Proceed without split (all money goes to platform)
            // 2. Require author to set up subaccount first
            // For now, we'll proceed without split but notify
            $paymentData['metadata']['note'] = 'Author subaccount not configured. Payment will go to platform.';
        }

        $response = $this->paystack->initializeTransaction($paymentData);

        return redirect($response['data']['authorization_url']);
    }

    /**
     * Handle book payment callback with revenue split tracking
     */
    public function bookCallback(Request $request)
    {
        $reference = $request->query('reference');
        $response = $this->paystack->verifyTransaction($reference);

        if ($response['status'] && $response['data']['status'] === 'success') {
            $paymentDetails = $response['data'];
            $subscriptionId = $paymentDetails['metadata']['subscription_id'];

            // Find the BookSubscription
            $subscription = BookSubscription::with(['book.author'])->findOrFail($subscriptionId);
            $book = $subscription->book;
            $author = $book->author;

            DB::transaction(function () use ($subscription, $paymentDetails, $author) {
                // 1. Update book subscription
                $subscription->update([
                    'status' => 'paid',
                    'payment_completed_at' => now(),
                    'end_date' => now()->addYear(),
                ]);

                // 2. Calculate revenue split
                $totalAmount = $subscription->annual_fee;
                $platformCharge = $author->subaccount ? $author->subaccount->percentage_charge : 0;
                $platformAmount = ($totalAmount * $platformCharge) / 100;
                $authorAmount = $totalAmount - $platformAmount;

                // 3. Record payment with split information
                Payment::create([
                    'reference' => $paymentDetails['reference'],
                    'amount' => $totalAmount,
                    'currency' => $paymentDetails['currency'] ?? 'GHS',
                    'status' => 'succeeded',
                    'subscription_id' => null,
                    'book_subscription_id' => $subscription->id,
                    'gateway_reference' => $paymentDetails['id'] ?? null,
                    'notes' => json_encode([
                        'revenue_split' => [
                            'total_amount' => $totalAmount,
                            'platform_amount' => $platformAmount,
                            'platform_percentage' => $platformCharge,
                            'author_amount' => $authorAmount,
                            'author_percentage' => 100 - $platformCharge,
                            'author_id' => $author->id,
                            'author_name' => $author->name,
                            'subaccount_code' => $author->subaccount?->subaccount_code ?? null,
                        ],
                        'book_info' => [
                            'book_id' => $subscription->book_id,
                            'book_title' => $subscription->book->title,
                        ],
                    ]),
                ]);

                // 4. Log revenue split for analytics
                activity()
                    ->causedBy(auth()->user())
                    ->performedOn($subscription)
                    ->withProperties([
                        'action' => 'book_payment_completed',
                        'total_amount' => $totalAmount,
                        'platform_amount' => $platformAmount,
                        'author_amount' => $authorAmount,
                        'author_id' => $author->id,
                        'book_id' => $subscription->book_id,
                    ])
                    ->log('Book subscription payment completed with revenue split');
            });

            return redirect()
                ->to("/books/{$subscription->book_id}")
                ->with('success', 'Book subscription paid successfully! You can now access the book.');
        }

        return redirect()
            ->to("/books/{$subscription->book_id}")
            ->with('error', 'Book subscription payment failed or was cancelled.');
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

        // Check if it's a free package - this should NOT reach here for free packages
        if ($subscription->package->isFree()) {
            \Log::warning('Free package reached payment initialization', [
                'subscription_id' => $subscription->id,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('token-subscriptions.index')
                ->with('error', 'Free trials do not require payment. Please contact support if you see this message.');
        }

        // Check if reference already exists in a completed payment
        $existingPayment = Payment::where('reference', $subscription->reference)
            ->where('status', 'succeeded')
            ->first();

        if ($existingPayment) {
            \Log::warning('Duplicate payment attempt', [
                'subscription_id' => $subscription->id,
                'reference' => $subscription->reference,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('token-subscriptions.index')
                ->with('error', 'This subscription has already been paid for.');
        }

        // Generate a fresh unique reference for Paystack
        $paystackReference = 'TOKEN-' . $subscription->id . '-' . time() . '-' . strtoupper(Str::random(6));

        $data = [
            'email' => auth()->user()->email,
            'amount' => (int) ($subscription->package->price * 100), // convert to kobo
            'reference' => $paystackReference, // Use fresh reference
            'metadata' => [
                'subscription_id' => $subscription->id,
                'type' => 'token_subscription',
                'package_name' => $subscription->package->name,
                'name' => auth()->user()->name,
                'phone' => auth()->user()->phone ?? '0000000000',
            ],
            'callback_url' => route('payment.token.callback'),
        ];

        try {
            $response = $this->paystack->initializeTransaction($data);

            // Store the Paystack reference for verification later
            $subscription->update(['reference' => $paystackReference]);

            return redirect($response['data']['authorization_url']);
        } catch (\Exception $e) {
            \Log::error('Paystack initialization failed', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            return redirect()
                ->route('token-subscriptions.index')
                ->with('error', 'Failed to initialize payment. Please try again or contact support.');
        }
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


    public function showPaymentForm_old(Student $student)
    {
        // Load any related data you need for the view, e.g. school, fees, etc.
        return view('payments.school-fees.feepayment', compact('student'));
    }

    public function showPaymentForm(Student $student)
    {
        // Fetch the fee structure based on student's group and level
        $feeStructure = AcademicFeeStructure::where('school_id', $student->school_id)
            ->where('academic_group_id', $student->academic_group_id)
            ->where('academic_level_id', $student->academic_level_id)
            ->latest() // optional, get the most recent if multiple exist
            ->first();

        $totalAmount = $feeStructure->amount ?? 0;
        $paymentMethod = $feeStructure->payment_method ?? 'Momo';
        $dueDate = $feeStructure->due_date;

        return view('payments.school-fees.feepayment', [
            'student' => $student,
            'totalAmount' => $totalAmount,
            'paymentMethod' => $paymentMethod,
            'dueDate' => $dueDate,
        ]);
    }



    public function processPayment(Request $request, PaystackService $paystack)
    {
        // 1️⃣ Validate request input
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount'     => 'required|numeric|min:1',
        ]);

        // 2️⃣ Get student and related school
        $student = Student::with(['school', 'academicGroup', 'academicLevel'])->findOrFail($validated['student_id']);
        $school  = $student->school;

        if (!$school) {
            return back()->withErrors(['school' => 'Student is not linked to any school.']);
        }

        // 3️⃣ Get school’s Paystack subaccount info
        $subaccount = Subaccount::where('school_id', $school->id)->first();

        if (!$subaccount || !$subaccount->subaccount_code) {
            return back()->withErrors(['payment' => 'This school does not have a registered Paystack subaccount.']);
        }

        // 4️⃣ Determine payer info (student or parent)
        $payer = Auth::user();
        $payer_id   = $payer->id ?? null;
        $payer_type = $payer ? get_class($payer) : null;

        // 5️⃣ Get the current term (academic period)
        $currentTerm = \App\Models\AcademicPeriod::where('is_current', 1)->first();
        $currentTermId = $currentTerm->id ?? null;

        // 6️⃣ Pull the total fee amount for this student’s group, level, and current term
        $feeStructure = \App\Models\AcademicFeeStructure::where('school_id', $school->id)
            ->where('academic_group_id', $student->academic_group_id)
            ->where('academic_level_id', $student->academic_level_id)
            ->where('current_term_id', $currentTermId)
            ->first();

        $termTotalAmount = $feeStructure->amount ?? 0;

        // 7️⃣ Prepare callback URL — include student id
        $callbackUrl = route('feepayment.callback', ['student' => $student->id]);

        // 8️⃣ Prepare Paystack transaction payload
        $paymentData = [
            'email'        => $payer->email ?? 'guest@example.com',
            'amount'       => $validated['amount'] * 100, // Paystack expects amount in pesewas
            'currency'     => 'GHS',
            'callback_url' => $callbackUrl,
            'subaccount'   => $subaccount->subaccount_code,
        ];

        // 9️⃣ Initialize transaction via Paystack API
        $response = $paystack->initializeTransaction($paymentData);

        if (empty($response['status']) || !$response['status'] || empty($response['data']['authorization_url'])) {
            return back()->withErrors(['payment' => 'Unable to initialize payment. Please try again.']);
        }

        // ✅ Extract Paystack reference
        $reference = $response['data']['reference'];

        // 🔟 Record payment in DB (now includes term_total_amount and current_term_id)
        SchoolFee::create([
            'school_id'          => $school->id,
            'student_id'         => $student->id,
            'payer_id'           => $payer_id,
            'payer_type'         => $payer_type,
            'school_name'        => $school->name,
            'amount'             => $validated['amount'],
            'term_total_amount'  => $termTotalAmount,
            'term_id'    => $currentTermId,
            'currency'           => 'GHS',
            'status'             => 'pending',
            'reference'          => $reference,
            'authorization_url'  => $response['data']['authorization_url'],
            'paystack_response'  => json_encode($response),
        ]);

        // 1️⃣1️⃣ Redirect user to Paystack for payment
        return redirect($response['data']['authorization_url']);
    }




    public function paymentCallback(Request $request, PaystackService $paystack, Student $student)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('feepayment.form')
                ->withErrors(['payment' => 'Missing payment reference.']);
        }

        // ✅ Verify transaction with Paystack
        $response = $paystack->verifyTransaction($reference);

        if (empty($response['status']) || !$response['status']) {
            return redirect()->route('feepayment.form')
                ->withErrors(['payment' => 'Payment verification failed.']);
        }

        $data = $response['data'] ?? [];

        // ✅ Update payment record in DB
        $payment = SchoolFee::where('reference', $reference)->first();

        if ($payment) {
            $payment->update([
                'status' => 'succeeded',
                'paystack_response' => json_encode($response),
            ]);
        }

        // ✅ Redirect to Thank You page for the student
        return redirect()
            ->route('feepayment.thankyou', ['student' => $student->id])
            ->with('success', 'Payment successful!');
    }


    public function thankYou(Student $student)
    {
        return view('payments.school-fees.thankyou', compact('student'));
    }
}
