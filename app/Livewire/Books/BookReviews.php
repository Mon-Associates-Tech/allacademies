<?php

namespace App\Livewire\Books;

use App\Models\Book;
use App\Models\BookReview;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class BookReviews extends Component
{
    use WithPagination;

    public Book $book;

    public $showReviewForm = false;

    public $editingReview = null;

    // Review form fields
    public $rating = 0;

    public $reviewTitle = '';

    public $reviewContent = '';

    // Filters and sorting
    public $sortBy = 'newest';

    public $filterByRating = 'all';

    public $perPage = 5;

    public function mount(Book $book)
    {
        $this->book = $book;

        // Check if user has existing review
        if (auth()->check()) {
            $existingReview = $this->book->getUserReview(auth()->id());
            if ($existingReview) {
                $this->editingReview = $existingReview;
                $this->rating = $existingReview->rating;
                $this->reviewTitle = $existingReview->title ?? '';
                $this->reviewContent = $existingReview->review;
            }
        }
    }

    public function render(): View
    {
        $reviews = $this->getReviews();
        $canUserReview = auth()->check() ? $this->book->canUserReview(auth()->id()) : false;

        return view('livewire.books.book-reviews', [
            'reviews' => $reviews,
            'canUserReview' => $canUserReview,
            'userReview' => auth()->check() ? $this->book->getUserReview(auth()->id()) : null,
            'ratingDistribution' => $this->book->rating_distribution,
        ]);
    }

    private function getReviews()
    {
        $query = $this->book->reviews()
            ->approved()
            ->with('user');

        // Apply rating filter
        if ($this->filterByRating !== 'all') {
            $query->where('rating', $this->filterByRating);
        }

        // Apply sorting
        switch ($this->sortBy) {
            case 'oldest':
                $query->oldest();
                break;
            case 'rating_high':
                $query->orderBy('rating', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'rating_low':
                $query->orderBy('rating', 'asc')->orderBy('created_at', 'desc');
                break;
            case 'helpful':
                $query->mostHelpful();
                break;
            case 'newest':
            default:
                $query->newest();
                break;
        }

        return $query->paginate($this->perPage);
    }

    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    public function toggleReviewForm()
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $this->showReviewForm = ! $this->showReviewForm;

        if (! $this->showReviewForm) {
            $this->resetReviewForm();
        }
    }

    public function submitReview()
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'reviewTitle' => 'nullable|string|max:255',
            'reviewContent' => 'required|string|min:10|max:2000',
        ], [
            'rating.required' => 'Please select a rating.',
            'rating.min' => 'Rating must be at least 1 star.',
            'rating.max' => 'Rating cannot exceed 5 stars.',
            'reviewContent.required' => 'Please write your review.',
            'reviewContent.min' => 'Review must be at least 10 characters long.',
            'reviewContent.max' => 'Review cannot exceed 2000 characters.',
        ]);

        $user = auth()->user();
        $isVerifiedPurchase = $this->checkVerifiedPurchase($user);

        if ($this->editingReview) {
            // Update existing review
            $this->editingReview->update([
                'rating' => $this->rating,
                'title' => $this->reviewTitle,
                'review' => $this->reviewContent,
                'is_approved' => true, // Auto-approve updates for now
            ]);

            session()->flash('message', 'Your review has been updated successfully!');
        } else {
            // Create new review
            BookReview::create([
                'book_id' => $this->book->id,
                'user_id' => $user->id,
                'reviewer_name' => $user->name,
                'reviewer_email' => $user->email,
                'rating' => $this->rating,
                'title' => $this->reviewTitle,
                'review' => $this->reviewContent,
                'is_verified_purchase' => $isVerifiedPurchase,
                'is_approved' => true, // Auto-approve for now
                'approved_at' => now(),
            ]);

            session()->flash('message', 'Thank you for your review! It has been submitted successfully.');
        }

        $this->resetReviewForm();
        $this->showReviewForm = false;

        // Refresh the component
        $this->dispatch('review-submitted');
    }

    public function deleteReview()
    {
        if ($this->editingReview && $this->editingReview->user_id === auth()->id()) {
            $this->editingReview->delete();
            $this->editingReview = null;
            $this->resetReviewForm();
            session()->flash('message', 'Your review has been deleted.');
        }
    }

    public function toggleHelpful($reviewId)
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $review = BookReview::find($reviewId);
        if ($review) {
            $wasHelpful = $review->toggleHelpfulVote(auth()->id());

            $this->dispatch('helpful-toggled', [
                'reviewId' => $reviewId,
                'wasHelpful' => $wasHelpful,
                'helpfulCount' => $review->helpful_count,
            ]);
        }
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function updatedFilterByRating()
    {
        $this->resetPage();
    }

    private function resetReviewForm()
    {
        if (! $this->editingReview) {
            $this->rating = 0;
            $this->reviewTitle = '';
            $this->reviewContent = '';
        }
    }

    private function checkVerifiedPurchase($user): bool
    {
        // Check if user has an active subscription or has borrowed the book
        return $this->book->subscriptions()
            ->where('user_id', $user->id)
            ->where('status', 'paid')
            ->exists() ||
            $this->book->borrowings()
                ->where('user_id', $user->id)
                ->whereNotNull('borrow_date')
                ->exists();
    }

    public function loadMoreReviews()
    {
        $this->perPage += 5;
    }
}
