<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentResponse;
use App\Models\Question;
use App\Models\Student;
use App\Models\Teacher;
use App\Notifications\EssayAssessmentSubmitted;
use App\Notifications\EssayAssessmentSubmittedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class AssessmentService
{
    public function submitAssessment(Assessment $assessment, array $questionsData, array $summaryData): AssessmentResponse
    {
        return DB::transaction(function () use ($assessment, $questionsData, $summaryData) {
            // Process questions data
            $processedQuestions = [];
            $hasEssayQuestions = false;

            foreach ($questionsData as $questionData) {
                $processedQuestion = $this->processQuestionResponse($questionData);
                $processedQuestions[] = $processedQuestion;

                if ($questionData['type'] === 'essay_question') {
                    $hasEssayQuestions = true;
                }
            }

            // Create comprehensive data structure
            $responseData = [
                'questions' => $processedQuestions,
                'summary' => $summaryData,
                'total_questions' => count($processedQuestions),
                'answered_questions' => $summaryData['answered_questions'],
                'correct_answers' => $summaryData['correct_answers'],
                'total_score' => $summaryData['total_score'],
                'max_score' => $summaryData['max_score'],
                'percentage' => $summaryData['percentage'],
                'time_taken' => $summaryData['time_taken'],
                'subject_id' => $assessment->subject_id,
                'topic_id' => $assessment->topic_id,
                'subtopic_id' => $assessment->subtopic_id,
                'submitted_at' => now()->toISOString(),
                'has_essay_questions' => $hasEssayQuestions,
            ];

            // Create or update assessment response
            $assessmentResponse = AssessmentResponse::updateOrCreate(
                ['assessment_id' => $assessment->id],
                ['data' => $responseData]
            );

            // Update assessment
            $assessment->update([
                'has_essay_questions' => $hasEssayQuestions,
                'end_time' => now(),
            ]);

            $assessment->markAsCompleted();

            // Send notification if has essay questions
            if ($hasEssayQuestions) {
                $this->notifyTeacherForEssayGrading($assessment);
            }

            return $assessmentResponse;
        });
    }

    protected function processQuestionResponse(array $questionData): array
    {
        $processed = AssessmentResponse::createQuestionData($questionData);

        // Auto-grade non-essay questions
        if ($questionData['type'] !== 'essay_question') {
            $processed = $this->autoGradeQuestion($processed);
        }

        return $processed;
    }

    protected function autoGradeQuestion(array $questionData): array
    {
        $isCorrect = false;
        $pointsEarned = 0;

        logInfo('Auto-grading question',
            $questionData,
        );

        switch ($questionData['type']) {
            case 'multiple_choice_question':
                $isCorrect = $questionData['student_answer'] === $questionData['correct_answer'];
                break;

            case 'true_or_false_question':
                $isCorrect = (bool)$questionData['student_answer'] === (bool)$questionData['correct_answer'];
                break;
        }

        if ($isCorrect) {
            $pointsEarned = $questionData['points_possible'];
        }

        $questionData['is_correct'] = $isCorrect;
        $questionData['points_earned'] = $pointsEarned;
        $questionData['is_graded'] = true;

        return $questionData;
    }

    public function gradeEssayQuestion(AssessmentResponse $assessmentResponse, int $questionIndex, float $points, string $feedback = null, Teacher $teacher = null): void
    {
        $assessmentResponse->gradeEssayQuestion($questionIndex, $points, $feedback, $teacher?->id);

        // Check if all essays are graded
        if ($assessmentResponse->allEssaysGraded()) {
            $assessment = $assessmentResponse->assessment;
            $assessment->update(['graded_by' => $teacher?->id]);
            $assessment->markAsGraded();
        }
    }

    protected function notifyTeacherForEssayGrading(Assessment $assessment): void
    {
        $teacher = $assessment->getTeacherForGrading();

        if ($teacher) {
            try {
                Notification::send($teacher, new EssayAssessmentSubmittedNotification($assessment));

                Log::info('Essay assessment notification sent', [
                    'assessment_id' => $assessment->id,
                    'teacher_id' => $teacher->id,
                    'student_id' => $assessment->student_id,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send essay assessment notification', [
                    'assessment_id' => $assessment->id,
                    'teacher_id' => $teacher->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    public function calculateAssessmentSummary(array $responses): array
    {
        $totalQuestions = count($responses);
        $answeredQuestions = 0;
        $correctAnswers = 0;
        $totalScore = 0;
        $maxScore = 0;

        foreach ($responses as $response) {
            if (!empty($response['student_answer'])) {
                $answeredQuestions++;
            }

            if ($response['is_correct'] ?? false) {
                $correctAnswers++;
            }

            $totalScore += $response['points_earned'] ?? 0;
            $maxScore += $response['points_possible'] ?? 0;
        }

        return [
            'total_questions' => $totalQuestions,
            'answered_questions' => $answeredQuestions,
            'correct_answers' => $correctAnswers,
            'total_score' => $totalScore,
            'max_score' => $maxScore,
            'percentage' => $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0,
        ];
    }
}
