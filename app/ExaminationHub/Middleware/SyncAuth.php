<?php

namespace App\ExaminationHub\Middleware;

use App\ExaminationHub\Models\Center;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SyncAuth
{
    /**
     * Authenticate sync API requests using per-center bearer tokens.
     *
     * The center sends its bearer token in the Authorization header.
     * Central verifies the token against the hashed token stored for that center.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Missing bearer token.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Find the center by token hash
        $tokenHash = Center::hashToken($token);
        $center = Center::where('token_hash', $tokenHash)->where('is_active', true)->first();

        if (!$center) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Invalid or revoked token.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Attach center to request attributes for downstream use
        $request->attributes->set('center', $center);

        return $next($request);
    }
}
