<?php

namespace App\ExaminationHub\Services;

use App\ExaminationHub\Models\ExamProctoringLog;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamSubmission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ProctoringService
{
    /**
     * How many high-severity events trigger an auto-flag on the submission.
     */
    private const HIGH_SEVERITY_THRESHOLD = 3;

    /**
     * How many medium-severity events trigger a warning flag.
     */
    private const MEDIUM_SEVERITY_THRESHOLD = 5;

    // ─── Event logging ────────────────────────────────────────────────────────

    /**
     * Record a proctoring event for the active submission.
     * Called from the browser via the `save-proctoring-event` route.
     *
     * @param  array  $eventData  Arbitrary context payload from the client.
     */
    public function logEvent(
        GeneralExamSubmission $submission,
        string $eventType,
        array $eventData = []
    ): ExamProctoringLog {
        $severity = ExamProctoringLog::defaultSeverity($eventType);

        $log = ExamProctoringLog::create([
            'general_exam_submission_id' => $submission->id,
            'event_type'                 => $eventType,
            'event_data'                 => $eventData,
            'severity'                   => $severity,
            'occurred_at'                => now(),
        ]);

        Log::info('Proctoring event logged', [
            'submission_id' => $submission->id,
            'event_type'    => $eventType,
            'severity'      => $severity,
        ]);

        $this->checkAndFlagIfNecessary($submission);

        return $log;
    }

    // ─── Summary & reporting ─────────────────────────────────────────────────

    /**
     * Return a violation summary grouped by event type for a single submission.
     *
     * @return array{total: int, high: int, medium: int, low: int, by_type: array}
     */
    public function getSummaryForSubmission(GeneralExamSubmission $submission): array
    {
        $logs = ExamProctoringLog::forSubmission($submission->id)->get();

        $byType = $logs->groupBy('event_type')->map(fn (Collection $group) => [
            'count'    => $group->count(),
            'severity' => $group->first()->severity,
        ])->toArray();

        return [
            'total'   => $logs->count(),
            'high'    => $logs->where('severity', ExamProctoringLog::SEVERITY_HIGH)->count(),
            'medium'  => $logs->where('severity', ExamProctoringLog::SEVERITY_MEDIUM)->count(),
            'low'     => $logs->where('severity', ExamProctoringLog::SEVERITY_LOW)->count(),
            'by_type' => $byType,
            'flagged' => $this->isFlagged($submission),
        ];
    }

    /**
     * Return proctoring summaries for all submissions on an exam (admin view).
     */
    public function getSummaryForExam(GeneralExam $exam): Collection
    {
        $exam->load('submissions');

        return $exam->submissions->map(function (GeneralExamSubmission $submission) {
            return [
                'submission'      => $submission,
                'proctoring'      => $this->getSummaryForSubmission($submission),
            ];
        })->filter(fn ($row) => $row['proctoring']['total'] > 0)
          ->sortByDesc(fn ($row) => $row['proctoring']['high'])
          ->values();
    }

    /**
     * Full event log for a submission, latest first.
     */
    public function getLogsForSubmission(GeneralExamSubmission $submission): Collection
    {
        return ExamProctoringLog::forSubmission($submission->id)
            ->orderByDesc('occurred_at')
            ->get();
    }

    // ─── Threshold checks ─────────────────────────────────────────────────────

    /**
     * Determine whether a submission has crossed the auto-flag threshold.
     */
    public function isFlagged(GeneralExamSubmission $submission): bool
    {
        $highCount   = ExamProctoringLog::forSubmission($submission->id)
            ->where('severity', ExamProctoringLog::SEVERITY_HIGH)->count();
        $mediumCount = ExamProctoringLog::forSubmission($submission->id)
            ->where('severity', ExamProctoringLog::SEVERITY_MEDIUM)->count();

        return $highCount   >= self::HIGH_SEVERITY_THRESHOLD
            || $mediumCount >= self::MEDIUM_SEVERITY_THRESHOLD;
    }

    /**
     * Determine whether a submission should be auto-submitted due to violations.
     * The exam must have `auto_submit_on_violation = true`.
     */
    public function shouldAutoSubmit(GeneralExam $exam, GeneralExamSubmission $submission): bool
    {
        if (! $exam->auto_submit_on_violation) {
            return false;
        }

        $exitCount = ExamProctoringLog::forSubmission($submission->id)
            ->where('event_type', ExamProctoringLog::EVENT_EXAM_EXIT)
            ->count();

        // Auto-submit after 3 exit attempts if enabled
        return $exitCount >= 3;
    }

    // ─── Private helpers ─────────────────────────────────────────────────────

    private function checkAndFlagIfNecessary(GeneralExamSubmission $submission): void
    {
        if ($this->isFlagged($submission) && empty($submission->flagged_at)) {
            $submission->updateQuietly(['flagged_at' => now()]);
        }
    }
}
