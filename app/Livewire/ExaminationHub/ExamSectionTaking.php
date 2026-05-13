<?php

namespace App\Livewire\ExaminationHub;

use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamSubmission;
use Illuminate\Support\Collection;
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
    public bool $showSectionInfo = true;
    public ?int $timeRemaining = null;

    public function mount(GeneralExam $exam, GeneralExamSubmission $submission, $section, int $sectionIndex, $questions): void
    {
        $this->examId = $exam->id;
        $this->submissionId = $submission->id;
        $this->sectionId = $section->id;
        $this->sectionIndex = $sectionIndex;
        
        // Load saved responses from fresh submission data
        $this->loadResponses();

        if ($exam->duration_in_minutes && $submission->started_at) {
            $elapsed = now()->diffInMinutes($submission->started_at);
            $this->timeRemaining = max(0, $exam->duration_in_minutes - $elapsed);
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
        return $this->section->questions()->orderBy('order')->get();
    }

    public function goToQuestion(int $index): void
    {
        if ($index >= 0 && $index < $this->questions->count()) {
            $this->currentQuestionIndex = $index;
        }
    }

    public function nextQuestion(): void
    {
        if ($this->currentQuestionIndex < $this->questions->count() - 1) {
            $this->currentQuestionIndex++;
        }
    }

    public function previousQuestion(): void
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        }
    }

    public function updatedResponses($value, $key): void
    {
        $submission = $this->submission;
        $savedResponses = $submission->responses ?? [];
        $savedResponses[$key] = [
            'response' => $value,
            'answered_at' => now()->toIso8601String(),
        ];
        
        $submission->update(['responses' => $savedResponses]);
    }

    public function hydrate(): void
    {
        // Reload responses after Livewire hydration to prevent data loss
        $this->loadResponses();
    }

    public function startSection(): void
    {
        $this->showSectionInfo = false;
    }

    public function isQuestionAnswered(int $questionId): bool
    {
        return !empty($this->responses[$questionId]);
    }

    public function getAnsweredCount(): int
    {
        return count(array_filter($this->responses, fn($r) => !empty($r) && $r !== null));
    }

    public function toggleSectionInfo(): void
    {
        $this->showSectionInfo = !$this->showSectionInfo;
    }

    public function render()
    {
        return view('livewire.examination-hub.exam-section-taking');
    }
}
