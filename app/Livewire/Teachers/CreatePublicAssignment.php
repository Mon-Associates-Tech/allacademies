<?php

namespace App\Livewire\Teachers;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Models\EssayQuestion;
use App\Models\MultipleChoiceQuestion;
use App\Models\Teacher;
use App\Models\TrueOrFalseQuestion;
use App\Services\PublicAssignment\PublicAssignmentAIService;
use App\Services\PublicAssignment\PublicAssignmentService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreatePublicAssignment extends Component
{
    use WithFileUploads;

    // Wizard step tracking
    public int $currentStep = 1;

    public int $totalSteps = 5;

    // Step 1: Basic Information
    public string $title = '';

    public string $description = '';

    public string $type = 'quiz';

    public string $instructions = '';

    // Step 2: Time & Configuration
    public ?int $duration_in_minutes = 60;

    public ?string $starts_at = null;

    public ?string $ends_at = null;

    public int $max_attempts = 1;

    // Step 3: Result & Proctoring Settings
    public string $result_visibility = 'immediate';

    public bool $show_correct_answers = true;

    public bool $show_score_breakdown = true;

    public bool $proctoring_enabled = true;

    public bool $restrict_navigation = true;

    public int $max_tab_switches = 3;

    public bool $auto_submit_on_violation = true;

    public bool $require_webcam = false;

    public bool $require_fullscreen = true;

    // Step 4: Sections & Questions
    public array $sections = [];

    public array $questions = []; // For assignments without sections

    public bool $useSections = false;

    public bool $is_randomized = false;

    // AI Generation
    public $uploadedFile = null;

    public ?string $documentContent = null;

    public bool $isGenerating = false;

    public string $aiDifficulty = 'medium';

    public array $aiQuestionTypes = [
        'multiple_choice' => 5,
        'true_false' => 3,
        'short_answer' => 2,
        'essay' => 0,
    ];

    public string $focusTopics = '';

    // Academic Hierarchy Selection for Question Generation
    public ?int $selectedAcademicGroupId = null;

    public ?int $selectedAcademicLevelId = null;

    public ?int $selectedAcademicSubjectId = null;

    public ?int $selectedAcademicTopicId = null;

    public ?int $selectedAcademicSubtopicId = null;

    public array $selectedAcademicTopicIds = [];

    public array $selectedAcademicSubtopicIds = [];

    // Question type counts for generation from database
    public array $questionTypeCounts = [
        'multiple_choice' => 5,
        'true_false' => 3,
        'essay' => 2,
    ];

    // Available questions count from database
    public array $availableQuestionsCount = [
        'multiple_choice' => 0,
        'true_false' => 0,
        'essay' => 0,
    ];

    // Question source: 'database' or 'ai'
    public string $questionSource = 'database';

    // Question editing
    public ?int $editingQuestionIndex = null;

    public ?int $editingSectionIndex = null;

    public array $editingQuestion = [];

    // Preview mode
    public bool $showPreview = false;

    protected PublicAssignmentService $assignmentService;

    protected PublicAssignmentAIService $aiService;

    protected ?Teacher $teacher = null;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'type' => 'required|in:quiz,examination,practice',
        'duration_in_minutes' => 'nullable|integer|min:5|max:480',
        'starts_at' => 'nullable|date',
        'ends_at' => 'nullable|date|after:starts_at',
        'max_attempts' => 'required|integer|min:1|max:10',
        'result_visibility' => 'required|in:immediate,after_due_date,manual_release',
    ];

    public function boot(
        PublicAssignmentService $assignmentService,
        PublicAssignmentAIService $aiService
    ): void {
        $this->assignmentService = $assignmentService;
        $this->aiService = $aiService;
    }

    public function mount(): void
    {
        $schoolId = getSchoolId() ?? Auth::user()?->school_id;
        $this->teacher = Teacher::where('user_id', Auth::id())->first();

        // Allow administrators/owners to create public assignments by ensuring a teacher profile exists
        if (! $this->teacher && $schoolId) {
            $this->teacher = Teacher::firstOrCreate(
                ['user_id' => Auth::id()],
                [
                    'school_id' => $schoolId,
                    'status' => 'active',
                ]
            );
        }

        // Set default dates
        $this->starts_at = Carbon::now()->addHour()->format('Y-m-d\TH:i');
        $this->ends_at = Carbon::now()->addWeek()->format('Y-m-d\TH:i');

        // Initialize with one empty section if using sections
        $this->sections = [];
        $this->questions = [];
    }

    // Academic Hierarchy Cascading Methods
    public function updatedSelectedAcademicGroupId(): void
    {
        $this->selectedAcademicLevelId = null;
        $this->selectedAcademicSubjectId = null;
        $this->selectedAcademicTopicId = null;
        $this->selectedAcademicSubtopicId = null;
        $this->updateAvailableQuestionsCount();
    }

    public function updatedSelectedAcademicLevelId(): void
    {
        $this->selectedAcademicSubjectId = null;
        $this->selectedAcademicTopicId = null;
        $this->selectedAcademicSubtopicId = null;
        $this->updateAvailableQuestionsCount();
    }

    public function updatedSelectedAcademicSubjectId(): void
    {
        $this->selectedAcademicTopicId = null;
        $this->selectedAcademicSubtopicId = null;
        $this->updateAvailableQuestionsCount();
    }

    public function updatedSelectedAcademicTopicId(): void
    {
        $this->selectedAcademicSubtopicId = null;
        $this->updateAvailableQuestionsCount();
    }

    public function updatedSelectedAcademicSubtopicId(): void
    {
        $this->updateAvailableQuestionsCount();
    }

    public function updatedEditingQuestionType($value): void
    {
        if ($value === 'multiple_choice') {
            $this->editingQuestion['options'] = ['A' => '', 'B' => '', 'C' => '', 'D' => ''];
            $this->editingQuestion['correct_answer'] = 'A';
        } elseif ($value === 'true_false') {
            $this->editingQuestion['options'] = [];
            $this->editingQuestion['correct_answer'] = 'true';
        } else { // short_answer, essay, or others
            $this->editingQuestion['options'] = [];
            $this->editingQuestion['correct_answer'] = null;
        }
    }

    // Get Academic Groups for dropdown
    public function getAcademicGroupsProperty(): Collection
    {
        return AcademicGroup::query()
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    // Get Academic Levels based on selected group
    public function getAcademicLevelsProperty(): Collection
    {
        if (! $this->selectedAcademicGroupId) {
            return collect();
        }

        return AcademicLevel::where('academic_group_id', $this->selectedAcademicGroupId)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    // Get Academic Subjects based on selected level
    public function getAcademicSubjectsProperty(): Collection
    {
        if (! $this->selectedAcademicLevelId) {
            return collect();
        }

        return AcademicSubject::where('academic_level_id', $this->selectedAcademicLevelId)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    // Get Academic Topics based on selected subject
    public function getAcademicTopicsProperty(): Collection
    {
        if (! $this->selectedAcademicSubjectId) {
            return collect();
        }

        return AcademicTopic::where('academic_subject_id', $this->selectedAcademicSubjectId)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    // Get Academic Subtopics based on selected topic
    public function getAcademicSubtopicsProperty(): Collection
    {
        if (! $this->selectedAcademicTopicId) {
            return collect();
        }

        return AcademicSubtopic::where('academic_topic_id', $this->selectedAcademicTopicId)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    // Update available questions count based on current selection
    public function updateAvailableQuestionsCount(): void
    {
        $this->availableQuestionsCount = [
            'multiple_choice' => $this->countAvailableQuestions('multiple_choice'),
            'true_false' => $this->countAvailableQuestions('true_false'),
            'essay' => $this->countAvailableQuestions('essay'),
        ];
    }

    // Count available questions of a specific type
    protected function countAvailableQuestions(string $type): int
    {
        $query = match ($type) {
            'multiple_choice' => MultipleChoiceQuestion::query(),
            'true_false' => TrueOrFalseQuestion::query(),
            'essay' => EssayQuestion::query(),
            default => null,
        };

        if (! $query) {
            return 0;
        }

        // Filter by subtopic if selected (for future compatibility when questions have subtopics)
        if ($this->selectedAcademicSubtopicId) {
            return $query->where('academic_subtopic_id', $this->selectedAcademicSubtopicId)->count();
        }

        // Filter by topic if selected - questions are linked directly to topics via academic_topic_id
        if ($this->selectedAcademicTopicId) {
            return $query->where('academic_topic_id', $this->selectedAcademicTopicId)->count();
        }

        // Filter by subject if selected - get all topics under this subject
        if ($this->selectedAcademicSubjectId) {
            $topicIds = AcademicTopic::where('academic_subject_id', $this->selectedAcademicSubjectId)
                ->pluck('id');

            return $query->whereIn('academic_topic_id', $topicIds)->count();
        }

        return 0;
    }

    // Generate questions from database based on selection
    public function generateQuestionsFromDatabase(?int $sectionIndex = null): void
    {
        if (! $this->selectedAcademicSubjectId) {
            $this->addError('academicSelection', 'Please select at least an academic subject.');

            return;
        }

        $this->isGenerating = true;

        try {
            $generatedQuestions = [];

            // Generate multiple choice questions
            if ($this->questionTypeCounts['multiple_choice'] > 0) {
                $mcQuestions = $this->fetchQuestionsFromDatabase('multiple_choice', $this->questionTypeCounts['multiple_choice']);
                $generatedQuestions = array_merge($generatedQuestions, $mcQuestions);
            }

            // Generate true/false questions
            if ($this->questionTypeCounts['true_false'] > 0) {
                $tfQuestions = $this->fetchQuestionsFromDatabase('true_false', $this->questionTypeCounts['true_false']);
                $generatedQuestions = array_merge($generatedQuestions, $tfQuestions);
            }

            // Generate essay questions
            if ($this->questionTypeCounts['essay'] > 0) {
                $essayQuestions = $this->fetchQuestionsFromDatabase('essay', $this->questionTypeCounts['essay']);
                $generatedQuestions = array_merge($generatedQuestions, $essayQuestions);
            }

            if (empty($generatedQuestions)) {
                $this->addError('generation', 'No questions found matching your criteria.');

                return;
            }

            if ($sectionIndex !== null && isset($this->sections[$sectionIndex])) {
                $this->sections[$sectionIndex]['questions'] = array_merge(
                    $this->sections[$sectionIndex]['questions'],
                    $generatedQuestions
                );
            } else {
                $this->questions = array_merge($this->questions, $generatedQuestions);
            }

            session()->flash('success', count($generatedQuestions).' questions added from database!');
        } catch (\Exception $e) {
            $this->addError('generation', 'Failed to fetch questions: '.$e->getMessage());
        } finally {
            $this->isGenerating = false;
        }
    }

    // Fetch questions from database
    protected function fetchQuestionsFromDatabase(string $type, int $count): array
    {
        $query = match ($type) {
            'multiple_choice' => MultipleChoiceQuestion::query(),
            'true_false' => TrueOrFalseQuestion::query(),
            'essay' => EssayQuestion::query(),
            default => null,
        };

        if (! $query) {
            return [];
        }

        // Apply filters based on selection
        // Questions are linked directly to topics via academic_topic_id
        if ($this->selectedAcademicSubtopicId) {
            // For future compatibility when questions have subtopics
            $query->where('academic_subtopic_id', $this->selectedAcademicSubtopicId);
        } elseif ($this->selectedAcademicTopicId) {
            // Filter directly by topic
            $query->where('academic_topic_id', $this->selectedAcademicTopicId);
        } elseif ($this->selectedAcademicSubjectId) {
            // Get all topics under this subject
            $topicIds = AcademicTopic::where('academic_subject_id', $this->selectedAcademicSubjectId)
                ->pluck('id');
            $query->whereIn('academic_topic_id', $topicIds);
        }

        // Get random questions
        $dbQuestions = $query->inRandomOrder()->limit($count)->get();

        // Convert to the format expected by the assignment
        return $dbQuestions->map(function ($dbQuestion) use ($type) {
            return $this->convertDatabaseQuestionToFormat($dbQuestion, $type);
        })->toArray();
    }

    /**
     * Extract string content from a value that may be a Mark object, array, or string
     */
    protected function extractStringFromMark($value): string
    {
        if ($value === null) {
            return '';
        }

        // Handle Mark object - use the 'up' property (markdown/raw content) or 'down' (HTML)
        if ($value instanceof \App\Support\Mark) {
            return $value->up ?? $value->down ?? '';
        }

        // Handle array format (legacy or JSON decoded)
        if (is_array($value)) {
            return $value['content'] ?? $value['up'] ?? $value['down'] ?? '';
        }

        // Already a string
        if (is_string($value)) {
            return $value;
        }

        // Fallback: try to cast to string
        return (string) $value;
    }

    // Convert database question to assignment format
    protected function convertDatabaseQuestionToFormat($dbQuestion, string $type): array
    {
        $difficulty = strtolower($dbQuestion->difficulty_level ?? 'medium');
        if (! in_array($difficulty, ['easy', 'medium', 'hard'], true)) {
            $difficulty = 'medium';
        }
        $baseQuestion = [
            'type' => $type,
            'question' => $this->extractStringFromMark($dbQuestion->question),
            'marks' => $dbQuestion->score ?? 1,
            'difficulty' => $difficulty,
            'explanation' => '',
            'ai_generated' => false,
            'database_id' => $dbQuestion->id,
        ];

        if ($type === 'multiple_choice') {
            $baseQuestion['options'] = [
                'A' => $this->extractStringFromMark($dbQuestion->option_a),
                'B' => $this->extractStringFromMark($dbQuestion->option_b),
                'C' => $this->extractStringFromMark($dbQuestion->option_c),
                'D' => $this->extractStringFromMark($dbQuestion->option_d),
            ];
            if ($dbQuestion->option_e) {
                $baseQuestion['options']['E'] = $this->extractStringFromMark($dbQuestion->option_e);
            }
            $baseQuestion['correct_answer'] = $dbQuestion->answer ?? 'A';
        } elseif ($type === 'true_false') {
            $baseQuestion['correct_answer'] = $dbQuestion->answer ? 'true' : 'false';
        } elseif ($type === 'essay') {
            $baseQuestion['correct_answer'] = $this->extractStringFromMark($dbQuestion->answer);
            $baseQuestion['keywords'] = [];
            $baseQuestion['grading_rubric'] = '';
        }

        return $baseQuestion;
    }

    // Navigation methods
    public function nextStep(): void
    {
        if ($this->validateCurrentStep()) {
            if ($this->currentStep < $this->totalSteps) {
                $this->currentStep++;
            }
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep(int $step): void
    {
        if ($step < $this->currentStep || $this->validateStepsUpTo($step - 1)) {
            $this->currentStep = $step;
        }
    }

    protected function validateCurrentStep(): bool
    {
        return match ($this->currentStep) {
            1 => $this->validateStep1(),
            2 => $this->validateStep2(),
            3 => $this->validateStep3(),
            4 => $this->validateStep4(),
            default => true,
        };
    }

    protected function validateStepsUpTo(int $step): bool
    {
        for ($i = 1; $i <= $step; $i++) {
            $this->currentStep = $i;
            if (! $this->validateCurrentStep()) {
                return false;
            }
        }

        return true;
    }

    protected function validateStep1(): bool
    {
        $this->resetErrorBag();

        if (empty($this->title)) {
            $this->addError('title', 'Please enter an assignment title.');

            return false;
        }

        return true;
    }

    protected function validateStep2(): bool
    {
        $this->resetErrorBag();

        if (! empty($this->starts_at) && ! empty($this->ends_at)) {
            if (Carbon::parse($this->starts_at)->gte(Carbon::parse($this->ends_at))) {
                $this->addError('ends_at', 'End date must be after start date.');

                return false;
            }
        }

        return true;
    }

    protected function validateStep3(): bool
    {
        return true;
    }

    protected function validateStep4(): bool
    {
        $this->resetErrorBag();

        $totalQuestions = $this->getTotalQuestionsCount();

        if ($totalQuestions === 0) {
            $this->addError('questions', 'Please add at least one question.');

            return false;
        }

        return true;
    }

    // Section management
    public function addSection(): void
    {
        $this->sections[] = [
            'title' => 'Section '.(count($this->sections) + 1),
            'description' => '',
            'instructions' => '',
            'time_limit_minutes' => null,
            'is_randomized' => false,
            'questions' => [],
        ];
    }

    public function removeSection(int $index): void
    {
        if (isset($this->sections[$index])) {
            unset($this->sections[$index]);
            $this->sections = array_values($this->sections);
        }
    }

    public function updateSectionTitle(int $index, string $title): void
    {
        if (isset($this->sections[$index])) {
            $this->sections[$index]['title'] = $title;
        }
    }

    // Question management
    public function addQuestion(?int $sectionIndex = null): void
    {
        $newQuestion = [
            'type' => 'multiple_choice',
            'question' => '',
            'options' => ['A' => '', 'B' => '', 'C' => '', 'D' => ''],
            'correct_answer' => 'A',
            'explanation' => '',
            'marks' => 1,
            'difficulty' => 'medium',
            'keywords' => [],
            'grading_rubric' => '',
            'ai_generated' => false,
        ];

        if ($sectionIndex !== null && isset($this->sections[$sectionIndex])) {
            $this->sections[$sectionIndex]['questions'][] = $newQuestion;
        } else {
            $this->questions[] = $newQuestion;
        }
    }

    public function removeQuestion(int $questionIndex, ?int $sectionIndex = null): void
    {
        if ($sectionIndex !== null && isset($this->sections[$sectionIndex]['questions'][$questionIndex])) {
            unset($this->sections[$sectionIndex]['questions'][$questionIndex]);
            $this->sections[$sectionIndex]['questions'] = array_values($this->sections[$sectionIndex]['questions']);
        } elseif (isset($this->questions[$questionIndex])) {
            unset($this->questions[$questionIndex]);
            $this->questions = array_values($this->questions);
        }
    }

    public function editQuestion(int $questionIndex, ?int $sectionIndex = null): void
    {
        $this->editingQuestionIndex = $questionIndex;
        $this->editingSectionIndex = $sectionIndex;

        if ($sectionIndex !== null) {
            $this->editingQuestion = $this->sections[$sectionIndex]['questions'][$questionIndex] ?? [];
        } else {
            $this->editingQuestion = $this->questions[$questionIndex] ?? [];
        }
    }

    public function saveQuestion(): void
    {
        if ($this->editingQuestionIndex === null) {
            return;
        }

        if ($this->editingSectionIndex !== null) {
            $this->sections[$this->editingSectionIndex]['questions'][$this->editingQuestionIndex] = $this->editingQuestion;
        } else {
            $this->questions[$this->editingQuestionIndex] = $this->editingQuestion;
        }

        $this->cancelEditing();
    }

    public function cancelEditing(): void
    {
        $this->editingQuestionIndex = null;
        $this->editingSectionIndex = null;
        $this->editingQuestion = [];
    }

    public function moveQuestionUp(int $questionIndex, ?int $sectionIndex = null): void
    {
        if ($questionIndex === 0) {
            return;
        }

        if ($sectionIndex !== null) {
            $questions = &$this->sections[$sectionIndex]['questions'];
        } else {
            $questions = &$this->questions;
        }

        $temp = $questions[$questionIndex - 1];
        $questions[$questionIndex - 1] = $questions[$questionIndex];
        $questions[$questionIndex] = $temp;
    }

    public function moveQuestionDown(int $questionIndex, ?int $sectionIndex = null): void
    {
        if ($sectionIndex !== null) {
            $questions = &$this->sections[$sectionIndex]['questions'];
        } else {
            $questions = &$this->questions;
        }

        if ($questionIndex >= count($questions) - 1) {
            return;
        }

        $temp = $questions[$questionIndex + 1];
        $questions[$questionIndex + 1] = $questions[$questionIndex];
        $questions[$questionIndex] = $temp;
    }

    // AI Generation
    public function updatedUploadedFile(): void
    {
        if ($this->uploadedFile) {
            try {
                $this->documentContent = 'Document uploaded: '.$this->uploadedFile->getClientOriginalName();
            } catch (\Exception $e) {
                $this->addError('uploadedFile', 'Failed to process the uploaded file.');
            }
        }
    }

    public function generateQuestions(?int $sectionIndex = null): void
    {
        if (! $this->uploadedFile) {
            $this->addError('uploadedFile', 'Please upload a document first.');

            return;
        }

        $this->isGenerating = true;

        try {
            $parameters = [
                'question_types' => array_filter($this->aiQuestionTypes, fn ($count) => $count > 0),
                'difficulty' => $this->aiDifficulty,
                'focus_topics' => array_filter(array_map('trim', explode(',', $this->focusTopics))),
            ];

            $generatedQuestions = $this->aiService->generateQuestionsFromDocument(
                $this->uploadedFile,
                $parameters
            );

            if ($sectionIndex !== null && isset($this->sections[$sectionIndex])) {
                $this->sections[$sectionIndex]['questions'] = array_merge(
                    $this->sections[$sectionIndex]['questions'],
                    $generatedQuestions
                );
            } else {
                $this->questions = array_merge($this->questions, $generatedQuestions);
            }

            session()->flash('success', count($generatedQuestions).' questions generated successfully!');
        } catch (\Exception $e) {
            $this->addError('generation', 'Failed to generate questions: '.$e->getMessage());
        } finally {
            $this->isGenerating = false;
        }
    }

    public function regenerateQuestion(int $questionIndex, ?int $sectionIndex = null): void
    {
        $this->isGenerating = true;

        try {
            $currentQuestion = $sectionIndex !== null
                ? $this->sections[$sectionIndex]['questions'][$questionIndex]
                : $this->questions[$questionIndex];

            $difficulty = strtolower($currentQuestion['difficulty'] ?? 'medium');
            if (! in_array($difficulty, ['easy', 'medium', 'hard'], true)) {
                $difficulty = 'medium';
            }

            $parameters = [
                'question_types' => [$currentQuestion['type'] => 1],
                'difficulty' => $difficulty,
            ];

            $content = $this->documentContent ?? $this->description ?? $this->title;

            $generatedQuestions = $this->aiService->generateQuestions($content, $parameters);

            if (! empty($generatedQuestions)) {
                $newQuestion = $generatedQuestions[0];
                $newQuestion['marks'] = $currentQuestion['marks'];

                if ($sectionIndex !== null) {
                    $this->sections[$sectionIndex]['questions'][$questionIndex] = $newQuestion;
                } else {
                    $this->questions[$questionIndex] = $newQuestion;
                }

                session()->flash('success', 'Question regenerated successfully!');
            }
        } catch (\Exception $e) {
            $this->addError('regeneration', 'Failed to regenerate question: '.$e->getMessage());
        } finally {
            $this->isGenerating = false;
        }
    }

    // Preview
    public function togglePreview(): void
    {
        $this->showPreview = ! $this->showPreview;
    }

    // Computed properties
    public function getTotalQuestionsCount(): int
    {
        $count = count($this->questions);

        foreach ($this->sections as $section) {
            $count += count($section['questions'] ?? []);
        }

        return $count;
    }

    public function getTotalMarks(): int
    {
        $total = 0;

        foreach ($this->questions as $question) {
            $total += $question['marks'] ?? 1;
        }

        foreach ($this->sections as $section) {
            foreach ($section['questions'] ?? [] as $question) {
                $total += $question['marks'] ?? 1;
            }
        }

        return $total;
    }

    // Create assignment
    public function createAssignment(): void
    {
        if (! $this->validateCurrentStep()) {
            return;
        }

        $schoolId = getSchoolId() ?? Auth::user()?->school_id;
        $this->teacher = Teacher::where('user_id', Auth::id())->first();

        // Allow administrators/owners to create public assignments by ensuring a teacher profile exists
        if (! $this->teacher && $schoolId) {
            $this->teacher = Teacher::firstOrCreate(
                ['user_id' => Auth::id()],
                [
                    'school_id' => $schoolId,
                    'status' => 'active',
                ]
            );
        }


        try {
            $data = [
                'title' => $this->title,
                'description' => $this->description,
                'type' => $this->type,
                'instructions' => $this->instructions,
                'duration_in_minutes' => $this->duration_in_minutes,
                'starts_at' => $this->starts_at ? Carbon::parse($this->starts_at) : null,
                'ends_at' => $this->ends_at ? Carbon::parse($this->ends_at) : null,
                'max_attempts' => $this->max_attempts,
                'is_randomized' => $this->is_randomized,
                'result_visibility' => $this->result_visibility,
                'show_correct_answers' => $this->show_correct_answers,
                'show_score_breakdown' => $this->show_score_breakdown,
                'proctoring_enabled' => $this->proctoring_enabled,
                'restrict_navigation' => $this->restrict_navigation,
                'max_tab_switches' => $this->max_tab_switches,
                'auto_submit_on_violation' => $this->auto_submit_on_violation,
                'require_webcam' => $this->require_webcam,
                'require_fullscreen' => $this->require_fullscreen,
            ];

            if ($this->useSections && ! empty($this->sections)) {
                $data['sections'] = $this->sections;
            } else {
                $data['questions'] = $this->questions;
            }

            $assignment = $this->assignmentService->createAssignment($this->teacher, $data);

            session()->flash('success', 'Assignment created successfully! Access code: '.$assignment->access_code);

            $this->redirect(route('teachers.public-assignments.show', $assignment));
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create assignment: '.$e->getMessage());
        }
    }

    public function saveAsDraft(): void
    {
        $this->createAssignment();
    }

    public function publishAssignment(): void
    {

        $schoolId = getSchoolId() ?? Auth::user()?->school_id;
        $this->teacher = Teacher::where('user_id', Auth::id())->first();

        // Allow administrators/owners to create public assignments by ensuring a teacher profile exists
        if (! $this->teacher && $schoolId) {
            $this->teacher = Teacher::firstOrCreate(
                ['user_id' => Auth::id()],
                [
                    'school_id' => $schoolId,
                    'status' => 'active',
                ]
            );
        }

        try {
            $data = $this->prepareAssignmentData();
            $assignment = $this->assignmentService->createAssignment($this->teacher, $data);
            $this->assignmentService->publishAssignment($assignment);

            session()->flash('success', 'Assignment published successfully! Access code: '.$assignment->access_code);

            $this->redirect(route('teachers.public-assignments.show', $assignment));
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to publish assignment: '.$e->getMessage());
        }
    }

    protected function prepareAssignmentData(): array
    {
        $normalizeDifficulty = function ($value): string {
            $value = strtolower($value ?? 'medium');
            return in_array($value, ['easy', 'medium', 'hard'], true) ? $value : 'medium';
        };

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'instructions' => $this->instructions,
            'duration_in_minutes' => $this->duration_in_minutes,
            'starts_at' => $this->starts_at ? Carbon::parse($this->starts_at) : null,
            'ends_at' => $this->ends_at ? Carbon::parse($this->ends_at) : null,
            'max_attempts' => $this->max_attempts,
            'is_randomized' => $this->is_randomized,
            'result_visibility' => $this->result_visibility,
            'show_correct_answers' => $this->show_correct_answers,
            'show_score_breakdown' => $this->show_score_breakdown,
            'proctoring_enabled' => $this->proctoring_enabled,
            'restrict_navigation' => $this->restrict_navigation,
            'max_tab_switches' => $this->max_tab_switches,
            'auto_submit_on_violation' => $this->auto_submit_on_violation,
            'require_webcam' => $this->require_webcam,
            'require_fullscreen' => $this->require_fullscreen,
        ];

        if ($this->useSections && ! empty($this->sections)) {
            $data['sections'] = array_map(function ($section) use ($normalizeDifficulty) {
                $section['questions'] = array_map(function ($q) use ($normalizeDifficulty) {
                    $q['question'] = $this->extractStringFromMark($q['question']);
                    $q['options'] = $q['options'] ?? null;
                    $q['keywords'] = $q['keywords'] ?? null;
                    $q['grading_rubric'] = $q['grading_rubric'] ?? null;
                    $q['difficulty'] = $normalizeDifficulty($q['difficulty'] ?? null);
                    return $q;
                }, $section['questions'] ?? []);
                return $section;
            }, $this->sections);
        } else {
            $data['questions'] = array_map(function ($q) use ($normalizeDifficulty) {
                $q['question'] = $this->extractStringFromMark($q['question']);
                $q['options'] = $q['options'] ?? null;
                $q['keywords'] = $q['keywords'] ?? null;
                $q['grading_rubric'] = $q['grading_rubric'] ?? null;
                $q['difficulty'] = $normalizeDifficulty($q['difficulty'] ?? null);
                return $q;
            }, $this->questions);
        }

        return $data;
    }

    // Step completion checks
    public function isStep1Complete(): bool
    {
        return ! empty($this->title);
    }

    public function isStep2Complete(): bool
    {
        return true;
    }

    public function isStep3Complete(): bool
    {
        return true;
    }

    public function isStep4Complete(): bool
    {
        return $this->getTotalQuestionsCount() > 0;
    }

    public function render()
    {
        return view('livewire.teachers.create-public-assignment', [
            'totalQuestions' => $this->getTotalQuestionsCount(),
            'totalMarks' => $this->getTotalMarks(),
        ]);
    }
}
