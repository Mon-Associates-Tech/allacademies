<?php

namespace App\Livewire\Teachers;

use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Subjects extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $selectedSubject = null;
    public $showSubjectModal = false;
    public $teacher;
    public $viewMode = 'grid'; // grid or list view
    public $sortBy = 'name';
    public $sortDirection = 'asc';
    public $filterByLevel = '';
    public $filterByGroup = '';

    public function mount()
    {
        $this->teacher = Auth::user()->teacher;

        if (!$this->teacher) {
            abort(403, 'Access denied. Teacher profile not found.');
        }
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function updatedFilterByLevel()
    {
        $this->resetPage();
    }

    public function updatedFilterByGroup()
    {
        $this->resetPage();
    }

    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'grid' ? 'list' : 'grid';
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function showSubjectDetails($subjectId)
    {
        $this->selectedSubject = $this->teacher->academicSubjects()
            ->with([
                'academicLevel.academicGroup',
                'lessons',
                'academicTopics',
                'quizzes',
                'examinations'
            ])
            ->findOrFail($subjectId);

        $this->showSubjectModal = true;
    }

    public function closeSubjectModal()
    {
        $this->showSubjectModal = false;
        $this->selectedSubject = null;
    }

    public function render()
    {
        $query = $this->teacher->academicSubjects()
            ->with([
                'academicLevel.academicGroup',
                'lessons',
                'academicTopics',
                'quizzes',
                'examinations'
            ]);

        // Apply filters
        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('code', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $this->searchTerm . '%')
                    ->orWhereHas('academicLevel', function($levelQuery) {
                        $levelQuery->where('name', 'like', '%' . $this->searchTerm . '%');
                    })
                    ->orWhereHas('academicLevel.academicGroup', function($groupQuery) {
                        $groupQuery->where('name', 'like', '%' . $this->searchTerm . '%');
                    });
            });
        }

        if ($this->filterByLevel) {
            $query->where('academic_level_id', $this->filterByLevel);
        }

        if ($this->filterByGroup) {
            $query->whereHas('academicLevel', function($levelQuery) {
                $levelQuery->where('academic_group_id', $this->filterByGroup);
            });
        }

        // Apply sorting
        switch ($this->sortBy) {
            case 'level':
                $query->join('academic_levels', 'academic_subjects.academic_level_id', '=', 'academic_levels.id')
                      ->orderBy('academic_levels.name', $this->sortDirection)
                      ->select('academic_subjects.*');
                break;
            case 'group':
                $query->join('academic_levels', 'academic_subjects.academic_level_id', '=', 'academic_levels.id')
                      ->join('academic_groups', 'academic_levels.academic_group_id', '=', 'academic_groups.id')
                      ->orderBy('academic_groups.name', $this->sortDirection)
                      ->select('academic_subjects.*');
                break;
            default:
                $query->orderBy($this->sortBy, $this->sortDirection);
                break;
        }

        $subjects = $query->paginate(12);

        // Get teacher's academic groups and levels for overview and filters
        $academicGroups = $this->teacher->academicGroups()->with('academicLevels')->get();
        $academicLevels = $this->teacher->academicLevels()->with('academicGroup')->get();

        // Get available filter options
        $availableGroups = $this->teacher->academicGroups()->orderBy('name')->get();
        $availableLevels = $this->teacher->academicLevels()->orderBy('name')->get();

        return view('livewire.teachers.subjects', [
            'subjects' => $subjects,
            'academicGroups' => $academicGroups,
            'academicLevels' => $academicLevels,
            'availableGroups' => $availableGroups,
            'availableLevels' => $availableLevels,
            'teacherName' => $this->teacher->user->name
        ]);
    }
}
