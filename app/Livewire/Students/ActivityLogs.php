<?php

namespace App\Livewire\Students;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;

class ActivityLogs extends Component
{
    use WithPagination;

    public $dateFrom;
    public $dateTo;
    public $activityType = 'all';
    public $search = '';
    public $selectedActivity = null;
    public $showModal = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'activityType' => ['except' => 'all'],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => '']
    ];

    public function mount()
    {
        $this->dateFrom = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingActivityType()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function viewActivityDetails($activityId)
    {
        $this->selectedActivity = Activity::find($activityId);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedActivity = null;
    }

    public function getActivityLogsProperty()
    {
        $student = Auth::user()->student;
        if (!$student) return Activity::query()->where('id', 0)->paginate(15);

        $query = Activity::query()
            ->where(function($q) use ($student) {
                $q->where('subject_type', get_class($student))
                  ->where('subject_id', $student->id)
                  ->orWhere('causer_id', Auth::id());
            })
            ->when($this->dateFrom, function($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                      ->orWhere('log_name', 'like', '%' . $this->search . '%')
                      ->orWhereRaw("JSON_EXTRACT(properties, '$.book_title') LIKE ?", ['%' . $this->search . '%'])
                      ->orWhereRaw("JSON_EXTRACT(properties, '$.subject_name') LIKE ?", ['%' . $this->search . '%'])
                      ->orWhereRaw("JSON_EXTRACT(properties, '$.assessment_title') LIKE ?", ['%' . $this->search . '%']);
                });
            })
            ->when($this->activityType !== 'all', function($query) {
                $query->whereRaw("JSON_EXTRACT(properties, '$.action') LIKE ?", ['%' . $this->activityType . '%']);
            })
            ->latest()
            ->paginate(15);

        return $query;
    }

    public function getActivityStatsProperty()
    {
        $student = Auth::user()->student;
        if (!$student) return [];

        $baseQuery = Activity::query()
            ->where(function($q) use ($student) {
                $q->where('subject_type', get_class($student))
                  ->where('subject_id', $student->id)
                  ->orWhere('causer_id', Auth::id());
            })
            ->when($this->dateFrom, function($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            });

        return [
            'total' => $baseQuery->count(),
            'assessments' => (clone $baseQuery)->where('description', 'like', '%assessment%')->count(),
            'books' => (clone $baseQuery)->where('description', 'like', '%book%')->count(),
            'schedule' => (clone $baseQuery)->where('description', 'like', '%schedule%')->count(),
            'today' => (clone $baseQuery)->whereDate('created_at', today())->count(),
        ];
    }

    public function getActivityTypeOptions()
    {
        return [
            'all' => 'All Activities',
            'assessment' => 'Assessment Activities',
            'book' => 'Book Activities',
            'schedule' => 'Schedule Activities',
            'reading' => 'Reading Activities',
            'subscription' => 'Subscription Activities'
        ];
    }

    public function render()
    {
        return view('livewire.students.activity-logs', [
            'activityLogs' => $this->activityLogs,
            'activityStats' => $this->activityStats,
            'activityTypeOptions' => $this->getActivityTypeOptions()
        ]);
    }
}
