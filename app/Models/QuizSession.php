<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'subject_id',
        'chapter_id',
        'page_start',
        'page_end',
        'question_type',
        'question_count',
        'difficulty',
        'questions',
        'answers',
        'results',
        'context',
        'time_taken',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'questions' => 'array',
        'answers' => 'array',
        'results' => 'array',
        'context' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the quiz session
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the book associated with the quiz session (optional)
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get the subject associated with the quiz session
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(AcademicSubject::class, 'subject_id');
    }

    /**
     * Scope a query to only include active quiz sessions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include completed quiz sessions
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Check if quiz is based on a book
     */
    public function isBookBased(): bool
    {
        return $this->book_id !== null;
    }

    /**
     * Check if quiz is based on uploaded content
     */
    public function isContentBased(): bool
    {
        return $this->book_id === null;
    }
}
