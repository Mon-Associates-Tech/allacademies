<?php

namespace App\Livewire\Students;

use App\Models\Assignment;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Assignments extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = 'all';

    public $subjectFilter = 'all';

    public $sortBy = 'created_at';

    public $sortDirection = 'desc';

    public $showDetails = false;

    public $selectedAssignment = null;

    public $student;

    public $unauthorized = false; // New property to track unauthorized access

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'subjectFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount()
    {
        $user = Auth::user();

        $this->student = $user->student;

        if (! $this->student) {
            $this->student = Student::withoutGlobalScopes()->where('user_id', $user->id)->first();
        }

        if (! $this->student) {
            $this->unauthorized = true;
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
        return redirect()->route('students.assignment.take', ['assignment' => $assignment]);
    }

    public function getAssignmentsProperty()
    {
        // Return empty collection if unauthorized
        if (! $this->student || $this->unauthorized) {
            return collect();
        }

        $student = $this->student;

        // Start with basic query
        $query = \App\Models\Assignment::where('status', 'published');
        //            ->where('starts_at', '<=', now());
        //            ->where('ends_at', '>', now());

        // Log the basic query count
        $basicCount = $query->count();
        \Log::info("Basic assignment query count (published, in date range): {$basicCount}");

        // Get student's relationship IDs upfront
        $academicGroupIds = $student->academicGroups?->pluck('id')->toArray() ?? [];
        $studentGroupIds = $student->studentGroups?->pluck('id')->toArray() ?? [];
        $academicLevelId = $student->academic_level_id;

        // Get the academic group ID that the student's academic level belongs to
        // This is important because assignments can be assigned to academic groups,
        // and all students whose academic level belongs to that group should see them
        $academicLevelGroupId = null;
        if ($academicLevelId) {
            $academicLevel = \App\Models\AcademicLevel::find($academicLevelId);
            $academicLevelGroupId = $academicLevel?->academic_group_id;
        }

        \Log::info('Student assignment eligibility check:', [
            'student_id' => $student->id,
            'academic_level_id' => $academicLevelId,
            'academic_level_group_id' => $academicLevelGroupId,
            'academic_group_ids' => $academicGroupIds,
            'student_group_ids' => $studentGroupIds,
        ]);

        // Add the relationship filters - student must match at least one assignment target
        // Using a flag to track if we've added the first condition (to use whereHas vs orWhereHas)
        $query->where(function ($query) use ($student, $academicGroupIds, $studentGroupIds, $academicLevelId, $academicLevelGroupId) {
            $hasCondition = false;

            // Check if assigned to student's academic level
            if ($academicLevelId) {
                $query->whereHas('academicLevels', function ($q) use ($academicLevelId) {
                    $q->where('academic_levels.id', $academicLevelId);
                });
                $hasCondition = true;
            }

            // Check if assigned to the academic group that the student's academic level belongs to
            // This matches the logic in AssignmentNotificationService
            if ($academicLevelGroupId) {
                if ($hasCondition) {
                    $query->orWhereHas('academicGroups', function ($q) use ($academicLevelGroupId) {
                        $q->where('academic_groups.id', $academicLevelGroupId);
                    });
                } else {
                    $query->whereHas('academicGroups', function ($q) use ($academicLevelGroupId) {
                        $q->where('academic_groups.id', $academicLevelGroupId);
                    });
                    $hasCondition = true;
                }
            }

            // Check if assigned to any of student's direct academic groups (many-to-many relationship)
            if (! empty($academicGroupIds)) {
                if ($hasCondition) {
                    $query->orWhereHas('academicGroups', function ($q) use ($academicGroupIds) {
                        $q->whereIn('academic_groups.id', $academicGroupIds);
                    });
                } else {
                    $query->whereHas('academicGroups', function ($q) use ($academicGroupIds) {
                        $q->whereIn('academic_groups.id', $academicGroupIds);
                    });
                    $hasCondition = true;
                }
            }

            // Check if assigned to any of student's student groups
            if (! empty($studentGroupIds)) {
                if ($hasCondition) {
                    $query->orWhereHas('studentGroups', function ($q) use ($studentGroupIds) {
                        $q->whereIn('student_groups.id', $studentGroupIds);
                    });
                } else {
                    $query->whereHas('studentGroups', function ($q) use ($studentGroupIds) {
                        $q->whereIn('student_groups.id', $studentGroupIds);
                    });
                    $hasCondition = true;
                }
            }

            // Check if assigned directly to this student (always check this)
            if ($hasCondition) {
                $query->orWhereHas('students', function ($q) use ($student) {
                    $q->where('students.id', $student->id);
                });
            } else {
                $query->whereHas('students', function ($q) use ($student) {
                    $q->where('students.id', $student->id);
                });
            }
        })
            ->with(['academicSubject', 'teacher.user', 'submissions' => function ($query) use ($student) {
                $query->where('student_id', $student->id);
            }]);

        // Log the final query
        $finalCount = $query->count();
        \Log::info("Final assignment query count (with relationships): {$finalCount}");

        // Get the actual SQL for debugging
        \Log::info('Final SQL Query: '.$query->toSql());
        \Log::info('Query Bindings: ', $query->getBindings());

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
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
        \Log::info('Paginated result count: '.$result->count());

        return $result;
    }

    public function getSubjectsProperty()
    {
        if (! $this->student || $this->unauthorized) {
            return collect();
        }

        return $this->student->getAllAccessibleSubjects();
    }

    public function getAssignmentStatus($assignment)
    {
        if (! $this->student || $this->unauthorized) {
            return 'not_started';
        }

        $submission = $assignment->submissions->where('student_id', $this->student->id)->first();

        if (! $submission) {
            return 'not_started';
        }

        return $submission->status;
    }

    public function getAssignmentProgress($assignment)
    {
        if (! $this->student || $this->unauthorized) {
            return 0;
        }

        $submission = $assignment->submissions->where('student_id', $this->student->id)->first();

        if (! $submission) {
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
        // Show unauthorized view if needed
        if ($this->unauthorized) {
            return view('livewire.students.unauthorized');
        }

        return view('livewire.students.assignments', [
            'assignments' => $this->assignments,
            'subjects' => $this->subjects,
        ]);
    }
}
