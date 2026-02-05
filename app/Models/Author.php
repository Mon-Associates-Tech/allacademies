<?php

namespace App\Models;

use App\Traits\ActivityLoggable;
use App\Traits\BelongsToSchoolEnhanced;
use App\Traits\HasMultipleSubAccounts;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use ActivityLoggable;
    use BelongsToSchoolEnhanced;
    use HasFactory;
    use HasMultipleSubAccounts;

    protected static bool $schoolRestricted = false;

    protected $fillable = [
        'user_id',
        'name',
        'biography',
        'website',
        'social_links',
        'writing_experience',
        'education',
        'awards',
        'author_statement',
        'pen_name',
    ];

    protected $with = [
        'user',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function reviews()
    {
        return $this->hasMany(BookReview::class);
    }

    /**
     * Get only published books for this author
     */
    public function publishedBooks()
    {
        return $this->hasMany(Book::class)->published();
    }

    /**
     * Get the count of published books
     */
    public function getPublishedBooksCountAttribute()
    {
        return $this->books()->published()->count();
    }

    /**
     * Get total readers across all published books
     */
    public function getTotalReadersAttribute()
    {
        return $this->books()
            ->published()
            ->withCount('subscriptions')
            ->get()
            ->sum('subscriptions_count');
    }

    /**
     * Get average rating across all published books
     */
    public function getAverageRatingAttribute()
    {
        $books = $this->books()->published()->get();
        if ($books->isEmpty()) {
            return 0;
        }

        $totalRating = $books->sum('average_rating');
        $booksWithRatings = $books->where('average_rating', '>', 0)->count();

        return $booksWithRatings > 0 ? round($totalRating / $booksWithRatings, 1) : 0;
    }
}
