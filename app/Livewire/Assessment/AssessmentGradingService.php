<?php

namespace App\Livewire\Assessment;

use App\Models\Assessment;
use App\Models\AssessmentResponse;
use Illuminate\Support\Collection;

class AssessmentGradingService
{

    /**
     * Grade an entire assessment.
     */
    public function gradeAssessment(Assessment $assessment, array $responses): array
    {
        $questions = collect($assessment->questions_data);
        $gradedResponses = [];
        $totalScore = 0;
        $maxScore = 0;
        $needsManualGrading = false;

        foreach ($questions as $index => $question) {
            $responseItem = $responses[$index] ?? null;
            $responseValue = $responseItem['response'] ?? null;

            $maxScore += $question['points'] ?? 1;
            $gradedResult = [
                'is_correct' => false,
                'score_earned' => 0,
                'feedback' => 'Not answered'
            ];

            if ($responseValue !== null) {
                switch ($question['type']) {
                    case 'multiple_choice_question':
                        $gradedResult = $this->gradeMultipleChoice($question, $responseValue);
                        break;
                    case 'true_or_false_question':
                        $gradedResult = $this->gradeTrueFalse($question, $responseValue);
                        break;
                    case 'essay_question':
                        $gradedResult = $this->prepareEssayForGrading($question, $responseValue);
                        if ($gradedResult['needs_grading']) {
                            $needsManualGrading = true;
                        }
                        break;
                }
            }

            $totalScore += $gradedResult['score_earned'] ?? 0;
            $gradedResponses[$index] = array_merge(
                $gradedResult,
                [
                    'question_id' => $question['id'],
                    'question_text' => $question['question'],
                    'response' => $responseValue
                ]
            );
        }

        return [
            'total_score' => $totalScore,
            'max_score' => $maxScore,
            'percentage' => $maxScore > 0 ? round(($totalScore / $maxScore) * 100) : 0,
            'graded_responses' => $gradedResponses,
            'needs_manual_grading' => $needsManualGrading,
            'status' => $needsManualGrading ? 'pending_manual_grading' : 'graded'
        ];
    }

    /**
     * Grade multiple choice questions
     */
    public function gradeMultipleChoice($question, $response): float
    {

        $isCorrect = strtoupper($response['selected_option']) === strtoupper($question['correct_answer']);

        return $isCorrect ? $question['points'] : 0;
    }

    /**
     * Grade true/false questions
     */
    public function gradeTrueFalse($question, $response)
    {
        $isCorrect = (bool)$response === (bool)$question['correct_answer'];

        return $isCorrect ? $question['points'] : 0;
    }

    /**
     * Prepare essay questions for manual grading
     */
    public function prepareEssayForGrading($question, $response): array
    {
        return [
            'is_correct' => null,
            'score_earned' => 0,
            'needs_grading' => true,
            'response_length' => strlen($response),
            'word_count' => str_word_count($response),
            'feedback' => 'Essay submitted for manual grading'
        ];
    }

    /**
     * Calculate rubric-based scores for essays
     */
    public function gradeEssayWithRubric($question, $response, array $rubricScores): array
    {
        $totalScore = array_sum($rubricScores);
        $maxScore = $question['points'];

        return [
            'is_correct' => $totalScore >= ($maxScore * 0.7), // 70% threshold
            'score_earned' => min($totalScore, $maxScore),
            'rubric_scores' => $rubricScores,
            'feedback' => $this->generateEssayFeedback($rubricScores)
        ];
    }

    protected function generateEssayFeedback(array $rubricScores): string
    {
        $feedback = "Essay grading breakdown:\n";

        $criteria = [
            'content' => 'Content and Knowledge',
            'organization' => 'Organization and Structure',
            'language' => 'Language and Grammar',
            'analysis' => 'Critical Analysis'
        ];

        foreach ($rubricScores as $criterion => $score) {
            $criterionName = $criteria[$criterion] ?? $criterion;
            $feedback .= "- {$criterionName}: {$score}/5\n";
        }

        return $feedback;
    }


}
