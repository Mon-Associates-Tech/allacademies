<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class GeneralExamSubmission extends Model
{
    use HasFactory;

    public const STATUS_NOT_STARTED = 'not_started';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_AUTO_GRADED = 'auto_graded';

    public const STATUS_MANUALLY_REVIEWED = 'manually_reviewed';

    public const STATUS_FINAL = 'final';

    protected $fillable = [
        'general_exam_id',
        'participant_type',
        'participant_id',
        'participant_name',
        'participant_email',
        'started_at',
        'submitted_at',
        'time_spent_seconds',
        'time_taken_minutes',
        'responses',
        'score',
        'total_marks',
        'percentage',
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
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'graded_at' => 'datetime',
            'responses' => 'array',
            'violations' => 'array',
            'requires_manual_review' => 'boolean',
            'auto_submitted' => 'boolean',
            'score' => 'decimal:2',
            'total_marks' => 'decimal:2',
            'percentage' => 'decimal:2',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(GeneralExam::class, 'general_exam_id');
    }

    public function participant(): MorphTo
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

    public function scoreAuditLogs()
    {
        return $this->hasMany(GeneralExamScoreAuditLog::class);
    }

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

    public function start(): void
    {
        $this->update([
            'started_at' => now(),
            'status' => self::STATUS_IN_PROGRESS,
        ]);
    }

    public function submit(bool $autoSubmitted = false, ?string $reason = null): void
    {
        // Calculate time spent if possible
        $timeSpent = $this->time_spent_seconds;
        if ($this->started_at) {
            $timeSpent = now()->diffInSeconds($this->started_at);
        }

        $this->update([
            'submitted_at' => now(),
            'status' => self::STATUS_SUBMITTED,
            'auto_submitted' => $autoSubmitted,
            'auto_submit_reason' => $reason,
            'time_spent_seconds' => $timeSpent,
        ]);
    }

    public function saveResponse(int $questionId, mixed $response): void
    {
        $responses = $this->responses ?? [];
        $responses[$questionId] = [
            'response' => $response,
            'answered_at' => now()->toISOString(),
        ];
        $this->update(['responses' => $responses]);
    }

    public function getResponse(int $questionId): mixed
    {
        return $this->responses[$questionId]['response'] ?? null;
    }

    public function hasAnswered(int $questionId): bool
    {
        return isset($this->responses[$questionId]);
    }

    public function getAnsweredCount(): int
    {
        return count($this->responses ?? []);
    }

    public function gradeSubmission(): void
    {
        $assignment = $this->assignment;
        $questions = $assignment->questions;

        $totalScore = 0;
        $totalMarks = 0;
        $requiresManualReview = false;
        $gradedResponses = [];

        foreach ($questions as $question) {
            $totalMarks += $question->marks;
            $response = $this->getResponse($question->id);

            if ($response !== null) {
                $gradeResult = $question->gradeResponse((string) $response);
                $totalScore += $gradeResult['points_earned'];

                if ($gradeResult['requires_review'] ?? false) {
                    $requiresManualReview = true;
                }

                $gradedResponses[$question->id] = array_merge(
                    $this->responses[$question->id] ?? [],
                    $gradeResult
                );
            } else {
                $gradedResponses[$question->id] = [
                    'response' => null,
                    'is_correct' => false,
                    'points_earned' => 0,
                    'feedback' => 'No answer provided',
                ];
            }
        }

        $percentage = $totalMarks > 0 ? ($totalScore / $totalMarks) * 100 : 0;

        $this->update([
            'responses' => $gradedResponses,
            'score' => $totalScore,
            'total_marks' => $totalMarks,
            'percentage' => round($percentage, 2),
            'grade' => $this->calculateGrade($percentage),
            'status' => self::STATUS_AUTO_GRADED,
            'requires_manual_review' => $requiresManualReview,
            'graded_at' => now(),
        ]);
    }

    public function manualGrade(int $questionId, float $points, ?string $feedback = null): void
    {
        $responses = $this->responses ?? [];

        if (isset($responses[$questionId])) {
            $responses[$questionId]['points_earned'] = $points;
            $responses[$questionId]['manual_feedback'] = $feedback;
            $responses[$questionId]['manually_graded'] = true;
        }

        // Recalculate total score
        $totalScore = 0;
        foreach ($responses as $response) {
            $totalScore += $response['points_earned'] ?? 0;
        }

        $percentage = $this->total_marks > 0 ? ($totalScore / $this->total_marks) * 100 : 0;

        $this->update([
            'responses' => $responses,
            'score' => $totalScore,
            'percentage' => round($percentage, 2),
            'grade' => $this->calculateGrade($percentage),
        ]);
    }

    public function finalizeGrading(int $graderId, ?string $feedback = null): void
    {
        $this->update([
            'status' => self::STATUS_FINAL,
            'graded_by' => $graderId,
            'graded_at' => now(),
            'teacher_feedback' => $feedback,
            'requires_manual_review' => false,
        ]);
    }

    protected function calculateGrade(float $percentage): string
    {
        $schoolId = auth()->user()?->school_id;
        $levelId = $this->assignment?->academic_level_id;
        
        if ($schoolId) {
            $gradeScale = GradeScale::getForScore($percentage, $schoolId, $levelId);
            if ($gradeScale) {
                return $gradeScale->letter_grade;
            }
        }
        
        // Fallback to default grading if no custom scale found
        return match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B',
            $percentage >= 60 => 'C',
            $percentage >= 50 => 'D',
            default => 'F',
        };
    }

    public function recordViolation(string $type, ?array $details = null): void
    {
        $violations = $this->violations ?? [];
        $violations[] = [
            'type' => $type,
            'timestamp' => now()->toISOString(),
            'details' => $details,
        ];

        $updateData = ['violations' => $violations];

        if ($type === 'tab_switch') {
            $updateData['tab_switch_count'] = $this->tab_switch_count + 1;
        }

        $this->update($updateData);
    }

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

    public function canViewResults(): bool
    {
        if (! $this->isSubmitted()) {
            return false;
        }

        return $this->assignment->canShowResults();
    }

    public function getRemainingTime(): ?int
    {
        if (! $this->started_at || ! $this->assignment->duration_in_minutes) {
            return null;
        }

        $endTime = $this->started_at->addMinutes($this->assignment->duration_in_minutes);
        $remaining = now()->diffInSeconds($endTime, false);

        return max(0, $remaining);
    }

    public function isTimeExpired(): bool
    {
        $remaining = $this->getRemainingTime();

        return $remaining !== null && $remaining <= 0;
    }
}
