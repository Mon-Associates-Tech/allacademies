<?php

namespace App\Livewire\Parent;

use App\Livewire\AppComponent;
use App\Models\Student;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\AcademicSubject;
use App\Models\StudentParent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

class ParentWardPerformance extends AppComponent
{
    use WithPagination;

    public $selectedWardId = null;
    public $selectedSubjectId = null;
    public $selectedPeriod = 'all';
    public $selectedAssignmentType = 'all';
    public $viewMode = 'overview'; // overview, detailed, analytics
    public $searchTerm = '';
    public $sortBy = 'submitted_at';
    public $sortDirection = 'desc';
    public $availablePeriods = ['all', 'week', 'month', 'quarter', 'year'];
    public $dateRange = null;

    public function mount()
    {
        $wards = $this->wards;
        if ($wards->isNotEmpty()) {
            $this->selectedWardId = $wards->first()->id;
        }
    }

    public function selectWard($wardId)
    {
        $this->selectedWardId = $wardId;
        $this->selectedSubjectId = null;
        $this->resetPage();
    }

    public function selectSubject($subjectId)
    {
        $this->selectedSubjectId = $subjectId;
        $this->resetPage();
    }

    public function changeViewMode($mode)
    {
        $this->viewMode = $mode;
        $this->resetPage();
    }

    #[Computed]
    public function wards()
    {
        $students = StudentParent::withoutGlobalScopes()
            ->where('user_id', Auth::id())
            ->with([
                'students' => function($query) {
                    $query->withoutGlobalScopes();
                },
                'students.user',
                'students.academicLevel.academicGroup',
                'students.studentGroup'
            ])
            ->get()
            ->flatMap(function($parent) {
                return $parent->students;
            })
            ->unique('id');

        if ($this->searchTerm) {
            $students = $students->filter(function($student) {
                return stripos($student->user->name, $this->searchTerm) !== false ||
                    stripos($student->academicLevel->name ?? '', $this->searchTerm) !== false ||
                    stripos($student->academicLevel->academicGroup->name ?? '', $this->searchTerm) !== false;
            });
        }

        return $students->sortBy($this->sortBy === 'name' ? 'user.name' : $this->sortBy,
            SORT_REGULAR, $this->sortDirection === 'desc');
    }

    #[Computed]
    public function selectedWard()
    {
        if (!$this->selectedWardId) return null;

        return Student::withoutGlobalScopes()
            ->with([
                'academicLevel.academicGroup',
                'user'
            ])->find($this->selectedWardId);
    }

    #[Computed]
    public function availableSubjects()
    {
        if (!$this->selectedWard) return collect();

        return $this->selectedWard->getAllAccessibleSubjects();
    }

    #[Computed]
    public function assignmentSubmissions()
    {
        if (!$this->selectedWardId) return collect();

        $query = AssignmentSubmission::where('student_id', $this->selectedWardId)
            ->with(['assignment.academicSubject', 'assignment.teacher.user'])
            ->whereIn('status', ['submitted', 'graded']);

        if ($this->selectedSubjectId) {
            $query->whereHas('assignment', function($q) {
                $q->where('academic_subject_id', $this->selectedSubjectId);
            });
        }

        if ($this->selectedAssignmentType !== 'all') {
            $query->whereHas('assignment', function($q) {
                $q->where('type', $this->selectedAssignmentType);
            });
        }

        if ($this->selectedPeriod !== 'all') {
            $date = match($this->selectedPeriod) {
                'week' => now()->subWeek(),
                'month' => now()->subMonth(),
                'quarter' => now()->subMonths(3),
                'year' => now()->subYear(),
                default => null
            };

            if ($date) {
                $query->where('submitted_at', '>=', $date);
            }
        }

        return $query->orderBy($this->sortBy, $this->sortDirection)->paginate(10);
    }

    #[Computed]
    public function performanceAnalytics()
    {
        if (!$this->selectedWardId) return [];

        $cacheKey = "assignment_performance_{$this->selectedWardId}_{$this->selectedSubjectId}_{$this->selectedPeriod}";

        return Cache::remember($cacheKey, 300, function() {
            $query = AssignmentSubmission::where('student_id', $this->selectedWardId)
                ->whereIn('status', ['submitted', 'graded']);

            if ($this->selectedSubjectId) {
                $query->whereHas('assignment', function($q) {
                    $q->where('academic_subject_id', $this->selectedSubjectId);
                });
            }

            if ($this->selectedPeriod !== 'all') {
                $date = match($this->selectedPeriod) {
                    'week' => now()->subWeek(),
                    'month' => now()->subMonth(),
                    'quarter' => now()->subMonths(3),
                    'year' => now()->subYear(),
                    default => null
                };

                if ($date) {
                    $query->where('submitted_at', '>=', $date);
                }
            }

            $submissions = $query->with('assignment')->get();

            // Calculate scores and statistics
            $submissionsWithScores = $submissions->filter(function($submission) {
                return $submission->total_marks > 0;
            });

            $scores = $submissionsWithScores->map(function($submission) {
                return ($submission->score / $submission->total_marks) * 100;
            });

            $passThreshold = 50; // 50% pass mark
            $passedCount = $scores->filter(fn($score) => $score >= $passThreshold)->count();

            return [
                'total_assignments' => $submissions->count(),
                'graded_assignments' => $submissions->where('status', 'graded')->count(),
                'pending_grading' => $submissions->where('status', 'submitted')->count(),
                'average_score' => $scores->avg() ?? 0,
                'highest_score' => $scores->max() ?? 0,
                'lowest_score' => $scores->min() ?? 0,
                'passed_count' => $passedCount,
                'failed_count' => $scores->count() - $passedCount,
                'pass_rate' => $scores->count() > 0 ? ($passedCount / $scores->count() * 100) : 0,
                'subject_breakdown' => $this->getSubjectBreakdown($submissions),
                'monthly_trend' => $this->getMonthlyTrend($submissions),
                'performance_trend' => $this->calculatePerformanceTrend($submissions),
                'assignment_type_breakdown' => $this->getAssignmentTypeBreakdown($submissions),
                'time_management' => $this->getTimeManagementStats($submissions),
            ];
        });
    }

    private function getSubjectBreakdown($submissions)
    {
        return $submissions->groupBy('assignment.academic_subject_id')
            ->map(function($subjectSubmissions) {
                $scores = $subjectSubmissions
                    ->filter(fn($s) => $s->total_marks > 0)
                    ->map(fn($s) => ($s->score / $s->total_marks) * 100);

                $passedCount = $scores->filter(fn($score) => $score >= 50)->count();

                return [
                    'subject_id' => $subjectSubmissions->first()->assignment->academic_subject_id,
                    'subject_name' => $subjectSubmissions->first()->assignment->academicSubject->name ?? 'Unknown',
                    'count' => $subjectSubmissions->count(),
                    'average' => $scores->avg() ?? 0,
                    'passed' => $passedCount,
                    'graded' => $subjectSubmissions->where('status', 'graded')->count(),
                ];
            });
    }

    private function getMonthlyTrend($submissions)
    {
        return $submissions->groupBy(function($submission) {
            return $submission->submitted_at?->format('Y-m') ?? 'pending';
        })
            ->filter(fn($group, $key) => $key !== 'pending')
            ->map(function($monthSubmissions) {
                $scores = $monthSubmissions
                    ->filter(fn($s) => $s->total_marks > 0)
                    ->map(fn($s) => ($s->score / $s->total_marks) * 100);

                return [
                    'count' => $monthSubmissions->count(),
                    'average' => $scores->avg() ?? 0,
                    'graded' => $monthSubmissions->where('status', 'graded')->count(),
                ];
            });
    }

    private function getAssignmentTypeBreakdown($submissions)
    {
        return $submissions->groupBy('assignment.type')
            ->map(function($typeSubmissions, $type) {
                $scores = $typeSubmissions
                    ->filter(fn($s) => $s->total_marks > 0)
                    ->map(fn($s) => ($s->score / $s->total_marks) * 100);

                return [
                    'type' => $type ?? 'unknown',
                    'count' => $typeSubmissions->count(),
                    'average' => $scores->avg() ?? 0,
                    'graded' => $typeSubmissions->where('status', 'graded')->count(),
                ];
            });
    }

    private function getTimeManagementStats($submissions)
    {
        $submittedOnTime = 0;
        $submittedLate = 0;
        $totalTimeSpent = 0;

        foreach ($submissions as $submission) {
            if ($submission->submitted_at && $submission->assignment->ends_at) {
                if ($submission->submitted_at <= $submission->assignment->ends_at) {
                    $submittedOnTime++;
                } else {
                    $submittedLate++;
                }
            }
            $totalTimeSpent += $submission->time_spent_minutes ?? 0;
        }

        $totalSubmissions = $submittedOnTime + $submittedLate;

        return [
            'on_time' => $submittedOnTime,
            'late' => $submittedLate,
            'on_time_rate' => $totalSubmissions > 0 ? ($submittedOnTime / $totalSubmissions * 100) : 0,
            'average_time_spent' => $submissions->count() > 0 ? ($totalTimeSpent / $submissions->count()) : 0,
            'total_time_spent' => $totalTimeSpent,
        ];
    }

    private function calculatePerformanceTrend($submissions)
    {
        if ($submissions->count() < 2) return 'stable';

        $sorted = $submissions->sortByDesc('submitted_at');

        $recentScores = $sorted->take(5)
            ->filter(fn($s) => $s->total_marks > 0)
            ->map(fn($s) => ($s->score / $s->total_marks) * 100);

        $previousScores = $sorted->skip(5)->take(5)
            ->filter(fn($s) => $s->total_marks > 0)
            ->map(fn($s) => ($s->score / $s->total_marks) * 100);

        if ($recentScores->isEmpty() || $previousScores->isEmpty()) {
            return 'stable';
        }

        $recentAvg = $recentScores->avg();
        $previousAvg = $previousScores->avg();

        if ($recentAvg > $previousAvg + 5) return 'improving';
        if ($recentAvg < $previousAvg - 5) return 'declining';
        return 'stable';
    }

    public function render()
    {
        return view('livewire.parent.performance');
    }
}
