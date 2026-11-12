<?php

namespace App\ExaminationHub\Services;

use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamConfiguredParticipant;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\ExaminationHub\Models\SubmissionAnswer;
use Illuminate\Support\Facades\DB;

/**
 * SubmissionIngestService — idempotently ingests submission batches on central.
 *
 * This service processes a batch of submissions pushed from a center.
 * Each submission is upserted by UUID, accepting only higher sync_version
 * values. Answers are upserted alongside their parent submission.
 * After ingestion, a queued parity check re-grades from raw answers.
 */
class SubmissionIngestService
{
    /**
     * Ingest a batch of submissions with their answers.
     *
     * @param array $batch Array of submission data with nested answers
     * @param int $centerId The center pushing this batch
     * @return array{total: int, ingested: int, skipped: int, errors: array}
     */
    public function ingest(array $batch, int $centerId): array
    {
        $total = count($batch);
        $ingested = 0;
        $skipped = 0;
        $errors = [];

        foreach ($batch as $index => $submissionData) {
            try {
                $result = $this->ingestOne($submissionData, $centerId);

                if ($result === 'ingested') {
                    $ingested++;
                } else {
                    $skipped++;
                }
            } catch (\Exception $e) {
                $errors[] = [
                    'index' => $index,
                    'submission_uuid' => $submissionData['uuid'] ?? 'unknown',
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
     * Ingest a single submission with its answers.
     *
     * @return string 'ingested' or 'skipped'
     */
    protected function ingestOne(array $data, int $centerId): string
    {
        $uuid = $data['uuid'] ?? null;
        if (!$uuid) {
            throw new \InvalidArgumentException('Submission missing uuid.');
        }

        $examUuid = $data['exam_uuid'] ?? null;
        if (!$examUuid) {
            throw new \InvalidArgumentException('Submission missing exam_uuid.');
        }

        // Find the exam by UUID
        $exam = GeneralExam::where('uuid', $examUuid)->first();
        if (!$exam) {
            throw new \InvalidArgumentException(
                "Unknown exam_uuid: {$examUuid}. The exam must be published and pulled before submissions can be ingested."
            );
        }

        // Find or create the submission by UUID
        $submission = GeneralExamSubmission::where('uuid', $uuid)->first();

        $newSyncVersion = (int) ($data['sync_version'] ?? 0);

        if ($submission) {
            // Already exists — only accept higher sync_version
            $currentSyncVersion = (int) ($submission->synced_version ?? 0);
            if ($newSyncVersion <= $currentSyncVersion) {
                return 'skipped';
            }
        }

        return DB::transaction(function () use ($submission, $uuid, $exam, $data, $centerId, $newSyncVersion): string {
            if (!$submission) {
                // Create new submission
                $submission = GeneralExamSubmission::create([
                    'uuid' => $uuid,
                    'general_exam_id' => $exam->id,
                    'participant_type' => $data['participant_type'] ?? 'App\ExaminationHub\Models\GeneralExamConfiguredParticipant',
                    'participant_id' => $data['participant_id'] ?? null,
                    'participant_name' => $data['participant_name'] ?? null,
                    'participant_email' => $data['participant_email'] ?? null,
                    'participant_index_number' => $data['participant_index_number'] ?? null,
                    'center_id' => $centerId,
                    'started_at' => $data['started_at'] ? \Carbon\Carbon::parse($data['started_at']) : null,
                    'submitted_at' => $data['submitted_at'] ? \Carbon\Carbon::parse($data['submitted_at']) : null,
                    'time_spent_seconds' => $data['time_spent_seconds'] ?? null,
                    'status' => $data['status'] ?? GeneralExamSubmission::STATUS_SUBMITTED,
                    'score' => $data['score'] ?? null,
                    'total_marks' => $data['total_marks'] ?? null,
                    'percentage' => $data['percentage'] ?? null,
                    'grade' => $data['grade'] ?? null,
                    'graded_at' => $data['graded_at'] ? \Carbon\Carbon::parse($data['graded_at']) : null,
                    'graded_revision' => $data['graded_revision'] ?? null,
                    'requires_manual_review' => $data['requires_manual_review'] ?? false,
                    'auto_submitted' => $data['auto_submitted'] ?? false,
                    'auto_submit_reason' => $data['auto_submit_reason'] ?? null,
                    'attempt_number' => $data['attempt_number'] ?? 1,
                    'ip_address' => $data['ip_address'] ?? null,
                    'user_agent' => $data['user_agent'] ?? null,
                    'server_origin' => $data['server_origin'] ?? 'local',
                    'tab_switch_count' => $data['tab_switch_count'] ?? 0,
                    'violations' => $data['violations'] ?? null,
                    'sync_version' => $newSyncVersion,
                    'synced_version' => $newSyncVersion,
                ]);

                // Upsert the configured participant by index number
                if (!empty($data['participant_index_number'])) {
                    $participant = GeneralExamConfiguredParticipant::firstOrCreate(
                        [
                            'general_exam_id' => $exam->id,
                            'unique_code' => $data['participant_index_number'],
                        ],
                        [
                            'uuid' => $data['participant_uuid'] ?? (string) \Illuminate\Support\Str::ulid(),
                            'name' => $data['participant_name'] ?? null,
                            'email' => $data['participant_email'] ?? null,
                            'is_active' => true,
                        ]
                    );

                    // Link submission to participant
                    $submission->update([
                        'participant_type' => 'App\ExaminationHub\Models\GeneralExamConfiguredParticipant',
                        'participant_id' => $participant->id,
                    ]);
                }
            } else {
                // Update existing submission
                $submission->update([
                    'started_at' => $data['started_at'] ? \Carbon\Carbon::parse($data['started_at']) : $submission->started_at,
                    'submitted_at' => $data['submitted_at'] ? \Carbon\Carbon::parse($data['submitted_at']) : $submission->submitted_at,
                    'time_spent_seconds' => $data['time_spent_seconds'] ?? $submission->time_spent_seconds,
                    'status' => $data['status'] ?? $submission->status,
                    'score' => $data['score'] ?? $submission->score,
                    'total_marks' => $data['total_marks'] ?? $submission->total_marks,
                    'percentage' => $data['percentage'] ?? $submission->percentage,
                    'grade' => $data['grade'] ?? $submission->grade,
                    'graded_at' => $data['graded_at'] ? \Carbon\Carbon::parse($data['graded_at']) : $submission->graded_at,
                    'graded_revision' => $data['graded_revision'] ?? $submission->graded_revision,
                    'requires_manual_review' => $data['requires_manual_review'] ?? $submission->requires_manual_review,
                    'auto_submitted' => $data['auto_submitted'] ?? $submission->auto_submitted,
                    'auto_submit_reason' => $data['auto_submit_reason'] ?? $submission->auto_submit_reason,
                    'tab_switch_count' => $data['tab_switch_count'] ?? $submission->tab_switch_count,
                    'violations' => $data['violations'] ?? $submission->violations,
                    'server_origin' => $data['server_origin'] ?? $submission->server_origin,
                    'sync_version' => $newSyncVersion,
                    'synced_version' => $newSyncVersion,
                ]);
            }

            // Ingest answers
            $answers = $data['answers'] ?? [];
            $this->ingestAnswers($submission, $answers);

            // Dispatch parity check for graded submissions
            if (!empty($data['score'])) {
                \App\ExaminationHub\Jobs\ParityCheckJob::dispatch($submission->id);
            }

            return 'ingested';
        });
    }

    /**
     * Ingest answers for a submission.
     *
     * @param array $answers Array of answer data
     */
    protected function ingestAnswers(GeneralExamSubmission $submission, array $answers): void
    {
        foreach ($answers as $answerData) {
            $questionUuid = $answerData['question_uuid'] ?? null;
            if (!$questionUuid) {
                continue;
            }

            $answerUuid = $answerData['uuid'] ?? (string) \Illuminate\Support\Str::ulid();

            // Find the question by UUID for this exam
            $question = \App\ExaminationHub\Models\GeneralExamQuestion::where('uuid', $questionUuid)
                ->whereHas('section', function ($q) use ($submission) {
                    $q->where('general_exam_id', $submission->general_exam_id);
                })
                ->first();

            if (!$question) {
                continue;
            }

            SubmissionAnswer::updateOrCreate(
                [
                    'uuid' => $answerUuid,
                ],
                [
                    'general_exam_submission_id' => $submission->id,
                    'general_exam_question_id' => $question->id,
                    'question_uuid' => $questionUuid,
                    'response' => strtoupper(trim($answerData['response'] ?? '')),
                    'answered_at' => $answerData['answered_at'] ? \Carbon\Carbon::parse($answerData['answered_at']) : now(),
                ]
            );
        }
    }
}
