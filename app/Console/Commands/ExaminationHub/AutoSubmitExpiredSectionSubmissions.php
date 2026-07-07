<?php

namespace App\Console\Commands\ExaminationHub;

use App\ExaminationHub\Models\ExamParticipantHeartbeat;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\ExaminationHub\Services\ExamGradingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoSubmitExpiredSectionSubmissions extends Command
{
    protected $signature = 'examination-hub:auto-submit-expired-sections';

    protected $description = 'Auto-submit in-progress exam submissions that have exceeded their section time limits.';

    public function handle(ExamGradingService $gradingService): int
    {
        // Find submissions that have section time limits and have exceeded them
        $submissions = GeneralExamSubmission::query()
            ->where('status', GeneralExamSubmission::STATUS_IN_PROGRESS)
            ->whereNull('submitted_at')
            ->whereNotNull('section_start_times') // Only submissions that have started sections
            ->get();

        $submitted = 0;

        foreach ($submissions as $submission) {
           /* @var GeneralExam $exam */
            $exam = $submission->exam;

            if (!$exam) {
                continue;
            }
            $exam->load(['sections' => fn ($q) => $q->orderBy('order')]);

            $sectionStartTimes = $submission->section_start_times ?? [];
            $needsAutoSubmit = false;
            $autoSubmitReason = '';

            foreach ($exam->sections as $section) {
                if ($section->time_limit_minutes) {
                    $sectionKey = (string) $section->id;
                    $startedAt = $sectionStartTimes[$sectionKey] ?? null;

                    if ($startedAt) {
                        // Use getTotalAllowedSeconds() for exam-level extra time.
                        // Section time limits don't have their own extra_time field,
                        // so we honour the exam-level extension as a ceiling.
                        $totalAllowed = $submission->getTotalAllowedSeconds();
                        $examEndsAt   = $submission->started_at
                            ? $submission->started_at->timestamp + $totalAllowed
                            : null;

                        $sectionEndTime = $startedAt + ($section->time_limit_minutes * 60);
                        // Clamp section end to exam-level ceiling if set
                        if ($examEndsAt !== null) {
                            $sectionEndTime = min($sectionEndTime, $examEndsAt);
                        }

                        if (now()->timestamp >= $sectionEndTime) {
                            $needsAutoSubmit = true;
                            $autoSubmitReason = "Section '{$section->title}' time limit exceeded (server-side auto-submit)";
                            break;
                        }
                    }
                }
            }

            if ($needsAutoSubmit) {
                DB::transaction(function () use ($submission, $gradingService, $autoSubmitReason) {
                    $timeTaken = $submission->started_at
                        ? (int) $submission->started_at->diffInMinutes(now())
                        : 0;

                    $submission->update([
                        'submitted_at' => now(),
                        'time_taken_minutes' => $timeTaken,
                        'status' => GeneralExamSubmission::STATUS_SUBMITTED,
                        'auto_submitted' => true,
                        'auto_submit_reason' => $autoSubmitReason,
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
                $this->line("Auto-submitted: submission #{$submission->id} (exam: {$submission->general_exam_id}) - {$autoSubmitReason}");
            }
        }

        $this->info("Done. {$submitted} submission(s) auto-submitted due to section time limits.");

        return self::SUCCESS;
    }
}
