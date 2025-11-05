<?php

namespace App\Models;

use App\Models\Book\BookMedia;
use App\Models\Book\BookTableOfContent;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Log;
use setasign\Fpdi\Fpdi;
use Storage;
use Throwable;

class Book extends Model
{
    use HasFactory;

    public $with = [
        'media', 'author', 'publisher', 'bookCategory', 'copies', 'approvedReviews', 'borrowings', 'subscriptions', 'groupSubscriptions', 'approvals', 'students'
    ];
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
        'has_audio',
        'has_video',
        'single_audio',
        'single_video',
        'chapter_audios',
        'chapter_videos',
    ];
    protected $casts = [
        'has_hardcopy' => 'boolean',
        'has_softcopy' => 'boolean',
        'cover_image' => 'string',
        'annual_subscription_fee' => 'decimal:2',
        'table_of_contents' => 'array',
        'average_rating' => 'decimal:2',
        'total_reviews' => 'integer',
        'has_audio' => 'boolean',
        'has_video' => 'boolean',
        'chapter_audios' => 'array',
        'chapter_videos' => 'array',
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

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(BookReview::class)->approved();
    }

    public function getAuthorNameAttribute()
    {
        return $this->author->name
            ?? $this->author?->user?->name
            ?? 'Unknown';
    }

    /**
     * Scope a query to only include published books.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')->orWhere('status', 'active');
    }

    public function getCoverImageAttribute(): string
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
        // If we have an existing sample URL, return it
        if ($this->attributes['sample_url']) {
            return asset('storage/' . $this->attributes['sample_url']);
        }

        // If no sample exists but we have a full PDF and table of contents, try to generate one
        if ($this->shouldGenerateSample()) {
            $generatedSample = $this->generateSampleFromFullPdf();
            if ($generatedSample) {
                // Update the model with the generated sample
                $this->update(['sample_url' => $generatedSample]);
                return asset('storage/' . $generatedSample);
            }
        }

        // Fallback to default sample
        return asset('sample.pdf');
    }

    /**
     * Determine if we should generate a sample PDF
     */
    private function shouldGenerateSample(): bool
    {
        return $this->attributes['content_url']
            && !$this->attributes['sample_url'];
    }

    /**
     * Generate a sample PDF from the full PDF using the first chapter
     */
    private function generateSampleFromFullPdf(): ?string
    {
        // Check if we have what we need
        if (!$this->shouldGenerateSample()) {
            return null;
        }

        try {
            // Get the path to the full PDF
            $fullPdfPath = Storage::disk('public')->path($this->attributes['content_url']);

            // Check if the file exists
            if (!file_exists($fullPdfPath)) {
                return null;
            }

            // Create a new FPDI instance
            $pdf = new Fpdi();

            // Get the first chapter pages
            $firstChapter = $this->table_of_contents[0] ?? null;
            if (!$firstChapter) {
                return null;
            }

            $startPage = $firstChapter['page_start'] ?? 1;
            $endPage = $firstChapter['page_end'] ?? min(5, $this->pages ?? 5); // Limit to 5 pages if not specified

            // Import pages from the source PDF
            $pageCount = $pdf->setSourceFile($fullPdfPath);

            // Make sure page numbers are within bounds
            $startPage = max(1, min($startPage, $pageCount));
            $endPage = max($startPage, min($endPage, $pageCount));

            // Add pages to the new PDF
            for ($pageNo = $startPage; $pageNo <= $endPage; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);
            }

            // Generate a unique filename for the sample
            $filename = 'sample_' . $this->id . '_' . time() . '.pdf';
            $samplePath = 'book-samples/' . $filename;

            // Save the sample PDF
            $pdfContent = $pdf->Output('', 'S');
            Storage::disk('public')->put($samplePath, $pdfContent);

            return $samplePath;
        } catch (Exception $e) {
            // Log the error but don't break the flow
            Log::error('Error extracting sample PDF for book ID ' . $this->id . ': ' . $e->getMessage());

            // Return null to indicate failure, but don't throw exception
            return asset('sample.pdf');
        } catch (Throwable $e) {
            // Catch any other errors (like parse errors)
            Log::error('Critical error extracting sample PDF for book ID ' . $this->id . ': ' . $e->getMessage());
            return null;
        }
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

    /**
     * Get the primary category for backward compatibility
     * Returns the first category if multiple exist, or the old book_category_id fallback
     */
    public function getPrimaryCategoryAttribute()
    {
        // First try the new categories relationship
        if ($this->relationLoaded('categories') && $this->categories->isNotEmpty()) {
            return $this->categories->first();
        }

        // Fallback to the old bookCategory relationship
        if ($this->relationLoaded('bookCategory') && $this->bookCategory) {
            return $this->bookCategory;
        }

        // Load and return the first available category
        return $this->categories()->first() ?? $this->bookCategory()->first();
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(BookCategory::class, 'book_category', 'book_id', 'category_id');
    }

    public function bookCategory(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class, 'book_category_id');
    }

    /**
     * Get categories with limit for display purposes
     */
    public function getCategoriesDisplay($limit = null)
    {
        if ($limit) {
            return $this->categories()->limit($limit)->get();
        }
        return $this->categories;
    }

    /**
     * Get category names as a comma-separated string
     */
    public function getCategoryNamesAttribute(): string
    {
        return $this->categories->pluck('name')->implode(', ');
    }


    public function getFormattedSubscriptionFeeAttribute(): string
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

    public function subject(): BelongsTo
    {
        return $this->belongsTo(AcademicSubject::class, 'subject_id');
    }

    /**
     * Get the table of contents with default structure if empty
     */
    public function getTableOfContentsAttributeDeprecated()
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
    public function getFormattedTableOfContentsAttributeDeprecated(): array
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

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->average('rating');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(BookReview::class);
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
            $count = $this->reviews()->where('rating', $i)->count();
            $percentage = $this->reviews()->count() > 0 ? ($count / $this->reviews()->count()) * 100 : 0;

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

    public function getSimilarBooks(int $limit = 6)
    {
        return $this->where('book_category_id', $this->attributes['book_category_id'])
            ->where('id', '!=', $this->attributes['id'])
            ->with(['author', 'bookCategory'])
            ->limit($limit)
            ->get();
    }

    public function getAuthorBooks(int $limit = 3)
    {
        return $this->where('author_id', $this->attributes['author_id'])
            ->where('id', '!=', $this->attributes['id'])
            ->with(['author', 'bookCategory'])
            ->limit($limit)
            ->get();
    }

    // public function getTableOfContentsAttribute()
    // {
    //     // First try to get from relationship
    //     if ($this->relationLoaded('tableOfContents') && $this->tableOfContents) {
    //         return $this->tableOfContents->content;
    //     }

    //     // If no relation exists, generate default
    //     return $this->generateDefaultTableOfContents();
    // }


    public function getTableOfContentsAttribute($value)
{
    // Use DB value if it exists
    if (!empty($value)) {
        return is_string($value) ? json_decode($value, true) : $value;
    }

    // Then fallback to relationship
    if ($this->relationLoaded('tableOfContents') && $this->tableOfContents) {
        return $this->tableOfContents->content;
    }

    // Otherwise, generate a default
    return $this->generateDefaultTableOfContents();
}


    public function getFormattedTableOfContentsAttribute(): array
    {
        // First try to get from relationship
        if ($this->relationLoaded('tableOfContents') && $this->tableOfContents) {
            $toc = $this->tableOfContents->content;
        } else {
            $toc = $this->generateDefaultTableOfContents();
        }

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


    public function getSingleAudioAttribute(): ?string
    {
        return $this->media?->getSingleAudioAttribute();
    }

    public function getSingleVideoAttribute(): ?string
    {
        return $this->media?->getSingleVideoAttribute();
    }

    // public function getChapterAudiosAttribute(): array
    // {
    //     return $this->media?->getChapterAudiosAttribute() ?? [];
    // }

    public function getChapterAudiosAttribute(): array
{
    $media = $this->media()->first(); // Always fetch the media row
    return $media?->chapter_audios ?? [];
}

    public function getChapterVideosAttribute(): array
    {
        return $this->media?->getChapterVideosAttribute() ?? [];
    }

    public function tableOfContents(): HasOne|Book
    {
        return $this->hasOne(BookTableOfContent::class);
    }

    public function media(): HasOne|Book
    {
        return $this->hasOne(BookMedia::class);
    }

    public function quizSessions()
    {
        return $this->hasMany(QuizSession::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'book_subscription_id');
    }
}
