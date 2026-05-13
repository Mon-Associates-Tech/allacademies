<?php

namespace App\Livewire\Public;

use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\Services\GeneralExam\GeneralExamGradingService;
use App\Services\GeneralExam\GeneralExamService;
use Livewire\Component;

class TakeGeneralExam extends Component
{
    public GeneralExam $assignment;

    public GeneralExamSubmission $submission;

    // Question navigation
    public int $currentQuestionIndex = 0;

    public array $responses = [];

    public array $questionOrder = [];

    // Timer
    public int $remainingSeconds = 0;

    public bool $timerStarted = false;

    // Proctoring
    public int $tabSwitchCount = 0;

    public bool $isFullscreen = false;

    // State
    public bool $hasStarted = false;

    public bool $isSubmitted = false;

    public bool $showInstructions = true;

    public bool $showReview = false;

    public bool $confirmSubmit = false;

    // Participant info (for session)
    public ?int $participantId = null;

    public ?string $participantType = null;

    protected GeneralExamService $assignmentService;

    protected GeneralExamGradingService $gradingService;

    protected $listeners = [
        'tabSwitch' => 'recordTabSwitch',
        'fullscreenChange' => 'handleFullscreenChange',
        'timerTick' => 'updateTimer',
        'beforeUnload' => 'saveProgress',
    ];

    public function boot(
        GeneralExamService             $assignmentService,
        GeneralExamGradingService $gradingService
    ): void {
        $this->assignmentService = $assignmentService;
        $this->gradingService = $gradingService;
    }

    public function mount(GeneralExamSubmission $submission): void
    {
        $this->submission = $submission;
        $this->assignment = $submission->assignment;
        $this->participantId = $submission->participant_id;
        $this->participantType = $submission->participant_type;

        // Check if already submitted
        if ($this->submission->isSubmitted()) {
            $this->isSubmitted = true;

            return;
        }

        // Check if assignment is still active
        if (! $this->assignment->isActive()) {
            session()->flash('error', 'This assignment is no longer available.');

            return;
        }

        // Initialize responses from existing submission
        $this->responses = $this->submission->responses ?? [];

        // Set up question order (randomize if needed)
        $this->initializeQuestionOrder();

        // Check if already started
        if ($this->submission->isInProgress()) {
            $this->hasStarted = true;
            $this->showInstructions = false;
            $this->timerStarted = true;
            $this->tabSwitchCount = $this->submission->tab_switch_count;
        }

        // Calculate remaining time
        $this->calculateRemainingTime();
    }

    protected function initializeQuestionOrder(): void
    {
        $questions = $this->assignment->questions;

        if ($this->assignment->is_randomized) {
            $this->questionOrder = $questions->pluck('id')->shuffle()->toArray();
        } else {
            $this->questionOrder = $questions->pluck('id')->toArray();
        }
    }

    protected function calculateRemainingTime(): void
    {
        if (! $this->assignment->duration_in_minutes) {
            $this->remainingSeconds = 0; // No time limit

            return;
        }

        $remaining = $this->submission->getRemainingTime();
        $this->remainingSeconds = $remaining ?? ($this->assignment->duration_in_minutes * 60);
    }

    public function startAssignment(): void
    {
        if ($this->hasStarted) {
            return;
        }

        // Start the submission
        $this->submission->start();

        // Start proctoring session if enabled
        if ($this->assignment->proctoring_enabled && $this->submission->proctoringSession) {
            $this->submission->proctoringSession->start([
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        $this->hasStarted = true;
        $this->showInstructions = false;
        $this->timerStarted = true;

        $this->calculateRemainingTime();

        $this->dispatch('assignment-started');
    }

    public function updateTimer(): void
    {
        if (! $this->timerStarted || $this->remainingSeconds <= 0) {
            return;
        }

        $this->remainingSeconds--;

        // Auto-submit when time runs out
        if ($this->remainingSeconds <= 0 && $this->assignment->duration_in_minutes) {
            $this->autoSubmit('Time expired');
        }
    }

    public function recordTabSwitch(): void
    {
        if (! $this->hasStarted || $this->isSubmitted) {
            return;
        }

        $this->tabSwitchCount++;
        $this->submission->recordViolation('tab_switch');

        // Update proctoring session
        if ($this->submission->proctoringSession) {
            $this->submission->proctoringSession->recordTabSwitch();
        }

        // Check if exceeded limit
        if ($this->assignment->restrict_navigation &&
            $this->tabSwitchCount >= $this->assignment->max_tab_switches &&
            $this->assignment->auto_submit_on_violation) {
            $this->autoSubmit('Tab switch limit exceeded');
        }
    }

    public function handleFullscreenChange(bool $isFullscreen): void
    {
        $this->isFullscreen = $isFullscreen;

        if (! $isFullscreen && $this->hasStarted && ! $this->isSubmitted) {
            if ($this->submission->proctoringSession) {
                $this->submission->proctoringSession->recordFullscreenExit();
            }
        }
    }

    public function saveResponse(int $questionId, $response): void
    {
        $this->responses[$questionId] = [
            'response' => $response,
            'answered_at' => now()->toISOString(),
        ];

        // Save to database
        $this->submission->saveResponse($questionId, $response);
    }

    public function updatedResponses($value, $key): void
    {
        // Livewire passes keys like "123.response" or "responses.123.response"
        $parts = explode('.', $key);

        $questionId = null;
        if (count($parts) >= 2) {
            if (is_numeric($parts[0])) {
                $questionId = (int) $parts[0];
            } elseif ($parts[0] === 'responses' && isset($parts[1]) && is_numeric($parts[1])) {
                $questionId = (int) $parts[1];
            }
        }

        if ($questionId !== null) {
            $response = $this->responses[$questionId]['response'] ?? $value;
            $this->saveResponse($questionId, $response);
        }
    }

    public function nextQuestion(): void
    {
        if ($this->currentQuestionIndex < count($this->questionOrder) - 1) {
            $this->currentQuestionIndex++;
        }
    }

    public function previousQuestion(): void
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        }
    }

    public function goToQuestion(int $index): void
    {
        if ($index >= 0 && $index < count($this->questionOrder)) {
            $this->currentQuestionIndex = $index;
        }
    }

    public function isQuestionAnswered(int $questionId): bool
    {
        $response = $this->responses[$questionId]['response'] ?? null;

        return $response !== null && $response !== '';
    }

    public function getAnsweredCount(): int
    {
        $count = 0;
        foreach ($this->questionOrder as $questionId) {
            if ($this->isQuestionAnswered($questionId)) {
                $count++;
            }
        }

        return $count;
    }

    public function toggleReview(): void
    {
        $this->showReview = ! $this->showReview;
    }

    public function confirmSubmission(): void
    {
        $this->confirmSubmit = true;
    }

    public function cancelSubmission(): void
    {
        $this->confirmSubmit = false;
    }

    public function submitAssignment(): void
    {
        if ($this->isSubmitted) {
            return;
        }

        // Save all current responses
        foreach ($this->responses as $questionId => $responseData) {
            if (isset($responseData['response'])) {
                $this->submission->saveResponse($questionId, $responseData['response']);
            }
        }

        // Submit the assignment with accurate time spent
        $this->submission->submit(false);

        // End proctoring session
        if ($this->submission->proctoringSession) {
            $this->submission->proctoringSession->end();
        }

        // Grade the submission
        $this->gradingService->gradeSubmission($this->submission);

        $this->isSubmitted = true;
        $this->confirmSubmit = false;

        $this->dispatch('assignment-submitted');
    }

    protected function autoSubmit(string $reason): void
    {
        if ($this->isSubmitted) {
            return;
        }

        // Save all current responses
        foreach ($this->responses as $questionId => $responseData) {
            if (isset($responseData['response'])) {
                $this->submission->saveResponse($questionId, $responseData['response']);
            }
        }

        // Submit with auto-submit flag
        $this->submission->submit(true, $reason);

        // End proctoring session
        if ($this->submission->proctoringSession) {
            $this->submission->proctoringSession->terminate($reason);
        }

        // Grade the submission
        $this->gradingService->gradeSubmission($this->submission);

        $this->isSubmitted = true;

        $this->dispatch('assignment-auto-submitted', reason: $reason);
    }

    public function saveProgress(): void
    {
        // Save current responses to database
        foreach ($this->responses as $questionId => $responseData) {
            if (isset($responseData['response'])) {
                $this->submission->saveResponse($questionId, $responseData['response']);
            }
        }
    }

    public function getCurrentQuestionProperty()
    {
        if (empty($this->questionOrder)) {
            return null;
        }

        $questionId = $this->questionOrder[$this->currentQuestionIndex] ?? null;

        if (! $questionId) {
            return null;
        }

        return $this->assignment->questions->firstWhere('id', $questionId);
    }

    public function getProgressPercentageProperty(): int
    {
        $total = count($this->questionOrder);
        if ($total === 0) {
            return 0;
        }

        return (int) round(($this->getAnsweredCount() / $total) * 100);
    }

    public function getFormattedTimeProperty(): string
    {
        if ($this->remainingSeconds <= 0 && ! $this->assignment->duration_in_minutes) {
            return 'No time limit';
        }

        $hours = floor($this->remainingSeconds / 3600);
        $minutes = floor(($this->remainingSeconds % 3600) / 60);
        $seconds = $this->remainingSeconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function getCanViewResultsProperty(): bool
    {
        return $this->isSubmitted && $this->assignment->canShowResults();
    }

    public function render()
    {
        return view('livewire.public.take-general-exam', [
            'currentQuestion' => $this->currentQuestion,
            'progressPercentage' => $this->progressPercentage,
            'formattedTime' => $this->formattedTime,
            'answeredCount' => $this->getAnsweredCount(),
            'totalQuestions' => count($this->questionOrder),
        ])->layout('layouts.general-exam');
    }
}
