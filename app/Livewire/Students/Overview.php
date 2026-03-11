<?php

namespace App\Livewire\Students;

use App\Models\Assessment;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\QuizSession;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Overview extends Component
{
    public $student;

    public string $range = 'all';

    // Assignment Stats
    public $totalAssignments = 0;

    public $completedAssignments = 0;

    public $ongoingAssignments = 0;

    public $overdueAssignments = 0;

    public $upcomingAssignments = 0;

    // Self-Assessment (Book-based) Stats
    public $totalSelfAssessments = 0;

    public $recentSelfAssessments = [];

    public $averageSelfAssessmentScore = 0;

    // Performance Stats
    public $averageAssignmentScore = 0;

    public $assignmentsThisWeek = 0;

    public $assignmentsThisMonth = 0;

    // Recent Data
    public $recentAssignments = [];

    public $upcomingDueAssignments = [];

    // Subject Performance
    public $subjectPerformance = [];

    // Charts data
    public $performanceChartData = [];

    public $subjectChartData = [];

    // Chart props for Livewire components
    public array $barLabels = [];

    public array $barDatasets = [];

    public array $barOptions = [];

    public array $pieLabels = [];

    public array $pieValues = [];

    public array $pieOptions = [];

    public float $gaugeValue = 0.0;

    public int $gaugeMin = 0;

    public int $gaugeMax = 100;

    public array $gaugeThresholds = [];

    // Book Quiz Chart props
    public array $quizBarLabels = [];

    public array $quizBarDatasets = [];

    public array $quizBarOptions = [];

    public array $quizPieLabels = [];

    public array $quizPieValues = [];

    public array $quizPieOptions = [];

    public array $quizTrendData = [];

    // Additional quiz stats
    public $totalQuizAttempts = 0;

    public $quizzesByDifficulty = [];

    public $quizzesByType = [];

    public function mount()
    {
        $user = Auth::user();
        $this->student = getStudent();

        if (! $this->student) {
            $this->student = \App\Models\Student::where('user_id', $user->id)->first();
        }

        if ($this->student) {
            $this->loadAll();
        }
    }

    public function updatedRange(): void
    {
        if ($this->student) {
            $this->loadAll();
        }
    }

    protected function loadAll(): void
    {
        $this->loadAssignmentStats();
        $this->loadSelfAssessmentStats();
        $this->loadPerformanceData();
        $this->loadRecentData();
        $this->loadSubjectPerformance();
        $this->prepareCharts();
    }

    protected function loadAssignmentStats()
    {
        $student = getStudent();

        // Get all assignments available to student
        $availableAssignments = $this->getAvailableAssignments();

        $this->totalAssignments = $availableAssignments->count();

        // Get student's submissions
        $submissions = AssignmentSubmission::where('student_id', $student->id)
            ->with('assignment')
            ->get();

        // Completed assignments
        $this->completedAssignments = $submissions
            ->whereIn('status', ['completed', 'submitted', 'graded'])
            ->count();

        // Ongoing assignments
        $this->ongoingAssignments = $submissions
            ->where('status', 'in_progress')
            ->count();

        // Overdue assignments
        $this->overdueAssignments = $availableAssignments
            ->filter(function ($assignment) use ($submissions) {
                $submission = $submissions->where('assignment_id', $assignment->id)->first();

                return $assignment->ends_at < now() &&
                    (! $submission || ! in_array($submission->status, ['completed', 'submitted', 'graded']));
            })
            ->count();

        // Upcoming assignments (due within 7 days)
        $this->upcomingAssignments = $availableAssignments
            ->filter(function ($assignment) use ($submissions) {
                $submission = $submissions->where('assignment_id', $assignment->id)->first();

                return $assignment->ends_at >= now() &&
                    $assignment->ends_at <= now()->addDays(7) &&
                    (! $submission || ! in_array($submission->status, ['completed', 'submitted', 'graded']));
            })
            ->count();

        // Average assignment score
        $gradedSubmissions = $submissions->whereIn('status', ['graded', 'completed']);

        if ($gradedSubmissions->count() > 0) {
            $this->averageAssignmentScore = round(
                $gradedSubmissions->avg(function ($submission) {
                    if ($submission->total_marks > 0) {
                        return ($submission->score / $submission->total_marks) * 100;
                    }

                    return 0;
                }),
                1
            );
        }

        // Assignments this week
        $this->assignmentsThisWeek = $submissions
            ->where('submitted_at', '>=', now()->startOfWeek())
            ->whereIn('status', ['completed', 'submitted', 'graded'])
            ->count();

        // Assignments this month
        $this->assignmentsThisMonth = $submissions
            ->where('submitted_at', '>=', now()->startOfMonth())
            ->whereIn('status', ['completed', 'submitted', 'graded'])
            ->count();
    }

    protected function getAvailableAssignments()
    {
        $student = $this->student;

        // Get the academic group ID that the student's academic level belongs to
        // This is important because assignments can be assigned to academic groups,
        // and all students whose academic level belongs to that group should see them
        $academicLevelGroupId = null;
        if ($student->academic_level_id) {
            $academicLevel = \App\Models\AcademicLevel::find($student->academic_level_id);
            $academicLevelGroupId = $academicLevel?->academic_group_id;
        }

        return Assignment::where('status', 'published')
            ->where('ends_at', '>', now())
            // Include assignments where start time is in the past (or now) but end time is in the future
            // This ensures we show currently active assignments
            ->where(function ($query) use ($student, $academicLevelGroupId) {
                $hasCondition = false;

                // Check academic level
                if ($student->academic_level_id) {
                    $query->whereHas('academicLevels', function ($q) use ($student) {
                        $q->where('academic_levels.id', $student->academic_level_id);
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

                // Check academic groups (direct many-to-many relationship)
                $academicGroupIds = $student->academicGroups?->pluck('id')->toArray() ?? [];
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

                // Check student groups
                $studentGroupIds = $student->studentGroups?->pluck('id')->toArray() ?? [];
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

                // Check direct assignment (always check this)
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
            ->with(['academicSubject', 'teacher.user'])
            ->get();
    }

    protected function loadSelfAssessmentStats()
    {
        $student = $this->student;
        $start = $this->rangeStart();

        // Get ALL quiz sessions (both book-based and uploaded content)
        $allQuizSessions = QuizSession::where('user_id', $student->user_id)
            ->where('status', 'completed')
            ->when($start, function ($q) use ($start) {
                $q->where('completed_at', '>=', $start);
            })
            ->with(['book', 'subject'])
            ->orderBy('completed_at', 'desc')
            ->get();

        $this->totalQuizAttempts = $allQuizSessions->count();
        $this->totalSelfAssessments = $allQuizSessions->count();

        // Recent self-assessments (all types)
        $this->recentSelfAssessments = $allQuizSessions->take(5)->map(function ($session) {
            $title = $session->book?->title ?? ($session->context['book_title'] ?? 'Uploaded Content');

            return [
                'id' => $session->id,
                'book_title' => $title,
                'score' => $session->results['percentage'] ?? 0,
                'completed_at' => $session->completed_at,
                'questions_count' => $session->question_count ?? 0,
                'difficulty' => $session->difficulty ?? 'medium',
                'type' => $session->book_id ? 'book' : 'uploaded',
            ];
        })->toArray();

        // Average self-assessment score
        if ($allQuizSessions->count() > 0) {
            $this->averageSelfAssessmentScore = round(
                $allQuizSessions->avg(function ($session) {
                    return $session->results['percentage'] ?? 0;
                }),
                1
            );
        }

        // Group by difficulty for pie chart
        $this->quizzesByDifficulty = $allQuizSessions->groupBy('difficulty')->map(function ($group, $difficulty) {
            return [
                'difficulty' => ucfirst($difficulty),
                'count' => $group->count(),
                'average_score' => round($group->avg(fn ($s) => $s->results['percentage'] ?? 0), 1),
            ];
        })->values()->toArray();

        // Group by question type
        $this->quizzesByType = $allQuizSessions->groupBy('question_type')->map(function ($group, $type) {
            $typeLabels = [
                'multiple_choice' => 'Multiple Choice',
                'true_false' => 'True/False',
                'essay' => 'Essay',
                'mixed' => 'Mixed',
            ];

            return [
                'type' => $typeLabels[$type] ?? ucfirst(str_replace('_', ' ', $type)),
                'count' => $group->count(),
                'average_score' => round($group->avg(fn ($s) => $s->results['percentage'] ?? 0), 1),
            ];
        })->values()->toArray();

        // Prepare quiz chart data
        $this->prepareQuizCharts($allQuizSessions);
    }

    protected function prepareQuizCharts($quizSessions): void
    {
        if ($quizSessions->isEmpty()) {
            $this->quizBarLabels = [];
            $this->quizBarDatasets = [];
            $this->quizPieLabels = [];
            $this->quizPieValues = [];
            $this->quizTrendData = [];

            return;
        }

        // Bar chart: Performance by book/content source
        $bySource = $quizSessions->groupBy(function ($session) {
            if ($session->book_id && $session->book) {
                return $session->book->title;
            }

            return $session->context['book_title'] ?? 'Uploaded Content';
        });

        $this->quizBarLabels = $bySource->keys()->take(10)->toArray();
        $barData = $bySource->take(10)->map(function ($group) {
            return round($group->avg(fn ($s) => $s->results['percentage'] ?? 0), 1);
        })->values()->toArray();

        $this->quizBarDatasets = [
            [
                'label' => 'Avg Score %',
                'data' => $barData,
                'backgroundColor' => '#8b5cf6',
            ],
        ];
        $this->quizBarOptions = [
            'plugins' => ['legend' => ['display' => true, 'position' => 'bottom']],
            'scales' => [
                'y' => ['beginAtZero' => true, 'max' => 100],
            ],
        ];

        // Pie chart: Distribution by difficulty
        $byDifficulty = $quizSessions->groupBy('difficulty');
        $this->quizPieLabels = $byDifficulty->keys()->map(fn ($d) => ucfirst($d))->toArray();
        $this->quizPieValues = $byDifficulty->map(fn ($g) => $g->count())->values()->toArray();
        $this->quizPieOptions = ['plugins' => ['legend' => ['position' => 'right']]];

        // Trend data: Performance over time
        $this->quizTrendData = $quizSessions->sortBy('completed_at')
            ->groupBy(function ($session) {
                return $session->completed_at->format('Y-m-d');
            })
            ->map(function ($group, $date) {
                return [
                    'date' => $date,
                    'score' => round($group->avg(fn ($s) => $s->results['percentage'] ?? 0), 1),
                    'count' => $group->count(),
                ];
            })
            ->values()
            ->toArray();
    }

    protected function loadPerformanceData()
    {
        $student = $this->student;

        // Get assignment submissions within selected range
        $start = $this->rangeStart();
        $submissions = AssignmentSubmission::where('student_id', $student->id)
            ->when($start, function ($q) use ($start) {
                $q->where('submitted_at', '>=', $start);
            })
            ->whereIn('status', ['graded', 'completed'])
            ->orderBy('submitted_at')
            ->get();

        // Prepare chart data
        $chartData = [];
        $submissions->groupBy(function ($submission) {
            return $submission->submitted_at->format('Y-m-d');
        })->each(function ($daySubmissions, $date) use (&$chartData) {
            $avgScore = $daySubmissions->avg(function ($submission) {
                if ($submission->total_marks > 0) {
                    return ($submission->score / $submission->total_marks) * 100;
                }

                return 0;
            });

            $chartData[] = [
                'date' => $date,
                'score' => round($avgScore, 1),
            ];
        });

        $this->performanceChartData = $chartData;
    }

    protected function loadRecentData()
    {
        $student = $this->student;

        // Recent assignments (completed)
        $this->recentAssignments = AssignmentSubmission::where('student_id', $student->id)
            ->whereIn('status', ['completed', 'submitted', 'graded', 'not_started', 'in_progress'])
            ->with(['assignment.academicSubject', 'assignment.teacher.user'])
            ->orderBy('submitted_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($submission) {
                $percentage = 0;
                if ($submission->total_marks > 0) {
                    $percentage = ($submission->score / $submission->total_marks) * 100;
                }

                return [
                    'id' => $submission->assignment_id,
                    'title' => $submission->assignment->title,
                    'subject' => $submission->assignment->academicSubject->name ?? 'N/A',
                    'score' => $submission->score,
                    'total_marks' => $submission->total_marks,
                    'percentage' => round($percentage, 1),
                    'submitted_at' => $submission->submitted_at,
                    'status' => $submission->status,
                ];
            });

        // Upcoming due assignments
        $availableAssignments = $this->getAvailableAssignments();
        $submissions = AssignmentSubmission::where('student_id', $student->id)->get();

        $this->upcomingDueAssignments = $availableAssignments
            ->filter(function ($assignment) use ($submissions) {
                $submission = $submissions->where('assignment_id', $assignment->id)->first();

                return $assignment->ends_at >= now() &&
                    $assignment->ends_at <= now()->addDays(7) &&
                    (! $submission || ! in_array($submission->status, ['completed', 'submitted', 'graded']));
            })
            ->sortBy('ends_at')
            ->take(5)
            ->map(function ($assignment) {
                // Calculate days until due with proper formatting
                $now = now();
                $endsAt = $assignment->ends_at;
                $diffInHours = $now->diffInHours($endsAt, false);

                // Format days_until_due as a clean integer or show "< 1 day" for less than 24 hours
                if ($diffInHours < 24 && $diffInHours > 0) {
                    $daysUntilDue = round($diffInHours / 24, 1); // Show as decimal like 0.5 for half a day
                } else {
                    $daysUntilDue = (int) floor($diffInHours / 24); // Show as whole number of days
                }

                return [
                    'id' => $assignment->id,
                    'title' => $assignment->title,
                    'subject' => $assignment->academicSubject->name ?? 'N/A',
                    'due_date' => $assignment->ends_at,
                    'days_until_due' => $daysUntilDue,
                    'hours_until_due' => max(0, $diffInHours), // Also provide hours for more precise display
                ];
            })
            ->values();
    }

    protected function loadSubjectPerformance(): void
    {
        $student = $this->student;

        // Get submissions grouped by subject (filtered by selected range)
        $start = $this->rangeStart();
        $submissions = AssignmentSubmission::where('student_id', $student->id)
            ->whereIn('status', ['graded', 'completed', 'in_progress', 'not_started', 'submitted'])
            ->when($start, function ($q) use ($start) {
                $q->where('submitted_at', '>=', $start);
            })
            ->with('assignment.academicSubject')
            ->get();

        $subjectStats = [];

        $submissions->groupBy('assignment.academic_subject_id')->each(function ($subjectSubmissions, $subjectId) use (&$subjectStats) {
            $subject = $subjectSubmissions->first()->assignment->academicSubject;

            if (! $subject) {
                return;
            }

            $totalScore = 0;
            $totalPossible = 0;

            foreach ($subjectSubmissions as $submission) {
                $totalScore += $submission->score;
                $totalPossible += $submission->total_marks;
            }

            $percentage = $totalPossible > 0 ? ($totalScore / $totalPossible) * 100 : 0;

            $subjectStats[] = [
                'subject' => $subject->name,
                'assignments_count' => $subjectSubmissions->count(),
                'average_score' => round($percentage, 1),
                'total_score' => $totalScore,
                'total_possible' => $totalPossible,
            ];
        });

        // Sort by average score descending
        usort($subjectStats, function ($a, $b) {
            return $b['average_score'] <=> $a['average_score'];
        });

        $this->subjectPerformance = $subjectStats;

        // Prepare chart data
        $this->subjectChartData = collect($subjectStats)
            ->map(function ($stat) {
                return [
                    'subject' => $stat['subject'],
                    'score' => $stat['average_score'],
                ];
            })
            ->toArray();
    }

    protected function prepareCharts(): void
    {
        // Bar chart: performance by subject
        $this->barLabels = collect($this->subjectChartData)->pluck('subject')->toArray();
        $barData = collect($this->subjectChartData)->pluck('score')->toArray();
        $this->barDatasets = [
            [
                'label' => 'Avg Score %',
                'data' => $barData,
                'backgroundColor' => '#3b82f6',
            ],
        ];
        $this->barOptions = [
            'plugins' => ['legend' => ['display' => true, 'position' => 'bottom']],
            'scales' => [
                'y' => ['beginAtZero' => true, 'max' => 100],
            ],
        ];

        // Pie chart: status distribution within selected range
        [$completed, $ongoing, $overdue] = $this->statusCountsInRange();
        $this->pieLabels = ['Completed', 'Ongoing', 'Overdue'];
        $this->pieValues = [$completed, $ongoing, $overdue];
        $this->pieOptions = ['plugins' => ['legend' => ['position' => 'right']]];

        // Gauge: completion rate within selected range
        $den = max(1, $completed + $ongoing + $overdue);
        $rate = ($completed / $den) * 100.0;
        $this->gaugeValue = round($rate, 1);
        $this->gaugeMin = 0;
        $this->gaugeMax = 100;
        $this->gaugeThresholds = [
            ['max' => 50, 'color' => '#ef4444', 'label' => 'Low'],
            ['max' => 80, 'color' => '#f59e0b', 'label' => 'Medium'],
            ['max' => 100, 'color' => '#10b981', 'label' => 'High'],
        ];
    }

    protected function statusCountsInRange(): array
    {
        $student = $this->student;
        $start = $this->rangeStart();

        $completed = AssignmentSubmission::where('student_id', $student->id)
            ->whereIn('status', ['completed', 'submitted', 'graded'])
            ->when($start, function ($q) use ($start) {
                $q->where('submitted_at', '>=', $start);
            })
            ->count();

        $ongoing = AssignmentSubmission::where('student_id', $student->id)
            ->where('status', 'in_progress')
            ->when($start, function ($q) use ($start) {
                $q->where('updated_at', '>=', $start);
            })
            ->count();

        // Overdue: assignments with due date past now, started before now, in the window, not completed
        $available = $this->getAvailableAssignments();
        $subs = AssignmentSubmission::where('student_id', $student->id)->get();
        $overdue = $available->filter(function ($assignment) use ($subs, $start) {
            $submission = $subs->where('assignment_id', $assignment->id)->first();
            $inWindow = $start ? ($assignment->ends_at >= $start) : true;

            return $inWindow && $assignment->ends_at < now() && (! $submission || ! in_array($submission->status, ['completed', 'submitted', 'graded']));
        })->count();

        return [$completed, $ongoing, $overdue];
    }

    protected function rangeStart(): ?\Carbon\Carbon
    {
        return match ($this->range) {
            'all' => null,
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            'term' => now()->subDays(90),
            default => null,
        };
    }

    public function render()
    {
        return view('livewire.students.overview');
    }
}
