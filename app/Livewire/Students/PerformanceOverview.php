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
        if (!$student) return;

        // Cache subjects for better performance
        $cacheKey = "student_subjects_{$student->id}";

        $this->subjects = Cache::remember($cacheKey, 300, function () use ($student) {
            return Subject::whereIn('id', function ($query) use ($student) {
                $query->select('subject_id')
                    ->from('assessments')
                    ->where('student_id', $student->id)
                    ->whereNotNull('subject_id')
                    ->distinct();
            })->orderBy('name')->pluck('name', 'id')->toArray();
        });
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
        $this->performanceData = $assessments
            ->groupBy(fn($a) => $a->subject?->name ?? 'Unknown')
            ->map(function ($group, $subjectName) {
                $scores = $group->pluck('score')->filter();
                $maxScores = $group->pluck('max_score')->filter();

                $totalScore = $scores->sum();
                $totalMaxScore = $maxScores->sum();
                $averagePercentage = $totalMaxScore > 0 ? ($totalScore / $totalMaxScore) * 100 : 0;

                // Calculate trend (compare last 3 assessments with previous 3)
                $recentScores = $group->take(3)->pluck('score')->avg() ?? 0;
                $previousScores = $group->skip(3)->take(3)->pluck('score')->avg() ?? 0;
                $trend = $recentScores > $previousScores ? 'up' : ($recentScores < $previousScores ? 'down' : 'stable');

                return [
                    'subject' => $subjectName,
                    'total_assessments' => $group->count(),
                    'average_score' => round($scores->avg() ?? 0, 2),
                    'highest_score' => $scores->max() ?? 0,
                    'lowest_score' => $scores->min() ?? 0,
                    'percentage' => round($averagePercentage, 2),
                    'grade' => $this->calculateGrade($averagePercentage),
                    'trend' => $trend,
                    'recent_performance' => round($recentScores, 2),
                    'improvement' => round($recentScores - $previousScores, 2),
                ];
            })
            ->when($this->selectedSubject, function ($collection) {
                return $collection->filter(fn($item) => $item['subject'] === ($this->subjects[$this->selectedSubject] ?? null));
            })
            ->sortByDesc('percentage')
            ->values()
            ->toArray();
    }

    private function calculateOverallStats($assessments)
    {
        if ($assessments->isEmpty()) {
            $this->overallStats = [
                'total_assessments' => 0,
                'average_percentage' => 0,
                'overall_grade' => 'N/A',
                'completed_assessments' => 0,
                'pending_assessments' => 0,
                'completion_rate' => 0,
                'study_streak' => 0,
            ];
            return;
        }

        $scores = $assessments->pluck('score')->filter();
        $maxScores = $assessments->pluck('max_score')->filter();

        $totalScore = $scores->sum();
        $totalMaxScore = $maxScores->sum();
        $overallPercentage = $totalMaxScore > 0 ? ($totalScore / $totalMaxScore) * 100 : 0;

        // Count completed vs pending assessments (including all statuses)
        $student = Auth::user()->student;
        $allAssessments = Assessment::where('student_id', $student->id);

        if ($this->selectedPeriod !== 'all') {
            $startDate = $this->getStartDateForPeriod($this->selectedPeriod);
            $allAssessments->where('created_at', '>=', $startDate);
        }

        $totalAll = $allAssessments->count();
        $completed = $assessments->count();
        $pending = $totalAll - $completed;

        // Calculate study streak
        $streak = $this->calculateStudyStreak($student);

        $this->overallStats = [
            'total_assessments' => $totalAll,
            'average_percentage' => round($overallPercentage, 2),
            'overall_grade' => $this->calculateGrade($overallPercentage),
            'completed_assessments' => $completed,
            'pending_assessments' => $pending,
            'completion_rate' => $totalAll > 0 ? round(($completed / $totalAll) * 100, 2) : 0,
            'study_streak' => $streak,
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
        $streak = 0;
        $currentDate = Carbon::today();

        // Look back up to 30 days
        for ($i = 0; $i < 30; $i++) {
            $hasActivity = Assessment::where('student_id', $student->id)
                ->whereDate('created_at', $currentDate)
                ->exists();

            if ($hasActivity) {
                $streak++;
                $currentDate->subDay();
            } else {
                break;
            }
        }

        return $streak;
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
}
