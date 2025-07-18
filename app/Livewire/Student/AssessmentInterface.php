<?php

namespace App\Livewire\Student;

use App\Models\AcademicSubject as Subject;
use App\Models\AcademicSubtopic as Subtopic;
use App\Models\AcademicTopic as Topic;
use App\Models\Assessment;
use App\Models\Assignment;
use App\Models\EssayQuestion;
use App\Models\MultipleChoiceQuestion;
use App\Models\TrueOrFalseQuestion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class AssessmentInterface extends Component
{
    use StartsAssessment;

    public $step = 'setup'; // setup, assessment, results
    public $assessmentMode = 'self'; // 'self' or 'assignment'

    // Setup phase - Self Assessment
    public $selectedSubject = null;
    public $selectedTopic = null;
    public $selectedSubtopic = null;
    public $questionTypes = [
        'multiple_choice_question' => true,
        'true_or_false_question' => true,
        'essay_question' => false
    ];
    public $questionCount = 10;
    public $difficulty = 'all';
    public $timeLimitMinutes = null;

    // Setup phase - Assignment
    public $selectedAssignment = null;

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
        'selectedSubject' => 'required_if:assessmentMode,self',
        'questionCount' => 'required_if:assessmentMode,self|integer|min:1|max:50',
        'selectedAssignment' => 'required_if:assessmentMode,assignment',
    ];

    public function mount($mode = 'self')
    {
        $this->assessmentMode = $mode;
        $student = auth()->user()->student;

        if (!$student) {
            $this->subjects = collect();
            $this->availableAssignments = collect();
            return;
        }

        $this->loadStudentSubjects($student);
        $this->loadAvailableAssignments();

        // Log student accessing assessment interface
        activity()->performedOn($student)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'accessed_assessment_interface',
                'mode' => $this->assessmentMode,
                'page' => 'assessment-interface'
            ])
            ->log('Student accessed assessment interface');
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
//            ->wherePivot('is_active', true)
            ->with('academicLevel')
            ->get();

        // Merge individual subjects, removing duplicates
        foreach ($individualSubjects as $subject) {
            if (!$this->subjects->contains('id', $subject->id)) {
                $this->subjects->push($subject);
            }
        }

        // Remove subjects that are individually marked as inactive
        $removedSubjects = $student->individualSubjects()
            ->wherePivot('is_active', true)
            ->pluck('academic_subjects.id');

        $this->subjects = $this->subjects->reject(function ($subject) use ($removedSubjects) {
            return $removedSubjects->contains($subject->id);
        });

        if(count($this->subjects) === 0) {
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

    public function updatedSelectedSubject($value)
    {
        $this->selectedTopic = null;
        $this->selectedSubtopic = null;
        $this->topics = [];
        $this->subtopics = [];

        if ($value) {
            $this->topics = Topic::where('academic_subject_id', $value)->get();
        }
    }

    public function updatedSelectedTopic($value)
    {
        $this->selectedSubtopic = null;
        $this->subtopics = [];

        if ($value) {
            $this->subtopics = Subtopic::where('academic_topic_id', $value)->get();
        }
    }

    public function getRecentAssessmentsProperty()
    {
        return Assessment::where('student_id', auth()->user()->student->id)
            ->with(['subject', 'topic'])
            ->latest()
            ->paginate(10);
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
        $this->validate(['selectedAssignment' => 'required']);

        $assignment = Assignment::find($this->selectedAssignment);
        if (!$assignment) {
            session()->flash('error', 'Assignment not found.');
            return;
        }

        // Check if student can start this assignment
        if (!$this->canStartAssignment($assignment)) {
            session()->flash('error', 'You are not eligible to start this assignment or it is not available.');
            return;
        }

        try {
            $this->initializeAssessmentFromAssignment($assignment);
        } catch (\Exception $e) {
            Log::error('Failed to start assignment practice', [
                'assignment_id' => $assignment->id,
                'error' => $e->getMessage(),
                'student_id' => auth()->user()->student->id
            ]);

            session()->flash('error', 'Failed to start assignment practice. Please try again.');
        }
    }

    private function startFromConfiguration(): void
    {
        $this->validate([
            'selectedSubject' => 'required',
            'questionCount' => 'required|integer|min:1|max:50',
        ]);

        // Validate question type combinations
        if (!$this->validateQuestionTypeCombinations()) {
            return;
        }

        try {
            $this->initializeAssessmentFromConfiguration();
        } catch (\Exception $e) {
            Log::error('Failed to start self-assessment', [
                'config' => $this->getConfigurationArray(),
                'error' => $e->getMessage(),
                'student_id' => auth()->user()->student->id
            ]);

            session()->flash('error', 'Failed to start self-assessment. Please try again.');
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

        $this->finalizeAssessmentStart('assignment');
    }

    private function initializeAssessmentFromConfiguration(): void
    {
        $config = $this->getConfigurationArray();

        // Create assessment record
        $this->assessment = Assessment::create([
            'student_id' => auth()->user()->student->id,
            'subject_id' => $config['subject_id'],
            'topic_id' => $config['topic_id'],
            'subtopic_id' => $config['subtopic_id'],
            'title' => $config['title'],
            'type' => Assessment::TYPE_SELF,
            'start_time' => now(),
            'status' => Assessment::STATUS_IN_PROGRESS,
            'time_limit_minutes' => $config['time_limit_minutes'],
        ]);

        // Set time limit
        $this->setupTimeLimit($config['time_limit_minutes']);

        // Generate questions from configuration
        $this->generateQuestionsFromConfiguration($config);

        $this->finalizeAssessmentStart('self');
    }

    private function generateQuestionsFromAssignment(Assignment $assignment): void
    {
        // Generate questions based on assignment configuration
        $generatedQuestions = $assignment->generateQuestionsForStudent(auth()->user()->student->id);

        $this->questions = $generatedQuestions->toArray();
        $this->responses = array_fill(0, count($this->questions), []);

        // Store questions data in assessment
        $this->assessment->setQuestionsData($this->questions);

        Log::info('Generated questions from assignment', [
            'assignment_id' => $assignment->id,
            'assessment_id' => $this->assessment->id,
            'question_count' => count($this->questions)
        ]);
    }

    private function generateQuestionsFromConfiguration(array $config): void
    {
        Log::info('Generating questions from configuration', [
            'config' => $config,
            'student_id' => auth()->user()->student->id
        ]);

        $allQuestions = collect();
        $selectedTypes = array_filter($config['question_types']);

        // For essay questions, we need to handle them specially
        if (in_array('essay_question', $selectedTypes)) {
            $questions = $this->getQuestionsForType('essay_question', $config);
            $allQuestions = $allQuestions->merge($questions);
        } else {
            // For other question types, distribute evenly
            $typesCount = count($selectedTypes);
            $questionsPerType = intval($config['question_count'] / $typesCount);
            $remainder = $config['question_count'] % $typesCount;

            foreach ($selectedTypes as $type) {
                $count = $questionsPerType;
                if ($remainder > 0) {
                    $count++;
                    $remainder--;
                }

                $typeQuestions = $this->getQuestionsForType($type, $config);

                // Limit to requested count for this type
                $typeQuestions = $typeQuestions->take($count);

                Log::info('Questions for type: ' . $type, [
                    'requested_count' => $count,
                    'actual_count' => $typeQuestions->count(),
                    'subject_id' => $config['subject_id']
                ]);

                $allQuestions = $allQuestions->merge($typeQuestions);
            }
        }

        if ($allQuestions->isEmpty()) {
            Log::warning('No questions found for configuration', [
                'config' => $config,
                'student_id' => auth()->user()->student->id
            ]);

            session()->flash('error', 'No questions found matching your criteria. Please try different settings.');
            return;
        }

        // Shuffle and limit questions
        $selectedQuestions = $allQuestions->shuffle()->take($config['question_count']);

        // Format questions for assessment
        $this->questions = $selectedQuestions->map(function ($item) {
            return $this->formatQuestionForAssessment($item['model'], $item['type']);
        })->toArray();

        // Initialize responses array
        $this->responses = array_fill(0, count($this->questions), []);

        // Check if assessment has essay questions
        $hasEssayQuestions = collect($this->questions)->contains('type', 'essay_question');
        $this->assessment->update(['has_essay_questions' => $hasEssayQuestions]);

        // Store questions data in assessment
        $this->assessment->setQuestionsData($this->questions);

        Log::info('Generated questions for self-assessment', [
            'assessment_id' => $this->assessment->id,
            'question_count' => count($this->questions),
            'question_types' => $selectedTypes,
            'subject_id' => $config['subject_id'],
            'topic_id' => $config['topic_id'],
            'subtopic_id' => $config['subtopic_id']
        ]);
    }

    private function getQuestionsForType(string $type, array $config): Collection
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

        if (!$query) {
            return collect();
        }

        // Apply difficulty filter
        if ($config['difficulty'] !== 'all') {
            $query->where('difficulty_level', $config['difficulty']);
        }

        // Apply subject filter - this is the main fix
        if ($config['subject_id']) {
            $query->whereHas('academicTopic', function ($q) use ($config) {
                $q->where('academic_subject_id', $config['subject_id']);
            });
        }

        // Apply topic filter
        if ($config['topic_id']) {
            $query->where('academic_topic_id', $config['topic_id']);
        }

        // Apply subtopic filter
        if ($config['subtopic_id']) {
            $query->where('academic_subtopic_id', $config['subtopic_id']);
        }

        return $query->get()->map(fn($q) => ['type' => $type, 'model' => $q]);
    }

    private function formatQuestionForAssessment($questionModel, string $type): array
    {
        return [
            'id' => $questionModel->id,
            'type' => $type,
            'model' => $questionModel,
            'points' => 1, // Default points, can be customized
            'difficulty_level' => $questionModel->difficulty_level ?? 'medium',
        ];
    }

    private function setupTimeLimit($minutes): void
    {
        if ($minutes) {
            $this->timeLimitSeconds = $minutes * 60;
            $this->timeRemaining = $this->timeLimitSeconds;
        } else {
            $this->timeLimitSeconds = 0;
            $this->timeRemaining = null;
        }
    }

    private function finalizeAssessmentStart(string $mode): void
    {
        $this->startTime = now();
        $this->step = 'assessment';
        $this->currentQuestionIndex = 0;

        // Log assessment start
        activity()->performedOn($this->assessment)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => $mode === 'assignment' ? 'started_assignment' : 'started_self_assessment',
                'assessment_id' => $this->assessment->id,
                'assignment_id' => $this->selectedAssignment,
                'question_count' => count($this->questions),
                'time_limit_minutes' => $this->assessment->time_limit_minutes,
            ])
            ->log('Student started ' . ($mode === 'assignment' ? 'assignment' : 'self assessment'));
    }

    private function getConfigurationArray(): array
    {
        return [
            'subject_id' => $this->selectedSubject,
            'topic_id' => $this->selectedTopic,
            'subtopic_id' => $this->selectedSubtopic,
            'question_types' => array_keys(array_filter($this->questionTypes)),
            'question_count' => $this->questionCount,
            'difficulty' => $this->difficulty,
            'time_limit_minutes' => $this->timeLimitMinutes,
            'title' => $this->generateAssessmentTitle(),
        ];
    }

    private function generateAssessmentTitle(): string
    {
        $subject = Subject::find($this->selectedSubject);
        $topic = $this->selectedTopic ? Topic::find($this->selectedTopic) : null;
        $subtopic = $this->selectedSubtopic ? Subtopic::find($this->selectedSubtopic) : null;

        $title = "Self Assessment: {$subject->name}";
        if ($topic) {
            $title .= " - {$topic->name}";
        }
        if ($subtopic) {
            $title .= " - {$subtopic->name}";
        }

        return $title;
    }

    public function render()
    {
        return view('livewire.students.AssessmentCenter');
    }
}
