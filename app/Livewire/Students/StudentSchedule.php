<?php

namespace App\Livewire\Students;

use App\Models\AssignmentSubmission;
use App\Models\Student;
use App\Models\Assessment;
use App\Models\Assignment;
use App\Models\BookReadingProgress;
use App\Models\Lesson;
use App\Models\Quiz;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class StudentSchedule extends Component
{
    use WithPagination;

    public Student $student;
    public $selectedDate;
    public $viewMode = 'calendar'; // 'calendar', 'list', 'week'
    public $filterType = 'all'; // 'all', 'assessments', 'assignments', 'reading', 'lessons'
    public $currentMonth;
    public $currentYear;
    public $selectedActivity = null;
    public $showActivityModal = false;

    protected $paginationTheme = 'bootstrap';
    protected $authorized = false;

    public function mount(?Student $student, $date = null)
    {
//        $this->student = $student;
        if(!$student ||  !auth()->user()->student) {
            $this->authorized = true;
        }
        else{
            $this->student = auth()->user()->student;
        }

        $this->selectedDate = $date ? Carbon::parse($date) : now();
        $this->currentMonth = $this->selectedDate->month;
        $this->currentYear = $this->selectedDate->year;
    }

    public function render()
    {

        if($this->authorized){
            return view('livewire.students.unauthorized');
        }

        $activities = $this->getActivitiesForPeriod();
        $calendarData = $this->getCalendarData();

        return view('livewire.students.schedule', [
            'activities' => $activities,
            'calendarData' => $calendarData,
            'monthName' => Carbon::create($this->currentYear, $this->currentMonth)->format('F Y'),
            'weekDays' => ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        ]);
    }

    public function getActivitiesForPeriod()
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

        $activities = collect();

        // Get assessments
        if ($this->filterType === 'all' || $this->filterType === 'assessments') {
            $assessments = $this->student->assessments()
                ->with(['subject', 'topic', 'subtopic', 'book'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get()
                ->map(function ($assessment) {
                    return [
                        'id' => $assessment->id,
                        'type' => 'assessment',
                        'title' => $assessment->title,
                        'subject' => $assessment->subject?->name,
                        'topic' => $assessment->topic?->name,
                        'date' => $assessment->start_time ?? $assessment->created_at,
                        'status' => $assessment->status,
                        'score' => $assessment->score,
                        'max_score' => $assessment->max_score,
                        'percentage' => $assessment->percentage_score,
                        'duration' => $this->calculateDuration($assessment->start_time, $assessment->end_time),
                        'model' => $assessment,
                        'icon' => 'fas fa-clipboard-check',
                        'color' => $this->getStatusColor($assessment->status, 'assessment'),
                    ];
                });
            $activities = $activities->merge($assessments);
        }

        // Get assignments (if Assignment model exists)
        if (class_exists(Assignment::class) && ($this->filterType === 'all' || $this->filterType === 'assignments')) {
            $assignments = Assignment::whereHas('students', function ($query) {
                $query->where('student_id', $this->student->id);
            })
                ->with(['subject', 'submissions' => function ($query) {
                    $query->where('student_id', $this->student->id);
                }])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get()
                ->map(function ($assignment) {
                    $submission = $assignment->submissions->first();
                    return [
                        'id' => $assignment->id,
                        'type' => 'assignment',
                        'title' => $assignment->title,
                        'subject' => $assignment->subject?->name,
                        'date' => $assignment->due_date ?? $assignment->created_at,
                        'status' => $submission ? $submission->status : 'not_submitted',
                        'submitted_at' => $submission?->submitted_at,
                        'grade' => $submission?->grade,
                        'model' => $assignment,
                        'submission' => $submission,
                        'icon' => 'fas fa-tasks',
                        'color' => $this->getStatusColor($submission?->status ?? 'not_submitted', 'assignment'),
                    ];
                });
            $activities = $activities->merge($assignments);
        }

        // Get reading progress
        if ($this->filterType === 'all' || $this->filterType === 'reading') {
            $readingProgress = BookReadingProgress::where('user_id', auth()->user()->id)
                ->with(['book', 'book.subject'])
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->get()
                ->map(function ($progress) {
                    return [
                        'id' => $progress->id,
                        'type' => 'reading',
                        'title' => "Reading: " . $progress->book->title,
                        'subject' => $progress->book->subject?->name,
                        'date' => $progress->updated_at,
                        'status' => $progress->status,
                        'progress' => $progress->progress_percentage,
                        'pages_read' => $progress->current_page,
                        'total_pages' => $progress->book->total_pages,
                        'model' => $progress,
                        'icon' => 'fas fa-book-open',
                        'color' => $this->getProgressColor($progress->progress_percentage),
                    ];
                });
            $activities = $activities->merge($readingProgress);
        }

        // Get lessons (if applicable)
        if (class_exists(Lesson::class) && ($this->filterType === 'all' || $this->filterType === 'lessons')) {
            // This would depend on how lessons are structured in your app
            // Assuming lessons are connected to students through subjects or groups
        }

        return $activities->sortByDesc('date');
    }

    public function getCalendarData()
    {
        $startOfMonth = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // Get all activities for the month
        $activities = $this->student->assessments()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get()
            ->groupBy(function ($assessment) {
                return $assessment->created_at->format('Y-m-d');
            });

        // Add other activity types here...

        $calendar = [];
        $currentDate = $startOfMonth->copy()->startOfWeek(Carbon::SUNDAY);


        for ($week = 0; $week < 6; $week++) {
            for ($day = 0; $day < 7; $day++) {
                $dateKey = $currentDate->format('Y-m-d');
                $calendar[$week][$day] = [
                    'date' => $currentDate->copy(),
                    'isCurrentMonth' => $currentDate->month === $this->currentMonth,
                    'isToday' => $currentDate->isToday(),
                    'activities' => $activities->get($dateKey, collect()),
                    'activityCount' => $activities->get($dateKey, collect())->count(),
                ];
                $currentDate->addDay();
            }
        }

        return $calendar;
    }

    public function selectDate($date)
    {
        $this->selectedDate = Carbon::parse($date);
        $this->viewMode = 'list';
    }

    public function changeMonth($direction)
    {
        if ($direction === 'next') {
            $this->currentMonth++;
            if ($this->currentMonth > 12) {
                $this->currentMonth = 1;
                $this->currentYear++;
            }
        } else {
            $this->currentMonth--;
            if ($this->currentMonth < 1) {
                $this->currentMonth = 12;
                $this->currentYear--;
            }
        }
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function setFilterType($type)
    {
        $this->filterType = $type;
        $this->resetPage();
    }

    public function showActivityDetails($activityId, $activityType)
    {
        $this->selectedActivity = $this->getActivityById($activityId, $activityType);
        $this->showActivityModal = true;
    }

    public function closeActivityModal()
    {
        $this->showActivityModal = false;
        $this->selectedActivity = null;
    }

    private function getActivityById($id, $type)
    {
        switch ($type) {
            case 'assessment':
                return Assessment::with(['subject', 'topic', 'subtopic', 'book', 'responses'])
                    ->find($id);
            case 'assignment':
                if (class_exists('App\Models\Assignment')) {
                    return Assignment::with(['subject', 'submissions' => function ($query) {
                        $query->where('student_id', $this->student->id);
                    }])->find($id);
                }
                break;
            case 'reading':
                return BookReadingProgress::with(['book', 'book.subject'])->find($id);
        }
        return null;
    }

    private function getStartDate()
    {
        switch ($this->viewMode) {
            case 'week':
                return $this->selectedDate->copy()->startOfWeek();
            case 'calendar':
                return Carbon::create($this->currentYear, $this->currentMonth, 1);
            default:
                return $this->selectedDate->copy()->startOfDay();
        }
    }

    private function getEndDate()
    {
        switch ($this->viewMode) {
            case 'week':
                return $this->selectedDate->copy()->endOfWeek();
            case 'calendar':
                return Carbon::create($this->currentYear, $this->currentMonth, 1)->endOfMonth();
            default:
                return $this->selectedDate->copy()->endOfDay();
        }
    }

    private function calculateDuration($startTime, $endTime)
    {
        if (!$startTime || !$endTime) {
            return null;
        }
        return Carbon::parse($startTime)->diffForHumans(Carbon::parse($endTime), true);
    }

    private function getStatusColor($status, $type)
    {
        $colors = [
            'assessment' => [
                'completed' => 'green',
                'in_progress' => 'yellow',
                'graded' => 'blue',
                'default' => 'gray',
            ],
            'assignment' => [
                'submitted' => 'green',
                'graded' => 'blue',
                'late' => 'red',
                'not_submitted' => 'gray',
                'default' => 'gray',
            ],
        ];

        return $colors[$type][$status] ?? $colors[$type]['default'];
    }

    private function getProgressColor($percentage)
    {
        if ($percentage >= 80) return 'green';
        if ($percentage >= 60) return 'blue';
        if ($percentage >= 40) return 'yellow';
        return 'red';
    }

    // Statistics methods
    public function getWeeklyStats()
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        return [
            'assessments_completed' => $this->student->assessments()
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->count(),
            'assignments_submitted' => AssignmentSubmission::where('student_id', $this->student->id)->get(), // Will depend on Assignment model structure
            'books_progress' => BookReadingProgress::where('user_id', auth()->user()->id)
                ->whereBetween('updated_at', [$startOfWeek, $endOfWeek])
                ->avg('progress_percentage'),
            'average_score' => $this->student->assessments()
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->avg('percentage_score'),
        ];
    }

    public function getMonthlyStats()
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        return [
            'total_activities' => $this->getActivitiesForPeriod()->count(),
            'assessments_count' => $this->student->assessments()
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count(),
            'average_score' => $this->student->assessments()
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->avg('percentage_score'),
            'subjects_engaged' => $this->student->assessments()
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->distinct('subject_id')
                ->count(),
        ];
    }
}
