<?php

namespace App\ExaminationHub\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SyncSignature
{
    /**
     * Maximum allowed timestamp skew in seconds.
     */
    protected int $maxSkew = 300; // 5 minutes

    /**
     * Verify HMAC-SHA256 signature and timestamp on sync API requests.
     *
     * Expects X-Signature header (HMAC-SHA256 of body) and X-Timestamp header.
     * Rejects requests with skew over 5 minutes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $signature = $request->header('X-Signature');
        $timestamp = $request->header('X-Timestamp');

        if (!$signature || !$timestamp) {
            return response()->json([
                'error' => 'Bad Request',
                'message' => 'Missing X-Signature or X-Timestamp header.',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Check timestamp skew
        $requestTime = (int) $timestamp;
        $currentTime = time();
        $skew = abs($currentTime - $requestTime);

        if ($skew > $this->maxSkew) {
            return response()->json([
                'error' => 'Bad Request',
                'message' => 'Timestamp skew exceeds allowed threshold.',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Verify HMAC signature for POST/PUT/PATCH requests (body signature)
        // For GET requests, the signature is over an empty string (already provided by PullAgent)
        $method = strtolower($request->method());
        if (in_array($method, ['post', 'put', 'patch'])) {
            // The signature is HMAC-SHA256 of the raw body using the center's token
            $token = $request->bearerToken();
            $expectedSignature = hash_hmac('sha256', $request->body(), $token ?? '');

            if (!hash_equals($expectedSignature, $signature)) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'Invalid signature.',
                ], Response::HTTP_UNAUTHORIZED);
            }
        }

        return $next($request);
    }
}
