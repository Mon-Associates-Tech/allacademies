<?php

namespace App\Console\Commands\ExaminationHub;

use App\ExaminationHub\Models\GeneralExamQuestion;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\Jobs\ExaminationHub\SyncSourceQuestionJob;
use App\Models\AcademicSubject;
use App\Models\EssayQuestion;
use App\Models\MultipleChoiceQuestion;
use App\Models\TrueOrFalseQuestion;
use App\Support\Mark;
use Illuminate\Console\Command;

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
                            {--regrade   : Sync content + regrade submissions for all matched exam questions}
                            {--subject=  : Sync+regrade only questions belonging to this academic_subject ID}
                            {--chunk=200 : Number of source questions to load per batch}';

    protected $description = 'Back-fill source_question_id on existing exam questions (MCQ, T/F, Essay) and optionally regrade affected submissions.';

    public function handle(): int
    {
        $dryRun    = (bool) $this->option('dry-run');
        $regrade   = (bool) $this->option('regrade');
        $chunkSize = (int) $this->option('chunk');

        if ($subjectId = $this->option('subject')) {
            return $this->syncBySubject((int) $subjectId);
        }

        $this->info($dryRun ? '--- DRY RUN — no changes will be written ---' : 'Back-filling source_question_id...');

        $totalMatched   = 0;
        $totalUnmatched = 0;
        $affectedExamIds = [];

        foreach ($this->sourceTypes() as [$type, $modelClass]) {
            $this->line("Processing type: {$type}");

            $index = $this->buildIndex($modelClass, $chunkSize);
            $this->line('  Indexed ' . count($index) . ' entries.');

            [$matched, $unmatched, $examIds] = $this->backfillType($type, $index, $chunkSize, $dryRun);

            $totalMatched   += $matched;
            $totalUnmatched += $unmatched;
            $affectedExamIds = array_merge($affectedExamIds, $examIds);
        }

        $this->info('Done.');
        $this->table(
            ['Result', 'Count'],
            [
                ['Matched' . ($dryRun ? ' (dry)' : ''), $totalMatched],
                ['Unmatched (AI-generated or text diverged)', $totalUnmatched],
            ]
        );

        if ($totalUnmatched > 0) {
            $this->warn("{$totalUnmatched} unmatched rows are likely AI-generated — no source record expected.");
        }

        if ($regrade && ! $dryRun && ! empty($affectedExamIds)) {
            $this->dispatchSyncs();
        }

        return self::SUCCESS;
    }

    private function sourceTypes(): array
    {
        return [
            [GeneralExamQuestion::TYPE_MULTIPLE_CHOICE, MultipleChoiceQuestion::class],
            [GeneralExamQuestion::TYPE_TRUE_FALSE,      TrueOrFalseQuestion::class],
            [GeneralExamQuestion::TYPE_ESSAY,           EssayQuestion::class],
        ];
    }

    private function buildIndex(string $modelClass, int $chunkSize): array
    {
        $index = [];

        $modelClass::withTrashed()
            ->select('id', 'question')
            ->chunk($chunkSize, function ($rows) use (&$index) {
                foreach ($rows as $row) {
                    $raw  = $row->getRawOriginal('question') ?? '';
                    $mark = Mark::fromString($raw);
                    $text = $mark->down ?? $mark->up ?? '';
                    $norm = $this->normalise($text);
                    if ($norm !== '') {
                        $index[$norm] = $row->id;
                    }
                }
            });

        return $index;
    }

    private function backfillType(string $type, array $index, int $chunkSize, bool $dryRun): array
    {
        $matched   = 0;
        $unmatched = 0;
        $examIds   = [];

        GeneralExamQuestion::where('type', $type)
            ->whereNull('source_question_id')
            ->chunk($chunkSize, function ($rows) use ($index, $dryRun, &$matched, &$unmatched, &$examIds) {
                foreach ($rows as $examQ) {
                    $norm      = $this->normalise($examQ->question ?? '');
                    $sourceId  = $index[$norm] ?? null;

                    if ($sourceId) {
                        $matched++;
                        $examIds[] = $examQ->general_exam_id;
                        if (! $dryRun) {
                            $examQ->updateQuietly(['source_question_id' => $sourceId]);
                        } else {
                            $this->line("  [DRY] exam_question #{$examQ->id} → source #{$sourceId}");
                        }
                    } else {
                        $unmatched++;
                    }
                }
            });

        return [$matched, $unmatched, $examIds];
    }

    private function syncBySubject(int $subjectId): int
    {
        $subject = AcademicSubject::find($subjectId);

        if (! $subject) {
            $this->error("Academic subject #{$subjectId} not found.");
            return self::FAILURE;
        }

        $this->info("Syncing questions for subject: {$subject->name} (#{$subjectId})");

        $topicIds = $subject->academicTopics()->pluck('id');

        if ($topicIds->isEmpty()) {
            $this->warn('No topics found for this subject.');
            return self::SUCCESS;
        }

        $count = 0;

        // MCQ
        $mcqIds = MultipleChoiceQuestion::whereIn('academic_topic_id', $topicIds)
            ->whereIn('id', function ($q) {
                $q->select('source_question_id')
                    ->from('general_exam_questions')
                    ->where('type', GeneralExamQuestion::TYPE_MULTIPLE_CHOICE)
                    ->whereNotNull('source_question_id');
            })
            ->pluck('id');

        foreach ($mcqIds as $id) {
            SyncSourceQuestionJob::dispatch(GeneralExamQuestion::TYPE_MULTIPLE_CHOICE, $id);
            $count++;
        }

        // True/False
        $tfIds = TrueOrFalseQuestion::whereIn('academic_topic_id', $topicIds)
            ->whereIn('id', function ($q) {
                $q->select('source_question_id')
                    ->from('general_exam_questions')
                    ->where('type', GeneralExamQuestion::TYPE_TRUE_FALSE)
                    ->whereNotNull('source_question_id');
            })
            ->pluck('id');

        foreach ($tfIds as $id) {
            SyncSourceQuestionJob::dispatch(GeneralExamQuestion::TYPE_TRUE_FALSE, $id);
            $count++;
        }

        // Essay
        $essayIds = EssayQuestion::whereIn('academic_topic_id', $topicIds)
            ->whereIn('id', function ($q) {
                $q->select('source_question_id')
                    ->from('general_exam_questions')
                    ->where('type', GeneralExamQuestion::TYPE_ESSAY)
                    ->whereNotNull('source_question_id');
            })
            ->pluck('id');

        foreach ($essayIds as $id) {
            SyncSourceQuestionJob::dispatch(GeneralExamQuestion::TYPE_ESSAY, $id);
            $count++;
        }

        $this->info("Dispatched {$count} sync+regrade job(s) for subject #{$subjectId}.");

        return self::SUCCESS;
    }

    private function dispatchSyncs(): void
    {
        $this->line('Dispatching sync+regrade jobs for all matched exam questions...');

        $count = 0;

        foreach ($this->sourceTypes() as [$type, $modelClass]) {
            $sourceIds = GeneralExamQuestion::where('type', $type)
                ->whereNotNull('source_question_id')
                ->distinct()
                ->pluck('source_question_id');

            foreach ($sourceIds as $sourceId) {
                SyncSourceQuestionJob::dispatch($type, $sourceId);
                $count++;
            }
        }

        $this->info("Dispatched {$count} sync job(s). Each will update exam question content then regrade affected submissions.");
    }

    private function normalise(string $text): string
    {
        $stripped   = strip_tags($text);
        $decoded    = html_entity_decode($stripped, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $collapsed  = preg_replace('/\s+/', ' ', $decoded);
        return strtolower(trim($collapsed));
    }
}
