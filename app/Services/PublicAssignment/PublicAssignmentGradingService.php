<?php

namespace App\Services\PublicAssignment;

use App\Models\PublicAssignment;
use App\Models\PublicAssignmentQuestion;
use App\Models\PublicAssignmentSubmission;
use App\Models\User;
use App\Services\AcademicChatService;
use Illuminate\Support\Facades\Log;

class PublicAssignmentGradingService
{
    public function __construct(
        protected AcademicChatService $chatService
    ) {}

    /**
     * Grade a submission (auto-grade what's possible, mark others for review)
     */
    public function gradeSubmission(PublicAssignmentSubmission $submission): PublicAssignmentSubmission
    {
        $assignment = $submission->assignment;
        $questions = $assignment->questions;

        $totalScore = 0;
        $totalMarks = 0;
        $requiresManualReview = false;
        $gradedResponses = [];

        foreach ($questions as $question) {
            $totalMarks += $question->marks;
            $questionId = $question->id;
            $response = $submission->getResponse($questionId);

            if ($response !== null && $response !== '') {
                $gradeResult = $this->gradeQuestion($question, (string) $response);
                $totalScore += $gradeResult['points_earned'];

                if ($gradeResult['requires_review'] ?? false) {
                    $requiresManualReview = true;
                }

                $gradedResponses[$questionId] = array_merge(
                    $submission->responses[$questionId] ?? [],
                    $gradeResult
                );
            } else {
                $gradedResponses[$questionId] = [
                    'response' => null,
                    'is_correct' => false,
                    'points_earned' => 0,
                    'feedback' => 'No answer provided',
                ];
            }
        }

        $percentage = $totalMarks > 0 ? ($totalScore / $totalMarks) * 100 : 0;

        $submission->update([
            'responses' => $gradedResponses,
            'score' => $totalScore,
            'total_marks' => $totalMarks,
            'percentage' => round($percentage, 2),
            'grade' => $this->calculateGrade($percentage),
            'status' => PublicAssignmentSubmission::STATUS_AUTO_GRADED,
            'requires_manual_review' => $requiresManualReview,
            'graded_at' => now(),
        ]);

        return $submission->fresh();
    }

    /**
     * Grade a single question
     */
    protected function gradeQuestion(PublicAssignmentQuestion $question, string $response): array
    {
        return match ($question->type) {
            PublicAssignmentQuestion::TYPE_MULTIPLE_CHOICE => $this->gradeMultipleChoice($question, $response),
            PublicAssignmentQuestion::TYPE_TRUE_FALSE => $this->gradeTrueFalse($question, $response),
            PublicAssignmentQuestion::TYPE_SHORT_ANSWER => $this->gradeShortAnswer($question, $response),
            PublicAssignmentQuestion::TYPE_ESSAY => $this->gradeEssay($question, $response),
            default => ['is_correct' => false, 'points_earned' => 0, 'feedback' => 'Unknown question type'],
        };
    }

    /**
     * Grade multiple choice question
     */
    protected function gradeMultipleChoice(PublicAssignmentQuestion $question, string $response): array
    {
        $normalizedResponse = strtoupper(trim($response));
        $normalizedCorrect = strtoupper(trim($question->correct_answer ?? ''));

        $isCorrect = $normalizedResponse === $normalizedCorrect;

        return [
            'is_correct' => $isCorrect,
            'points_earned' => $isCorrect ? $question->marks : 0,
            'feedback' => $isCorrect ? 'Correct!' : "Incorrect. The correct answer is: {$question->correct_answer}",
            'correct_answer' => $question->correct_answer,
        ];
    }

    /**
     * Grade true/false question
     */
    protected function gradeTrueFalse(PublicAssignmentQuestion $question, string $response): array
    {
        $normalizedResponse = strtolower(trim($response));
        $normalizedCorrect = strtolower(trim($question->correct_answer ?? ''));

        $trueValues = ['true', '1', 'yes', 't'];
        $falseValues = ['false', '0', 'no', 'f'];

        $responseIsTrue = in_array($normalizedResponse, $trueValues);
        $responseIsFalse = in_array($normalizedResponse, $falseValues);
        $correctIsTrue = in_array($normalizedCorrect, $trueValues);

        $isCorrect = ($responseIsTrue && $correctIsTrue) || ($responseIsFalse && ! $correctIsTrue);

        return [
            'is_correct' => $isCorrect,
            'points_earned' => $isCorrect ? $question->marks : 0,
            'feedback' => $isCorrect ? 'Correct!' : "Incorrect. The correct answer is: {$question->correct_answer}",
            'correct_answer' => $question->correct_answer,
        ];
    }

    /**
     * Grade short answer question (with AI assistance)
     */
    protected function gradeShortAnswer(PublicAssignmentQuestion $question, string $response): array
    {
        $normalizedResponse = strtolower(trim($response));
        $normalizedCorrect = strtolower(trim($question->correct_answer ?? ''));

        // Check for exact match first
        if ($normalizedResponse === $normalizedCorrect) {
            return [
                'is_correct' => true,
                'points_earned' => $question->marks,
                'feedback' => 'Correct!',
                'correct_answer' => $question->correct_answer,
            ];
        }

        // Check keywords if available
        if (! empty($question->keywords)) {
            $matchedKeywords = 0;
            $totalKeywords = count($question->keywords);

            foreach ($question->keywords as $keyword) {
                if (str_contains($normalizedResponse, strtolower($keyword))) {
                    $matchedKeywords++;
                }
            }

            if ($totalKeywords > 0) {
                $keywordRatio = $matchedKeywords / $totalKeywords;
                $pointsEarned = round($question->marks * $keywordRatio, 2);

                return [
                    'is_correct' => $keywordRatio >= 0.8,
                    'points_earned' => $pointsEarned,
                    'feedback' => "Matched {$matchedKeywords} of {$totalKeywords} expected keywords.",
                    'correct_answer' => $question->correct_answer,
                    'requires_review' => false,
                    'keyword_match_ratio' => $keywordRatio,
                ];
            }
        }

        // Try AI grading
        try {
            return $this->gradeWithAI($question, $response, 'short_answer');
        } catch (\Exception $e) {
            Log::warning('AI grading failed for short answer', [
                'question_id' => $question->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'is_correct' => null,
                'points_earned' => 0,
                'feedback' => 'Requires manual review',
                'correct_answer' => $question->correct_answer,
                'requires_review' => true,
            ];
        }
    }

    /**
     * Grade essay question (with AI assistance)
     */
    protected function gradeEssay(PublicAssignmentQuestion $question, string $response): array
    {
        try {
            return $this->gradeWithAI($question, $response, 'essay');
        } catch (\Exception $e) {
            Log::warning('AI grading failed for essay', [
                'question_id' => $question->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'is_correct' => null,
                'points_earned' => 0,
                'feedback' => 'Requires manual review',
                'requires_review' => true,
            ];
        }
    }

    /**
     * Grade using AI
     */
    protected function gradeWithAI(PublicAssignmentQuestion $question, string $response, string $type): array
    {
        $prompt = $this->buildGradingPrompt($question, $response, $type);

        $aiResponse = $this->chatService->chat([
            'input' => $prompt,
            'request_type' => 'grading',
            'creativity_level' => 0.3,
            'response_length' => 500,
        ]);

        $content = $aiResponse['content'] ?? '';

        return $this->parseAIGradingResponse($content, $question);
    }

    /**
     * Build AI grading prompt
     */
    protected function buildGradingPrompt(PublicAssignmentQuestion $question, string $response, string $type): string
    {
        $rubric = $question->grading_rubric ?? 'Grade based on accuracy, completeness, and clarity.';
        $maxMarks = $question->marks;
        $correctAnswer = $question->correct_answer ?? 'Not provided';
        $keywords = ! empty($question->keywords) ? implode(', ', $question->keywords) : 'Not specified';

        return <<<PROMPT
You are an academic grading assistant. Grade the following {$type} response.

QUESTION:
{$question->question}

EXPECTED ANSWER/KEY POINTS:
{$correctAnswer}

KEYWORDS TO LOOK FOR:
{$keywords}

GRADING RUBRIC:
{$rubric}

MAXIMUM MARKS: {$maxMarks}

STUDENT'S RESPONSE:
{$response}

Please provide your assessment in the following JSON format:
{
    "points_earned": <number between 0 and {$maxMarks}>,
    "percentage": <percentage of marks earned>,
    "feedback": "<constructive feedback for the student>",
    "strengths": ["<list of strengths>"],
    "areas_for_improvement": ["<list of areas to improve>"],
    "is_correct": <true if mostly correct, false otherwise, null if unclear>
}

Be fair but rigorous in your assessment. Provide specific, constructive feedback.
PROMPT;
    }

    /**
     * Parse AI grading response
     */
    protected function parseAIGradingResponse(string $aiResponse, PublicAssignmentQuestion $question): array
    {
        try {
            // Extract JSON from response
            $jsonMatch = [];
            if (preg_match('/\{[\s\S]*\}/', $aiResponse, $jsonMatch)) {
                $parsed = json_decode($jsonMatch[0], true);

                if ($parsed && isset($parsed['points_earned'])) {
                    $pointsEarned = min($question->marks, max(0, (float) $parsed['points_earned']));

                    return [
                        'is_correct' => $parsed['is_correct'] ?? ($pointsEarned >= $question->marks * 0.7),
                        'points_earned' => $pointsEarned,
                        'feedback' => $parsed['feedback'] ?? 'Graded by AI',
                        'ai_graded' => true,
                        'ai_details' => [
                            'strengths' => $parsed['strengths'] ?? [],
                            'areas_for_improvement' => $parsed['areas_for_improvement'] ?? [],
                        ],
                        'requires_review' => false,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to parse AI grading response', [
                'question_id' => $question->id,
                'error' => $e->getMessage(),
            ]);
        }

        return [
            'is_correct' => null,
            'points_earned' => 0,
            'feedback' => 'AI grading failed. Requires manual review.',
            'requires_review' => true,
        ];
    }

    /**
     * Manually grade a question in a submission
     */
    public function manualGradeQuestion(
        PublicAssignmentSubmission $submission,
        int $questionId,
        float $points,
        ?string $feedback = null,
        ?int $graderId = null
    ): PublicAssignmentSubmission {
        $question = PublicAssignmentQuestion::find($questionId);

        if (! $question) {
            throw new \InvalidArgumentException("Question not found: {$questionId}");
        }

        // Ensure points don't exceed max marks
        $points = min($question->marks, max(0, $points));

        $responses = $submission->responses ?? [];

        if (isset($responses[$questionId])) {
            $responses[$questionId]['points_earned'] = $points;
            $responses[$questionId]['manual_feedback'] = $feedback;
            $responses[$questionId]['manually_graded'] = true;
            $responses[$questionId]['graded_by'] = $graderId;
            $responses[$questionId]['graded_at'] = now()->toISOString();
            $responses[$questionId]['is_correct'] = $points >= $question->marks * 0.7;
        }

        // Recalculate total score
        $totalScore = 0;
        foreach ($responses as $response) {
            $totalScore += $response['points_earned'] ?? 0;
        }

        $percentage = $submission->total_marks > 0
            ? ($totalScore / $submission->total_marks) * 100
            : 0;

        $submission->update([
            'responses' => $responses,
            'score' => $totalScore,
            'percentage' => round($percentage, 2),
            'grade' => $this->calculateGrade($percentage),
            'status' => PublicAssignmentSubmission::STATUS_MANUALLY_REVIEWED,
        ]);

        return $submission->fresh();
    }

    /**
     * Finalize grading for a submission
     */
    public function finalizeGrading(
        PublicAssignmentSubmission $submission,
        User $grader,
        ?string $overallFeedback = null
    ): PublicAssignmentSubmission {
        $submission->update([
            'status' => PublicAssignmentSubmission::STATUS_FINAL,
            'graded_by' => $grader->id,
            'graded_at' => now(),
            'teacher_feedback' => $overallFeedback,
            'requires_manual_review' => false,
        ]);

        return $submission->fresh();
    }

    /**
     * Bulk grade all pending submissions for an assignment
     */
    public function bulkGradeAssignment(PublicAssignment $assignment): array
    {
        $submissions = $assignment->submissions()
            ->where('status', PublicAssignmentSubmission::STATUS_SUBMITTED)
            ->get();

        $results = [
            'total' => $submissions->count(),
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
                    'error' => $e->getMessage(),
                ];
                Log::error('Bulk grading failed for submission', [
                    'submission_id' => $submission->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $results;
    }

    /**
     * Get submissions requiring manual review
     */
    public function getSubmissionsRequiringReview(PublicAssignment $assignment): \Illuminate\Database\Eloquent\Collection
    {
        return $assignment->submissions()
            ->where('requires_manual_review', true)
            ->whereIn('status', [
                PublicAssignmentSubmission::STATUS_AUTO_GRADED,
                PublicAssignmentSubmission::STATUS_MANUALLY_REVIEWED,
            ])
            ->get();
    }

    /**
     * Get grading summary for an assignment
     */
    public function getGradingSummary(PublicAssignment $assignment): array
    {
        $submissions = $assignment->submissions;

        $gradeDistribution = [
            'A+' => 0, 'A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'F' => 0,
        ];

        $gradedSubmissions = $submissions->whereIn('status', [
            PublicAssignmentSubmission::STATUS_AUTO_GRADED,
            PublicAssignmentSubmission::STATUS_MANUALLY_REVIEWED,
            PublicAssignmentSubmission::STATUS_FINAL,
        ]);

        foreach ($gradedSubmissions as $submission) {
            if (isset($gradeDistribution[$submission->grade])) {
                $gradeDistribution[$submission->grade]++;
            }
        }

        // Question-level analysis
        $questionAnalysis = [];
        $questions = $assignment->questions;

        foreach ($questions as $question) {
            $correctCount = 0;
            $totalAttempts = 0;
            $totalPoints = 0;

            foreach ($gradedSubmissions as $submission) {
                $response = $submission->responses[$question->id] ?? null;
                if ($response) {
                    $totalAttempts++;
                    if ($response['is_correct'] ?? false) {
                        $correctCount++;
                    }
                    $totalPoints += $response['points_earned'] ?? 0;
                }
            }

            $questionAnalysis[$question->id] = [
                'question' => $question->question,
                'type' => $question->type,
                'total_attempts' => $totalAttempts,
                'correct_count' => $correctCount,
                'accuracy_rate' => $totalAttempts > 0 ? round(($correctCount / $totalAttempts) * 100, 2) : 0,
                'average_points' => $totalAttempts > 0 ? round($totalPoints / $totalAttempts, 2) : 0,
                'max_points' => $question->marks,
            ];
        }

        return [
            'total_submissions' => $submissions->count(),
            'graded_count' => $gradedSubmissions->count(),
            'pending_review' => $submissions->where('requires_manual_review', true)->count(),
            'average_score' => $gradedSubmissions->avg('percentage') ?? 0,
            'median_score' => $this->calculateMedian($gradedSubmissions->pluck('percentage')->toArray()),
            'highest_score' => $gradedSubmissions->max('percentage') ?? 0,
            'lowest_score' => $gradedSubmissions->min('percentage') ?? 0,
            'grade_distribution' => $gradeDistribution,
            'question_analysis' => $questionAnalysis,
        ];
    }

    /**
     * Calculate grade from percentage
     */
    protected function calculateGrade(float $percentage): string
    {
        return match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B',
            $percentage >= 60 => 'C',
            $percentage >= 50 => 'D',
            default => 'F',
        };
    }

    /**
     * Calculate median value
     */
    protected function calculateMedian(array $values): float
    {
        if (empty($values)) {
            return 0;
        }

        sort($values);
        $count = count($values);
        $middle = floor($count / 2);

        if ($count % 2 === 0) {
            return ($values[$middle - 1] + $values[$middle]) / 2;
        }

        return $values[$middle];
    }
}
