<?php

namespace App\Livewire\ExaminationHub;

use App\ExaminationHub\Services\AnswerKeyResolutionService;
use App\Models\AcademicSubject;
use App\Models\AcademicTopic;
use App\Models\MultipleChoiceQuestion;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Answer Key Resolution
 * ─────────────────────
 * Allows administrators to:
 *   1. Select a subject and topic from the question bank
 *   2. Review all MCQs for that topic with their current options and answer
 *   3. Edit option text and the correct answer
 *   4. Save all corrections in a single action, which:
 *        a. Updates multiple_choice_questions (source of truth)
 *        b. Syncs corrections into every matching general_exam_questions row
 *        c. Queues RegradeSubmissionJob for every affected submission
 *
 * FORM STATE
 * ──────────
 * Editing state is managed in Alpine.js on the client to avoid a network
 * round-trip on every keystroke.  When "Save Changes" is clicked, Alpine
 * serialises its state and calls $wire.applyChanges(data), which runs the
 * AnswerKeyResolutionService.
 *
 * The component itself only needs to:
 *   • Serve the question data to Alpine via $this->questionData()
 *   • Receive the final changes map from Alpine and pass it to the service
 */
class AnswerKeyResolution extends Component
{
    // ─── Filters ─────────────────────────────────────────────────────────────

    #[Url(as: 'subject')]
    public ?int $subjectId = null;

    #[Url(as: 'topic')]
    public ?int $topicId   = null;

    // ─── UI state ────────────────────────────────────────────────────────────

    public bool    $saving      = false;
    public ?string $saveMessage = null;
    public bool    $saveSuccess = false;

    // ─── Subjects list (loaded once on mount) ────────────────────────────────

    public array $subjects = [];

    // ─── Lifecycle ────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->subjects = AcademicSubject::with('academicLevel')
            ->orderBy('name')
            ->get()
            ->groupBy(fn ($s) => $s->academicLevel?->name ?? 'Uncategorised')
            ->map(fn ($group) => $group->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])->values()->all())
            ->all();
    }

    // ─── Computed ─────────────────────────────────────────────────────────────

    /**
     * Topics for the currently selected subject.
     *
     * @return array<int, array{id: int, name: string}>
     */
    #[Computed]
    public function topics(): array
    {
        if (! $this->subjectId) {
            return [];
        }

        return AcademicTopic::where('academic_subject_id', $this->subjectId)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($t) => ['id' => $t->id, 'name' => $t->name])
            ->all();
    }

    /**
     * Question data array consumed by the Alpine.js editor.
     *
     * Each entry contains everything the UI needs to render and edit one MCQ:
     *   id          — primary key of multiple_choice_questions
     *   question    — raw text (bypassing Mark cast for clean string comparison)
     *   answer      — current correct answer letter ('A'–'E')
     *   option_*    — raw text of each option (bypass Mark cast)
     *   option_count — number of non-empty options (controls how many rows show)
     *
     * We use getRawOriginal() to bypass the Mark cast, which may return an
     * object.  The raw DB value is always a plain string suitable for editing.
     *
     * @return array<int, array<string, mixed>>
     */
    #[Computed]
    public function questionData(): array
    {
        if (! $this->topicId) {
            return [];
        }

        return MultipleChoiceQuestion::where('academic_topic_id', $this->topicId)
            ->orderBy('id')
            ->get()
            ->map(function (MultipleChoiceQuestion $q) {
                $options = [];
                $count   = 0;
                foreach (['a', 'b', 'c', 'd', 'e'] as $letter) {
                    $decoded = json_decode($q->getRawOriginal("option_{$letter}") ?? '', true);
                    $up = is_array($decoded) ? ($decoded['up'] ?? '') : '';
                    $options["option_{$letter}"] = $up;
                    if ($up !== '') {
                        $count++;
                    }
                }

                $questionDecoded = json_decode($q->getRawOriginal('question') ?? '', true);

                return array_merge([
                    'id'           => $q->id,
                    'question'     => is_array($questionDecoded) ? ($questionDecoded['down'] ?? '') : '',
                    'answer'       => strtoupper($q->answer ?? 'A'),
                    'option_count' => $count,
                ], $options);
            })
            ->values()
            ->all();
    }

    // ─── Watchers ─────────────────────────────────────────────────────────────

    public function updatedSubjectId(): void
    {
        $this->topicId   = null;
        $this->saveMessage = null;
        unset($this->topics);       // clear computed cache
        unset($this->questionData);
    }

    public function updatedTopicId(): void
    {
        $this->saveMessage = null;
        unset($this->questionData); // recompute for new topic
    }

    // ─── Actions ──────────────────────────────────────────────────────────────

    /**
     * Receive the changes map from Alpine.js and apply them.
     *
     * $changes shape (sent by Alpine):
     * {
     *   "<mcq_id>": {
     *     "answer":   "C",
     *     "option_a": "Revised text for A",
     *     "option_b": "...",
     *     ...
     *   },
     *   ...
     * }
     *
     * Only entries that were actually modified are included (Alpine filters
     * unchanged questions before calling this method).
     */
    public function applyChanges(array $changes, AnswerKeyResolutionService $service): void
    {
        if (empty($changes)) {
            $this->saveMessage = 'No changes detected.';
            $this->saveSuccess = true;
            return;
        }

        $this->saving = true;

        try {
            $result = $service->applyChanges($changes);

            $this->saveSuccess = true;
            $this->saveMessage = sprintf(
                'Updated %d question(s) in the bank · %d exam question(s) synced · %d submission(s) queued for regrading.',
                $result['mcqs_updated'],
                $result['exam_questions_synced'],
                $result['submissions_queued']
            );

            // Recompute question data so the view reflects the saved values
            unset($this->questionData);

            $this->dispatch('answer-key-saved');
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
        return view('livewire.examination-hub.answer-key-resolution');
    }
}
