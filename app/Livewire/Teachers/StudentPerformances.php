<?php

namespace App\Livewire\Teachers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Attendance\Attendance;
use App\Models\Attendance\AttendanceRecord;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class StudentPerformances extends Component
{
    use WithPagination;

    public $teacher;

    public $search = '';

    public $selectedSubject = '';

    public $selectedLevel = '';

    public $selectedGroup = '';

    public $selectedStudent = null;

    public $selectedStudentStats = null;

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
        $this->teacher = Teacher::withoutGlobalScopes()->where('user_id', Auth::id())->first();

        if (! $this->teacher) {
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
            'assessments.assignment.academicSubject',
        ])->findOrFail($studentId);

        // Load comprehensive student statistics
        $this->selectedStudentStats = $this->getStudentDetailedStats($studentId);

        $this->showStudentModal = true;
    }

    public function closeStudentModal()
    {
        $this->showStudentModal = false;
        $this->selectedStudent = null;
        $this->selectedStudentStats = null;
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
            DB::raw('MAX(submitted_at) as last_submission'),
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
            'academicSubjects',
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
        if ($average >= 90) {
            return 'A+';
        }
        if ($average >= 80) {
            return 'A';
        }
        if ($average >= 70) {
            return 'B+';
        }
        if ($average >= 60) {
            return 'B';
        }
        if ($average >= 50) {
            return 'C';
        }
        if ($average >= 40) {
            return 'D';
        }

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

    /**
     * Get aggregate overview statistics for all students
     */
    public function getOverviewStats(): array
    {
        $students = $this->getStudentsWithPerformance();
        $timeFilter = $this->buildTimeFilter();

        $totalStudents = $students->count();
        $avgPerformance = $students->avg('performance_avg') ?: 0;

        // Performance distribution
        $excellent = $students->filter(fn ($s) => $s->performance_avg >= 80)->count();
        $good = $students->filter(fn ($s) => $s->performance_avg >= 60 && $s->performance_avg < 80)->count();
        $needsImprovement = $students->filter(fn ($s) => $s->performance_avg < 60)->count();

        // Assignment statistics
        $assignmentIds = $this->teacher->assignments()->pluck('id');
        $totalAssignments = $assignmentIds->count();

        $submissionsQuery = AssignmentSubmission::whereIn('assignment_id', $assignmentIds);
        if ($timeFilter) {
            $submissionsQuery->where('submitted_at', '>=', $timeFilter);
        }

        $totalSubmissions = (clone $submissionsQuery)->count();
        $gradedSubmissions = (clone $submissionsQuery)->where('status', 'graded')->count();
        $pendingSubmissions = (clone $submissionsQuery)->where('status', 'submitted')->count();

        // Calculate submission rate
        $expectedSubmissions = $totalAssignments * $totalStudents;
        $submissionRate = $expectedSubmissions > 0 ? round(($totalSubmissions / $expectedSubmissions) * 100, 1) : 0;

        // Top performers
        $topPerformers = $students->sortByDesc('performance_avg')->take(5)->values();

        // Students needing attention (lowest performers with activity)
        $needingAttention = $students->filter(fn ($s) => $s->performance_avg < 50 && $s->total_submissions > 0)
            ->sortBy('performance_avg')
            ->take(5)
            ->values();

        return [
            'total_students' => $totalStudents,
            'avg_performance' => round($avgPerformance, 1),
            'excellent_count' => $excellent,
            'good_count' => $good,
            'needs_improvement_count' => $needsImprovement,
            'total_assignments' => $totalAssignments,
            'total_submissions' => $totalSubmissions,
            'graded_submissions' => $gradedSubmissions,
            'pending_submissions' => $pendingSubmissions,
            'submission_rate' => $submissionRate,
            'top_performers' => $topPerformers,
            'needing_attention' => $needingAttention,
        ];
    }

    /**
     * Get performance distribution chart data
     */
    public function getPerformanceDistributionChartData(): array
    {
        $students = $this->getStudentsWithPerformance();

        $distribution = [
            '90-100%' => $students->filter(fn ($s) => $s->performance_avg >= 90)->count(),
            '80-89%' => $students->filter(fn ($s) => $s->performance_avg >= 80 && $s->performance_avg < 90)->count(),
            '70-79%' => $students->filter(fn ($s) => $s->performance_avg >= 70 && $s->performance_avg < 80)->count(),
            '60-69%' => $students->filter(fn ($s) => $s->performance_avg >= 60 && $s->performance_avg < 70)->count(),
            '50-59%' => $students->filter(fn ($s) => $s->performance_avg >= 50 && $s->performance_avg < 60)->count(),
            'Below 50%' => $students->filter(fn ($s) => $s->performance_avg < 50)->count(),
        ];

        return [
            'labels' => array_keys($distribution),
            'data' => array_values($distribution),
            'colors' => ['#10B981', '#34D399', '#60A5FA', '#FBBF24', '#F97316', '#EF4444'],
        ];
    }

    /**
     * Get subject performance comparison chart data
     */
    public function getSubjectComparisonChartData(): array
    {
        $subjects = $this->teacher->academicSubjects()->get();
        $labels = [];
        $avgScores = [];
        $submissionCounts = [];

        foreach ($subjects as $subject) {
            $assignments = Assignment::where('teacher_id', $this->teacher->id)
                ->where('academic_subject_id', $subject->id)
                ->pluck('id');

            $submissions = AssignmentSubmission::whereIn('assignment_id', $assignments)
                ->where('status', 'graded')
                ->get();

            if ($submissions->count() > 0) {
                $labels[] = $subject->name;
                $avgScore = $submissions->avg(function ($sub) {
                    return $sub->total_marks > 0 ? ($sub->score / $sub->total_marks) * 100 : 0;
                });
                $avgScores[] = round($avgScore, 1);
                $submissionCounts[] = $submissions->count();
            }
        }

        return [
            'labels' => $labels,
            'avgScores' => $avgScores,
            'submissionCounts' => $submissionCounts,
        ];
    }

    /**
     * Get performance trend chart data over time
     */
    public function getPerformanceTrendChartData(): array
    {
        $assignmentIds = $this->teacher->assignments()->pluck('id');
        $days = $this->timeRange === 'all' ? 90 : (int) $this->timeRange;

        $labels = [];
        $avgScores = [];
        $submissionCounts = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M d');

            $daySubmissions = AssignmentSubmission::whereIn('assignment_id', $assignmentIds)
                ->whereDate('submitted_at', $date)
                ->where('status', 'graded')
                ->get();

            $submissionCounts[] = $daySubmissions->count();

            if ($daySubmissions->count() > 0) {
                $avgScore = $daySubmissions->avg(function ($sub) {
                    return $sub->total_marks > 0 ? ($sub->score / $sub->total_marks) * 100 : 0;
                });
                $avgScores[] = round($avgScore, 1);
            } else {
                $avgScores[] = null;
            }
        }

        return [
            'labels' => $labels,
            'avgScores' => $avgScores,
            'submissionCounts' => $submissionCounts,
        ];
    }

    /**
     * Get student distribution by level chart data
     */
    public function getStudentsByLevelChartData(): array
    {
        $students = $this->getTeacherStudents();

        $byLevel = $students->groupBy(function ($student) {
            return $student->academicLevel->name ?? 'Unknown';
        });

        $labels = $byLevel->keys()->toArray();
        $data = $byLevel->map->count()->values()->toArray();

        $colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16'];

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => array_slice($colors, 0, count($labels)),
        ];
    }

    /**
     * Get enhanced subject analysis with detailed metrics
     */
    public function getEnhancedSubjectAnalysis(): array
    {
        $subjects = $this->teacher->academicSubjects()
            ->with('academicLevel')
            ->get();

        $analysis = [];
        $totalStudents = $this->getTeacherStudents()->count();

        foreach ($subjects as $subject) {
            $assignments = Assignment::where('teacher_id', $this->teacher->id)
                ->where('academic_subject_id', $subject->id)
                ->get();

            $assignmentIds = $assignments->pluck('id');

            $submissions = AssignmentSubmission::whereIn('assignment_id', $assignmentIds)->get();
            $gradedSubmissions = $submissions->where('status', 'graded');

            // Calculate average score properly
            $avgScore = 0;
            if ($gradedSubmissions->count() > 0) {
                $avgScore = $gradedSubmissions->avg(function ($sub) {
                    return $sub->total_marks > 0 ? ($sub->score / $sub->total_marks) * 100 : 0;
                });
            }

            // Grade distribution for this subject
            $gradeDistribution = [
                'A' => $gradedSubmissions->filter(fn ($s) => $this->getScorePercentage($s) >= 80)->count(),
                'B' => $gradedSubmissions->filter(fn ($s) => $this->getScorePercentage($s) >= 60 && $this->getScorePercentage($s) < 80)->count(),
                'C' => $gradedSubmissions->filter(fn ($s) => $this->getScorePercentage($s) >= 50 && $this->getScorePercentage($s) < 60)->count(),
                'D' => $gradedSubmissions->filter(fn ($s) => $this->getScorePercentage($s) >= 40 && $this->getScorePercentage($s) < 50)->count(),
                'F' => $gradedSubmissions->filter(fn ($s) => $this->getScorePercentage($s) < 40)->count(),
            ];

            // Submission status breakdown
            $statusBreakdown = [
                'graded' => $submissions->where('status', 'graded')->count(),
                'submitted' => $submissions->where('status', 'submitted')->count(),
                'in_progress' => $submissions->where('status', 'in_progress')->count(),
                'not_started' => $submissions->where('status', 'not_started')->count(),
            ];

            // Expected vs actual submissions
            $expectedSubmissions = $assignments->count() * $totalStudents;
            $actualSubmissions = $submissions->count();
            $completionRate = $expectedSubmissions > 0 ? ($actualSubmissions / $expectedSubmissions) * 100 : 0;

            // Performance trend (last 4 weeks)
            $weeklyTrend = [];
            for ($i = 3; $i >= 0; $i--) {
                $weekStart = now()->subWeeks($i)->startOfWeek();
                $weekEnd = now()->subWeeks($i)->endOfWeek();

                $weekSubmissions = $gradedSubmissions->filter(function ($sub) use ($weekStart, $weekEnd) {
                    return $sub->submitted_at && $sub->submitted_at->between($weekStart, $weekEnd);
                });

                $weekAvg = $weekSubmissions->count() > 0
                    ? $weekSubmissions->avg(fn ($s) => $this->getScorePercentage($s))
                    : null;

                $weeklyTrend[] = [
                    'week' => $weekStart->format('M d'),
                    'avg_score' => $weekAvg ? round($weekAvg, 1) : null,
                    'submissions' => $weekSubmissions->count(),
                ];
            }

            // Top and bottom performers in this subject
            $studentPerformance = $gradedSubmissions->groupBy('student_id')->map(function ($studentSubs) {
                return [
                    'avg' => $studentSubs->avg(fn ($s) => $this->getScorePercentage($s)),
                    'count' => $studentSubs->count(),
                ];
            })->sortByDesc('avg');

            $analysis[] = [
                'subject' => $subject,
                'total_assignments' => $assignments->count(),
                'total_submissions' => $submissions->count(),
                'graded_submissions' => $gradedSubmissions->count(),
                'average_score' => round($avgScore, 1),
                'completion_rate' => round($completionRate, 1),
                'grade_distribution' => $gradeDistribution,
                'status_breakdown' => $statusBreakdown,
                'weekly_trend' => $weeklyTrend,
                'top_performers_count' => $studentPerformance->filter(fn ($p) => $p['avg'] >= 80)->count(),
                'struggling_count' => $studentPerformance->filter(fn ($p) => $p['avg'] < 50)->count(),
                'expected_submissions' => $expectedSubmissions,
            ];
        }

        return $analysis;
    }

    /**
     * Get subject performance comparison chart data (bar chart)
     */
    public function getSubjectPerformanceChartData(): array
    {
        $analysis = $this->getEnhancedSubjectAnalysis();

        $labels = [];
        $avgScores = [];
        $completionRates = [];
        $colors = [];

        $colorPalette = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16', '#F97316', '#14B8A6'];

        foreach ($analysis as $index => $item) {
            $labels[] = $item['subject']->name;
            $avgScores[] = $item['average_score'];
            $completionRates[] = $item['completion_rate'];
            $colors[] = $colorPalette[$index % count($colorPalette)];
        }

        return [
            'labels' => $labels,
            'avgScores' => $avgScores,
            'completionRates' => $completionRates,
            'colors' => $colors,
        ];
    }

    /**
     * Get grade distribution across all subjects chart data (stacked bar)
     */
    public function getSubjectGradeDistributionChartData(): array
    {
        $analysis = $this->getEnhancedSubjectAnalysis();

        $labels = [];
        $gradeA = [];
        $gradeB = [];
        $gradeC = [];
        $gradeD = [];
        $gradeF = [];

        foreach ($analysis as $item) {
            $labels[] = $item['subject']->name;
            $gradeA[] = $item['grade_distribution']['A'];
            $gradeB[] = $item['grade_distribution']['B'];
            $gradeC[] = $item['grade_distribution']['C'];
            $gradeD[] = $item['grade_distribution']['D'];
            $gradeF[] = $item['grade_distribution']['F'];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                ['label' => 'A (≥80%)', 'data' => $gradeA, 'color' => '#10B981'],
                ['label' => 'B (60-79%)', 'data' => $gradeB, 'color' => '#3B82F6'],
                ['label' => 'C (50-59%)', 'data' => $gradeC, 'color' => '#F59E0B'],
                ['label' => 'D (40-49%)', 'data' => $gradeD, 'color' => '#F97316'],
                ['label' => 'F (<40%)', 'data' => $gradeF, 'color' => '#EF4444'],
            ],
        ];
    }

    /**
     * Get submission status distribution chart data (pie/doughnut)
     */
    public function getSubmissionStatusChartData(): array
    {
        $analysis = $this->getEnhancedSubjectAnalysis();

        $totals = [
            'graded' => 0,
            'submitted' => 0,
            'in_progress' => 0,
            'not_started' => 0,
        ];

        foreach ($analysis as $item) {
            $totals['graded'] += $item['status_breakdown']['graded'];
            $totals['submitted'] += $item['status_breakdown']['submitted'];
            $totals['in_progress'] += $item['status_breakdown']['in_progress'];
            $totals['not_started'] += $item['status_breakdown']['not_started'];
        }

        return [
            'labels' => ['Graded', 'Pending Review', 'In Progress', 'Not Started'],
            'data' => array_values($totals),
            'colors' => ['#10B981', '#3B82F6', '#F59E0B', '#9CA3AF'],
        ];
    }

    /**
     * Get weekly performance trend across all subjects
     */
    public function getWeeklyPerformanceTrendChartData(): array
    {
        $assignmentIds = $this->teacher->assignments()->pluck('id');

        $labels = [];
        $avgScores = [];
        $submissionCounts = [];

        for ($i = 7; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = now()->subWeeks($i)->endOfWeek();

            $labels[] = $weekStart->format('M d');

            $weekSubmissions = AssignmentSubmission::whereIn('assignment_id', $assignmentIds)
                ->where('status', 'graded')
                ->whereBetween('submitted_at', [$weekStart, $weekEnd])
                ->get();

            $submissionCounts[] = $weekSubmissions->count();

            if ($weekSubmissions->count() > 0) {
                $avgScore = $weekSubmissions->avg(function ($sub) {
                    return $sub->total_marks > 0 ? ($sub->score / $sub->total_marks) * 100 : 0;
                });
                $avgScores[] = round($avgScore, 1);
            } else {
                $avgScores[] = null;
            }
        }

        return [
            'labels' => $labels,
            'avgScores' => $avgScores,
            'submissionCounts' => $submissionCounts,
        ];
    }

    /**
     * Get subject ranking data
     */
    public function getSubjectRankingData(): array
    {
        $analysis = $this->getEnhancedSubjectAnalysis();

        // Sort by average score descending
        usort($analysis, fn ($a, $b) => $b['average_score'] <=> $a['average_score']);

        $rankings = [];
        foreach ($analysis as $index => $item) {
            $rankings[] = [
                'rank' => $index + 1,
                'subject' => $item['subject']->name,
                'code' => $item['subject']->code,
                'avg_score' => $item['average_score'],
                'completion_rate' => $item['completion_rate'],
                'total_submissions' => $item['total_submissions'],
                'trend' => $this->calculateSubjectTrend($item['weekly_trend']),
            ];
        }

        return $rankings;
    }

    /**
     * Calculate trend direction from weekly data
     */
    private function calculateSubjectTrend(array $weeklyTrend): string
    {
        $scores = array_filter(array_column($weeklyTrend, 'avg_score'), fn ($s) => $s !== null);

        if (count($scores) < 2) {
            return 'stable';
        }

        $firstHalf = array_slice($scores, 0, ceil(count($scores) / 2));
        $secondHalf = array_slice($scores, ceil(count($scores) / 2));

        $firstAvg = count($firstHalf) > 0 ? array_sum($firstHalf) / count($firstHalf) : 0;
        $secondAvg = count($secondHalf) > 0 ? array_sum($secondHalf) / count($secondHalf) : 0;

        $diff = $secondAvg - $firstAvg;

        if ($diff > 5) {
            return 'up';
        }
        if ($diff < -5) {
            return 'down';
        }

        return 'stable';
    }

    /**
     * Get detailed statistics for a specific student
     */
    public function getStudentDetailedStats($studentId): array
    {
        $student = Student::with(['user', 'academicLevel.academicGroup', 'academicSubjects'])->find($studentId);

        if (! $student) {
            return [];
        }

        $timeFilter = $this->buildTimeFilter();
        $assignmentIds = $this->teacher->assignments()->pluck('id');

        // Get all submissions for this student from teacher's assignments
        $submissionsQuery = AssignmentSubmission::where('student_id', $studentId)
            ->whereIn('assignment_id', $assignmentIds);

        if ($timeFilter) {
            $submissionsQuery->where('submitted_at', '>=', $timeFilter);
        }

        $submissions = $submissionsQuery->with('assignment.academicSubject')->get();

        // Basic stats
        $totalSubmissions = $submissions->count();
        $gradedSubmissions = $submissions->where('status', 'graded');
        $pendingSubmissions = $submissions->where('status', 'submitted')->count();
        $inProgressSubmissions = $submissions->where('status', 'in_progress')->count();

        // Calculate average score
        $avgScore = 0;
        if ($gradedSubmissions->count() > 0) {
            $avgScore = $gradedSubmissions->avg(function ($sub) {
                return $sub->total_marks > 0 ? ($sub->score / $sub->total_marks) * 100 : 0;
            });
        }

        // Performance by subject
        $subjectPerformance = [];
        $subjectGroups = $submissions->groupBy('assignment.academic_subject_id');

        foreach ($subjectGroups as $subjectId => $subjectSubmissions) {
            $subject = $subjectSubmissions->first()->assignment->academicSubject ?? null;
            if ($subject) {
                $graded = $subjectSubmissions->where('status', 'graded');
                $subjectAvg = $graded->count() > 0 ? $graded->avg(function ($sub) {
                    return $sub->total_marks > 0 ? ($sub->score / $sub->total_marks) * 100 : 0;
                }) : 0;

                $subjectPerformance[] = [
                    'subject' => $subject->name,
                    'subject_code' => $subject->code,
                    'total_submissions' => $subjectSubmissions->count(),
                    'graded' => $graded->count(),
                    'avg_score' => round($subjectAvg, 1),
                ];
            }
        }

        // Performance trend (last submissions)
        $recentSubmissions = $submissions->where('status', 'graded')
            ->sortByDesc('submitted_at')
            ->take(10)
            ->values();

        $trendLabels = [];
        $trendScores = [];

        foreach ($recentSubmissions->reverse() as $sub) {
            $trendLabels[] = $sub->submitted_at ? $sub->submitted_at->format('M d') : 'N/A';
            $trendScores[] = $sub->total_marks > 0 ? round(($sub->score / $sub->total_marks) * 100, 1) : 0;
        }

        // Attendance stats (if available)
        $attendanceStats = $this->getStudentAttendanceStats($studentId);

        // Assignment completion rate
        $totalAssignmentsForStudent = $assignmentIds->count();
        $completionRate = $totalAssignmentsForStudent > 0
            ? round(($totalSubmissions / $totalAssignmentsForStudent) * 100, 1)
            : 0;

        // Grade distribution
        $gradeDistribution = [
            'A+' => $gradedSubmissions->filter(fn ($s) => $this->getScorePercentage($s) >= 90)->count(),
            'A' => $gradedSubmissions->filter(fn ($s) => $this->getScorePercentage($s) >= 80 && $this->getScorePercentage($s) < 90)->count(),
            'B+' => $gradedSubmissions->filter(fn ($s) => $this->getScorePercentage($s) >= 70 && $this->getScorePercentage($s) < 80)->count(),
            'B' => $gradedSubmissions->filter(fn ($s) => $this->getScorePercentage($s) >= 60 && $this->getScorePercentage($s) < 70)->count(),
            'C' => $gradedSubmissions->filter(fn ($s) => $this->getScorePercentage($s) >= 50 && $this->getScorePercentage($s) < 60)->count(),
            'D' => $gradedSubmissions->filter(fn ($s) => $this->getScorePercentage($s) >= 40 && $this->getScorePercentage($s) < 50)->count(),
            'F' => $gradedSubmissions->filter(fn ($s) => $this->getScorePercentage($s) < 40)->count(),
        ];

        // Recent assignments
        $recentAssignments = $submissions->sortByDesc('created_at')->take(5)->map(function ($sub) {
            return [
                'title' => $sub->assignment->title ?? 'Unknown',
                'subject' => $sub->assignment->academicSubject->name ?? 'N/A',
                'status' => $sub->status,
                'score' => $sub->score,
                'total_marks' => $sub->total_marks,
                'percentage' => $sub->total_marks > 0 ? round(($sub->score / $sub->total_marks) * 100, 1) : 0,
                'submitted_at' => $sub->submitted_at,
            ];
        })->values()->toArray();

        // Strengths and weaknesses (subjects)
        $sortedSubjects = collect($subjectPerformance)->sortByDesc('avg_score');
        $strengths = $sortedSubjects->take(3)->values()->toArray();
        $weaknesses = $sortedSubjects->sortBy('avg_score')->take(3)->values()->toArray();

        return [
            'total_submissions' => $totalSubmissions,
            'graded_count' => $gradedSubmissions->count(),
            'pending_count' => $pendingSubmissions,
            'in_progress_count' => $inProgressSubmissions,
            'avg_score' => round($avgScore, 1),
            'completion_rate' => $completionRate,
            'subject_performance' => $subjectPerformance,
            'trend_labels' => $trendLabels,
            'trend_scores' => $trendScores,
            'attendance' => $attendanceStats,
            'grade_distribution' => $gradeDistribution,
            'recent_assignments' => $recentAssignments,
            'strengths' => $strengths,
            'weaknesses' => $weaknesses,
            'overall_grade' => $this->calculatePerformanceGrade($avgScore),
        ];
    }

    /**
     * Get attendance statistics for a student
     */
    private function getStudentAttendanceStats($studentId): array
    {
        $attendanceIds = Attendance::where('teacher_id', $this->teacher->id)->pluck('id');

        $records = AttendanceRecord::where('student_id', $studentId)
            ->whereIn('attendance_id', $attendanceIds)
            ->get();

        $total = $records->count();
        $present = $records->where('status', 'present')->count();
        $absent = $records->where('status', 'absent')->count();
        $late = $records->where('status', 'late')->count();
        $excused = $records->where('status', 'excused')->count();

        $attendanceRate = $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0;

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'excused' => $excused,
            'attendance_rate' => $attendanceRate,
        ];
    }

    /**
     * Helper to get score percentage from submission
     */
    private function getScorePercentage($submission): float
    {
        return $submission->total_marks > 0 ? ($submission->score / $submission->total_marks) * 100 : 0;
    }

    /**
     * Compare student performance with class average
     */
    public function getStudentComparisonData($studentId): array
    {
        $studentStats = $this->getStudentDetailedStats($studentId);
        $overviewStats = $this->getOverviewStats();

        $studentAvg = $studentStats['avg_score'] ?? 0;
        $classAvg = $overviewStats['avg_performance'] ?? 0;

        $difference = $studentAvg - $classAvg;
        $percentile = $this->calculateStudentPercentile($studentId);

        return [
            'student_avg' => $studentAvg,
            'class_avg' => $classAvg,
            'difference' => round($difference, 1),
            'percentile' => $percentile,
            'comparison_text' => $difference >= 0
                ? 'Above class average by '.abs(round($difference, 1)).'%'
                : 'Below class average by '.abs(round($difference, 1)).'%',
        ];
    }

    /**
     * Calculate student's percentile rank
     */
    private function calculateStudentPercentile($studentId): int
    {
        $students = $this->getStudentsWithPerformance();
        $studentPerformance = $students->firstWhere('id', $studentId);

        if (! $studentPerformance) {
            return 0;
        }

        $belowCount = $students->filter(fn ($s) => $s->performance_avg < $studentPerformance->performance_avg)->count();
        $totalCount = $students->count();

        return $totalCount > 0 ? round(($belowCount / $totalCount) * 100) : 0;
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

        // Get enhanced subject analysis data for charts (only when in subject-analysis mode)
        $enhancedSubjectAnalysis = [];
        $subjectPerformanceChart = [];
        $subjectGradeDistribution = [];
        $submissionStatusChart = [];
        $weeklyTrendChart = [];
        $subjectRankings = [];

        if ($this->viewMode === 'subject-analysis') {
            $enhancedSubjectAnalysis = $this->getEnhancedSubjectAnalysis();
            $subjectPerformanceChart = $this->getSubjectPerformanceChartData();
            $subjectGradeDistribution = $this->getSubjectGradeDistributionChartData();
            $submissionStatusChart = $this->getSubmissionStatusChartData();
            $weeklyTrendChart = $this->getWeeklyPerformanceTrendChartData();
            $subjectRankings = $this->getSubjectRankingData();
        }

        // Get overview statistics and chart data
        $overviewStats = $this->getOverviewStats();
        $performanceDistribution = $this->getPerformanceDistributionChartData();
        $subjectComparison = $this->getSubjectComparisonChartData();
        $studentsByLevel = $this->getStudentsByLevelChartData();

        // Get student comparison data if a student is selected
        $studentComparison = null;
        if ($this->selectedStudent) {
            $studentComparison = $this->getStudentComparisonData($this->selectedStudent->id);
        }

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
            'overviewStats' => $overviewStats,
            'performanceDistribution' => $performanceDistribution,
            'subjectComparison' => $subjectComparison,
            'studentsByLevel' => $studentsByLevel,
            'studentComparison' => $studentComparison,
            // Enhanced subject analysis data
            'enhancedSubjectAnalysis' => $enhancedSubjectAnalysis,
            'subjectPerformanceChart' => $subjectPerformanceChart,
            'subjectGradeDistribution' => $subjectGradeDistribution,
            'submissionStatusChart' => $submissionStatusChart,
            'weeklyTrendChart' => $weeklyTrendChart,
            'subjectRankings' => $subjectRankings,
        ]);
    }
}
