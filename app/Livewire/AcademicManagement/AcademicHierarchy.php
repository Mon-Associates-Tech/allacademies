<?php

namespace App\Livewire\AcademicManagement;

use App\Models\AcademicGroup;
use App\Models\EssayQuestion;
use App\Models\MultipleChoiceQuestion;
use App\Models\TrueOrFalseQuestion;
use Livewire\Component;
use Livewire\WithPagination;

class AcademicHierarchy extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $academicGroups = [];

    public $expandedGroups = [];

    public $expandedLevels = [];

    public $expandedSubjects = [];

    public $expandedTopics = [];

    public $expandedSubtopics = [];

    public $activeTab = 'mcq';

    public $selectedSubtopic = null;

    public $selectedTopic = null;

    // Track which topic/subtopic is showing questions
    public $questionsTopicId = null;

    public $questionsSubtopicId = null;

    public function mount()
    {
        $this->academicGroups = AcademicGroup::with([
            'academicLevels.academicSubjects.academicTopics.subtopics',
        ])->get();
    }

    public function toggleGroup($groupId)
    {
        if (in_array($groupId, $this->expandedGroups)) {
            $this->expandedGroups = array_diff($this->expandedGroups, [$groupId]);
        } else {
            $this->expandedGroups[] = $groupId;
        }
    }

    public function toggleLevel($levelId)
    {
        if (in_array($levelId, $this->expandedLevels)) {
            $this->expandedLevels = array_diff($this->expandedLevels, [$levelId]);
        } else {
            $this->expandedLevels[] = $levelId;
        }
    }

    public function toggleSubject($subjectId)
    {
        if (in_array($subjectId, $this->expandedSubjects)) {
            $this->expandedSubjects = array_diff($this->expandedSubjects, [$subjectId]);
        } else {
            $this->expandedSubjects[] = $subjectId;
        }
    }

    public function toggleTopic($topicId)
    {
        if (in_array($topicId, $this->expandedTopics)) {
            $this->expandedTopics = array_diff($this->expandedTopics, [$topicId]);
        } else {
            $this->expandedTopics[] = $topicId;
        }
    }

    public function toggleSubtopic($subtopicId, $topicId)
    {
        if (in_array($subtopicId, $this->expandedSubtopics)) {
            $this->expandedSubtopics = array_diff($this->expandedSubtopics, [$subtopicId]);
        } else {
            $this->expandedSubtopics[] = $subtopicId;
        }

        $this->selectedSubtopic = $subtopicId;
        $this->selectedTopic = $topicId;
        $this->questionsSubtopicId = $subtopicId;
        $this->questionsTopicId = $topicId;
        $this->resetPage();
    }

    public function selectTopic($topicId)
    {
        $this->selectedTopic = $topicId;
        $this->selectedSubtopic = null;
        $this->questionsTopicId = $topicId;
        $this->questionsSubtopicId = null;
        $this->resetPage();
    }

    public function closeQuestions()
    {
        $this->questionsTopicId = null;
        $this->questionsSubtopicId = null;
        $this->selectedTopic = null;
        $this->selectedSubtopic = null;
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function getQuestionsProperty()
    {
        $query = null;

        if ($this->activeTab === 'mcq') {
            $query = MultipleChoiceQuestion::query();
        } elseif ($this->activeTab === 'true_false') {
            $query = TrueOrFalseQuestion::query();
        } else {
            $query = EssayQuestion::query();
        }

        if ($this->questionsSubtopicId) {
            $query->where('academic_subtopic_id', $this->questionsSubtopicId);
        } elseif ($this->questionsTopicId) {
            $query->where('academic_topic_id', $this->questionsTopicId)
                ->whereNull('academic_subtopic_id');
        } else {
            // Return empty paginator if nothing is selected
            return $this->activeTab === 'mcq' ?
                MultipleChoiceQuestion::whereRaw('1 = 0')->paginate(10) :
                ($this->activeTab === 'true_false' ?
                    TrueOrFalseQuestion::whereRaw('1 = 0')->paginate(10) :
                    EssayQuestion::whereRaw('1 = 0')->paginate(10));
        }

        return $query->paginate(5);
    }

    public function getStartingIndex()
    {
        return ($this->questions->currentPage() - 1) * $this->questions->perPage();
    }

    public function render()
    {
        return view('livewire.academic-management.academic-hierarchy');
    }
}
