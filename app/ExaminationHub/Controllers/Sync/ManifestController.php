<?php

namespace App\ExaminationHub\Controllers\Sync;

use App\ExaminationHub\Services\SyncManifestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * ManifestController — returns the sync manifest.
 *
 * GET /api/sync/v1/manifest
 * Returns server_time and a list of exams with uuid, revision, checksum, and size.
 */
class ManifestController
{
    public function __construct(
        protected SyncManifestService $manifestService
    ) {}

    /**
     * Get the sync manifest.
     */
    public function index(Request $request): JsonResponse
    {
        $manifest = $this->manifestService->generate();

        return response()->json($manifest);
    }
}
