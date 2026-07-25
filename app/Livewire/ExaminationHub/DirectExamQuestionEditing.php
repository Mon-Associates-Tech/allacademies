<?php
// NEW_FILE_CODE
namespace App\Livewire\ExaminationHub;

use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamQuestion;
use App\ExaminationHub\Services\DirectExamQuestionEditingService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Direct Exam Question Editing
 * ─────────────────────────────
 * Allows administrators to:
 *   1. Select an exam from the list of available exams
 *   2. Review all questions in that exam (MCQ, T/F, etc.)
 *   3. Edit question text, option text and the correct answer directly
 *   4. Save all changes in a single action, which:
 *        a. Updates general_exam_questions directly (not source questions)
 *        b. Queues RegradeSubmissionJob for every affected submission
 *
 * FORM STATE
 * ──────────
 * Editing state is managed in Alpine.js on the client to avoid a network
 * round-trip on every keystroke.  When "Save Changes" is clicked, Alpine
 * serialises its state and calls $wire.applyChanges(data), which runs the
 * DirectExamQuestionEditingService.
 *
 * The component itself only needs to:
 *   • Serve the question data to Alpine via $this->questionData()
 *   • Receive the final changes map from Alpine and pass it to the service
 */
class DirectExamQuestionEditing extends Component
{
    // ─── Filters ─────────────────────────────────────────────────────────────

    #[Url(as: 'exam')]
    public ?int $examId = null;

    // ─── UI state ────────────────────────────────────────────────────────────

    public bool    $saving      = false;
    public ?string $saveMessage = null;
    public bool    $saveSuccess = false;

    // ─── Exams list (loaded once on mount) ───────────────────────────────────

    public array $exams = [];

    // ─── Lifecycle ────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->exams = GeneralExam::orderBy('title')
            ->get()
            ->map(fn ($exam) => ['id' => $exam->id, 'title' => $exam->title])
            ->all();
    }

    // ─── Computed ─────────────────────────────────────────────────────────────

    /**
     * Questions for the currently selected exam.
     *
     * Each entry contains everything the UI needs to render and edit one exam question:
     *   id          — primary key of general_exam_questions
     *   question    — current question text
     *   type        — question type (multiple_choice, true_false, essay, etc.)
     *   answer      — current correct answer (for auto-gradable questions)
     *   options     — array of options for MCQ/T&F questions
     *   marks       — marks allocated to the question
     *
     * @return array<int, array<string, mixed>>
     */
    #[Computed]
    public function questionData(): array
    {
        if (!$this->examId) {
            return [];
        }

        return GeneralExamQuestion::where('general_exam_id', $this->examId)
            ->orderBy('order')
            ->orderBy('id')
            ->get()
            ->map(function (GeneralExamQuestion $q) {
                $options = [];
                $count = 0;
                
                if ($q->isMultipleChoice() || $q->isTrueFalse()) {
                    // Format options for display
                    $optionsArray = $q->getOptionsForDisplay();
                    $count = count($optionsArray);
                    
                    // Ensure we have all option columns filled
                    foreach (['a', 'b', 'c', 'd', 'e'] as $letter) {
                        $optionKey = "option_{$letter}";
                        $options[$optionKey] = $optionsArray[strtoupper($letter)] ?? '';
                    }
                } else {
                    // For non-MCQ questions, set empty options
                    foreach (['a', 'b', 'c', 'd', 'e'] as $letter) {
                        $optionKey = "option_{$letter}";
                        $options[$optionKey] = '';
                    }
                }

                return array_merge([
                    'id'           => $q->id,
                    'question'     => $q->question ?? '',
                    'type'         => $q->type,
                    'answer'       => strtoupper($q->correct_answer ?? ''),
                    'marks'        => $q->marks,
                    'difficulty'   => $q->difficulty,
                    'option_count' => $count,
                    'is_edited'    => $q->is_edited,
                ], $options);
            })
            ->values()
            ->all();
    }

    // ─── Watchers ─────────────────────────────────────────────────────────────

    public function updatedExamId(): void
    {
        $this->saveMessage = null;
        unset($this->questionData); // recompute for new exam
    }

    // ─── Actions ──────────────────────────────────────────────────────────────

    /**
     * Receive the changes map from Alpine.js and apply them directly to exam questions.
     *
     * $changes shape (sent by Alpine):
     * {
     *   "<exam_question_id>": {
     *     "question": "Updated question text",  // optional question text edit
     *     "answer":   "C",                      // the corrected answer letter
     *     "option_a": "Revised text for A",     // optional option edits
     *     "option_b": "...",
     *     ...
     *   },
     *   ...
     * }
     *
     * Only entries that were actually modified are included (Alpine filters
     * unchanged questions before calling this method).
     */
    public function applyChanges(array $changes, DirectExamQuestionEditingService $service): void
    {
        if (empty($changes)) {
            $this->saveMessage = 'No changes detected.';
            $this->saveSuccess = true;
            return;
        }

        $this->saving = true;

        try {
            $result = $service->applyDirectChanges($changes);

            $this->saveSuccess = true;
            $this->saveMessage = sprintf(
                'Updated %d exam question(s) directly · %d submission(s) queued for regrading.',
                $result['exam_questions_updated'],
                $result['submissions_queued']
            );

            // Recompute question data so the view reflects the saved values
            unset($this->questionData);

            $this->dispatch('direct-exam-question-edited');
        } catch (\Exception $e) {
            $this->saveSuccess = false;
            $this->saveMessage = 'Error: ' . $e->getMessage();
        } finally {
            $this->saving = false;
        }
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        return view('livewire.examination-hub.direct-exam-question-editing');
    }
}