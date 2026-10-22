<?php

namespace App\Livewire\MockExam\Subscriptions;

use App\MockExam\Models\MockExam;
use App\MockExam\Models\MockExamPricingTier;
use App\MockExam\Models\MockExamSubmission;
use App\MockExam\Models\MockExamSubscription;
use App\MockExam\Models\MockExamSubscriptionPlan;
use App\MockExam\Models\MockExamUserGroup;
use App\MockExam\Services\MockExamScoreAuditLog;
use App\MockExam\Services\MockExamSubscriptionService;
use App\MockExam\Models\MockExamSubscriptionStatus;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class Dashboard extends Component
{
    use WithPagination;

#[Url(as: 'tab', except: 'subscriptions', history: true)]
public string $activeTab = 'subscriptions';

    // ── Search and Filter ──────────────────────────────────────────────────────
    #[Url]
    public string $search = '';
    #[Url]
    public string $sortBy = 'created_at';
    #[Url]
    public string $sortDirection = 'desc';
    #[Url(as: 'exam_type', except: 'all')]
    public string $examTypeFilter = 'all';

    // ── Purchase form ──────────────────────────────────────────────────────────
    public bool $showPurchaseForm = false;
    public int $planId = 0;
    public string $type = 'print';
    public string $userGroup = 'teacher';
    public array $selectedSubjectIds = [];
    public string $subjectSearch = '';
    public ?int $selectedAcademicGroupId = null;
    public ?int $selectedAcademicLevelId = null;
    public int $participantCount = 30;
    public ?int $maxExams = 1;
    public float $calculatedPrice = 0;

    // ── Pending payment ────────────────────────────────────────────────────────
    public bool $showCancelConfirm = false;
    public ?int $cancellingSubscriptionId = null;

    // ── Subscription details modal ───────────────────────────────────────────
    public bool $showSubscriptionDetails = false;
    public ?int $detailsSubscriptionId = null;

    // ── Expanded mocks ─────────────────────────────────────────────────────────
    public array $expandedSubscriptionIds = [];

    // ── Top-up form ────────────────────────────────────────────────────────────
    public bool $showTopUpForm = false;
    public ?int $topUpSubscriptionId = null;
    public int $additionalParticipants = 10;
    public float $topUpPrice = 0;

    // ── Results dashboard ──────────────────────────────────────────────────────
    public ?int $selectedSubscriptionId = null;
    public ?int $selectedExamId = null;
    public string $participantSearch = '';

    // ── Score editing ──────────────────────────────────────────────────────────
    public bool $showScoreEditor = false;
    public ?int $editingSubmissionId = null;
    public array $editedScores = [];
    public string $scoreEditReason = '';

    // ── Performance query ──────────────────────────────────────────────────────
    public string $performanceSearch = '';
    public array $performanceResults = [];



protected array $allowedTabs = [
    'subscriptions',
    'results',
    'performance',
    'identity',
];

public function mount(): void
{
    $savedTab = session($this->tabSessionKey(), 'subscriptions');

    $this->activeTab = in_array($savedTab, $this->allowedTabs, true)
        ? $savedTab
        : 'subscriptions';
}

public function updatedActiveTab(string $value): void
{
    $tab = in_array($value, $this->allowedTabs, true)
        ? $value
        : 'subscriptions';

    session()->put($this->tabSessionKey(), $tab);
}

protected function tabSessionKey(): string
{
    return 'mock-exam.subscriptions.dashboard.active_tab';
}

    // ==================== AUTH GUARD ====================

    private function requireAuth(string $message = null): bool
    {
        if (auth()->check()) {
            return true;
        }
        session()->flash('info', $message ?? 'Please sign in to continue.');
        $this->redirect(route('login'));
        return false;
    }

    // ==================== SUBJECT SELECTION ====================

    public function addSubject(int $id): void
    {
        if (in_array($id, $this->selectedSubjectIds)) {
            return;
        }
        $this->selectedSubjectIds[] = $id;
        $this->subjectSearch = '';
        $this->recalculatePrice();
    }

    public function removeSubject(int $id): void
    {
        $this->selectedSubjectIds = array_values(
            array_filter($this->selectedSubjectIds, fn ($s) => $s !== $id)
        );
        $this->recalculatePrice();
    }

    public function getSubjectSearchResultsProperty()
    {
        if (strlen($this->subjectSearch) < 1) {
            return collect();
        }

        return AcademicSubject::withoutGlobalScopes()
            ->when($this->selectedAcademicLevelId, fn ($q) => $q->where('academic_level_id', $this->selectedAcademicLevelId))
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
            ->with('academicLevel.academicGroup')
            ->whereIn('id', $this->selectedSubjectIds)
            ->get();
    }

    public function updatedSelectedAcademicGroupId(): void
    {
        $this->selectedAcademicLevelId = null;
        $this->subjectSearch = '';
    }

    public function getAcademicGroupsProperty()
    {
        return AcademicGroup::orderBy('name')->get(['id', 'name']);
    }

    public function getAcademicLevelsProperty()
    {
        if (! $this->selectedAcademicGroupId) {
            return collect();
        }

        return AcademicLevel::where('academic_group_id', $this->selectedAcademicGroupId)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function getFilteredSubjectsProperty()
    {
        if (! $this->selectedAcademicLevelId) {
            return collect();
        }

        return AcademicSubject::withoutGlobalScopes()
            ->where('academic_level_id', $this->selectedAcademicLevelId)
            ->whereNotIn('id', $this->selectedSubjectIds)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    // ==================== PRICING ====================

    public function getSelectedPricingTierProperty(): ?MockExamPricingTier
    {
        if (! $this->planId || empty($this->selectedSubjectIds) || ! $this->userGroup) {
            return null;
        }

        $plan = MockExamSubscriptionPlan::find($this->planId);
        if (! $plan || $plan->base_price > 0) {
            return null;
        }

        return MockExamPricingTier::forSubjectCountAndGroup(
            count($this->selectedSubjectIds),
            $this->userGroup
        );
    }

    public function getUserGroupOptionsProperty(): array
    {
        return MockExamUserGroup::options();
    }

    // ==================== PURCHASE FLOW ====================

    public function updatedPlanId(): void
    {
        $plan = MockExamSubscriptionPlan::find($this->planId);
        if ($plan) {
            $this->type = $plan->type;
        }
        $this->recalculatePrice();
    }
    public function updatedUserGroup(): void { $this->recalculatePrice(); }
    public function updatedSelectedSubjectIds(): void { $this->recalculatePrice(); }
    public function updatedParticipantCount(): void { $this->recalculatePrice(); }
    public function updatedMaxExams(): void { $this->recalculatePrice(); }

    public function recalculatePrice(): void
    {
        if (! $this->planId || empty($this->selectedSubjectIds)) {
            $this->calculatedPrice = 0;
            return;
        }

        $plan = MockExamSubscriptionPlan::find($this->planId);
        $this->calculatedPrice = $plan
            ? $plan->calculatePrice(
                count($this->selectedSubjectIds),
                $this->participantCount,
                $this->userGroup,
                max(1, (int) $this->maxExams)
            )
            : 0;
    }

    public function openPurchaseForm(): void
    {
        if (! $this->requireAuth('Please sign in to purchase a subscription.')) {
            return;
        }

        $this->reset([
            'planId', 'type', 'userGroup', 'selectedSubjectIds', 'subjectSearch',
            'selectedAcademicGroupId', 'selectedAcademicLevelId',
            'participantCount', 'maxExams', 'calculatedPrice',
        ]);
        $this->userGroup = 'teacher';
        $this->participantCount = 30;
        $this->maxExams = 1;
        $this->showPurchaseForm = true;
    }

    private function resolvePlanId(string $type): int
    {
        return (int) MockExamSubscriptionPlan::active()
            ->where('type', $type)
            ->value('id');
    }

    public function purchase(MockExamSubscriptionService $service)
    {
        if (! $this->requireAuth('Please sign in to purchase a mock exam subscription.')) {
            return;
        }

        $this->validate([
            'planId'             => 'required|exists:mock_exam_subscription_plans,id',
            'type'               => 'required|in:online,print',
            'userGroup'          => 'required|in:teacher,school,local_government,organization',
            'selectedSubjectIds' => 'required|array|min:1|distinct',
            'participantCount'   => 'required_if:type,online|integer|min:1',
            'maxExams'           => 'required|integer|min:1',
        ]);

        try {
            $result = $service->initiatePayment(auth()->user(), [
                'plan_id'         => $this->planId,
                'type'            => $this->type,
                'user_group'      => $this->userGroup,
                'subject_ids'     => array_unique($this->selectedSubjectIds),
                'participant_count' => $this->type === 'online' ? $this->participantCount : 0,
                'max_exams'       => $this->maxExams,
            ]);

            if (! empty($result['authorization_url'])) {
                return redirect($result['authorization_url']);
            }
        } catch (\Throwable $e) {
            \Log::error('Mock exam subscription purchase failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->addError('planId', $e->getMessage());
        }
    }

    // ==================== PENDING PAYMENT ACTIONS ====================

    public function retryPayment(int $subscriptionId, MockExamSubscriptionService $service)
    {
        $subscription = MockExamSubscription::with('subjects')->findOrFail($subscriptionId);
        abort_unless($subscription->user_id === auth()->id(), 403);

        $payment = $subscription->payments()->where('status', 'pending')->latest()->first();

        if ($payment && $payment->paystack_access_code) {
            return redirect('https://checkout.paystack.com/' . $payment->paystack_access_code);
        }

        try {
            $result = $service->initiatePayment(auth()->user(), [
                'plan_id'         => $subscription->mock_exam_subscription_plan_id,
                'type'            => $subscription->type,
                'user_group'      => $subscription->user_group instanceof \App\MockExam\Models\MockExamUserGroup
                    ? $subscription->user_group->value
                    : $subscription->user_group,
                'subject_ids'     => $subscription->subjects->pluck('id')->toArray(),
                'participant_count' => $subscription->participant_slots,
                'max_exams'       => $subscription->max_exams,
            ]);

            if (! empty($result['authorization_url'])) {
                return redirect($result['authorization_url']);
            }
        } catch (\Throwable $e) {
            \Log::error('Mock exam subscription retry payment failed', [
                'user_id' => auth()->id(),
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage(),
            ]);
            $this->dispatch('flash', type: 'error', message: $e->getMessage());
        }
    }

    public function confirmCancel(int $subscriptionId): void
    {
        $this->cancellingSubscriptionId = $subscriptionId;
        $this->showCancelConfirm = true;
    }

    public function cancelSubscription(): void
    {
        $subscription = MockExamSubscription::findOrFail($this->cancellingSubscriptionId);
        abort_unless($subscription->user_id === auth()->id(), 403);

        $subscription->update(['status' => \App\MockExam\Models\MockExamSubscriptionStatus::Cancelled]);

        $this->showCancelConfirm = false;
        $this->cancellingSubscriptionId = null;
        $this->showSubscriptionDetails = false;
        $this->detailsSubscriptionId = null;
        $this->dispatch('flash', type: 'success', message: 'Subscription cancelled.');
        $this->activeTab = 'subscriptions';
    }

    // ==================== TOGGLE MOCKS ====================

    public function toggleMocks(int $subscriptionId): void
    {
        if (in_array($subscriptionId, $this->expandedSubscriptionIds)) {
            $this->expandedSubscriptionIds = array_values(
                array_filter($this->expandedSubscriptionIds, fn ($id) => $id !== $subscriptionId)
            );
        } else {
            $this->expandedSubscriptionIds[] = $subscriptionId;
        }
    }

    // ==================== TOP-UP ====================

    public function openTopUp(int $subscriptionId): void
    {
        if (! $this->requireAuth()) {
            return;
        }

        $this->topUpSubscriptionId = $subscriptionId;
        $this->additionalParticipants = 10;
        $this->topUpPrice = 0;
        $this->showTopUpForm = true;
        $this->recalculateTopUp();
    }

    public function updatedAdditionalParticipants(): void { $this->recalculateTopUp(); }

    public function recalculateTopUp(?MockExamSubscriptionService $service = null): void
    {
        if (! $this->topUpSubscriptionId) {
            return;
        }
        $service ??= app(MockExamSubscriptionService::class);
        $subscription = MockExamSubscription::find($this->topUpSubscriptionId);
        if ($subscription) {
            $this->topUpPrice = $service->calculateTopUpPrice($subscription, $this->additionalParticipants);
        }
    }

    public function processTopUp(MockExamSubscriptionService $service)
    {
        if (! $this->requireAuth()) {
            return;
        }

        $this->validate(['additionalParticipants' => 'required|integer|min:1']);

        try {
            $subscription = MockExamSubscription::findOrFail($this->topUpSubscriptionId);
            abort_unless($subscription->user_id === auth()->id(), 403);

            $result = $service->initiateTopUp(auth()->user(), $subscription, $this->additionalParticipants);

            if (! empty($result['authorization_url'])) {
                return redirect($result['authorization_url']);
            }
        } catch (\Throwable $e) {
            \Log::error('Mock exam subscription top-up payment failed', [
                'user_id' => auth()->id(),
                'subscription_id' => $this->topUpSubscriptionId,
                'error' => $e->getMessage(),
            ]);
            $this->addError('additionalParticipants', $e->getMessage());
        }
    }

    // ==================== SCORE EDITING ====================

    public function openScoreEditor(int $submissionId): void
    {
        if (! $this->requireAuth()) {
            return;
        }

        $submission = MockExamSubmission::with('assignment.questions')->findOrFail($submissionId);
        $this->editingSubmissionId = $submissionId;
        $this->editedScores = [];
        $this->scoreEditReason = '';

        foreach ($submission->assignment->questions as $question) {
            $this->editedScores[$question->id] = $submission->responses[$question->id]['points_earned'] ?? 0;
        }

        $this->showScoreEditor = true;
    }

    public function saveScores(MockExamSubscriptionService $service): void
    {
        if (! $this->requireAuth()) {
            return;
        }

        $this->validate([
            'scoreEditReason' => 'nullable|string|max:500',
            'editedScores.*'  => 'required|numeric|min:0',
        ]);

        $submission = MockExamSubmission::findOrFail($this->editingSubmissionId);
        $service->updateScoreWithAudit($submission, $this->editedScores, auth()->user(), $this->scoreEditReason ?: null);
        $this->showScoreEditor = false;
        $this->dispatch('flash', type: 'success', message: 'Scores updated and audit log recorded.');
    }

    // ==================== PERFORMANCE QUERY ====================

    public function searchPerformance(MockExamSubscriptionService $service): void
    {
        if (! $this->requireAuth()) {
            return;
        }

        $this->validate(['performanceSearch' => 'required|string|min:2']);
        $this->performanceResults = $service->getParticipantPerformance(auth()->user(), $this->performanceSearch);
    }

    // ==================== SUBSCRIPTIONS QUERY ====================

    public function getMockSubscriptionsProperty(): Collection
    {
        $user = auth()->user();
        if (!$user) return collect();

        $query = MockExamSubscription::where('user_id', $user->id);

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->whereHas('plan', function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('subjects', function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('mockExams', function ($subQuery) {
                    $subQuery->where('title', 'like', '%' . $this->search . '%');
                });
            });
        }

        switch ($this->sortBy) {
            case 'plan.name':
                $query->orderBy('name');
                break;
            case 'expires_at':
                $query->orderBy('expires_at', $this->sortDirection);
                break;
            case 'created_at':
            default:
                $query->orderBy('created_at', $this->sortDirection);
                break;
        }

        $subscriptions = $query->with([
            'plan',
            'subjects',
            'grantedBy',
            'mockExams',
        ])->get();

        foreach ($subscriptions as $subscription) {
            $subscription->load('mockExams.subjectExams.academicSubject');
            $subscription->load('mockExams.subjectExams.sections.questions');

            // Filter mock exams by source type
            if ($this->examTypeFilter !== 'all') {
                $subscription->setRelation(
                    'mockExams',
                    $subscription->mockExams->filter(fn ($exam) => ($exam->source_type ?? 'template') === $this->examTypeFilter)
                );
            }
        }

        // When filtering by exam type, hide subscriptions with no matching exams
        if ($this->examTypeFilter !== 'all') {
            $subscriptions = $subscriptions->filter(fn ($sub) => $sub->mockExams->isNotEmpty());
        }

        return $subscriptions;
    }

    // ==================== SUBSCRIPTION DETAILS ====================

public function openSubscriptionDetails(int $subscriptionId): void
{
    $this->detailsSubscriptionId = $subscriptionId;
    $this->showSubscriptionDetails = true;
}

public function closeSubscriptionDetails(): void
{
    $this->showSubscriptionDetails = false;
    $this->detailsSubscriptionId = null;
}

public function getDetailsSubscriptionProperty(): ?MockExamSubscription
{
    if (! auth()->check() || ! $this->detailsSubscriptionId) {
        return null;
    }

    return MockExamSubscription::with(['plan', 'subjects', 'grantedBy', 'mockExams'])
        ->where('user_id', auth()->id())
        ->find($this->detailsSubscriptionId);
}

public function getCancellingSubscriptionProperty(): ?MockExamSubscription
{
    if (! auth()->check() || ! $this->cancellingSubscriptionId) {
        return null;
    }

    return MockExamSubscription::with(['plan', 'subjects'])
        ->where('user_id', auth()->id())
        ->find($this->cancellingSubscriptionId);
}

public function closeCancelConfirm(): void
{
    $this->showCancelConfirm = false;
    $this->cancellingSubscriptionId = null;
}

/**
 * Alias so the blade can continue using openTopUpForm().
 */
public function openTopUpForm(int $subscriptionId): void
{
    $this->openTopUp($subscriptionId);
}

    // ==================== RENDER ====================

    public function render()
    {
    if (! in_array($this->activeTab, $this->allowedTabs, true)) {
        $this->activeTab = 'subscriptions';
    }

        $mockSubscriptions = $this->mockSubscriptions;

        $subscriptionMocks = [];
        if (! empty($this->expandedSubscriptionIds)) {
            $mocks = MockExam::whereIn('mock_exam_subscription_id', $this->expandedSubscriptionIds)
                ->withCount('subjectExams')
                ->latest()
                ->get()
                ->groupBy('mock_exam_subscription_id');
            foreach ($this->expandedSubscriptionIds as $subId) {
                $subscriptionMocks[$subId] = $mocks->get($subId, collect());
            }
        }

        $selectedMockSubscription = $this->selectedSubscriptionId
            ? $mockSubscriptions->firstWhere('id', $this->selectedSubscriptionId)
            : null;

        $mockExams = $selectedMockSubscription
            ? MockExam::where('mock_exam_subscription_id', $selectedMockSubscription->id)
                ->withCount('submissions')
                ->latest()
                ->get()
            : collect();

        $mockSubmissions = collect();
        $mockAuditLogs = collect();

        if ($this->selectedExamId) {
            $mockSubmissions = MockExamSubmission::where('mock_exam_id', $this->selectedExamId)
                ->with('participant')
                ->whereNotNull('submitted_at')
                ->latest('submitted_at')
                ->paginate(20);

            if ($this->editingSubmissionId) {
                $mockAuditLogs = MockExamScoreAuditLog::where('mock_exam_submission_id', $this->editingSubmissionId)
                    ->with('editor')
                    ->latest()
                    ->get();
            }
        }

        $editingMockSubmission = $this->editingSubmissionId
            ? MockExamSubmission::with('assignment.questions')->find($this->editingSubmissionId)
            : null;

        return view('livewire.mock-exam.subscriptions.dashboard', [
            'mockSubscriptions'        => $mockSubscriptions,
            'subscriptionMocks'        => $subscriptionMocks,
            'selectedMockSubscription' => $selectedMockSubscription,
            'mockExams'                => $mockExams,
            'mockSubmissions'          => $mockSubmissions,
            'mockAuditLogs'            => $mockAuditLogs,
            'editingMockSubmission'    => $editingMockSubmission,
            'mockPlans'                => MockExamSubscriptionPlan::active()->get(),
            'selectedPricingTier'      => $this->selectedPricingTier,
            'userGroupOptions'         => $this->userGroupOptions,
        ]);
    }
}
