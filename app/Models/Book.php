<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'author_id',
        'book_category_id',
        'edition',
        'publisher',
        'pages',
        'has_hardcopy',
        'has_softcopy',
        'additional_info',
        'table_of_contents',
        'average_rating',
        'total_reviews',
        'cover_image',
        'cover_image_path',
        'content_url',
        'sample_url',
        'pdf_file_path',
        'annual_subscription_fee',
        'subscription_conditions',
        'status',
    ];

    protected $casts = [
        'has_hardcopy' => 'boolean',
        'has_softcopy' => 'boolean',
        'cover_image' => 'string',
        'annual_subscription_fee' => 'decimal:2',
        'table_of_contents' => 'array',
        'average_rating' => 'decimal:2',
        'total_reviews' => 'integer'
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    public function copies(): HasMany
    {
        return $this->hasMany(BookInventory::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(BookReview::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(BookReview::class)->approved();
    }

    /**
     * Scope a query to only include published books.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')->orWhere('status', 'active');
    }

    public function getCoverImageAttribute()
    {
        if ($this->attributes['cover_image']) {
            return asset('storage/' . $this->attributes['cover_image']);
        }
        $sampleCovers = [
            'images/book-cover.png',
            'images/book-cover-1.jpg',
            'images/book-cover-2.jpg',
        ];
        return asset($sampleCovers[array_rand($sampleCovers)]);
    }

    public function getContentUrlAttribute(): string
    {
        if ($this->attributes['content_url']) {
            return asset('storage/' . $this->attributes['content_url']);
        }
        return asset('sample.pdf');
    }

    public function getSampleUrlAttribute(): string
    {
        if ($this->attributes['sample_url']) {
            return asset('storage/' . $this->attributes['sample_url']);
        }
        return asset('sample.pdf');
    }

    public function bookCategory(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class, 'book_category_id');
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(BookBorrowing::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(BookSubscription::class);
    }

    public function groupSubscriptions(): HasMany
    {
        return $this->hasMany(GroupBookSubscription::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(BookApproval::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class)
            ->withTimestamps();
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class)
            ->withTimestamps();
    }

    public function getFormattedSubscriptionFeeAttribute()
    {
        return 'GHS ' . number_format($this->annual_subscription_fee, 2);
    }

    public function getSubscriptionConditionsAttribute()
    {
        return $this->attributes['subscription_conditions'] ??
            "1. Subscription is valid for one year from payment date\n" .
            "2. Book content is for reading only - no downloading, copying or printing allowed\n" .
            "3. Access will be revoked upon subscription expiry\n" .
            "4. Subscription is non-refundable\n" .
            "5. Content is protected by copyright laws";
    }

    public function getIsFreeAttribute(): bool
    {
        return !$this->annual_subscription_fee || $this->annual_subscription_fee == 0;
    }

    public function scopeFree($query)
    {
        return $query->whereNull('annual_subscription_fee')->orWhere('annual_subscription_fee', 0);
    }

    public function scopePaid($query)
    {
        return $query->where('annual_subscription_fee', '>', 0);
    }

    public function subject(){
        return $this->belongsTo(AcademicSubject::class, 'subject_id');
    }

    /**
     * Get the table of contents with default structure if empty
     */
    public function getTableOfContentsAttribute()
    {
        $toc = $this->attributes['table_of_contents'] ? json_decode($this->attributes['table_of_contents'], true) : null;

        if (!$toc) {
            // Return default table of contents structure
            return $this->generateDefaultTableOfContents();
        }

        return $toc;
    }

    /**
     * Generate a default table of contents based on book pages
     */
    private function generateDefaultTableOfContents(): array
    {
        $chaptersCount = max(1, min(15, intval($this->pages / 20))); // Rough estimate
        $chapters = [];

        for ($i = 1; $i <= $chaptersCount; $i++) {
            $chapters[] = [
                'chapter' => $i,
                'title' => "Chapter {$i}",
                'description' => "Content for chapter {$i}",
                'page_start' => (($i - 1) * intval($this->pages / $chaptersCount)) + 1,
                'page_end' => $i * intval($this->pages / $chaptersCount),
                'sections' => []
            ];
        }

        return $chapters;
    }

    /**
     * Get formatted table of contents for display
     */
    public function getFormattedTableOfContentsAttribute(): array
    {
        $toc = $this->table_of_contents;

        return collect($toc)->map(function ($chapter) {
            return [
                'chapter_number' => $chapter['chapter'] ?? 1,
                'title' => $chapter['title'] ?? 'Untitled Chapter',
                'description' => $chapter['description'] ?? '',
                'page_range' => isset($chapter['page_start'], $chapter['page_end'])
                    ? "Pages {$chapter['page_start']}-{$chapter['page_end']}"
                    : '',
                'page_count' => isset($chapter['page_start'], $chapter['page_end'])
                    ? $chapter['page_end'] - $chapter['page_start'] + 1
                    : 0,
                'sections' => $chapter['sections'] ?? []
            ];
        })->toArray();
    }

    /**
     * Update the book's average rating and total reviews count
     */
    public function updateAverageRating(): void
    {
        $reviews = $this->reviews()->approved();
        $totalReviews = $reviews->count();
        $averageRating = $totalReviews > 0 ? $reviews->avg('rating') : 0;

        $this->update([
            'average_rating' => round($averageRating, 2),
            'total_reviews' => $totalReviews
        ]);
    }

    /**
     * Get rating distribution
     */
    public function getRatingDistributionAttribute(): array
    {
        $distribution = [];

        for ($i = 5; $i >= 1; $i--) {
            $count = $this->reviews()->approved()->where('rating', $i)->count();
            $percentage = $this->total_reviews > 0 ? ($count / $this->total_reviews) * 100 : 0;

            $distribution[] = [
                'rating' => $i,
                'count' => $count,
                'percentage' => round($percentage, 1)
            ];
        }

        return $distribution;
    }

    /**
     * Get star rating as array for display
     */
    public function getStarsAttribute(): array
    {
        $stars = [];
        $rating = $this->average_rating;

        for ($i = 1; $i <= 5; $i++) {
            $stars[] = [
                'filled' => $i <= $rating,
                'half_filled' => $i - 0.5 == $rating,
                'number' => $i
            ];
        }
        return $stars;
    }

    /**
     * Check if user can write a review for this book
     */
    public function canUserReview($userId): bool
    {
        // Check if user hasn't already reviewed this book
        $existingReview = $this->reviews()->where('user_id', $userId)->exists();

        return !$existingReview;
    }

    /**
     * Get user's review for this book
     */
    public function getUserReview($userId): ?BookReview
    {
        return $this->reviews()->where('user_id', $userId)->first();
    }

    /**
     * Get recent reviews (last 5)
     */
    public function getRecentReviewsAttribute()
    {
        return $this->reviews()
            ->approved()
            ->with('user')
            ->latest()
            ->limit(5)
            ->get();
    }

    /**
     * Get estimated reading time in hours
     */
    public function getEstimatedReadingTimeAttribute(): string
    {
        $wordsPerPage = 250; // Average words per page
        $wordsPerMinute = 250; // Average reading speed

        $totalWords = $this->pages * $wordsPerPage;
        $readingTimeMinutes = $totalWords / $wordsPerMinute;
        $readingTimeHours = $readingTimeMinutes / 60;

        if ($readingTimeHours < 1) {
            return round($readingTimeMinutes) . ' minutes';
        } else {
            $hours = floor($readingTimeHours);
            $minutes = round(($readingTimeHours - $hours) * 60);

            if ($minutes == 0) {
                return $hours . ' hour' . ($hours > 1 ? 's' : '');
            } else {
                return $hours . 'h ' . $minutes . 'm';
            }
        }
    }

    public function getSimilarBooks(int $limit = 6){
        return $this->where('book_category_id', $this->attributes['book_category_id'])
            ->where('id', '!=', $this->attributes['id'])
            ->with(['author', 'bookCategory'])
            ->limit($limit)
            ->get();
    }

    public function getAuthorBooks(int $limit = 3){
       return $this->where('author_id', $this->attributes['author_id'])
            ->where('id', '!=', $this->attributes['id'])
            ->with(['author', 'bookCategory'])
            ->limit($limit)
            ->get();
    }
}
