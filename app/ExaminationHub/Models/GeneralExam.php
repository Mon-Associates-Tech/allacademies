<?php

namespace App\ExaminationHub\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class GeneralExam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'access_code',
        'send_reminders',
        'reminder_datetime',
        'reminder_sent',
        'reminder_sent_at',
        'user_id',
        'general_exam_subscription_id',
        'academic_subject_id',
        'teacher_id',
        'school_id',
        'type',
        'delivery_type',
        'participant_mode',
        'participant_required_fields',
        'configured_match_mode',
        'duration_in_minutes',
        'starts_at',
        'ends_at',
        'is_randomized',
        'instructions',
        'total_marks',
        'result_visibility',
        'results_release_datetime',
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
        'hardened_mode',
        'max_attempts',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'results_release_datetime' => 'datetime',
            'results_released_at' => 'datetime',
            'reminder_datetime' => 'datetime',
            'reminder_sent_at' => 'datetime',
            'is_randomized' => 'boolean',
            'results_released' => 'boolean',
            'show_correct_answers' => 'boolean',
            'show_score_breakdown' => 'boolean',
            'send_reminders' => 'boolean',
            'reminder_sent' => 'boolean',
            'proctoring_enabled' => 'boolean',
            'restrict_navigation' => 'boolean',
            'auto_submit_on_violation' => 'boolean',
            'require_webcam' => 'boolean',
            'require_fullscreen' => 'boolean',
            'ai_generated' => 'boolean',
            'hardened_mode' => 'boolean',
            'ai_generation_settings' => 'array',
            'participant_required_fields' => 'array',
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
            $code = strtoupper(Str::random(6));
        } while (self::where('access_code', $code)->exists());

        return $code;
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(GeneralExamSubscription::class, 'general_exam_subscription_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function academicSubject(): BelongsTo
    {
        return $this->belongsTo(AcademicSubject::class, 'academic_subject_id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(GeneralExamSection::class)->orderBy('order');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(GeneralExamQuestion::class)->orderBy('order');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(GeneralExamSubmission::class);
    }

    public function configuredParticipants(): HasMany
    {
        return $this->hasMany(GeneralExamConfiguredParticipant::class);
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
            'scheduled' => $this->results_release_datetime && now()->gte($this->results_release_datetime),
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

    public function getSubmissionForParticipant($participantType, $participantId): ?GeneralExamSubmission
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
