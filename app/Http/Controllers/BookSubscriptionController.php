<?php

namespace App\Http\Controllers;

use App\Enums\SubscriptionStatus;
use App\Models\Book;
use App\Models\BookSubscription;
use App\Traits\HandlesPayments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookSubscriptionController extends Controller
{
    use HandlesPayments;

    /**
     * Show the subscription form for a book.
     */
    public function create(Book $book)
    {
        $redirectResponse = $this->checkSubscription($book);
        if ($redirectResponse) {
            return $redirectResponse;
        }

        $subscription = $book->subscriptions()->where('user_id', auth()->id())->first();

        return view('books.BookSubscriptionForm', [
            'book' => $book,
            'subscription' => $subscription,
            'student' => auth()->user()->student ?? null, // Safely handle if student relationship is missing
        ]);
    }

    /**
     * Check if user already has a subscription. Returns Redirect if true, null otherwise.
     */
    public function checkSubscription(Book $book)
    {
        $existingSubscription = BookSubscription::where('user_id', auth()->id())
            ->where('book_id', $book->id)
            ->where(function ($query) {
                $query->where('status', SubscriptionStatus::PAID)
                    ->orWhere('status', SubscriptionStatus::UNPAID)
                    ->orWhere('status', SubscriptionStatus::PART_PAID)
                    ->orWhere('status', 'pending_payment');
            })
            ->first();

        if ($existingSubscription) {
            if ($existingSubscription->status === SubscriptionStatus::PAID || $existingSubscription->status === 'paid') {
                return redirect()->route('books.show', $book)
                    ->with('info', 'You already have an active subscription to this book.');
            } else {
                // Redirect to the correct existing payment route
                return redirect()->route('subscriptions.payment.show', $existingSubscription->id)
                    ->with('info', 'You have a pending subscription for this book. Please complete the payment.');
            }
        }

        return null;
    }

    /**
     * Initialize a new book subscription.
     */
    public function store(Request $request, Book $book)
    {
        // 1. Prevent duplicate subscription by checking the returned redirect
        $redirectResponse = $this->checkSubscription($book);
        if ($redirectResponse) {
            return $redirectResponse;
        }

        try {
            return DB::transaction(function () use ($book) {
                $reference = 'SUB-' . uniqid();

                // Create or fetch existing subscription
                $subscription = BookSubscription::firstOrCreate(
                    [
                        'user_id' => auth()->id(),
                        'book_id' => $book->id,
                    ],
                    [
                        'reference' => $book->is_free ? 'FREE_' . uniqid() : $reference,
                        'annual_fee' => $book->is_free ? 0 : $book->annual_subscription_fee,
                        'status' => $book->is_free ? 'paid' : 'pending_payment',
                        'start_date' => now(),
                        'end_date' => null,
                        'payment_completed_at' => $book->is_free ? now() : null,
                    ]
                );

                if ($book->is_free) {
                    // Mark subscription as completed
                    if (method_exists($this, 'payForBookSubscription')) {
                        $this->payForBookSubscription([
                            'book' => $book,
                            'subscription' => $subscription,
                            'reference' => $subscription->reference,
                            'amount' => 0.00,
                        ], $subscription);
                    }

                    // Log the subscription creation (wrapped in function_exists to prevent 500 if Spatie package is missing)
                    if (function_exists('activity')) {
                        activity()
                            ->performedOn($book)
                            ->causedBy(auth()->user())
                            ->withProperties([
                                'action' => 'book_subscription_created',
                                'book_id' => $book->id,
                                'subscription_id' => $subscription->id,
                                'amount' => $subscription->annual_fee,
                                'reference' => $subscription->reference,
                            ])
                            ->log('Student initiated book subscription');
                    }

                    return redirect()->route('books.show', $book)
                        ->with('success', 'Subscription created successfully!');
                } else {
                    // 2. FIXED: Redirect to the ACTUAL route defined in your routes file
                    return redirect()->route('subscriptions.payment.show', $subscription->id)
                        ->with('info', 'Please complete the payment to access this book.');
                }
            });
        } catch (\Exception $e) {
            Log::error('Failed to create book subscription', [
                'error' => $e->getMessage(),
                'book_id' => $book->id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(), // Added trace for easier debugging
            ]);

            return back()->with('error', 'Failed to create subscription. Please try again.');
        }
    }

    /**
     * Show payment information for a subscription.
     */
    public function showPayment(BookSubscription $subscription)
    {
        // 3. FIXED: Use user_id consistently to prevent "Attempt to read property 'id' on null"
        if (auth()->id() !== $subscription->user_id) {
            abort(403, 'Unauthorized access to this subscription.');
        }

        if ($subscription->status !== 'pending_payment') {
            return redirect()->route('books.show', $subscription->book)
                ->with('info', 'This subscription is not awaiting payment.');
        }

        return view('subscriptions.payment', [
            'subscription' => $subscription->load('book', 'user'), // Changed 'student' to 'user' for consistency
        ]);
    }

    /**
     * Handle payment verification and subscription activation.
     */
    public function verifyPayment(Request $request, BookSubscription $subscription)
    {
        // 3. FIXED: Use user_id consistently
        if (auth()->id() !== $subscription->user_id) {
            abort(403, 'Unauthorized access to this subscription.');
        }

        if ($subscription->status !== 'pending_payment') {
            return redirect()->route('books.show', $subscription->book)
                ->with('info', 'This subscription is not awaiting payment verification.');
        }

        try {
            DB::transaction(function () use ($subscription) {
                $subscription->update([
                    'status' => 'active', // Ensure this matches your SubscriptionStatus enum if applicable
                    'payment_completed_at' => now(),
                ]);

                if (function_exists('activity')) {
                    activity()
                        ->performedOn($subscription)
                        ->causedBy(auth()->user())
                        ->withProperties([
                            'action' => 'payment_verified',
                            'subscription_id' => $subscription->id,
                            'amount' => $subscription->annual_fee,
                        ])
                        ->log('Payment verified for book subscription');
                }
            });

            return redirect()->route('books.read', $subscription->book)
                ->with('success', 'Payment verified successfully. You can now access the book.');
        } catch (\Exception $e) {
            Log::error('Payment verification failed', [
                'error' => $e->getMessage(),
                'subscription_id' => $subscription->id,
            ]);

            return back()->with('error', 'An error occurred during payment verification. Please try again.');
        }
    }

    /**
     * Cancel a pending subscription and remove it from database.
     */
    public function cancel(BookSubscription $subscription)
    {
        // 3. FIXED: Use user_id consistently
        if (auth()->id() !== $subscription->user_id) {
            abort(403, 'Unauthorized access to this subscription.');
        }

        try {
            DB::transaction(function () use ($subscription) {
                if (function_exists('activity')) {
                    activity()
                        ->performedOn($subscription)
                        ->causedBy(auth()->user())
                        ->withProperties([
                            'action' => 'subscription_cancelled',
                            'subscription_id' => $subscription->id,
                            'book_id' => $subscription->book_id,
                            'reference' => $subscription->reference,
                        ])
                        ->log('Book subscription cancelled and removed');
                }

                $subscription->delete();
            });

            return redirect()->route('books.show', $subscription->book)
                ->with('success', 'Subscription cancelled successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to cancel subscription', [
                'error' => $e->getMessage(),
                'subscription_id' => $subscription->id,
            ]);

            return back()->with('error', 'Failed to cancel subscription. Please try again.');
        }
    }
}
