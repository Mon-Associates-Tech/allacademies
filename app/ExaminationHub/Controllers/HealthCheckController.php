<?php

namespace App\ExaminationHub\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HealthCheckController extends Controller
{
    /**
     * GET /up
     * Basic liveness check. Returns 200 if the application and database are reachable.
     * Used by HAProxy to verify the backend is alive.
     */
    public function up(): JsonResponse
    {
        try {
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database connection failed',
            ], 503);
        }

        return response()->json([
            'status' => 'ok',
        ]);
    }

    /**
     * GET /lb-health
     * Load balancer health check. Returns 503 when PHP-FPM workers exceed the
     * configured busy threshold, signaling HAProxy to route candidates elsewhere.
     *
     * Reads PHP-FPM status from the FastCGI status page or falls back to
     * checking the number of active database connections as a proxy for load.
     */
    public function lbHealth(Request $request): JsonResponse
    {
        $threshold = (int) config('center.lb_health_threshold', 85);

        // Try to read PHP-FPM status via the status path configured in Nginx
        $fpmStatus = $this->getFpmStatus();

        if ($fpmStatus !== null) {
            $active = (int) ($fpmStatus['active'] ?? 0);
            $total = (int) ($fpmStatus['max active'] ?? 0);
            $idle = (int) ($fpmStatus['idle'] ?? 0);

            $utilization = $total > 0 ? round(($active / $total) * 100, 2) : 0;

            if ($utilization >= $threshold) {
                return response()->json([
                    'status' => 'overloaded',
                    'active_workers' => $active,
                    'max_active' => $total,
                    'idle_workers' => $idle,
                    'utilization_percent' => $utilization,
                    'threshold_percent' => $threshold,
                ], 503);
            }

            return response()->json([
                'status' => 'ok',
                'active_workers' => $active,
                'max_active' => $total,
                'idle_workers' => $idle,
                'utilization_percent' => $utilization,
                'threshold_percent' => $threshold,
            ]);
        }

        // Fallback: check database connection count as a proxy for load
        try {
            $dbConnections = $this->getDbConnectionCount();
        } catch (\Throwable $e) {
            $dbConnections = 0;
        }

        // Fallback threshold: 50 concurrent DB connections is considered overloaded
        $dbThreshold = (int) config('center.lb_health_db_threshold', 50);

        if ($dbConnections >= $dbThreshold) {
            return response()->json([
                'status' => 'overloaded',
                'db_connections' => $dbConnections,
                'db_threshold' => $dbThreshold,
                'note' => 'PHP-FPM status unavailable, using DB connections as proxy',
            ], 503);
        }

        return response()->json([
            'status' => 'ok',
            'db_connections' => $dbConnections,
            'db_threshold' => $dbThreshold,
            'note' => 'PHP-FPM status unavailable, using DB connections as proxy',
        ]);
    }

    /**
     * Attempt to read PHP-FPM status from the local FastCGI status endpoint.
     * Returns an associative array of status values, or null if unavailable.
     *
     * @return array<string, mixed>|null
     */
    protected function getFpmStatus(): ?array
    {
        // PHP-FPM status is typically exposed via Nginx at /fpm-status
        // We try to read it via a local request
        $statusPath = config('center.fpm_status_path', '/fpm-status');

        try {
            $url = 'http://127.0.0.1:8081' . $statusPath;
            $content = @file_get_contents($url);

            if ($content === false) {
                return null;
            }

            $status = [];
            foreach (explode("\n", trim($content)) as $line) {
                $parts = explode(':', trim($line), 2);
                if (count($parts) === 2) {
                    $status[trim($parts[0])] = trim($parts[1]);
                }
            }

            return $status;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Get the current number of active database connections.
     */
    protected function getDbConnectionCount(): int
    {
        try {
            $result = DB::select('SELECT COUNT(*) as count FROM information_schema.PROCESSLIST WHERE db = DATABASE()');
            return (int) ($result[0]->count ?? 0);
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
