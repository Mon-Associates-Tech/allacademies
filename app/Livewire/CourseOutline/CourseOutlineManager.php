<?php

namespace App\Livewire\CourseOutline;

use App\Models\AcademicPeriod;
use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Models\CourseOutline;
use App\Models\CourseOutlineItem;
use Livewire\Component;
use Livewire\WithPagination;

class CourseOutlineManager extends Component
{
    use WithPagination;

    // Existing properties for creating outline
    public $selectedSubject;

    public $selectedLevel;

    public $selectedPeriod;

    public $title;

    public $description;

    // For outline items
    public $showItemForm = false;

    public $selectedOutline;

    public $selectedTopic;

    public $selectedSubtopic;

    public $plannedDate;

    public $teachingStrategy;

    public $resourcesNeeded;

    public $learningObjectives;

    public $assessmentMethod;

    public $notes;

    // For new topic/subtopic creation
    public $showNewTopicForm = false;

    public $showNewSubtopicForm = false;

    public $newTopicTitle;

    public $newTopicDescription;

    public $newSubtopicTitle;

    public $newSubtopicDescription;

    // Lists
    public $subjects = [];

    public $levels = [];

    public $periods = [];

    public $topics = [];

    public $subtopics = [];

    protected $rules = [
        'selectedSubject' => 'required|exists:academic_subjects,id',
        'selectedLevel' => 'required|exists:academic_levels,id',
        'selectedPeriod' => 'required|exists:academic_periods,id',
        'title' => 'required|string|max:255',
        'description' => 'required|string',
    ];

    public function mount()
    {
        $this->loadTeacherData();
        $this->loadTopics();
    }

    public function loadTeacherData()
    {
        $teacher = auth()->user()->teacher;

        $this->subjects = $teacher->subjects()
            ->select('academic_subjects.id', 'academic_subjects.name')
            ->get()
            ->toArray();

        $this->levels = $teacher->academicLevels()
            ->select('academic_levels.id', 'academic_levels.name')
            ->get()
            ->toArray();

        $this->periods = AcademicPeriod::where('is_active', true)
            ->select('id', 'name', 'academic_year')
            ->get()
            ->toArray();
    }

    public function loadTopics()
    {
        $this->topics = AcademicTopic::select('id', 'name')
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function openAddItemModal($outlineId): void
    {
        $this->selectedOutline = $outlineId;
        $this->showItemForm = true;
        $this->reset([
            'selectedTopic',
            'selectedSubtopic',
            'plannedDate',
            'teachingStrategy',
            'resourcesNeeded',
            'learningObjectives',
            'assessmentMethod',
            'notes',
        ]);
    }

    public function closeAddItemModal()
    {
        $this->showItemForm = false;
        $this->selectedOutline = null;
        $this->reset([
            'selectedTopic',
            'selectedSubtopic',
            'plannedDate',
            'teachingStrategy',
            'resourcesNeeded',
            'learningObjectives',
            'assessmentMethod',
            'notes',
        ]);
    }

    public function updatedSelectedTopic($value)
    {
        if ($value) {
            $this->subtopics = AcademicSubtopic::where('topic_id', $value)
                ->select('id', 'name')
                ->orderBy('name')
                ->get()
                ->toArray();
        } else {
            $this->subtopics = [];
        }
        $this->selectedSubtopic = null;
    }

    public function createCourseOutline()
    {
        $this->validate();

        $outline = CourseOutline::create([
            'teacher_id' => auth()->user()->teacher->id,
            'academic_subject_id' => $this->selectedSubject,
            'academic_level_id' => $this->selectedLevel,
            'academic_period_id' => $this->selectedPeriod,
            'title' => $this->title,
            'description' => $this->description,
        ]);

        $this->reset(['title', 'description', 'selectedSubject', 'selectedLevel', 'selectedPeriod']);
        session()->flash('message', 'Course outline created successfully.');
    }

    public function createOutlineItem()
    {
        $this->validate([
            'selectedTopic' => 'required|exists:topics,id',
            'plannedDate' => 'required|date',
            'teachingStrategy' => 'nullable|string',
            'resourcesNeeded' => 'nullable|string',
            'learningObjectives' => 'nullable|string',
            'assessmentMethod' => 'nullable|string',
        ]);

        $lastOrder = CourseOutlineItem::where('course_outline_id', $this->selectedOutline)
            ->max('order') ?? 0;

        CourseOutlineItem::create([
            'course_outline_id' => $this->selectedOutline,
            'topic_id' => $this->selectedTopic,
            'subtopic_id' => $this->selectedSubtopic,
            'planned_date' => $this->plannedDate,
            'teaching_strategy' => $this->teachingStrategy,
            'resources_needed' => $this->resourcesNeeded,
            'learning_objectives' => $this->learningObjectives,
            'assessment_method' => $this->assessmentMethod,
            'notes' => $this->notes,
            'order' => $lastOrder + 1,
        ]);

        $this->closeAddItemModal();
        session()->flash('message', 'Outline item added successfully.');
    }

    public function createNewTopic()
    {
        $this->validate([
            'newTopicTitle' => 'required|string|max:255',
            'newTopicDescription' => 'required|string',
        ]);

        $topic = AcademicTopic::create([
            'title' => $this->newTopicTitle,
            'description' => $this->newTopicDescription,
        ]);

        $this->loadTopics();
        $this->selectedTopic = $topic->id;
        $this->reset(['newTopicTitle', 'newTopicDescription']);
        $this->showNewTopicForm = false;
    }

    public function createNewSubtopic()
    {
        $this->validate([
            'selectedTopic' => 'required|exists:topics,id',
            'newSubtopicTitle' => 'required|string|max:255',
            'newSubtopicDescription' => 'required|string',
        ]);

        $subtopic = AcademicSubtopic::create([
            'topic_id' => $this->selectedTopic,
            'title' => $this->newSubtopicTitle,
            'description' => $this->newSubtopicDescription,
        ]);

        $this->updatedSelectedTopic($this->selectedTopic);
        $this->selectedSubtopic = $subtopic->id;
        $this->reset(['newSubtopicTitle', 'newSubtopicDescription']);
        $this->showNewSubtopicForm = false;
    }

    public function render()
    {
        return view('livewire.teachers.course.course-outline-manager', [
            'outlines' => CourseOutline::where('teacher_id', auth()->user()->teacher->id)
                ->with(['outlineItems.topic', 'outlineItems.subtopic', 'academicSubject', 'academicLevel', 'academicPeriod'])
                ->paginate(10),
        ]);
    }
}
