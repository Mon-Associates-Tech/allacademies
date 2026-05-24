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
            $startedAt = \Carbon\Carbon::createFromTimestamp($sectionStartTimes[$sectionKey]);
            $endsAt = $startedAt->copy()->addMinutes((int) $section->time_limit_minutes);
            $this->timeRemaining = max(0, now()->diffInSeconds($endsAt, false));
        } elseif ($exam->duration_in_minutes && $submission->started_at) {
            $examEndsAt = $submission->started_at->copy()->addMinutes((int) $exam->duration_in_minutes);
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
        // Prevent saving if section time expired
        $submission = $this->submission;
        $sectionStartTimes = $submission->section_start_times ?? [];
        $sectionKey = (string) $this->sectionId;
        if ($this->section->time_limit_minutes && isset($sectionStartTimes[$sectionKey])) {
            $startedAt = \Carbon\Carbon::createFromTimestamp($sectionStartTimes[$sectionKey]);
            $endsAt = $startedAt->copy()->addMinutes((int) $this->section->time_limit_minutes);
            if (now()->greaterThanOrEqualTo($endsAt)) {
                return; // ignore updates after expiry
            }
        }

        $savedResponses = $submission->responses ?? [];
        $savedResponses[$key] = [
            'response' => $value,
            'answered_at' => now()->toIso8601String(),
        ];

        $submission->update(['responses' => $savedResponses]);
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

    public function startSection(): void
    {
        $submission = $this->submission;
        $sectionStartTimes = $submission->section_start_times ?? [];
        $sectionKey = (string) $this->sectionId;

        // Only set section start time when participant explicitly begins
        if (! isset($sectionStartTimes[$sectionKey]) && $this->section->time_limit_minutes) {
            $sectionStartTimes[$sectionKey] = now()->timestamp;
            $submission->update(['section_start_times' => $sectionStartTimes]);

            // Compute time remaining for this section
            $this->timeRemaining = $this->section->time_limit_minutes * 60;
        } elseif (! $this->section->time_limit_minutes) {
            // Fallback to exam-wide remaining time
            $remaining = $submission->getRemainingTime();
            $this->timeRemaining = $remaining ?? ($this->exam->duration_in_minutes * 60);
        }

        $this->showSectionInfo = false;
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
