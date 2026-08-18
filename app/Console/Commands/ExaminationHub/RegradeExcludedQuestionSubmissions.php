<?php

namespace App\Console\Commands\ExaminationHub;

use App\ExaminationHub\Models\GeneralExamQuestion;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\Services\GeneralExam\GeneralExamGradingService;
use Illuminate\Console\Command;

class RegradeExcludedQuestionSubmissions extends Command
{
    protected $signature = 'exam:regrade-excluded
                            {--exam= : Regrade a specific exam ID only}
                            {--include-final : Also regrade STATUS_FINAL submissions}';

    protected $description = 'Regrade all submissions for exams that have excluded questions';

    public function handle(GeneralExamGradingService $gradingService): int
    {
        $examIds = GeneralExamQuestion::where('excluded_from_grading', true)
            ->distinct()
            ->pluck('general_exam_id');

        if ($onlyExam = $this->option('exam')) {
            $examIds = $examIds->filter(fn ($id) => $id == $onlyExam)->values();
        }

        if ($examIds->isEmpty()) {
            $this->info('No exams with excluded questions found.');
            return self::SUCCESS;
        }

        $statuses = [
            GeneralExamSubmission::STATUS_SUBMITTED,
            GeneralExamSubmission::STATUS_AUTO_GRADED,
            GeneralExamSubmission::STATUS_MANUALLY_REVIEWED,
        ];

        if ($this->option('include-final')) {
            $statuses[] = GeneralExamSubmission::STATUS_FINAL;
        }

        $submissions = GeneralExamSubmission::whereIn('general_exam_id', $examIds)
            ->whereIn('status', $statuses)
            ->whereNotNull('submitted_at')
            ->get();

        if ($submissions->isEmpty()) {
            $this->info('No submissions to regrade.');
            return self::SUCCESS;
        }

        $this->info("Regrading {$submissions->count()} submission(s) across exam(s): " . $examIds->join(', '));

        $bar = $this->output->createProgressBar($submissions->count());
        $bar->start();

        $failed = 0;

        foreach ($submissions as $submission) {
            try {
                $gradingService->gradeSubmission($submission);
            } catch (\Exception $e) {
                $failed++;
                $this->newLine();
                $this->warn("Failed submission {$submission->id}: {$e->getMessage()}");
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $succeeded = $submissions->count() - $failed;
        $this->info("Done. {$succeeded} regraded, {$failed} failed.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
