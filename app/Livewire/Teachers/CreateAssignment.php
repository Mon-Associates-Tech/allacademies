<?php

namespace App\Livewire\Teachers;

use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Models\Assignment;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\AssignmentNotificationService;
use App\Services\QuestionAvailabilityChecker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CreateAssignment extends Component
{
    public $title = '';

    public $description = '';

    public $type = 'quiz'; // quiz or examination

    public $academic_subject_id;

    public $duration_in_minutes = 60;

    public $starts_at;

    public $ends_at;

    public $is_randomized = false;

    public $instructions = '';

    public $total_marks = 100;

    // Assignment targets
    public $selectedAcademicGroups = [];

    public $selectedAcademicLevels = [];

    public $selectedStudentGroups = [];

    public $selectedStudents = [];

    // Questions selection
    public $selectedTopics = [];

    public $selectedSubtopics = [];

    public $questionTypes = [
        'multiple_choice_question' => ['enabled' => false, 'count' => 5, 'difficulty' => 'all'],
        'true_or_false_question' => ['enabled' => false, 'count' => 3, 'difficulty' => 'all'],
        'essay_question' => ['enabled' => false, 'count' => 2, 'difficulty' => 'all'],
    ];

    // Available options
    public $availableSubjects = [];

    public $availableAcademicGroups = [];

    public $availableAcademicLevels = [];

    public $availableStudentGroups = [];

    public $availableStudents = [];

    public $availableTopics = [];

    public $availableSubtopics = [];

    public $teacher;

    public $showQuestionSelection = false;

    public $restrict_navigation = false;

    // Wizard step tracking (1 = Basic Info, 2 = Time & Config, 3 = Questions, 4 = Targets)
    public $currentStep = 1;

    public $max_tab_switches = 3;

    public $auto_submit_on_violation = true;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'type' => 'required|in:quiz,examination',
        'academic_subject_id' => 'required|exists:academic_subjects,id',
        'duration_in_minutes' => 'required|integer|min:5|max:480',
        'starts_at' => 'required|date|after:now',
        'ends_at' => 'required|date|after:starts_at',
        'is_randomized' => 'boolean',
        'instructions' => 'nullable|string',
        'total_marks' => 'required|integer|min:1',
        'restrict_navigation' => 'boolean',
        'max_tab_switches' => 'nullable|integer|min:1|max:10',
        'auto_submit_on_violation' => 'boolean',
    ];

    protected $messages = [
        //   'question_count_validation' => 'The selected topics/subtopics do not have enough questions to fulfill the assignment requirements.',
    ];

    public function mount()
    {

        $this->teacher = Teacher::where('user_id', Auth::id())->first();
        $this->loadAvailableOptions();

        // Set default dates
        $this->starts_at = Carbon::now()->addHour()->format('Y-m-d\TH:i');
        $this->ends_at = Carbon::now()->addDay()->format('Y-m-d\TH:i');
    }

    public function loadAvailableOptions()
    {
        if (! $this->teacher) {
            return;
        }

        // Load teacher's subjects
        $this->availableSubjects = $this->teacher->academicSubjects()
            ->select('academic_subjects.id', 'academic_subjects.name', 'academic_subjects.code')
            ->get()
            ->toArray();

        // Load teacher's academic groups
        $this->availableAcademicGroups = $this->teacher->academicGroups()
            ->select('academic_groups.id', 'academic_groups.name')
            ->get()
            ->toArray();

        // Load teacher's academic levels
        $this->availableAcademicLevels = $this->teacher->academicLevels()
            ->select('academic_levels.id', 'academic_levels.name')
            ->get()
            ->toArray();

        // Load teacher's student groups
        $this->availableStudentGroups = $this->teacher->studentGroups()
            ->select('student_groups.id', 'student_groups.name')
            ->get()
            ->toArray();

        // Load teacher's students
        $this->availableStudents = $this->teacher->assignedStudents()
            ->with('user:id,name')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->user->name ?? 'Unknown',
                ];
            })
            ->toArray();
    }

    public function updatedAcademicSubjectId($value)
    {
        if ($value) {
            $this->loadTopicsForSubject($value);
            $this->showQuestionSelection = true;
        } else {
            $this->availableTopics = [];
            $this->availableSubtopics = [];
            $this->showQuestionSelection = false;
        }
    }

    public function loadTopicsForSubject($subjectId)
    {
        $this->availableTopics = AcademicTopic::where('academic_subject_id', $subjectId)
            ->select('id', 'name')
            ->get()
            ->toArray();
    }

    public function updatedSelectedTopics($value)
    {
        $this->availableSubtopics = [];

        // Ensure we're working with an array
        $topicIds = [];
        if (! empty($this->selectedTopics) && is_array($this->selectedTopics)) {
            $topicIds = $this->selectedTopics;
        }

        if (! empty($topicIds)) {
            $this->availableSubtopics = AcademicSubtopic::whereIn('academic_topic_id', $topicIds)
                ->select('id', 'name', 'academic_topic_id')
                ->with('academicTopic:id,name')
                ->get()
                ->toArray();
        }
    }

    public function validateQuestionCounts()
    {
        $checker = new QuestionAvailabilityChecker;
        $questionsConfig = $this->buildQuestionsConfiguration();

        $result = $checker->checkQuestionAvailability(
            $questionsConfig,
            $this->academic_subject_id,
            $this->selectedTopics,
            $this->selectedSubtopics
        );

        return $result;
    }

    public function createAssignment()
    {
        // NEW: Check school has active content subscription before allowing assignment creation
        $school = auth()->user()->school;
        if (!$school || !$school->hasActiveContentSubscription()) {
            session()->flash('error',
                'Your school must have an active subscription to create assignments. ' .
                'Please contact your school administrator.'
            );
            return;
        }

        session()->flash('success', 'Creating assignment...');
        //        $this->validate();

        // Validate that at least one question type is enabled
        $hasQuestions = false;
        foreach ($this->questionTypes as $type => $config) {
            if ($config['enabled'] && $config['count'] > 0) {
                $hasQuestions = true;
                break;
            }
        }

        if (! $hasQuestions) {
            session()->flash('error', 'Please enable at least one question type with a count greater than 0.');

            return;
        }

        // Validate question availability
        logError(json_encode($this->validateQuestionCounts()));
        if (! $this->validateQuestionCounts()) {
            logError('Question availability check failed');
            // session()->flash('error', 'The selected topics/subtopics do not have enough questions to fulfill the assignment requirements.');
            // return;
        }

        try {
            DB::beginTransaction();

            // Build questions configuration
            $questionsConfig = $this->buildQuestionsConfiguration();

            // Create the assignment
            $assignment = Assignment::create([
                'title' => $this->title,
                'description' => $this->description,
                'type' => $this->type,
                'academic_subject_id' => $this->academic_subject_id,
                'teacher_id' => $this->teacher->id,
                'duration_in_minutes' => $this->duration_in_minutes,
                'starts_at' => $this->starts_at,
                'ends_at' => $this->ends_at,
                'is_randomized' => $this->is_randomized,
                'status' => 'published',
                'instructions' => $this->instructions,
                'total_marks' => $this->total_marks,
                'questions' => $questionsConfig,
                'restrict_navigation' => $this->restrict_navigation,
                'max_tab_switches' => $this->restrict_navigation ? $this->max_tab_switches : null,
                'auto_submit_on_violation' => $this->restrict_navigation ? $this->auto_submit_on_violation : null,

            ]);

            // Assign to academic groups
            if (! empty($this->selectedAcademicGroups)) {
                $assignment->academicGroups()->attach($this->selectedAcademicGroups);
            }

            // Assign to academic levels
            if (! empty($this->selectedAcademicLevels)) {
                $assignment->academicLevels()->attach($this->selectedAcademicLevels);
            }

            // Assign to student groups
            if (! empty($this->selectedStudentGroups)) {
                $assignment->studentGroups()->attach($this->selectedStudentGroups);
            }

            // Assign to individual students
            if (! empty($this->selectedStudents)) {
                $assignment->students()->attach($this->selectedStudents);
            }

            // Assign topics and subtopics
            if (! empty($this->selectedTopics)) {
                $assignment->topics()->attach($this->selectedTopics);
            }

            if (! empty($this->selectedSubtopics)) {
                $assignment->subtopics()->attach($this->selectedSubtopics);
            }

            DB::commit();

            // Send notifications
            app(AssignmentNotificationService::class)->sendAssignmentNotifications($assignment);

            session()->flash('success', 'Assignment created successfully and notifications sent!');

            return redirect()->route('teachers.assignments.index');

        } catch (\Exception $e) {
            logError('Assignment Creation Error: '.$e->getMessage());
            DB::rollBack();
            session()->flash('error', 'Failed to create assignment: '.$e->getMessage());
        }
    }

    private function buildQuestionsConfiguration()
    {
        $questionsConfig = [];

        foreach ($this->questionTypes as $type => $config) {
            if (! $config['enabled'] || $config['count'] <= 0) {
                continue;
            }

            $questionConfig = [
                'type' => $type,
                'count' => (int) $config['count'],
                'difficulty' => $config['difficulty'],
                'topic_ids' => [],
                'subtopic_ids' => [],
                'specific_ids' => [],
            ];

            // Add topic/subtopic filters if selected
            if (! empty($this->selectedSubtopics)) {
                $questionConfig['subtopic_ids'] = array_map('intval', $this->selectedSubtopics);
            } elseif (! empty($this->selectedTopics)) {
                $questionConfig['topic_ids'] = array_map('intval', $this->selectedTopics);
            }
            // If no topics/subtopics selected, questions will be filtered by the assignment's subject

            $questionsConfig[] = $questionConfig;
        }

        return $questionsConfig;
    }

    public function getTotalQuestionsProperty()
    {
        $total = 0;
        foreach ($this->questionTypes as $config) {
            if ($config['enabled']) {
                $total += (int) $config['count'];
            }
        }

        return $total;
    }

    /**
     * Navigate to the next step in the wizard
     */
    public function nextStep(): void
    {
        if (! $this->validateCurrentStep()) {
            return;
        }

        if ($this->currentStep < 4) {
            $this->currentStep++;
        }
    }

    /**
     * Navigate to the previous step in the wizard
     */
    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    /**
     * Navigate to a specific step in the wizard
     */
    public function goToStep(int $step): void
    {
        // Only allow going back or to completed steps
        if ($step < $this->currentStep) {
            $this->currentStep = $step;

            return;
        }

        // For forward navigation, validate all previous steps
        if ($step > $this->currentStep) {
            for ($i = $this->currentStep; $i < $step; $i++) {
                $this->currentStep = $i;
                if (! $this->validateCurrentStep()) {
                    return;
                }
            }
            $this->currentStep = $step;
        }
    }

    /**
     * Validate the current step
     */
    protected function validateCurrentStep(): bool
    {
        return match ($this->currentStep) {
            1 => $this->validateStep1(),
            2 => $this->validateStep2(),
            3 => $this->validateStep3(),
            default => true,
        };
    }

    /**
     * Validate Step 1: Basic Information (title, type, subject)
     */
    protected function validateStep1(): bool
    {
        $this->resetErrorBag();

        $hasErrors = false;

        if (empty($this->title)) {
            $this->addError('title', 'Please enter an assignment title.');
            $hasErrors = true;
        }

        if (empty($this->academic_subject_id)) {
            $this->addError('academic_subject_id', 'Please select a subject.');
            $hasErrors = true;
        }

        return ! $hasErrors;
    }

    /**
     * Validate Step 2: Time & Configuration
     */
    protected function validateStep2(): bool
    {
        $this->resetErrorBag();

        $hasErrors = false;

        if (empty($this->duration_in_minutes) || $this->duration_in_minutes < 5) {
            $this->addError('duration_in_minutes', 'Please enter a valid duration (minimum 5 minutes).');
            $hasErrors = true;
        }

        if (empty($this->starts_at)) {
            $this->addError('starts_at', 'Please select a start date and time.');
            $hasErrors = true;
        }

        if (empty($this->ends_at)) {
            $this->addError('ends_at', 'Please select an end date and time.');
            $hasErrors = true;
        }

        if (! empty($this->starts_at) && ! empty($this->ends_at) && $this->starts_at >= $this->ends_at) {
            $this->addError('ends_at', 'End date must be after start date.');
            $hasErrors = true;
        }

        return ! $hasErrors;
    }

    /**
     * Validate Step 3: Question Configuration
     */
    protected function validateStep3(): bool
    {
        $this->resetErrorBag();

        $hasQuestions = false;
        foreach ($this->questionTypes as $type => $config) {
            if ($config['enabled'] && $config['count'] > 0) {
                $hasQuestions = true;
                break;
            }
        }

        if (! $hasQuestions) {
            $this->addError('questionTypes', 'Please enable at least one question type with a count greater than 0.');

            return false;
        }

        return true;
    }

    /**
     * Check if Step 1 is complete (for progress indicator)
     */
    public function isStep1Complete(): bool
    {
        return ! empty($this->title) && ! empty($this->academic_subject_id);
    }

    /**
     * Check if Step 2 is complete (for progress indicator)
     */
    public function isStep2Complete(): bool
    {
        return ! empty($this->duration_in_minutes)
            && ! empty($this->starts_at)
            && ! empty($this->ends_at)
            && ! empty($this->total_marks);
    }

    /**
     * Check if Step 3 is complete (for progress indicator)
     */
    public function isStep3Complete(): bool
    {
        foreach ($this->questionTypes as $config) {
            if ($config['enabled'] && $config['count'] > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if Step 4 is complete (for progress indicator)
     */
    public function isStep4Complete(): bool
    {
        return ! empty($this->selectedAcademicGroups)
            || ! empty($this->selectedAcademicLevels)
            || ! empty($this->selectedStudentGroups)
            || ! empty($this->selectedStudents);
    }

    public function render()
    {
        return view('livewire.teachers.create-assignment');
    }
}
