<?php

namespace App\Livewire\Assessment;

use App\Models\AssessmentResponse;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Models\Assessment;
use App\Models\Assignment;
use App\Models\Student;
use App\Services\AssessmentConfigurationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UnifiedAssessmentComponent extends Component
{
    // Component state
    public $step = 'selection'; // selection, configuration, assessment, results
    public $assessmentMode = 'self'; // 'self' or 'assignment'

    // Subject selection
    public $selectedSubject = null;
    public $selectedTopic = null;
    public $selectedSubtopic = null;
    public $subjects = [];
    public $topics = [];
    public $subtopics = [];

    // Question configuration
    public $questionTypes = [
        'multiple_choice_question' => true,
        'true_or_false_question' => true,
        'essay_question' => false
    ];
    public $questionCount = 10;
    public $difficulty = 'all';
    public $timeLimitMinutes = null;
    public $questionCounts = [];

    // Assessment phase
    public $assessment = null;
    public $questions = [];
    public $responses = [];
    public $currentQuestionIndex = 0;
    public $timeRemaining = null;
    public $assessmentResponse = null;
    public $startTime = null;
    public $isTimerActive = false;

    // Results phase
    public $assessmentResult = null;
    public $performance = [];
    public $gradingComplete = false;

    // Assignment mode
    public $selectedAssignment = null;
    public $availableAssignments = [];

    // Services
    protected SubjectSelectionService $subjectService;
    protected RandomQuestionSelectionService $questionService;
    protected AssessmentConfigurationService $assessmentService;
    protected AssessmentGradingService $gradingService;
    protected StudentAssessmentService $studentAssessmentService;

    protected $rules = [
        'selectedSubject' => 'required_if:assessmentMode,self',
        'questionCount' => 'required_if:assessmentMode,self|integer|min:1|max:50',
        'selectedAssignment' => 'required_if:assessmentMode,assignment',
    ];

    public function boot(
        SubjectSelectionService $subjectService,
        RandomQuestionSelectionService $questionService,
        AssessmentConfigurationService $assessmentService,
        AssessmentGradingService $gradingService,
        StudentAssessmentService $studentAssessmentService
    ) {
        $this->subjectService = $subjectService;
        $this->questionService = $questionService;
        $this->assessmentService = $assessmentService;
        $this->gradingService = $gradingService;
        $this->studentAssessmentService = $studentAssessmentService;
    }

    public function mount($mode = 'self')
    {
        $this->assessmentMode = $mode;
        $this->responses = [];
        $this->loadInitialData();
        $this->validateResponseStructure();
    }

    private function loadInitialData()
    {
        $this->subjects = $this->subjectService->getAvailableSubjects();
        $this->availableAssignments = $this->getAvailableAssignments();

        if (!empty($this->questions)) {
            foreach (array_keys($this->questions) as $index) {
                if (!isset($this->responses[$index])) {
                    $this->responses[$index] = ['answer' => null];
                }
            }
        }
    }

    public function updatedResponses($value, $key)
{
    // Extract question index from key (e.g., '0.answer')
    preg_match('/^(\d+)(?:\.|$)/', $key, $matches);
    $questionIndex = isset($matches[1]) ? (int)$matches[1] : null;

    if ($questionIndex !== null) {
        // Ensure response has correct structure
        if (!isset($this->responses[$questionIndex])) {
            $this->responses[$questionIndex] = ['answer' => null];
        }

        // Update local state
        $responseValue = $value ?? $this->responses[$questionIndex]['answer'] ?? null;

        // Save to database
        $this->handleResponseUpdate($questionIndex, $responseValue);
    }
}

public function isQuestionAnswered($index): bool
{
    $response = $this->responses[$index] ?? null;

    if (is_array($response) && isset($response['answer'])) {
        return $response['answer'] !== null && $response['answer'] !== '';
    }

    return false;
}




public function getAnsweredCount(): int
{
    $count = 0;
    foreach ($this->responses as $response) {
        if (is_array($response) &&
            isset($response['answer']) &&
            $response['answer'] !== null &&
            $response['answer'] !== '') {
            $count++;
        }
    }
    return $count;
}


    public function getCanSubmitProperty(): bool
    {
        return $this->getAnsweredCount() > 1 ;
    }

public function updatedResponsesAnswer($value, $key)
{
    $questionIndex = (int)$key;

    // Ensure we have the correct response structure
    if (!isset($this->responses[$questionIndex])) {
        $this->responses[$questionIndex] = [
            'answer' => null,
            'type' => $this->questions[$questionIndex]['type'] ?? null,
        ];
    }

    // Store the answer with its type information
    $this->responses[$questionIndex] = [
        'answer' => $value,
        'type' => $this->questions[$questionIndex]['type'] ?? null,
        'answered_at' => now()
    ];

    // Save to database
    $this->handleResponseUpdate($questionIndex, $value);
}

/**
 * Handle individual response updates
 */
public function handleResponseUpdate(int $questionIndex, $responseValue = null): void
{
    if (!$this->assessment || !$this->assessmentResponse) {
        Log::warning('Cannot save response - assessment not initialized');
        $this->dispatch('showError');
        return;
    }

    try {
        $question = $this->questions[$questionIndex] ?? null;
        if (!$question) {
            Log::warning("Question not found for index: {$questionIndex}");
            $this->dispatch('showError');
            return;
        }

        // Validate response
        if ($responseValue === null && (!isset($this->responses[$questionIndex]) || $this->responses[$questionIndex]['answer'] === null)) {
            Log::info('No change in response, skipping save');
            return;
        }

        // Format response based on question type
        $formattedResponse = $this->formatResponseForStorage($question, $responseValue);

        // Get current responses data
        $responsesData = $this->assessmentResponse->responses_data ?? [];

        // Update the specific response
        $responsesData[$questionIndex] = [
            'question_id' => $question['id'],
            'question_type' => $question['type'],
            'response' => $formattedResponse,
            'answered_at' => now()->toDateTimeString(),
        ];

        // Save to database
        $this->assessmentResponse->update([
            'responses_data' => $responsesData,
            'updated_at' => now(),
        ]);

        Log::info('Response saved successfully', [
            'question_index' => $questionIndex,
            'question_id' => $question['id'] ?? 'unknown',
            'assessment_id' => $this->assessment->id ?? 'unknown'
        ]);

    } catch (\Exception $e) {
        Log::error('Failed to save response', [
            'question_index' => $questionIndex,
            'error' => $e->getMessage(),
            'assessment_id' => $this->assessment->id ?? 'unknown'
        ]);

        $this->dispatch('showError');
    }
}

    /**
     * Save individual response to database
     */
    private function saveResponse(int $questionIndex, $responseValue): void
    {
        // Check if assessmentResponse exists
        if (!$this->assessmentResponse) {
            Log::warning('Assessment response not found, cannot save response');
            return;
        }

        try {
            $question = $this->questions[$questionIndex] ?? null;
            if (!$question) {
                Log::warning("Question not found for index: {$questionIndex}");
                return;
            }

            // Format response based on question type
            $formattedResponse = $this->formatResponseForStorage($question, $responseValue);

            // Get current responses data
            $responsesData = $this->assessmentResponse->responses_data ?? [];

            // Update the specific response
            $responsesData[$questionIndex] = [
                'question_id' => $question['id'],
                'question_type' => $question['type'],
                'response' => $formattedResponse,
                'answered_at' => now()->toDateTimeString(),
            ];

            // Save to database
            $this->assessmentResponse->update([
                'responses_data' => $responsesData,
                'updated_at' => now(),
            ]);

            Log::info('Response saved successfully', [
                'question_index' => $questionIndex,
                'question_id' => $question['id'],
                'assessment_id' => $this->assessment->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to save response', [
                'question_index' => $questionIndex,
                'error' => $e->getMessage(),
                'assessment_id' => $this->assessment->id ?? 'unknown'
            ]);
        }
    }

    /**
     * Format response for storage based on question type
     */
    private function formatResponseForStorage($question, $responseValue)
    {
        return match ($question['type']) {
            'multiple_choice_question' => [
                'selected_option' => $responseValue,
                'options' => $question['options'] ?? []
            ],
            'true_or_false_question' => [
                'selected_answer' => $responseValue,
                'answer_boolean' => $responseValue === 'true' || $responseValue === true
            ],
            'essay_question' => [
                'essay_text' => $responseValue,
                'word_count' => str_word_count($responseValue ?? ''),
                'character_count' => strlen($responseValue ?? '')
            ],
            default => $responseValue,
        };
    }

    public function updatedSelectedSubject($value)
    {
        $this->selectedTopic = null;
        $this->selectedSubtopic = null;
        $this->topics = [];
        $this->subtopics = [];
        $this->questionCounts = [];

        if ($value) {
            $this->topics = $this->subjectService->getTopicsForSubject($value);
            $this->updateQuestionCounts();
        }
    }

    public function updatedSelectedTopic($value)
    {
        $this->selectedSubtopic = null;
        $this->subtopics = [];

        if ($value) {
            $this->subtopics = $this->subjectService->getSubtopicsForTopic($value);
        }

        $this->updateQuestionCounts();
    }

    public function updatedSelectedSubtopic(): void
    {
        $this->updateQuestionCounts();
    }

    public function updatedQuestionTypes(): void
    {
        $this->updateQuestionCounts();
    }

    public function updatedQuestionCount(): void
    {
        $this->updateQuestionCounts();
    }

    private function updateQuestionCounts(): void
    {
        if ($this->selectedSubject) {
            $config = $this->buildQuestionConfig();
            $this->questionCounts = $this->questionService->getAvailableQuestionCounts($config);
        }
    }

    // Assessment Configuration
    public function proceedToConfiguration(): void
    {
        if ($this->assessmentMode === 'self') {
            $this->validate(['selectedSubject' => 'required']);
        } else {
            $this->validate(['selectedAssignment' => 'required']);
        }

        $this->step = 'configuration';
    }

    public function backToSelection(): void
    {
        $this->step = 'selection';
    }

    public function startAssessment(): void
    {
        try {
            if ($this->assessmentMode === 'self') {
                $this->startSelfAssessment();
            } else {
                $this->startAssignmentAssessment();
            }
        } catch (\Exception $e) {
            Log::error('Failed to start assessment', [
                'error' => $e->getMessage(),
                'mode' => $this->assessmentMode,
                'user_id' => Auth::id()
            ]);

            session()->flash('error', 'Failed to start assessment. Please try again.');
        }
    }

    private function startSelfAssessment(): void
    {
        $this->validate([
            'selectedSubject' => 'required',
            'questionCount' => 'required|integer|min:1|max:50'
        ]);

        $config = $this->buildAssessmentConfig();
        $student = Auth::user()->student;

        if (!$student) {
            session()->flash('error', 'Student profile not found.');
            return;
        }

        DB::beginTransaction();

        try {
            // Create assessment
            $this->assessment = $this->assessmentService->createFromConfiguration($config, $student);

            // Generate questions
            $this->questions = $this->questionService->generateQuestions($this->buildQuestionConfig())->toArray();

            // Initialize responses with proper structure
            $this->responses = [];
            foreach ($this->questions as $index => $question) {
                $this->responses[$index] = [
                    'answer' => null,
                    'type' => $question['type'],
                    'answered_at' => null
                ];
            }

            // Create assessment response record
            $this->assessmentResponse = AssessmentResponse::create([
                'assessment_id' => $this->assessment->id,
                'responses_data' => [],
                'submission_data' => [
                    'start_time' => now()->toDateTimeString(),
                    'questions_count' => count($this->questions),
                    'time_limit' => $this->timeLimitMinutes,
                ],
                'is_graded' => false,
            ]);

            // Set timer
            if ($this->timeLimitMinutes) {
                $this->timeRemaining = $this->timeLimitMinutes * 60;
                $this->isTimerActive = true;
            }

            $this->startTime = now();
            $this->step = 'assessment';

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // Error handling
        }


    }

    private function validateResponseStructure()
{
    foreach ($this->questions as $index => $question) {
        if (!isset($this->responses[$index]) ||
            !is_array($this->responses[$index]) ||
            !array_key_exists('answer', $this->responses[$index])) {

            $this->responses[$index] = [
                'answer' => null,
                'type' => $question['type'],
                'answered_at' => null
            ];
        }
    }
}


    // Add this property to enable watching
    public function getListeners()
    {
        return [
            'updatedResponses' => 'handleResponseUpdate',
        ];
    }

// Add this method for debugging purposes
    public function debugResponses()
    {
        Log::info('Current responses:', $this->responses);
        session()->flash('debug', 'Responses logged to server');
    }

    private function startAssignmentAssessment()
    {
        $assignment = Assignment::find($this->selectedAssignment);

        if (!$assignment || !$this->canStartAssignment($assignment)) {
            session()->flash('error', 'Assignment not available or you are not eligible.');
            return;
        }

        // Create assessment from assignment
        $this->assessment = $this->assessmentService->createFromAssignment($assignment, Auth::user()->student);

        // Generate questions from assignment
        $this->questions = $this->prepareQuestionsFromAssignment($assignment);

        // Initialize responses
        $this->responses = array_fill(0, count($this->questions), null);

        // Set timer from assignment
        if ($assignment->duration_in_minutes) {
            $this->timeRemaining = $assignment->duration_in_minutes * 60;
            $this->isTimerActive = true;
        }

        $this->startTime = now();
        $this->step = 'assessment';

        session()->flash('success', 'Assignment started successfully!');
    }

    // Assessment Navigation
    public function nextQuestion()
    {
        if ($this->currentQuestionIndex < count($this->questions) - 1) {
            $this->currentQuestionIndex++;
            $this->updateResponse($this->currentQuestionIndex, $this->responses[$this->currentQuestionIndex] ?? null);
        }
    }

    public function previousQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
            $this->updateResponse($this->currentQuestionIndex, $this->responses[$this->currentQuestionIndex] ?? null);
        }
    }

    public function goToQuestion($index): void
    {
        if ($index >= 0 && $index < count($this->questions)) {
            $this->currentQuestionIndex = $index;
            $this->updateResponse($this->currentQuestionIndex, $this->responses[$index] ?? null);
        }
    }

    public function updateResponse($questionIndex, $response): void
    {
        $this->responses[$questionIndex] = $response;
    }

    private function initializeAssessmentState()
    {
        $this->questions = $this->assessment->questions_data;
        $this->responses = array_fill(0, count($this->questions), null);
        $this->currentQuestionIndex = 0;
        $this->timeRemaining = $this->assessment->time_limit_minutes * 60;
        $this->startTime = now();
        $this->isTimerActive = true;
        $this->step = 'assessment';
    }


    // Assessment Submission
    /**
     * Submit assessment for grading
     */
    public function submitAssessment(): void
    {
        if (!$this->assessment || !$this->assessmentResponse) {
            session()->flash('error', 'Assessment not found.');
            logError('Assessment is empty');
              return;
        }

        DB::beginTransaction();

        try {
            // Update assessment response with submission data
            $this->assessmentResponse->update([
                'submission_data' => array_merge(
                    $this->assessmentResponse->submission_data ?? [],
                    [
                        'submitted_at' => now()->toDateTimeString(),
                        'time_taken' => $this->startTime ? now()->diffInMinutes($this->startTime) : null,
                        'answered_questions' => $this->getAnsweredCount(),
                        'total_questions' => count($this->questions)
                    ]
                )
            ]);

            // Grade the assessment
            $gradingResult = $this->gradeAssessment();

            // Update assessment status and scores
            $this->assessment->update([
                'status' => $gradingResult['needs_manual_grading'] ?
                    Assessment::STATUS_PENDING_REVIEW :
                    Assessment::STATUS_COMPLETED,
                'end_time' => now(),
                'score' => $gradingResult['total_score'],
                'max_score' => $gradingResult['max_score'],
                'percentage_score' => $gradingResult['percentage'],
                'has_essay_questions' => $gradingResult['needs_manual_grading'],
                'essay_grading_status' => $gradingResult['needs_manual_grading'] ? 'pending' : null,
            ]);

            // Save grading results to assessment response
            $this->assessmentResponse->update([
                'grading_data' => $gradingResult,
                'is_graded' => !$gradingResult['needs_manual_grading'],
                'graded_at' => !$gradingResult['needs_manual_grading'] ? now() : null,
            ]);

            $this->assessmentResult = $gradingResult;
            $this->performance = $this->calculatePerformance();
            $this->gradingComplete = !$gradingResult['needs_manual_grading'];

            DB::commit();

            $this->step = 'results';
            session()->flash('success', 'Assessment submitted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to submit assessment', [
                'error' => $e->getMessage(),
                'assessment_id' => $this->assessment->id
            ]);
            session()->flash('error', 'Failed to submit assessment. Please try again.');
        }
    }

    /**
     * Grade the assessment
     */
    private function gradeAssessment(): array
    {
        $responsesData = $this->assessmentResponse->responses_data ?? [];
        $gradedResponses = [];
        $totalScore = 0;
        $maxScore = 0;
        $needsManualGrading = false;

        foreach ($this->questions as $index => $question) {
            $responseData = $responsesData[$index] ?? null;
            $maxScore += $question['points'] ?? 1;

            if ($responseData && isset($responseData['response'])) {
                $gradedResult = $this->gradeQuestionResponse($question, $responseData['response']);

                if (isset($gradedResult['needs_grading']) && $gradedResult['needs_grading']) {
                    $needsManualGrading = true;
                }

                $totalScore += $gradedResult['score_earned'] ?? 0;
                $gradedResponses[$index] = $gradedResult;
            } else {
                $gradedResponses[$index] = [
                    'is_correct' => false,
                    'score_earned' => 0,
                    'feedback' => 'Question not answered',
                    'question_id' => $question['id'],
                    'question_type' => $question['type']
                ];
            }
        }

        return [
            'total_score' => $totalScore,
            'max_score' => $maxScore,
            'percentage' => $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 2) : 0,
            'answered_questions' => count(array_filter($responsesData)),
            'correct_answers' => count(array_filter($gradedResponses, fn($r) => $r['is_correct'] ?? false)),
            'completion_rate' => count($this->questions) > 0 ?
                round((count(array_filter($responsesData)) / count($this->questions)) * 100, 2) : 0,
            'graded_responses' => $gradedResponses,
            'needs_manual_grading' => $needsManualGrading,
            'graded_at' => now()->toDateTimeString()
        ];
    }

    /**
     * Grade individual question response
     */
    private function gradeQuestionResponse($question, $response): array
    {
        return match ($question['type']) {
            'multiple_choice_question' => $this->gradeMultipleChoiceResponse($question, $response),
            'true_or_false_question' => $this->gradeTrueFalseResponse($question, $response),
            'essay_question' => $this->prepareEssayForGrading($question, $response),
            default => [
                'is_correct' => false,
                'score_earned' => 0,
                'feedback' => 'Unknown question type',
                'question_id' => $question['id'],
                'question_type' => $question['type']
            ],
        };
    }

    private function gradeMultipleChoiceResponse($question, $response): array
    {
        $selectedOption = $response['selected_option'] ?? null;
        $correctAnswer = $question['correct_answer'] ?? null;

        $isCorrect = $selectedOption && $correctAnswer &&
            strtoupper($selectedOption) === strtoupper($correctAnswer);

        return [
            'is_correct' => $isCorrect,
            'score_earned' => $isCorrect ? ($question['points'] ?? 1) : 0,
            'feedback' => $isCorrect ? 'Correct!' : 'Incorrect. The correct answer was ' . $correctAnswer,
            'question_id' => $question['id'],
            'question_type' => $question['type'],
            'selected_option' => $selectedOption,
            'correct_answer' => $correctAnswer
        ];
    }

    private function gradeTrueFalseResponse($question, $response): array
    {
        $selectedAnswer = $response['answer_boolean'] ?? null;
        $correctAnswer = $question['correct_answer'] ?? null;

        $isCorrect = $selectedAnswer === $correctAnswer;

        return [
            'is_correct' => $isCorrect,
            'score_earned' => $isCorrect ? ($question['points'] ?? 1) : 0,
            'feedback' => $isCorrect ? 'Correct!' : 'Incorrect. The correct answer was ' . ($correctAnswer ? 'True' : 'False'),
            'question_id' => $question['id'],
            'question_type' => $question['type'],
            'selected_answer' => $selectedAnswer,
            'correct_answer' => $correctAnswer
        ];
    }

    private function prepareEssayForGrading($question, $response): array
    {
        $essayText = $response['essay_text'] ?? '';

        return [
            'is_correct' => null,
            'score_earned' => 0,
            'needs_grading' => true,
            'feedback' => 'Essay submitted for manual grading',
            'question_id' => $question['id'],
            'question_type' => $question['type'],
            'essay_text' => $essayText,
            'word_count' => $response['word_count'] ?? 0,
            'character_count' => $response['character_count'] ?? 0
        ];
    }

    /**
     * Calculate performance metrics
     */
    private function calculatePerformance(): array
    {
        if (!$this->assessmentResult) {
            return [];
        }

        $data = $this->assessmentResult;

        return [
            'overall_score' => $data['percentage'],
            'grade' => $this->getGradeFromPercentage($data['percentage']),
            'time_taken' => $this->startTime ? now()->diffInMinutes($this->startTime) : 0,
            'questions_answered' => $data['answered_questions'],
            'questions_correct' => $data['correct_answers'],
            'total_questions' => count($this->questions),
            'completion_rate' => $data['completion_rate'],
            'difficulty_breakdown' => $this->getDifficultyBreakdown(),
            'topic_breakdown' => $this->getTopicBreakdown(),
        ];
    }

    // Timer Management
    public function updateTimer()
    {
        if ($this->isTimerActive && $this->timeRemaining > 0) {
            $this->timeRemaining--;

            if ($this->timeRemaining <= 0) {
                $this->submitAssessment();
            }
        }
    }

    // Results and Performance
    public function restartAssessment()
    {
        $this->reset([
            'step',
            'assessment',
            'questions',
            'responses',
            'currentQuestionIndex',
            'timeRemaining',
            'startTime',
            'isTimerActive',
            'assessmentResult',
            'performance',
            'gradingComplete'
        ]);

        $this->step = 'selection';
    }

    public function viewDetailedResults()
    {
        return redirect()->route('student.assessment.results', $this->assessment->id);
    }

    // Helper Methods
    private function buildQuestionConfig(): array
    {
        return [
            'subject_id' => $this->selectedSubject,
            'topic_id' => $this->selectedTopic,
            'subtopic_id' => $this->selectedSubtopic,
            'question_types' => $this->questionTypes,
            'question_count' => $this->questionCount,
            'difficulty' => $this->difficulty,
        ];
    }

    private function buildAssessmentConfig(): array
    {
        return array_merge($this->buildQuestionConfig(), [
            'time_limit_minutes' => $this->timeLimitMinutes,
            'assessment_mode' => $this->assessmentMode,
        ]);
    }

    private function processAssessmentResponses(): array
    {
        $processedQuestions = [];
        $totalScore = 0;
        $maxScore = 0;
        $correctAnswers = 0;
        $answeredQuestions = 0;

        foreach ($this->questions as $index => $question) {
            $response = $this->responses[$index] ?? null;

            // Handle nested response format from frontend
            $responseValue = null;
            if (is_array($response) && isset($response['answer'])) {
                $responseValue = $response['answer'];
            } elseif (is_array($response) && isset($response['response'])) {
                $responseValue = $response['response'];
            } else {
                $responseValue = $response;
            }

            // Check if response is actually provided and not empty
            $isAnswered = $responseValue !== null && $responseValue !== '' && $responseValue !== [];

            if ($isAnswered) {
                $answeredQuestions++;
            }

            $questionScore = $isAnswered ? $this->calculateQuestionScore($question, $responseValue) : 0;
            $questionMaxScore = $question['points'] ?? 1;

            $totalScore += $questionScore;
            $maxScore += $questionMaxScore;

            if ($questionScore == $questionMaxScore && $isAnswered) {
                $correctAnswers++;
            }

            $processedQuestions[] = [
                'question_id' => $question['id'],
                'question_type' => $question['type'],
                'response' => $responseValue,
                'score' => $questionScore,
                'max_score' => $questionMaxScore,
                'is_correct' => $questionScore == $questionMaxScore && $isAnswered,
                'is_answered' => $isAnswered,
            ];
        }

        return [
            'questions' => $processedQuestions,
            'total_score' => $totalScore,
            'max_score' => $maxScore,
            'percentage' => $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 2) : 0,
            'correct_answers' => $correctAnswers,
            'answered_questions' => $answeredQuestions,
            'total_questions' => count($this->questions),
            'completion_rate' => count($this->questions) > 0 ? round(($answeredQuestions / count($this->questions)) * 100, 2) : 0,
        ];
    }

    private function calculateQuestionScore($question, $response): float
    {
        if ($response === null || $response === '' || $response === []) {
            return 0;
        }

        $questionModel = $question['model'];
        $questionType = $question['type'];

        switch ($questionType) {
            case 'multiple_choice_question':
                return $this->gradingService->gradeMultipleChoice($questionModel, $response);

            case 'true_or_false_question':
                return $this->gradingService->gradeTrueFalse($questionModel, $response);

            case 'essay_question':
                // Essay questions need manual grading
                return 0;

            default:
                return 0;
        }
    }

    private function getGradeFromPercentage($percentage): string
    {
        if ($percentage >= 90) return 'A';
        if ($percentage >= 80) return 'B';
        if ($percentage >= 70) return 'C';
        if ($percentage >= 60) return 'D';
        return 'F';
    }

    private function getDifficultyBreakdown(): array
    {
        // Implementation for difficulty analysis
        return [];
    }

    private function getTopicBreakdown(): array
    {
        // Implementation for topic analysis
        return [];
    }

    private function canStartAssignment($assignment): bool
    {
        $student = Auth::user()->student;

        if ($assignment->status !== 'published') {
            return false;
        }

        if ($assignment->starts_at > now() || $assignment->ends_at < now()) {
            return false;
        }

        $eligibleStudents = $assignment->getEligibleStudents();
        if (!$eligibleStudents->contains('id', $student->id)) {
            return false;
        }

        $existingSubmission = $assignment->submissions()
            ->where('student_id', $student->id)
            ->exists();

        return !$existingSubmission;
    }

    private function prepareQuestionsFromAssignment($assignment): array
    {
        $questions = [];

        foreach ($assignment->assignmentSections as $section) {
            $sectionQuestions = $this->assessmentService->generateQuestionsForSection($section, $this->assessment);
            $questions = array_merge($questions, $sectionQuestions->toArray());
        }

        return $questions;
    }

    private function getAvailableAssignments()
    {
        $student = Auth::user()->student;

        return Assignment::where('status', 'published')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>', now())
            ->whereHas('academicGroups', function ($query) use ($student) {
                $academicGroupIds = optional($student->academicGroups)?->pluck('id') ?? [];
                $query->whereIn('academic_groups.id', $academicGroupIds);
            })
            ->orWhereHas('academicLevels', function ($query) use ($student) {
                $query->where('academic_levels.id', $student->academic_level_id);
            })
            ->orWhereHas('students', function ($query) use ($student) {
                $query->where('students.id', $student->id);
            })
            ->with(['academicSubject', 'teacher.user'])
            ->get();
    }

    public function render()
    {
        return view('livewire.assessments.unified-assessments');
    }
}
