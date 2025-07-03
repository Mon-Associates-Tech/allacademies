<?php

namespace App\Livewire\Teachers;

use App\Models\Teacher;
use App\Models\Student;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\AcademicSubject;
use App\Models\AcademicLevel;
use App\Models\AcademicGroup;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class StudentPerformances extends Component
{
    use WithPagination;

    public $teacher;
    public $search = '';
    public $selectedSubject = '';
    public $selectedLevel = '';
    public $selectedGroup = '';
    public $selectedStudent = null;
    public $viewMode = 'overview'; // 'overview', 'detailed', 'subject-analysis'
    public $sortBy = 'performance_avg';
    public $sortDirection = 'desc';
    public $showStudentModal = false;
    public $performanceFilter = 'all'; // 'all', 'excellent', 'good', 'needs_improvement'
    public $timeRange = '30'; // days: '7', '30', '90', 'all'

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedSubject' => ['except' => ''],
        'selectedLevel' => ['except' => ''],
        'selectedGroup' => ['except' => ''],
        'viewMode' => ['except' => 'overview'],
        'sortBy' => ['except' => 'performance_avg'],
        'sortDirection' => ['except' => 'desc'],
        'performanceFilter' => ['except' => 'all'],
        'timeRange' => ['except' => '30'],
    ];

    public function mount()
    {
        $this->teacher = Teacher::where('user_id', Auth::id())->first();

        if (!$this->teacher) {
            abort(403, 'Access denied. Teacher profile not found.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedSubject()
    {
        $this->resetPage();
    }

    public function updatedSelectedLevel()
    {
        $this->resetPage();
    }

    public function updatedSelectedGroup()
    {
        $this->resetPage();
    }

    public function updatedPerformanceFilter()
    {
        $this->resetPage();
    }

    public function updatedTimeRange()
    {
        $this->resetPage();
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
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
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->selectedSubject = '';
        $this->selectedLevel = '';
        $this->selectedGroup = '';
        $this->performanceFilter = 'all';
        $this->timeRange = '30';
        $this->sortBy = 'performance_avg';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function showStudentDetails($studentId)
    {
        $this->selectedStudent = Student::with([
            'user',
            'academicLevel.academicGroup',
            'academicSubjects',
            'assessments.assignment.academicSubject'
        ])->findOrFail($studentId);

        $this->showStudentModal = true;
    }

    public function closeStudentModal()
    {
        $this->showStudentModal = false;
        $this->selectedStudent = null;
    }

    public function getStudentsWithPerformance()
    {
        $teacherStudents = $this->getTeacherStudents();
        $studentIds = $teacherStudents->pluck('id');

        if ($studentIds->isEmpty()) {
            return collect();
        }

        // Build time range filter
        $timeFilter = $this->buildTimeFilter();

        // Get student performance data
        $performanceData = AssignmentSubmission::select([
            'student_id',
            DB::raw('COUNT(*) as total_submissions'),
            DB::raw('AVG(CASE WHEN total_marks > 0 THEN (score / total_marks) * 100 ELSE 0 END) as performance_avg'),
            DB::raw('SUM(CASE WHEN status = "submitted" THEN 1 ELSE 0 END) as completed_assignments'),
            DB::raw('SUM(CASE WHEN status = "graded" THEN 1 ELSE 0 END) as graded_assignments'),
            DB::raw('MAX(submitted_at) as last_submission')
        ])
        ->whereIn('student_id', $studentIds)
        ->whereHas('assignment', function ($query) {
            $query->where('teacher_id', $this->teacher->id);
        })
        ->when($timeFilter, function ($query, $timeFilter) {
            $query->where('submitted_at', '>=', $timeFilter);
        })
        ->groupBy('student_id')
        ->get()
        ->keyBy('student_id');

        // Merge with student data
        $studentsWithPerformance = $teacherStudents->map(function ($student) use ($performanceData) {
            $performance = $performanceData->get($student->id);

            $student->performance_avg = $performance ? round($performance->performance_avg, 1) : 0;
            $student->total_submissions = $performance ? $performance->total_submissions : 0;
            $student->completed_assignments = $performance ? $performance->completed_assignments : 0;
            $student->graded_assignments = $performance ? $performance->graded_assignments : 0;
            $student->last_submission = $performance ? $performance->last_submission : null;

            // Calculate performance grade
            $student->performance_grade = $this->calculatePerformanceGrade($student->performance_avg);

            return $student;
        });

        // Apply filters
        $studentsWithPerformance = $this->applyFilters($studentsWithPerformance);

        // Apply sorting
        $studentsWithPerformance = $this->applySorting($studentsWithPerformance);

        return $studentsWithPerformance;
    }

    private function getTeacherStudents()
    {
        $query = Student::with([
            'user',
            'academicLevel.academicGroup',
            'academicSubjects'
        ]);

        // Get all students accessible to this teacher
        $studentIds = collect();

        // From direct assignments
        $directStudents = $this->teacher->assignedStudents()->pluck('students.id');
        $studentIds = $studentIds->merge($directStudents);

        // From academic groups
        foreach ($this->teacher->academicGroups as $group) {
            $groupStudents = Student::whereHas('academicLevel', function ($q) use ($group) {
                $q->where('academic_group_id', $group->id);
            })->pluck('id');
            $studentIds = $studentIds->merge($groupStudents);
        }

        // From academic levels
        foreach ($this->teacher->academicLevels as $level) {
            $levelStudents = Student::where('academic_level_id', $level->id)->pluck('id');
            $studentIds = $studentIds->merge($levelStudents);
        }

        $studentIds = $studentIds->unique();

        return $query->whereIn('id', $studentIds)->get();
    }

    private function buildTimeFilter()
    {
        return match ($this->timeRange) {
            '7' => now()->subDays(7),
            '30' => now()->subDays(30),
            '90' => now()->subDays(90),
            'all' => null,
            default => now()->subDays(30),
        };
    }

    private function calculatePerformanceGrade($average)
    {
        if ($average >= 90) return 'A+';
        if ($average >= 80) return 'A';
        if ($average >= 70) return 'B+';
        if ($average >= 60) return 'B';
        if ($average >= 50) return 'C';
        if ($average >= 40) return 'D';
        return 'F';
    }

    private function applyFilters($students)
    {
        if ($this->search) {
            $students = $students->filter(function ($student) {
                return stripos($student->user->name, $this->search) !== false ||
                       stripos($student->user->email, $this->search) !== false;
            });
        }

        if ($this->selectedSubject) {
            $students = $students->filter(function ($student) {
                return $student->academicSubjects->contains('id', $this->selectedSubject);
            });
        }

        if ($this->selectedLevel) {
            $students = $students->filter(function ($student) {
                return $student->academic_level_id == $this->selectedLevel;
            });
        }

        if ($this->selectedGroup) {
            $students = $students->filter(function ($student) {
                return $student->academicLevel &&
                       $student->academicLevel->academicGroup &&
                       $student->academicLevel->academicGroup->id == $this->selectedGroup;
            });
        }

        if ($this->performanceFilter !== 'all') {
            $students = $students->filter(function ($student) {
                return match ($this->performanceFilter) {
                    'excellent' => $student->performance_avg >= 80,
                    'good' => $student->performance_avg >= 60 && $student->performance_avg < 80,
                    'needs_improvement' => $student->performance_avg < 60,
                    default => true,
                };
            });
        }

        return $students;
    }

    private function applySorting($students)
    {
        $direction = $this->sortDirection === 'asc' ? 1 : -1;

        return $students->sort(function ($a, $b) use ($direction) {
            $aValue = match ($this->sortBy) {
                'name' => $a->user->name,
                'performance_avg' => $a->performance_avg,
                'total_submissions' => $a->total_submissions,
                'completed_assignments' => $a->completed_assignments,
                'last_submission' => $a->last_submission ? $a->last_submission->timestamp : 0,
                default => $a->performance_avg,
            };

            $bValue = match ($this->sortBy) {
                'name' => $b->user->name,
                'performance_avg' => $b->performance_avg,
                'total_submissions' => $b->total_submissions,
                'completed_assignments' => $b->completed_assignments,
                'last_submission' => $b->last_submission ? $b->last_submission->timestamp : 0,
                default => $b->performance_avg,
            };

            if (is_string($aValue) && is_string($bValue)) {
                return strcasecmp($aValue, $bValue) * $direction;
            }

            return ($aValue <=> $bValue) * $direction;
        });
    }

    public function getSubjectAnalysis()
    {
        $subjects = $this->teacher->academicSubjects()
            ->with('academicLevel')
            ->get();

        $analysis = [];

        foreach ($subjects as $subject) {
            $assignments = Assignment::where('teacher_id', $this->teacher->id)
                ->where('academic_subject_id', $subject->id)
                ->get();

            $submissions = AssignmentSubmission::whereIn('assignment_id', $assignments->pluck('id'))
                ->where('status', 'graded')
                ->get();

            $analysis[] = [
                'subject' => $subject,
                'total_assignments' => $assignments->count(),
                'total_submissions' => $submissions->count(),
                'average_score' => $submissions->avg('score') ?: 0,
                'completion_rate' => $assignments->count() > 0 ?
                    ($submissions->count() / ($assignments->count() * $this->getTeacherStudents()->count())) * 100 : 0,
            ];
        }

        return collect($analysis);
    }

    public function render()
    {
        $studentsWithPerformance = $this->getStudentsWithPerformance();

        // Paginate results
        $perPage = 12;
        $page = request()->get('page', 1);
        $total = $studentsWithPerformance->count();
        $items = $studentsWithPerformance->slice(($page - 1) * $perPage, $perPage)->values();

        // Get filter options
        $subjects = $this->teacher->academicSubjects()->with('academicLevel')->get();
        $levels = $this->teacher->academicLevels()->with('academicGroup')->get();
        $groups = $this->teacher->academicGroups()->get();

        // Get subject analysis if needed
        $subjectAnalysis = $this->viewMode === 'subject-analysis' ? $this->getSubjectAnalysis() : collect();

        return view('livewire.teachers.student-performances', [
            'students' => $items,
            'total' => $total,
            'subjects' => $subjects,
            'levels' => $levels,
            'groups' => $groups,
            'subjectAnalysis' => $subjectAnalysis,
            'currentPage' => $page,
            'perPage' => $perPage,
            'hasNextPage' => ($page * $perPage) < $total,
            'hasPrevPage' => $page > 1,
        ]);
    }
}
