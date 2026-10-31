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
            'center_uuid' => 'required|string',
            'app_version' => 'required|string',
            'last_pull_at' => 'nullable|date',
            'backlog_size' => 'nullable|integer|min:0',
        ]);

        $center = Center::where('uuid', $validated['center_uuid'])->first();

        if (!$center) {
            // Auto-create center if it doesn't exist
            $center = Center::firstOrCreate(
                ['uuid' => $validated['center_uuid']],
                [
                    'code' => substr($validated['center_uuid'], 0, 8),
                    'name' => 'Center ' . substr($validated['center_uuid'], 0, 8),
                    'is_active' => true,
                ]
            );
        }

        $center->update([
            'last_seen_at' => now(),
            'last_pull_at' => $validated['last_pull_at'] ?? $center->last_pull_at,
            'app_version' => $validated['app_version'],
        ]);

        return response()->json([
            'status' => 'ok',
            'server_time' => now()->toIso8601String(),
        ]);
    }
}
