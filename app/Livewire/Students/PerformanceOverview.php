<?php

namespace App\Livewire\Students;

use Livewire\Component;
use App\Models\Assessment;
use App\Models\AcademicSubject as Subject;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PerformanceOverview extends Component
{
    public $selectedPeriod = 'all';
    public $selectedSubject = '';
    public $subjects = [];
    public $performanceData = [];
    public $overallStats = [];

    public function mount()
    {
        $this->loadSubjects();
        $this->loadPerformanceData();
    }

    public function updatedSelectedPeriod()
    {
        $this->loadPerformanceData();
    }

    public function updatedSelectedSubject()
    {
        \Log::info("Subject changed to: ", ['subject' => $this->selectedSubject]);
        $this->loadPerformanceData();
        $this->dispatch('updatedSelectedSubject');
    }

    public function loadSubjects()
    {
        $student = Auth::user()->student;
        if (!$student) return;

        $this->subjects = Subject::whereIn('id', function ($query) use ($student) {
            $query->select('subject_id')
                ->from('assessments')
                ->where('student_id', $student->id)
                ->whereNotNull('subject_id');
        })->pluck('name', 'id')->toArray();
    }

    public function loadPerformanceData()
    {
        $student = Auth::user()->student;
        if (!$student) return;

        $query = Assessment::with(['subject'])
            ->where('student_id', $student->id)
            ->whereNotNull('subject_id');

        // Apply period filter
        if ($this->selectedPeriod !== 'all') {
            $startDate = $this->getStartDateForPeriod($this->selectedPeriod);
            $query->where('created_at', '>=', $startDate);
        }

        // Apply subject filter
        if ($this->selectedSubject) {
            $query->where('subject_id', $this->selectedSubject);
        }

        $assessments = $query->get();

        $this->calculatePerformanceMetrics($assessments);
        $this->calculateOverallStats($assessments);
    }

    private function getStartDateForPeriod($period)
    {
        switch ($period) {
            case 'week':
                return Carbon::now()->startOfWeek();
            case 'month':
                return Carbon::now()->startOfMonth();
            case 'quarter':
                return Carbon::now()->startOfQuarter();
            case 'year':
                return Carbon::now()->startOfYear();
            default:
                return null;
        }
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

            return [
                'subject' => $subjectName,
                'total_assessments' => $group->count(),
                'average_score' => round($scores->avg() ?? 0, 2),
                'highest_score' => $scores->max() ?? 0,
                'lowest_score' => $scores->min() ?? 0,
                'percentage' => round($averagePercentage, 2),
                'grade' => $this->calculateGrade($averagePercentage),
                'trend' => $this->calculateTrend($group),
            ];
        })
        ->when($this->selectedSubject, function ($collection) {
            // If a specific subject is selected, only show that one
            return $collection->filter(fn($item) => $item['subject'] === $this->subjects[$this->selectedSubject] ?? null);
        })
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
            ];
            return;
        }

        $scores = $assessments->pluck('score')->filter();
        $maxScores = $assessments->pluck('max_score')->filter();

        $totalScore = $scores->sum();
        $totalMaxScore = $maxScores->sum();
        $overallPercentage = $totalMaxScore > 0 ? ($totalScore / $totalMaxScore) * 100 : 0;

        // Count completed vs pending assessments
        $completedCount = $assessments->where('status', 'completed')->count();
        $pendingCount = $assessments->whereIn('status', ['in_progress', 'needs_grading'])->count();

        $this->overallStats = [
            'total_assessments' => $assessments->count(),
            'average_percentage' => round($overallPercentage, 2),
            'overall_grade' => $this->calculateGrade($overallPercentage),
            'completed_assessments' => $completedCount,
            'pending_assessments' => $pendingCount,
        ];
    }

    private function calculateGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 85) return 'A';
        if ($percentage >= 80) return 'B+';
        if ($percentage >= 75) return 'B';
        if ($percentage >= 70) return 'C+';
        if ($percentage >= 65) return 'C';
        if ($percentage >= 60) return 'D+';
        if ($percentage >= 55) return 'D';
        return 'F';
    }

    private function calculateTrend($assessments)
    {
        if ($assessments->count() < 2) return 'stable';

        $recent = $assessments->sortByDesc('created_at')->take(3);
        $older = $assessments->sortByDesc('created_at')->skip(3)->take(3);

        $recentAvg = $recent->avg('score') ?? 0;
        $olderAvg = $older->avg('score') ?? 0;

        if ($recentAvg > $olderAvg + 2) return 'improving';
        if ($recentAvg < $olderAvg - 2) return 'declining';
        return 'stable';
    }

    public function render()
    {
        return view('livewire.students.performance-overview', [
            'performanceData' => $this->performanceData,
            'overallStats' => $this->overallStats,
            'subjects' => $this->subjects,
        ]);
    }
}
