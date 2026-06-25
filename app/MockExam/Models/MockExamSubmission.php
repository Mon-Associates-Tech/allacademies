<?php

namespace App\MockExam\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MockExamSubmission extends Model
{
    public const STATUS_NOT_STARTED       = 'not_started';
    public const STATUS_IN_PROGRESS      = 'in_progress';
    public const STATUS_SUBMITTED        = 'submitted';
    public const STATUS_AUTO_GRADED      = 'auto_graded';
    public const STATUS_MANUALLY_REVIEWED = 'manually_reviewed';
    public const STATUS_FINAL            = 'final';

    protected $fillable = [
        'mock_exam_id',
        'participant_type',
        'participant_id',
        'participant_name',
        'participant_email',
        'started_at',
        'submitted_at',
        'time_spent_seconds',
        'responses',
        'randomized_question_order',
        'section_timings',
        'last_activity_at',
        'current_section_index',
        'score',
        'total_marks',
        'percentage',
        'grade',
        'status',
        'requires_manual_review',
        'graded_at',
        'graded_by',
        'teacher_feedback',
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
            'last_activity_at'         => 'datetime',
            'responses'                => 'array',
            'randomized_question_order'=> 'array',
            'section_timings'          => 'array',
            'requires_manual_review'   => 'boolean',
            'score'                    => 'float',
            'total_marks'              => 'float',
            'percentage'               => 'float',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function mockExam(): BelongsTo
    {
        return $this->belongsTo(MockExam::class);
    }

    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    // ─── Status helpers ───────────────────────────────────────────────────────

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
        ], true);
    }

    public function isGraded(): bool
    {
        return in_array($this->status, [
            self::STATUS_AUTO_GRADED,
            self::STATUS_MANUALLY_REVIEWED,
            self::STATUS_FINAL,
        ], true);
    }

    // ─── Lifecycle ────────────────────────────────────────────────────────────

    public function start(): void
    {
        $this->update([
            'started_at' => now(),
            'status'     => self::STATUS_IN_PROGRESS,
        ]);
    }

    public function submit(bool $auto = false, ?string $reason = null): void
    {
        $timeSpent = $this->started_at ? abs(now()->diffInSeconds($this->started_at)) : 0;

        $this->update([
            'submitted_at'       => now(),
            'status'             => self::STATUS_SUBMITTED,
            'time_spent_seconds' => $timeSpent,
        ]);
    }

    // ─── Response management ──────────────────────────────────────────────────

    public function saveResponse(int $questionId, mixed $response): void
    {
        $responses = $this->responses ?? [];
        $responses[$questionId] = [
            'response'    => $response,
            'answered_at' => now()->toIso8601String(),
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

    // ─── Grading ─────────────────────────────────────────────────────────────

    /**
     * Auto-grade all questions that support it; flag the rest for manual review.
     */
    public function autoGrade(string $grade): void
    {
        $exam      = $this->mockExam;
        $questions = MockExamQuestion::whereIn(
            'mock_exam_section_id',
            MockExamSection::whereIn(
                'mock_exam_subject_exam_id',
                MockExamSubjectExam::where('mock_exam_id', $exam->id)->pluck('id')
            )->pluck('id')
        )->get();

        $totalScore    = 0.0;
        $totalMarks    = 0.0;
        $needsManual   = false;
        $gradedResponses = $this->responses ?? [];

        foreach ($questions as $question) {
            $totalMarks += $question->marks;
            $response    = $this->getResponse($question->id);

            if ($response === null) {
                $gradedResponses[$question->id] = array_merge(
                    $gradedResponses[$question->id] ?? [],
                    ['is_correct' => false, 'points_earned' => 0.0, 'feedback' => 'No answer provided']
                );
                continue;
            }

            if ($question->canAutoGrade()) {
                $result = $question->gradeResponse((string) $response);
                $gradedResponses[$question->id] = array_merge(
                    $gradedResponses[$question->id] ?? [],
                    $result
                );
                $totalScore += $result['points_earned'];
            } else {
                $result = $question->gradeResponse((string) $response);
                $gradedResponses[$question->id] = array_merge(
                    $gradedResponses[$question->id] ?? [],
                    $result
                );
                $totalScore += $result['points_earned'];
                if ($result['requires_review'] ?? false) {
                    $needsManual = true;
                }
            }
        }

        $percentage = $totalMarks > 0 ? ($totalScore / $totalMarks) * 100 : 0.0;

        $this->update([
            'responses'             => $gradedResponses,
            'score'                 => $totalScore,
            'total_marks'           => $totalMarks,
            'percentage'            => round($percentage, 2),
            'grade'                 => $grade,
            'status'                => self::STATUS_AUTO_GRADED,
            'requires_manual_review'=> $needsManual,
            'graded_at'             => now(),
        ]);
    }

    /**
     * Manually grade a single question and recalculate totals.
     */
    public function manualGradeQuestion(int $questionId, float $points, ?string $feedback = null): void
    {
        $responses = $this->responses ?? [];

        $responses[$questionId] = array_merge($responses[$questionId] ?? [], [
            'points_earned'    => $points,
            'manual_feedback'  => $feedback,
            'manually_graded'  => true,
        ]);

        $totalScore = collect($responses)->sum(fn ($r) => (float) ($r['points_earned'] ?? 0));
        $percentage = $this->total_marks > 0 ? ($totalScore / $this->total_marks) * 100 : 0.0;

        $this->update([
            'responses'  => $responses,
            'score'      => $totalScore,
            'percentage' => round($percentage, 2),
        ]);
    }

    public function finalizeGrading(int $graderId, string $grade, ?string $feedback = null): void
    {
        $this->update([
            'status'                 => self::STATUS_FINAL,
            'graded_by'              => $graderId,
            'graded_at'              => now(),
            'grade'                  => $grade,
            'teacher_feedback'       => $feedback,
            'requires_manual_review' => false,
        ]);
    }

    // ─── Display helpers ──────────────────────────────────────────────────────

    public function getRemainingTime(): ?int
    {
        if (! $this->started_at || ! $this->mockExam->duration_in_minutes) {
            return null;
        }

        // Mock exam has no single duration; individual subject exams do.
        return null;
    }

    public function canViewResults(): bool
    {
        return $this->isSubmitted() && $this->mockExam->canShowResults();
    }

    // ─── Monitoring & Activity ────────────────────────────────────────────────

    public function updateActivity(?int $sectionIndex = null): void
    {
        $data = ['last_activity_at' => now()];
        if ($sectionIndex !== null) {
            $data['current_section_index'] = $sectionIndex;
        }
        $this->update($data);
    }

    public function isIdle(): bool
    {
        if (! $this->last_activity_at) {
            return false;
        }
        $threshold = config('mock-exam.monitoring.idle_threshold_minutes', 3);
        return $this->last_activity_at->diffInMinutes(now()) > $threshold;
    }

    // ─── Section Timing ───────────────────────────────────────────────────────

    public function startSection(int $sectionId, int $timeLimitMinutes): void
    {
        $timings = $this->section_timings ?? [];
        if (isset($timings[$sectionId])) {
            return;
        }
        $startedAt = now();
        $timings[$sectionId] = [
            'started_at' => $startedAt->toIso8601String(),
            'expires_at' => $startedAt->addMinutes($timeLimitMinutes)->toIso8601String(),
            'submitted_at' => null,
        ];
        $this->update(['section_timings' => $timings]);
    }

    public function isSectionExpired(int $sectionId): bool
    {
        $timings = $this->section_timings ?? [];
        if (! isset($timings[$sectionId]['expires_at'])) {
            return false;
        }
        return now()->greaterThan($timings[$sectionId]['expires_at']);
    }

    public function getRemainingSecondsForSection(int $sectionId): ?int
    {
        $timings = $this->section_timings ?? [];
        if (! isset($timings[$sectionId]['expires_at'])) {
            return null;
        }
        $expiresAt = \Carbon\Carbon::parse($timings[$sectionId]['expires_at']);
        $remaining = now()->diffInSeconds($expiresAt, false);
        return max(0, (int) $remaining);
    }

    // ─── Analytics & Statistics ───────────────────────────────────────────────

    /**
     * Get detailed analytics for the submission
     */
    public function getAnalytics(): array
    {
        $responses = $this->responses ?? [];
        $totalQuestions = count($responses);
        $answeredQuestions = collect($responses)->filter(fn($r) => ($r['response'] ?? null) !== null)->count();
        $correctAnswers = collect($responses)->where('is_correct', true)->count();
        $incorrectAnswers = collect($responses)->where('is_correct', false)->count();
        $ungradedAnswers = collect($responses)->whereNull('is_correct')->count();

        // Calculate accuracy
        $gradedQuestions = $correctAnswers + $incorrectAnswers;
        $accuracy = $gradedQuestions > 0 ? round(($correctAnswers / $gradedQuestions) * 100, 2) : 0;

        // Time analytics
        $timeSpent = $this->time_spent_seconds ?? 0;
        $avgTimePerQuestion = $totalQuestions > 0 ? round($timeSpent / $totalQuestions, 2) : 0;

        // Points breakdown
        $totalPointsEarned = collect($responses)->sum('points_earned');
        $totalPossiblePoints = $this->total_marks ?? 0;

        // Subject/Section breakdown
        $subjectBreakdown = $this->getSubjectBreakdown();

        return [
            'total_questions' => $totalQuestions,
            'answered_questions' => $answeredQuestions,
            'unanswered_questions' => $totalQuestions - $answeredQuestions,
            'correct_answers' => $correctAnswers,
            'incorrect_answers' => $incorrectAnswers,
            'ungraded_answers' => $ungradedAnswers,
            'accuracy_percentage' => $accuracy,
            'time_spent_seconds' => $timeSpent,
            'time_spent_formatted' => $this->formatTime($timeSpent),
            'avg_time_per_question' => $avgTimePerQuestion,
            'total_points_earned' => round($totalPointsEarned, 2),
            'total_possible_points' => round($totalPossiblePoints, 2),
            'score_percentage' => $this->percentage ?? 0,
            'grade' => $this->grade,
            'subject_breakdown' => $subjectBreakdown,
        ];
    }

    /**
     * Get breakdown by subject exam
     */
    public function getSubjectBreakdown(): array
    {
        $exam = $this->mockExam;
        $responses = $this->responses ?? [];
        $breakdown = [];

        foreach ($exam->subjectExams as $subjectExam) {
            $subjectResponses = [];
            $subjectMarks = 0;
            $subjectEarned = 0;
            $subjectCorrect = 0;
            $subjectTotal = 0;

            foreach ($subjectExam->sections as $section) {
                foreach ($section->questions as $question) {
                    $subjectTotal++;
                    $subjectMarks += $question->marks;

                    if (isset($responses[$question->id])) {
                        $resp = $responses[$question->id];
                        $subjectEarned += $resp['points_earned'] ?? 0;
                        if (($resp['is_correct'] ?? false) === true) {
                            $subjectCorrect++;
                        }
                        $subjectResponses[] = $resp;
                    }
                }
            }

            $breakdown[] = [
                'subject_name' => $subjectExam->getDisplayTitle(),
                'total_questions' => $subjectTotal,
                'answered_questions' => count($subjectResponses),
                'correct_answers' => $subjectCorrect,
                'marks_possible' => round($subjectMarks, 2),
                'marks_earned' => round($subjectEarned, 2),
                'percentage' => $subjectMarks > 0 ? round(($subjectEarned / $subjectMarks) * 100, 2) : 0,
            ];
        }

        return $breakdown;
    }

    /**
     * Format seconds into human-readable time
     */
    private function formatTime(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%dh %dm %ds', $hours, $minutes, $secs);
        } elseif ($minutes > 0) {
            return sprintf('%dm %ds', $minutes, $secs);
        }

        return sprintf('%ds', $secs);
    }
}
