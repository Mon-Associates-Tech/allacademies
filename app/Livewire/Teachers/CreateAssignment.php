<?php

namespace App\Livewire\Teachers;

use App\Models\AcademicSubject;
use App\Models\AcademicTopic;
use App\Models\Assignment;
use App\Models\Student;
use App\Models\StudentGroup;
use App\Models\AcademicSubtopic;
use App\Models\Teacher;
use App\Services\AssignmentNotificationService;
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
        'essay_question' => ['enabled' => false, 'count' => 2, 'difficulty' => 'all']
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
        if (!$this->teacher) return;

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
        if (!empty($this->selectedTopics) && is_array($this->selectedTopics)) {
            $topicIds = $this->selectedTopics;
        }

        if (!empty($topicIds)) {
            $this->availableSubtopics = AcademicSubtopic::whereIn('academic_topic_id', $topicIds)
                ->select('id', 'name', 'academic_topic_id')
                ->with('academicTopic:id,name')
                ->get()
                ->toArray();
        }
    }

    public function createAssignment()
    {
        $this->validate();

        // Validate that at least one question type is enabled
        $hasQuestions = false;
        foreach ($this->questionTypes as $type => $config) {
            if ($config['enabled'] && $config['count'] > 0) {
                $hasQuestions = true;
                break;
            }
        }

        if (!$hasQuestions) {
            session()->flash('error', 'Please enable at least one question type with a count greater than 0.');
            return;
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
            ]);

            // Assign to academic groups
            if (!empty($this->selectedAcademicGroups)) {
                $assignment->academicGroups()->attach($this->selectedAcademicGroups);
            }

            // Assign to academic levels
            if (!empty($this->selectedAcademicLevels)) {
                $assignment->academicLevels()->attach($this->selectedAcademicLevels);
            }

            // Assign to student groups
            if (!empty($this->selectedStudentGroups)) {
                $assignment->studentGroups()->attach($this->selectedStudentGroups);
            }

            // Assign to individual students
            if (!empty($this->selectedStudents)) {
                $assignment->students()->attach($this->selectedStudents);
            }

            // Assign topics and subtopics
            if (!empty($this->selectedTopics)) {
                $assignment->topics()->attach($this->selectedTopics);
            }

            if (!empty($this->selectedSubtopics)) {
                $assignment->subtopics()->attach($this->selectedSubtopics);
            }

            DB::commit();

            // Send notifications
            app(AssignmentNotificationService::class)->sendAssignmentNotifications($assignment);

            session()->flash('success', 'Assignment created successfully and notifications sent!');

            return redirect()->route('teacher.assignments.index');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to create assignment: ' . $e->getMessage());
        }
    }

    private function buildQuestionsConfiguration()
    {
        $questionsConfig = [];

        foreach ($this->questionTypes as $type => $config) {
            if (!$config['enabled'] || $config['count'] <= 0) {
                continue;
            }

            $questionConfig = [
                'type' => $type,
                'count' => (int) $config['count'],
                'difficulty' => $config['difficulty'],
                'topic_ids' => [],
                'subtopic_ids' => [],
                'specific_ids' => []
            ];

            // Add topic/subtopic filters if selected
            if (!empty($this->selectedSubtopics)) {
                $questionConfig['subtopic_ids'] = array_map('intval', $this->selectedSubtopics);
            } elseif (!empty($this->selectedTopics)) {
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

    public function render()
    {
        return view('livewire.teachers.create-assignment');
    }
}
