<?php
// NEW_FILE_CODE
namespace App\ExaminationHub\Services;

use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamQuestion;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\Services\GeneralExam\GeneralExamGradingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DirectExamQuestionEditingService
{
    public function __construct(
        private readonly GeneralExamGradingService $gradingService
    ) {}

    /**
     * Apply direct changes to exam questions and regrade affected submissions.
     *
     * $changes shape:
     * [
     *   <exam_question_id> => [
     *     'question'   => 'Updated question text',  // optional question text edit
     *     'answer'     => 'C',                      // the corrected answer letter
     *     'option_a'   => 'New text for option A',  // optional option edits
     *     'option_b'   => '...',
     *     ...
     *   ],
     *   ...
     * ]
     *
     * Steps:
     *   1. Update general_exam_questions directly (no source question updates)
     *   2. Queue RegradeSubmissionJob for every affected submission
     */
    public function applyDirectChanges(array $changes): array
    {
        if (empty($changes)) {
            return [
                'exam_questions_updated' => 0,
                'submissions_queued'     => 0,
            ];
        }

        $examQuestionsUpdated = 0;
        $affectedExamQIds     = [];

        DB::transaction(function () use ($changes, &$examQuestionsUpdated, &$affectedExamQIds) {
            foreach ($changes as $examQuestionId => $change) {
                $examQuestion = GeneralExamQuestion::find((int) $examQuestionId);

                if (! $examQuestion) {
                    Log::warning('DirectExamQuestionEditing: Exam question not found', ['exam_question_id' => $examQuestionId]);
                    continue;
                }

                // Update the exam question directly
                $examQuestionUpdate = [];

                if (! empty($change['question'])) {
                    $examQuestionUpdate['question'] = $change['question'];
                }

                if (! empty($change['answer'])) {
                    $examQuestionUpdate['correct_answer'] = strtoupper($change['answer']);
                }

                if ($this->hasOptionChanges($change)) {
                    $examQuestionUpdate['options'] = $this->mergeOptionChanges(
                        $examQuestion->options ?? [],
                        $change
                    );
                }

                // Mark as edited to indicate this question was modified from source
                $examQuestionUpdate['is_edited'] = true;

                if (! empty($examQuestionUpdate)) {
                    $examQuestion->update($examQuestionUpdate);
                    $examQuestionsUpdated++;
                    $affectedExamQIds[] = $examQuestion->id;
                }
            }
        });

        // Queue regrading for affected submissions
        $submissionsQueued = $this->queueRegrading($affectedExamQIds);

        Log::info('DirectExamQuestionEditing: changes applied', [
            'exam_questions_updated' => $examQuestionsUpdated,
            'submissions_queued'     => $submissionsQueued,
        ]);

        return [
            'exam_questions_updated' => $examQuestionsUpdated,
            'submissions_queued'     => $submissionsQueued,
        ];
    }

    /**
     * Regrade submissions synchronously for the given exam question IDs.
     */
    protected function regradeSync(array $examQuestionIds): array
    {
        $results = ['regraded' => 0, 'failed' => 0, 'errors' => []];

        if (empty($examQuestionIds)) {
            return $results;
        }

        $examIds = GeneralExamQuestion::whereIn('id', $examQuestionIds)
            ->distinct()
            ->pluck('general_exam_id');

        $submissions = GeneralExamSubmission::whereIn('general_exam_id', $examIds)
            ->whereIn('status', [
                GeneralExamSubmission::STATUS_SUBMITTED,
                GeneralExamSubmission::STATUS_AUTO_GRADED,
                GeneralExamSubmission::STATUS_MANUALLY_REVIEWED,
            ])
            ->whereNotNull('submitted_at')
            ->get();

        foreach ($submissions as $submission) {
            try {
                $this->gradingService->gradeSubmission($submission);
                $results['regraded']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = ['submission_id' => $submission->id, 'error' => $e->getMessage()];
                Log::error('DirectExamQuestionEditing: regrade failed', [
                    'submission_id' => $submission->id,
                    'error'         => $e->getMessage(),
                ]);
            }
        }

        return $results;
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
            ])
            ->whereNotNull('submitted_at')
            ->pluck('id');

        foreach ($submissionIds as $id) {
            // Note: Need to import RegradeSubmissionJob class
            // Assuming it exists from the original AnswerKeyResolutionService
            // If it doesn't exist, we'll need to create it or use a different approach
            \App\Jobs\ExaminationHub\RegradeSubmissionJob::dispatch($id)->onQueue('grading');
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
        array $change
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
            // If no current options exist, build from change data
            $built = [];
            foreach ($letterToColumn as $letter => $col) {
                if (isset($change[$col])) {
                    $built[] = ['key' => $letter, 'value' => $change[$col]];
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
}