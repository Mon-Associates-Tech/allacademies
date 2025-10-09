<?php

namespace App\Livewire\Students;

use App\Models\AssignmentSubmission;
use App\Models\AcademicSubject as Subject;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PerformanceOverview extends Component
{
    public $selectedPeriod = 'all';
    public $selectedSubject = '';
    public $subjects = [];
    public $performanceData = [];
    public $overallStats = [];
    public $trendData = [];
    public $insights = [];

    protected $listeners = ['refreshPerformanceData' => 'loadPerformanceData'];

    public function mount()
    {
        $this->loadSubjects();
        $this->loadPerformanceData();
        $this->generateInsights();
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

    public function loadSubjects()
    {
        $student = Auth::user()->student;
        if (!$student) {
            $this->subjects = [];
            return;
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
        $student = Auth::user()->student;
        if (!$student) return;

        $query = AssignmentSubmission::with(['assignment.academicSubject', 'assignment'])
            ->where('student_id', $student->id)
            ->whereIn('status', ['graded']); // Only include graded submissions

        // Apply period filter
        if ($this->selectedPeriod !== 'all') {
            $startDate = $this->getStartDateForPeriod($this->selectedPeriod);
            $query->where('submitted_at', '>=', $startDate);
        }

        // Apply subject filter
        if ($this->selectedSubject) {
            $query->whereHas('assignment', function ($q) {
                $q->where('academic_subject_id', $this->selectedSubject);
            });
        }

        $submissions = $query->orderBy('submitted_at', 'desc')->get();

        $this->calculatePerformanceMetrics($submissions);
        $this->calculateOverallStats($submissions);
        $this->calculateTrendData($submissions);
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

    private function calculatePerformanceMetrics($submissions)
    {
        if ($submissions->isEmpty()) {
            $this->performanceData = [];
            return;
        }

        $this->performanceData = $submissions
            ->groupBy(function ($submission) {
                return $submission->assignment->academic_subject_id;
            })
            ->map(function ($group, $subjectId) {
                $subject = $group->first()->assignment->academicSubject;
                $subjectName = $subject ? $subject->name : 'Unknown Subject';

                // Filter out submissions with null scores
                $validSubmissions = $group->filter(function($submission) {
                    return $submission->score !== null && $submission->total_marks !== null && $submission->total_marks > 0;
                });

                if ($validSubmissions->isEmpty()) {
                    return [
                        'subject' => $subjectName,
                        'total_assignments' => $group->count(),
                        'average_score' => 0,
                        'highest_score' => 0,
                        'lowest_score' => 0,
                        'percentage' => 0,
                        'grade' => 'N/A',
                        'trend' => 'stable',
                        'recent_performance' => 0,
                        'improvement' => 0,
                    ];
                }

                $scores = $validSubmissions->pluck('score');
                $totalMarks = $validSubmissions->pluck('total_marks');

                $totalScore = $scores->sum();
                $totalMaxScore = $totalMarks->sum();
                $averagePercentage = $totalMaxScore > 0 ? ($totalScore / $totalMaxScore) * 100 : 0;

                // Calculate trend (compare last 3 submissions with previous 3)
                $sortedSubmissions = $validSubmissions->sortByDesc('submitted_at');
                $recent = $sortedSubmissions->take(3);
                $previous = $sortedSubmissions->skip(3)->take(3);

                $recentAvg = $recent->count() > 0 ? $recent->avg(function($s) {
                    return $s->total_marks > 0 ? ($s->score / $s->total_marks) * 100 : 0;
                }) : 0;

                $previousAvg = $previous->count() > 0 ? $previous->avg(function($s) {
                    return $s->total_marks > 0 ? ($s->score / $s->total_marks) * 100 : 0;
                }) : 0;

                $trend = $recentAvg > $previousAvg ? 'up' : ($recentAvg < $previousAvg ? 'down' : 'stable');

                return [
                    'subject' => $subjectName,
                    'total_assignments' => $group->count(),
                    'average_score' => round($scores->avg(), 2),
                    'highest_score' => $scores->max(),
                    'lowest_score' => $scores->min(),
                    'percentage' => round($averagePercentage, 2),
                    'grade' => $this->calculateGrade($averagePercentage),
                    'trend' => $trend,
                    'recent_performance' => round($recentAvg, 2),
                    'improvement' => round($recentAvg - $previousAvg, 2),
                ];
            })
            ->sortByDesc('percentage')
            ->values()
            ->toArray();
    }

    private function calculateOverallStats($submissions)
    {
        $student = Auth::user()->student;
        if (!$student) {
            $this->overallStats = $this->getEmptyStats();
            return;
        }

        // Get all assignment submissions for this student
        $allSubmissionsQuery = AssignmentSubmission::where('student_id', $student->id);

        if ($this->selectedPeriod !== 'all') {
            $startDate = $this->getStartDateForPeriod($this->selectedPeriod);
            $allSubmissionsQuery->where('submitted_at', '>=', $startDate);
        }

        if ($this->selectedSubject) {
            $allSubmissionsQuery->whereHas('assignment', function ($q) {
                $q->where('academic_subject_id', $this->selectedSubject);
            });
        }

        $totalAssignments = $allSubmissionsQuery->count();
        $gradedSubmissions = $submissions->count();
        $pendingSubmissions = $allSubmissionsQuery->whereIn('status', ['submitted', 'in_progress'])->count();

        if ($submissions->isEmpty()) {
            $this->overallStats = [
                'total_assignments' => $totalAssignments,
                'average_percentage' => 0,
                'overall_grade' => 'N/A',
                'completed_assignments' => $gradedSubmissions,
                'pending_assignments' => $pendingSubmissions,
                'completion_rate' => $totalAssignments > 0 ? round(($gradedSubmissions / $totalAssignments) * 100, 2) : 0,
                'study_streak' => $this->calculateStudyStreak($student),
                'total_subjects' => 0,
            ];
            return;
        }

        // Calculate scores only from graded submissions
        $scores = $submissions->filter(function($submission) {
            return $submission->score !== null && $submission->total_marks !== null && $submission->total_marks > 0;
        });

        $totalScore = $scores->sum('score');
        $totalMaxScore = $scores->sum('total_marks');
        $overallPercentage = $totalMaxScore > 0 ? ($totalScore / $totalMaxScore) * 100 : 0;

        $this->overallStats = [
            'total_assignments' => $totalAssignments,
            'average_percentage' => round($overallPercentage, 2),
            'overall_grade' => $this->calculateGrade($overallPercentage),
            'completed_assignments' => $gradedSubmissions,
            'pending_assignments' => $pendingSubmissions,
            'completion_rate' => $totalAssignments > 0 ? round(($gradedSubmissions / $totalAssignments) * 100, 2) : 0,
            'study_streak' => $this->calculateStudyStreak($student),
            'total_subjects' => $submissions->map(function($s) {
                return $s->assignment->academic_subject_id;
            })->unique()->count(),
        ];
    }

    private function calculateTrendData($submissions)
    {
        // Group submissions by week for trend analysis
        $weeklyData = $submissions->groupBy(function ($submission) {
            return $submission->submitted_at->format('Y-W');
        })->map(function ($group) {
            $scores = $group->pluck('score');
            $totalMarks = $group->pluck('total_marks');
            $percentage = $totalMarks->sum() > 0 ? ($scores->sum() / $totalMarks->sum()) * 100 : 0;

            return [
                'week' => $group->first()->submitted_at->format('M d'),
                'percentage' => round($percentage, 1),
                'count' => $group->count()
            ];
        })->take(8)->reverse()->values();

        $this->trendData = $weeklyData->toArray();
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
}
