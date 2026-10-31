<?php

namespace App\ExaminationHub\Controllers\Sync;

use App\ExaminationHub\Services\SyncManifestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * StateController — returns the state block for an exam.
 *
 * GET /api/sync/v1/exams/{uuid}/state
 * Returns a small state block with results_released, status, etc.
 */
class StateController
{
    public function __construct(
        protected SyncManifestService $manifestService
    ) {}

    /**
     * Get the state block for a specific exam.
     */
    public function show(Request $request, string $uuid): JsonResponse
    {
        $stateBlock = $this->manifestService->getStateBlock($uuid);

        if (!$stateBlock) {
            return response()->json([
                'error' => 'not_found',
                'message' => "Exam {$uuid} not found.",
            ], 404);
        }

        return response()->json($stateBlock);
    }
}
