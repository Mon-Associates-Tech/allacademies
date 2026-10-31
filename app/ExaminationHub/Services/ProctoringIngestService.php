<?php

namespace App\ExaminationHub\Services;

use App\ExaminationHub\Models\ExamProctoringLog;
use App\ExaminationHub\Models\GeneralExamSubmission;

/**
 * ProctoringIngestService — idempotently ingests proctoring log batches on central.
 *
 * This service processes a batch of proctoring events pushed from a center.
 * Each log entry is upserted by UUID. Logs are only accepted for submissions
 * that central has already acknowledged.
 */
class ProctoringIngestService
{
    /**
     * Ingest a batch of proctoring logs.
     *
     * @param array $batch Array of proctoring log data
     * @param int $centerId The center pushing this batch
     * @return array{total: int, ingested: int, skipped: int, errors: array}
     */
    public function ingest(array $batch, int $centerId): array
    {
        $total = count($batch);
        $ingested = 0;
        $skipped = 0;
        $errors = [];

        foreach ($batch as $index => $logData) {
            try {
                $result = $this->ingestOne($logData, $centerId);

                if ($result === 'ingested') {
                    $ingested++;
                } else {
                    $skipped++;
                }
            } catch (\Exception $e) {
                $errors[] = [
                    'index' => $index,
                    'log_uuid' => $logData['uuid'] ?? 'unknown',
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'total' => $total,
            'ingested' => $ingested,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * Ingest a single proctoring log entry.
     *
     * @return string 'ingested' or 'skipped'
     */
    protected function ingestOne(array $data, int $centerId): string
    {
        $uuid = $data['uuid'] ?? null;
        if (!$uuid) {
            throw new \InvalidArgumentException('Proctoring log missing uuid.');
        }

        $submissionUuid = $data['submission_uuid'] ?? null;
        if (!$submissionUuid) {
            throw new \InvalidArgumentException('Proctoring log missing submission_uuid.');
        }

        // Check that the submission exists on central
        $submission = GeneralExamSubmission::where('uuid', $submissionUuid)->first();
        if (!$submission) {
            throw new \InvalidArgumentException(
                "Submission {$submissionUuid} not found. Proctoring logs can only be ingested after the submission is acknowledged."
            );
        }

        // Check if already ingested
        $existing = ExamProctoringLog::where('uuid', $uuid)->first();
        if ($existing && $existing->synced_at !== null) {
            return 'skipped';
        }

        if ($existing) {
            // Update existing but unsynced log
            $existing->update([
                'event_type' => $data['event_type'] ?? $existing->event_type,
                'event_data' => $data['event_data'] ?? $existing->event_data,
                'severity' => $data['severity'] ?? $existing->severity,
                'occurred_at' => $data['occurred_at'] ? \Carbon\Carbon::parse($data['occurred_at']) : $existing->occurred_at,
                'center_id' => $centerId,
                'synced_at' => now(),
            ]);
        } else {
            // Create new log entry
            ExamProctoringLog::create([
                'uuid' => $uuid,
                'general_exam_submission_id' => $submission->id,
                'event_type' => $data['event_type'] ?? null,
                'event_data' => $data['event_data'] ?? null,
                'severity' => $data['severity'] ?? ExamProctoringLog::defaultSeverity($data['event_type'] ?? 'unknown'),
                'occurred_at' => $data['occurred_at'] ? \Carbon\Carbon::parse($data['occurred_at']) : now(),
                'center_id' => $centerId,
                'synced_at' => now(),
            ]);
        }

        return 'ingested';
    }
}
