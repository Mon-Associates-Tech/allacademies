<?php

namespace App\Livewire\Authors;

use App\Livewire\AppComponent;
use App\Models\Author;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;

class Reviews extends AppComponent
{
    public Author $author;
    public $search = '';
    public $ratingFilter = 'all';
    public $bookFilter = 'all';
    public $sortBy = 'latest';
    public $perPage = 12;
    public $selectedReview = null;
    public $showReplyModal = false;
    public $replyContent = '';

    public function mount(Author $author)
    {
        $this->author = $author;
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
        $reviews = $this->author->books()
            ->with(['reviews' => function ($reviewQuery) {
                $reviewQuery->with(['student.user'])
                    ->orderBy('created_at', $this->sortBy === 'latest' ? 'desc' : 'asc');

                if ($this->search) {
                    $reviewQuery->where(function ($q) {
                        $q->where('review_content', 'like', '%' . $this->search . '%')
                          ->orWhereHas('student.user', function ($userQuery) {
                              $userQuery->where('name', 'like', '%' . $this->search . '%');
                          });
                    });
                }

                if ($this->ratingFilter !== 'all') {
                    $reviewQuery->where('rating', $this->ratingFilter);
                }
            }])
            ->when($this->bookFilter !== 'all', function ($q) {
                $q->where('id', $this->bookFilter);
            })
            ->get()
            ->flatMap(function ($book) {
                return $book->reviews->map(function ($review) use ($book) {
                    $review->book = $book;
                    return $review;
                });
            });

        // Apply sorting
        if ($this->sortBy === 'rating_high') {
            $reviews = $reviews->sortByDesc('rating');
        } elseif ($this->sortBy === 'rating_low') {
            $reviews = $reviews->sortBy('rating');
        } elseif ($this->sortBy === 'oldest') {
            $reviews = $reviews->sortBy('created_at');
        } else {
            $reviews = $reviews->sortByDesc('created_at');
        }

        // Manual pagination
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $this->perPage;
        $paginatedReviews = $reviews->slice($offset, $this->perPage);

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedReviews,
            $reviews->count(),
            $this->perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'page']
        );
    }

    private function getReviewStats()
    {
        $allReviews = $this->author->books()
            ->with('reviews')
            ->get()
            ->flatMap(function ($book) {
                return $book->reviews;
            });

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

        return [
            'total_reviews' => $totalReviews,
            'average_rating' => round($averageRating, 1),
            'rating_breakdown' => $ratingBreakdown,
            'recent_reviews' => $recentReviews,
            'positive_reviews' => $allReviews->where('rating', '>=', 4)->count(),
            'response_rate' => $this->calculateResponseRate($allReviews),
        ];
    }

    private function calculateResponseRate($reviews)
    {
        $totalReviews = $reviews->count();
        if ($totalReviews === 0) return 0;

        // Assuming we have a replied_at field or similar
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

        // Here you would save the reply to the database
        // Review::find($this->selectedReview)->update(['author_reply' => $this->replyContent]);

        $this->closeReplyModal();
        $this->dispatch('review-reply-sent');
    }

    public function markAsHelpful($reviewId)
    {
        // Implementation for marking review as helpful
        $this->dispatch('review-marked-helpful', ['reviewId' => $reviewId]);
    }
}
