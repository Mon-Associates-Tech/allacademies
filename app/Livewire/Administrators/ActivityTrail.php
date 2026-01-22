<?php

namespace App\Livewire\Administrators;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class ActivityTrail extends Component
{
    use WithPagination;

    public $selectedUser = '';

    public $selectedQuestionType = '';

    public $selectedAction = '';

    public $selectedModule = '';

    public $dateFrom = '';

    public $dateTo = '';

    public $search = '';

    public $perPage = 25;

    public $sortBy = 'created_at';

    public $sortDirection = 'desc';

    public $showOnlyQuestions = true; // Default to question activities only

    public $selectedActivityId = null;

    public $showActivityModal = false;

    public $questionTypes = [
        'essay' => 'Essay Questions',
        'multiple_choice' => 'Multiple Choice Questions',
        'true_or_false' => 'True or False Questions',
    ];

    public $actionTypes = [
        'created' => 'Created',
        'updated' => 'Updated',
        'deleted' => 'Deleted',
    ];

    public $modules = [
        'questions' => 'Question Management',
        // Add other modules as needed
        'users' => 'User Management',
        'system' => 'System',
        'authentication' => 'Authentication',
    ];

    protected $queryString = [
        'selectedUser' => ['except' => ''],
        'selectedQuestionType' => ['except' => ''],
        'selectedAction' => ['except' => ''],
        'selectedModule' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'search' => ['except' => ''],
        'showOnlyQuestions' => ['except' => true],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'page' => ['except' => 1],
    ];

    public function mount()
    {
        $this->dateFrom = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
        $this->selectedModule = $this->showOnlyQuestions ? 'questions' : '';
    }

    public function updatingSelectedUser()
    {
        $this->resetPage();
    }

    public function updatingSelectedQuestionType()
    {
        $this->resetPage();
    }

    public function updatingSelectedAction()
    {
        $this->resetPage();
    }

    public function updatingSelectedModule()
    {
        $this->resetPage();
    }

    public function updatingShowOnlyQuestions()
    {
        $this->resetPage();
        if ($this->showOnlyQuestions) {
            $this->selectedModule = 'questions';
            $this->selectedQuestionType = '';
        } else {
            $this->selectedModule = '';
            $this->selectedQuestionType = '';
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
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

    public function resetFilters()
    {
        $this->selectedUser = '';
        $this->selectedQuestionType = '';
        $this->selectedAction = '';
        $this->selectedModule = $this->showOnlyQuestions ? 'questions' : '';
        $this->search = '';
        $this->dateFrom = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
        $this->resetPage();
    }

    public function toggleQuestionFocus()
    {
        $this->showOnlyQuestions = ! $this->showOnlyQuestions;
        $this->resetFilters();
    }

    public function getActivitiesProperty()
    {
        return Activity::query()
            ->with(['causer', 'subject'])
            ->when($this->selectedUser, function (Builder $query) {
                $query->where('causer_id', $this->selectedUser);
            })
            ->when($this->showOnlyQuestions, function (Builder $query) {
                // Focus on question management activities
                $query->where(function (Builder $subQuery) {
                    $subQuery->where('log_name', 'question_management')
                        ->orWhere(function (Builder $questionQuery) {
                            $questionQuery->whereJsonContains('properties->module', 'questions')
                                ->orWhereIn('subject_type', [
                                    'App\Models\EssayQuestion',
                                    'App\Models\MultipleChoiceQuestion',
                                    'App\Models\TrueOrFalseQuestion',
                                ]);
                        });
                });
            })
            ->when($this->selectedQuestionType && $this->showOnlyQuestions, function (Builder $query) {
                $query->whereJsonContains('properties->question_type', $this->selectedQuestionType);
            })
            ->when($this->selectedModule && ! $this->showOnlyQuestions, function (Builder $query) {
                $query->where(function (Builder $subQuery) {
                    $subQuery->where('log_name', $this->selectedModule)
                        ->orWhereJsonContains('properties->module', $this->selectedModule);
                });
            })
            ->when($this->selectedAction, function (Builder $query) {
                $query->where('description', $this->selectedAction);
            })
            ->when($this->dateFrom, function (Builder $query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function (Builder $query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $subQuery) {
                    $subQuery->where('description', 'like', '%'.$this->search.'%')
                        ->orWhere('log_name', 'like', '%'.$this->search.'%')
                        ->orWhereHas('causer', function (Builder $userQuery) {
                            $userQuery->where('name', 'like', '%'.$this->search.'%')
                                ->orWhere('email', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function getUsersProperty()
    {
        return User::select('id', 'name', 'email')
            ->orderBy('name')
            ->get();
    }

    public function getActivityStatsProperty()
    {
        $baseQuery = Activity::query()
            ->when($this->dateFrom, function (Builder $query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function (Builder $query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            });

        // Apply question filter if enabled
        if ($this->showOnlyQuestions) {
            $baseQuery->where(function (Builder $query) {
                $query->where('log_name', 'question_management')
                    ->orWhere(function (Builder $subQuery) {
                        $subQuery->whereJsonContains('properties->module', 'questions')
                            ->orWhereIn('subject_type', [
                                'App\Models\EssayQuestion',
                                'App\Models\MultipleChoiceQuestion',
                                'App\Models\TrueOrFalseQuestion',
                            ]);
                    });
            });
        }

        return [
            'total' => (clone $baseQuery)->count(),
            'created' => (clone $baseQuery)->where('description', 'created')->count(),
            'updated' => (clone $baseQuery)->where('description', 'updated')->count(),
            'deleted' => (clone $baseQuery)->where('description', 'deleted')->count(),
            'unique_users' => (clone $baseQuery)->distinct('causer_id')->count('causer_id'),
        ];
    }

    public function getModuleStatsProperty()
    {
        if ($this->showOnlyQuestions) {
            return [];
        }

        $baseQuery = Activity::query()
            ->when($this->dateFrom, function (Builder $query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function (Builder $query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            });

        // Get activities by log_name (primary way modules are organized)
        $logNameStats = (clone $baseQuery)
            ->selectRaw('log_name, COUNT(*) as count')
            ->groupBy('log_name')
            ->get()
            ->pluck('count', 'log_name')
            ->toArray();

        // Get activities by module property (fallback)
        $modulePropertyStats = (clone $baseQuery)
            ->selectRaw('JSON_EXTRACT(properties, "$.module") as module, COUNT(*) as count')
            ->whereNotNull('properties')
            ->groupBy('module')
            ->get()
            ->pluck('count', 'module')
            ->toArray();

        // Merge and clean up the stats
        $combined = array_merge($logNameStats, $modulePropertyStats);

        // Remove null keys and format
        unset($combined[null], $combined['']);

        return $combined;
    }

    /**
     * Show activity details modal
     */
    public function showActivityDetails($activityId)
    {
        $this->selectedActivityId = $activityId;
        $this->showActivityModal = true;
        $this->js('window.Modal.open("activity-trail-details-modal")');
    }

    /**
     * Close activity details modal
     */
    public function closeActivityModal()
    {
        $this->selectedActivityId = null;
        $this->showActivityModal = false;
    }

    /**
     * Get selected activity details
     */
    public function getSelectedActivityProperty()
    {
        if (! $this->selectedActivityId) {
            return null;
        }

        return Activity::with(['causer', 'subject'])
            ->find($this->selectedActivityId);
    }

    /**
     * Format activity properties for display
     */
    public function getFormattedPropertiesProperty(): array
    {
        $activity = $this->selectedActivity;
        if (! $activity) {
            return [];
        }

        $formatted = [];
        $properties = $activity->properties ?? [];

        if (isset($properties['question_type'])) {
            $formatted['Question Type'] = match ($properties['question_type']) {
                'essay' => 'Essay Question',
                'multiple_choice' => 'Multiple Choice Question',
                'true_or_false' => 'True or False Question',
                default => ucwords(str_replace('_', ' ', $properties['question_type']))
            };
        }

        if (isset($properties['difficulty_level'])) {
            $formatted['Difficulty Level'] = $properties['difficulty_level'];
        }

        if (isset($properties['score'])) {
            $formatted['Score'] = $properties['score'];
        }

        if (isset($properties['academic_topic_id'])) {
            $formatted['Academic Topic ID'] = $properties['academic_topic_id'];
        }

        if (isset($properties['academic_subtopic_id'])) {
            $formatted['Academic Subtopic ID'] = $properties['academic_subtopic_id'];
        }

        if (isset($properties['metadata'])) {
            $metadata = $properties['metadata'];
            if (isset($metadata['subtopic_name'])) {
                $formatted['Subtopic'] = $metadata['subtopic_name'];
            }
            if (isset($metadata['topic_name'])) {
                $formatted['Topic'] = $metadata['topic_name'];
            }
        }

        if (isset($properties['changes']) && is_array($properties['changes'])) {
            $formatted['Changes'] = $properties['changes'];
        }

        if (isset($properties['question_data'])) {
            $formatted['Question Data'] = $properties['question_data'];
        }

        return $formatted;
    }

    public function render()
    {
        return view('livewire.administrators.activity-trail', [
            'activities' => $this->activities,
            'users' => $this->users,
            'stats' => $this->activityStats,
            'moduleStats' => $this->moduleStats,
        ]);
    }
}
