<?php

namespace App\Livewire\Assessment;

use App\Enums\Grade;
use App\Models\Assessment;
use App\Models\AssessmentResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class QuizTakingPage extends Component
{
    // Question selection and generation
    public $selectedSubject = null;

    public $selectedTopic = null;

    public $selectedSubtopic = null;

    public $questionTypes = [
        'multiple_choice_question' => true,
        'true_or_false_question' => true,
        'essay_question' => false,
    ];

    public $questionCount = 10;

    public $difficulty = 'all';

    public $timeLimitMinutes = null;

    public $balancedDistribution = false;

    // Subject selection data
    public $subjects = [];

    public $topics = [];

    public $subtopics = [];

    public $questionCounts = [];

    public $questionDistribution = [];

    // Assessment state
    public $step = 'selection'; // selection, taking, results

    public $assessment = null;

    public $questions = [];

    public $responses = [];

    public $currentQuestionIndex = 0;

    public $timeRemaining = null;

    public $startTime = null;

    public $isTimerActive = false;

    public $isSubmitted = false;

    public $results = null;

    // UI state
    public $showResults = false;

    public $showReview = false;

    public $darkMode = false;

    public $previousAssessments = [];

    // Services
    protected RandomQuestionSelectionService $questionService;

    protected SubjectSelectionService $subjectService;

    protected $rules = [
        'selectedSubject' => 'required',
        'questionCount' => 'required|integer|min:1|max:50',
        'responses.*' => 'nullable',
    ];

    public function boot(
        RandomQuestionSelectionService $questionService,
        SubjectSelectionService $subjectService
    ) {
        $this->questionService = $questionService;
        $this->subjectService = $subjectService;
    }

    public function mount()
    {
        $this->darkMode = request()->cookie('theme') === 'dark';
        $this->loadSubjects();
        $this->loadPreviousAssessments();
    }

    // Subject Selection Methods
    public function loadSubjects()
    {
        $this->subjects = $this->subjectService->getAvailableSubjects();
        Log::info('Loaded subjects:', $this->subjects->toArray());
    }

    public function updatedSelectedSubject($value)
    {
        Log::info("Selected subject changed to: {$value}");

        $this->selectedTopic = null;
        $this->selectedSubtopic = null;
        $this->topics = [];
        $this->subtopics = [];
        $this->resetQuestionData();

        if ($value) {
            $this->topics = $this->subjectService->getTopicsForSubject($value);
            Log::info("Topics loaded for subject {$value}:", $this->topics->toArray());
            $this->updateQuestionData();
        }
    }

    public function updatedSelectedTopic($value)
    {
        Log::info("Selected topic changed to: {$value}");

        $this->selectedSubtopic = null;
        $this->subtopics = [];

        if ($value) {
            $this->subtopics = $this->subjectService->getSubtopicsForTopic($value);
            Log::info("Subtopics loaded for topic {$value}:", $this->subtopics->toArray());
        }

        $this->updateQuestionData();
    }

    public function updatedSelectedSubtopic()
    {
        Log::info("Selected subtopic changed to: {$this->selectedSubtopic}");
        $this->updateQuestionData();
    }

    public function updatedQuestionTypes()
    {
        $this->updateQuestionData();
    }

    public function updatedQuestionCount()
    {
        $this->updateQuestionData();
    }

    public function updatedDifficulty()
    {
        $this->updateQuestionData();
    }

    protected function updateQuestionData()
    {
        if (! $this->selectedSubject) {
            $this->resetQuestionData();

            return;
        }

        $config = $this->buildQuestionConfig();
        Log::info('Updating question data with config:', $config);

        $this->questionCounts = $this->questionService->getAvailableQuestionCounts($config);
        $this->questionDistribution = $this->questionService->getQuestionDistribution($config);

        Log::info('Question counts:', $this->questionCounts);
        Log::info('Question distribution:', $this->questionDistribution);
    }

    protected function resetQuestionData()
    {
        $this->questionCounts = [];
        $this->questionDistribution = [];
    }

    protected function buildQuestionConfig(): array
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

    // Assessment Generation and Start
    public function startAssessment()
    {
        $this->validate([
            'selectedSubject' => 'required',
            'questionCount' => 'required|integer|min:1|max:50',
        ]);

        try {
            // Generate questions using the service
            $config = $this->buildQuestionConfig();
            Log::info('Starting assessment with config:', $config);

            $generatedQuestions = $this->balancedDistribution
                ? $this->questionService->generateBalancedQuestions($config)
                : $this->questionService->generateQuestions($config);

            if ($generatedQuestions->isEmpty()) {
                session()->flash('error', 'No questions available for the selected criteria. Please adjust your selection.');

                return;
            }

            // Format questions for display
            $this->questions = $this->questionService->formatQuestionsForAssessment($generatedQuestions)->toArray();
            Log::info('Generated {'.count($this->questions).'} questions');

            // Create assessment record
            $this->createAssessment();

            // Initialize responses
            $this->initializeResponses();

            // Set timer if specified
            if ($this->timeLimitMinutes) {
                $this->timeRemaining = $this->timeLimitMinutes * 60;
                $this->isTimerActive = true;
            }

            $this->startTime = now();
            $this->step = 'taking';

            session()->flash('success', 'Assessment started with '.count($this->questions).' questions!');

        } catch (\Exception $e) {
            Log::error('Failed to start assessment', [
                'error' => $e->getMessage(),
                'config' => $config ?? null,
                'user_id' => Auth::id(),
            ]);

            session()->flash('error', 'Failed to start assessment. Please try again.');
        }
    }

    private function createAssessment()
    {
        $student = Auth::user()->student;

        if (! $student) {
            throw new \Exception('Student profile not found.');
        }

        $subjectHierarchy = $this->subjectService->getSelectionHierarchy(
            $this->selectedSubject,
            $this->selectedTopic,
            $this->selectedSubtopic
        );

        $this->assessment = Assessment::create([
            'student_id' => $student->id,
            'subject_id' => $this->selectedSubject,
            'topic_id' => $this->selectedTopic,
            'subtopic_id' => $this->selectedSubtopic,
            'title' => $this->generateAssessmentTitle($subjectHierarchy),
            'type' => 'self',
            'status' => 'in_progress',
            'start_time' => now(),
            'questions_data' => $this->questions,
            'time_limit_minutes' => $this->timeLimitMinutes,
            'max_score' => array_sum(array_column($this->questions, 'points')),
        ]);

        Log::info('Assessment created:', ['id' => $this->assessment->id]);
    }

    private function generateAssessmentTitle($hierarchy): string
    {
        $parts = ['Self Assessment'];

        if (isset($hierarchy['subject'])) {
            $parts[] = $hierarchy['subject']['name'];
        }

        if (isset($hierarchy['topic'])) {
            $parts[] = $hierarchy['topic']['name'];
        }

        if (isset($hierarchy['subtopic'])) {
            $parts[] = $hierarchy['subtopic']['name'];
        }

        return implode(' - ', $parts);
    }

    private function initializeResponses()
    {
        $this->responses = array_fill(0, count($this->questions), null);
    }

    // Assessment Navigation
    public function nextQuestion()
    {
        if ($this->currentQuestionIndex < count($this->questions) - 1) {
            $this->currentQuestionIndex++;
        }
    }

    public function previousQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        }
    }

    public function goToQuestion($index)
    {
        if ($index >= 0 && $index < count($this->questions)) {
            $this->currentQuestionIndex = $index;
        }
    }

    public function isQuestionAnswered($index)
    {
        $response = $this->responses[$index] ?? null;

        return $response !== null && $response !== '';
    }

    public function getAnsweredCount()
    {
        return count(array_filter($this->responses, function ($response) {
            return $response !== null && $response !== '';
        }));
    }

    public function getProgress()
    {
        $total = count($this->questions);
        $answered = $this->getAnsweredCount();

        return $total > 0 ? round(($answered / $total) * 100) : 0;
    }

    // Assessment Submission and Grading
    public function submitAssessment()
    {
        if ($this->isSubmitted) {
            return;
        }

        DB::beginTransaction();

        try {
            // Create or update assessment response
            $assessmentResponse = $this->createOrUpdateAssessmentResponse();

            // Grade the responses
            $results = $this->gradeResponses();

            // Update assessment response with results
            $assessmentResponse->update([
                'grading_data' => $results,
                'is_graded' => ! $results['needs_manual_grading'],
                'graded_at' => ! $results['needs_manual_grading'] ? now() : null,
            ]);

            // Update assessment status
            $this->assessment->update([
                'status' => 'completed',
                'end_time' => now(),
                'score' => $results['total_score'],
                'max_score' => $results['max_score'],
                'percentage_score' => $results['percentage'],
            ]);

            $this->results = $results;
            $this->isSubmitted = true;
            $this->isTimerActive = false;
            $this->step = 'results';

            DB::commit();

            session()->flash('success', 'Assessment submitted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to submit assessment', [
                'error' => $e->getMessage(),
                'assessment_id' => $this->assessment->id ?? null,
                'user_id' => Auth::id(),
            ]);

            session()->flash('error', 'Failed to submit assessment. Please try again.');
        }
    }

    private function createOrUpdateAssessmentResponse()
    {
        return AssessmentResponse::updateOrCreate(
            ['assessment_id' => $this->assessment->id],
            [
                'responses_data' => $this->formatResponsesForStorage(),
                'submission_data' => [
                    'submitted_at' => now()->toDateTimeString(),
                    'time_taken' => $this->startTime ? now()->diffInMinutes($this->startTime) : null,
                    'answered_questions' => $this->getAnsweredCount(),
                    'total_questions' => count($this->questions),
                ],
            ]
        );
    }

    private function formatResponsesForStorage()
    {
        $formattedResponses = [];

        foreach ($this->questions as $index => $question) {
            $response = $this->responses[$index] ?? null;

            if ($response !== null) {
                $formattedResponses[$index] = [
                    'question_id' => $question['id'],
                    'question_type' => $question['type'],
                    'response' => $this->formatSingleResponse($question, $response),
                    'answered_at' => now()->toDateTimeString(),
                ];
            }
        }

        return $formattedResponses;
    }

    private function formatSingleResponse($question, $response): array
    {
        return match ($question['type']) {
            'multiple_choice_question' => [
                'selected_option' => $response,
                'options' => $question['options'] ?? [],
                'answer' => $question['answer'] ?? null,
            ],
            'true_or_false_question' => [
                'selected_answer' => filter_var($response, FILTER_VALIDATE_BOOLEAN),
                'answer_boolean' => $response === ('True' || 'true'),
            ],
            'essay_question' => [
                'essay_text' => $response,
                'word_count' => str_word_count($response ?? ''),
                'character_count' => strlen($response ?? ''),
            ],
            default => $response,
        };
    }

    private function gradeResponses()
    {

        $totalScore = 0;
        $maxScore = 0;
        $correctAnswers = 0;
        $needsManualGrading = false;
        $gradedResponses = [];

        foreach ($this->questions as $index => $question) {
            $response = $this->responses[$index] ?? null;
            $questionMaxScore = $question['points'] ?? 1;
            $maxScore += $questionMaxScore;

            if (! empty($response)) {
                $gradeResult = $this->gradeQuestionResponse($question, $response);

                if ($gradeResult['needs_manual_grading'] ?? false) {
                    $needsManualGrading = true;
                } else {
                    $totalScore += $gradeResult['score_earned'];
                    if ($gradeResult['is_correct']) {
                        $correctAnswers++;
                    }
                }

                $gradedResponses[$index] = $gradeResult;
            } else {
                $gradedResponses[$index] = [
                    'is_correct' => false,
                    'score_earned' => 0,
                    'feedback' => 'Question not answered',
                    'question_id' => $question['id'],
                    'question_type' => $question['type'],
                ];
            }
        }

        return [
            'total_score' => $totalScore,
            'max_score' => $maxScore,
            'percentage' => $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 2) : 0,
            'correct_answers' => $correctAnswers,
            'answered_questions' => $this->getAnsweredCount(),
            'total_questions' => count($this->questions),
            'completion_rate' => count($this->questions) > 0 ?
                round(($this->getAnsweredCount() / count($this->questions)) * 100, 2) : 0,
            'graded_responses' => $gradedResponses,
            'needs_manual_grading' => $needsManualGrading,
            'graded_at' => now()->toDateTimeString(),
        ];
    }

    private function gradeQuestionResponse($question, $response)
    {

        return match ($question['type']) {
            'multiple_choice_question' => $this->gradeMultipleChoice($question, $response),
            'true_or_false_question' => $this->gradeTrueFalse($question, $response),
            'essay_question' => $this->prepareEssayForGrading($question, $response),
            default => [
                'is_correct' => false,
                'score_earned' => 0,
                'feedback' => 'Unknown question type',
                'question_id' => $question['id'],
                'question_type' => $question['type'],
            ],
        };
    }

    private function gradeMultipleChoice($question, $response)
    {
        Log::warning('Grading multiple choice question', [$question, $response]);

        $correctAnswer = $question['answer'];
        $isCorrect = strtoupper($response) === strtoupper($correctAnswer);

        return [
            'is_correct' => $isCorrect,
            'score_earned' => $isCorrect ? ($question['points'] ?? 1) : 0,
            'feedback' => $isCorrect ? 'Correct!' : "Incorrect. The correct answer was {$correctAnswer}",
            'question_id' => $question['id'],
            'question_type' => $question['type'],
            'selected_option' => $response,
            'correct_answer' => $correctAnswer,
        ];
    }

    private function gradeTrueFalse($question, $response)
    {
        $correctAnswer = filter_var($question['answer'], FILTER_VALIDATE_BOOLEAN);
        $responseBoolean = $response === 'true' || $response === 'True';
        $isCorrect = $responseBoolean === $correctAnswer;

        return [
            'is_correct' => $isCorrect,
            'score_earned' => $isCorrect ? ($question['points'] ?? 1) : 0,
            'feedback' => $isCorrect ? 'Correct!' : 'Incorrect. The correct answer was '.($correctAnswer ? 'True' : 'False'),
            'question_id' => $question['id'],
            'question_type' => $question['type'],
            'selected_answer' => $response,
            'correct_answer' => $correctAnswer,
        ];
    }

    private function prepareEssayForGrading($question, $response)
    {
        return [
            'is_correct' => null,
            'score_earned' => 0,
            'needs_manual_grading' => true,
            'feedback' => 'Essay submitted for manual grading',
            'question_id' => $question['id'],
            'question_type' => $question['type'],
            'essay_text' => $response,
            'word_count' => str_word_count($response ?? ''),
            'character_count' => strlen($response ?? ''),
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

    // Results Management
    public function toggleReview()
    {
        $this->showReview = ! $this->showReview;
    }

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
            'isSubmitted',
            'results',
            'showReview',
        ]);

        $this->step = 'selection';
        $this->loadSubjects();
        $this->loadPreviousAssessments();
    }

    public function backToSelection()
    {
        $this->step = 'selection';
    }

    public function getGrade($percentage)
    {
        return Grade::fromPercentage($percentage);
    }

    // Debug method for testing
    public function debugQuestionData()
    {
        $config = $this->buildQuestionConfig();
        $debug = $this->questionService->debugQuestionData($config);

        Log::info('Debug question data:', $debug);
        session()->flash('success', 'Debug data logged. Check application logs.');
    }

    public function loadPreviousAssessments(): void
    {
        $student = Auth::user()->student;

        if ($student) {
            $this->previousAssessments = Assessment::where('student_id', $student->id)
                ->where('status', 'completed')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->toArray();
        }
    }

    public function render()
    {
        return view('livewire.assessments.quiz-taking-page');
    }
}
