<?php

namespace App\Livewire\Common;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Models\User;
use App\Services\ActivityLogHelper;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityLogManager extends Component
{
    use WithPagination;

    // Filter properties
    public $selectedModelType = '';

    public $selectedUserId = '';

    public $selectedModelInstance = '';

    public $selectedModelId = '';

    public $startDate = '';

    public $endDate = '';

    public $searchKeyword = '';

    public $limitPerPage = 25;

    // Display properties
    public $activeTab = 'all'; // all, model, user, date, search, statistics

    public $showFilters = true;

    public $activities = [];

    public $statistics = [];

    public $users = [];

    public $modelInstances = [];

    // Loading states
    public $loading = false;

    public $loadingStatistics = false;

    // Model options
    public $modelTypes = [
        'academicgroup' => 'Academic Groups',
        'academiclevel' => 'Academic Levels',
        'academicsubject' => 'Academic Subjects',
        'academictopic' => 'Academic Topics',
        'academicsubtopic' => 'Academic Subtopics',
    ];

    protected $queryString = [
        'activeTab' => ['except' => 'all'],
        'selectedModelType' => ['except' => ''],
        'selectedUserId' => ['except' => ''],
        'searchKeyword' => ['except' => ''],
    ];

    protected $rules = [
        'startDate' => 'nullable|date',
        'endDate' => 'nullable|date|after_or_equal:startDate',
        'limitPerPage' => 'integer|min:10|max:100',
        'searchKeyword' => 'nullable|string|min:2|max:255',
    ];

    public function mount()
    {
        $this->loadUsers();
        $this->loadActivities();

        // Set default date range (last 30 days)
        $this->startDate = now()->subDays(30)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function updatedActiveTab()
    {
        $this->resetPage();
        $this->resetFilters();
        $this->loadActivities();
    }

    public function updatedSelectedModelType()
    {
        $this->selectedModelInstance = '';
        $this->selectedModelId = '';
        $this->loadModelInstances();
        $this->loadActivities();
    }

    public function updatedSelectedUserId()
    {
        $this->loadActivities();
    }

    public function updatedSelectedModelInstance()
    {
        $this->loadActivities();
    }

    public function updatedLimitPerPage()
    {
        $this->resetPage();
        $this->loadActivities();
    }

    public function loadUsers()
    {
        $this->users = User::select('id', 'name', 'email')
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function loadModelInstances()
    {
        if (! $this->selectedModelType) {
            $this->modelInstances = [];

            return;
        }

        $modelClasses = [
            'academicgroup' => AcademicGroup::class,
            'academiclevel' => AcademicLevel::class,
            'academicsubject' => AcademicSubject::class,
            'academictopic' => AcademicTopic::class,
            'academicsubtopic' => AcademicSubtopic::class,
        ];

        if (isset($modelClasses[$this->selectedModelType])) {
            $modelClass = $modelClasses[$this->selectedModelType];
            $this->modelInstances = $modelClass::select('id', 'name')
                ->orderBy('name')
                ->limit(100)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name ?? "ID: {$item->id}",
                    ];
                })
                ->toArray();
        }
    }

    public function loadActivities()
    {
        $this->loading = true;

        try {
            switch ($this->activeTab) {
                case 'model':
                    $this->loadModelActivities();
                    break;
                case 'user':
                    $this->loadUserActivities();
                    break;
                case 'date':
                    $this->loadDateRangeActivities();
                    break;
                case 'search':
                    $this->loadSearchActivities();
                    break;
                case 'instance':
                    $this->loadInstanceActivities();
                    break;
                case 'statistics':
                    $this->loadStatistics();
                    break;
                default:
                    $this->loadAllActivities();
                    break;
            }
        } catch (\Exception $e) {
            $this->activities = [];
            session()->flash('error', 'Error loading activities: '.$e->getMessage());
        }

        $this->loading = false;
    }

    private function loadAllActivities()
    {
        $activities = ActivityLogHelper::getAllAcademicActivities($this->limitPerPage);
        $this->activities = ActivityLogHelper::formatActivities($activities);
    }

    private function loadModelActivities()
    {
        if (! $this->selectedModelType) {
            $this->activities = [];

            return;
        }

        $activities = ActivityLogHelper::getActivitiesForModel($this->selectedModelType, $this->limitPerPage);
        $this->activities = ActivityLogHelper::formatActivities($activities);
    }

    private function loadUserActivities()
    {
        if (! $this->selectedUserId) {
            $this->activities = [];

            return;
        }

        $activities = ActivityLogHelper::getActivitiesByUser((int) $this->selectedUserId, $this->limitPerPage);
        $this->activities = ActivityLogHelper::formatActivities($activities);
    }

    private function loadDateRangeActivities()
    {
        if (! $this->startDate || ! $this->endDate) {
            $this->activities = [];

            return;
        }

        $startDate = Carbon::parse($this->startDate)->startOfDay();
        $endDate = Carbon::parse($this->endDate)->endOfDay();

        $activities = ActivityLogHelper::getActivitiesByDateRange($startDate, $endDate, $this->limitPerPage);
        $this->activities = ActivityLogHelper::formatActivities($activities);
    }

    private function loadSearchActivities()
    {
        if (! $this->searchKeyword || strlen($this->searchKeyword) < 2) {
            $this->activities = [];

            return;
        }

        $activities = ActivityLogHelper::searchActivities($this->searchKeyword, $this->limitPerPage);
        $this->activities = ActivityLogHelper::formatActivities($activities);
    }

    private function loadInstanceActivities()
    {
        if (! $this->selectedModelType || ! $this->selectedModelId) {
            $this->activities = [];

            return;
        }

        $modelClasses = [
            'academicgroup' => AcademicGroup::class,
            'academiclevel' => AcademicLevel::class,
            'academicsubject' => AcademicSubject::class,
            'academictopic' => AcademicTopic::class,
            'academicsubtopic' => AcademicSubtopic::class,
        ];

        if (isset($modelClasses[$this->selectedModelType])) {
            $modelClass = $modelClasses[$this->selectedModelType];
            $model = $modelClass::find($this->selectedModelId);

            if ($model) {
                $this->activities = ActivityLogHelper::getModelActivity($model, $this->limitPerPage);
            } else {
                $this->activities = [];
            }
        }
    }

    private function loadStatistics()
    {
        $this->loadingStatistics = true;

        try {
            $startDate = $this->startDate ? Carbon::parse($this->startDate) : null;
            $endDate = $this->endDate ? Carbon::parse($this->endDate) : null;

            $this->statistics = ActivityLogHelper::getActivityStatistics($startDate, $endDate);
        } catch (\Exception $e) {
            $this->statistics = [];
            session()->flash('error', 'Error loading statistics: '.$e->getMessage());
        }

        $this->loadingStatistics = false;
    }

    public function search()
    {
        $this->validate(['searchKeyword' => 'required|string|min:2|max:255']);
        $this->activeTab = 'search';
        $this->resetPage();
        $this->loadActivities();
    }

    public function filterByDateRange()
    {
        $this->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
        ]);

        $this->activeTab = 'date';
        $this->resetPage();
        $this->loadActivities();
    }

    public function filterByModel()
    {
        if (! $this->selectedModelType) {
            session()->flash('error', 'Please select a model type.');

            return;
        }

        $this->activeTab = 'model';
        $this->resetPage();
        $this->loadActivities();
    }

    public function filterByUser()
    {
        if (! $this->selectedUserId) {
            session()->flash('error', 'Please select a user.');

            return;
        }

        $this->activeTab = 'user';
        $this->resetPage();
        $this->loadActivities();
    }

    public function filterByInstance()
    {
        if (! $this->selectedModelType || ! $this->selectedModelId) {
            session()->flash('error', 'Please select both model type and instance.');

            return;
        }

        $this->activeTab = 'instance';
        $this->resetPage();
        $this->loadActivities();
    }

    public function resetFilters()
    {
        $this->selectedModelType = '';
        $this->selectedUserId = '';
        $this->selectedModelId = '';
        $this->searchKeyword = '';
        $this->modelInstances = [];
        $this->resetPage();
    }

    public function exportActivities()
    {
        // This would typically export to CSV or Excel
        // For now, we'll just show a success message
        session()->flash('success', 'Export functionality would be implemented here.');
    }

    public function refreshData()
    {
        $this->loadActivities();
        session()->flash('success', 'Activity data refreshed successfully.');
    }

    public function getActionTypeColorProperty()
    {
        return [
            'created' => 'bg-green-100 text-green-800',
            'updated' => 'bg-blue-100 text-blue-800',
            'deleted' => 'bg-red-100 text-red-800',
            'restored' => 'bg-yellow-100 text-yellow-800',
        ];
    }

    public function render()
    {
        return view('livewire.common.activity-log-manager');
    }
}
