<?php

namespace App\Livewire\Students;

use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\View\View;
use Livewire\Component;
use App\Models\Assessment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class StudentSchedule extends Component
{
    public $viewType = 'month'; // month, week, day
    public $currentDate;
    public $calendarStartDate;
    public $calendarEndDate;
    public $assessments = [];
    public $selectedEvent = null;
    public $selectedStatus = null;
    public $hasQuestions = false;
    public $selectedEventQuestions = [];

    public function mount(): void
    {
        $this->currentDate = Carbon::now()->addDays(-7);
        $this->updateCalendarDates();

        // Log schedule page access
        activity()->performedOn(auth()->user()->student)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'accessed_schedule_page',
                'page' => 'schedule',
                'view_type' => $this->viewType
            ])
            ->log('Student accessed schedule page');
    }

    public function updatedSelectedStatus(): void
    {
        $this->loadAssessments();

        // Log status filter change
        activity()->performedOn(auth()->user()->student)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'filtered_schedule_by_status',
                'selected_status' => $this->selectedStatus
            ])
            ->log('Student filtered schedule by status');
    }

    public function updateCalendarDates(): void
    {
        switch ($this->viewType) {
            case 'month':
                $this->calendarStartDate = $this->currentDate->copy()->startOfMonth()->startOfWeek();
                $this->calendarEndDate = $this->currentDate->copy()->endOfMonth()->endOfWeek();
                break;
            case 'week':
                $this->calendarStartDate = $this->currentDate->copy()->startOfWeek();
                $this->calendarEndDate = $this->currentDate->copy()->endOfWeek();
                break;
            case 'day':
                $this->calendarStartDate = $this->currentDate->copy()->startOfDay();
                $this->calendarEndDate = $this->currentDate->copy()->endOfDay();
                break;
        }

        $this->loadAssessments();
    }

    public function loadAssessments(): void
    {
        $query = Assessment::where('student_id', Auth::user()->student->id)
            ->whereBetween('created_at', [$this->calendarStartDate, $this->calendarEndDate]);

        if ($this->selectedStatus) {
            $query->where('status', $this->selectedStatus);
        }

        $assessments = $query->with(['subject', 'topic', 'book', 'responses'])
            ->get()
            ->map(fn($a) => $this->formatAssessment($a));

        $this->assessments = $assessments->toArray();
    }

    /**
     * @throws \JsonException
     */
    private function formatAssessment($assessment): array
    {
        $questions = [];

        if ($assessment->responses->isNotEmpty()) {
            $responseData = $assessment->responses->first()?->data ?? [];
            $responseData = json_decode($responseData, false, 512, JSON_THROW_ON_ERROR);

            foreach ($responseData->questions as $q) {
                if(isset($q->question))
                    $questions[] = [
                        'question' => $q->question,
                        'studentAnswer' => $q->user_answer ?? null,
                        'correctAnswer' => $q->correct_answer ?? null,
                        'isCorrect' => $q->is_correct ?? false
                    ];
            }
        }
        return [
            'id' => "assessment_{$assessment->id}",
            'title' => "Assessment: {$assessment->title}",
            'start' => $assessment->created_at->toIso8601String(),
            'end' => $assessment->updated_at?->toIso8601String(),
            'type' => 'assessment',
            'status' => $assessment->status,
            'subject' => optional($assessment->subject)->name,
            'book' => optional($assessment->book)->title,
            'score' => $assessment->score,
            'max_score' => $assessment->max_score,
            'percentage' => $assessment->percentage_score ?? 0,
            'className' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            'is_assessment' => true,
            'questions' => $questions,
            // Add formatted date strings
            'formatted_date' => $assessment->created_at->isoFormat('Do MMMM, YYYY'),
            'formatted_time' => $assessment->created_at->format('h:i A'),
            'relative_date' => $assessment->created_at->diffForHumans()
        ];
    }

    public function openEventDetails($event): void
    {
        $this->selectedEvent = $event;
        $this->selectedEventQuestions = $event['questions'] ?? [];

        // Set hasQuestions based on whether questions exist
        $this->hasQuestions = !empty($event['questions']) && count($event['questions']) > 0;

        // Log event details view
        activity()->performedOn(auth()->user()->student)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'viewed_assessment_details',
                'assessment_id' => str_replace('assessment_', '', $event['id']),
                'assessment_title' => $event['title'],
                'has_questions' => $this->hasQuestions
            ])
            ->log('Student viewed assessment details');
    }

    public function resetSelectedEvent(): void
    {
        $this->selectedEvent = null;
    }

    public function changeView($newView): void
    {
        $this->viewType = $newView;
        $this->updateCalendarDates();

        // Log view change
        activity()->performedOn(auth()->user()->student)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'changed_schedule_view',
                'new_view' => $newView,
                'previous_view' => $this->viewType
            ])
            ->log('Student changed schedule view');
    }

    public function nextPeriod(): void
    {
        switch ($this->viewType) {
            case 'month':
                $this->currentDate = $this->currentDate->addMonth();
                break;
            case 'week':
                $this->currentDate = $this->currentDate->addWeek();
                break;
            case 'day':
                $this->currentDate = $this->currentDate->addDay();
                break;
        }

        $this->updateCalendarDates();

        // Log navigation
        activity()->performedOn(auth()->user()->student)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'navigated_schedule_forward',
                'view_type' => $this->viewType,
                'new_date' => $this->currentDate->toDateString()
            ])
            ->log('Student navigated schedule forward');
    }

    public function previousPeriod(): void
    {
        switch ($this->viewType) {
            case 'month':
                $this->currentDate = $this->currentDate->subMonth();
                break;
            case 'week':
                $this->currentDate = $this->currentDate->subWeek();
                break;
            case 'day':
                $this->currentDate = $this->currentDate->subDay();
                break;
        }

        $this->updateCalendarDates();

        // Log navigation
        activity()->performedOn(auth()->user()->student)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'navigated_schedule_backward',
                'view_type' => $this->viewType,
                'new_date' => $this->currentDate->toDateString()
            ])
            ->log('Student navigated schedule backward');
    }

    public function render(): \Illuminate\Contracts\View\View|Application|Factory|View
    {
        return view('livewire.students.schedule');
    }
}
