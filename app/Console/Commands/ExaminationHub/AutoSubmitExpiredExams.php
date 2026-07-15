<?php

namespace App\Console\Commands\ExaminationHub;

use App\ExaminationHub\Models\ExamParticipantHeartbeat;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\ExaminationHub\Services\ExamGradingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoSubmitExpiredExams extends Command
{
    protected $signature = 'examination-hub:auto-submit-expired';

    protected $description = 'Auto-submit in-progress exam submissions that have exceeded their time limit.';

    public function handle(ExamGradingService $gradingService): int
    {
        // Find exams with a duration that are still active (not ended globally)
        $exams = GeneralExam::query()
            ->whereNotNull('duration_in_minutes')
            ->where('status', 'published')
            ->get();

        $submitted = 0;

        foreach ($exams as $exam) {
            // Find in-progress submissions where started_at + duration < now
            $expired = GeneralExamSubmission::query()
                ->where('general_exam_id', $exam->id)
                ->where('status', GeneralExamSubmission::STATUS_IN_PROGRESS)
                ->whereNull('submitted_at')
                ->whereNotNull('started_at')
                ->whereRaw(
                    'DATE_ADD(started_at, INTERVAL ? MINUTE) < NOW()',
                    [$exam->duration_in_minutes]
                )
                ->get();

            foreach ($expired as $submission) {
                DB::transaction(function () use ($submission, $gradingService) {
                    $timeTaken = $submission->started_at
                        ? (int) $submission->started_at->diffInMinutes(now())
                        : 0;

                    $submission->update([
                        'submitted_at' => now(),
                        'time_taken_minutes' => $timeTaken,
                        'status' => GeneralExamSubmission::STATUS_SUBMITTED,
                        'auto_submitted' => true,
                        'auto_submit_reason' => 'Time limit exceeded (server-side auto-submit)',
                    ]);

                    ExamParticipantHeartbeat::where('general_exam_submission_id', $submission->id)
                        ->whereNotIn('status', [
                            ExamParticipantHeartbeat::STATUS_COMPLETED,
                            ExamParticipantHeartbeat::STATUS_TERMINATED,
                        ])
                        ->update(['status' => ExamParticipantHeartbeat::STATUS_COMPLETED]);

                    $gradingService->dispatchGrading($submission);
                });

                $submitted++;
                $this->line("Auto-submitted: submission #{$submission->id} (exam: {$submission->general_exam_id})");
            }
        }

        $this->info("Done. {$submitted} submission(s) auto-submitted.");

        return self::SUCCESS;
    }
}
