<?php

namespace App\Livewire\Students;

use App\Livewire\Common\HasGlobalMessages;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Assignments extends Component
{
    use WithPagination, HasGlobalMessages;

    public $search = '';
    public $statusFilter = 'all';
    public $subjectFilter = 'all';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $showDetails = false;
    public $selectedAssignment = null;
    public $student;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'subjectFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount()
    {
        $this->student = Auth::user()->student;
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
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function viewAssignment($assignmentId)
    {
        $this->selectedAssignment = Assignment::with(['academicSubject', 'teacher.user'])->find($assignmentId);
        $this->showDetails = true;
    }

    public function closeDetails()
    {
        $this->showDetails = false;
        $this->selectedAssignment = null;
    }

    public function startAssignment($assignmentId)
    {
        // Redirect to assessment page with assignment
        return redirect()->route('student.assessments', ['assignment' => $assignmentId]);
    }

    public function getAssignmentsProperty()
    {
        if (!$this->student) {
            return collect();
        }

        $query = Assignment::where('status', 'published')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>', now())
            ->where(function ($query) {
                $student = $this->student;
                $academicGroupIds = optional($student->academicGroups)?->pluck('id') ?? [];

                $query->whereHas('academicGroups', function ($q) use ($academicGroupIds) {
                    $q->whereIn('academic_groups.id', $academicGroupIds);
                })
                ->orWhereHas('academicLevels', function ($q) use ($student) {
                    $q->where('academic_levels.id', $student->academic_level_id);
                })
                ->orWhereHas('students', function ($q) use ($student) {
                    $q->where('students.id', $student->id);
                });
            })
            ->with(['academicSubject', 'teacher.user', 'submissions' => function ($query) {
                $query->where('student_id', $this->student->id);
            }]);

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Apply subject filter
        if ($this->subjectFilter !== 'all') {
            $query->where('academic_subject_id', $this->subjectFilter);
        }

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            if ($this->statusFilter === 'completed') {
                $query->whereHas('submissions', function ($q) {
                    $q->where('student_id', $this->student->id)
                      ->where('status', 'completed');
                });
            } elseif ($this->statusFilter === 'pending') {
                $query->whereDoesntHave('submissions', function ($q) {
                    $q->where('student_id', $this->student->id)
                      ->where('status', 'completed');
                });
            }
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(12);
    }

    public function getSubjectsProperty()
    {
        if (!$this->student) {
            return collect();
        }

        return $this->student->getAllAccessibleSubjects();
    }

    public function getAssignmentStatus($assignment)
    {
        if (!$this->student) {
            return 'not_started';
        }

        $submission = $assignment->submissions->where('student_id', $this->student->id)->first();

        if (!$submission) {
            return 'not_started';
        }

        return $submission->status;
    }

    public function getAssignmentProgress($assignment)
    {
        if (!$this->student) {
            return 0;
        }

        $submission = $assignment->submissions->where('student_id', $this->student->id)->first();

        if (!$submission) {
            return 0;
        }

        // Calculate progress based on submission status
        switch ($submission->status) {
            case 'completed':
                return 100;
            case 'in_progress':
                return 50; // You can calculate actual progress based on answered questions
            default:
                return 0;
        }
    }

    public function render()
    {
        return view('livewire.students.assignments', [
            'assignments' => $this->assignments,
            'subjects' => $this->subjects,
        ]);
    }
}
