<?php

namespace App\Livewire\Students;

use App\Models\Assessment;
use App\Models\AcademicSubject as Subject;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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

    // Get subjects that have assessments for this student
    $subjectsWithAssessments = Assessment::where('student_id', $student->id)
        ->whereNotNull('subject_id')
        ->distinct()
        ->pluck('subject_id')
        ->toArray();

    if (empty($subjectsWithAssessments)) {
        $this->subjects = [];
        return;
    }

    $this->subjects = Subject::whereIn('id', $subjectsWithAssessments)
        ->orderBy('name')
        ->pluck('name', 'id')
        ->toArray();
}

    public function loadPerformanceData()
    {
        $student = Auth::user()->student;
        if (!$student) return;

        $query = Assessment::with(['subject'])
            ->where('student_id', $student->id)
            ->whereNotNull('subject_id')
            ->where('status', 'completed'); // Only include completed assessments

        // Apply period filter
        if ($this->selectedPeriod !== 'all') {
            $startDate = $this->getStartDateForPeriod($this->selectedPeriod);
            $query->where('created_at', '>=', $startDate);
        }

        // Apply subject filter
        if ($this->selectedSubject) {
            $query->where('subject_id', $this->selectedSubject);
        }

        $assessments = $query->orderBy('created_at', 'desc')->get();

        $this->calculatePerformanceMetrics($assessments);
        $this->calculateOverallStats($assessments);
        $this->calculateTrendData($assessments);
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

private function calculatePerformanceMetrics($assessments)
{
    if ($assessments->isEmpty()) {
        $this->performanceData = [];
        return;
    }

    $this->performanceData = $assessments
        ->groupBy('subject_id')
        ->map(function ($group, $subjectId) {
            $subject = $group->first()->subject;
            $subjectName = $subject ? $subject->name : 'Unknown Subject';

            // Filter out assessments with null scores
            $validAssessments = $group->filter(function($assessment) {
                return $assessment->score !== null && $assessment->max_score !== null && $assessment->max_score > 0;
            });

            if ($validAssessments->isEmpty()) {
                return [
                    'subject' => $subjectName,
                    'total_assessments' => $group->count(),
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

            $scores = $validAssessments->pluck('score');
            $maxScores = $validAssessments->pluck('max_score');

            $totalScore = $scores->sum();
            $totalMaxScore = $maxScores->sum();
            $averagePercentage = $totalMaxScore > 0 ? ($totalScore / $totalMaxScore) * 100 : 0;

            // Calculate trend (compare last 3 assessments with previous 3)
            $sortedAssessments = $validAssessments->sortByDesc('created_at');
            $recent = $sortedAssessments->take(3);
            $previous = $sortedAssessments->skip(3)->take(3);

            $recentAvg = $recent->count() > 0 ? $recent->avg(function($a) {
                return $a->max_score > 0 ? ($a->score / $a->max_score) * 100 : 0;
            }) : 0;

            $previousAvg = $previous->count() > 0 ? $previous->avg(function($a) {
                return $a->max_score > 0 ? ($a->score / $a->max_score) * 100 : 0;
            }) : 0;

            $trend = $recentAvg > $previousAvg ? 'up' : ($recentAvg < $previousAvg ? 'down' : 'stable');

            return [
                'subject' => $subjectName,
                'total_assessments' => $group->count(),
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

    private function calculateOverallStats($assessments)
    {
        $student = Auth::user()->student;
        if (!$student) {
            $this->overallStats = $this->getEmptyStats();
            return;
        }

        // Get all assessments for this student (not just completed ones for totals)
        $allAssessmentsQuery = Assessment::where('student_id', $student->id);

        if ($this->selectedPeriod !== 'all') {
            $startDate = $this->getStartDateForPeriod($this->selectedPeriod);
            $allAssessmentsQuery->where('created_at', '>=', $startDate);
        }

        if ($this->selectedSubject) {
            $allAssessmentsQuery->where('subject_id', $this->selectedSubject);
        }

        $totalAssessments = $allAssessmentsQuery->count();
        $completedAssessments = $assessments->count();
        $pendingAssessments = $allAssessmentsQuery->whereIn('status', ['pending', 'in_progress'])->count();

        if ($assessments->isEmpty()) {
            $this->overallStats = [
                'total_assessments' => $totalAssessments,
                'average_percentage' => 0,
                'overall_grade' => 'N/A',
                'completed_assessments' => $completedAssessments,
                'pending_assessments' => $pendingAssessments,
                'completion_rate' => $totalAssessments > 0 ? round(($completedAssessments / $totalAssessments) * 100, 2) : 0,
                'study_streak' => $this->calculateStudyStreak($student),
                'total_subjects' => 0,
            ];
            return;
        }

        // Calculate scores only from completed assessments
        $scores = $assessments->filter(function($assessment) {
            return $assessment->score !== null && $assessment->max_score !== null && $assessment->max_score > 0;
        });

        $totalScore = $scores->sum('score');
        $totalMaxScore = $scores->sum('max_score');
        $overallPercentage = $totalMaxScore > 0 ? ($totalScore / $totalMaxScore) * 100 : 0;

        $this->overallStats = [
            'total_assessments' => $totalAssessments,
            'average_percentage' => round($overallPercentage, 2),
            'overall_grade' => $this->calculateGrade($overallPercentage),
            'completed_assessments' => $completedAssessments,
            'pending_assessments' => $pendingAssessments,
            'completion_rate' => $totalAssessments > 0 ? round(($completedAssessments / $totalAssessments) * 100, 2) : 0,
            'study_streak' => $this->calculateStudyStreak($student),
            'total_subjects' => $assessments->pluck('subject_id')->unique()->count(),
        ];
    }

    private function calculateTrendData($assessments)
    {
        // Group assessments by week for trend analysis
        $weeklyData = $assessments->groupBy(function ($assessment) {
            return $assessment->created_at->format('Y-W');
        })->map(function ($group) {
            $scores = $group->pluck('score');
            $maxScores = $group->pluck('max_score');
            $percentage = $maxScores->sum() > 0 ? ($scores->sum() / $maxScores->sum()) * 100 : 0;

            return [
                'week' => $group->first()->created_at->format('M d'),
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
        $hasAssessment = Assessment::where('student_id', $student->id)
            ->whereDate('created_at', $currentDate)
            ->exists();

        if ($hasAssessment) {
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
                'action' => 'Aim for at least one assessment per day',
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
        'total_assessments' => 0,
        'average_percentage' => 0,
        'overall_grade' => 'N/A',
        'completed_assessments' => 0,
        'pending_assessments' => 0,
        'completion_rate' => 0,
        'study_streak' => 0,
        'total_subjects' => 0,
    ];
}
}
