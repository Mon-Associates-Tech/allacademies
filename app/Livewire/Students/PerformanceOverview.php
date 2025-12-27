<?php

namespace App\Livewire\Students;

use App\Models\AcademicSubject as Subject;
use App\Models\Assessment;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Student;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Log;

class PerformanceOverview extends Component
{
    public $selectedPeriod = 'all';
    public $selectedSubject = '';
    public $subjects = [];
    public $performanceData = [];
    public $overallStats = [];
    public $trendData = [];
    public $insights = [];
    public $upcomingAssignments = [];
    public $pendingAssignments = [];

    protected $listeners = ['refreshPerformanceData' => 'loadPerformanceData'];

    public function mount()
    {
        $this->loadSubjects();
        $this->loadPerformanceData();
        $this->generateInsights();
    }

    public function loadSubjects()
    {
        $student = getStudent(auth()->id(), withoutScopes: true);

        if (!$student) {
            $this->subjects = [];
        }


        // Get subjects that have assignment submissions for this student
        $subjectsWithSubmissions = AssignmentSubmission::where('student_id', $student->id)
            ->whereHas('assignment', function ($query) {
                $query->whereNotNull('academic_subject_id');
            })
            ->with('assignment.academicSubject')
            ->get()
            ->pluck('assignment.academic_subject_id')
            ->unique()
            ->filter()
            ->toArray();

        if (empty($subjectsWithSubmissions)) {
            $this->subjects = [];
            return;
        }

        $this->subjects = Subject::whereIn('id', $subjectsWithSubmissions)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function loadPerformanceData()
    {
        try {
            $student = Auth::user()->student;

            if (!$student) {
                $student = Student::withoutGlobalScopes()
                    ->where('user_id', Auth::id())
                    ->first();
            }

            if (!$student) {
                Log::warning('No student found in loadPerformanceData', ['user_id' => Auth::id()]);
                $this->performanceData = [];
                $this->overallStats = $this->getEmptyStats();
                $this->trendData = [];
                return;
            }

            // Get all assignments targeted to this student
            $allAssignments = $this->getStudentAssignments($student);

            Log::info('Assignments loaded', [
                'count' => $allAssignments->count(),
                'student_id' => $student->id
            ]);

            // Get submissions for these assignments
            $submissions = AssignmentSubmission::with(['assignment.academicSubject'])
                ->where('student_id', $student->id)
                ->whereIn('assignment_id', $allAssignments->pluck('id'))
                ->whereIn('status', ['graded', 'submitted', 'in_progress'])
                ->get();

            Log::info('Submissions loaded', [
                'total' => $submissions->count(),
                'graded' => $submissions->where('status', 'graded')->count(),
                'student_id' => $student->id
            ]);

            // Apply period filter to submissions
            if ($this->selectedPeriod !== 'all') {
                $startDate = $this->getStartDateForPeriod($this->selectedPeriod);
                if ($startDate) {
                    $submissions = $submissions->filter(function ($submission) use ($startDate) {
                        return $submission->submitted_at && $submission->submitted_at >= $startDate;
                    });
                }
            }

            // Apply subject filter
            if ($this->selectedSubject) {
                $submissions = $submissions->filter(function ($submission) {
                    return $submission->assignment
                        && $submission->assignment->academic_subject_id == $this->selectedSubject;
                });

                // Filter assignments too
                $allAssignments = $allAssignments->filter(function ($assignment) {
                    return $assignment->academic_subject_id == $this->selectedSubject;
                });
            }

            // Calculate metrics only for graded submissions
            $gradedSubmissions = $submissions->where('status', 'graded');

            Log::info('Processing metrics', [
                'graded_count' => $gradedSubmissions->count(),
                'total_assignments' => $allAssignments->count()
            ]);

            $this->calculatePerformanceMetrics($gradedSubmissions, []);
            $this->calculateOverallStats($submissions, [], $allAssignments);
            $this->calculateTrendData($gradedSubmissions, []);

            Log::info('Performance data calculated', [
                'performance_items' => count($this->performanceData),
                'has_overall_stats' => !empty($this->overallStats),
                'trend_items' => count($this->trendData)
            ]);

        } catch (Exception $e) {
            Log::error('Error loading performance data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->performanceData = [];
            $this->overallStats = $this->getEmptyStats();
            $this->trendData = [];

            session()->flash('error', 'Unable to load performance data. Please try again.');
        }
    }

    private function getEmptyStats(): array
    {
        return [
            'total_assignments' => 0,
            'average_percentage' => 0,
            'overall_grade' => 'N/A',
            'completed_assignments' => 0,
            'pending_assignments' => 0,
            'completion_rate' => 0,
            'study_streak' => 0,
            'total_subjects' => 0,
        ];
    }

    /**
     * Get all assignments targeted to a student through various relationships
     */
    private function getStudentAssignments($student)
    {
        return Assignment::where('status', 'published')
            ->where(function ($query) use ($student) {
                // Direct assignment to student
                $query->whereHas('students', function ($q) use ($student) {
                    $q->where('students.id', $student->id);
                })
                    // Assignment through academic level
                    ->orWhereHas('academicLevels', function ($q) use ($student) {
                        $q->where('academic_levels.id', $student->academic_level_id);
                    })
                    // Assignment through academic group
                    ->orWhereHas('academicGroups', function ($q) use ($student) {
                        $q->where('academic_groups.id', $student->academic_group_id);
                    })
                    // Assignment through student groups
                    ->orWhereHas('studentGroups', function ($q) use ($student) {
                        $q->whereHas('students', function ($sq) use ($student) {
                            $sq->where('students.id', $student->id);
                        });
                    });
            })
            ->with('academicSubject')
            ->get();
    }

    private function getStartDateForPeriod($period)
    {
        return match ($period) {
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'quarter' => Carbon::now()->startOfQuarter(),
            'year' => Carbon::now()->startOfYear(),
            default => null,
        };
    }

    private function calculatePerformanceMetrics($submissions, $assessments = [])
    {
        try {
            $allData = collect();

            // Add submissions data
            foreach ($submissions as $submission) {
                if (!$submission->assignment) {
                    Log::warning('Submission missing assignment', ['submission_id' => $submission->id]);
                    continue;
                }

                if ($submission->score !== null && $submission->total_marks > 0) {
                    $allData->push([
                        'subject_id' => $submission->assignment->academic_subject_id,
                        'subject_name' => $submission->assignment->academicSubject?->name ?? 'Unknown',
                        'score' => $submission->score,
                        'max_score' => $submission->total_marks,
                        'date' => $submission->submitted_at ?? now(),
                        'type' => 'assignment'
                    ]);
                }
            }

            // Add assessments data
            $assessmentsCollection = is_array($assessments) ? collect($assessments) : collect();
            foreach ($assessmentsCollection as $assessment) {
                if ($assessment->score !== null && $assessment->max_score > 0) {
                    $allData->push([
                        'subject_id' => $assessment->academic_subject_id,
                        'subject_name' => $assessment->academicSubject?->name ?? 'Unknown',
                        'score' => $assessment->score,
                        'max_score' => $assessment->max_score,
                        'date' => $assessment->created_at ?? now(),
                        'type' => 'assessment'
                    ]);
                }
            }

            Log::info('Performance metrics data collected', [
                'total_items' => $allData->count(),
                'subjects' => $allData->pluck('subject_id')->unique()->count()
            ]);

            if ($allData->isEmpty()) {
                Log::info('No performance data to display');
                $this->performanceData = [];
                return;
            }

            $this->performanceData = $allData
                ->groupBy('subject_id')
                ->map(function ($group) {
                    $subjectName = $group->first()['subject_name'];

                    $validItems = $group->filter(function ($item) {
                        return $item['score'] !== null && $item['max_score'] > 0;
                    });

                    if ($validItems->isEmpty()) {
                        return null; // Filter out later
                    }

                    $scores = $validItems->map(function ($item) {
                        return ($item['score'] / $item['max_score']) * 100;
                    });

                    $totalScore = $validItems->sum('score');
                    $totalMaxScore = $validItems->sum('max_score');
                    $averagePercentage = $totalMaxScore > 0 ? ($totalScore / $totalMaxScore) * 100 : 0;

                    // Calculate trend
                    $sortedItems = $validItems->sortByDesc('date');
                    $recent = $sortedItems->take(3);
                    $previous = $sortedItems->skip(3)->take(3);

                    $recentAvg = $recent->count() > 0 ? $recent->map(function ($item) {
                        return ($item['score'] / $item['max_score']) * 100;
                    })->avg() : 0;

                    $previousAvg = $previous->count() > 0 ? $previous->map(function ($item) {
                        return ($item['score'] / $item['max_score']) * 100;
                    })->avg() : 0;

                    $trend = $recentAvg > $previousAvg ? 'up' : ($recentAvg < $previousAvg ? 'down' : 'stable');

                    return [
                        'subject' => $subjectName,
                        'total_assignments' => $group->count(),
                        'average_score' => round($scores->avg(), 2),
                        'highest_score' => round($scores->max(), 2),
                        'lowest_score' => round($scores->min(), 2),
                        'percentage' => round($averagePercentage, 2),
                        'grade' => $this->calculateGrade($averagePercentage),
                        'trend' => $trend,
                        'recent_performance' => round($recentAvg, 2),
                        'improvement' => round($recentAvg - $previousAvg, 2),
                    ];
                })
                ->filter() // Remove nulls
                ->sortByDesc('percentage')
                ->values()
                ->toArray();

            Log::info('Performance metrics calculated', [
                'subjects_count' => count($this->performanceData)
            ]);

        } catch (Exception $e) {
            Log::error('Error calculating performance metrics', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->performanceData = [];
        }
    }

    private function calculateGrade($percentage)
    {
        return match (true) {
            $percentage >= 97 => 'A+',
            $percentage >= 93 => 'A',
            $percentage >= 90 => 'A-',
            $percentage >= 87 => 'B+',
            $percentage >= 83 => 'B',
            $percentage >= 80 => 'B-',
            $percentage >= 77 => 'C+',
            $percentage >= 73 => 'C',
            $percentage >= 70 => 'C-',
            $percentage >= 67 => 'D+',
            $percentage >= 65 => 'D',
            default => 'F',
        };
    }

    private function calculateOverallStats($submissions, $assessments, $allAssignments)
    {
        try {
            $student = Auth::user()->student;

            if (!$student) {
                $student = Student::withoutGlobalScopes()
                    ->where('user_id', Auth::id())
                    ->first();
            }

            if (!$student) {
                $this->overallStats = $this->getEmptyStats();
                return;
            }

            // Convert collections properly
            $submissionsCollection = $submissions instanceof Collection
                ? $submissions
                : collect($submissions);

            $assessmentsCollection = is_array($assessments)
                ? collect($assessments)
                : ($assessments instanceof Collection ? $assessments : collect());

            $assignmentsCollection = $allAssignments instanceof Collection
                ? $allAssignments
                : collect($allAssignments);

            // Apply period filter to assignments
            if ($this->selectedPeriod !== 'all') {
                $startDate = $this->getStartDateForPeriod($this->selectedPeriod);
                if ($startDate) {
                    $assignmentsCollection = $assignmentsCollection->filter(function ($assignment) use ($startDate) {
                        return $assignment->starts_at && $assignment->starts_at >= $startDate;
                    });
                }
            }

            $totalItems = $assignmentsCollection->count() + $assessmentsCollection->count();

            // Count graded items
            $gradedSubmissions = $submissionsCollection->where('status', 'graded');
            $gradedAssessments = $assessmentsCollection->whereIn('status', [
                Assessment::STATUS_COMPLETED,
                Assessment::STATUS_GRADED
            ]);

            $totalGraded = $gradedSubmissions->count() + $gradedAssessments->count();

            // Combine all graded items for statistics
            $allGradedData = collect();

            foreach ($gradedSubmissions as $submission) {
                if ($submission->score !== null && $submission->total_marks > 0 && $submission->assignment) {
                    $allGradedData->push([
                        'score' => $submission->score,
                        'max_score' => $submission->total_marks,
                        'date' => $submission->submitted_at ?? now(),
                        'subject_id' => $submission->assignment->academic_subject_id
                    ]);
                }
            }

            foreach ($gradedAssessments as $assessment) {
                if ($assessment->score !== null && $assessment->max_score > 0) {
                    $allGradedData->push([
                        'score' => $assessment->score,
                        'max_score' => $assessment->max_score,
                        'date' => $assessment->created_at ?? now(),
                        'subject_id' => $assessment->academic_subject_id
                    ]);
                }
            }

            if ($allGradedData->isEmpty()) {
                $this->overallStats = [
                    'total_assignments' => $totalItems,
                    'available_assignments' => $assignmentsCollection->filter(function ($a) {
                        return $a->ends_at && $a->ends_at > now();
                    })->count(),
                    'average_percentage' => 0,
                    'overall_grade' => 'N/A',
                    'completed_assignments' => $totalGraded,
                    'submitted_assignments' => $submissionsCollection->whereIn('status', ['submitted', 'in_progress'])->count(),
                    'pending_assignments' => max(0, $totalItems - $totalGraded),
                    'completion_rate' => $totalItems > 0 ? round(($totalGraded / $totalItems) * 100, 2) : 0,
                    'study_streak' => $this->calculateStudyStreak($student),
                    'total_subjects' => 0,
                ];

                Log::info('Overall stats (empty data)', $this->overallStats);
                return;
            }

            $totalScore = $allGradedData->sum('score');
            $totalMaxScore = $allGradedData->sum('max_score');
            $overallPercentage = $totalMaxScore > 0 ? ($totalScore / $totalMaxScore) * 100 : 0;

            $this->overallStats = [
                'total_assignments' => $totalItems,
                'available_assignments' => $assignmentsCollection->filter(function ($a) {
                    return $a->ends_at && $a->ends_at > now();
                })->count(),
                'average_percentage' => round($overallPercentage, 2),
                'overall_grade' => $this->calculateGrade($overallPercentage),
                'completed_assignments' => $totalGraded,
                'submitted_assignments' => $submissionsCollection->whereIn('status', ['submitted', 'in_progress'])->count(),
                'pending_assignments' => max(0, $totalItems - $totalGraded),
                'completion_rate' => $totalItems > 0 ? round(($totalGraded / $totalItems) * 100, 2) : 0,
                'study_streak' => $this->calculateStudyStreak($student),
                'total_subjects' => $allGradedData->pluck('subject_id')->filter()->unique()->count(),
            ];

            Log::info('Overall stats calculated', $this->overallStats);

        } catch (Exception $e) {
            Log::error('Error calculating overall stats', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->overallStats = $this->getEmptyStats();
        }
    }

    private function calculateStudyStreak($student)
    {
        $consecutiveDays = 0;
        $currentDate = Carbon::now()->startOfDay();

        while ($currentDate->greaterThan(Carbon::now()->subDays(365))) {
            $hasSubmission = AssignmentSubmission::where('student_id', $student->id)
                ->whereDate('submitted_at', $currentDate)
                ->exists();

            if ($hasSubmission) {
                $consecutiveDays++;
                $currentDate->subDay();
            } else {
                break;
            }
        }

        return $consecutiveDays;
    }

    private function calculateTrendData($submissions, $assessments)
    {
        $allData = collect();

        // Add submissions
        foreach ($submissions as $submission) {
            if ($submission->score !== null && $submission->total_marks > 0 && $submission->submitted_at) {
                $allData->push([
                    'date' => $submission->submitted_at,
                    'percentage' => ($submission->score / $submission->total_marks) * 100
                ]);
            }
        }

        // Add assessments
        $assessmentsCollection = is_array($assessments) ? collect($assessments) : $assessments;
        foreach ($assessmentsCollection as $assessment) {
            if ($assessment->score !== null && $assessment->max_score > 0 && $assessment->created_at) {
                $allData->push([
                    'date' => $assessment->created_at,
                    'percentage' => ($assessment->score / $assessment->max_score) * 100
                ]);
            }
        }

        if ($allData->isEmpty()) {
            $this->trendData = [];
            return;
        }

        // Group by week for trend analysis
        $weeklyData = $allData->groupBy(function ($item) {
            return $item['date']->format('Y-W');
        })->map(function ($group) {
            return [
                'week' => $group->first()['date']->format('M d'),
                'percentage' => round($group->avg('percentage'), 1),
                'count' => $group->count()
            ];
        })->take(8)->reverse()->values();

        $this->trendData = $weeklyData->toArray();
    }

    private function generateInsights()
    {
        if (empty($this->performanceData) || empty($this->overallStats)) {
            $this->insights = [];
            return;
        }

        $insights = [];

        // Find strongest subject
        $strongest = collect($this->performanceData)->sortByDesc('percentage')->first();
        if ($strongest) {
            $insights[] = [
                'type' => 'strength',
                'title' => 'Top Performing Subject',
                'message' => "You're excelling in {$strongest['subject']} with {$strongest['percentage']}% average!",
                'action' => 'Keep up the great work!',
                'color' => 'green'
            ];
        }

        // Find improvement opportunities
        $needsWork = collect($this->performanceData)->sortBy('percentage')->first();
        if ($needsWork && $needsWork['percentage'] < 70) {
            $insights[] = [
                'type' => 'improvement',
                'title' => 'Focus Area Identified',
                'message' => "{$needsWork['subject']} needs attention - currently at {$needsWork['percentage']}%",
                'action' => 'Consider additional practice in this subject',
                'color' => 'orange'
            ];
        }

        // Study consistency insight
        $streak = $this->overallStats['study_streak'];
        if ($streak >= 7) {
            $insights[] = [
                'type' => 'consistency',
                'title' => 'Excellent Consistency',
                'message' => "{$streak} day study streak! You're building great habits.",
                'action' => 'Keep maintaining this momentum!',
                'color' => 'blue'
            ];
        } elseif ($streak < 3) {
            $insights[] = [
                'type' => 'consistency',
                'title' => 'Consistency Opportunity',
                'message' => 'Try to maintain daily practice for better results.',
                'action' => 'Aim for at least one assignment per day',
                'color' => 'yellow'
            ];
        }

        // Performance trend insight
        $improvingSubjects = collect($this->performanceData)->where('trend', 'up')->count();
        if ($improvingSubjects > 0) {
            $insights[] = [
                'type' => 'trend',
                'title' => 'Positive Progress',
                'message' => "You're improving in {$improvingSubjects} subject(s)!",
                'action' => 'Your hard work is paying off',
                'color' => 'green'
            ];
        }

        $this->insights = $insights;
    }

    public function updatedSelectedPeriod()
    {
        $this->loadPerformanceData();
        $this->generateInsights();
        $this->dispatch('periodChanged', $this->selectedPeriod);
    }

    public function updatedSelectedSubject()
    {
        $this->loadPerformanceData();
        $this->generateInsights();
        $this->dispatch('subjectChanged', $this->selectedSubject);
    }

    public function exportData()
    {
        // This would export performance data as CSV/PDF
        $this->dispatch('exportRequested', [
            'performance' => $this->performanceData,
            'stats' => $this->overallStats,
            'period' => $this->selectedPeriod
        ]);
    }

    public function render()
    {
        return view('livewire.students.performance-overview');
    }

    /**
     * Load upcoming and pending assignments for the student
     */
    private function loadUpcomingAndPendingAssignments($allAssignments, $submissions)
    {
        $submittedAssignmentIds = $submissions->pluck('assignment_id')->unique();

        // Upcoming assignments (not started, starts in the future)
        $this->upcomingAssignments = $allAssignments
            ->whereNotIn('id', $submittedAssignmentIds)
            ->where('starts_at', '>', now())
            ->sortBy('starts_at')
            ->take(5)
            ->values()
            ->toArray();

        // Pending assignments (available now, not completed)
        $this->pendingAssignments = $allAssignments
            ->filter(function ($assignment) use ($submissions) {
                $submission = $submissions->firstWhere('assignment_id', $assignment->id);

                return $assignment->starts_at <= now()
                    && $assignment->ends_at > now()
                    && (!$submission || !in_array($submission->status, ['graded', 'submitted']));
            })
            ->sortBy('ends_at')
            ->take(5)
            ->map(function ($assignment) use ($submissions) {
                $submission = $submissions->firstWhere('assignment_id', $assignment->id);

                return [
                    'assignment' => $assignment,
                    'status' => $submission ? $submission->status : 'not_started',
                    'time_remaining' => now()->diffInHours($assignment->ends_at),
                ];
            })
            ->values()
            ->toArray();
    }
}
