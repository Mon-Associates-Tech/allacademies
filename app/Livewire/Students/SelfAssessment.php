<?php

namespace App\Livewire\Students;

use App\Livewire\Student\StartsAssessment;
use App\Models\AcademicSubject as Subject;
use App\Models\AcademicSubtopic as Subtopic;
use App\Models\AcademicTopic as Topic;
use App\Models\Assessment;
use App\Models\Assignment;
use App\Models\EssayQuestion;
use App\Models\MultipleChoiceQuestion;
use App\Models\Question;
use App\Models\TrueOrFalseQuestion;
use App\Services\AssessmentService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class SelfAssessment extends Component
{
    use StartsAssessment;

    public $step = 'setup'; // setup, assessment, results

    public $assessmentMode = 'self'; // 'self' or 'assignment'

    // Setup phase
    public $selectedSubject = null;

    public $selectedTopic = null;

    public $selectedSubtopic = null;

    public $selectedAssignment = null;

    public $questionTypes = [
        'multiple_choice_question' => true,
        'true_or_false_question' => true,
        'essay_question' => false,
    ];

    public $questionCount = 10;

    public $difficulty = 'all';

    public $timeLimitMinutes = null;

    // Assessment phase
    public $currentQuestionIndex = 0;

    public $questions = [];

    public $responses = [];

    public $assessment = null;

    public $timeRemaining = null;

    public $timeLimitSeconds = 0;

    public $startTime = null;

    // Results phase
    public $assessmentResult = null;

    public $subjects = [];

    public $topics = [];

    public $subtopics = [];

    public $availableAssignments = [];

    protected $rules = [
        'selectedSubject' => 'required',
        'questionCount' => 'required|integer|min:1|max:50',
    ];

    public function mount()
    {
        $student = auth()->user()->student;

        if (! $student) {
            $this->subjects = collect();

            return;
        }

        $this->loadStudentSubjects($student);
        $this->loadAvailableAssignments();

        // Log student accessing self-assessment
        activity()->performedOn($student)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'accessed_self_assessment',
                'page' => 'self-assessment',
            ])
            ->log('Student accessed self-assessment page');
    }

    private function loadStudentSubjects($student): void
    {
        $this->subjects = collect();

        // Get subjects from student's academic level
        if ($student->academicLevel) {
            $levelSubjects = $student->academicLevel->academicSubjects()
                ->with('academicLevel')
                ->get();
            $this->subjects = $this->subjects->merge($levelSubjects);
        }

        // Get individual subjects assigned to the student
        $individualSubjects = $student->individualSubjects()
            ->wherePivot('is_active', true)
            ->with('academicLevel')
            ->get();

        // Merge individual subjects, removing duplicates
        foreach ($individualSubjects as $subject) {
            if (! $this->subjects->contains('id', $subject->id)) {
                $this->subjects->push($subject);
            }
        }

        // Remove subjects that are individually marked as inactive
        $removedSubjects = $student->individualSubjects()
            ->wherePivot('is_active', false)
            ->pluck('academic_subjects.id');

        $this->subjects = $this->subjects->reject(function ($subject) use ($removedSubjects) {
            return $removedSubjects->contains($subject->id);
        });

        if (count($this->subjects) === 0) {
            $this->subjects = Subject::get();
        }
    }

    private function loadAvailableAssignments(): void
    {
        $this->availableAssignments = $this->getAvailableAssignments();
    }

    public function switchAssessmentMode($mode): void
    {
        $this->assessmentMode = $mode;
        $this->reset(['selectedAssignment', 'selectedSubject', 'selectedTopic', 'selectedSubtopic']);
    }

    public function startAssessment(): void
    {
        if ($this->assessmentMode === 'assignment') {
            $this->startFromAssignment();
        } else {
            $this->startFromConfiguration();
        }
    }

    private function startFromAssignment(): void
    {
        if (! $this->selectedAssignment) {
            session()->flash('error', 'Please select an assignment.');

            return;
        }

        $assignment = Assignment::find($this->selectedAssignment);
        if (! $assignment) {
            session()->flash('error', 'Assignment not found.');

            return;
        }

        // Check if student can start this assignment
        if (! $this->canStartAssignment($assignment)) {
            session()->flash('error', 'You are not eligible to start this assignment or it is not available.');

            return;
        }

        try {
            $this->initializeAssessmentFromAssignment($assignment);
        } catch (\Exception $e) {
            Log::error('Failed to start assignment practice', [
                'assignment_id' => $assignment->id,
                'error' => $e->getMessage(),
                'student_id' => auth()->user()->student->id,
            ]);

            session()->flash('error', 'Failed to start assignment practice. Please try again.');
        }
    }

    private function startFromConfiguration(): void
    {
        $this->validate();
        $student = auth()->user()->student;
        $this->loadStudentSubjects(auth()->user()->student);
        if (! $student) {
            $this->showError('Student not found');

            return;
        }

        // Validate question type combinations
        if (! $this->validateQuestionTypeCombinations()) {
            return;
        }

        try {
            $this->initializeAssessmentFromConfiguration();
        } catch (\Exception $e) {
            $this->showError($e->getMessage());
            Log::error('Failed to start self-assessment', [
                'config' => $this->getConfigurationArray(),
                'error' => $e->getMessage(),
                'student_id' => auth()->user()->student->id,
            ]);

            session()->flash('error', 'Failed to start assessment. Please try again.');
        }
    }

    private function validateQuestionTypeCombinations(): bool
    {
        $selectedTypes = array_filter($this->questionTypes);

        if (empty($selectedTypes)) {
            session()->flash('error', 'Please select at least one question type.');

            return false;
        }

        // If essay is selected, it must be the only type
        if ($this->questionTypes['essay_question'] && count($selectedTypes) > 1) {
            session()->flash('error', 'Essay questions cannot be combined with other question types.');

            return false;
        }

        return true;
    }

    private function initializeAssessmentFromAssignment(Assignment $assignment): void
    {
        // Create assessment record
        $this->assessment = Assessment::create([
            'student_id' => auth()->user()->student->id,
            'subject_id' => $assignment->academic_subject_id,
            'title' => "Assignment Practice: {$assignment->title}",
            'type' => Assessment::TYPE_ASSIGNMENT,
            'start_time' => now(),
            'status' => Assessment::STATUS_IN_PROGRESS,
            'assignment_id' => $assignment->id,
            'time_limit_minutes' => $assignment->duration_in_minutes,
        ]);

        // Set time limit
        $this->setupTimeLimit($assignment->duration_in_minutes);

        // Generate questions from assignment
        $this->generateQuestionsFromAssignment($assignment);

        $this->finalizeAssessmentStart();
    }

    private function initializeAssessmentFromConfiguration(): void
    {
        $config = $this->getConfigurationArray();

        $student = auth()->user()->student;
        $this->showError(json_encode($student));
        if (! $student) {
            $this->showError('Student not found');

            return;
        }

        // Ensure subject exists
        $subject = Subject::find($config['subject_id']);
        if (! $subject) {
            $this->showError('Subject not found.');

            return;
        }

        $topic = $config['topic_id'] ? Topic::find($config['topic_id']) : null;
        if ($config['topic_id'] && ! $topic) {
            $this->showError('Topic not found.');

            return;
        }

        $subtopic = $config['subtopic_id'] ? Subtopic::find($config['subtopic_id']) : null;
        if ($config['subtopic_id'] && ! $subtopic) {
            $this->showError('Subtopic not found.');

            return;
        }

        // Create assessment record
        $this->assessment = Assessment::create([
            'student_id' => $student->id,
            'subject_id' => $config['subject_id'],
            'topic_id' => $config['topic_id'],
            'subtopic_id' => $config['subtopic_id'],
            'title' => $config['title'],
            'type' => Assessment::TYPE_SELF,
            'question_types' => $config['question_types'],
            'start_time' => now(),
            'status' => Assessment::STATUS_IN_PROGRESS,
            'time_limit_minutes' => $config['time_limit_minutes'],
        ]);
        $this->showError(json_encode($this->assessment), false);
        // Set time limit
        $this->setupTimeLimit($config['time_limit_minutes']);

        // Generate questions from configuration
        $this->generateQuestionsFromConfiguration($config);

        $this->finalizeAssessmentStart();
    }

    private function generateQuestionsFromConfiguration(array $config): void
    {
        $allQuestions = collect();

        // Generate questions for each selected type
        foreach ($config['question_types'] as $type => $enabled) {
            if ($enabled) {
                $questions = $this->getQuestionsForType($type, $config);
                $allQuestions = $allQuestions->merge($questions);
            }
        }

        if ($allQuestions->isEmpty()) {
            throw new \Exception('No questions found matching your criteria');
        }

        // Shuffle and limit questions
        $selectedQuestions = $allQuestions->shuffle()->take($config['question_count']);

        // Format questions for assessment
        $this->questions = $selectedQuestions->map(function ($item) {
            return $this->formatQuestionForAssessment($item['model'], $item['type']);
        })->toArray();

        // Check if assessment has essay questions
        $hasEssayQuestions = collect($this->questions)->contains('type', 'essay_question');
        $this->assessment->update(['has_essay_questions' => $hasEssayQuestions]);
    }

    private function getQuestionsForType(string $type, array $config): \Illuminate\Support\Collection
    {
        $query = null;

        switch ($type) {
            case 'multiple_choice_question':
                $query = MultipleChoiceQuestion::query();
                break;
            case 'true_or_false_question':
                $query = TrueOrFalseQuestion::query();
                break;
            case 'essay_question':
                $query = EssayQuestion::query();
                break;
        }

        if (! $query) {
            return collect();
        }

        // Apply filters
        if ($config['difficulty'] !== 'all') {
            $query->where('difficulty_level', $config['difficulty']);
        }

        $this->applyContentFilters($query, $config);

        return $query->get()->map(fn ($q) => ['type' => $type, 'model' => $q]);
    }

    private function applyContentFilters($query, array $config): void
    {
        if ($config['subject_id']) {
            $query->whereHas('academicTopic', function ($q) use ($config) {
                $q->where('academic_subject_id', $config['subject_id']);
            });
        }

        if ($config['topic_id']) {
            $query->where('academic_topic_id', $config['topic_id']);
        }

        if ($config['subtopic_id']) {
            $query->where('academic_subtopic_id', $config['subtopic_id']);
        }
    }

    private function formatQuestionForAssessment($questionModel, string $type): array
    {
        $question = Question::where('questionable_type', $this->getQuestionableType($type))
            ->where('questionable_id', $questionModel->id)
            ->first();

        $formatted = [
            'id' => $question->id,
            'question_id' => $question->id,
            'questionable_id' => $questionModel->id,
            'type' => $type,
            'points' => $question->points ?? 1,
            'difficulty' => $questionModel->difficulty_level ?? 'medium',
        ];

        switch ($type) {
            case 'multiple_choice_question':
                $formatted['question'] = $questionModel->question;
                $formatted['options'] = $questionModel->options;
                $formatted['correct_answer'] = $questionModel->correct_answer;
                break;
            case 'true_or_false_question':
                $formatted['question'] = $questionModel->question;
                $formatted['correct_answer'] = $questionModel->answer;
                break;
            case 'essay_question':
                $formatted['question'] = $questionModel->question;
                $formatted['max_words'] = $questionModel->max_words;
                break;
        }

        return $formatted;
    }

    private function getQuestionableType(string $type): string
    {
        return match ($type) {
            'multiple_choice_question' => MultipleChoiceQuestion::class,
            'true_or_false_question' => TrueOrFalseQuestion::class,
            'essay_question' => EssayQuestion::class,
            default => throw new \InvalidArgumentException("Unknown question type: $type"),
        };
    }

    private function setupTimeLimit(?int $minutes): void
    {
        if ($minutes) {
            $this->timeLimitSeconds = $minutes * 60;
            $this->timeRemaining = $this->timeLimitSeconds;
        } else {
            $this->timeLimitSeconds = 0;
            $this->timeRemaining = null;
        }
    }

    private function finalizeAssessmentStart(): void
    {
        // Initialize responses
        $this->responses = [];
        foreach ($this->questions as $index => $question) {
            $this->responses[$index] = [
                'question_id' => $question['question_id'],
                'student_answer' => null,
                'is_answered' => false,
                'response_time' => 0,
            ];
        }

        $this->currentQuestionIndex = 0;
        $this->startTime = now();
        $this->step = 'assessment';

        session()->flash('success', 'Assessment started successfully!');
    }

    public function saveResponse(int $questionIndex, $response): void
    {
        if (isset($this->responses[$questionIndex])) {
            $this->responses[$questionIndex]['student_answer'] = $response;
            $this->responses[$questionIndex]['is_answered'] = ! empty($response);
            $this->responses[$questionIndex]['response_time'] = now()->diffInSeconds($this->startTime);
        }
    }

    public function nextQuestion(): void
    {
        if ($this->currentQuestionIndex < count($this->questions) - 1) {
            $this->currentQuestionIndex++;
        }
    }

    public function previousQuestion(): void
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        }
    }

    public function goToQuestion(int $index): void
    {
        if ($index >= 0 && $index < count($this->questions)) {
            $this->currentQuestionIndex = $index;
        }
    }

    public function submitAssessment(): void
    {
        try {
            $assessmentService = app(AssessmentService::class);

            // Prepare questions data
            $questionsData = [];
            foreach ($this->questions as $index => $question) {
                $response = $this->responses[$index] ?? [];

                $questionsData[] = [
                    'question_id' => $question['question_id'],
                    'type' => $question['type'],
                    'question_text' => $question['question'],
                    'options' => $question['options'] ?? null,
                    'student_answer' => $response['student_answer'] ?? null,
                    'correct_answer' => $question['correct_answer'] ?? null,
                    'points_possible' => $question['points'],
                    'response_time' => $response['response_time'] ?? 0,
                ];
            }

            // Calculate summary
            $summaryData = $assessmentService->calculateAssessmentSummary($questionsData);
            $summaryData['time_taken'] = now()->diffInSeconds($this->startTime);
            $summaryData['answered_questions'] = collect($this->responses)->where('is_answered', true)->count();

            // Submit assessment
            $assessmentResponse = $assessmentService->submitAssessment(
                $this->assessment,
                $questionsData,
                $summaryData
            );

            $this->assessmentResult = $assessmentResponse;
            $this->step = 'results';

            session()->flash('success', 'Assessment submitted successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to submit assessment', [
                'assessment_id' => $this->assessment->id,
                'error' => $e->getMessage(),
                'student_id' => auth()->user()->student->id,
            ]);

            session()->flash('error', 'Failed to submit assessment. Please try again.');
        }
    }

    public function restartAssessment(): void
    {
        $this->reset(['questions', 'responses', 'assessment', 'assessmentResult', 'currentQuestionIndex']);
        $this->step = 'setup';
    }

    private function getConfigurationArray(): array
    {
        $subjectName = Subject::find($this->selectedSubject)?->name ?? 'Unknown';

        return [
            'subject_id' => $this->selectedSubject,
            'topic_id' => $this->selectedTopic,
            'subtopic_id' => $this->selectedSubtopic,
            'question_types' => $this->questionTypes,
            'question_count' => $this->questionCount,
            'difficulty' => $this->difficulty,
            'time_limit_minutes' => $this->timeLimitMinutes,
            'title' => 'Self Assessment - '.$subjectName,
        ];
    }

    public function updatedSelectedSubject()
    {
        $this->topics = $this->selectedSubject
            ? Topic::where('academic_subject_id', $this->selectedSubject)->get()
            : collect();

        $this->selectedTopic = null;
        $this->selectedSubtopic = null;
        $this->subtopics = collect();
    }

    public function updatedSelectedTopic()
    {
        $this->subtopics = $this->selectedTopic
            ? Subtopic::where('academic_topic_id', $this->selectedTopic)->get()
            : collect();

        $this->selectedSubtopic = null;
    }

    public function updatedQuestionTypes()
    {
        // If essay is selected, disable other types
        if ($this->questionTypes['essay_question']) {
            $this->questionTypes['multiple_choice_question'] = false;
            $this->questionTypes['true_or_false_question'] = false;
        }
    }

    public function getRecentAssessmentsProperty()
    {
        return Assessment::where('student_id', auth()->user()->student->id)
            ->with(['subject', 'topic'])
            ->latest()
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.students.self-assessment');
    }
}
