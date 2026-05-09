<?php

namespace App\Livewire;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\Examination;
use Livewire\Component;
use Livewire\WithPagination;

class GenerateExamination extends Component
{
    use WithPagination;

    public $groupedSubjects = [];
    public $selectedSubject = null;
    public $searchTerm = '';
    public $filterSubject = '';

    protected $queryString = ['searchTerm', 'filterSubject'];

    public function mount()
    {
        $this->loadGroupedSubjects();
    }

    public function loadGroupedSubjects()
    {
        $groups = AcademicGroup::with(['academicLevels.subjects'])
            ->get()
            ->map(function ($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'tag' => $group->tag,
                    'levels' => $group->academicLevels->map(function ($level) use ($group) {
                        return [
                            'id' => $level->id,
                            'name' => $level->name,
                            'label' => $level->label,
                            'subjects' => $level->subjects->map(function ($subject) use ($group, $level) {
                                return [
                                    'id' => $subject->id,
                                    'name' => $subject->name,
                                    'code' => $subject->code,
                                    'group_id' => $group->id,
                                    'group_name' => $group->name,
                                    'level_id' => $level->id,
                                    'level_name' => $level->name,
                                    'create_url' => route('examinations.create', [
                                        'academic_group' => $group->id,
                                        'academic_level' => $level->id,
                                        'academic_subject' => $subject->id,
                                    ]),
                                ];
                            })->toArray(),
                        ];
                    })->toArray(),
                ];
            })
            ->toArray();

        $this->groupedSubjects = $groups;
    }

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function updatingFilterSubject()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->searchTerm = '';
        $this->filterSubject = '';
        $this->resetPage();
    }

    public function getRecentExaminationsProperty()
    {
        $query = Examination::with(['academicSubject.academicLevel.academicGroup', 'creator', 'team'])
            ->where('creator_id', auth()->id())
            ->latest();

        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->searchTerm . '%')
                    ->orWhereHas('academicSubject', function ($subQuery) {
                        $subQuery->where('name', 'like', '%' . $this->searchTerm . '%')
                            ->orWhere('code', 'like', '%' . $this->searchTerm . '%');
                    });
            });
        }

        if ($this->filterSubject) {
            $query->where('academic_subject_id', $this->filterSubject);
        }

        return $query->paginate(10);
    }

    public function getSubjectsForFilterProperty()
    {
        return AcademicSubject::whereHas('examinations', function ($query) {
            $query->where('creator_id', auth()->id());
        })
            ->with('academicLevel.academicGroup')
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.generate-examination', [
            'recentExaminations' => $this->recentExaminations,
            'subjectsForFilter' => $this->subjectsForFilter,
        ]);
    }
}
