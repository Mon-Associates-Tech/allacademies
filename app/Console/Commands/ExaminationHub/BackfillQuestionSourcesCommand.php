<?php

namespace App\Console\Commands\ExaminationHub;

use App\ExaminationHub\Models\GeneralExamQuestion;
use App\Models\MultipleChoiceQuestion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Best-effort back-fill of source_question_id on existing general_exam_questions
 * rows that pre-date the column.
 *
 * MATCHING STRATEGY
 * ─────────────────
 * For each MCQ exam question without a source_question_id we compare its
 * stored question text (after strip_tags + lowercase + trim) against every
 * multiple_choice_questions row in the DB.  This is a one-time operation;
 * once the column is populated the Answer Key Resolution page uses direct
 * ID lookups instead.
 *
 * PERFORMANCE NOTE
 * ────────────────
 * The command loads MCQs in chunks and indexes them in memory by normalised
 * text to avoid O(n²) queries.  For very large question banks (>50k rows)
 * you may prefer to run it with --chunk=500.
 *
 * USAGE
 * ─────
 *   php artisan exam:backfill-question-sources
 *   php artisan exam:backfill-question-sources --dry-run
 *   php artisan exam:backfill-question-sources --chunk=500
 */
class BackfillQuestionSourcesCommand extends Command
{
    protected $signature = 'exam:backfill-question-sources
                            {--dry-run   : Show what would be updated without writing to DB}
                            {--chunk=200 : Number of MCQs to load per batch}';

    protected $description = 'Back-fill source_question_id on existing exam questions by matching question text to the MCQ bank.';

    public function handle(): int
    {
        $dryRun    = (bool) $this->option('dry-run');
        $chunkSize = (int) $this->option('chunk');

        $this->info($dryRun ? '--- DRY RUN — no changes will be written ---' : 'Back-filling source_question_id...');

        // ── Build in-memory index: normalised_text => mcq_id ────────────────
        $this->line('Building MCQ text index…');

        $index = []; // [ normalised_text => mcq_id ]

        MultipleChoiceQuestion::withTrashed()
            ->select('id', 'question')
            ->chunk($chunkSize, function ($mcqs) use (&$index) {
                foreach ($mcqs as $mcq) {
                    $raw  = $mcq->getRawOriginal('question') ?? '';
                    $norm = $this->normalise($raw);
                    if ($norm !== '') {
                        $index[$norm] = $mcq->id;
                    }
                }
            });

        $this->line('Indexed ' . count($index) . ' MCQ entries.');

        // ── Walk exam questions without a source_question_id ─────────────────
        $matched   = 0;
        $unmatched = 0;

        GeneralExamQuestion::where('type', GeneralExamQuestion::TYPE_MULTIPLE_CHOICE)
            ->whereNull('source_question_id')
            ->chunk($chunkSize, function ($examQuestions) use ($index, $dryRun, &$matched, &$unmatched) {
                foreach ($examQuestions as $examQ) {
                    $norm    = $this->normalise($examQ->question ?? '');
                    $mcqId   = $index[$norm] ?? null;

                    if ($mcqId) {
                        $matched++;
                        if (! $dryRun) {
                            $examQ->update(['source_question_id' => $mcqId]);
                        } else {
                            $this->line("  [DRY] exam_question #{$examQ->id} → mcq #{$mcqId}");
                        }
                    } else {
                        $unmatched++;
                    }
                }
            });

        $this->info("Done.");
        $this->table(
            ['Result', 'Count'],
            [
                ['Matched'   . ($dryRun ? ' (dry)' : ''), $matched],
                ['Unmatched (AI-generated or text diverged)', $unmatched],
            ]
        );

        if ($unmatched > 0) {
            $this->warn("The {$unmatched} unmatched rows are likely AI-generated questions — they do not have a source MCQ and that is expected.");
        }

        return self::SUCCESS;
    }

    private function normalise(string $text): string
    {
        $stripped   = strip_tags($text);
        $decoded    = html_entity_decode($stripped, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $collapsed  = preg_replace('/\s+/', ' ', $decoded);
        return strtolower(trim($collapsed));
    }
}
