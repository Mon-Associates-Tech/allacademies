<?php

namespace App\Models\Lms;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CourseEnrollment extends Model
{
    use HasFactory;

    protected $table = 'lms_enrollments';

    public const STATUS_ENROLLED = 'enrolled';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_DROPPED = 'dropped';

    protected $fillable = [
        'course_id',
        'user_id',
        'status',
        'progress_percentage',
        'enrolled_at',
        'started_at',
        'completed_at',
        'final_grade',
        'metadata',
    ];

    protected $casts = [
        'progress_percentage' => 'decimal:2',
        'final_grade' => 'decimal:2',
        'enrolled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(CourseProgress::class, 'enrollment_id');
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(IssuedCertificate::class, 'enrollment_id');
    }

    public function scopeEnrolled(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ENROLLED);
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeDropped(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DROPPED);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_ENROLLED, self::STATUS_IN_PROGRESS]);
    }

    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function isEnrolled(): bool
    {
        return $this->status === self::STATUS_ENROLLED;
    }

    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isDropped(): bool
    {
        return $this->status === self::STATUS_DROPPED;
    }

    public function isActive(): bool
    {
        return in_array($this->status, [self::STATUS_ENROLLED, self::STATUS_IN_PROGRESS]);
    }

    public function start(): bool
    {
        if ($this->status !== self::STATUS_ENROLLED) {
            return false;
        }

        return $this->update([
            'status' => self::STATUS_IN_PROGRESS,
            'started_at' => now(),
        ]);
    }

    public function complete(?float $finalGrade = null): bool
    {
        return $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
            'progress_percentage' => 100,
            'final_grade' => $finalGrade,
        ]);
    }

    public function drop(): bool
    {
        return $this->update([
            'status' => self::STATUS_DROPPED,
        ]);
    }

    public function updateProgress(float $percentage): bool
    {
        return $this->update([
            'progress_percentage' => min(100, max(0, $percentage)),
        ]);
    }

    public function getCompletedContentsCount(): int
    {
        return $this->progress()->where('is_completed', true)->count();
    }

    public function getContentProgress(CourseContent $content): ?CourseProgress
    {
        return $this->progress()->where('content_id', $content->id)->first();
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_ENROLLED => 'Enrolled',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_DROPPED => 'Dropped',
        ];
    }
}
