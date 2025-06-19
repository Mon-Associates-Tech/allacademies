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
    try {
        $query = Assessment::where('student_id', Auth::user()->student->id)
            ->whereBetween('created_at', [$this->calendarStartDate, $this->calendarEndDate]);

        if ($this->selectedStatus) {
            $query->where('status', $this->selectedStatus);
        }

        $assessments = $query->with(['subject', 'topic', 'book', 'responses', 'questions.questionable'])
            ->get()
            ->map(function($assessment) {
                try {
                    $formatted = $this->formatAssessment($assessment);

                    // Ensure proper date formatting for FullCalendar
                    $formatted['start'] = $assessment->created_at->format('Y-m-d\TH:i:s');
                    $formatted['end'] = $assessment->updated_at
                        ? $assessment->updated_at->format('Y-m-d\TH:i:s')
                        : $assessment->created_at->addHour()->format('Y-m-d\TH:i:s');

                    return $formatted;
                } catch (\Exception $e) {
                    \Log::warning('Error formatting individual assessment', [
                        'assessment_id' => $assessment->id,
                        'error' => $e->getMessage()
                    ]);

                    // Return minimal assessment data on error
                    return [
                        'id' => "assessment_{$assessment->id}",
                        'title' => "Assessment #{$assessment->id}",
                        'start' => $assessment->created_at->format('Y-m-d\TH:i:s'),
                        'end' => $assessment->created_at->addHour()->format('Y-m-d\TH:i:s'),
                        'type' => 'assessment',
                        'status' => $assessment->status ?? 'unknown',
                        'className' => 'bg-gray-100 text-gray-800',
                        'is_assessment' => true,
                        'questions' => [],
                        'error' => true
                    ];
                }
            });

        $this->assessments = $assessments->toArray();

        // Emit event to trigger calendar refresh
        $this->dispatch('assessmentsUpdated');

    } catch (\Exception $e) {
        \Log::error('Error loading assessments', [
            'student_id' => Auth::user()->student->id,
            'error' => $e->getMessage()
        ]);

        $this->assessments = [];
        session()->flash('error', 'Unable to load assessments. Please try again.');
    }
}

    /**
     * @throws \JsonException
     */


private function formatAssessment($assessment): array
{
    $questions = [];

    try {
        // Check if assessment has responses
        if ($assessment->responses && $assessment->responses->isNotEmpty()) {
            foreach ($assessment->responses as $response) {
                // Handle different response data structures
                $responseData = null;

                if (is_string($response->response)) {
                    // Try to decode JSON response
                    $decoded = json_decode($response->response, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $responseData = $decoded;
                    }
                } elseif ($response->data) {
                    // Handle data field if it exists
                    if (is_string($response->data)) {
                        $responseData = json_decode($response->data, true);
                    } else {
                        $responseData = $response->data;
                    }
                }

                // Process questions from response data
                if ($responseData && isset($responseData['questions'])) {
                    foreach ($responseData['questions'] as $q) {
                        if (isset($q['question']) || isset($q->question)) {
                            // Handle both array and object formats
                            $question = is_array($q) ? $q['question'] : $q->question;
                            $userAnswer = is_array($q) ? ($q['user_answer'] ?? null) : ($q->user_answer ?? null);
                            $correctAnswer = is_array($q) ? ($q['correct_answer'] ?? null) : ($q->correct_answer ?? null);
                            $isCorrect = is_array($q) ? ($q['is_correct'] ?? false) : ($q->is_correct ?? false);

                            $questions[] = [
                                'question' => $question,
                                'studentAnswer' => $userAnswer,
                                'correctAnswer' => $correctAnswer,
                                'isCorrect' => $isCorrect
                            ];
                        }
                    }
                }
            }
        }

        // If no questions found in responses, try to get from assessment questions relationship
        if (empty($questions) && $assessment->questions) {
            foreach ($assessment->questions as $question) {
                $questions[] = [
                    'question' => $question->questionable->question ?? 'Question not available',
                    'studentAnswer' => null,
                    'correctAnswer' => $question->questionable->answer ?? null,
                    'isCorrect' => false
                ];
            }
        }

    } catch (\Exception $e) {
        // Log error but don't break the formatting
        \Log::warning('Error formatting assessment questions', [
            'assessment_id' => $assessment->id,
            'error' => $e->getMessage()
        ]);
    }

    return [
        'id' => "assessment_{$assessment->id}",
        'title' => $assessment->title ?? "Assessment #{$assessment->id}",
        'start' => $assessment->created_at->toIso8601String(),
        'end' => $assessment->updated_at?->toIso8601String() ?? $assessment->created_at->addHour()->toIso8601String(),
        'type' => 'assessment',
        'status' => $assessment->status ?? 'unknown',
        'subject' => $assessment->subject?->name ?? 'No Subject',
        'topic' => $assessment->topic?->name ?? null,
        'book' => $assessment->book?->title ?? null,
        'score' => $assessment->total_score ?? $assessment->score ?? 0,
        'max_score' => $assessment->max_score ?? 0,
        'percentage' => $assessment->percentage_score ?? 0,
        'className' => $this->getAssessmentClassName($assessment->status ?? 'unknown'),
        'is_assessment' => true,
        'questions' => $questions,
        'questions_count' => count($questions),
        // Add formatted date strings with null checks
        'formatted_date' => $assessment->created_at->isoFormat('Do MMMM, YYYY'),
        'formatted_time' => $assessment->created_at->format('h:i A'),
        'relative_date' => $assessment->created_at->diffForHumans(),
        // Add duration if available
        'duration' => $assessment->end_time && $assessment->start_time
            ? $assessment->start_time->diffInMinutes($assessment->end_time) . ' minutes'
            : null
    ];
}

private function getAssessmentClassName($status): string
{
    return match($status) {
        'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'in_progress' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        'pending' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        'failed' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
    };
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
