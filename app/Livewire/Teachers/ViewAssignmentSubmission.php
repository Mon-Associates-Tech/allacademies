<?php

namespace App\Livewire\Teachers;

use App\Models\Assessment;
use App\Models\AssessmentResponse;
use App\Models\AssignmentSubmission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ViewAssignmentSubmission extends Component
{
    public $submissionId;

    public $teacherId;

    public $showGradingPanel = false;

    public $currentEssayIndex = null;

    public $essayGrade = '';

    public $essayFeedback = '';

    protected $submission;

    protected $teacher;

    protected $assignment;

    protected $student;

    protected $questions = [];

    protected $answers = [];

    protected $gradingData = [];

    public function mount(AssignmentSubmission $submission)
    {
        $this->submissionId = $submission->id;
        $this->teacherId = Auth::id();

        $this->initializeData();
    }

    private function initializeData()
    {
        $this->submission = AssignmentSubmission::with([
            'assignment.academicSubject',
            'assignment.user',
            'student.user',
        ])->find($this->submissionId);

        // Security check - ensure current user owns the assignment
        if ($this->submission->assignment->user_id !== Auth::id()) {
            abort(403, 'You are not authorized to view this submission.');
        }

        $this->assignment = $this->submission->assignment;
        $this->student = $this->submission->student;
        $this->answers = $this->submission->answers ?? [];

        Log::info('DEBUGGING SUBMISSION DATA:', [
            'submission_id' => $this->submissionId,
            'raw_answers_structure' => $this->answers,
            'answers_keys' => array_keys($this->answers),
            'answers_count' => count($this->answers),
            'assignment_id' => $this->assignment->id,
            'assignment_title' => $this->assignment->title,
            'student_id' => $this->student->id,
            'student_name' => $this->student->user->name ?? 'Unknown',
        ]);

        // Additional debug: show first few answer entries
        $sampleAnswers = array_slice($this->answers, 0, 5, true);
        Log::info('Sample of first 5 answers:', $sampleAnswers);

        $this->loadActualQuestionsAnswered();
    }

    // Use computed properties instead of public properties for complex data
    public function getSubmissionProperty()
    {
        if (! $this->submission) {
            $this->initializeData();
        }

        return $this->submission;
    }

    public function getAssignmentProperty()
    {
        return $this->submission->assignment;
    }

    public function getStudentProperty()
    {
        return $this->submission->student;
    }

    public function getQuestionsProperty()
    {
        if (empty($this->questions)) {
            $this->loadActualQuestionsAnswered();
        }

        return $this->questions;
    }

    public function getAnswersProperty()
    {
        return $this->answers;
    }

    public function getGradingDataProperty()
    {
        if (empty($this->gradingData)) {
            $this->loadActualQuestionsAnswered();
        }

        return $this->gradingData;
    }

    public function getHasEssayQuestionsProperty()
    {
        return collect($this->questions)->contains('type', 'essay_question');
    }

    public function getNeedsGradingProperty()
    {
        return collect($this->gradingData)->contains('needs_manual_grading', true);
    }

    private function loadActualQuestionsAnswered()
    {
        Log::info('Starting to load actual questions answered', [
            'submission_id' => $this->submissionId,
            'answers_count' => count($this->answers),
        ]);

        // PRIORITY 1: Try to get data from AssessmentResponse first (this contains the correct processed data)
        $this->questions = $this->getQuestionsFromAssessmentResponse();

        if (! empty($this->questions)) {
            Log::info('Successfully loaded questions from AssessmentResponse:', [
                'count' => count($this->questions),
                'question_ids' => collect($this->questions)->pluck('id')->toArray(),
            ]);

            // Initialize grading data from the assessment response
            $this->initializeGradingDataFromAssessmentResponse();

            return;
        }

        // FALLBACK: Only use submission answers if no assessment response found
        Log::warning('No AssessmentResponse found, falling back to submission answers');
        $this->questions = $this->getOnlyAnsweredQuestions();

        if (empty($this->questions)) {
            Log::error('No questions found for submission', [
                'submission_id' => $this->submissionId,
                'assignment_id' => $this->assignment->id,
            ]);
            $this->questions = [];
        }

        // Initialize grading data
        $this->initializeGradingData();
    }

    private function getQuestionsFromAssessmentResponse()
    {
        // Find the Assessment record for this submission
        $assessment = Assessment::where('assignment_id', $this->assignment->id)
            ->where('student_id', $this->student->id)
            ->first();

        if (! $assessment) {
            Log::info('No assessment found for submission', [
                'submission_id' => $this->submissionId,
                'assignment_id' => $this->assignment->id,
                'student_id' => $this->student->id,
            ]);

            return [];
        }

        // Try to get questions from assessment_responses table
        $assessmentResponse = AssessmentResponse::where('assessment_id', $assessment->id)->first();

        if (! $assessmentResponse) {
            Log::info('No AssessmentResponse found', [
                'assessment_id' => $assessment->id,
            ]);

            return [];
        }

        $responseData = $assessmentResponse->data;

        if (! isset($responseData['questions']) || empty($responseData['questions'])) {
            Log::warning('No questions found in AssessmentResponse data');

            return [];
        }

        $questionsData = $responseData['questions'];

        Log::info('Found questions in AssessmentResponse', [
            'count' => count($questionsData),
            'submission_id' => $this->submissionId,
        ]);

        return $this->formatQuestionsFromAssessmentResponse($questionsData);
    }

    private function formatQuestionsFromAssessmentResponse($questionsData)
    {
        $formattedQuestions = [];

        foreach ($questionsData as $index => $questionData) {
            // Extract question ID if available, otherwise use index
            $questionId = null;
            if (isset($questionData['question_id'])) {
                $questionId = $questionData['question_id'];
            } elseif (isset($questionData['id'])) {
                $questionId = $questionData['id'];
            } else {
                // Try to find the question ID from the responses based on the question content
                $questionId = $this->findQuestionIdByContent($questionData);
            }

            $formattedQuestions[] = [
                'id' => $questionId ?: $index, // Use index as fallback
                'type' => $questionData['type'] ?? 'unknown',
                'question' => $questionData['question'] ?? 'Question not available',
                'options' => $questionData['options'] ?? null,
                'answer' => $questionData['correct_answer'] ?? null,
                'points' => $questionData['points_possible'] ?? 1,
                'difficulty' => $questionData['difficulty'] ?? 'medium',
                'student_answer' => $questionData['student_answer'] ?? null,
                'is_correct' => $questionData['is_correct'] ?? null,
                'points_earned' => $questionData['points_earned'] ?? 0,
                'is_graded' => $questionData['is_graded'] ?? true,
                'teacher_feedback' => $questionData['teacher_feedback'] ?? null,
                'graded_by' => $questionData['graded_by'] ?? null,
                'graded_at' => $questionData['graded_at'] ?? null,
            ];
        }

        return $formattedQuestions;
    }

    private function findQuestionIdByContent($questionData)
    {
        // This is a helper method to try to match questions by content
        // when question ID is not directly available
        $questionText = $questionData['question'] ?? '';

        if (empty($questionText)) {
            return null;
        }

        // Try to find a matching question in the database by content
        $types = ['MultipleChoiceQuestion', 'TrueOrFalseQuestion', 'EssayQuestion'];

        foreach ($types as $type) {
            $model = "\\App\\Models\\{$type}";
            if (class_exists($model)) {
                $question = $model::whereRaw('JSON_EXTRACT(question, "$.down") LIKE ?', ["%{$questionText}%"])
                    ->orWhereRaw('JSON_EXTRACT(question, "$.up") LIKE ?', ["%{$questionText}%"])
                    ->first();

                if ($question) {
                    return $question->id;
                }
            }
        }

        return null;
    }

    private function initializeGradingDataFromAssessmentResponse()
    {
        $this->gradingData = [];

        foreach ($this->questions as $index => $question) {
            Log::info('Processing question from AssessmentResponse:', [
                'question_index' => $index,
                'question_id' => $question['id'],
                'question_type' => $question['type'],
                'student_answer' => $question['student_answer'],
                'is_correct' => $question['is_correct'],
                'points_earned' => $question['points_earned'],
            ]);

            $this->gradingData[$index] = [
                'question_id' => $question['id'],
                'question_type' => $question['type'],
                'student_answer' => $question['student_answer'],
                'correct_answer' => $question['answer'],
                'is_correct' => $question['is_correct'],
                'points_possible' => $question['points'],
                'points_earned' => $question['points_earned'],
                'needs_manual_grading' => $question['type'] === 'essay_question' && ! $question['is_graded'],
                'teacher_feedback' => $question['teacher_feedback'] ?? '',
                'is_graded' => $question['is_graded'],
                'graded_by' => $question['graded_by'],
                'graded_at' => $question['graded_at'],
            ];
        }

        Log::info('Grading data initialized from AssessmentResponse:', [
            'total_questions' => count($this->gradingData),
            'graded_questions' => collect($this->gradingData)->where('is_graded', true)->count(),
            'correct_answers' => collect($this->gradingData)->where('is_correct', true)->count(),
        ]);
    }

    // Keep the existing getOnlyAnsweredQuestions method as fallback
    private function getOnlyAnsweredQuestions()
    {
        Log::info('Getting only answered questions from submission (fallback method)', [
            'submission_id' => $this->submissionId,
            'answers_count' => count($this->answers),
        ]);

        if (empty($this->answers)) {
            return [];
        }

        $answeredQuestions = [];

        // Only get questions for which we have answers - use the EXACT keys from answers
        $questionIds = array_keys($this->answers);

        Log::info('Question IDs that have answers:', [
            'question_ids' => $questionIds,
            'count' => count($questionIds),
        ]);

        // Process each question ID individually to maintain exact control
        foreach ($questionIds as $questionId) {
            $question = null;
            $questionType = null;

            // Try to find the question in each table, but only add it ONCE
            $mcQuestion = \App\Models\MultipleChoiceQuestion::find($questionId);
            if ($mcQuestion) {
                $question = $mcQuestion;
                $questionType = 'multiple_choice_question';
                Log::info("Found multiple choice question ID: {$questionId}");
            }

            if (! $question) {
                $tfQuestion = \App\Models\TrueOrFalseQuestion::find($questionId);
                if ($tfQuestion) {
                    $question = $tfQuestion;
                    $questionType = 'true_or_false_question';
                    Log::info("Found true/false question ID: {$questionId}");
                }
            }

            if (! $question) {
                $essayQuestion = \App\Models\EssayQuestion::find($questionId);
                if ($essayQuestion) {
                    $question = $essayQuestion;
                    $questionType = 'essay_question';
                    Log::info("Found essay question ID: {$questionId}");
                }
            }

            // Only add if we found the question
            if ($question && $questionType) {
                $answeredQuestions[] = [
                    'id' => $question->id,
                    'type' => $questionType,
                    'question' => $this->getQuestionText($question),
                    'options' => $this->getQuestionOptions($question, $questionType),
                    'answer' => $this->getCorrectAnswer($question, $questionType),
                    'points' => $question->score ?? 1,
                    'difficulty' => $question->difficulty_level ?? 'medium',
                ];

                Log::info('Successfully added question to display:', [
                    'id' => $question->id,
                    'type' => $questionType,
                    'has_answer' => isset($this->answers[$questionId]),
                    'student_answer' => $this->answers[$questionId] ?? 'null',
                ]);
            } else {
                Log::error('Could not find question in any table:', [
                    'question_id' => $questionId,
                    'submission_id' => $this->submissionId,
                ]);
            }
        }

        Log::info('Final answered questions processed:', [
            'expected_count' => count($questionIds),
            'actual_count' => count($answeredQuestions),
            'question_ids_expected' => $questionIds,
            'question_ids_found' => collect($answeredQuestions)->pluck('id')->toArray(),
        ]);

        return $answeredQuestions;
    }

    private function getQuestionsFromAssessmentResponses()
    {
        // DISABLE THIS METHOD - it's causing the 92 questions issue
        Log::info('Skipping AssessmentResponse - using only answered questions');

        return [];
    }

    private function getOnlyAnsweredQuestionss()
    {
        Log::info('Getting only answered questions from submission', [
            'submission_id' => $this->submissionId,
            'answers_count' => count($this->answers),
        ]);

        if (empty($this->answers)) {
            return [];
        }

        $answeredQuestions = [];

        // Only get questions for which we have answers - use the EXACT keys from answers
        $questionIds = array_keys($this->answers);

        Log::info('Question IDs that have answers:', [
            'question_ids' => $questionIds,
            'count' => count($questionIds),
        ]);

        // Process each question ID individually to maintain exact control
        foreach ($questionIds as $questionId) {
            $question = null;
            $questionType = null;

            // Try to find the question in each table, but only add it ONCE
            $mcQuestion = \App\Models\MultipleChoiceQuestion::find($questionId);
            if ($mcQuestion) {
                $question = $mcQuestion;
                $questionType = 'multiple_choice_question';
                Log::info("Found multiple choice question ID: {$questionId}");
            }

            if (! $question) {
                $tfQuestion = \App\Models\TrueOrFalseQuestion::find($questionId);
                if ($tfQuestion) {
                    $question = $tfQuestion;
                    $questionType = 'true_or_false_question';
                    Log::info("Found true/false question ID: {$questionId}");
                }
            }

            if (! $question) {
                $essayQuestion = \App\Models\EssayQuestion::find($questionId);
                if ($essayQuestion) {
                    $question = $essayQuestion;
                    $questionType = 'essay_question';
                    Log::info("Found essay question ID: {$questionId}");
                }
            }

            // Only add if we found the question
            if ($question && $questionType) {
                $answeredQuestions[] = [
                    'id' => $question->id,
                    'type' => $questionType,
                    'question' => $this->getQuestionText($question),
                    'options' => $this->getQuestionOptions($question, $questionType),
                    'answer' => $this->getCorrectAnswer($question, $questionType),
                    'points' => $question->score ?? 1,
                    'difficulty' => $question->difficulty_level ?? 'medium',
                ];

                Log::info('Successfully added question to display:', [
                    'id' => $question->id,
                    'type' => $questionType,
                    'has_answer' => isset($this->answers[$questionId]),
                    'student_answer' => $this->answers[$questionId] ?? 'null',
                ]);
            } else {
                Log::error('Could not find question in any table:', [
                    'question_id' => $questionId,
                    'submission_id' => $this->submissionId,
                ]);
            }
        }

        Log::info('Final answered questions processed:', [
            'expected_count' => count($questionIds),
            'actual_count' => count($answeredQuestions),
            'question_ids_expected' => $questionIds,
            'question_ids_found' => collect($answeredQuestions)->pluck('id')->toArray(),
        ]);

        return $answeredQuestions;
    }

    private function getQuestionsFromAssessmentResponsess()
    {
        // Find the Assessment record for this submission
        $assessment = Assessment::where('assignment_id', $this->assignment->id)
            ->where('student_id', $this->student->id)
            ->first();

        if (! $assessment) {
            Log::info('No assessment found for submission', [
                'submission_id' => $this->submissionId,
                'assignment_id' => $this->assignment->id,
                'student_id' => $this->student->id,
            ]);

            return [];
        }

        // Try to get questions from assessment_responses table
        $assessmentResponse = AssessmentResponse::where('assessment_id', $assessment->id)->first();

        if ($assessmentResponse) {
            $questionsData = $assessmentResponse->getQuestionsData();

            if (! empty($questionsData)) {
                Log::info('Found questions in AssessmentResponse', [
                    'count' => count($questionsData),
                    'submission_id' => $this->submissionId,
                ]);

                return $this->formatQuestionsFromAssessmentResponse($questionsData);
            }
        }

        // Try to get questions from assessment.questions_data
        if ($assessment->questions_data) {
            Log::info('Found questions in Assessment.questions_data', [
                'count' => count($assessment->questions_data),
                'submission_id' => $this->submissionId,
            ]);

            return $assessment->questions_data;
        }

        return [];
    }

    private function getQuestionText($question)
    {
        // Handle different question text formats
        if (is_array($question->question)) {
            return $question->question['up'] ??
                   $question->question['down'] ??
                   $question->question['summary'] ??
                   $question->question['text'] ??
                   json_encode($question->question);
        } elseif (is_string($question->question)) {
            // Handle JSON string format
            $decoded = json_decode($question->question, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded['up'] ??
                       $decoded['down'] ??
                       $decoded['summary'] ??
                       $decoded['text'] ??
                       $question->question;
            }

            return $question->question;
        } elseif (is_object($question->question)) {
            return $question->question->up ??
                   $question->question->down ??
                   $question->question->summary ??
                   $question->question->text ??
                   'Question object format not recognized';
        }

        return $question->question ?? 'Question text not available';
    }

    private function getQuestionOptions($question, $type)
    {
        if ($type === 'multiple_choice_question') {
            $options = [];

            // Helper function to extract option text
            $extractOptionText = function ($option) {
                if (is_array($option)) {
                    return $option['up'] ?? $option['down'] ?? $option['text'] ?? json_encode($option);
                } elseif (is_string($option)) {
                    $decoded = json_decode($option, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        return $decoded['up'] ?? $decoded['down'] ?? $decoded['text'] ?? $option;
                    }

                    return $option;
                } elseif (is_object($option)) {
                    return $option->up ?? $option->down ?? $option->text ?? 'Option format not recognized';
                }

                return $option;
            };

            // Extract each option
            if ($question->option_a) {
                $options['A'] = $extractOptionText($question->option_a);
            }
            if ($question->option_b) {
                $options['B'] = $extractOptionText($question->option_b);
            }
            if ($question->option_c) {
                $options['C'] = $extractOptionText($question->option_c);
            }
            if ($question->option_d) {
                $options['D'] = $extractOptionText($question->option_d);
            }
            if ($question->option_e) {
                $options['E'] = $extractOptionText($question->option_e);
            }

            return $options;
        }

        return null;
    }

    private function getCorrectAnswer($question, $type)
    {
        return match ($type) {
            'multiple_choice_question' => $question->answer,
            'true_or_false_question' => $question->answer ? 'true' : 'false',
            'essay_question' => null,
            default => null,
        };
    }

    private function initializeGradingData()
    {
        $this->gradingData = [];

        foreach ($this->questions as $index => $question) {
            // Get the student's answer for this specific question ID
            $savedAnswer = $this->answers[$question['id']] ?? null;

            // Handle both old format (direct value) and new format (structured data)
            $studentAnswer = null;
            $answeredAt = null;
            $questionType = $question['type']; // fallback to question type from question data

            if ($savedAnswer !== null) {
                if (is_array($savedAnswer)) {
                    // New format: structured answer data
                    $studentAnswer = $savedAnswer['response'] ?? null;
                    $answeredAt = $savedAnswer['answered_at'] ?? null;
                    $questionType = $savedAnswer['question_type'] ?? $question['type'];
                } else {
                    // Old format: direct answer value
                    $studentAnswer = $savedAnswer;
                }
            }

            Log::info('Processing question for grading:', [
                'question_index' => $index,
                'question_id' => $question['id'],
                'question_type' => $questionType,
                'raw_saved_answer' => $savedAnswer,
                'extracted_student_answer' => $studentAnswer,
                'correct_answer' => $question['answer'] ?? 'N/A',
            ]);

            // Clean up the student answer based on question type
            $cleanedStudentAnswer = $this->cleanStudentAnswer($studentAnswer, $questionType);

            Log::info('Cleaned student answer:', [
                'question_id' => $question['id'],
                'raw_answer' => $studentAnswer,
                'cleaned_answer' => $cleanedStudentAnswer,
            ]);

            $isCorrect = $this->evaluateAnswer($question, $cleanedStudentAnswer);
            $pointsEarned = $this->calculatePoints($question, $cleanedStudentAnswer, $isCorrect);

            $this->gradingData[$index] = [
                'question_id' => $question['id'],
                'question_type' => $questionType,
                'student_answer' => $cleanedStudentAnswer,
                'raw_student_answer' => $studentAnswer, // Keep original for debugging
                'saved_answer_data' => $savedAnswer, // Keep full saved data for debugging
                'correct_answer' => $question['answer'],
                'is_correct' => $isCorrect,
                'points_possible' => $question['points'],
                'points_earned' => $pointsEarned,
                'needs_manual_grading' => $questionType === 'essay_question',
                'teacher_feedback' => '',
                'is_graded' => $questionType !== 'essay_question',
                'answered_at' => $answeredAt,
            ];
        }

        Log::info('Grading data initialized:', [
            'total_questions' => count($this->gradingData),
            'grading_summary' => collect($this->gradingData)->map(function ($data, $index) {
                return [
                    'index' => $index,
                    'question_id' => $data['question_id'],
                    'type' => $data['question_type'],
                    'student_answer' => $data['student_answer'],
                    'raw_answer' => $data['raw_student_answer'],
                    'is_correct' => $data['is_correct'],
                ];
            })->toArray(),
        ]);
    }

    private function cleanStudentAnswer($answer, $questionType)
    {
        if ($answer === null || $answer === '') {
            return null;
        }

        Log::info('Cleaning answer:', [
            'question_type' => $questionType,
            'raw_answer' => $answer,
            'answer_type' => gettype($answer),
        ]);

        switch ($questionType) {
            case 'multiple_choice_question':
                // For multiple choice, the answer should be A, B, C, D, or E
                // But sometimes students' answers are stored as 'true'/'false' for T/F questions
                // that were mistakenly saved as multiple choice
                if (is_string($answer) && strlen($answer) === 1 && ctype_alpha($answer)) {
                    return strtoupper($answer);
                }

                // Handle the case where true/false answers were stored for multiple choice
                // This happens when the question type was mixed up during submission
                if (in_array(strtolower($answer), ['true', 'false'])) {
                    // For now, return as is - the evaluation will handle the mismatch
                    Log::warning('Multiple choice question has true/false answer - possible data issue', [
                        'answer' => $answer,
                        'question_type' => $questionType,
                    ]);

                    return $answer;
                }

                return $answer;

            case 'true_or_false_question':
                // Normalize true/false answers
                if (is_bool($answer)) {
                    return $answer ? 'true' : 'false';
                }
                if (is_string($answer)) {
                    $lower = strtolower(trim($answer));
                    if (in_array($lower, ['true', '1', 'yes', 'on'])) {
                        return 'true';
                    } elseif (in_array($lower, ['false', '0', 'no', 'off'])) {
                        return 'false';
                    }
                    // Return the original if it's already 'true' or 'false'
                    if (in_array($lower, ['true', 'false'])) {
                        return $lower;
                    }
                }

                return $answer;

            case 'essay_question':
                // For essays, just return as string
                return (string) $answer;

            default:
                return $answer;
        }
    }

    private function evaluateAnswer($question, $studentAnswer)
    {
        if ($question['type'] === 'essay_question' || $studentAnswer === null) {
            return null;
        }

        Log::info('Evaluating answer:', [
            'question_id' => $question['id'],
            'question_type' => $question['type'],
            'student_answer' => $studentAnswer,
            'student_answer_type' => gettype($studentAnswer),
            'correct_answer' => $question['answer'],
            'correct_answer_type' => gettype($question['answer']),
        ]);

        $result = match ($question['type']) {
            'multiple_choice_question' => $this->evaluateMultipleChoice($question, $studentAnswer),
            'true_or_false_question' => $this->evaluateTrueFalse($question, $studentAnswer),
            default => false,
        };

        Log::info('Answer evaluation result:', [
            'question_id' => $question['id'],
            'is_correct' => $result,
        ]);

        return $result;
    }

    private function evaluateMultipleChoice($question, $studentAnswer)
    {
        // Handle normal case: student answer is A, B, C, D, E
        if (is_string($studentAnswer) && strlen($studentAnswer) === 1 && ctype_alpha($studentAnswer)) {
            return strtoupper($studentAnswer) === strtoupper($question['answer']);
        }

        // Handle edge case: student answer is 'true'/'false' but question is multiple choice
        // This might indicate a data storage issue, but let's handle it gracefully
        if (in_array(strtolower($studentAnswer), ['true', 'false'])) {
            Log::warning('Multiple choice question answered with true/false', [
                'question_id' => $question['id'],
                'student_answer' => $studentAnswer,
                'correct_answer' => $question['answer'],
            ]);

            // For now, these will be marked incorrect since they don't match A,B,C,D format
            return false;
        }

        // Fallback to string comparison
        return strtoupper((string) $studentAnswer) === strtoupper((string) $question['answer']);
    }

    private function evaluateTrueFalse($question, $studentAnswer)
    {
        $correctAnswer = $question['answer']; // Should be 'true' or 'false'

        // Normalize both answers for comparison
        $normalizedStudentAnswer = strtolower($studentAnswer);
        $normalizedCorrectAnswer = strtolower($correctAnswer);

        return $normalizedStudentAnswer === $normalizedCorrectAnswer;
    }

    private function calculatePoints($question, $studentAnswer, $isCorrect)
    {
        if ($question['type'] === 'essay_question') {
            return 0; // Will be manually graded
        }

        return $isCorrect ? $question['points'] : 0;
    }

    public function openGradingPanel($questionIndex)
    {
        $this->currentEssayIndex = $questionIndex;
        $this->essayGrade = $this->gradingData[$questionIndex]['points_earned'] ?? '';
        $this->essayFeedback = $this->gradingData[$questionIndex]['teacher_feedback'] ?? '';
        $this->showGradingPanel = true;
    }

    public function closeGradingPanel()
    {
        $this->showGradingPanel = false;
        $this->currentEssayIndex = null;
        $this->essayGrade = '';
        $this->essayFeedback = '';
    }

    public function gradeEssayQuestion()
    {
        $this->validate([
            'essayGrade' => 'required|numeric|min:0|max:'.$this->gradingData[$this->currentEssayIndex]['points_possible'],
            'essayFeedback' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            // Update grading data
            $this->gradingData[$this->currentEssayIndex]['points_earned'] = (float) $this->essayGrade;
            $this->gradingData[$this->currentEssayIndex]['teacher_feedback'] = $this->essayFeedback;
            $this->gradingData[$this->currentEssayIndex]['is_graded'] = true;
            $this->gradingData[$this->currentEssayIndex]['needs_manual_grading'] = false;

            // Recalculate total score
            $this->recalculateScore();

            // Update submission status if all essays are graded
            $this->updateSubmissionStatus();

            DB::commit();

            session()->flash('success', 'Essay question graded successfully!');
            $this->closeGradingPanel();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to grade essay question', [
                'error' => $e->getMessage(),
                'submission_id' => $this->submissionId,
                'question_index' => $this->currentEssayIndex,
            ]);

            session()->flash('error', 'Failed to grade essay question. Please try again.');
        }
    }

    private function recalculateScore()
    {
        $totalScore = collect($this->gradingData)->sum('points_earned');
        $maxScore = collect($this->gradingData)->sum('points_possible');

        $this->submission->update([
            'score' => $totalScore,
            'total_marks' => $maxScore,
        ]);

        // Refresh the submission data
        $this->submission = $this->submission->fresh();
    }

    private function updateSubmissionStatus()
    {
        $allGraded = collect($this->gradingData)->every('is_graded', true);

        if ($allGraded && $this->submission->status === 'submitted') {
            $this->submission->update(['status' => 'graded']);
        }
    }

    public function getProgressProperty()
    {
        if (empty($this->gradingData)) {
            return 0;
        }

        $gradedCount = collect($this->gradingData)->where('is_graded', true)->count();
        $totalCount = count($this->gradingData);

        return $totalCount > 0 ? round(($gradedCount / $totalCount) * 100) : 0;
    }

    public function getTotalScoreProperty()
    {
        return collect($this->gradingData)->sum('points_earned');
    }

    public function getMaxScoreProperty()
    {
        return collect($this->gradingData)->sum('points_possible');
    }

    public function getPercentageProperty()
    {
        return $this->maxScore > 0 ? round(($this->totalScore / $this->maxScore) * 100, 2) : 0;
    }

    public function getGradeProperty()
    {
        $percentage = $this->percentage;

        return match (true) {
            $percentage >= 90 => 'A',
            $percentage >= 80 => 'B',
            $percentage >= 70 => 'C',
            $percentage >= 60 => 'D',
            default => 'F'
        };
    }

    public function debugSubmission()
    {
        // This is a temporary debug method - remove in production
        $debugData = [
            'submission_id' => $this->submissionId,
            'answers_raw' => $this->answers,
            'answers_keys' => array_keys($this->answers),
            'answers_count' => count($this->answers),
            'assignment_title' => $this->assignment->title,
            'assignment_questions_config' => $this->assignment->questions,
        ];

        // Check what happens when we query each question type
        $questionIds = array_keys($this->answers);

        $mcQuestions = \App\Models\MultipleChoiceQuestion::whereIn('id', $questionIds)->get();
        $tfQuestions = \App\Models\TrueOrFalseQuestion::whereIn('id', $questionIds)->get();
        $essayQuestions = \App\Models\EssayQuestion::whereIn('id', $questionIds)->get();

        $debugData['database_results'] = [
            'mc_questions_found' => $mcQuestions->count(),
            'mc_questions_ids' => $mcQuestions->pluck('id')->toArray(),
            'tf_questions_found' => $tfQuestions->count(),
            'tf_questions_ids' => $tfQuestions->pluck('id')->toArray(),
            'essay_questions_found' => $essayQuestions->count(),
            'essay_questions_ids' => $essayQuestions->pluck('id')->toArray(),
        ];

        Log::info('DEBUG SUBMISSION DATA:', $debugData);

        // Also return it for immediate viewing
        session()->flash('debug_data', json_encode($debugData, JSON_PRETTY_PRINT));
    }

    public function render()
    {
        return view('livewire.teachers.view-assignment-submission', [
            'submission' => $this->submission,
            'assignment' => $this->assignment,
            'student' => $this->student,
            'questions' => $this->questions,
            'answers' => $this->answers,
            'gradingData' => $this->gradingData,
            'hasEssayQuestions' => $this->hasEssayQuestions,
            'needsGrading' => $this->needsGrading,
        ]);
    }
}
