<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseProgress extends Model
{
    use HasFactory;

    protected $table = 'lms_progress';

    protected $fillable = [
        'enrollment_id',
        'content_id',
        'is_completed',
        'progress_value',
        'progress_max',
        'quiz_score',
        'quiz_passed',
        'quiz_attempts',
        'interaction_data',
        'started_at',
        'completed_at',
        'last_accessed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'progress_value' => 'integer',
        'progress_max' => 'integer',
        'quiz_score' => 'decimal:2',
        'quiz_passed' => 'boolean',
        'quiz_attempts' => 'integer',
        'interaction_data' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_accessed_at' => 'datetime',
    ];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(CourseEnrollment::class, 'enrollment_id');
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(CourseContent::class, 'content_id');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('is_completed', true);
    }

    public function scopeIncomplete(Builder $query): Builder
    {
        return $query->where('is_completed', false);
    }

    public function scopeForEnrollment(Builder $query, CourseEnrollment $enrollment): Builder
    {
        return $query->where('enrollment_id', $enrollment->id);
    }

    public function isCompleted(): bool
    {
        return $this->is_completed;
    }

    public function getProgressPercentage(): float
    {
        if ($this->progress_max <= 0) {
            return 0;
        }

        return min(100, ($this->progress_value / $this->progress_max) * 100);
    }

    public function markAsCompleted(): bool
    {
        return $this->update([
            'is_completed' => true,
            'completed_at' => now(),
            'progress_value' => $this->progress_max,
        ]);
    }

    public function updateVideoProgress(int $watchedSeconds, int $totalSeconds): bool
    {
        $this->progress_value = $watchedSeconds;
        $this->progress_max = $totalSeconds;
        $this->last_accessed_at = now();

        if (! $this->started_at) {
            $this->started_at = now();
        }

        // Check if video is considered complete (90% watched)
        // Guard against division by zero when duration is unknown
        if ($totalSeconds > 0) {
            $watchPercentage = ($watchedSeconds / $totalSeconds) * 100;
            if ($watchPercentage >= 90 && ! $this->is_completed) {
                $this->is_completed = true;
                $this->completed_at = now();
            }
        }

        return $this->save();
    }

    public function updateTextProgress(int $paragraphsRead, int $totalParagraphs): bool
    {
        $this->progress_value = $paragraphsRead;
        $this->progress_max = $totalParagraphs;
        $this->last_accessed_at = now();

        if (! $this->started_at) {
            $this->started_at = now();
        }

        // Check if text is considered complete (all paragraphs read)
        if ($paragraphsRead >= $totalParagraphs && ! $this->is_completed) {
            $this->is_completed = true;
            $this->completed_at = now();
        }

        return $this->save();
    }

    public function updateQuizProgress(float $score, bool $passed): bool
    {
        $this->quiz_score = $score;
        $this->quiz_passed = $passed;
        $this->quiz_attempts = ($this->quiz_attempts ?? 0) + 1;
        $this->last_accessed_at = now();

        if (! $this->started_at) {
            $this->started_at = now();
        }

        if ($passed && ! $this->is_completed) {
            $this->is_completed = true;
            $this->completed_at = now();
        }

        return $this->save();
    }

    public function recordInteraction(string $key, mixed $value): bool
    {
        $data = $this->interaction_data ?? [];
        $data[$key] = $value;
        $data['last_interaction_at'] = now()->toIso8601String();

        return $this->update([
            'interaction_data' => $data,
            'last_accessed_at' => now(),
        ]);
    }
}
