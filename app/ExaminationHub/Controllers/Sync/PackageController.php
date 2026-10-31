<?php

namespace App\ExaminationHub\Controllers\Sync;

use App\ExaminationHub\Models\ExamPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * PackageController — serves exam package downloads.
 *
 * GET /api/sync/v1/exams/{uuid}/revisions/{revision}
 * Returns the gzip-compressed package payload.
 */
class PackageController
{
    /**
     * Download a specific exam package revision.
     */
    public function show(Request $request, string $uuid, int $revision): JsonResponse|Response
    {
        $package = ExamPackage::findExamRevision($uuid, $revision);

        if (!$package) {
            return response()->json([
                'error' => 'not_found',
                'message' => "Package not found for exam {$uuid} revision {$revision}.",
            ], 404);
        }

        // Return the compressed payload as a downloadable response
        $response = response()->make($package->payload)
            ->header('Content-Type', 'application/gzip')
            ->header('Content-Disposition', 'attachment; filename="exam-' . $uuid . '-r' . $revision . '.gz"')
            ->header('X-Checksum', $package->checksum)
            ->header('X-Signature', $package->signature)
            ->header('X-Revision', (string) $package->revision);

        return $response;
    }
}
