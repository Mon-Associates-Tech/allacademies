<?php

namespace App\Livewire\MockExam\Subscriptions;

use App\MockExam\Models\MockExamSubscription;
use App\MockExam\Models\MockExamSubscriptionPlan;
use App\MockExam\Models\MockExamSubscriptionStatus;
use App\MockExam\Models\MockExamUserGroup;
use App\MockExam\Services\MockExamSubscriptionService;
use App\Models\AcademicSubject;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class MockExamSubscriptionManager extends Component
{
    use WithPagination;

    // List filters
    public string $search = '';

    public string $statusFilter = '';

    // Allocation form
    public bool $showAllocationForm = false;

    public string $userSearch = '';

    public ?int $selectedUserId = null;

    public string $selectedUserName = '';

    public int $planId = 0;

    public string $type = 'print';

    public string $userGroup = 'teacher';

    public string $subjectSearch = '';

    public array $selectedSubjectIds = [];

    public int $participantCount = 30;

    public ?int $maxExams = 1;

    public string $allocationMode = 'grant'; // grant or payment

    public float $calculatedPrice = 0;

    public string $activeTab = 'subscriptions';

    public function mount(): void {}

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPlanId(): void
    {
        $this->recalculatePrice();
    }

    public function updatedSelectedSubjectIds(): void
    {
        $this->recalculatePrice();
    }

    public function updatedParticipantCount(): void
    {
        $this->recalculatePrice();
    }

    public function updatedType(): void { $this->recalculatePrice(); }
    public function updatedUserGroup(): void { $this->recalculatePrice(); }

    public function recalculatePrice(): void
    {
        if (! $this->planId || empty($this->selectedSubjectIds)) {
            $this->calculatedPrice = 0;

            return;
        }

        $plan = MockExamSubscriptionPlan::find($this->planId);
        if (! $plan) {
            $this->calculatedPrice = 0;

            return;
        }

        $this->calculatedPrice = $plan->calculatePrice(
            count($this->selectedSubjectIds),
            $this->participantCount,
            $this->userGroup
        );
    }

    public function selectUser(int $userId, string $name): void
    {
        $this->selectedUserId = $userId;
        $this->selectedUserName = $name;
        $this->userSearch = '';
    }

    public function openAllocationForm(): void
    {
        $this->reset(['selectedUserId', 'selectedUserName', 'planId', 'type', 'userGroup', 'selectedSubjectIds', 'participantCount', 'maxExams', 'calculatedPrice', 'subjectSearch']);
        $this->allocationMode = 'grant';
        $this->participantCount = 30;
        $this->type = 'print';
        $this->userGroup = 'teacher';
        $this->maxExams = 1;
        $this->showAllocationForm = true;
    }

    public function allocate(MockExamSubscriptionService $service)
    {
        $this->validate([
            'selectedUserId'     => 'required|exists:users,id',
            'planId'             => 'required|exists:mock_exam_subscription_plans,id',
            'type'               => 'required|in:online,print',
            'userGroup'          => 'required|in:teacher,school,local_government,organization',
            'selectedSubjectIds' => 'required|array|min:1|distinct',
            'participantCount'   => 'required_if:type,online|integer|min:1',
            'maxExams'           => 'required|integer|min:1',
        ]);

        $owner = auth()->user();
        $targetUser = User::findOrFail($this->selectedUserId);

        $config = [
            'plan_id'         => $this->planId,
            'type'            => $this->type,
            'user_group'      => $this->userGroup,
            'subject_ids'     => array_unique($this->selectedSubjectIds),
            'participant_count' => $this->type === 'online' ? $this->participantCount : 0,
            'max_exams'       => $this->maxExams,
        ];

        try {
            if ($this->allocationMode === 'grant') {
                $service->grantSubscription($owner, $targetUser, $config);
                $this->showAllocationForm = false;
                $this->dispatch('flash', type: 'success', message: "Subscription granted to {$targetUser->name}.");
            } else {
                $result = $service->initiateOwnerPayment($owner, $targetUser, $config);
                if ($result['authorization_url']) {
                    return redirect($result['authorization_url']);
                }
            }
        } catch (\Throwable $e) {
            \Log::error('Mock exam owner subscription allocation failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);
            $this->dispatch('flash', type: 'error', message: $e->getMessage());
        }
    }

    public function cancelSubscription(int $id): void
    {
        $subscription = MockExamSubscription::findOrFail($id);
        $subscription->update(['status' => MockExamSubscriptionStatus::Cancelled]);
        $this->dispatch('flash', type: 'success', message: 'Subscription cancelled.');
    }

    public function getUserSearchResultsProperty(): \Illuminate\Support\Collection
    {
        if (strlen($this->userSearch) < 2) {
            return collect();
        }

        return User::where(function ($q) {
            $q->where('name', 'like', "%{$this->userSearch}%")
                ->orWhere('email', 'like', "%{$this->userSearch}%");
        })->limit(10)->get();
    }

    public function getPlansProperty()
    {
        return MockExamSubscriptionPlan::active()->get();
    }

    public function addSubject(int $id): void
    {
        if (in_array($id, $this->selectedSubjectIds)) {
            return; // prevent duplicates
        }
        $this->selectedSubjectIds[] = $id;
        $this->recalculatePrice();
        $this->subjectSearch = '';
    }

    public function removeSubject(int $id): void
    {
        $this->selectedSubjectIds = array_values(array_filter($this->selectedSubjectIds, fn ($s) => $s !== $id));
        $this->recalculatePrice();
    }

    public function getSubjectSearchResultsProperty()
    {
        if (strlen($this->subjectSearch) < 1) {
            return collect();
        }

        return AcademicSubject::withoutGlobalScopes()
            ->where('name', 'like', "%{$this->subjectSearch}%")
            ->whereNotIn('id', $this->selectedSubjectIds)
            ->orderBy('name')
            ->limit(10)
            ->get();
    }

    public function getSelectedSubjectsProperty()
    {
        if (empty($this->selectedSubjectIds)) {
            return collect();
        }

        return AcademicSubject::withoutGlobalScopes()
            ->whereIn('id', $this->selectedSubjectIds)
            ->get();
    }

    public function getSubjectsProperty()
    {
        return AcademicSubject::withoutGlobalScopes()->orderBy('name')->get();
    }

    public function render()
    {
        $subscriptions = MockExamSubscription::with(['user', 'plan', 'subjects', 'grantedBy'])
            ->when($this->search, fn ($q) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%")))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(15);

        return view('livewire.mock-exam.subscriptions.mock-exam-subscription-manager', [
            'subscriptions' => $subscriptions,
        ]);
    }
}
