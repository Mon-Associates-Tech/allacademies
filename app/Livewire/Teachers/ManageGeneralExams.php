<?php

namespace App\Livewire\Teachers;

use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\Services\GeneralExam\GeneralExamService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ManageGeneralExams extends Component
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

    protected GeneralExamService $assignmentService;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'typeFilter' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function boot(GeneralExamService $assignmentService): void
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
            $url = route('general-exams.join.code', $assignment->access_code);
            $this->dispatch('copy-to-clipboard', text: $url);
        }
    }

    protected function getAssignment(int $id): ?GeneralExam
    {
        return $this->baseAssignmentQuery()->where('id', $id)->first();
    }

    protected function baseAssignmentQuery()
    {
        return GeneralExam::where(function ($q) {
            $q->where('user_id', $this->userId);

            if (Auth::user()?->teacher) {
                $q->orWhere('teacher_id', Auth::user()->teacher->id);
            }
        });
    }

    public function getAssignmentsProperty()
    {
        if (! $this->userId) {
            return collect();
        }

        $query = $this->baseAssignmentQuery()->withCount(['submissions', 'questions', 'sections']);

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
            return $this->baseAssignmentQuery()->where('status', $status)->count();
        };

        return [
            'draft' => $baseFilter('draft'),
            'published' => $baseFilter('published'),
            'closed' => $baseFilter('closed'),
            'archived' => $baseFilter('archived'),
        ];
    }

    public function getDashboardStatsProperty(): array
    {
        if (! $this->userId) {
            return [
                'total_exams' => 0,
                'total_submissions' => 0,
                'submitted_count' => 0,
                'completion_rate' => 0.0,
                'avg_score' => 0.0,
                'guest_participants' => 0,
                'configured_participants' => 0,
                'auto_gradable_questions' => 0,
                'manual_review_questions' => 0,
            ];
        }

        $assignmentIds = $this->baseAssignmentQuery()->pluck('id');
        if ($assignmentIds->isEmpty()) {
            return [
                'total_exams' => 0,
                'total_submissions' => 0,
                'submitted_count' => 0,
                'completion_rate' => 0.0,
                'avg_score' => 0.0,
                'guest_participants' => 0,
                'configured_participants' => 0,
                'auto_gradable_questions' => 0,
                'manual_review_questions' => 0,
            ];
        }

        $submissions = GeneralExamSubmission::whereIn('general_exam_id', $assignmentIds);

        $totalSubmissions = (clone $submissions)->count();
        $submittedCount = (clone $submissions)->whereNotNull('submitted_at')->count();
        $avgScore = (float) ((clone $submissions)->whereNotNull('percentage')->avg('percentage') ?? 0);

        $guestParticipants = (clone $submissions)
            ->whereIn('participant_type', ['participant', 'App\\Models\\GeneralExamParticipant'])
            ->distinct('participant_id')
            ->count('participant_id');

        $configuredParticipants = (clone $submissions)
            ->where('participant_type', 'App\\Models\\Student')
            ->distinct('participant_id')
            ->count('participant_id');

        $questionTypeCounts = \App\ExaminationHub\Models\GeneralExamQuestion::whereIn('general_exam_id', $assignmentIds)
            ->selectRaw("SUM(CASE WHEN type IN ('multiple_choice','true_false') THEN 1 ELSE 0 END) as auto_gradable")
            ->selectRaw("SUM(CASE WHEN type IN ('short_answer','essay') THEN 1 ELSE 0 END) as manual_review")
            ->first();

        return [
            'total_exams' => $assignmentIds->count(),
            'total_submissions' => $totalSubmissions,
            'submitted_count' => $submittedCount,
            'completion_rate' => $totalSubmissions > 0 ? round(($submittedCount / $totalSubmissions) * 100, 1) : 0.0,
            'avg_score' => round($avgScore, 1),
            'guest_participants' => $guestParticipants,
            'configured_participants' => $configuredParticipants,
            'auto_gradable_questions' => (int) ($questionTypeCounts->auto_gradable ?? 0),
            'manual_review_questions' => (int) ($questionTypeCounts->manual_review ?? 0),
        ];
    }

    public function render()
    {
        return view('livewire.teachers.manage-general-exams', [
            'assignments' => $this->assignments,
            'statusCounts' => $this->statusCounts,
            'dashboardStats' => $this->dashboardStats,
        ]);
    }
}
