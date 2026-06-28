<?php

namespace App\Livewire\ExaminationHub;

use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamSubmission;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ExamSectionTaking extends Component
{
    #[Locked]
    public int $examId;

    #[Locked]
    public int $submissionId;

    #[Locked]
    public int $sectionId;

    public int $sectionIndex;

    public array $responses = [];

    public int $currentQuestionIndex = 0;
    public int $initialQuestionIndex = 0;

    public bool $showSectionInfo = true;
    public bool $instructionsAcknowledged = false;

    public ?int $timeRemaining = null;
    public array $flaggedQuestions = [];

    public string|int|null $start_time = '';

    public bool $isSingleSection = false;

    public function mount(GeneralExam $exam, GeneralExamSubmission $submission, $section, int $sectionIndex, $questions): void
    {
        $this->examId = $exam->id;
        $this->submissionId = $submission->id;
        $this->sectionId = $section->id;
        $this->sectionIndex = $sectionIndex;
        $this->flaggedQuestions = $submission->flagged_questions ?? [];
        // Load saved responses from fresh submission data
        $this->loadResponses();

        // If the submission has a saved last_position for this section, restore it
        $lastPos = $submission->last_position ?? null;
        if (is_array($lastPos) && isset($lastPos['section']) && (int) $lastPos['section'] === $sectionIndex) {
            $this->currentQuestionIndex = (int) ($lastPos['question'] ?? 0);
        }

        // Determine time remaining for this view: prefer section-level limits
        $sectionStartTimes = $submission->section_start_times ?? [];
        $sectionKey = (string) $section->id;

        if ($section->time_limit_minutes && isset($sectionStartTimes[$sectionKey])) {
            $startedAt      = \Carbon\Carbon::createFromTimestamp($sectionStartTimes[$sectionKey]);
            $endsAt         = $startedAt->copy()->addMinutes((int) $section->time_limit_minutes);
            $sectionSeconds = max(0, now()->diffInSeconds($endsAt, false));

            // Exam duration is the hard ceiling — cap the displayed timer to whatever is left on the exam clock
            if ($exam->duration_in_minutes && $submission->started_at) {
                $examEndsAt  = $submission->started_at->copy()->addMinutes((int) $exam->duration_in_minutes);
                $examSeconds = max(0, now()->diffInSeconds($examEndsAt, false));
                $sectionSeconds = min($sectionSeconds, $examSeconds);
            }

            $this->timeRemaining = $sectionSeconds;
        } elseif ($exam->duration_in_minutes && $submission->started_at) {
            $examEndsAt          = $submission->started_at->copy()->addMinutes((int) $exam->duration_in_minutes);
            $this->timeRemaining = max(0, now()->diffInSeconds($examEndsAt, false));
        }
    }

    protected function loadResponses(): void
    {
        $submission = $this->submission;
        $savedResponses = $submission->responses ?? [];

        foreach ($this->questions as $question) {
            if (isset($savedResponses[$question->id]['response'])) {
                $this->responses[$question->id] = $savedResponses[$question->id]['response'];
            }
        }
    }

    public function getExamProperty()
    {
        return GeneralExam::find($this->examId);
    }

    public function getSubmissionProperty()
    {
        return GeneralExamSubmission::find($this->submissionId);
    }

    public function getSectionProperty()
    {
        return $this->exam->sections()->find($this->sectionId);
    }

    public function getQuestionsProperty()
    {
        $questions = $this->section->questions()->orderBy('order')->get();

        // Apply randomization if stored in submission
        $submission = $this->submission;
        $exam = $this->exam;

        if (($exam->is_randomized || $this->section->is_randomized) && $questions->isNotEmpty()) {
            $randomizedOrder = $submission->randomized_question_order ?? [];
            $sectionKey = "section_{$this->sectionId}";

            if (isset($randomizedOrder[$sectionKey])) {
                $orderedQuestions = collect();
                foreach ($randomizedOrder[$sectionKey] as $questionId) {
                    $question = $questions->firstWhere('id', $questionId);
                    if ($question) {
                        $orderedQuestions->push($question);
                    }
                }

                return $orderedQuestions;
            }
        }

        return $questions;
    }

    public function goToQuestion(int $index): void
    {
        if ($index >= 0 && $index < $this->questions->count()) {
            $this->currentQuestionIndex = $index;
            // Persist last position
            $submission = $this->submission;
            $submission->update(['last_position' => ['section' => $this->sectionIndex, 'question' => $this->currentQuestionIndex]]);
        }
    }

    public function nextQuestion(): void
    {
        if ($this->currentQuestionIndex < $this->questions->count() - 1) {
            $this->currentQuestionIndex++;
            $submission = $this->submission;
            $submission->update(['last_position' => ['section' => $this->sectionIndex, 'question' => $this->currentQuestionIndex]]);
        }
    }

    public function previousQuestion(): void
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
            $submission = $this->submission;
            $submission->update(['last_position' => ['section' => $this->sectionIndex, 'question' => $this->currentQuestionIndex]]);
        }
    }

    public function updatedResponses($value, $key): void
    {
        // Reject saves after the authoritative time limit has expired.
        $submission      = $this->submission;
        $exam            = $this->exam;
        $isSingleSection = $exam->sections->count() === 1;
        $sectionKey      = (string) $this->sectionId;

        if ($isSingleSection && $exam->duration_in_minutes) {
            if ($submission->started_at) {
                $examEndsAt = $submission->started_at->copy()->addMinutes($exam->duration_in_minutes);
                if (now()->greaterThanOrEqualTo($examEndsAt)) {
                    $this->performAutoSubmit(
                        $submission,
                        'Time limit exceeded (server-side auto-submit)',
                        $exam
                    );
                    return;
                }
            }
        } else {
            $sectionStartTimes = $submission->section_start_times ?? [];

            // Exam-level hard ceiling applies even in multi-section exams
            if ($exam->duration_in_minutes && $submission->started_at) {
                $examEndsAt = $submission->started_at->copy()->addMinutes((int) $exam->duration_in_minutes);
                if (now()->greaterThanOrEqualTo($examEndsAt)) {
                    $this->performAutoSubmit($submission, 'Exam duration exceeded (server-side auto-submit)', $exam);
                    return;
                }
            }

            if ($this->section->time_limit_minutes && isset($sectionStartTimes[$sectionKey])) {
                $startedAt = \Carbon\Carbon::createFromTimestamp($sectionStartTimes[$sectionKey]);
                $endsAt    = $startedAt->copy()->addMinutes((int) $this->section->time_limit_minutes);
                if (now()->greaterThanOrEqualTo($endsAt)) {
                    $this->performAutoSubmit(
                        $submission,
                        "Section '{$this->section->title}' time limit exceeded (server-side auto-submit)",
                        $exam
                    );
                    return;
                }
            }
        }

        $savedResponses = $submission->responses ?? [];
        $savedResponses[$key] = [
            'response' => $value,
            'answered_at' => now()->toIso8601String(),
        ];

        $submission->update(['responses' => $savedResponses]);

        // Emit event for JavaScript auto-save tracking
        $this->dispatch('responseUpdated', questionId: $key, response: $value);
    }

    /**
     * Called directly by the client-side timer when it counts down to zero.
     * Performs the same server-side validation and auto-submit as updatedResponses(),
     * but fires even when the candidate is idle (no response change in flight).
     */
    public function handleTimerExpired(): void
    {
        $submission      = $this->submission;
        $exam            = $this->exam;
        $isSingleSection = $exam->sections->count() === 1;
        $sectionKey      = (string) $this->sectionId;

        // Guard: already submitted — just redirect
        if ($submission->submitted_at) {
            $this->dispatch('examAutoSubmitted', [
                'autoSubmitted' => true,
                'reason'        => 'Exam already submitted.',
                'redirectUrl'   => route('examination-hub.take.completed', $exam),
            ]);
            return;
        }

        if ($isSingleSection && $exam->duration_in_minutes) {
            $examEndsAt = $submission->started_at
                ? $submission->started_at->copy()->addMinutes($exam->duration_in_minutes)
                : now()->subSecond(); // treat as expired when no start time
            if (now()->greaterThanOrEqualTo($examEndsAt)) {
                $this->performAutoSubmit(
                    $submission,
                    'Time limit exceeded (client timer, server-verified)',
                    $exam
                );
                return;
            }
        } elseif ($this->section->time_limit_minutes) {
            // Exam-level ceiling check first
            if ($exam->duration_in_minutes && $submission->started_at) {
                $examEndsAt = $submission->started_at->copy()->addMinutes((int) $exam->duration_in_minutes);
                if (now()->greaterThanOrEqualTo($examEndsAt)) {
                    $this->performAutoSubmit($submission, 'Exam duration exceeded (client timer, server-verified)', $exam);
                    return;
                }
            }

            $sectionStartTimes = $submission->section_start_times ?? [];
            if (isset($sectionStartTimes[$sectionKey])) {
                $startedAt = \Carbon\Carbon::createFromTimestamp($sectionStartTimes[$sectionKey]);
                $endsAt    = $startedAt->copy()->addMinutes((int) $this->section->time_limit_minutes);
                if (now()->greaterThanOrEqualTo($endsAt)) {
                    $this->performAutoSubmit(
                        $submission,
                        "Section '{$this->section->title}' time limit exceeded (client timer, server-verified)",
                        $exam
                    );
                    return;
                }
            }
        }

        // If the server clock disagrees (e.g. clock skew), do nothing —
        // the timer will re-fire on the next Livewire tick.
    }

    /**
     * Marks the submission as auto-submitted, kicks off grading, and dispatches
     * the examAutoSubmitted browser event with the redirect URL.
     *
     * Uses app() to resolve ExamGradingService so the Livewire component does not
     * need a declared $gradingService property (Livewire serialises all public/
     * protected properties between requests, which would break for service objects).
     */
    protected function performAutoSubmit(
        GeneralExamSubmission $submission,
        string $reason,
        \App\ExaminationHub\Models\GeneralExam $exam
    ): void {
        $submission->update([
            'submitted_at'       => now(),
            'time_taken_minutes' => (int) $submission->started_at?->diffInMinutes(now()),
            'status'             => GeneralExamSubmission::STATUS_SUBMITTED,
            'auto_submitted'     => true,
            'auto_submit_reason' => $reason,
        ]);

        // Resolve the grading service via the container — never store service
        // objects as component properties because Livewire will try to serialise them.
        app(\App\ExaminationHub\Services\ExamGradingService::class)->dispatchGrading($submission);

        $this->dispatch('examAutoSubmitted', [
            'autoSubmitted' => true,
            'reason'        => $reason,
            'redirectUrl'   => route('examination-hub.take.completed', $exam),
        ]);
    }

    public function toggleFlagQuestion(int $questionId): void
    {
        $submission = $this->submission;

        if ($submission->isFlagged($questionId)) {
            $submission->unflagQuestion($questionId);
            unset($this->flaggedQuestions[(string) $questionId]);
        } else {
            $submission->flagQuestion($questionId);
            $this->flaggedQuestions[(string) $questionId] = now()->toIso8601String();
        }
    }

    public function hydrate(): void
    {
        // Reload responses after Livewire hydration to prevent data loss
        $this->loadResponses();
    }

    protected function validateStartSection(): bool
    {
        $submission = $this->submission;

        // Check if submission exists
        if (!$submission) {
            $this->addError('general', 'No valid submission found.');
            return false;
        }

        // Check if exam has already been submitted
        if ($submission->submitted_at) {
            $this->addError('general', 'This exam has already been submitted.');
            return false;
        }

        // Check if section has already been started and completed
        $sectionProgress = $submission->section_progress ?? [];
        $sectionKey = (string) $this->sectionId;

        if (isset($sectionProgress[$sectionKey]) && $sectionProgress[$sectionKey] === 'completed') {
            $this->addError('general', 'This section has already been completed.');
            return false;
        }

        // Check time expiry — exam clock for single-section exams, section clock otherwise.
        $exam            = $this->exam;
        $isSingleSection = $exam->sections->count() === 1;
        $sectionStartTimes = $submission->section_start_times ?? [];

        if ($isSingleSection && $exam->duration_in_minutes) {
            if ($submission->started_at) {
                $examEndsAt = $submission->started_at->copy()->addMinutes($exam->duration_in_minutes);
                if (now()->greaterThanOrEqualTo($examEndsAt)) {
                    $this->addError('general', 'Exam time limit has expired.');
                    return false;
                }
            }
        } elseif ($this->section->time_limit_minutes && isset($sectionStartTimes[$sectionKey])) {
        } elseif ($exam->duration_in_minutes && $submission->started_at) {
            // Multi-section: always enforce exam-level ceiling
            $examEndsAt = $submission->started_at->copy()->addMinutes((int) $exam->duration_in_minutes);
            if (now()->greaterThanOrEqualTo($examEndsAt)) {
                $this->addError('general', 'Exam time limit has expired.');
                return false;
            }

            // Then check section-specific limit if set
            if ($this->section->time_limit_minutes && isset($sectionStartTimes[(string) $this->sectionId])) {
                $startedAt = \Carbon\Carbon::createFromTimestamp($sectionStartTimes[(string) $this->sectionId]);
                $endsAt    = $startedAt->copy()->addMinutes((int) $this->section->time_limit_minutes);
                if (now()->greaterThanOrEqualTo($endsAt)) {
                    $this->addError('general', 'Section time limit has expired.');
                    return false;
                }
            }
        }

        return true;
    }

    protected function getOrCreateSubmission(): ?GeneralExamSubmission
    {
        // This method should retrieve the current submission
        // Since it's a property, we can just return it
        return $this->submission;
    }

    protected function updateSectionProgress(string $status): void
    {
        $submission = $this->submission;
        $sectionProgress = $submission->section_progress ?? [];
        $sectionProgress[(string) $this->sectionId] = $status;

        // Update section start time if starting
        $sectionStartTimes = $submission->section_start_times ?? [];
        if ($status === 'started' && !isset($sectionStartTimes[(string) $this->sectionId])) {
            $sectionStartTimes[(string) $this->sectionId] = now()->timestamp;
        }

        $submission->update([
            'section_progress' => $sectionProgress,
            'section_start_times' => $sectionStartTimes,
        ]);
    }

    protected function resetViolations(): void
    {
        // Reset any violations for this section if needed
        $submission = $this->submission;
        $proctoringLogs = $submission->proctoring_logs ?? [];

        // Clear any pending violations for this section
        // Implementation depends on how violations are tracked
    }

    public function startSection(): void
    {
        if (!$this->validateStartSection()) {
            return;
        }

        // Create or get existing submission
        $this->submission = $this->getOrCreateSubmission();

        if (!$this->submission) {
            $this->addError('general', 'Unable to start exam. Please refresh the page and try again.');
            return;
        }

        // Record start time
        $this->start_time = now();

        // Mark section as started
        $this->updateSectionProgress('started');

        // Hide section info to show exam content
        $this->showSectionInfo = false;

        // Reset any previous violations for this section
        $this->resetViolations();

        // Emit event to show exam content
        $this->dispatch('section-started',
            sectionIndex: $this->sectionIndex,
            submissionId: $this->submission->id
        );

        // Also emit an event to show exam content regardless of fullscreen state
        $this->dispatch('show-exam-content');
    }

    public function isQuestionAnswered(int $questionId): bool
    {
        return ! empty($this->responses[$questionId]);
    }

    public function getAnsweredCount(): int
    {
        return count(array_filter($this->responses, fn ($r) => ! empty($r) && $r !== null));
    }

    public function toggleSectionInfo(): void
    {
        $this->showSectionInfo = ! $this->showSectionInfo;
    }

    public function render()
    {
        return view('livewire.examination-hub.exam-section-taking');
    }
}
