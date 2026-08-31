<?php

namespace App\Console\Commands\ExaminationHub;

use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\Jobs\ExaminationHub\RegradeSubmissionJob;
use Illuminate\Console\Command;

/**
 * Retroactively re-grades submissions so that scores are calculated against
 * the exam's `target_total_marks` (e.g. 100) instead of the raw question sum.
 *
 * Run this AFTER:
 *   1. Running the migration that adds target_total_marks
 *   2. Setting target_total_marks on the affected exam(s)
 *
 * USAGE
 * ─────
 *   # Regrade all exams that have target_total_marks set
 *   php artisan exam:regrade-normalize
 *
 *   # Regrade a single exam
 *   php artisan exam:regrade-normalize --exam=42
 *
 *   # Also regrade FINAL submissions (overrides human sign-off)
 *   php artisan exam:regrade-normalize --include-final
 *
 *   # Preview without dispatching jobs
 *   php artisan exam:regrade-normalize --dry-run
 */
class RegradeNormalizeCommand extends Command
{
    protected $signature = 'exam:regrade-normalize
                            {--exam=       : Only regrade submissions for this exam ID}
                            {--include-final : Also regrade STATUS_FINAL submissions}
                            {--dry-run      : Show what would be queued without dispatching jobs}';

    protected $description = 'Queue RegradeSubmissionJob for all graded submissions so scores are normalised against target_total_marks.';

    public function handle(): int
    {
        $examId        = $this->option('exam') ? (int) $this->option('exam') : null;
        $includeFinal  = (bool) $this->option('include-final');
        $dryRun        = (bool) $this->option('dry-run');

        $this->info($dryRun ? '--- DRY RUN ---' : 'Queuing regrade jobs…');

        // ── Build exam query ─────────────────────────────────────────────────
        $examQuery = GeneralExam::query();// whereNotNull('target_total_marks')
                                //->where('target_total_marks', '>', 0);

        if ($examId) {
            $examQuery->where('id', $examId);
        }

        $exams = $examQuery->get();

        if ($exams->isEmpty()) {
            $this->warn('No exams found with target_total_marks set. Set the field first, then re-run.');
            return self::SUCCESS;
        }

        // ── Collect eligible submissions ─────────────────────────────────────
        $statuses = [
            GeneralExamSubmission::STATUS_SUBMITTED,
            GeneralExamSubmission::STATUS_AUTO_GRADED,
            GeneralExamSubmission::STATUS_MANUALLY_REVIEWED,
        ];

        if ($includeFinal) {
            $statuses[] = GeneralExamSubmission::STATUS_FINAL;
        }

        $queued = 0;

        foreach ($exams as $exam) {
            $submissions = $exam->submissions()
                ->whereIn('status', $statuses)
                ->whereNotNull('submitted_at')
                ->get();

            $this->line("Exam #{$exam->id} «{$exam->title}» — target: {$exam->target_total_marks} — submissions: {$submissions->count()}");

            foreach ($submissions as $submission) {
                if ($dryRun) {
                    $this->line("  [DRY] Would regrade submission #{$submission->id} (status: {$submission->status})");
                } else {
                    RegradeSubmissionJob::dispatch($submission->id, $includeFinal)
                                        ->onQueue('grading');
                }
                $queued++;
            }
        }

        if ($dryRun) {
            $this->info("Dry run complete — {$queued} submissions would be queued.");
        } else {
            $this->info("Done — {$queued} regrade jobs dispatched to the 'grading' queue.");
            $this->line("Run your queue worker:  php artisan queue:work --queue=grading");
        }

        return self::SUCCESS;
    }
}
