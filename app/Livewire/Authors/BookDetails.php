<?php

namespace App\Livewire\Authors;

use App\Models\Book;
use App\Models\BookSubscription;
use Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BookDetails extends Component
{
    use AuthorizesRequests;

    public Book $book;
    public $showPdfReader = false;
    public $currentPage = 1;
    public $canRead = true;
    public $userSubscription = null;
    public $subscriptionData = [];
    public $isLoading = false;

    public function mount(Book $book)
    {
        $this->authorize('view', $book);

        $this->book = $book->load([
            'author',
            'bookCategory',
            'subscriptions' => fn($query) => $query->latest()->take(5),
            'borrowings' => fn($query) => $query->latest()->take(5)
        ]);
        $this->checkUserSubscription();

    }

    public function checkUserSubscription()
    {
        if (Auth::check() && Auth::user()->student) {
            $this->userSubscription = BookSubscription::where('student_id', Auth::user()->student->id)
                ->where('book_id', $this->book->id)
                ->whereIn('status', ['active', 'pending_payment'])
                ->first();

            // User can read if they have an active subscription or if the book is free and they're subscribed
            $this->canRead =  true; // = $this->userSubscription && $this->userSubscription->status === 'active';
        }
    }

    public function subscribeToBook()
    {
        $this->isLoading = true;

        if (!Auth::check()) {
            session()->flash('error', 'Please log in to subscribe to this book.');
            $this->isLoading = false;
            return redirect()->route('login');
        }

        $student = Auth::user()->student;

        if (!$student) {
            session()->flash('error', 'Student profile not found.');
            $this->isLoading = false;
            return;
        }

        if (!$this->book->has_softcopy) {
            session()->flash('error', 'This book is not available for subscription.');
            $this->isLoading = false;
            return;
        }

        $existingSubscription = BookSubscription::where('student_id', $student->id)
            ->where('book_id', $this->book->id)
            ->whereIn('status', ['active', 'pending_payment'])
            ->first();

        if ($existingSubscription) {
            if ($existingSubscription->status === 'active') {
                session()->flash('error', 'You are already subscribed to this book.');
            } else {
                session()->flash('error', 'You have a pending subscription for this book. Please complete payment.');
            }
            $this->isLoading = false;
            return;
        }

        // Create subscription
        $reference = 'BS' . time() . $student->id . $this->book->id;

        $subscription = BookSubscription::create([
            'student_id' => $student->id,
            'book_id' => $this->book->id,
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'status' => $this->book->is_free ? 'active' : 'pending_payment',
            'reference' => $reference,
            'annual_fee' => $this->book->annual_subscription_fee ?? 0,
            'payment_completed_at' => $this->book->is_free ? now() : null,
        ]);

        // If book is free, activate subscription immediately
        if ($this->book->is_free) {
            $this->userSubscription = $subscription;
            $this->canRead = true;
            session()->flash('success', 'Congratulations! You have successfully subscribed to this free book. You can now start reading!');
        } else {
            // Set subscription data for payment modal
            $this->subscriptionData = [
                'book_title' => $this->book->title,
                'amount' => $subscription->annual_fee,
                'reference' => $reference,
                'subscription_id' => $subscription->id
            ];
            $this->dispatch('showSubscriptionModal', $this->subscriptionData);
            session()->flash('success', 'Subscription initiated! Please proceed with payment to activate your access.');
        }

        // Log subscription activity
        activity()
            ->performedOn($subscription)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'initiated_book_subscription',
                'book_id' => $this->book->id,
                'book_title' => $this->book->title,
                'subscription_duration' => '1 year',
                'annual_fee' => $subscription->annual_fee,
                'reference' => $reference,
                'status' => $subscription->status
            ])
            ->log('Student initiated book subscription');

        $this->checkUserSubscription();
        $this->isLoading = false;
    }


    public function openPdfReader()
    {
        if ($this->book->content_url) {
            $this->showPdfReader = true;

            Log::info('Opening PDF reader', [
                'content_url' => $this->book->content_url,
                'current_page' => $this->currentPage
            ]);

            // Dispatch event to trigger PDF reader with consistent structure
            $this->dispatch('pdf-reader-open', [
                'pdfUrl' => $this->book->content_url,
                'title' => $this->book->title,
                'currentPage' => $this->currentPage
            ]);

        } else {
            Log::error('No content URL available for book', ['book_id' => $this->book->id]);
        }
    }

    public function closePdfReader()
    {
        $this->showPdfReader = false;
    }

    public function updateCurrentPage($page)
    {
        $this->currentPage = $page;
    }

    public function render()
    {
        return view('livewire.authors.BookDetailsPage');
    }
}
