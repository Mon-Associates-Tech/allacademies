<?php

namespace App\Livewire\Authors;

use App\Models\Author;
use App\Models\BookReview;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Reviews extends Component
{
    use WithPagination;

    public Author $author;

    public $search = '';

    public $ratingFilter = 'all';

    public $bookFilter = 'all';

    public $sortBy = 'latest';

    public $perPage = 12;

    public $selectedReview = null;

    public $showReplyModal = false;

    public $replyContent = '';

    public function mount(?Author $author)
    {
        if (! $author) {
            $this->author = auth()->user->author;
        } else {
            $this->author = $author;
        }

    }

    public function render(): View
    {
        $reviews = $this->getReviews();
        $reviewStats = $this->getReviewStats();

        return view('livewire.authors.reviews', [
            'reviews' => $reviews,
            'reviewStats' => $reviewStats,
            'books' => $this->author->books()->get(),
            'hasReviews' => $reviews->total() > 0,
        ]);
    }

    private function getReviews()
    {

        $query = BookReview::whereHas('book', function ($bookQuery) {
            $bookQuery->where('author_id', auth()->user()->author->id);
        })
            ->with(['book', 'user']); // Only show approved reviews
        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('review', 'like', '%'.$this->search.'%')
                    ->orWhere('title', 'like', '%'.$this->search.'%')
                    ->orWhere('reviewer_name', 'like', '%'.$this->search.'%')
                    ->orWhereHas('user', function ($userQuery) {
                        $userQuery->where('name', 'like', '%'.$this->search.'%');
                    });
            });
        }

        // Apply rating filter
        if ($this->ratingFilter !== 'all') {
            $query->where('rating', $this->ratingFilter);
        }

        // Apply book filter
        if ($this->bookFilter !== 'all') {
            $query->where('book_id', $this->bookFilter);
        }

        // Apply sorting
        switch ($this->sortBy) {
            case 'rating_high':
                $query->orderBy('rating', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'rating_low':
                $query->orderBy('rating', 'asc')->orderBy('created_at', 'desc');
                break;
            case 'helpful':
                $query->orderBy('helpful_count', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query->paginate($this->perPage);
    }

    public function markAsHelpful(BookReview $review)
    {
        $review->toggleHelpfulVote(auth()->user()->id);
    }

    private function getReviewStats()
    {
        $bookIds = auth()->user()->author->books()->pluck('id');

        $allReviews = BookReview::whereIn('book_id', $bookIds)
//            ->approved()
            ->get();

        $totalReviews = $allReviews->count();
        $averageRating = $totalReviews > 0 ? $allReviews->avg('rating') : 0;

        $ratingBreakdown = [
            5 => $allReviews->where('rating', 5)->count(),
            4 => $allReviews->where('rating', 4)->count(),
            3 => $allReviews->where('rating', 3)->count(),
            2 => $allReviews->where('rating', 2)->count(),
            1 => $allReviews->where('rating', 1)->count(),
        ];

        $recentReviews = $allReviews->where('created_at', '>', now()->subDays(30))->count();
        $verifiedReviews = $allReviews->where('is_verified_purchase', true)->count();

        return [
            'total_reviews' => $totalReviews,
            'average_rating' => round($averageRating, 1),
            'rating_breakdown' => $ratingBreakdown,
            'recent_reviews' => $recentReviews,
            'positive_reviews' => $allReviews->where('rating', '>=', 4)->count(),
            'verified_reviews' => $verifiedReviews,
            'verification_rate' => $totalReviews > 0 ? round(($verifiedReviews / $totalReviews) * 100, 1) : 0,
            'most_helpful_review' => $allReviews->sortByDesc('helpful_count')->first(),
            'response_rate' => $this->calculateResponseRate($allReviews),
        ];
    }

    private function calculateResponseRate($reviews)
    {
        $totalReviews = $reviews->count();
        if ($totalReviews === 0) {
            return 0;
        }

        // Count reviews that have author replies (you'll need to add this field if needed)
        $repliedReviews = $reviews->whereNotNull('author_reply')->count();

        return round(($repliedReviews / $totalReviews) * 100, 1);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedRatingFilter()
    {
        $this->resetPage();
    }

    public function updatedBookFilter()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->ratingFilter = 'all';
        $this->bookFilter = 'all';
        $this->sortBy = 'latest';
        $this->resetPage();
    }

    public function openReplyModal($reviewId)
    {
        $this->selectedReview = $reviewId;
        $this->showReplyModal = true;
        $this->replyContent = '';
    }

    public function closeReplyModal()
    {
        $this->showReplyModal = false;
        $this->selectedReview = null;
        $this->replyContent = '';
    }

    public function submitReply()
    {
        $this->validate([
            'replyContent' => 'required|min:10|max:1000',
        ]);

        $review = BookReview::find($this->selectedReview);
        if ($review) {
            // You might want to add an author_reply field to the book_reviews table
            $review->update([
                'author_reply' => $this->replyContent,
                'author_replied_at' => now(),
            ]);
        }

        $this->closeReplyModal();
        $this->dispatch('review-reply-sent');
        session()->flash('message', 'Reply sent successfully!');
    }

    public function toggleHelpful($reviewId)
    {
        if (! auth()->check()) {
            $this->dispatch('show-login-modal');

            return;
        }

        $review = BookReview::find($reviewId);
        if ($review) {
            $wasHelpful = $review->toggleHelpfulVote(auth()->id());

            $this->dispatch('review-helpful-toggled', [
                'reviewId' => $reviewId,
                'wasHelpful' => $wasHelpful,
                'helpfulCount' => $review->helpful_count,
            ]);
        }
    }

    public function reportReview($reviewId)
    {
        if (! auth()->check()) {
            $this->dispatch('show-login-modal');

            return;
        }

        // Here you could implement review reporting functionality
        // For now, just show a success message
        $this->dispatch('review-reported', ['reviewId' => $reviewId]);
        session()->flash('message', 'Review has been reported for moderation.');
    }

    public function getReviewsProperty()
    {
        return $this->getReviews();
    }

    public function getReviewStatsProperty()
    {
        return $this->getReviewStats();
    }
}
