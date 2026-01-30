<?php

namespace App\Livewire\Students;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
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

    public $sortBy = 'created_at';

    public $sortDirection = 'desc';

    public $perPage = 15;

    public $showFilters = true;

    public $viewMode = 'timeline'; // timeline or list

    protected $queryString = [
        'search' => ['except' => ''],
        'activityType' => ['except' => 'all'],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'viewMode' => ['except' => 'timeline'],
        'perPage' => ['except' => 15],
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

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function toggleFilters()
    {
        $this->showFilters = ! $this->showFilters;
    }

    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'timeline' ? 'list' : 'timeline';
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
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

    public function clearFilters()
    {
        $this->search = '';
        $this->activityType = 'all';
        $this->dateFrom = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
        $this->resetPage();
    }

    public function setDateRange($range)
    {
        $now = Carbon::now();

        switch ($range) {
            case 'today':
                $this->dateFrom = $now->format('Y-m-d');
                $this->dateTo = $now->format('Y-m-d');
                break;
            case 'week':
                $this->dateFrom = $now->subWeek()->format('Y-m-d');
                $this->dateTo = Carbon::now()->format('Y-m-d');
                break;
            case 'month':
                $this->dateFrom = $now->subMonth()->format('Y-m-d');
                $this->dateTo = Carbon::now()->format('Y-m-d');
                break;
            case 'quarter':
                $this->dateFrom = $now->subQuarter()->format('Y-m-d');
                $this->dateTo = Carbon::now()->format('Y-m-d');
                break;
            case 'year':
                $this->dateFrom = $now->subYear()->format('Y-m-d');
                $this->dateTo = Carbon::now()->format('Y-m-d');
                break;
        }
        $this->resetPage();
    }

    public function getActivityLogsProperty()
    {
        $student = getStudent(auth()->id(), withoutScopes: true);
        if (! $student) {
            return Activity::query()->where('id', 0)->paginate($this->perPage);
        }
        $query = Activity::query()
            ->where(function ($q) use ($student) {
                $q->where('subject_type', get_class($student))
                    ->where('subject_id', $student->id)
                    ->orWhere('causer_id', Auth::id());
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%'.$this->search.'%')
                        ->orWhere('log_name', 'like', '%'.$this->search.'%')
                        ->orWhereRaw("JSON_EXTRACT(properties, '$.book_title') LIKE ?", ['%'.$this->search.'%'])
                        ->orWhereRaw("JSON_EXTRACT(properties, '$.subject_name') LIKE ?", ['%'.$this->search.'%'])
                        ->orWhereRaw("JSON_EXTRACT(properties, '$.assessment_title') LIKE ?", ['%'.$this->search.'%'])
                        ->orWhereRaw("JSON_EXTRACT(properties, '$.course_name') LIKE ?", ['%'.$this->search.'%']);
                });
            })
            ->when($this->activityType !== 'all', function ($query) {
                if ($this->activityType === 'assessment') {
                    $query->where(function ($q) {
                        $q->where('description', 'like', '%assessment%')
                            ->orWhereRaw("JSON_EXTRACT(properties, '$.action') LIKE ?", ['%assessment%']);
                    });
                } else {
                    $query->where(function ($q) {
                        $q->where('description', 'like', '%'.$this->activityType.'%')
                            ->orWhereRaw("JSON_EXTRACT(properties, '$.action') LIKE ?", ['%'.$this->activityType.'%']);
                    });
                }
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return $query;
    }

    public function getActivityStatsProperty()
    {
        $student = Auth::user()->student;
        if (! $student) {
            return [];
        }

        $baseQuery = Activity::query()
            ->where(function ($q) use ($student) {
                $q->where('subject_type', get_class($student))
                    ->where('subject_id', $student->id)
                    ->orWhere('causer_id', Auth::id());
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            });

        return [
            'total' => $baseQuery->count(),
            'assessments' => (clone $baseQuery)->where('description', 'like', '%assessment%')->count(),
            'books' => (clone $baseQuery)->where('description', 'like', '%book%')->count(),
            'schedule' => (clone $baseQuery)->where('description', 'like', '%schedule%')->count(),
            'reading' => (clone $baseQuery)->where('description', 'like', '%reading%')->count(),
            'today' => (clone $baseQuery)->whereDate('created_at', today())->count(),
            'this_week' => (clone $baseQuery)->whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ])->count(),
        ];
    }

    public function getActivityStreakProperty()
    {
        $student = Auth::user()->student;
        if (! $student) {
            return 0;
        }

        $streak = 0;
        $currentDate = Carbon::now();

        while (true) {
            $hasActivity = Activity::query()
                ->where(function ($q) use ($student) {
                    $q->where('subject_type', get_class($student))
                        ->where('subject_id', $student->id)
                        ->orWhere('causer_id', Auth::id());
                })
                ->whereDate('created_at', $currentDate->format('Y-m-d'))
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

    public function getActivityTypeOptions()
    {
        return [
            'all' => 'All Activities',
            'assessment' => 'Assessment Activities',
            'book' => 'Book Activities',
            'schedule' => 'Schedule Activities',
            'reading' => 'Reading Activities',
            'subscription' => 'Subscription Activities',
            'course' => 'Course Activities',
            'lesson' => 'Lesson Activities',
        ];
    }

    public function getActivityTypeColor($description)
    {
        $description = strtolower($description);

        return match (true) {
            str_contains($description, 'assessment') => 'green',
            str_contains($description, 'book') => 'purple',
            str_contains($description, 'schedule') => 'orange',
            str_contains($description, 'reading') => 'blue',
            str_contains($description, 'subscription') => 'indigo',
            str_contains($description, 'course') => 'pink',
            str_contains($description, 'lesson') => 'yellow',
            default => 'gray'
        };
    }

    public function exportActivities()
    {
        // Implementation for exporting activities to CSV/PDF
        $this->dispatch('notify', [
            'type' => 'info',
            'message' => 'Export feature coming soon!',
        ]);
    }

    public function render()
    {
        return view('livewire.students.activity-logs', [
            'activityLogs' => $this->activityLogs,
            'activityStats' => $this->activityStats,
            'activityStreak' => $this->activityStreak,
            'activityTypeOptions' => $this->getActivityTypeOptions(),
        ]);
    }
}
