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

        // Add debugging
        if ($this->student) {
            \Log::info('=== Student Assignment Debug ===');
            \Log::info('Student ID: ' . $this->student->id);
            \Log::info('Student User ID: ' . $this->student->user_id);
            \Log::info('Student Academic Level ID: ' . ($this->student->academic_level_id ?? 'NULL'));

            // Check academic groups
            $academicGroups = $this->student->academicGroups ?? collect();
            \Log::info('Student Academic Groups: ', $academicGroups->pluck('id')->toArray());

            // Check student groups
            $studentGroups = $this->student->studentGroups ?? collect();
            \Log::info('Student Groups: ', $studentGroups->pluck('id')->toArray());

            // Check all published assignments
            $allAssignments = \App\Models\Assignment::where('status', 'published')->get();
            \Log::info('Total published assignments: ' . $allAssignments->count());

            foreach ($allAssignments as $assignment) {
                \Log::info('Assignment ID: ' . $assignment->id . ' - Title: ' . $assignment->title);
                \Log::info('  - Academic Groups: ', $assignment->academicGroups->pluck('id')->toArray());
                \Log::info('  - Academic Levels: ', $assignment->academicLevels->pluck('id')->toArray());
                \Log::info('  - Student Groups: ', $assignment->studentGroups->pluck('id')->toArray());
                \Log::info('  - Direct Students: ', $assignment->students->pluck('id')->toArray());
                \Log::info('  - Start: ' . $assignment->starts_at . ' - End: ' . $assignment->ends_at);
            }
        } else {
            \Log::info('No student found for user: ' . Auth::id());
        }
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

    public function startAssignment(Assignment $assignment)
    {
        \Log::warning('lets  go');
        return redirect()->route('student.assignment.take', ['assignment' => $assignment]);
    }


    public function getAssignmentsProperty()
    {
        if (!$this->student) {
            \Log::info('No student available for assignments query');
            return collect();
        }

        $student = $this->student;

        // Start with basic query
        $query = \App\Models\Assignment::where('status', 'published')
//            ->where('starts_at', '<=', now())
            ->where('ends_at', '>', now());

        // Log the basic query count
        $basicCount = $query->count();
        \Log::info("Basic assignment query count (published, in date range): {$basicCount}");

        // Add the relationship filters
        $query->where(function ($query) use ($student) {
            // Get student's academic group IDs
            $academicGroupIds = $student->academicGroups?->pluck('id')->toArray() ?? [];
            \Log::info('Checking academic groups: ', $academicGroupIds);

            // Check if assigned to any of student's academic groups
            if (!empty($academicGroupIds)) {
                $query->orWhereHas('academicGroups', function ($q) use ($academicGroupIds) {
                    $q->whereIn('academic_groups.id', $academicGroupIds);
                });
            }

            // Check if assigned to student's academic level
            if ($student->academic_level_id) {
                \Log::info('Checking academic level: ' . $student->academic_level_id);
                $query->orWhereHas('academicLevels', function ($q) use ($student) {
                    $q->where('academic_levels.id', $student->academic_level_id);
                });
            }

            // Check if assigned to any of student's groups
            $studentGroupIds = $student->studentGroups?->pluck('id')->toArray() ?? [];
            \Log::info('Checking student groups: ', $studentGroupIds);
            if (!empty($studentGroupIds)) {
                $query->orWhereHas('studentGroups', function ($q) use ($studentGroupIds) {
                    $q->whereIn('student_groups.id', $studentGroupIds);
                });
            }

            // Check if assigned directly to this student
            \Log::info('Checking direct student assignment for ID: ' . $student->id);
            $query->orWhereHas('students', function ($q) use ($student) {
                $q->where('students.id', $student->id);
            });
        })
        ->with(['academicSubject', 'teacher.user', 'submissions' => function ($query) use ($student) {
            $query->where('student_id', $student->id);
        }]);

        // Log the final query
        $finalCount = $query->count();
        \Log::info("Final assignment query count (with relationships): {$finalCount}");

        // Get the actual SQL for debugging
        \Log::info('Final SQL Query: ' . $query->toSql());
        \Log::info('Query Bindings: ', $query->getBindings());

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
                $query->whereHas('submissions', function ($q) use ($student) {
                    $q->where('student_id', $student->id)
                      ->whereIn('status', ['completed', 'submitted', 'graded']);
                });
            } elseif ($this->statusFilter === 'pending') {
                $query->whereDoesntHave('submissions', function ($q) use ($student) {
                    $q->where('student_id', $student->id)
                      ->whereIn('status', ['completed', 'submitted', 'graded']);
                });
            }
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        $result = $query->paginate(12);
        \Log::info('Paginated result count: ' . $result->count());

        return $result;
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
            case 'submitted':
            case 'graded':
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
