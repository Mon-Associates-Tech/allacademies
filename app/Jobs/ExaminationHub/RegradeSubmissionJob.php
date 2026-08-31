<?php

namespace App\Jobs\ExaminationHub;

use App\ExaminationHub\Models\GeneralExamSubmission;
use App\Services\GeneralExam\GeneralExamGradingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Re-grades a submission unconditionally — unlike GradeSubmissionJob, it does
 * NOT skip submissions that are already in AUTO_GRADED or MANUALLY_REVIEWED
 * status.
 *
 * Used after:
 *   • Answer-key corrections via the Answer Key Resolution page
 *   • Bulk normalization when target_total_marks is configured on an exam
 *
 * IMPORTANT: STATUS_FINAL submissions are skipped to preserve human sign-off.
 * To include them, dispatch with $includeFinal = true.
 */
class RegradeSubmissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 10;

    public function __construct(
        public readonly int  $submissionId,
        public readonly bool $includeFinal = false
    ) {}

    public function handle(GeneralExamGradingService $gradingService): void
    {
        $submission = GeneralExamSubmission::with('assignment')->find($this->submissionId);

        if (! $submission) {
            Log::warning("RegradeSubmissionJob: submission {$this->submissionId} not found");
            return;
        }

        if (! $submission->submitted_at) {
            Log::warning("RegradeSubmissionJob: submission {$this->submissionId} has no submitted_at — skipping");
            return;
        }

        // Respect the finalized status unless explicitly forced
        if (! $this->includeFinal && $submission->status === GeneralExamSubmission::STATUS_FINAL) {
            Log::info("RegradeSubmissionJob: submission {$this->submissionId} is FINAL — skipping (dispatch with includeFinal=true to override)");
            return;
        }

        try {
            $gradingService->gradeSubmission($submission);
            Log::info("RegradeSubmissionJob: successfully regraded submission {$this->submissionId}");
        } catch (\Exception $e) {
            Log::error("RegradeSubmissionJob: failed for submission {$this->submissionId}: {$e->getMessage()}");
            throw $e;
        }
    }
}
