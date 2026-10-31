<?php

namespace App\ExaminationHub\Jobs;

use App\ExaminationHub\Models\GeneralExamSubmission;
use App\ExaminationHub\Services\ParityCheckService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * ParityCheckJob — re-grades a submission on central and checks parity.
 *
 * Dispatched after a submission is ingested from a center. The job
 * re-grades from raw answers using McqGrader and compares with the
 * center's score. Mismatches are flagged but never overwrite center scores.
 */
class ParityCheckJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected int $submissionId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(ParityCheckService $service): void
    {
        $submission = GeneralExamSubmission::find($this->submissionId);

        if (!$submission) {
            return;
        }

        // Only check parity for graded submissions
        if ($submission->score === null) {
            return;
        }

        $service->check($submission);
    }
}
