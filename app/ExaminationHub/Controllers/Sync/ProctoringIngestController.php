<?php

namespace App\ExaminationHub\Controllers\Sync;

use App\ExaminationHub\Services\ProctoringIngestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * ProctoringIngestController — handles batch proctoring log ingestion on central.
 *
 * POST /api/sync/v1/ingest/proctoring
 *
 * Accepts a batch of proctoring logs from a center.
 * Each log is upserted by UUID, only for acknowledged submissions.
 */
class ProctoringIngestController
{
    public function __construct(
        protected ProctoringIngestService $service
    ) {}

    /**
     * Ingest a batch of proctoring logs.
     *
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $batch = $request->input('proctoring', []);
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
