<?php

namespace App\Http\Controllers;

use App\Enums\SubscriptionStatus;
use App\Models\Book;
use App\Models\BookSubscription;
use App\Services\PaymentGateway;
use App\Traits\HandlesPayments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookSubscriptionController extends Controller
{
    use HandlesPayments;

    /**
     * Show the subscription form for a book.
     */
    public function create(Book $book)
    {
        $this->checkSubscription($book);
        $subscription = $book->subscriptions()->where('user_id', auth()->user()->id)->where('book_id', $book->id)->first();

        return view('books.BookSubscriptionForm', [
            'book' => $book,
            'subscription' => $subscription,
            'student' => auth()->user()->student,
        ]);
    }

    public function checkSubscription(Book $book)
    {
        $existingSubscription = BookSubscription::where('user_id', auth()->user()->id)
            ->where('book_id', $book->id)
            ->where(function ($query) {
                $query->where('status', SubscriptionStatus::PAID)
                    ->orWhere('status', SubscriptionStatus::UNPAID)
                    ->orWhere('status', SubscriptionStatus::PART_PAID);
            })
            ->first();

        if ($existingSubscription) {
            if ($existingSubscription->status === SubscriptionStatus::PAID) {
                return redirect()->route('books.show', $book)
                    ->with('info', 'You already have an active subscription to this book.');
            } else {
                return redirect()->route('books.show', $book)
                    ->with('info', 'You have a pending subscription for this book. Please complete the payment.');

//                return redirect()->route('subscriptions.payment.show', $existingSubscription)
//                    ->with('info', 'You have a pending subscription for this book. Please complete the payment.');
            }
        }
    }

    /**
     * Initialize a new book subscription.
     */
   public function store(Request $request, Book $book)
   {
    // Prevent duplicate subscription for this book
    $this->checkSubscription($book);

    try {
        return DB::transaction(function () use ($book, $request) {
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
                $this->payForBookSubscription([
                    'book' => $book,
                    'subscription' => $subscription,
                    'reference' => $subscription->reference,
                    'amount' => 0.00,
                ], $subscription);

                $redirectRoute = route('books.show', $book);
            } else {

                // Redirect to Paystack initialize with type=book
                // return redirect()->route('payment.initialize', [
                //     'id' => $subscription->id,
                //     'type' => 'book',
                // ]);

                return redirect()->route('payment.book.initialize', ['subscription' => $subscription->id]);
            }

            // Log the subscription creation
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

            return redirect($redirectRoute)
                ->with('success', 'Subscription created successfully!');
        });
    } catch (\Exception $e) {
        Log::error('Failed to create book subscription', [
            'error' => $e->getMessage(),
            'book_id' => $book->id,
            'user_id' => auth()->id(),
        ]);

        return back()->with('error', 'Failed to create subscription. Please try again.');
    }
}


    /**
     * Show payment information for a subscription.
     */
    public function showPayment(BookSubscription $subscription)
    {
        // Authorize access - only the student who created the subscription can view it
        if (auth()->user()->student->id !== $subscription->student_id) {
            abort(403, 'Unauthorized access to this subscription.');
        }

        // Verify subscription is pending payment
        if ($subscription->status !== 'pending_payment') {
            return redirect()->route('books.show', $subscription->book)
                ->with('info', 'This subscription is not awaiting payment.');
        }

        return view('subscriptions.payment', [
            'subscription' => $subscription->load('book', 'student'),
        ]);
    }

    /**
     * Handle payment verification and subscription activation.
     */
    public function verifyPayment(Request $request, BookSubscription $subscription)
    {
        // Authorize access
        if (auth()->user()->student->id !== $subscription->student_id) {
            abort(403, 'Unauthorized access to this subscription.');
        }

        // Verify subscription status
        if ($subscription->status !== 'pending_payment') {
            return redirect()->route('books.show', $subscription->book)
                ->with('info', 'This subscription is not awaiting payment verification.');
        }

        try {
            // For now, we'll assume payment is verified manually
            // In the future, you can integrate with actual payment gateway

            DB::transaction(function () use ($subscription) {
                // Update subscription status
                $subscription->update([
                    'status' => 'active',
                    'payment_completed_at' => now(),
                ]);

                // Log the payment verification
                activity()
                    ->performedOn($subscription)
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'action' => 'payment_verified',
                        'subscription_id' => $subscription->id,
                        'amount' => $subscription->annual_fee,
                    ])
                    ->log('Payment verified for book subscription');
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
        // Authorize access - only the student who created the subscription can cancel it
        if (auth()->user()->id !== $subscription->user_id) {
            abort(403, 'Unauthorized access to this subscription.');
        }

        // Verify subscription can be cancelled
        if ($subscription->status !== 'pending_payment') {
            //  return back()->with('error', 'Only pending subscriptions can be cancelled.');
        }

        try {
            DB::transaction(function () use ($subscription) {
                // Log the cancellation before deleting
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

                // Delete the subscription from database
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

    /**
     * Go back to the book show page from payment instructions.
     */
    public function goBack(BookSubscription $subscription)
    {
        // Authorize access
        if (auth()->user()->id !== $subscription->user_id) {
            abort(403, 'Unauthorized access to this subscription.');
        }

        return redirect()->route('books.show', $subscription->book);
    }
}
