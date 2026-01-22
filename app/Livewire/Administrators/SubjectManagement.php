<?php

namespace App\Livewire\Administrators;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject as Subject;
use App\Models\AcademicTopic as Topic;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class SubjectManagement extends Component
{
    use WithPagination;

    // For Subject
    public $name;

    public $slug;

    public $description;

    public $academicLevelId;

    // For Topic
    public $topicName;

    public $topicSlug;

    public $topicDescription;

    public $subjectId;

    public $subjectCode;

    // For Subtopic
    public $subtopicName;

    public $subtopicSlug;

    public $subtopicDescription;

    public $topicId;

    // Filter and search
    public $academicGroupId;

    public $searchTerm = '';

    // UI states
    public $isEditingSubject = false;

    public $isEditingTopic = false;

    public $isEditingSubtopic = false;

    public $editingSubjectId;

    public $editingTopicId;

    public $editingSubtopicId;

    public $viewingSubjectId;

    public $viewingTopicId;

    public $showTopicForm = false;

    public $showSubtopicForm = false;

    // Data arrays
    public $academicGroups;

    public $academicLevels = [];

    public $subjects = [];

    public $topics = [];

    public $subtopics = [];

    protected $subjectRules = [
        'name' => 'required|min:2',
        'description' => 'nullable|string',
        'academicLevelId' => 'required|exists:academic_levels,id',
        'subjectCode' => 'required|min:2',
    ];

    protected $topicRules = [
        'topicName' => 'required|min:2',
        'topicDescription' => 'nullable|string',
        'subjectId' => 'required|exists:academic_subjects,id',
    ];

    protected $subtopicRules = [
        'subtopicName' => 'required|min:2',
        'subtopicDescription' => 'nullable|string',
        'topicId' => 'required|exists:academic_topics,id',
    ];

    public function mount()
    {
        $this->academicGroups = AcademicGroup::all();
        if ($this->academicGroups->isNotEmpty()) {
            $this->academicGroupId = $this->academicGroups->first()->id;
            $this->loadAcademicLevels();
        }
    }

    public function loadAcademicLevels()
    {
        if ($this->academicGroupId) {
            $this->academicLevels = AcademicLevel::where('academic_group_id', $this->academicGroupId)->get();
            if ($this->academicLevels->isNotEmpty()) {
                $this->academicLevelId = $this->academicLevels->first()->id;
            } else {
                $this->academicLevelId = null;
            }
        }
    }

    public function updatedAcademicGroupId()
    {
        $this->loadAcademicLevels();
    }

    public function updatedName()
    {
        $this->slug = Str::slug($this->name);
    }

    public function updatedTopicName()
    {
        $this->topicSlug = Str::slug($this->topicName);
    }

    public function updatedSubtopicName()
    {
        $this->subtopicSlug = Str::slug($this->subtopicName);
    }

    // Subject CRUD

    public function createSubject()
    {

        $this->validate($this->subjectRules);

        $uniqueSlug = $this->generateUniqueSlug($this->slug, 'subject');

        Subject::create([
            'name' => $this->name,
            'slug' => $uniqueSlug,
            'code' => $this->subjectCode,
            'description' => $this->description,
            'academic_level_id' => $this->academicLevelId,
        ]);

        $this->resetSubjectForm();
        session()->flash('message', 'Subject created successfully!');
    }

    public function editSubject($subjectId)
    {
        $this->isEditingSubject = true;
        $this->editingSubjectId = $subjectId;

        $subject = Subject::findOrFail($subjectId);
        $this->name = $subject->name;
        $this->slug = $subject->slug;
        $this->subjectCode = $subject->code;
        $this->description = $subject->description;
        $this->academicLevelId = $subject->academic_level_id;

        // Make sure the academic group is set correctly
        $academicLevel = AcademicLevel::find($subject->academic_level_id);
        if ($academicLevel) {
            $this->academicGroupId = $academicLevel->academic_group_id;
            $this->loadAcademicLevels();
        }
    }

    public function updateSubject()
    {
        $this->validate($this->subjectRules);

        $subject = Subject::findOrFail($this->editingSubjectId);

        // Only regenerate slug if name has changed
        $slug = $this->slug;
        if ($subject->name !== $this->name) {
            $slug = $this->generateUniqueSlug($this->slug, 'subject', $this->editingSubjectId);
        }

        $subject->update([
            'name' => $this->name,
            'slug' => $slug,
            'code' => $this->subjectCode,
            'description' => $this->description,
            'academic_level_id' => $this->academicLevelId,
        ]);

        $this->resetSubjectForm();
        session()->flash('message', 'Subject updated successfully!');
    }

    public function deleteSubject($subjectId)
    {
        $subject = Subject::findOrFail($subjectId);

        // Check if subject has topics
        if ($subject->topics()->count() > 0) {
            session()->flash('error', 'Cannot delete subject with topics. Please remove those first.');

            return;
        }

        $subject->delete();
        session()->flash('message', 'Subject deleted successfully!');
    }

    // Topic CRUD

    public function viewSubjectTopics($subjectId)
    {
        $this->viewingSubjectId = $subjectId;
        $this->subjectId = $subjectId;
        $this->viewingTopicId = null;
        $this->resetTopicForm();
        $this->resetSubtopicForm();
        $this->topics = Topic::where('academic_subject_id', $subjectId)
            ->withCount('subtopics')
            ->get();
    }

    public function showCreateTopicForm()
    {
        $this->resetTopicForm();
        $this->showTopicForm = true;
        $this->isEditingTopic = false;
    }

    public function createTopic()
    {
        $this->validate($this->topicRules);

        $uniqueSlug = $this->generateUniqueSlug($this->topicSlug, 'topic');

        Topic::create([
            'name' => $this->topicName,
            'slug' => $uniqueSlug,
            'description' => $this->topicDescription,
            'academic_subject_id' => $this->subjectId,
        ]);

        $this->resetTopicForm();
        $this->topics = Topic::where('academic_subject_id', $this->subjectId)
            ->withCount('subtopics')
            ->get();
        session()->flash('message', 'Topic created successfully!');
    }

    public function editTopic($topicId)
    {
        $this->isEditingTopic = true;
        $this->editingTopicId = $topicId;
        $this->showTopicForm = true;

        $topic = Topic::findOrFail($topicId);
        $this->topicName = $topic->name;
        $this->topicSlug = $topic->slug;
        $this->topicDescription = $topic->description;
        $this->subjectId = $topic->subject_id;
    }

    public function updateTopic()
    {
        $this->validate($this->topicRules);

        $topic = Topic::findOrFail($this->editingTopicId);

        // Only regenerate slug if name has changed
        $slug = $this->topicSlug;
        if ($topic->name !== $this->topicName) {
            $slug = $this->generateUniqueSlug($this->topicSlug, 'topic', $this->editingTopicId);
        }

        $topic->update([
            'name' => $this->topicName,
            'slug' => $slug,
            'description' => $this->topicDescription,
        ]);

        $this->resetTopicForm();
        $this->topics = Topic::where('academic_subject_id', $this->subjectId)
            ->withCount('subtopics')
            ->get();
        session()->flash('message', 'Topic updated successfully!');
    }

    public function deleteTopic($topicId)
    {
        $topic = Topic::findOrFail($topicId);

        // Check if topic has subtopics
        if ($topic->subtopics()->count() > 0) {
            session()->flash('error', 'Cannot delete topic with subtopics. Please remove those first.');

            return;
        }

        $topic->delete();
        $this->topics = Topic::where('academic_subject_id', $this->subjectId)
            ->withCount('subtopics')
            ->get();
        session()->flash('message', 'Topic deleted successfully!');
    }

    // Subtopic CRUD

    public function viewTopicSubtopics($topicId)
    {
        $this->viewingTopicId = $topicId;
        $this->topicId = $topicId;
        $this->resetSubtopicForm();
        $this->subtopics = Topic::findOrFail($topicId)->subtopics;
    }

    public function showCreateSubtopicForm()
    {
        $this->resetSubtopicForm();
        $this->showSubtopicForm = true;
        $this->isEditingSubtopic = false;
    }

    public function createSubtopic()
    {
        $this->validate($this->subtopicRules);

        $uniqueSlug = $this->generateUniqueSlug($this->subtopicSlug, 'subtopic');

        $topic = Topic::findOrFail($this->topicId);
        $topic->subtopics()->create([
            'name' => $this->subtopicName,
            'slug' => $uniqueSlug,
            'description' => $this->subtopicDescription,
        ]);

        $this->resetSubtopicForm();
        $this->subtopics = Topic::findOrFail($this->topicId)->subtopics;
        session()->flash('message', 'Subtopic created successfully!');
    }

    public function editSubtopic($subtopicId)
    {
        $this->isEditingSubtopic = true;
        $this->editingSubtopicId = $subtopicId;
        $this->showSubtopicForm = true;

        $topic = Topic::findOrFail($this->topicId);
        $subtopic = $topic->subtopics()->findOrFail($subtopicId);

        $this->subtopicName = $subtopic->name;
        $this->subtopicSlug = $subtopic->slug;
        $this->subtopicDescription = $subtopic->description;
    }

    public function updateSubtopic()
    {
        $this->validate($this->subtopicRules);

        $topic = Topic::findOrFail($this->topicId);
        $subtopic = $topic->subtopics()->findOrFail($this->editingSubtopicId);

        // Only regenerate slug if name has changed
        $slug = $this->subtopicSlug;
        if ($subtopic->name !== $this->subtopicName) {
            $slug = $this->generateUniqueSlug($this->subtopicSlug, 'subtopic', $this->editingSubtopicId);
        }

        $subtopic->update([
            'name' => $this->subtopicName,
            'slug' => $slug,
            'description' => $this->subtopicDescription,
        ]);

        $this->resetSubtopicForm();
        $this->subtopics = Topic::findOrFail($this->topicId)->subtopics;
        session()->flash('message', 'Subtopic updated successfully!');
    }

    public function deleteSubtopic($subtopicId)
    {
        $topic = Topic::findOrFail($this->topicId);
        $subtopic = $topic->subtopics()->findOrFail($subtopicId);

        // Check if subtopic has lessons
        if ($subtopic->lessons()->count() > 0) {
            session()->flash('error', 'Cannot delete subtopic with lessons. Please remove those first.');

            return;
        }

        $subtopic->delete();
        $this->subtopics = Topic::findOrFail($this->topicId)->subtopics;
        session()->flash('message', 'Subtopic deleted successfully!');
    }

    // Helper methods

    private function generateUniqueSlug($slug, $type, $excludeId = null)
    {
        $originalSlug = $slug;
        $counter = 1;
        $exists = true;

        while ($exists) {
            switch ($type) {
                case 'subject':
                    $query = Subject::where('slug', $slug);
                    if ($excludeId) {
                        $query->where('id', '!=', $excludeId);
                    }
                    $exists = $query->exists();
                    break;

                case 'topic':
                    $query = Topic::where('slug', $slug);
                    if ($excludeId) {
                        $query->where('id', '!=', $excludeId);
                    }
                    $exists = $query->exists();
                    break;

                case 'subtopic':
                    $topic = Topic::find($this->topicId);
                    if (! $topic) {
                        return $slug;
                    }

                    $query = $topic->subtopics()->where('slug', $slug);
                    if ($excludeId) {
                        $query->where('id', '!=', $excludeId);
                    }
                    $exists = $query->exists();
                    break;

                default:
                    return $slug;
            }

            if ($exists) {
                $slug = $originalSlug.'-'.$counter++;
            }
        }

        return $slug;
    }

    public function backToSubjects()
    {
        $this->viewingSubjectId = null;
        $this->viewingTopicId = null;
        $this->resetTopicForm();
        $this->resetSubtopicForm();
    }

    public function backToTopics()
    {
        $this->viewingTopicId = null;
        $this->resetSubtopicForm();
    }

    public function resetSubjectForm()
    {
        $this->name = '';
        $this->slug = '';
        $this->subjectCode = '';
        $this->description = '';
        $this->isEditingSubject = false;
        $this->editingSubjectId = null;
        $this->resetValidation();
        $this->js('window.Modal.close("subject-management-form")');
    }

    public function resetTopicForm()
    {
        $this->topicName = '';
        $this->topicSlug = '';
        $this->topicDescription = '';
        $this->isEditingTopic = false;
        $this->editingTopicId = null;
        $this->showTopicForm = false;
        $this->resetValidation();
    }

    public function resetSubtopicForm()
    {
        $this->subtopicName = '';
        $this->subtopicSlug = '';
        $this->subtopicDescription = '';
        $this->isEditingSubtopic = false;
        $this->editingSubtopicId = null;
        $this->showSubtopicForm = false;
        $this->resetValidation();
    }

    public function render()
    {
        // If viewing a specific subject or topic, don't query all subjects
        if ($this->viewingSubjectId || $this->viewingTopicId) {
            return view('livewire.administrators.subject-management');
        }

        //        $subjectsQuery = Subject::with(['academicLevel.academicGroup'])
        //      ->withCount(['topics', 'lessons']);

        $subjectsQuery = Subject::with(['academicLevel.academicGroup']);

        // Filter by academic level if set
        if ($this->academicLevelId) {
            $subjectsQuery->where('academic_level_id', $this->academicLevelId);
        } elseif ($this->academicGroupId) {
            // Filter by academic group if the level is not set
            $subjectsQuery->whereHas('academicLevel', function ($query) {
                $query->where('academic_group_id', $this->academicGroupId);
            });
        }

        // Search
        if ($this->searchTerm) {
            $subjectsQuery->where(function ($query) {
                $query->where('name', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('description', 'like', '%'.$this->searchTerm.'%');
            });
        }

        $subjects = $subjectsQuery->paginate(10);

        return view('livewire.administrators.subject-management', [
            'subjectsList' => $subjects,
        ]);
    }
}
