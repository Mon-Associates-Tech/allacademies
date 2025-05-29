<?php

namespace App\Livewire\Students;

use Livewire\Component;
use App\Models\Activity;
use Carbon\Carbon;

class Schedule extends Component
{
    public $view = 'month'; // month, week, day
    public $currentDate;
    public $calendarStartDate;
    public $calendarEndDate;
    public $activities = [];
    public $activityTypes = [
        'assessment' => true,
        'book_reading' => true,
        'group_meeting' => true,
        'quiz' => true,
        'exam' => true
    ];
    
    public function mount()
    {
        $this->currentDate = Carbon::now();
        $this->updateCalendarDates();
    }
    
    public function updateCalendarDates()
    {
        switch ($this->view) {
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
        
        $this->loadActivities();
    }
    
    public function loadActivities()
    {
        $types = collect($this->activityTypes)
                ->filter(function ($value) {
                    return $value === true;
                })
                ->keys()
                ->toArray();
                
        $this->activities = Activity::forStudent(auth()->id())
            ->whereIn('activity_type', $types)
            ->where(function($query) {
                $query->whereBetween('start_time', [$this->calendarStartDate, $this->calendarEndDate])
                      ->orWhereBetween('end_time', [$this->calendarStartDate, $this->calendarEndDate]);
            })
            ->with(['subject', 'group'])
            ->get()
            ->map(function($activity) {
                // Format for calendar display
                return [
                    'id' => $activity->id,
                    'title' => $activity->title,
                    'start' => $activity->start_time->format('Y-m-d H:i:s'),
                    'end' => $activity->end_time->format('Y-m-d H:i:s'),
                    'type' => $activity->activity_type,
                    'groupName' => $activity->is_group_activity ? $activity->group->name : null,
                    'subject' => $activity->subject ? $activity->subject->name : null,
                    'location' => $activity->location,
                    'status' => $activity->status,
                    'className' => $this->getEventClassName($activity),
                ];
            });
    }
    
    private function getEventClassName($activity)
    {
        // Define color classes for different activity types
        $colors = [
            'assessment' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
            'book_reading' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'group_meeting' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'quiz' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'exam' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        ];
        
        return $colors[$activity->activity_type] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
    }
    
    public function changeView($view)
    {
        $this->view = $view;
        $this->updateCalendarDates();
    }
    
    public function nextPeriod()
    {
        switch ($this->view) {
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
    }
    
    public function previousPeriod()
    {
        switch ($this->view) {
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
    }
    
    public function createActivity()
    {
        return redirect()->route('student.activities.create');
    }
    
    public function viewActivity($id)
    {
        return redirect()->route('student.activities.show', $id);
    }
    
    public function render()
    {
        return view('livewire.students.schedule');
    }
}