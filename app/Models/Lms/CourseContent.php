<?php

namespace App\Models\Lms;

use App\Models\Quiz;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseContent extends Model
{
    use HasFactory;

    protected $table = 'lms_contents';

    public const TYPE_VIDEO = 'video';

    public const TYPE_AUDIO = 'audio';

    public const TYPE_TEXT = 'text';

    public const TYPE_QUIZ = 'quiz';

    public const TYPE_FEEDBACK = 'feedback';

    protected $fillable = [
        'section_id',
        'type',
        'title',
        'content',
        'media_path',
        'media_url',
        'duration_seconds',
        'word_count',
        'quiz_id',
        'order',
        'is_required',
        'completion_criteria',
        'ai_summary',
        'is_published',
    ];

    protected $casts = [
        'duration_seconds' => 'integer',
        'word_count' => 'integer',
        'order' => 'integer',
        'is_required' => 'boolean',
        'completion_criteria' => 'array',
        'ai_summary' => 'array',
        'is_published' => 'boolean',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(CourseProgress::class, 'content_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeRequired(Builder $query): Builder
    {
        return $query->where('is_required', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order');
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function isVideo(): bool
    {
        return $this->type === self::TYPE_VIDEO;
    }

    public function isAudio(): bool
    {
        return $this->type === self::TYPE_AUDIO;
    }

    public function isText(): bool
    {
        return $this->type === self::TYPE_TEXT;
    }

    public function isQuiz(): bool
    {
        return $this->type === self::TYPE_QUIZ;
    }

    public function isFeedback(): bool
    {
        return $this->type === self::TYPE_FEEDBACK;
    }

    public function isMediaContent(): bool
    {
        return in_array($this->type, [self::TYPE_VIDEO, self::TYPE_AUDIO]);
    }

    public function getMediaUrl(): ?string
    {
        if ($this->media_url) {
            return $this->media_url;
        }

        if ($this->media_path) {
            return asset('storage/'.$this->media_path);
        }

        return null;
    }

    public function getCompletionThreshold(): int
    {
        $criteria = $this->completion_criteria ?? [];

        if ($this->isVideo() || $this->isAudio()) {
            return $criteria['watch_percentage'] ?? 90;
        }

        if ($this->isQuiz()) {
            return $criteria['min_score'] ?? 70;
        }

        return 100;
    }

    public function getDurationFormatted(): string
    {
        if (! $this->duration_seconds) {
            return '0:00';
        }

        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;

        return sprintf('%d:%02d', $minutes, $seconds);
    }

    public function getEstimatedReadingTime(): int
    {
        if (! $this->word_count) {
            return 0;
        }

        // Average reading speed: 200 words per minute
        return (int) ceil($this->word_count / 200);
    }

    public static function getTypes(): array
    {
        return [
            self::TYPE_VIDEO => 'Video',
            self::TYPE_AUDIO => 'Audio',
            self::TYPE_TEXT => 'Text',
            self::TYPE_QUIZ => 'Quiz',
            self::TYPE_FEEDBACK => 'Feedback',
        ];
    }
}
