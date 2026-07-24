<?php

namespace App\ExaminationHub\Models;

use App\Models\ProctoringSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class GeneralExamSubmission extends Model
{
    use HasFactory;

    public const STATUS_NOT_STARTED      = 'not_started';
    public const STATUS_IN_PROGRESS      = 'in_progress';
    public const STATUS_SUBMITTED        = 'submitted';
    public const STATUS_AUTO_GRADED      = 'auto_graded';
    public const STATUS_MANUALLY_REVIEWED = 'manually_reviewed';
    public const STATUS_FINAL            = 'final';

    protected $fillable = [
        'general_exam_id',
        'participant_type',
        'participant_id',
        'participant_name',
        'participant_email',
        'started_at',
        'submitted_at',
        'time_spent_seconds',
        // ── Admin time-extension fields ────────────────────────────────────
        'extra_time_minutes',
        'extra_time_granted_by',
        'extra_time_granted_at',
        // ── Single-session enforcement ─────────────────────────────────────
        // Rotated on every authenticate() call. HeartbeatController compares
        // session('exam_device_token') against this; a mismatch means a second
        // device has taken over and the old one must be kicked.
        'device_token',
        // ──────────────────────────────────────────────────────────────────
        'time_taken_minutes',
        'responses',
        'randomized_question_order',
        'section_start_times',
        'flagged_questions',
        'last_position',
        'score',
        'total_marks',
        'percentage',
        'bonus_points',
        'bonus_reason',
        'bonus_granted_by',
        'bonus_granted_at',
        'grade',
        'status',
        'requires_manual_review',
        'graded_at',
        'graded_by',
        'teacher_feedback',
        'tab_switch_count',
        'violations',
        'auto_submitted',
        'auto_submit_reason',
        'attempt_number',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'started_at'               => 'datetime',
            'submitted_at'             => 'datetime',
            'graded_at'                => 'datetime',
            'extra_time_granted_at'    => 'datetime',
            'responses'                => 'array',
            'randomized_question_order' => 'array',
            'section_start_times'      => 'array',
            'flagged_questions'        => 'array',
            'last_position'            => 'array',
            'violations'               => 'array',
            'requires_manual_review'   => 'boolean',
            'auto_submitted'           => 'boolean',
            'extra_time_minutes'       => 'integer',
            'extra_time_granted_by'    => 'integer',
            'score'                    => 'decimal:2',
            'total_marks'              => 'decimal:2',
            'percentage'               => 'decimal:2',
            'bonus_points'             => 'decimal:2',
            'bonus_granted_at'         => 'datetime',
            'bonus_granted_by'         => 'integer',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(GeneralExam::class, 'general_exam_id');
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(GeneralExam::class, 'general_exam_id');
    }

    public function generalExamParticipant(): MorphTo
    {
        return $this->morphTo();
    }

    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function proctoringSession(): HasOne
    {
        return $this->hasOne(ProctoringSession::class);
    }

    public function scoreAuditLogs(): HasMany
    {
        return $this->hasMany(GeneralExamScoreAuditLog::class);
    }

    /** All readmission grants that originated from this submission. */
    public function readmissionGrants(): HasMany
    {
        return $this->hasMany(ExamReadmissionGrant::class, 'original_submission_id');
    }

    /** The active (unused, unrevoked) readmission grant for this submission, if any. */
    public function activeReadmissionGrant(): ?ExamReadmissionGrant
    {
        return ExamReadmissionGrant::activeForSubmission($this->id);
    }

    // ─── Status Helpers ───────────────────────────────────────────────────────

    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function isSubmitted(): bool
    {
        return in_array($this->status, [
            self::STATUS_SUBMITTED,
            self::STATUS_AUTO_GRADED,
            self::STATUS_MANUALLY_REVIEWED,
            self::STATUS_FINAL,
        ]);
    }

    public function isGraded(): bool
    {
        return in_array($this->status, [
            self::STATUS_AUTO_GRADED,
            self::STATUS_MANUALLY_REVIEWED,
            self::STATUS_FINAL,
        ]);
    }

    // ─── Lifecycle ────────────────────────────────────────────────────────────

    public function start(): void
    {
        $this->update([
            'started_at' => now(),
            'status'     => self::STATUS_IN_PROGRESS,
        ]);
    }

    public function submit(bool $autoSubmitted = false, ?string $reason = null): void
    {
        $timeSpent = $this->time_spent_seconds;
        if ($this->started_at) {
            $timeSpent = abs(now()->diffInSeconds($this->started_at));
        }

        $this->update([
            'submitted_at'       => now(),
            'status'             => self::STATUS_SUBMITTED,
            'auto_submitted'     => $autoSubmitted,
            'auto_submit_reason' => $reason,
            'time_spent_seconds' => $timeSpent,
        ]);
    }

    // ─── Time Extension ───────────────────────────────────────────────────────

    /**
     * Add additional minutes to this submission's allowed time.
     * Safe to call multiple times — each call accumulates onto the total.
     *
     * @param  int       $minutes    Number of minutes to add (must be > 0)
     * @param  int|null  $grantedBy  User ID of the admin granting the extension
     */
    public function extendTime(int $minutes, ?int $grantedBy = null): void
    {
        $this->update([
            'extra_time_minutes'    => $this->extra_time_minutes + $minutes,
            'extra_time_granted_by' => $grantedBy ?? $this->extra_time_granted_by,
            'extra_time_granted_at' => now(),
        ]);
    }

    // ─── Timer Calculations ───────────────────────────────────────────────────

    /**
     * Total allowed seconds = base duration + admin-granted extra time.
     * Returns null when the exam has no time limit.
     */
    public function getTotalAllowedSeconds(): ?int
    {
        $baseDuration = $this->assignment?->duration_in_minutes;
        if ($baseDuration === null) {
            return null;
        }

        return ($baseDuration + ($this->extra_time_minutes ?? 0)) * 60;
    }

    /**
     * Seconds remaining, accounting for both base duration and any extensions.
     * Returns null when the exam has no time limit.
     * Returns 0 when time has expired (never negative).
     */
    public function getRemainingTime(): ?int
    {
        if (! $this->started_at) {
            return null;
        }

        $totalAllowed = $this->getTotalAllowedSeconds();
        if ($totalAllowed === null) {
            return null;
        }

        // Calculate elapsed time from started_at to now
        // (started_at first, then now - returns positive value)
        $elapsed   = $this->started_at->diffInSeconds(now());
        $remaining = $totalAllowed - $elapsed;

        return max(0, $remaining);
    }

    // ─── Re-admission ─────────────────────────────────────────────────────────

    /**
     * Re-open a submitted submission so the candidate can continue.
     * Called by ExamTakingController when consuming a 'continue' grant.
     *
     *  - Clears submitted_at so the exam is no longer marked submitted.
     *  - Resets status to in_progress.
     *  - Preserves all existing responses.
     *  - Does NOT reset the timer (started_at stays as-is); the extension
     *    added via extendTime() gives the candidate additional headroom.
     */
    public function reopenForContinue(): void
    {
        $this->update([
            'submitted_at'       => null,
            'auto_submitted'     => false,
            'auto_submit_reason' => null,
            'status'             => self::STATUS_IN_PROGRESS,
        ]);
    }

    // ─── Response Management ──────────────────────────────────────────────────

    public function saveResponse(int $questionId, mixed $response): void
    {
        $responses = $this->responses ?? [];
        $responses[$questionId] = [
            'response'    => $response,
            'answered_at' => now()->toISOString(),
        ];
        $this->update(['responses' => $responses]);
    }

    public function getResponse(int $questionId): mixed
    {
        return $this->responses[(string) $questionId]['response'] ?? null;
    }

    public function hasAnswered(int $questionId): bool
    {
        return isset($this->responses[$questionId]);
    }

    public function getAnsweredCount(): int
    {
        return count($this->responses ?? []);
    }

    // ─── Grading ─────────────────────────────────────────────────────────────

    public function gradeSubmission(): void
    {
        $assignment = $this->assignment;
        $questions  = $assignment->questions;

        $totalScore          = 0;
        $totalMarks          = 0;
        $requiresManualReview = false;
        $gradedResponses     = [];

        foreach ($questions as $question) {
            $totalMarks += $question->marks;
            $response    = $this->getResponse($question->id);

            if ($response !== null) {
                $gradeResult  = $question->gradeResponse((string) $response);
                $totalScore  += $gradeResult['points_earned'];

                if ($gradeResult['requires_review'] ?? false) {
                    $requiresManualReview = true;
                }

                $gradedResponses[$question->id] = array_merge(
                    $this->responses[$question->id] ?? [],
                    $gradeResult
                );
            } else {
                $gradedResponses[$question->id] = [
                    'response'     => null,
                    'is_correct'   => false,
                    'points_earned' => 0,
                    'feedback'     => 'No answer provided',
                ];
            }
        }

        $percentage = $totalMarks > 0 ? ($totalScore / $totalMarks) * 100 : 0;

        $this->update([
            'responses'             => $gradedResponses,
            'score'                 => $totalScore,
            'total_marks'           => $totalMarks,
            'percentage'            => round($percentage, 2),
            'grade'                 => $this->calculateGrade($percentage),
            'status'                => self::STATUS_AUTO_GRADED,
            'requires_manual_review' => $requiresManualReview,
            'graded_at'             => now(),
        ]);
    }

    public function manualGrade(int $questionId, float $points, ?string $feedback = null): void
    {
        $responses = $this->responses ?? [];

        if (isset($responses[$questionId])) {
            $responses[$questionId]['points_earned']    = $points;
            $responses[$questionId]['manual_feedback']  = $feedback;
            $responses[$questionId]['manually_graded']  = true;
        }

        $totalScore = 0;
        foreach ($responses as $response) {
            $totalScore += $response['points_earned'] ?? 0;
        }

        $percentage = $this->total_marks > 0 ? ($totalScore / $this->total_marks) * 100 : 0;

        $this->update([
            'responses'  => $responses,
            'score'      => $totalScore,
            'percentage' => round($percentage, 2),
            'grade'      => $this->calculateGrade($percentage),
        ]);
    }

    public function finalizeGrading(int $graderId, ?string $feedback = null): void
    {
        $this->update([
            'status'                => self::STATUS_FINAL,
            'graded_by'             => $graderId,
            'graded_at'             => now(),
            'teacher_feedback'      => $feedback,
            'requires_manual_review' => false,
        ]);
    }

    protected function calculateGrade(float $percentage): string
    {
        $schoolId = auth()->user()?->school_id;
        $levelId  = $this->assignment?->academic_level_id;

        if ($schoolId) {
            $gradeScale = ExaminationHubGradeScale::getForScore($percentage, $schoolId, $levelId);
            if ($gradeScale) {
                return $gradeScale->letter_grade;
            }
        }

        return match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B',
            $percentage >= 60 => 'C',
            $percentage >= 50 => 'D',
            default           => 'F',
        };
    }

    // ─── Violations ───────────────────────────────────────────────────────────

    public function recordViolation(string $type, ?array $details = null): void
    {
        $violations = $this->violations ?? [];
        $violations[] = [
            'type'      => $type,
            'timestamp' => now()->toISOString(),
            'details'   => $details,
        ];

        $updateData = ['violations' => $violations];

        if ($type === 'tab_switch') {
            $updateData['tab_switch_count'] = $this->tab_switch_count + 1;
        }

        $this->update($updateData);
    }

    // ─── Participant Info ─────────────────────────────────────────────────────

    public function getParticipantName(): string
    {
        if ($this->participant_type === 'App\\Models\\Student') {
            return $this->participant?->user?->name ?? 'Unknown Student';
        }

        return $this->participant?->name ?? 'Unknown Participant';
    }

    public function getParticipantEmail(): string
    {
        if ($this->participant_type === 'App\\Models\\Student') {
            return $this->participant?->user?->email ?? '';
        }

        return $this->participant?->email ?? '';
    }

    // ─── Flagging ─────────────────────────────────────────────────────────────

    public function flagQuestion(int $questionId): void
    {
        $flags = $this->flagged_questions ?? [];
        $flags[(string) $questionId] = now()->toIso8601String();
        $this->update(['flagged_questions' => $flags]);
    }

    public function unflagQuestion(int $questionId): void
    {
        $flags = $this->flagged_questions ?? [];
        unset($flags[(string) $questionId]);
        $this->update(['flagged_questions' => $flags]);
    }

    public function isFlagged(int $questionId): bool
    {
        return isset(($this->flagged_questions ?? [])[(string) $questionId]);
    }

    public function flaggedCount(): int
    {
        return count($this->flagged_questions ?? []);
    }

    // ─── Results ─────────────────────────────────────────────────────────────

    public function canViewResults(): bool
    {
        return $this->isSubmitted() && $this->assignment->canShowResults();
    }

    public function isTimeExpired(): bool
    {
        $remaining = $this->getRemainingTime();

        return $remaining !== null && $remaining <= 0;
    }
}
