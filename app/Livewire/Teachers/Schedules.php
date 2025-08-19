<?php

namespace App\Livewire\Teachers;

use App\Models\AssignmentSubmission;
use Livewire\Component;
use Carbon\Carbon;
use App\Models\Teacher;
use App\Models\Assessment;
use App\Models\Assignment;
use App\Models\AcademicSubject;
use Illuminate\Support\Facades\Auth;

class Schedules extends Component
{
    public $viewMode = 'calendar';
    public $filterType = 'all';
    public $selectedDate;
    public $currentMonth;
    public $currentYear;
    public $monthName;
    public $weekDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    public $calendarData = [];
    public $showActivityModal = false;
    public $selectedActivity = null;
    public $activityType = null;

    public function mount()
    {
        $this->selectedDate = Carbon::today();
        $this->currentMonth = $this->selectedDate->month;
        $this->currentYear = $this->selectedDate->year;
        $this->generateCalendar();
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function setFilterType($type)
    {
        $this->filterType = $type;
    }

    public function changeMonth($direction)
    {
        if ($direction === 'prev') {
            $this->selectedDate = $this->selectedDate->copy()->subMonth();
        } else {
            $this->selectedDate = $this->selectedDate->copy()->addMonth();
        }

        $this->currentMonth = $this->selectedDate->month;
        $this->currentYear = $this->selectedDate->year;
        $this->generateCalendar();
    }

    public function selectDate($date)
    {
        $this->selectedDate = Carbon::parse($date);
    }

    public function showActivityDetails($id, $type)
    {
        $this->activityType = $type;

        switch ($type) {
            case 'assessment':
                $this->selectedActivity = Assessment::find($id);
                break;
            case 'assignment':
                $this->selectedActivity = Assignment::find($id);
                break;
            default:
                $this->selectedActivity = null;
        }

        $this->showActivityModal = true;
    }

    public function closeActivityModal()
    {
        $this->showActivityModal = false;
        $this->selectedActivity = null;
        $this->activityType = null;
    }

    private function generateCalendar()
    {
        $this->monthName = $this->selectedDate->format('F Y');

        $startOfMonth = $this->selectedDate->copy()->startOfMonth();
        $endOfMonth = $this->selectedDate->copy()->endOfMonth();
        $startOfWeek = $startOfMonth->copy()->startOfWeek();
        $endOfWeek = $endOfMonth->copy()->endOfWeek();

        $this->calendarData = [];
        $week = [];

        $currentDay = $startOfWeek->copy();

        while ($currentDay <= $endOfWeek) {
            $dayActivities = $this->getActivitiesForDate($currentDay);

            $dayData = [
                'date' => $currentDay->copy(),
                'isCurrentMonth' => $currentDay->month === $this->currentMonth,
                'isToday' => $currentDay->isToday(),
                'activityCount' => $dayActivities->count(),
                'activities' => $dayActivities
            ];

            $week[] = $dayData;

            if (count($week) === 7) {
                $this->calendarData[] = $week;
                $week = [];
            }

            $currentDay->addDay();
        }
    }

    private function getActivitiesForDate($date)
    {
        $teacher = Auth::user()->teacher;

        if (!$teacher) {
            return collect();
        }

        $activities = collect();

        // Get assessments
        if ($this->filterType === 'all' || $this->filterType === 'assessments') {
//            $assessments = AssignmentSubmission::where('teacher_id', $teacher->id)
//                ->whereDate('', $date)
//                ->get()
//                ->map(function ($assessment) {
//                    return [
//                        'id' => $assessment->id,
//                        'title' => $assessment->title,
//                        'type' => 'assessment',
//                        'date' => $assessment->scheduled_date,
//                        'color' => 'blue',
//                        'icon' => 'fas fa-clipboard-check',
//                        'status' => $assessment->status,
//                        'subject' => $assessment->subject->name ?? 'N/A',
//                        'percentage' => $assessment->percentage_score,
//                        'score' => $assessment->score,
//                        'max_score' => $assessment->max_score,
//                    ];
//                });
//            $activities = $activities->merge($assessments);
        }

        // Get assignments
        if ($this->filterType === 'all' || $this->filterType === 'assignments') {
            $assignments = Assignment::where('teacher_id', $teacher->id)
                ->whereDate('ends_at', $date)
                ->get()
                ->map(function ($assignment) {
                    return [
                        'id' => $assignment->id,
                        'title' => $assignment->title,
                        'type' => 'assignment',
                        'date' => $assignment->ends_at,
                        'color' => 'purple',
                        'icon' => 'fas fa-tasks',
                        'status' => $assignment->status,
                        'subject' => $assignment->subject->name ?? 'N/A',
                    ];
                });
            $activities = $activities->merge($assignments);
        }

        return $activities->sortBy('date');
    }

    public function getActivitiesProperty()
    {
        return $this->getActivitiesForDate($this->selectedDate);
    }

    public function getWeeklyStats()
    {
        $teacher = Auth::user()->teacher;

        if (!$teacher) {
            return [
                'assessments_created' => 0,
                'assignments_created' => 0,
                'average_score' => 0,
            ];
        }

        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

//        $assessmentsCount = Assessment::where('teacher_id', $teacher->id)
//            ->whereBetween('start_time', [$weekStart, $weekEnd])
//            ->count();

        $assignmentsCount = Assignment::where('teacher_id', $teacher->id)
            ->whereBetween('ends_at', [$weekStart, $weekEnd])
            ->count();

        $averageScore = Assessment::where('graded_by', $teacher->id)
            ->whereBetween('start_time', [$weekStart, $weekEnd])
            ->whereNotNull('percentage_score')
            ->avg('percentage_score') ?? 0;

        return [
            'assessments_created' => 0,
            'assignments_created' => $assignmentsCount,
            'average_score' => round($averageScore, 1),
        ];
    }

    public function getMonthlyStats()
    {
        $teacher = Auth::user()->teacher;

        if (!$teacher) {
            return [
                'total_activities' => 0,
                'subjects_taught' => 0,
            ];
        }

        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

//        $assessmentsCount = AssignmentSubmission::where('teacher_id', $teacher->id)
//            ->whereBetween('start_time', [$monthStart, $monthEnd])
//            ->count();

        $assignmentsCount = Assignment::where('teacher_id', $teacher->id)
            ->whereBetween('ends_at', [$monthStart, $monthEnd])
            ->count();

        $subjectsCount = AcademicSubject::whereHas('teachers', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->count();

        return [
            'total_activities' => $assignmentsCount,
            'subjects_taught' => $subjectsCount,
        ];
    }

    public function render()
    {
        return view('livewire.teachers.schedules');
    }
}

