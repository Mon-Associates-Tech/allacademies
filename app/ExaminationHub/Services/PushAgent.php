<?php

namespace App\ExaminationHub\Services;

use App\ExaminationHub\Models\ExamProctoringLog;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\ExaminationHub\Models\SubmissionAnswer;
use App\ExaminationHub\Models\SyncRun;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * PushAgent — pushes results from a center to central.
 *
 * This service implements the outbox pattern: it finds submissions and
 * proctoring logs where synced_version < sync_version, batches them,
 * and pushes to central's ingest endpoints. On success, it updates
 * synced_version to mark rows as synced.
 *
 * Retry uses exponential backoff with jitter (30 seconds to 15 minutes).
 */
class PushAgent
{
    const SUBMISSION_BATCH_SIZE = 100;
    const PROCTORING_BATCH_SIZE = 1000;

    /**
     * Push all pending submissions and proctoring logs to central.
     *
     * @return array{submissions: array, proctoring: array, sync_run: SyncRun|null}
     */
    public function push(): array
    {
        $centralUrl = config('center.central_url');
        $token = config('center.sync_token');
        $centerId = config('center.center_id');

        if (!$centralUrl || !$token || !$centerId) {
            Log::warning('PushAgent: Central URL, token, or center_id not configured.');
            return [
                'submissions' => ['error' => 'Configuration missing'],
                'proctoring' => ['error' => 'Configuration missing'],
                'sync_run' => null,
            ];
        }

        $syncRun = SyncRun::create([
            'direction' => 'push',
            'started_at' => now(),
            'status' => 'running',
        ]);

        try {
            $submissionResult = $this->pushSubmissions($centralUrl, $token, $centerId, $syncRun);
            $proctoringResult = $this->pushProctoring($centralUrl, $token, $centerId, $syncRun);

            $syncRun->update([
                'finished_at' => now(),
                'status' => 'success',
                'items_total' => ($submissionResult['total'] ?? 0) + ($proctoringResult['total'] ?? 0),
                'items_ok' => ($submissionResult['ingested'] ?? 0) + ($proctoringResult['ingested'] ?? 0),
            ]);

            // Report push status to central via heartbeat so last_push_at is tracked
            $this->reportHeartbeat($centralUrl, $token, 'push');

            return [
                'submissions' => $submissionResult,
                'proctoring' => $proctoringResult,
                'sync_run' => $syncRun,
            ];
        } catch (\Exception $e) {
            $syncRun->update([
                'finished_at' => now(),
                'status' => 'failed',
                'error' => $e->getMessage(),
            ]);

            Log::error('PushAgent failed: ' . $e->getMessage(), [
                'sync_run_id' => $syncRun->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'submissions' => ['error' => $e->getMessage()],
                'proctoring' => ['error' => $e->getMessage()],
                'sync_run' => $syncRun,
            ];
        }
    }

    /**
     * Push pending submissions in batches.
     */
    protected function pushSubmissions(string $centralUrl, string $token, int $centerId, SyncRun $syncRun): array
    {
        $totalIngested = 0;
        $totalSkipped = 0;
        $totalErrors = 0;
        $page = 0;

        do {
            // Find pending submissions: synced_version < sync_version
            $submissions = GeneralExamSubmission::whereRaw('synced_version < sync_version')
                ->orderBy('id')
                ->limit(self::SUBMISSION_BATCH_SIZE)
                ->get();

            if ($submissions->isEmpty()) {
                break;
            }

            $batch = $this->buildSubmissionBatch($submissions);
            $page++;

            try {
                $result = $this->sendBatch(
                    $centralUrl . '/api/sync/v1/ingest/submissions',
                    $token,
                    $batch
                );

                $ingested = $result['ingested'] ?? 0;
                $skipped = $result['skipped'] ?? 0;
                $errors = $result['errors'] ?? [];

                $totalIngested += $ingested;
                $totalSkipped += $skipped;
                $totalErrors += count($errors);

                // Mark successfully ingested submissions as synced
                if ($ingested > 0) {
                    $ingestedUuids = collect($batch)
                        ->filter(fn ($s) => isset($s['uuid']))
                        ->pluck('uuid')
                        ->toArray();

                    GeneralExamSubmission::whereIn('uuid', $ingestedUuids)
                        ->update(['synced_version' => DB::raw('sync_version')]);
                }

                Log::info("PushAgent: Submission batch {$page} pushed: {$ingested} ingested, {$skipped} skipped, " . count($errors) . " errors");
            } catch (\Exception $e) {
                $totalErrors++;
                Log::error("PushAgent: Submission batch {$page} failed: " . $e->getMessage());

                // Retry with backoff
                $this->retryWithBackoff('push_submissions');

                break;
            }
        } while (true);

        return [
            'total' => $totalIngested + $totalSkipped + $totalErrors,
            'ingested' => $totalIngested,
            'skipped' => $totalSkipped,
            'errors' => $totalErrors,
        ];
    }

    /**
     * Push pending proctoring logs in batches.
     */
    protected function pushProctoring(string $centralUrl, string $token, int $centerId, SyncRun $syncRun): array
    {
        $totalIngested = 0;
        $totalSkipped = 0;
        $totalErrors = 0;
        $page = 0;

        do {
            // Find unsynced proctoring logs
            $logs = ExamProctoringLog::whereNull('synced_at')
                ->orderBy('id')
                ->limit(self::PROCTORING_BATCH_SIZE)
                ->get();

            if ($logs->isEmpty()) {
                break;
            }

            $batch = $this->buildProctoringBatch($logs);
            $page++;

            try {
                $result = $this->sendBatch(
                    $centralUrl . '/api/sync/v1/ingest/proctoring',
                    $token,
                    $batch
                );

                $ingested = $result['ingested'] ?? 0;
                $skipped = $result['skipped'] ?? 0;
                $errors = $result['errors'] ?? [];

                $totalIngested += $ingested;
                $totalSkipped += $skipped;
                $totalErrors += count($errors);

                // Mark successfully synced logs
                if ($ingested > 0) {
                    $syncedUuids = collect($batch)
                        ->filter(fn ($l) => isset($l['uuid']))
                        ->pluck('uuid')
                        ->toArray();

                    ExamProctoringLog::whereIn('uuid', $syncedUuids)
                        ->update(['synced_at' => now()]);
                }

                Log::info("PushAgent: Proctoring batch {$page} pushed: {$ingested} ingested, {$skipped} skipped, " . count($errors) . " errors");
            } catch (\Exception $e) {
                $totalErrors++;
                Log::error("PushAgent: Proctoring batch {$page} failed: " . $e->getMessage());

                $this->retryWithBackoff('push_proctoring');

                break;
            }
        } while (true);

        return [
            'total' => $totalIngested + $totalSkipped + $totalErrors,
            'ingested' => $totalIngested,
            'skipped' => $totalSkipped,
            'errors' => $totalErrors,
        ];
    }

    /**
     * Build a submission batch array for pushing.
     *
     * @param \Illuminate\Database\Eloquent\Collection $submissions
     * @return array
     */
    protected function buildSubmissionBatch($submissions): array
    {
        $batch = [];

        foreach ($submissions as $submission) {
            // Load answers for this submission
            $answers = SubmissionAnswer::where(
                'general_exam_submission_id',
                $submission->id
            )->get();

            $answerData = [];
            foreach ($answers as $answer) {
                $answerData[] = [
                    'uuid' => $answer->uuid,
                    'question_uuid' => $answer->question_uuid,
                    'response' => $answer->response,
                    'answered_at' => $answer->answered_at?->toIso8601String(),
                ];
            }

            $batch[] = [
                'uuid' => $submission->uuid,
                'exam_uuid' => $submission->exam?->uuid,
                'participant_type' => $submission->participant_type,
                'participant_id' => $submission->participant_id,
                'participant_name' => $submission->participant_name,
                'participant_email' => $submission->participant_email,
                'participant_index_number' => $submission->participant_index_number,
                'participant_uuid' => $submission->participant?->uuid ?? null,
                'started_at' => $submission->started_at?->toIso8601String(),
                'submitted_at' => $submission->submitted_at?->toIso8601String(),
                'time_spent_seconds' => $submission->time_spent_seconds,
                'status' => $submission->status,
                'score' => $submission->score,
                'total_marks' => $submission->total_marks,
                'percentage' => $submission->percentage,
                'grade' => $submission->grade,
                'graded_at' => $submission->graded_at?->toIso8601String(),
                'graded_revision' => $submission->graded_revision,
                'requires_manual_review' => $submission->requires_manual_review,
                'auto_submitted' => $submission->auto_submitted,
                'auto_submit_reason' => $submission->auto_submit_reason,
                'attempt_number' => $submission->attempt_number,
                'ip_address' => $submission->ip_address,
                'user_agent' => $submission->user_agent,
                'tab_switch_count' => $submission->tab_switch_count,
                'violations' => $submission->violations,
                'sync_version' => $submission->sync_version,
                'answers' => $answerData,
            ];
        }

        return $batch;
    }

    /**
     * Build a proctoring log batch array for pushing.
     *
     * @param \Illuminate\Database\Eloquent\Collection $logs
     * @return array
     */
    protected function buildProctoringBatch($logs): array
    {
        $batch = [];

        foreach ($logs as $log) {
            $batch[] = [
                'uuid' => $log->uuid,
                'submission_uuid' => $log->submission?->uuid,
                'event_type' => $log->event_type,
                'event_data' => $log->event_data,
                'severity' => $log->severity,
                'occurred_at' => $log->occurred_at?->toIso8601String(),
            ];
        }

        return $batch;
    }

    /**
     * Send a batch to central's ingest endpoint.
     */
    protected function sendBatch(string $url, string $token, array $batch): array
    {
        $body = json_encode($batch);
        $signature = hash_hmac('sha256', $body, $token);
        $timestamp = (string) time();

        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'X-Signature' => $signature,
                'X-Timestamp' => $timestamp,
                'X-App-Version' => config('center.app_version', '1.0.0'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->post($url, $batch);

        if (!$response->successful()) {
            throw new \RuntimeException(
                "Push failed: HTTP {$response->status()} - {$response->body()}"
            );
        }

        return $response->json();
    }

    /**
     * Retry a failed operation with exponential backoff and jitter.
     *
     * @param string $operation The operation type ('push_submissions' or 'push_proctoring')
     * @param int $maxRetries Maximum number of retries
     * @param int $baseDelay Base delay in seconds
     */
    protected function retryWithBackoff(string $operation, int $maxRetries = 3, int $baseDelay = 30): void
    {
        // Exponential backoff with jitter
        $delay = $baseDelay + random_int(0, $baseDelay);
        // Cap at 15 minutes
        $delay = min($delay, 900);

        Log::info("PushAgent: Scheduling retry for {$operation} in {$delay} seconds");

        // Schedule retry via SyncRetryJob with serializable operation name
        \App\ExaminationHub\Jobs\SyncRetryJob::dispatch($operation)
            ->delay(now()->addSeconds($delay));
    }

    /**
     * Report heartbeat to central so last_push_at / last_pull_at is tracked.
     */
    protected function reportHeartbeat(string $centralUrl, string $token, string $direction): void
    {
        try {
            $centerUuid = \App\ExaminationHub\Models\Center::where('id', config('center.center_id'))->value('uuid');
            if (!$centerUuid) {
                return;
            }

            $body = [
                'center_uuid' => $centerUuid,
                'app_version' => config('center.app_version', '1.0.0'),
            ];

            if ($direction === 'push') {
                $body['last_push_at'] = now()->toIso8601String();
            } else {
                $body['last_pull_at'] = now()->toIso8601String();
            }

            $signature = hash_hmac('sha256', json_encode($body), $token);

            Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'X-Signature' => $signature,
                    'X-Timestamp' => (string) time(),
                    'X-App-Version' => config('center.app_version', '1.0.0'),
                    'Content-Type' => 'application/json',
                ])
                ->post($centralUrl . '/api/sync/v1/heartbeat', $body);
        } catch (\Exception $e) {
            Log::warning("PushAgent: Heartbeat failed: " . $e->getMessage());
        }
    }
}
