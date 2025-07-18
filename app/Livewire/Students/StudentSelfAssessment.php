<?php

namespace App\Livewire\Students;

use App\Models\AcademicSubject;
use App\Models\AcademicTopic;
use App\Models\AcademicSubtopic;
use App\Models\Assessment;
use App\Models\AssessmentResponse;
use App\Models\EssayQuestion;
use App\Models\MultipleChoiceQuestion;
use App\Models\TrueOrFalseQuestion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Carbon\Carbon;

class StudentSelfAssessment extends Component
{
    // Assessment configuration
    public $selectedSubject = null;
    public $selectedTopic = null;
    public $selectedSubtopic = null;
    public $questionTypes = [
        'multiple_choice_question' => true,
        'true_or_false_question' => true,
        'essay_question' => false
    ];
    public $questionCount = 10;
    public $timeLimitMinutes = 30;
    public $difficulty = 'all';

    // Assessment state
    public $step = 'configuration'; // configuration, assessment, results
    public $currentQuestionIndex = 0;
    public $questions = [];
    public $responses = [];
    public $assessment = null;
    public $startTime = null;
    public $timeRemaining = null;
    public $isSubmitting = false;
    public $showConfirmSubmit = false;
    public $showReview = false;
    public $showSettings = false;
    public $showHelp = false;
    public $showFeedback = false;
    public $feedbackText = '';
    public $saveProgress = false;

    // Data collections
    public $subjects = [];
    public $topics = [];
    public $subtopics = [];
    public $results = null;
    public $performance = null;
    public $customErrors = [];

    // Progress tracking
    public $answeredQuestions = [];
    public $completedCount = 0;
    public $progressPercentage = 0;

    protected $rules = [
        'selectedSubject' => 'required|exists:academic_subjects,id',
        'questionCount' => 'required|integer|min:1|max:50',
        'timeLimitMinutes' => 'required|integer|min:5|max:180',
        'questionTypes' => 'required|array',
        'questionTypes.*' => 'boolean',
        'feedbackText' => 'required|string|max:1000'
    ];

    protected $listeners = [
        'timeUp' => 'handleTimeUp',
        'saveProgressData' => 'saveProgressData'
    ];

    public function mount()
    {
        logInfo('Component mounted successfully');

        $this->loadStudentSubjects();
        $this->initializeQuestionTypes();
        $this->logActivity('accessed_self_assessment');
    }

    protected function handleError(\Exception $e, string $method)
    {
        Log::error("Error in {$method}", [
            'component' => static::class,
            'method' => $method,
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'user_id' => auth()->id(),
            'request_data' => request()->all(),
            'component_state' => [
                'step' => $this->step,
                'selectedSubject' => $this->selectedSubject,
                'questionCount' => $this->questionCount,
            ]
        ]);
    }

    public function loadStudentSubjects()
    {
        $student = auth()->user()->student;

        if (!$student) {
            $this->subjects = collect();
            return;
        }

        $this->subjects = collect();

        // Load subjects from student's academic level
        if ($student->academicLevel) {
            $levelSubjects = $student->academicLevel->academicSubjects()
                ->with('academicLevel')
                ->get();
            $this->subjects = $this->subjects->merge($levelSubjects);
        }

        // Load individual subjects
        $individualSubjects = $student->individualSubjects()
//            ->wherePivot('is_active', true)
            ->with('academicLevel')
            ->get();

        foreach ($individualSubjects as $subject) {
            if (!$this->subjects->contains('id', $subject->id)) {
                $this->subjects->push($subject);
            }
        }

        // Remove deactivated subjects
        $removedSubjects = $student->individualSubjects()
            ->wherePivot('is_active', 'true')
            ->pluck('academic_subjects.id');

        //$this->subjects = $this->subjects->reject(function ($subject) use ($removedSubjects) {
         //   return $removedSubjects->contains($subject->id);
       // });

        // Fallback to all subjects if no specific subjects found
        if ($this->subjects->isEmpty()) {
            $this->subjects = AcademicSubject::all();
        }
    }

    public function initializeQuestionTypes()
    {
        $this->questionTypes = [
            'multiple_choice_question' => true,
            'true_or_false_question' => true,
            'essay_question' => false
        ];
    }

    public function updatedSelectedSubject($value)
    {
        $this->selectedTopic = null;
        $this->selectedSubtopic = null;
        $this->topics = [];
        $this->subtopics = [];

        if ($value) {
            $this->topics = AcademicTopic::where('academic_subject_id', $value)
                ->orderBy('name')
                ->get();
        }
    }

    public function updatedSelectedTopic($value)
    {
        $this->selectedSubtopic = null;
        $this->subtopics = [];

        if ($value) {
            $this->subtopics = AcademicSubtopic::where('academic_topic_id', $value)
                ->orderBy('name')
                ->get();
        }
    }

    public function updatedQuestionTypes()
    {
        $this->validateQuestionTypes();
    }

    public function validateQuestionTypes()
    {
        $selectedTypes = array_filter($this->questionTypes);

        if (empty($selectedTypes)) {
            $this->addError('questionTypes', 'You must select at least one question type.');
            return false;
        }

        $this->clearError('questionTypes');
        return true;
    }

    public function startAssessment()
    {
        logInfo('start assessment');
//        $this->validate();

        if (!$this->validateQuestionTypes()) {
            logInfo('Validation failed for question types', [
                //'errors' => $this->getErrorBag()->toArray()
            ]);
            return;
        }

        try {
            $this->generateQuestions();

            if (empty($this->questions)) {
                $this->addError('questions', 'No questions found for the selected criteria. Please adjust your selection.');
                return;
            }

            $this->createAssessment();
            $this->initializeAssessmentState();
            $this->step = 'assessment';
            $this->logActivity('started_assessment');

        } catch (\Exception $e) {
            $this->handleError($e, __METHOD__);;
            $this->addError('assessment', 'Failed to start assessment: ' . $e->getMessage());
        }
    }

    public function generateQuestions()
    {
        $selectedTypes = array_keys(array_filter($this->questionTypes));
        $questionsPerType = $this->distributeQuestions($selectedTypes);
        $this->questions = [];

        // Use array_push instead of array_merge in loop for better performance
        foreach ($selectedTypes as $type) {
            $count = $questionsPerType[$type];
            $typeQuestions = $this->getQuestionsByType($type, $count);
            array_push($this->questions, ...$typeQuestions);
        }

        // Shuffle questions for random order
        shuffle($this->questions);

        // Add question indices - remove the 'formatted' key that causes serialization issues
        $this->questions = array_map(function ($question, $index) {
            return array_merge($question, [
                'index' => $index,
            // Remove this line that causes serialization issues:
            // 'formatted' => $this->formatQuestion($question)
        ]);
    }, $this->questions, array_keys($this->questions));
}

    public function distributeQuestions($selectedTypes)
    {
        $typeCount = count($selectedTypes);
        $baseCount = intval($this->questionCount / $typeCount);
        $remainder = $this->questionCount % $typeCount;

        $distribution = [];
        foreach ($selectedTypes as $i => $type) {
            $distribution[$type] = $baseCount + ($i < $remainder ? 1 : 0);
        }

        return $distribution;
    }

    public function getQuestionsByType($type, $count)
    {
        $query = $this->buildQuestionQuery($type);

        return $query->inRandomOrder()
            ->limit($count)
            ->get()
            ->map(function ($question) use ($type) {
                return $this->formatQuestionData($question, $type);
            })
            ->toArray();
    }

    public function buildQuestionQuery($type)
    {
        $modelClass = $this->getQuestionModel($type);
        $query = $modelClass::query();

        // Filter by subtopic, topic, or subject
        if ($this->selectedSubtopic) {
            $query->where('academic_subtopic_id', $this->selectedSubtopic);
        } elseif ($this->selectedTopic) {
            $query->whereHas('subtopic', function ($q) {
                $q->where('academic_topic_id', $this->selectedTopic);
            });
        } else {
            $query->whereHas('subtopic.academicTopic', function ($q) {
//                $q->where('academic_subject_id', $this->selectedSubject);
            });
        }

        // Filter by difficulty if specified
        if ($this->difficulty !== 'all') {
            $query->where('difficulty_level', $this->difficulty);
        }

        return $query;
    }

    public function getQuestionModel($type)
    {
        $models = [
            'multiple_choice_question' => MultipleChoiceQuestion::class,
            'true_or_false_question' => TrueOrFalseQuestion::class,
            'essay_question' => EssayQuestion::class
        ];

        return $models[$type];
    }

    public function formatQuestionData($question, $type)
    {
        $formatted = $question->getQuestion();

        return [
            'id' => $question->id,
            'type' => $type,
            'question' => $formatted['question'],
            'options' => $formatted['options'] ?? [],
            'correct_answer' => $formatted['answer'],
            'score' => $question->score ?? 1,
            'difficulty' => $question->difficulty_level ?? 'medium',
            // Remove this line that stores complex objects:
            // 'model' => $question->toArray()

            // Instead, store only the essential data you need:
            'question_id' => $question->id,
            'subtopic_id' => $question->academic_subtopic_id ?? null,
            'topic_id' => $question->academic_topic_id ?? null,
        ];
    }

// Create a method to get formatted question data when needed (not stored in component state)
public function getFormattedQuestion($questionIndex)
{
    if (!isset($this->questions[$questionIndex])) {
        return null;
    }

    $question = $this->questions[$questionIndex];


    // Format the question for display
    $formatted = [
        'display_question' => $this->cleanHtml($question['question']),
        'display_options' => []
    ];

    if ($question['type'] === 'multiple_choice_question') {
        foreach ($question['options'] as $key => $option) {
            if (!empty($option)) {
                $formatted['options'][$key] = $this->cleanHtml($option);
            }
        }
    } elseif ($question['type'] === 'true_or_false_question') {
        $formatted['options'] = [
            'true' => 'True',
            'false' => 'False'
        ];
    }

    return $formatted;
}

// Add this method to safely clean HTML
private function cleanHtml($text)
{
    if (is_string($text)) {
        return strip_tags($text);
    }

    if (is_array($text)) {
        return strip_tags($text['down'] ?? $text['up'] ?? '');
    }

    if (is_object($text)) {
        return strip_tags($text->down ?? $text->up ?? '');
    }

    return '';
}

    public function formatQuestion($question)
    {
        // Additional formatting for display
        $formatted = [
            'question' => $this->cleanHtml($question['question']),
            'options' => []
        ];

        if ($question['type'] === 'multiple_choice_question') {
            foreach ($question['options'] as $key => $option) {
                if (!empty($option)) {
                    $formatted['options'][$key] = $this->cleanHtml($option);
                }
            }
        } elseif ($question['type'] === 'true_or_false_question') {
            $formatted['options'] = [
                'true' => 'True',
                'false' => 'False'
            ];
        }

        return $formatted;
    }

    public function createAssessment()
    {
        $this->assessment = Assessment::create([
            'student_id' => auth()->user()->student->id,
            'subject_id' => $this->selectedSubject,
            'topic_id' => $this->selectedTopic,
            'subtopic_id' => $this->selectedSubtopic,
            'title' => 'Self Assessment - ' . $this->getSubjectName(),
            'type' => Assessment::TYPE_SELF,
            'question_types' => array_keys(array_filter($this->questionTypes)),
            'max_score' => collect($this->questions)->sum('score'),
            'time_limit_minutes' => $this->timeLimitMinutes,
            'status' => Assessment::STATUS_IN_PROGRESS,
            'has_essay_questions' => in_array('essay_question', array_keys(array_filter($this->questionTypes))),
            'start_time' => now(),
            'questions_data' => json_encode($this->questions) // JSON encode the data
        ]);
    }

    public function initializeAssessmentState()
    {
        $this->responses = array_fill(0, count($this->questions), null);
        $this->answeredQuestions = array_fill(0, count($this->questions), false);
        $this->completedCount = 0;
        $this->progressPercentage = 0;
        $this->startTime = now();
        $this->timeRemaining = $this->timeLimitMinutes * 60;
        $this->currentQuestionIndex = 0;
    }

    public function navigateToQuestion($index)
    {
        if ($index >= 0 && $index < count($this->questions)) {
            $this->currentQuestionIndex = $index;
        }
    }

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

    public function answerQuestion($answer)
    {
        $this->responses[$this->currentQuestionIndex] = $answer;

        if (!$this->answeredQuestions[$this->currentQuestionIndex]) {
            $this->answeredQuestions[$this->currentQuestionIndex] = true;
            $this->completedCount++;
        }

        $this->updateProgress();

        if ($this->saveProgress) {
            $this->saveProgressData();
        }
    }

    public function updateProgress()
    {
        $this->progressPercentage = ($this->completedCount / count($this->questions)) * 100;
    }

    public function saveProgressData()
    {
        if ($this->assessment) {
            $this->assessment->update([
                'questions_data' => [
                    'questions' => $this->questions,
                    'responses' => $this->responses,
                    'current_index' => $this->currentQuestionIndex,
                    'completed_count' => $this->completedCount,
                    'last_saved' => now()
                ]
            ]);
        }
    }

    public function confirmSubmit()
    {
        $this->showConfirmSubmit = true;
    }

    public function cancelSubmit()
    {
        $this->showConfirmSubmit = false;
    }

    public function submitAssessment()
    {
        $this->isSubmitting = true;
        $this->showConfirmSubmit = false;

        try {
            $this->processResponses();
            $this->calculateResults();
            $this->saveAssessmentResponse();
            $this->updateAssessmentStatus();
            $this->step = 'results';
            $this->logActivity('submitted_assessment');

        } catch (\Exception $e) {
            $this->addError('submit', 'Failed to submit assessment: ' . $e->getMessage());
        } finally {
            $this->isSubmitting = false;
        }
    }

    public function processResponses()
    {
        $processedQuestions = [];

        foreach ($this->questions as $index => $question) {
            $response = $this->responses[$index] ?? null;
            $isCorrect = $this->checkAnswer($question, $response);

            $processedQuestions[] = [
                'question_data' => $question,
                'student_answer' => $response,
                'correct_answer' => $question['correct_answer'],
                'is_correct' => $isCorrect,
                'score_earned' => $isCorrect ? $question['score'] : 0,
                'max_score' => $question['score'],
                'type' => $question['type'],
                'is_graded' => $question['type'] !== 'essay_question'
            ];
        }

        $this->questions = $processedQuestions;
    }

    public function checkAnswer($question, $response)
    {
        if ($response === null) {
            return false;
        }

        if ($question['type'] === 'essay_question') {
            return null; // Essay questions need manual grading
        }

        if ($question['type'] === 'true_or_false_question') {
            return (bool)$response === (bool)$question['correct_answer'];
        }

        if ($question['type'] === 'multiple_choice_question') {
            return strtolower($response) === strtolower($question['correct_answer']);
        }

        return false;
    }

    public function calculateResults()
    {
        $totalQuestions = count($this->questions);
        $answeredQuestions = count(array_filter($this->responses, fn($r) => $r !== null));
        $correctAnswers = 0;
        $totalScore = 0;
        $maxScore = 0;
        $typeResults = [];

        foreach ($this->questions as $question) {
            $maxScore += $question['max_score'];

            if ($question['is_correct'] === true) {
                $correctAnswers++;
                $totalScore += $question['score_earned'];
            }

            $type = $question['type'];
            if (!isset($typeResults[$type])) {
                $typeResults[$type] = [
                    'total' => 0,
                    'correct' => 0,
                    'score' => 0,
                    'max_score' => 0
                ];
            }

            $typeResults[$type]['total']++;
            $typeResults[$type]['max_score'] += $question['max_score'];

            if ($question['is_correct'] === true) {
                $typeResults[$type]['correct']++;
                $typeResults[$type]['score'] += $question['score_earned'];
            }
        }

        $this->results = [
            'total_questions' => $totalQuestions,
            'answered_questions' => $answeredQuestions,
            'correct_answers' => $correctAnswers,
            'total_score' => $totalScore,
            'max_score' => $maxScore,
            'percentage' => $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0,
            'time_taken' => now()->diffInSeconds($this->startTime),
            'type_results' => $typeResults,
            'grade' => $this->calculateGrade($totalScore, $maxScore)
        ];
    }

    public function calculateGrade($score, $maxScore)
    {
        if ($maxScore == 0) return 'N/A';

        $percentage = ($score / $maxScore) * 100;

        if ($percentage >= 90) return 'A';
        if ($percentage >= 80) return 'B';
        if ($percentage >= 70) return 'C';
        if ($percentage >= 60) return 'D';
        return 'F';
    }

    public function saveAssessmentResponse()
    {
        $responseData = [
            'questions' => $this->questions,
            'total_questions' => $this->results['total_questions'],
            'answered_questions' => $this->results['answered_questions'],
            'correct_answers' => $this->results['correct_answers'],
            'total_score' => $this->results['total_score'],
            'max_score' => $this->results['max_score'],
            'percentage' => $this->results['percentage'],
            'time_taken' => $this->results['time_taken'],
            'type_results' => $this->results['type_results'],
            'subject_id' => $this->selectedSubject,
            'topic_id' => $this->selectedTopic,
            'subtopic_id' => $this->selectedSubtopic,
            'completed_at' => now()
        ];

        AssessmentResponse::create([
            'assessment_id' => $this->assessment->id,
            'data' => $responseData
        ]);
    }

    public function updateAssessmentStatus()
    {
        $status = $this->assessment->canAutoGrade()
            ? Assessment::STATUS_COMPLETED
            : Assessment::STATUS_PENDING_REVIEW;

        $this->assessment->update([
            'status' => $status,
            'end_time' => now(),
            'score' => $this->results['total_score'],
            'max_score' => $this->results['max_score'],
            'percentage_score' => $this->results['percentage']
        ]);
    }

    public function retakeAssessment()
    {
        $this->reset([
            'step', 'currentQuestionIndex', 'questions', 'responses',
            'assessment', 'startTime', 'timeRemaining', 'results',
            'answeredQuestions', 'completedCount', 'progressPercentage'
        ]);

        $this->step = 'configuration';
        $this->logActivity('retake_assessment');
    }

    public function toggleReview()
    {
        $this->showReview = !$this->showReview;
    }

    public function toggleSettings()
    {
        $this->showSettings = !$this->showSettings;
    }

    public function toggleHelp()
    {
        $this->showHelp = !$this->showHelp;
    }

    public function toggleFeedback()
    {
        $this->showFeedback = !$this->showFeedback;
    }

    public function submitFeedback()
    {
        $this->validate(['feedbackText' => 'required|string|max:1000']);

        // Save feedback logic here
        $this->logActivity('submitted_feedback', ['feedback' => $this->feedbackText]);

        $this->showFeedback = false;
        $this->feedbackText = '';

        session()->flash('message', 'Thank you for your feedback!');
    }

    public function handleTimeUp()
    {
        $this->submitAssessment();
    }

    public function exportResults($format = 'pdf')
    {
        // Export logic will be implemented here
        $this->logActivity('exported_results', ['format' => $format]);
    }

    public function shareResults()
    {
        // Share results logic will be implemented here
        $this->logActivity('shared_results');
    }

    // Helper methods
    public function getSubjectName()
    {
        $subject = $this->subjects->firstWhere('id', $this->selectedSubject);
        return $subject ? $subject->name : 'Unknown Subject';
    }

    public function addError($key, $message)
    {
        $this->customErrors[$key] = $message;
    }

    public function clearError($key)
    {
        unset($this->customErrors[$key]);
    }

    public function logActivity($action, $properties = [])
    {
        $student = auth()->user()->student;

        if ($student) {
            activity()
                ->performedOn($student)
                ->causedBy(auth()->user())
                ->withProperties(array_merge([
                    'action' => $action,
                    'assessment_id' => $this->assessment?->id,
                    'subject_id' => $this->selectedSubject,
                    'step' => $this->step
                ], $properties))
                ->log("Student {$action} in self-assessment");
        }
    }


    public function render()
    {
        return view('livewire.students.AssessmentConfiguration');
    }
}
