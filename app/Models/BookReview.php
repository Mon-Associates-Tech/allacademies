<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'user_id',
        'reviewer_name',
        'reviewer_email',
        'rating',
        'title',
        'review',
        'author_reply',
        'author_replied_at',
        'is_verified_purchase',
        'is_approved',
        'helpful_votes',
        'helpful_count',
        'approved_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_verified_purchase' => 'boolean',
        'is_approved' => 'boolean',
        'helpful_votes' => 'array',
        'helpful_count' => 'integer',
        'approved_at' => 'datetime',
        'author_replied_at' => 'datetime',
    ];

    /**
     * Get the book that this review belongs to
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get the user who wrote this review
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get only approved reviews
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope to get only verified purchase reviews
     */
    public function scopeVerifiedPurchase(Builder $query): Builder
    {
        return $query->where('is_verified_purchase', true);
    }

    /**
     * Scope to filter by rating
     */
    public function scopeByRating(Builder $query, int $rating): Builder
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope to order by most helpful
     */
    public function scopeMostHelpful(Builder $query): Builder
    {
        return $query->orderBy('helpful_count', 'desc');
    }

    /**
     * Scope to order by newest first
     */
    public function scopeNewest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope to order by oldest first
     */
    public function scopeOldest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Scope for reviews with author replies
     */
    public function scopeWithAuthorReply(Builder $query): Builder
    {
        return $query->whereNotNull('author_reply');
    }

    /**
     * Check if a user found this review helpful
     */
    public function isHelpfulToUser($userId): bool
    {
        return in_array($userId, $this->helpful_votes ?? []);
    }

    /**
     * Toggle helpful vote for a user
     */
    public function toggleHelpfulVote($userId): bool
    {
        $helpfulVotes = $this->helpful_votes ?? [];

        if (in_array($userId, $helpfulVotes)) {
            // Remove vote
            $helpfulVotes = array_values(array_diff($helpfulVotes, [$userId]));
            $wasHelpful = false;
        } else {
            // Add vote
            $helpfulVotes[] = $userId;
            $wasHelpful = true;
        }

        $this->update([
            'helpful_votes' => $helpfulVotes,
            'helpful_count' => count($helpfulVotes),
        ]);

        return $wasHelpful;
    }

    /**
     * Check if this review has an author reply
     */
    public function hasAuthorReply(): bool
    {
        return ! is_null($this->author_reply);
    }

    /**
     * Get the formatted time since review was created
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at?->diffForHumans();
    }

    /**
     * Get the formatted time since author replied
     */
    public function getAuthorReplyTimeAgoAttribute(): ?string
    {
        return $this->author_replied_at?->diffForHumans();
    }

    /**
     * Get star rating as array for display
     */
    public function getStarsAttribute(): array
    {
        $stars = [];
        for ($i = 1; $i <= 5; $i++) {
            $stars[] = [
                'filled' => $i <= $this->rating,
                'number' => $i,
            ];
        }

        return $stars;
    }

    /**
     * Get reviewer's initials
     */
    public function getReviewerInitialsAttribute(): string
    {
        $words = explode(' ', $this->reviewer_name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1).substr($words[1], 0, 1));
        }

        return strtoupper(substr($this->reviewer_name, 0, 2));
    }

    /**
     * Get rating color class for UI
     */
    public function getRatingColorAttribute(): string
    {
        return match ($this->rating) {
            5 => 'text-green-500',
            4 => 'text-green-400',
            3 => 'text-yellow-500',
            2 => 'text-orange-500',
            1 => 'text-red-500',
            default => 'text-gray-400'
        };
    }

    /**
     * Get rating background color class for UI
     */
    public function getRatingBgColorAttribute(): string
    {
        return match ($this->rating) {
            5 => 'bg-green-100 text-green-800',
            4 => 'bg-green-50 text-green-700',
            3 => 'bg-yellow-100 text-yellow-800',
            2 => 'bg-orange-100 text-orange-800',
            1 => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // When a review is created, update book's average rating
        static::created(function ($review) {
            if ($review->is_approved) {
                $review->book->updateAverageRating();
            }
        });

        // When a review is updated, update book's average rating
        static::updated(function ($review) {
            if ($review->wasChanged('rating') || $review->wasChanged('is_approved')) {
                $review->book->updateAverageRating();
            }
        });

        // When a review is deleted, update book's average rating
        static::deleted(function ($review) {
            $review->book->updateAverageRating();
        });
    }
}
