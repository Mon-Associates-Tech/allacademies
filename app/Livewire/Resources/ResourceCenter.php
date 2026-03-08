<?php

namespace App\Livewire\Resources;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Models\EducationalResource;
use Livewire\Component;
use Livewire\WithPagination;

class ResourceCenter extends Component
{
    use WithPagination;

    public string $search = '';

    public string $tagSearch = '';

    public ?string $format = null;

    public ?int $academicGroupId = null;

    public ?int $academicLevelId = null;

    public ?int $academicSubjectId = null;

    public ?int $topicId = null;

    public ?int $subtopicId = null;

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 12;

    public string $viewMode = 'grid';

    protected $queryString = [
        'search' => ['except' => ''],
        'tagSearch' => ['except' => ''],
        'format' => ['except' => null],
        'academicGroupId' => ['except' => null],
        'academicLevelId' => ['except' => null],
        'academicSubjectId' => ['except' => null],
        'topicId' => ['except' => null],
        'subtopicId' => ['except' => null],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'viewMode' => ['except' => 'grid'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTagSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFormat(): void
    {
        $this->resetPage();
    }

    public function updatedAcademicGroupId(): void
    {
        $this->academicLevelId = null;
        $this->academicSubjectId = null;
        $this->topicId = null;
        $this->subtopicId = null;
        $this->resetPage();
    }

    public function updatedAcademicLevelId(): void
    {
        $this->academicSubjectId = null;
        $this->topicId = null;
        $this->subtopicId = null;
        $this->resetPage();
    }

    public function updatedAcademicSubjectId(): void
    {
        $this->topicId = null;
        $this->subtopicId = null;
        $this->resetPage();
    }

    public function updatedTopicId(): void
    {
        $this->subtopicId = null;
        $this->resetPage();
    }

    public function updatedSubtopicId(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset([
            'search',
            'tagSearch',
            'format',
            'academicGroupId',
            'academicLevelId',
            'academicSubjectId',
            'topicId',
            'subtopicId',
        ]);
        $this->resetPage();
    }

    public function setSort(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'desc';
        }
        $this->resetPage();
    }

    public function setViewMode(string $mode): void
    {
        $this->viewMode = $mode;
    }

    public function canUpload(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->hasAnyRole(['admin', 'owner', 'teacher', 'super-admin', 'moderator']);
    }

    public function render()
    {
        $user = auth()->user();

        $query = EducationalResource::query()
            ->with(['academicSubject.academicLevel.academicGroup', 'uploader', 'topics', 'subtopics'])
            ->active()
            ->accessibleBy($user);

        // Apply search filter
        if (! empty($this->search)) {
            $query->search($this->search);
        }

        // Apply tag search filter
        if (! empty($this->tagSearch)) {
            $tags = array_filter(array_map('trim', explode(',', $this->tagSearch)));
            if (! empty($tags)) {
                $query->withTags($tags);
            }
        }

        // Apply format filter
        if ($this->format) {
            $query->byFormat($this->format);
        }

        // Apply academic hierarchy filters
        if ($this->academicGroupId) {
            $query->byAcademicGroup($this->academicGroupId);
        }

        if ($this->academicLevelId) {
            $query->byAcademicLevel($this->academicLevelId);
        }

        if ($this->academicSubjectId) {
            $query->bySubject($this->academicSubjectId);
        }

        if ($this->topicId) {
            $query->byTopic($this->topicId);
        }

        if ($this->subtopicId) {
            $query->bySubtopic($this->subtopicId);
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        $resources = $query->paginate($this->perPage);

        // Get filter options - show all academic groups without school restriction
        $academicGroups = AcademicGroup::query()
            ->orderBy('name')
            ->get();

        $academicLevels = $this->academicGroupId
            ? AcademicLevel::where('academic_group_id', $this->academicGroupId)->orderBy('name')->get()
            : collect();

        $academicSubjects = $this->academicLevelId
            ? AcademicSubject::where('academic_level_id', $this->academicLevelId)->orderBy('name')->get()
            : collect();

        $topics = $this->academicSubjectId
            ? AcademicTopic::where('academic_subject_id', $this->academicSubjectId)->orderBy('name')->get()
            : collect();

        $subtopics = $this->topicId
            ? AcademicSubtopic::where('academic_topic_id', $this->topicId)->orderBy('name')->get()
            : collect();

        $formats = ['video', 'pdf', 'image', 'text'];

        return view('livewire.resources.resource-center', [
            'resources' => $resources,
            'academicGroups' => $academicGroups,
            'academicLevels' => $academicLevels,
            'academicSubjects' => $academicSubjects,
            'topics' => $topics,
            'subtopics' => $subtopics,
            'formats' => $formats,
        ]);
    }
}
