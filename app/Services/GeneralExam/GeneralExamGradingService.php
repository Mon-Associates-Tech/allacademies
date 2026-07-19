<?php

namespace App\Services\GeneralExam;

use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamQuestion;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\Models\User;
use App\Services\ResearchAssistantService;
use Illuminate\Support\Facades\Log;

class GeneralExamGradingService
{
    public function __construct(
        protected ResearchAssistantService $chatService
    ) {}

    // ─── Core grading ────────────────────────────────────────────────────────

    /**
     * Grade a submission (auto-grade what's possible, mark others for review).
     *
     * Scoring denominator
     * ───────────────────
     * If the parent exam has a `target_total_marks` set (e.g. 100), that value
     * is used as the denominator so that results are always expressed out of
     * the intended total — even when the exam has fewer physical questions
     * than the target.  When `target_total_marks` is null, the live sum of
     * question marks is used (original behaviour).
     */
    public function gradeSubmission(GeneralExamSubmission $submission): GeneralExamSubmission
    {
        $assignment = $submission->assignment;

        // Eager-load questions once to avoid N+1 during grading loop
        $questions = $assignment->questions;

        $rawScore            = 0;
        $questionSum         = 0; // actual sum of question marks
        $requiresManualReview = false;
        $gradedResponses     = [];

        foreach ($questions as $question) {
            $questionId = $question->id;

            // Excluded questions: removed from grading entirely.
            // If award_marks_on_exclusion is set, full marks are given; otherwise skipped.
            if ($question->excluded_from_grading) {
                $awardMarks = (bool) $question->award_marks_on_exclusion;

                if ($awardMarks) {
                    $questionSum += $question->marks;
                    $rawScore    += $question->marks;
                }

                $gradedResponses[$questionId] = array_merge(
                    $submission->responses[$questionId] ?? [],
                    [
                        'points_earned'         => $awardMarks ? $question->marks : 0,
                        'excluded_from_grading' => true,
                        'award_marks_on_exclusion' => $awardMarks,
                    ]
                );
                continue;
            }

            $questionSum += $question->marks;

            $response = $submission->getResponse($questionId);

            if ($response !== null && $response !== '') {
                $gradeResult  = $this->gradeQuestion($question, (string) $response);
                $rawScore    += $gradeResult['points_earned'];

                if ($gradeResult['requires_review'] ?? false) {
                    $requiresManualReview = true;
                }

                $gradedResponses[$questionId] = array_merge(
                    $submission->responses[$questionId] ?? [],
                    $gradeResult
                );
            } else {
                $gradedResponses[$questionId] = [
                    'response'      => null,
                    'is_correct'    => false,
                    'points_earned' => 0,
                    'feedback'      => 'No answer provided',
                ];
            }
        }

        // ── Denominator resolution ──────────────────────────────────────────
        // Use the exam's configured target if set; fall back to question sum.
        $effectiveTotal = $this->resolveEffectiveTotal($assignment, $questionSum);

        $percentage = $effectiveTotal > 0
            ? ($rawScore / $effectiveTotal) * 100
            : 0;

        // Percentage can exceed 100 only if target < question sum — cap it.
        $percentage = min(100, round($percentage, 2));

        $submission->update([
            'responses'              => $gradedResponses,
            'score'                  => $rawScore,
            'total_marks'            => $effectiveTotal,  // What the submission is marked out of
            'percentage'             => $percentage,
            'grade'                  => $this->calculateGrade($percentage),
            'status'                 => GeneralExamSubmission::STATUS_AUTO_GRADED,
            'requires_manual_review' => $requiresManualReview,
            'graded_at'              => now(),
        ]);

        return $submission->fresh();
    }

    /**
     * Resolve what total marks a submission should be scored out of.
     *
     * Priority:
     *   1. exam.target_total_marks  (admin-configured ceiling, e.g. 100)
     *   2. $questionSum             (live sum of all question marks)
     */
    protected function resolveEffectiveTotal(GeneralExam $exam, float $questionSum): float
    {
        $target = $exam->target_total_marks ?? null;

        if ($target !== null && $target > 0) {
            return (float) $target;
        }

        return $questionSum > 0 ? $questionSum : 1; // guard divide-by-zero
    }

    // ─── Question graders ─────────────────────────────────────────────────────

    protected function gradeQuestion(GeneralExamQuestion $question, string $response): array
    {
        return match ($question->type) {
            GeneralExamQuestion::TYPE_MULTIPLE_CHOICE => $this->gradeMultipleChoice($question, $response),
            GeneralExamQuestion::TYPE_TRUE_FALSE       => $this->gradeTrueFalse($question, $response),
            GeneralExamQuestion::TYPE_SHORT_ANSWER     => $this->gradeShortAnswer($question, $response),
            GeneralExamQuestion::TYPE_ESSAY            => $this->gradeEssay($question, $response),
            default => [
                'is_correct'    => false,
                'points_earned' => 0,
                'feedback'      => 'Unknown question type',
            ],
        };
    }

    protected function gradeMultipleChoice(GeneralExamQuestion $question, string $response): array
    {
        $normalizedResponse = strtoupper(trim($response));
        $normalizedCorrect  = strtoupper(trim($question->correct_answer ?? ''));

        $isCorrect = $normalizedResponse === $normalizedCorrect;

        return [
            'is_correct'     => $isCorrect,
            'points_earned'  => $isCorrect ? $question->marks : 0,
            'feedback'       => $isCorrect
                ? 'Correct!'
                : "Incorrect. The correct answer is: {$question->correct_answer}",
            'correct_answer' => $question->correct_answer,
        ];
    }

    protected function gradeTrueFalse(GeneralExamQuestion $question, string $response): array
    {
        $normalizedResponse = strtolower(trim($response));
        $normalizedCorrect  = strtolower(trim($question->correct_answer ?? ''));

        $trueValues  = ['true', '1', 'yes', 't'];
        $falseValues = ['false', '0', 'no', 'f'];

        $responseIsTrue = in_array($normalizedResponse, $trueValues);
        $responseIsFalse = in_array($normalizedResponse, $falseValues);
        $correctIsTrue  = in_array($normalizedCorrect, $trueValues);

        $isCorrect = ($responseIsTrue && $correctIsTrue)
                  || ($responseIsFalse && ! $correctIsTrue);

        return [
            'is_correct'     => $isCorrect,
            'points_earned'  => $isCorrect ? $question->marks : 0,
            'feedback'       => $isCorrect
                ? 'Correct!'
                : "Incorrect. The correct answer is: {$question->correct_answer}",
            'correct_answer' => $question->correct_answer,
        ];
    }

    protected function gradeShortAnswer(GeneralExamQuestion $question, string $response): array
    {
        $normalizedResponse = strtolower(trim($response));
        $normalizedCorrect  = strtolower(trim($question->correct_answer ?? ''));

        if ($normalizedResponse === $normalizedCorrect) {
            return [
                'is_correct'     => true,
                'points_earned'  => $question->marks,
                'feedback'       => 'Correct!',
                'correct_answer' => $question->correct_answer,
            ];
        }

        if (! empty($question->keywords)) {
            $matchedKeywords = 0;
            $totalKeywords   = count($question->keywords);

            foreach ($question->keywords as $keyword) {
                if (str_contains($normalizedResponse, strtolower($keyword))) {
                    $matchedKeywords++;
                }
            }

            if ($totalKeywords > 0) {
                $keywordRatio = $matchedKeywords / $totalKeywords;
                $pointsEarned = round($question->marks * $keywordRatio, 2);

                return [
                    'is_correct'          => $keywordRatio >= 0.8,
                    'points_earned'       => $pointsEarned,
                    'feedback'            => "Matched {$matchedKeywords} of {$totalKeywords} expected keywords.",
                    'correct_answer'      => $question->correct_answer,
                    'requires_review'     => false,
                    'keyword_match_ratio' => $keywordRatio,
                ];
            }
        }

        try {
            return $this->gradeWithAI($question, $response, 'short_answer');
        } catch (\Exception $e) {
            Log::warning('AI grading failed for short answer', [
                'question_id' => $question->id,
                'error'       => $e->getMessage(),
            ]);

            return [
                'is_correct'     => null,
                'points_earned'  => 0,
                'feedback'       => 'Requires manual review',
                'correct_answer' => $question->correct_answer,
                'requires_review' => true,
            ];
        }
    }

    protected function gradeEssay(GeneralExamQuestion $question, string $response): array
    {
        try {
            return $this->gradeWithAI($question, $response, 'essay');
        } catch (\Exception $e) {
            Log::warning('AI grading failed for essay', [
                'question_id' => $question->id,
                'error'       => $e->getMessage(),
            ]);

            return [
                'is_correct'      => null,
                'points_earned'   => 0,
                'feedback'        => 'Requires manual review',
                'requires_review' => true,
            ];
        }
    }

    // ─── AI grading (unchanged from original) ─────────────────────────────────

    protected function gradeWithAI(GeneralExamQuestion $question, string $response, string $type): array
    {
        $prompt = $this->buildGradingPrompt($question, $response, $type);

        $aiResponse = $this->chatService->chat([
            'input'            => $prompt,
            'request_type'     => 'grading',
            'creativity_level' => 0.3,
            'response_length'  => 500,
        ]);

        $content = $aiResponse['content'] ?? '';

        return $this->parseAIGradingResponse($content, $question);
    }

    protected function buildGradingPrompt(GeneralExamQuestion $question, string $response, string $type): string
    {
        return match ($type) {
            'short_answer' => "Grade this short-answer response.\n\nQuestion: {$question->question}\nCorrect Answer: {$question->correct_answer}\nStudent Response: {$response}\nMax Marks: {$question->marks}\n\nReturn JSON: {\"is_correct\": bool, \"points_earned\": float, \"feedback\": string}",
            'essay'        => "Grade this essay response.\n\nQuestion: {$question->question}\nRubric: {$question->grading_rubric}\nStudent Response: {$response}\nMax Marks: {$question->marks}\n\nReturn JSON: {\"is_correct\": null, \"points_earned\": float, \"feedback\": string, \"requires_review\": bool}",
            default        => '',
        };
    }

    protected function parseAIGradingResponse(string $content, GeneralExamQuestion $question): array
    {
        try {
            $clean   = preg_replace('/```json\s*|\s*```/', '', $content);
            $decoded = json_decode(trim($clean), true, 512, JSON_THROW_ON_ERROR);

            $pointsEarned = min(
                $question->marks,
                max(0, (float) ($decoded['points_earned'] ?? 0))
            );

            return [
                'is_correct'      => $decoded['is_correct'] ?? null,
                'points_earned'   => $pointsEarned,
                'feedback'        => $decoded['feedback'] ?? 'No feedback provided',
                'requires_review' => $decoded['requires_review'] ?? false,
            ];
        } catch (\Exception) {
            return [
                'is_correct'      => null,
                'points_earned'   => 0,
                'feedback'        => 'AI grading failed. Requires manual review.',
                'requires_review' => true,
            ];
        }
    }

    // ─── Manual grading ───────────────────────────────────────────────────────

    /**
     * Manually grade a single question in a submission.
     *
     * Points are capped at the question's own marks value.  The percentage
     * is recalculated against the submission's stored total_marks (which
     * already reflects target_total_marks if that was set at grading time).
     */
    public function manualGradeQuestion(
        GeneralExamSubmission $submission,
        int                   $questionId,
        float                 $points,
        ?string               $feedback = null,
        ?int                  $graderId  = null
    ): GeneralExamSubmission {
        $question = GeneralExamQuestion::find($questionId);

        if (! $question) {
            throw new \InvalidArgumentException("Question not found: {$questionId}");
        }

        $points    = min($question->marks, max(0, $points));
        $responses = $submission->responses ?? [];
        $key       = (string) $questionId;

        $responses[$key] = array_merge($responses[$key] ?? [], [
            'points_earned'   => $points,
            'manual_feedback' => $feedback,
            'manually_graded' => true,
            'graded_by'       => $graderId,
            'graded_at'       => now()->toISOString(),
            'is_correct'      => $points >= $question->marks * 0.7,
        ]);


        // Excluded questions must not contribute to the score
        $totalScore = collect($responses)
            ->filter(fn($r) => empty($r['excluded_from_grading']))
            ->sum('points_earned');
        $percentage = $submission->total_marks > 0
            ? min(100, ($totalScore / $submission->total_marks) * 100)
            : 0;

        $submission->update([
            'responses'  => $responses,
            'score'      => $totalScore,
            'percentage' => round($percentage, 2),
            'grade'      => $this->calculateGrade($percentage),
            'status'     => GeneralExamSubmission::STATUS_MANUALLY_REVIEWED,
        ]);

        return $submission->fresh();
    }

    // ─── Bonus ─────────────────────────────────────────────────────────────

    /**
     * Apply bonus points to a submission.
     * The final percentage is capped at 100 regardless of bonus size.
     * Passing null bonus_points removes any existing bonus.
     */
    public function applyBonus(
        GeneralExamSubmission $submission,
        float                 $bonusPoints,
        ?string               $reason = null,
        ?int                  $grantedBy = null
    ): GeneralExamSubmission {
        $bonusPoints = max(0, $bonusPoints);

        // Recompute base percentage from stored score/total_marks (without any prior bonus)
        $basePercentage = $submission->total_marks > 0
            ? ($submission->score / $submission->total_marks) * 100
            : 0;

        $finalPercentage = min(100, round($basePercentage + $bonusPoints, 2));

        $submission->update([
            'bonus_points'      => $bonusPoints,
            'bonus_reason'      => $reason,
            'bonus_granted_by'  => $grantedBy,
            'bonus_granted_at'  => $bonusPoints > 0 ? now() : null,
            'percentage'        => $finalPercentage,
            'grade'             => $this->calculateGrade($finalPercentage),
        ]);

        return $submission->fresh();
    }

    // ─── Finalisation ─────────────────────────────────────────────────────────

    public function finalizeGrading(
        GeneralExamSubmission $submission,
        User                  $grader,
        ?string               $overallFeedback = null
    ): GeneralExamSubmission {
        $submission->update([
            'status'                 => GeneralExamSubmission::STATUS_FINAL,
            'graded_by'              => $grader->id,
            'graded_at'              => now(),
            'teacher_feedback'       => $overallFeedback,
            'requires_manual_review' => false,
        ]);

        return $submission->fresh();
    }

    // ─── Bulk operations ─────────────────────────────────────────────────────

    /**
     * Bulk-grade all STATUS_SUBMITTED submissions for an exam.
     */
    public function bulkGradeAssignment(GeneralExam $assignment): array
    {
        $submissions = $assignment->submissions()
            ->where('status', GeneralExamSubmission::STATUS_SUBMITTED)
            ->get();

        return $this->processSubmissionBatch($submissions, 'bulk grade');
    }

    /**
     * Re-grade all graded (or submitted) submissions for an exam.
     *
     * Used after answer-key corrections or after target_total_marks changes.
     * Submissions in STATUS_FINAL are skipped unless $includeFinal is true.
     */
    public function regradeAllForExam(GeneralExam $assignment, bool $includeFinal = false): array
    {
        $statuses = [
            GeneralExamSubmission::STATUS_SUBMITTED,
            GeneralExamSubmission::STATUS_AUTO_GRADED,
            GeneralExamSubmission::STATUS_MANUALLY_REVIEWED,
        ];

        if ($includeFinal) {
            $statuses[] = GeneralExamSubmission::STATUS_FINAL;
        }

        $submissions = $assignment->submissions()
            ->whereIn('status', $statuses)
            ->whereNotNull('submitted_at')
            ->get();

        return $this->processSubmissionBatch($submissions, 'regrade exam');
    }

    /**
     * Re-grade all submissions that contain at least one of the given
     * GeneralExamQuestion IDs.
     *
     * Used after answer-key corrections on specific questions.
     *
     * @param  int[]  $examQuestionIds  IDs from general_exam_questions
     */
    public function regradeForQuestions(array $examQuestionIds, bool $includeFinal = false): array
    {
        if (empty($examQuestionIds)) {
            return ['total' => 0, 'graded' => 0, 'failed' => 0, 'errors' => []];
        }

        // Find every exam that contains at least one of these question IDs
        $examIds = GeneralExamQuestion::whereIn('id', $examQuestionIds)
            ->distinct()
            ->pluck('general_exam_id');

        $statuses = [
            GeneralExamSubmission::STATUS_SUBMITTED,
            GeneralExamSubmission::STATUS_AUTO_GRADED,
            GeneralExamSubmission::STATUS_MANUALLY_REVIEWED,
        ];

        if ($includeFinal) {
            $statuses[] = GeneralExamSubmission::STATUS_FINAL;
        }

        $submissions = GeneralExamSubmission::whereIn('general_exam_id', $examIds)
            ->whereIn('status', $statuses)
            ->whereNotNull('submitted_at')
            ->get();

        return $this->processSubmissionBatch($submissions, 'regrade by question');
    }

    // ─── Reporting ────────────────────────────────────────────────────────────

    public function getSubmissionsRequiringReview(GeneralExam $assignment): \Illuminate\Database\Eloquent\Collection
    {
        return $assignment->submissions()
            ->where('requires_manual_review', true)
            ->whereIn('status', [
                GeneralExamSubmission::STATUS_AUTO_GRADED,
                GeneralExamSubmission::STATUS_MANUALLY_REVIEWED,
            ])
            ->get();
    }

    public function getGradingSummary(GeneralExam $assignment): array
    {
        $submissions = $assignment->submissions;

        $gradeDistribution = ['A+' => 0, 'A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'F' => 0];

        $gradedSubmissions = $submissions->whereIn('status', [
            GeneralExamSubmission::STATUS_AUTO_GRADED,
            GeneralExamSubmission::STATUS_MANUALLY_REVIEWED,
            GeneralExamSubmission::STATUS_FINAL,
        ]);

        foreach ($gradedSubmissions as $submission) {
            if (isset($gradeDistribution[$submission->grade])) {
                $gradeDistribution[$submission->grade]++;
            }
        }

        $questionAnalysis = [];
        foreach ($assignment->questions as $question) {
            $correctCount  = 0;
            $totalAttempts = 0;
            $totalPoints   = 0;

            foreach ($gradedSubmissions as $submission) {
                $resp = $submission->responses[$question->id] ?? null;
                if ($resp) {
                    $totalAttempts++;
                    if ($resp['is_correct'] ?? false) {
                        $correctCount++;
                    }
                    $totalPoints += $resp['points_earned'] ?? 0;
                }
            }

            $questionAnalysis[$question->id] = [
                'question'      => $question->question,
                'type'          => $question->type,
                'total_attempts' => $totalAttempts,
                'correct_count' => $correctCount,
                'accuracy_rate' => $totalAttempts > 0
                    ? round(($correctCount / $totalAttempts) * 100, 2)
                    : 0,
                'average_points' => $totalAttempts > 0
                    ? round($totalPoints / $totalAttempts, 2)
                    : 0,
                'max_points'    => $question->marks,
            ];
        }

        return [
            'total_submissions'  => $submissions->count(),
            'graded_count'       => $gradedSubmissions->count(),
            'pending_review'     => $submissions->where('requires_manual_review', true)->count(),
            'average_score'      => $gradedSubmissions->avg('percentage') ?? 0,
            'median_score'       => $this->calculateMedian($gradedSubmissions->pluck('percentage')->toArray()),
            'highest_score'      => $gradedSubmissions->max('percentage') ?? 0,
            'lowest_score'       => $gradedSubmissions->min('percentage') ?? 0,
            'grade_distribution' => $gradeDistribution,
            'question_analysis'  => $questionAnalysis,
        ];
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    protected function processSubmissionBatch(
        \Illuminate\Support\Collection $submissions,
        string $operation
    ): array {
        $results = [
            'total'  => $submissions->count(),
            'graded' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($submissions as $submission) {
            try {
                $this->gradeSubmission($submission);
                $results['graded']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'submission_id' => $submission->id,
                    'error'         => $e->getMessage(),
                ];
                Log::error("GeneralExamGradingService [{$operation}] failed", [
                    'submission_id' => $submission->id,
                    'error'         => $e->getMessage(),
                ]);
            }
        }

        return $results;
    }

    protected function calculateGrade(float $percentage): string
    {
        return match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B',
            $percentage >= 60 => 'C',
            $percentage >= 50 => 'D',
            default           => 'F',
        };
    }

    protected function calculateMedian(array $values): float
    {
        if (empty($values)) {
            return 0;
        }
        sort($values);
        $count  = count($values);
        $middle = (int) floor($count / 2);

        return $count % 2 === 0
            ? ($values[$middle - 1] + $values[$middle]) / 2
            : $values[$middle];
    }
}
