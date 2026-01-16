<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class AcademicChatRateLimit
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $maxRequests = config('academic_chat.chat.rate_limit.max_requests', 50);
        $perMinutes = config('academic_chat.chat.rate_limit.per_minutes', 60);

        // Create rate limit key based on IP and user ID
        $key = $this->resolveRequestSignature($request);

        // Check rate limit
        if (RateLimiter::tooManyAttempts($key, $maxRequests)) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'success' => false,
                'error' => 'Too many requests. Please try again in '.$seconds.' seconds.',
                'retry_after' => $seconds,
            ], 429);
        }

        // Record the attempt
        RateLimiter::hit($key, $perMinutes * 60);

        $response = $next($request);

        // Add rate limit headers
        $response->headers->set('X-RateLimit-Limit', $maxRequests);
        $response->headers->set('X-RateLimit-Remaining', $maxRequests - RateLimiter::attempts($key));
        $response->headers->set('X-RateLimit-Reset', now()->addSeconds(RateLimiter::availableIn($key))->timestamp);

        return $response;
    }

    /**
     * Resolve the rate limiting key for the request
     */
    protected function resolveRequestSignature(Request $request): string
    {
        $userId = $request->user()?->id ?? 'guest';
        $ip = $request->ip();

        return "academic_chat_rate_limit:{$userId}:{$ip}";
    }
}

// Register in App\Http\Kernel.php
// protected $middlewareAliases = [
//     'chat.rate.limit' => \App\Http\Middleware\EducationalChatRateLimit::class,
// ];
