<?php

namespace App\Livewire\Teachers;

use App\Models\AcademicSubject;
use App\Models\GeneralExam;
use App\Models\GeneralExamScoreAuditLog;
use App\Models\GeneralExamSubmission;
use App\Models\GeneralExamSubscription;
use App\Models\GeneralExamSubscriptionPlan;
use App\Services\GeneralExam\GeneralExamSubscriptionService;
use Livewire\Component;
use Livewire\WithPagination;

class GeneralExamSubscriptionDashboard extends Component
{
    use WithPagination;

    public string $activeTab = 'subscriptions';

    // Subscription purchase form
    public bool $showPurchaseForm = false;

    public int $planId = 0;

    public string $type = 'online';

    public array $selectedSubjectIds = [];

    public string $subjectSearch = '';

    public int $participantCount = 30;

    public ?int $maxExams = null;

    public float $calculatedPrice = 0;

    // Top-up form
    public bool $showTopUpForm = false;

    public ?int $topUpSubscriptionId = null;

    public int $additionalParticipants = 10;

    public float $topUpPrice = 0;

    // Results dashboard
    public ?int $selectedSubscriptionId = null;

    public ?int $selectedExamId = null;

    public string $participantSearch = '';

    // Score editing
    public bool $showScoreEditor = false;

    public ?int $editingSubmissionId = null;

    public array $editedScores = [];

    public string $scoreEditReason = '';

    // Performance query
    public string $performanceSearch = '';

    public array $performanceResults = [];

    public function mount(): void {}

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

    // ==================== PURCHASE FLOW ====================

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
        $this->calculatedPrice = $plan
            ? $plan->calculatePrice(count($this->selectedSubjectIds), $this->participantCount)
            : 0;
    }

    public function purchase(GeneralExamSubscriptionService $service): void
    {
        $this->validate([
            'planId' => 'required|exists:general_exam_subscription_plans,id',
            'type' => 'required|in:online,print',
            'selectedSubjectIds' => 'required|array|min:1',
            'participantCount' => 'required_if:type,online|integer|min:1',
        ]);

        $result = $service->initiatePayment(auth()->user(), [
            'plan_id' => $this->planId,
            'type' => $this->type,
            'subject_ids' => $this->selectedSubjectIds,
            'participant_count' => $this->type === 'online' ? $this->participantCount : 0,
            'max_exams' => $this->maxExams,
        ]);

        if ($result['authorization_url']) {
            $this->redirect($result['authorization_url']);
        }
    }

    // ==================== TOP-UP ====================

    public function openTopUp(int $subscriptionId): void
    {
        $this->topUpSubscriptionId = $subscriptionId;
        $this->additionalParticipants = 10;
        $this->topUpPrice = 0;
        $this->showTopUpForm = true;
        $this->recalculateTopUp();
    }

    public function updatedAdditionalParticipants(): void
    {
        $this->recalculateTopUp();
    }

    public function recalculateTopUp(?GeneralExamSubscriptionService $service = null): void
    {
        if (! $this->topUpSubscriptionId) {
            return;
        }

        $service ??= app(GeneralExamSubscriptionService::class);
        $subscription = GeneralExamSubscription::find($this->topUpSubscriptionId);

        if ($subscription) {
            $this->topUpPrice = $service->calculateTopUpPrice($subscription, $this->additionalParticipants);
        }
    }

    public function processTopUp(GeneralExamSubscriptionService $service): void
    {
        $this->validate(['additionalParticipants' => 'required|integer|min:1']);

        $subscription = GeneralExamSubscription::findOrFail($this->topUpSubscriptionId);
        $result = $service->initiateTopUp(auth()->user(), $subscription, $this->additionalParticipants);

        if ($result['authorization_url']) {
            $this->redirect($result['authorization_url']);
        }
    }

    // ==================== SCORE EDITING ====================

    public function openScoreEditor(int $submissionId): void
    {
        $submission = GeneralExamSubmission::with('assignment.questions')->findOrFail($submissionId);
        $this->editingSubmissionId = $submissionId;
        $this->editedScores = [];
        $this->scoreEditReason = '';

        foreach ($submission->assignment->questions as $question) {
            $this->editedScores[$question->id] = $submission->responses[$question->id]['points_earned'] ?? 0;
        }

        $this->showScoreEditor = true;
    }

    public function saveScores(GeneralExamSubscriptionService $service): void
    {
        $this->validate([
            'scoreEditReason' => 'nullable|string|max:500',
            'editedScores.*' => 'required|numeric|min:0',
        ]);

        $submission = GeneralExamSubmission::findOrFail($this->editingSubmissionId);
        $service->updateScoreWithAudit($submission, $this->editedScores, auth()->user(), $this->scoreEditReason ?: null);

        $this->showScoreEditor = false;
        $this->dispatch('flash', type: 'success', message: 'Scores updated and audit log recorded.');
    }

    // ==================== PERFORMANCE QUERY ====================

    public function searchPerformance(GeneralExamSubscriptionService $service): void
    {
        $this->validate(['performanceSearch' => 'required|string|min:2']);

        $this->performanceResults = $service->getParticipantPerformance(auth()->user(), $this->performanceSearch);
    }

    // ==================== RENDER ====================

    public function render()
    {
        $user = auth()->user();

        $subscriptions = GeneralExamSubscription::with(['plan', 'subjects'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $selectedSubscription = $this->selectedSubscriptionId
            ? $subscriptions->firstWhere('id', $this->selectedSubscriptionId)
            : null;

        $exams = $selectedSubscription
            ? GeneralExam::where('general_exam_subscription_id', $selectedSubscription->id)
                ->withCount('submissions')
                ->latest()
                ->get()
            : collect();

        $submissions = collect();
        $auditLogs = collect();

        if ($this->selectedExamId) {
            $submissions = GeneralExamSubmission::where('general_exam_id', $this->selectedExamId)
                ->with(['participant'])
                ->when($this->participantSearch, function ($q) {
                    // Filter by participant name/email after loading
                })
                ->whereNotNull('submitted_at')
                ->latest('submitted_at')
                ->paginate(20);

            if ($this->editingSubmissionId) {
                $auditLogs = GeneralExamScoreAuditLog::where('general_exam_submission_id', $this->editingSubmissionId)
                    ->with('editor')
                    ->latest()
                    ->get();
            }
        }

        $editingSubmission = $this->editingSubmissionId
            ? GeneralExamSubmission::with('assignment.questions')->find($this->editingSubmissionId)
            : null;

        return view('livewire.teachers.general-exam-subscription-dashboard', [
            'subscriptions' => $subscriptions,
            'selectedSubscription' => $selectedSubscription,
            'exams' => $exams,
            'submissions' => $submissions,
            'auditLogs' => $auditLogs,
            'editingSubmission' => $editingSubmission,
            'plans' => GeneralExamSubscriptionPlan::active()->get(),
            'subjects' => AcademicSubject::withoutGlobalScopes()->orderBy('name')->get(),
        ]);
    }
}
