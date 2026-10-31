<?php

namespace App\ExaminationHub\Controllers\Sync;

use App\ExaminationHub\Services\SubmissionIngestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * SubmissionIngestController — handles batch submission ingestion on central.
 *
 * POST /api/sync/v1/ingest/submissions
 *
 * Accepts a batch of submissions with their answers from a center.
 * Each submission is upserted by UUID, accepting only higher sync_version.
 */
class SubmissionIngestController
{
    public function __construct(
        protected SubmissionIngestService $service
    ) {}

    /**
     * Ingest a batch of submissions with answers.
     *
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $batch = $request->input('submissions', []);
        $centerId = $request->input('center_id');

        if (!$centerId) {
            return response()->json([
                'error' => 'center_id is required.',
            ], 400);
        }

        if (empty($batch)) {
            return response()->json([
                'total' => 0,
                'ingested' => 0,
                'skipped' => 0,
                'errors' => [],
            ]);
        }

        $result = $this->service->ingest($batch, (int) $centerId);

        return response()->json($result);
    }
}
