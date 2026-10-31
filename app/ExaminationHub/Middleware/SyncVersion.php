<?php

namespace App\ExaminationHub\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SyncVersion
{
    /**
     * Expected application version for sync compatibility.
     */
    protected string $expectedVersion;

    public function __construct()
    {
        $this->expectedVersion = config('center.app_version', '1.0.0');
    }

    /**
     * Check the X-App-Version header on sync API requests.
     *
     * Central answers 426 (Not Acceptable) if the version doesn't match,
     * preventing payload ingestion from a different schema version.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $clientVersion = $request->header('X-App-Version');

        if (!$clientVersion) {
            return response()->json([
                'error' => 'Bad Request',
                'message' => 'Missing X-App-Version header.',
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($clientVersion !== $this->expectedVersion) {
            return response()->json([
                'error' => 'Not Acceptable',
                'message' => "App version mismatch. Expected {$this->expectedVersion}, got {$clientVersion}.",
                'expected_version' => $this->expectedVersion,
                'client_version' => $clientVersion,
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        return $next($request);
    }
}
