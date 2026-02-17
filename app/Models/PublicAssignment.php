<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PublicAssignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'access_code',
        'teacher_id',
        'school_id',
        'type',
        'duration_in_minutes',
        'starts_at',
        'ends_at',
        'is_randomized',
        'instructions',
        'total_marks',
        'result_visibility',
        'results_released',
        'results_released_at',
        'show_correct_answers',
        'show_score_breakdown',
        'proctoring_enabled',
        'restrict_navigation',
        'max_tab_switches',
        'auto_submit_on_violation',
        'require_webcam',
        'require_fullscreen',
        'ai_generated',
        'source_document_path',
        'ai_generation_settings',
        'status',
        'max_attempts',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'results_released_at' => 'datetime',
            'is_randomized' => 'boolean',
            'results_released' => 'boolean',
            'show_correct_answers' => 'boolean',
            'show_score_breakdown' => 'boolean',
            'proctoring_enabled' => 'boolean',
            'restrict_navigation' => 'boolean',
            'auto_submit_on_violation' => 'boolean',
            'require_webcam' => 'boolean',
            'require_fullscreen' => 'boolean',
            'ai_generated' => 'boolean',
            'ai_generation_settings' => 'array',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($assignment) {
            if (empty($assignment->access_code)) {
                $assignment->access_code = self::generateUniqueAccessCode();
            }
        });
    }

    public static function generateUniqueAccessCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('access_code', $code)->exists());

        return $code;
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(PublicAssignmentSection::class)->orderBy('order');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(PublicAssignmentQuestion::class)->orderBy('order');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(PublicAssignmentSubmission::class);
    }

    public function isActive(): bool
    {
        $now = now();

        if ($this->status !== 'published') {
            return false;
        }

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }

        return true;
    }

    public function isExpired(): bool
    {
        return $this->ends_at && now()->gt($this->ends_at);
    }

    public function canShowResults(): bool
    {
        return match ($this->result_visibility) {
            'immediate' => true,
            'after_due_date' => $this->isExpired(),
            'manual_release' => $this->results_released,
            default => false,
        };
    }

    public function releaseResults(): void
    {
        $this->update([
            'results_released' => true,
            'results_released_at' => now(),
        ]);
    }

    public function getSubmissionForParticipant($participantType, $participantId): ?PublicAssignmentSubmission
    {
        return $this->submissions()
            ->where('participant_type', $participantType)
            ->where('participant_id', $participantId)
            ->first();
    }

    public function hasParticipantSubmitted($participantType, $participantId): bool
    {
        return $this->submissions()
            ->where('participant_type', $participantType)
            ->where('participant_id', $participantId)
            ->whereNotNull('submitted_at')
            ->exists();
    }

    public function getParticipantAttemptCount($participantType, $participantId): int
    {
        return $this->submissions()
            ->where('participant_type', $participantType)
            ->where('participant_id', $participantId)
            ->count();
    }

    public function canParticipantAttempt($participantType, $participantId): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        $attemptCount = $this->getParticipantAttemptCount($participantType, $participantId);

        return $attemptCount < $this->max_attempts;
    }

    public function recalculateTotalMarks(): void
    {
        $total = $this->questions()->sum('marks');
        $this->update(['total_marks' => $total]);
    }

    public static function findByAccessCode(string $code): ?self
    {
        return self::where('access_code', strtoupper($code))->first();
    }
}
