<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
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
        'completed_at'
    ];

    protected $casts = [
        'questions' => 'array',
        'answers' => 'array',
        'results' => 'array',
        'context' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    /**
     * Get the user that owns the quiz session
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the book associated with the quiz session
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
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
}
