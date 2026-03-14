<?php

namespace App\Livewire\Teachers;

use App\Models\PublicAssignment;
use App\Services\PublicAssignment\PublicAssignmentService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ManagePublicAssignments extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $typeFilter = '';

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    public ?int $confirmingDelete = null;

    public ?int $viewingAssignment = null;

    public ?array $assignmentStats = null;

    public ?int $userId = null;

    protected PublicAssignmentService $assignmentService;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'typeFilter' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function boot(PublicAssignmentService $assignmentService): void
    {
        $this->assignmentService = $assignmentService;
    }

    public function hydrate(): void
    {
        // Ensure userId persists across Livewire requests
        $this->userId = $this->userId ?? Auth::id();
    }

    public function mount(): void
    {
        $this->userId = Auth::id();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function publishAssignment(int $id): void
    {
        $assignment = $this->getAssignment($id);

        if (! $assignment) {
            session()->flash('error', 'Assignment not found.');

            return;
        }

        try {
            $this->assignmentService->publishAssignment($assignment);
            session()->flash('success', 'Assignment published successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to publish assignment: '.$e->getMessage());
        }
    }

    public function closeAssignment(int $id): void
    {
        $assignment = $this->getAssignment($id);

        if (! $assignment) {
            session()->flash('error', 'Assignment not found.');

            return;
        }

        try {
            $this->assignmentService->closeAssignment($assignment);
            session()->flash('success', 'Assignment closed successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to close assignment: '.$e->getMessage());
        }
    }

    public function reopenAssignment(int $id): void
    {
        $assignment = $this->getAssignment($id);

        if (! $assignment) {
            session()->flash('error', 'Assignment not found.');

            return;
        }

        try {
            $assignment->update(['status' => 'published']);
            session()->flash('success', 'Assignment reopened successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to reopen assignment: '.$e->getMessage());
        }
    }

    public function duplicateAssignment(int $id): void
    {
        $assignment = $this->getAssignment($id);

        if (! $assignment) {
            session()->flash('error', 'Assignment not found.');

            return;
        }

        try {
            $newAssignment = $this->assignmentService->duplicateAssignment($assignment);
            session()->flash('success', 'Assignment duplicated successfully! New access code: '.$newAssignment->access_code);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to duplicate assignment: '.$e->getMessage());
        }
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmingDelete = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDelete = null;
    }

    public function deleteAssignment(): void
    {
        if (! $this->confirmingDelete) {
            return;
        }

        $assignment = $this->getAssignment($this->confirmingDelete);

        if (! $assignment) {
            session()->flash('error', 'Assignment not found.');
            $this->confirmingDelete = null;

            return;
        }

        try {
            $assignment->delete();
            session()->flash('success', 'Assignment deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete assignment: '.$e->getMessage());
        }

        $this->confirmingDelete = null;
    }

    public function viewStats(int $id): void
    {
        $assignment = $this->getAssignment($id);

        if (! $assignment) {
            $this->assignmentStats = null;

            return;
        }

        $this->viewingAssignment = $id;
        $this->assignmentStats = $this->assignmentService->getAssignmentStatistics($assignment);
    }

    public function closeStats(): void
    {
        $this->viewingAssignment = null;
        $this->assignmentStats = null;
    }

    public function releaseResults(int $id): void
    {
        $assignment = $this->getAssignment($id);

        if (! $assignment) {
            session()->flash('error', 'Assignment not found.');

            return;
        }

        try {
            $assignment->releaseResults();
            session()->flash('success', 'Results released successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to release results: '.$e->getMessage());
        }
    }

    public function copyAccessCode(int $id): void
    {
        $assignment = $this->getAssignment($id);

        if ($assignment) {
            $this->dispatch('copy-to-clipboard', text: $assignment->access_code);
        }
    }

    public function copyJoinUrl(int $id): void
    {
        $assignment = $this->getAssignment($id);

        if ($assignment) {
            $url = route('public-assignments.join.code', $assignment->access_code);
            $this->dispatch('copy-to-clipboard', text: $url);
        }
    }

    protected function getAssignment(int $id): ?PublicAssignment
    {
        $query = PublicAssignment::where('id', $id);

        $query->where(function ($q) {
            $q->where('user_id', $this->userId);

            // Backward compatibility: include legacy teacher ownership
            if (Auth::user()?->teacher) {
                $q->orWhere('teacher_id', Auth::user()->teacher->id);
            }
        });

        return $query->first();
    }

    public function getAssignmentsProperty()
    {
        if (! $this->userId) {
            return collect();
        }

        $query = PublicAssignment::where(function ($q) {
            $q->where('user_id', $this->userId);

            if (Auth::user()?->teacher) {
                $q->orWhere('teacher_id', Auth::user()->teacher->id);
            }
        })->withCount(['submissions', 'questions']);

        // Search filter
        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('access_code', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            });
        }

        // Status filter
        if (! empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        // Type filter
        if (! empty($this->typeFilter)) {
            $query->where('type', $this->typeFilter);
        }

        // Sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(10);
    }

    public function getStatusCountsProperty(): array
    {
        if (! $this->userId) {
            return ['draft' => 0, 'published' => 0, 'closed' => 0, 'archived' => 0];
        }

        $baseFilter = function ($status) {
            return PublicAssignment::where(function ($q) {
                $q->where('user_id', $this->userId);
                if (Auth::user()?->teacher) {
                    $q->orWhere('teacher_id', Auth::user()->teacher->id);
                }
            })->where('status', $status)->count();
        };

        return [
            'draft' => $baseFilter('draft'),
            'published' => $baseFilter('published'),
            'closed' => $baseFilter('closed'),
            'archived' => $baseFilter('archived'),
        ];
    }

    public function render()
    {
        return view('livewire.teachers.manage-public-assignments', [
            'assignments' => $this->assignments,
            'statusCounts' => $this->statusCounts,
        ]);
    }
}
