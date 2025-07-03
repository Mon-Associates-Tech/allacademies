<?php

namespace App\Livewire\Teachers;

use App\Models\Assignment;
use App\Models\Teacher;
use App\Models\AcademicSubject;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Assignments extends Component
{
    use WithPagination;

    public $teacher;
    public $search = '';
    public $statusFilter = 'all';
    public $subjectFilter = 'all';
    public $typeFilter = 'all';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 12;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'subjectFilter' => ['except' => 'all'],
        'typeFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount()
    {
        $this->teacher = Teacher::where('user_id', Auth::id())->first();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingSubjectFilter()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->subjectFilter = 'all';
        $this->typeFilter = 'all';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function deleteAssignment($assignmentId)
    {
        $assignment = Assignment::findOrFail($assignmentId);

        // Check if the assignment belongs to the current teacher
        if ($assignment->teacher_id !== $this->teacher->id) {
            $this->dispatch('error', 'You are not authorized to delete this assignment.');
            return;
        }

        $assignment->delete();
        $this->dispatch('success', 'Assignment deleted successfully.');
    }

    public function duplicateAssignment($assignmentId)
    {
        $assignment = Assignment::findOrFail($assignmentId);

        if ($assignment->teacher_id !== $this->teacher->id) {
            $this->dispatch('error', 'You are not authorized to duplicate this assignment.');
            return;
        }

        $duplicated = $assignment->replicate();
        $duplicated->title = $assignment->title . ' (Copy)';
        $duplicated->status = 'draft';
        $duplicated->created_at = now();
        $duplicated->save();

        $this->dispatch('success', 'Assignment duplicated successfully.');
    }

    public function getAssignmentsProperty()
    {
        $query = Assignment::query()
            ->where('teacher_id', $this->teacher->id)
            ->with(['academicSubject', 'students', 'academicLevels', 'submissions']);

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Apply subject filter
        if ($this->subjectFilter !== 'all') {
            $query->where('academic_subject_id', $this->subjectFilter);
        }

        // Apply type filter
        if ($this->typeFilter !== 'all') {
            $query->where('type', $this->typeFilter);
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    public function getSubjectsProperty()
    {
        return $this->teacher->academicSubjects()->get();
    }

    public function render()
    {
        return view('livewire.teachers.assignments', [
            'assignments' => $this->assignments,
            'subjects' => $this->subjects,
        ]);
    }
}
