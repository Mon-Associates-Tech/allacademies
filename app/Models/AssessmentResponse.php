<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentResponse extends Model
{
    protected $fillable = [
        'assessment_id',
        'data', // JSON field containing all assessment data
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get questions data from the response
     */
    public function getQuestionsData(): array
    {
        return $this->data['questions'] ?? [];
    }

    /**
     * Get assessment summary data
     */
    public function getSummaryData(): array
    {
        return [
            'total_questions' => $this->data['total_questions'] ?? 0,
            'answered_questions' => $this->data['answered_questions'] ?? 0,
            'correct_answers' => $this->data['correct_answers'] ?? 0,
            'total_score' => $this->data['total_score'] ?? 0,
            'max_score' => $this->data['max_score'] ?? 0,
            'percentage' => $this->data['percentage'] ?? 0,
            'time_taken' => $this->data['time_taken'] ?? 0,
            'subject_id' => $this->data['subject_id'] ?? null,
            'topic_id' => $this->data['topic_id'] ?? null,
            'subtopic_id' => $this->data['subtopic_id'] ?? null,
        ];
    }

    /**
     * Check if assessment has essay questions
     */
    public function hasEssayQuestions(): bool
    {
        $questions = $this->getQuestionsData();

        foreach ($questions as $question) {
            if ($question['type'] === 'essay_question') {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if all essay questions are graded
     */
    public function allEssaysGraded(): bool
    {
        $questions = $this->getQuestionsData();

        foreach ($questions as $question) {
            if ($question['type'] === 'essay_question' && !($question['is_graded'] ?? false)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get essay questions that need grading
     */
    public function getEssayQuestionsForGrading(): array
    {
        $questions = $this->getQuestionsData();
        $essayQuestions = [];

        foreach ($questions as $index => $question) {
            if ($question['type'] === 'essay_question' && !($question['is_graded'] ?? false)) {
                $essayQuestions[] = array_merge($question, ['index' => $index]);
            }
        }

        return $essayQuestions;
    }

    /**
     * Update essay question grading
     */
    public function gradeEssayQuestion(int $questionIndex, float $points, string $feedback = null, $gradedBy = null): void
    {
        $data = $this->data;

        if (isset($data['questions'][$questionIndex])) {
            $data['questions'][$questionIndex]['is_graded'] = true;
            $data['questions'][$questionIndex]['points_earned'] = $points;
            $data['questions'][$questionIndex]['teacher_feedback'] = $feedback;
            $data['questions'][$questionIndex]['graded_by'] = $gradedBy;
            $data['questions'][$questionIndex]['graded_at'] = now()->toISOString();

            // Recalculate total score
            $this->recalculateScores($data);

            $this->update(['data' => $data]);
        }
    }

    /**
     * Recalculate assessment scores
     */
    protected function recalculateScores(array &$data): void
    {
        $totalScore = 0;
        $maxScore = 0;
        $correctAnswers = 0;

        foreach ($data['questions'] as $question) {
            $maxScore += $question['points_possible'] ?? 0;
            $totalScore += $question['points_earned'] ?? 0;

            if ($question['is_correct'] ?? false) {
                $correctAnswers++;
            }
        }

        $data['total_score'] = $totalScore;
        $data['max_score'] = $maxScore;
        $data['correct_answers'] = $correctAnswers;
        $data['percentage'] = $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0;
    }

    /**
     * Structure for storing question data
     */
    public static function createQuestionData(array $questionData): array
    {
        return [
            'question_id' => $questionData['question_id'],
            'type' => $questionData['type'], // 'multiple_choice_question', 'true_or_false_question', 'essay_question'
            'question_text' => $questionData['question_text'],
            'options' => $questionData['options'] ?? null, // For multiple choice
            'student_answer' => $questionData['student_answer'],
            'correct_answer' => $questionData['correct_answer'] ?? null, // Not applicable for essays
            'is_correct' => $questionData['is_correct'] ?? null, // Not applicable for essays initially
            'points_possible' => $questionData['points_possible'],
            'points_earned' => $questionData['points_earned'] ?? 0,
            'response_time' => $questionData['response_time'] ?? null,
            'is_graded' => $questionData['is_graded'] ?? ($questionData['type'] !== 'essay_question'),
            'teacher_feedback' => $questionData['teacher_feedback'] ?? null,
            'graded_by' => $questionData['graded_by'] ?? null,
            'graded_at' => $questionData['graded_at'] ?? null,
        ];
    }
}
