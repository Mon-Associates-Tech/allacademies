<?php

namespace App\Livewire\Students;

use App\Models\Book;
use App\Models\BookSubscription;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BookSubscriptionModal extends Component
{
    public $showModal = false;
    public $subscriptionData = [];
    public $step = 1; // 1: conditions, 2: payment info
    public $acceptedConditions = false;

    protected $listeners = [
        'showSubscriptionModal' => 'show',
        'closeSubscriptionModal' => 'close'
    ];

    public function show($subscriptionData): void
    {
        $this->subscriptionData = $subscriptionData;
        $this->showModal = true;
        $this->step = 1;
        $this->acceptedConditions = false;
    }

    public function close(): void
    {
        $this->showModal = false;
        $this->subscriptionData = [];
        $this->step = 1;
        $this->acceptedConditions = false;
    }

    public function closeSubscriptionModal(): void
    {
        $this->close();
    }

    public function proceedToPayment(): void
    {
        if (!$this->acceptedConditions) {
            session()->flash('error', 'You must accept the subscription conditions to proceed.');
            return;
        }

        $this->step = 2;
    }

    public function confirmSubscription(): void
    {
        if (!$this->acceptedConditions) {
            session()->flash('error', 'You must accept the subscription conditions to proceed.');
            return;
        }

        $student = Auth::user()->student;
        $bookId = $this->subscriptionData['book_id'] ?? null;

        if (!$bookId) {
            session()->flash('error', 'Invalid book selection.');
            return;
        }

        $book = Book::findOrFail($bookId);

        // Check if already subscribed
        $existingSubscription = BookSubscription::where('student_id', $student->id)
            ->where('book_id', $bookId)
            ->first();

        if ($existingSubscription) {
            if ($existingSubscription->status === 'active') {
                session()->flash('error', 'You are already subscribed to this book.');
                $this->close();
                return;
            } elseif ($existingSubscription->status === 'pending_payment') {
                session()->flash('error', 'You have a pending subscription for this book.');
                $this->close();
                return;
            }
        }

        // Create subscription with pending payment status
        $reference = 'SUB-' . strtoupper(uniqid()) . '-' . $student->id;

        $subscription = BookSubscription::create([
            'student_id' => $student->id,
            'book_id' => $bookId,
            'annual_fee' => $book->annual_subscription_fee,
            'status' => 'pending_payment',
            'reference' => $reference,
        ]);

        // Update subscription data with reference
        $this->subscriptionData['reference'] = $reference;
        $this->subscriptionData['subscription_id'] = $subscription->id;

        // Log the subscription creation
        activity()
            ->performedOn($book)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'book_subscription_created',
                'book_id' => $bookId,
                'subscription_id' => $subscription->id,
                'amount' => $book->annual_subscription_fee,
                'reference' => $reference,
            ])
            ->log('Student created book subscription');

        $this->step = 2;

        session()->flash('success', 'Subscription created successfully! Please complete payment to activate access.');
    }

    public function render(): View|Application|Factory|\Illuminate\View\View
    {
        return view('livewire.students.book-subscription-modal');
    }
}
