<?php

namespace App\ExaminationHub\Services;

use App\ExaminationHub\Models\GeneralExamQuestion;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\Jobs\ExaminationHub\RegradeSubmissionJob;
use App\Models\MultipleChoiceQuestion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnswerKeyResolutionService
{
    /**
     * Apply answer-key corrections and trigger regrading.
     *
     * $changes shape:
     * [
     *   <mcq_id> => [
     *     'answer'   => 'C',                     // the corrected answer letter
     *     'option_a' => 'New text for option A',  // optional option edits
     *     'option_b' => '...',
     *     ...
     *   ],
     *   ...
     * ]
     *
     * Steps:
     *   1. Update multiple_choice_questions (source of truth in the bank)
     *   2. Sync corrections into every matching general_exam_questions row
     *   3. Queue RegradeSubmissionJob for every affected submission
     */
    public function applyChanges(array $changes): array
    {
        if (empty($changes)) {
            return [
                'mcqs_updated'          => 0,
                'exam_questions_synced' => 0,
                'submissions_queued'    => 0,
            ];
        }

        $mcqsUpdated         = 0;
        $affectedExamQIds    = [];

        DB::transaction(function () use ($changes, &$mcqsUpdated, &$affectedExamQIds) {
            foreach ($changes as $mcqId => $change) {
                $mcq = MultipleChoiceQuestion::find((int) $mcqId);

                if (! $mcq) {
                    Log::warning('AnswerKeyResolution: MCQ not found', ['mcq_id' => $mcqId]);
                    continue;
                }

                // ── 1. Update the question bank ─────────────────────────────
                $mcqUpdate = [];

                if (! empty($change['answer'])) {
                    $mcqUpdate['answer'] = strtoupper($change['answer']);
                }

                foreach (['option_a', 'option_b', 'option_c', 'option_d', 'option_e'] as $col) {
                    if (isset($change[$col])) {
                        // $change[$col] is the plain 'up' string from the editor.
                        // Wrap it back into Mark JSON so the cast stores both keys.
                        $up = $change[$col];
                        $mcqUpdate[$col] = json_encode(['up' => $up, 'down' => $up]);
                    }
                }

                if (! empty($mcqUpdate)) {
                    $mcq->update($mcqUpdate);
                    $mcqsUpdated++;
                }

                // ── 2. Sync into live exam questions ────────────────────────
                $synced = $this->syncToExamQuestions($mcq, $change);
                foreach ($synced as $id) {
                    $affectedExamQIds[] = $id;
                }
            }
        });

        $affectedExamQIds = array_unique($affectedExamQIds);

        // ── 3. Queue regrading ──────────────────────────────────────────────
        $submissionsQueued = $this->queueRegrading($affectedExamQIds);

        Log::info('AnswerKeyResolution: changes applied', [
            'mcqs_updated'          => $mcqsUpdated,
            'exam_questions_synced' => count($affectedExamQIds),
            'submissions_queued'    => $submissionsQueued,
        ]);

        return [
            'mcqs_updated'          => $mcqsUpdated,
            'exam_questions_synced' => count($affectedExamQIds),
            'submissions_queued'    => $submissionsQueued,
        ];
    }

    /**
     * Find every GeneralExamQuestion that corresponds to the given MCQ and
     * update its correct_answer (and options if changed).
     *
     * Lookup strategy (in order):
     *   1. source_question_id match  — exact, fast, preferred
     *   2. Question-text match       — normalised LOWER(TRIM(question)) for rows
     *      that pre-date the source_question_id column.  While doing so, we
     *      back-fill source_question_id so future lookups use path 1.
     *
     * Returns the IDs of updated GeneralExamQuestion rows.
     */
    protected function syncToExamQuestions(MultipleChoiceQuestion $mcq, array $change): array
    {
        // Use getRawOriginal so we compare against the actual DB string, not
        // whatever the Mark cast returns as an object.
        $rawQuestionText = $mcq->getRawOriginal('question');
        $normalised      = $this->normaliseText($rawQuestionText);

        $rows = GeneralExamQuestion::where('type', GeneralExamQuestion::TYPE_MULTIPLE_CHOICE)
            ->where(function ($q) use ($mcq, $normalised) {
                $q->where('source_question_id', $mcq->id)
                  ->orWhereRaw('LOWER(TRIM(question)) = ?', [$normalised]);
            })
            ->get();

        if ($rows->isEmpty()) {
            Log::info('AnswerKeyResolution: no matching exam questions found', [
                'mcq_id'     => $mcq->id,
                'normalised' => substr($normalised, 0, 80),
            ]);
            return [];
        }

        $updatedIds = [];

        foreach ($rows as $examQ) {
            $update = [];

            if (! empty($change['answer'])) {
                $update['correct_answer'] = strtoupper($change['answer']);
            }

            // Sync option text when the admin corrected one or more options
            if ($this->hasOptionChanges($change)) {
                $update['options'] = $this->mergeOptionChanges(
                    $examQ->options ?? [],
                    $change,
                    $mcq
                );
            }

            // Back-fill source_question_id on rows that pre-date the column
            if ($examQ->source_question_id === null) {
                $update['source_question_id'] = $mcq->id;
            }

            if (! empty($update)) {
                $examQ->update($update);
            }

            $updatedIds[] = $examQ->id;
        }

        return $updatedIds;
    }

    /**
     * Queue RegradeSubmissionJob for all submitted/graded submissions that
     * belong to exams containing any of the given GeneralExamQuestion IDs.
     */
    protected function queueRegrading(array $examQuestionIds): int
    {
        if (empty($examQuestionIds)) {
            return 0;
        }

        $examIds = GeneralExamQuestion::whereIn('id', $examQuestionIds)
            ->distinct()
            ->pluck('general_exam_id');

        $submissionIds = GeneralExamSubmission::whereIn('general_exam_id', $examIds)
            ->whereIn('status', [
                GeneralExamSubmission::STATUS_SUBMITTED,
                GeneralExamSubmission::STATUS_AUTO_GRADED,
                GeneralExamSubmission::STATUS_MANUALLY_REVIEWED,
                // Note: STATUS_FINAL is intentionally excluded.
                // Finalised submissions have been signed off by a grader —
                // automatically regrading them would override human decisions.
                // To regrade FINAL submissions, run: exam:regrade --include-final
            ])
            ->whereNotNull('submitted_at')
            ->pluck('id');

        foreach ($submissionIds as $id) {
            RegradeSubmissionJob::dispatch($id)->onQueue('grading');
        }

        return $submissionIds->count();
    }

    // ─── Option helpers ───────────────────────────────────────────────────────

    protected function hasOptionChanges(array $change): bool
    {
        foreach (['option_a', 'option_b', 'option_c', 'option_d', 'option_e'] as $col) {
            if (isset($change[$col])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Merge admin-corrected option text into the [{key:'A', value:'...'}] JSON
     * format used by GeneralExamQuestion.
     */
    protected function mergeOptionChanges(
        array $currentOptions,
        array $change,
        MultipleChoiceQuestion $mcq
    ): array {
        // Map letter → column: 'A' => 'option_a', etc.
        $letterToColumn = [
            'A' => 'option_a',
            'B' => 'option_b',
            'C' => 'option_c',
            'D' => 'option_d',
            'E' => 'option_e',
        ];

        if (empty($currentOptions)) {
            $built = [];
            foreach ($letterToColumn as $letter => $col) {
                $decoded = json_decode($mcq->getRawOriginal($col) ?? '', true);
                $up = is_array($decoded) ? ($decoded['up'] ?? '') : '';
                if ($up !== '') {
                    $built[] = ['key' => $letter, 'value' => $up];
                }
            }
            return $built;
        }

        // Patch in only the changed options, leave others as-is.
        return array_map(function (array $opt) use ($change, $letterToColumn) {
            $letter = $opt['key'] ?? '';
            $col    = $letterToColumn[$letter] ?? null;

            if ($col && isset($change[$col])) {
                $opt['value'] = $change[$col];
            }

            return $opt;
        }, $currentOptions);
    }

    // ─── Text normalisation ───────────────────────────────────────────────────

    /**
     * Strip HTML, collapse whitespace, lowercase — for fuzzy question matching.
     * We cannot use a Laravel cast here because we are comparing raw DB values.
     */
    protected function normaliseText(string $text): string
    {
        $stripped    = strip_tags($text);
        $noEntities  = html_entity_decode($stripped, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $collapsed   = preg_replace('/\s+/', ' ', $noEntities);
        return strtolower(trim($collapsed));
    }
}
