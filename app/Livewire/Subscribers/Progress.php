<?php

namespace App\Livewire\Subscribers;

use App\Models\Assessment;
use App\Models\BookSubscription;
use App\Models\Quiz;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Carbon\Carbon;

class Progress extends Component
{
    public $timeframe = 'month'; // 'week', 'month', 'year'
    public $progressData = [];
    public $stats = [];

    public function mount()
    {
        $this->loadProgressData();
    }

    public function updatedTimeframe()
    {
        $this->loadProgressData();
    }

    public function loadProgressData()
    {
        $user = Auth::user();

        if (!$user->student) {
            return;
        }

        $startDate = match($this->timeframe) {
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'year' => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfMonth(),
        };

        // Assessment progress
        $assessments = Assessment::where('student_id', $user->student->id)
            ->where('created_at', '>=', $startDate)
            ->get();

        // Reading progress (book subscriptions)
        $readingProgress = BookSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->count();

        $this->stats = [
            'assessments_taken' => $assessments->count(),
            'average_score' => $assessments->avg('score') ?? 0,
            'books_subscribed' => $readingProgress,
            'study_streak' => $this->calculateStudyStreak(),
        ];

        $this->progressData = $this->formatProgressData($assessments);
    }

    private function calculateStudyStreak()
    {
        // Simple streak calculation - days with activity
        $user = Auth::user();
        $streak = 0;
        $date = Carbon::today();

        while ($date->gte(Carbon::today()->subDays(30))) {
            $hasActivity = Assessment::where('student_id', $user->student->id ?? 0)
                ->whereDate('created_at', $date)
                ->exists();

            if ($hasActivity) {
                $streak++;
            } else {
                break;
            }

            $date->subDay();
        }

        return $streak;
    }

    private function formatProgressData($assessments)
    {
        return $assessments->groupBy(function ($assessment) {
            return $assessment->created_at->format('Y-m-d');
        })->map(function ($dayAssessments) {
            return [
                'date' => $dayAssessments->first()->created_at->format('M d'),
                'count' => $dayAssessments->count(),
                'average_score' => $dayAssessments->avg('score'),
            ];
        })->values();
    }

    public function render()
    {
        return view('livewire.subscribers.progress');
    }
}
