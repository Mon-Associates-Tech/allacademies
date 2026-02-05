<?php

namespace App\Livewire\Activities;

use App\Models\UserActivity;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Activity Tracker')]
class ActivityTracker extends Component
{
    use WithPagination;

    public string $searchTerm = '';
    public string $filterCategory = '';
    public string $filterType = '';
    public string $sortBy = 'recent';
    public int $perPage = 20;

    public function updatedSearchTerm(): void
    {
        $this->resetPage();
    }

    public function updatedFilterCategory(): void
    {
        $this->resetPage();
    }

    public function updatedFilterType(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->searchTerm = '';
        $this->filterCategory = '';
        $this->filterType = '';
        $this->sortBy = 'recent';
        $this->resetPage();
    }

    public function getActivitiesProperty()
    {
        $query = UserActivity::query()
            ->where('user_id', auth()->id());

        // Apply category filter
        if ($this->filterCategory) {
            $query->where('category', $this->filterCategory);
        }

        // Apply activity type filter
        if ($this->filterType) {
            $query->where('activity_type', $this->filterType);
        }

        // Apply search filter
        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('activity_name', 'like', "%{$this->searchTerm}%")
                    ->orWhere('description', 'like', "%{$this->searchTerm}%")
                    ->orWhere('activity_type', 'like', "%{$this->searchTerm}%");
            });
        }

        // Apply sorting
        match ($this->sortBy) {
            'oldest' => $query->oldest('created_at'),
            'alphabetical' => $query->orderBy('activity_name'),
            'category' => $query->orderBy('category')->latest('created_at'),
            default => $query->latest('created_at'),
        };

        return $query->paginate($this->perPage);
    }

    public function getCategoriesProperty()
    {
        return UserActivity::where('user_id', auth()->id())
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values()
            ->all();
    }

    public function getActivityTypesProperty()
    {
        return UserActivity::where('user_id', auth()->id())
            ->distinct()
            ->pluck('activity_type')
            ->sort()
            ->values()
            ->all();
    }

    public function getActivityStatsProperty()
    {
        $activities = UserActivity::where('user_id', auth()->id());

        return [
            'total' => $activities->count(),
            'today' => $activities->whereDate('created_at', today())->count(),
            'this_week' => $activities->where('created_at', '>=', now()->startOfWeek())->count(),
            'this_month' => $activities->where('created_at', '>=', now()->startOfMonth())->count(),
        ];
    }

    public function render()
    {
        return view('livewire.activities.activity-tracker', [
            'activities' => $this->activities,
            'categories' => $this->categories,
            'activityTypes' => $this->activityTypes,
            'stats' => $this->activityStats,
        ]);
    }
}
