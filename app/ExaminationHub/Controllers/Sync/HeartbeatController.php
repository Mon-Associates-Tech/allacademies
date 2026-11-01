<?php

namespace App\ExaminationHub\Controllers\Sync;

use App\ExaminationHub\Models\Center;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * HeartbeatController — receives heartbeat from centers.
 *
 * POST /api/sync/v1/heartbeat
 * Center reports app version, last pull, backlog.
 */
class HeartbeatController
{
    /**
     * Store heartbeat data for a center.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'app_version' => 'nullable|string',
            'last_pull_at' => 'nullable|date',
            'last_push_at' => 'nullable|date',
            'backlog_size' => 'nullable|integer|min:0',
        ]);

        // Center is resolved by SyncAuth middleware from the bearer token
        $center = $request->attributes->get('center');
        if (!$center || !$center instanceof Center) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Center not identified.',
            ], 401);
        }

        $updateData = [
            'last_seen_at' => now(),
        ];

        if (!empty($validated['app_version'])) {
            $updateData['app_version'] = $validated['app_version'];
        }

        if (isset($validated['last_pull_at'])) {
            $updateData['last_pull_at'] = $validated['last_pull_at'];
        }

        if (isset($validated['last_push_at'])) {
            $updateData['last_push_at'] = $validated['last_push_at'];
        }

        $center->update($updateData);

        return response()->json([
            'status' => 'ok',
            'server_time' => now()->toIso8601String(),
        ]);
    }
}
