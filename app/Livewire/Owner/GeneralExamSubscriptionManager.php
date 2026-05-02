<?php

namespace App\Livewire\Owner;

use App\Models\AcademicSubject;
use App\Models\GeneralExamSubscription;
use App\Models\GeneralExamSubscriptionPlan;
use App\Models\User;
use App\Services\GeneralExam\GeneralExamSubscriptionService;
use Livewire\Component;
use Livewire\WithPagination;

class GeneralExamSubscriptionManager extends Component
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

    public string $type = 'online';

    public string $subjectSearch = '';

    public array $selectedSubjectIds = [];

    public int $participantCount = 30;

    public ?int $maxExams = null;

    public string $allocationMode = 'grant'; // grant or payment

    public float $calculatedPrice = 0;

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

    public function updatedType(): void
    {
        $this->recalculatePrice();
    }

    public function recalculatePrice(): void
    {
        if (! $this->planId || empty($this->selectedSubjectIds)) {
            $this->calculatedPrice = 0;

            return;
        }

        $plan = GeneralExamSubscriptionPlan::find($this->planId);
        if (! $plan) {
            $this->calculatedPrice = 0;

            return;
        }

        $this->calculatedPrice = $plan->calculatePrice(
            count($this->selectedSubjectIds),
            $this->participantCount
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
        $this->reset(['selectedUserId', 'selectedUserName', 'planId', 'type', 'selectedSubjectIds', 'participantCount', 'maxExams', 'calculatedPrice', 'subjectSearch']);
        $this->allocationMode = 'grant';
        $this->participantCount = 30;
        $this->type = 'online';
        $this->showAllocationForm = true;
    }

    public function allocate(GeneralExamSubscriptionService $service): void
    {
        $this->validate([
            'selectedUserId' => 'required|exists:users,id',
            'planId' => 'required|exists:general_exam_subscription_plans,id',
            'type' => 'required|in:online,print',
            'selectedSubjectIds' => 'required|array|min:1',
            'participantCount' => 'required_if:type,online|integer|min:1',
        ]);

        $owner = auth()->user();
        $targetUser = User::findOrFail($this->selectedUserId);

        $config = [
            'plan_id' => $this->planId,
            'type' => $this->type,
            'subject_ids' => $this->selectedSubjectIds,
            'participant_count' => $this->type === 'online' ? $this->participantCount : 0,
            'max_exams' => $this->maxExams,
        ];

        if ($this->allocationMode === 'grant') {
            $service->grantSubscription($owner, $targetUser, $config);
            $this->showAllocationForm = false;
            $this->dispatch('flash', type: 'success', message: "Subscription granted to {$targetUser->name}.");
        } else {
            $result = $service->initiateOwnerPayment($owner, $targetUser, $config);
            if ($result['authorization_url']) {
                $this->redirect($result['authorization_url']);
            }
        }
    }

    public function cancelSubscription(int $id): void
    {
        $subscription = GeneralExamSubscription::findOrFail($id);
        $subscription->update(['status' => \App\Enums\GeneralExamSubscriptionStatus::Cancelled]);
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
        return GeneralExamSubscriptionPlan::active()->get();
    }

    public function addSubject(int $id): void
    {
        if (! in_array($id, $this->selectedSubjectIds)) {
            $this->selectedSubjectIds[] = $id;
            $this->recalculatePrice();
        }
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
        $subscriptions = GeneralExamSubscription::with(['user', 'plan', 'subjects', 'grantedBy'])
            ->when($this->search, fn ($q) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%")))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(15);

        return view('livewire.owner.general-exam-subscription-manager', [
            'subscriptions' => $subscriptions,
        ]);
    }
}
