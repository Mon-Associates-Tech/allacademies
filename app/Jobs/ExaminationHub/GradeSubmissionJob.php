<?php

namespace App\Jobs\ExaminationHub;

use App\ExaminationHub\Models\GeneralExamSubmission;
use App\ExaminationHub\Services\ExamGradingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GradeSubmissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 10;

    public function __construct(public readonly int $submissionId) {}

    public function handle(ExamGradingService $gradingService): void
    {
        $submission = GeneralExamSubmission::find($this->submissionId);

        if (! $submission) {
            Log::warning("GradeSubmissionJob: Submission with ID {$this->submissionId} not found");
            return;
        }

        // Check if already graded to prevent duplicate processing
        if ($submission->status === GeneralExamSubmission::STATUS_AUTO_GRADED || 
            $submission->status === GeneralExamSubmission::STATUS_MANUALLY_REVIEWED ||
            $submission->status === GeneralExamSubmission::STATUS_FINAL) {
            Log::info("GradeSubmissionJob: Submission {$this->submissionId} already graded with status {$submission->status}");
            return;
        }

        if (! $submission->submitted_at) {
            Log::warning("GradeSubmissionJob: Attempting to grade unsubmitted submission {$this->submissionId}");
            return;
        }

        try {
            $gradingService->grade($submission);
            Log::info("GradeSubmissionJob: Successfully graded submission {$this->submissionId}");
        } catch (\Exception $e) {
            Log::error("GradeSubmissionJob: Failed to grade submission {$this->submissionId}: " . $e->getMessage());
            throw $e; // Re-throw to trigger retry/backoff
        }
    }
}