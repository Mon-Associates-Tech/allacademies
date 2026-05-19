<?php

namespace App\MockExam\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MockExam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'instructions',
        'access_code',
        'status',
        'delivery_type',
        'participant_mode',
        'configured_match_mode',
        'participant_required_fields',
        'email_verification_required',
        'result_visibility',
        'results_release_datetime',
        'results_released',
        'results_released_at',
        'starts_at',
        'ends_at',
        'is_randomized',
        'auto_advance_sections',
        'fullscreen_required',
        'copy_paste_disabled',
        'tab_switch_limit',
        'auto_submit_on_violation',
        'max_attempts',
    ];

    protected function casts(): array
    {
        return [
            'starts_at'                  => 'datetime',
            'ends_at'                    => 'datetime',
            'results_release_datetime'   => 'datetime',
            'results_released_at'        => 'datetime',
            'is_randomized'              => 'boolean',
            'auto_advance_sections'      => 'boolean',
            'fullscreen_required'        => 'boolean',
            'copy_paste_disabled'        => 'boolean',
            'auto_submit_on_violation'   => 'boolean',
            'results_released'           => 'boolean',
            'email_verification_required'=> 'boolean',
            'participant_required_fields'=> 'array',
        ];
    }

    // ─── Boot ────────────────────────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $exam) {
            if (empty($exam->access_code)) {
                $exam->access_code = self::generateUniqueAccessCode();
            }
        });
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public static function generateUniqueAccessCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (self::where('access_code', $code)->exists());

        return $code;
    }

    public static function findByAccessCode(string $code): ?self
    {
        return self::where('access_code', strtoupper(trim($code)))->first();
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subjectExams(): HasMany
    {
        return $this->hasMany(MockExamSubjectExam::class)->orderBy('order');
    }

    public function configuredParticipants(): HasMany
    {
        return $this->hasMany(MockExamConfiguredParticipant::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(MockExamSubmission::class);
    }

    // ─── Status helpers ───────────────────────────────────────────────────────

    public function isActive(): bool
    {
        if ($this->status !== 'published') {
            return false;
        }

        $now = now();

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

    public function isPrint(): bool
    {
        return $this->delivery_type === 'print';
    }

    public function isOnline(): bool
    {
        return $this->delivery_type === 'online';
    }

    public function canShowResults(): bool
    {
        return match ($this->result_visibility) {
            'immediate'      => true,
            'after_due_date' => $this->isExpired(),
            'manual_release' => (bool) $this->results_released,
            'scheduled'      => $this->results_release_datetime && now()->gte($this->results_release_datetime),
            default          => false,
        };
    }

    public function releaseResults(): void
    {
        $this->update([
            'results_released'    => true,
            'results_released_at' => now(),
        ]);
    }

    public function canParticipantAttempt(string $type, int $participantId): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        $attempts = $this->submissions()
            ->where('participant_type', $type)
            ->where('participant_id', $participantId)
            ->whereNotNull('submitted_at')
            ->count();

        return $attempts < $this->max_attempts;
    }

    /** Aggregate total marks across all subject exams / sections / questions. */
    public function getTotalMarks(): float
    {
        return (float) $this->subjectExams()
            ->with('sections.questions')
            ->get()
            ->sum(fn ($se) => $se->sections->sum(fn ($s) => $s->questions->sum('marks')));
    }

    public function getTotalQuestions(): int
    {
        return $this->subjectExams()
            ->with('sections.questions')
            ->get()
            ->sum(fn ($se) => $se->sections->sum(fn ($s) => $s->questions->count()));
    }

    // ─── Proctoring ─────────────────────────────────────────────────────────────

    public function hasProctoringEnabled(): bool
    {
        return $this->fullscreen_required
            || $this->copy_paste_disabled
            || $this->tab_switch_limit > 0;
    }

    public function getViolationLimit(): int
    {
        return (int) $this->tab_switch_limit;
    }
}
