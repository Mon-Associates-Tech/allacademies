<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateExamSession
{
    /**
     * Handle an incoming request.
     *
     * Validates exam session integrity by checking IP and user agent consistency.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only validate if we have an active exam session
        if (!session()->has('exam_submission_id')) {
            return $next($request);
        }

        // Check IP address consistency (optional - can be disabled for mobile users)
        if (session()->has('exam_ip_address')) {
            $storedIp = session('exam_ip_address');
            $currentIp = $request->ip();
            
            // Allow minor IP changes (e.g., mobile networks) but log significant changes
            if ($storedIp !== $currentIp) {
                // Log the IP change for security audit
                \Log::warning('Exam session IP mismatch detected', [
                    'submission_id' => session('exam_submission_id'),
                    'stored_ip' => $storedIp,
                    'current_ip' => $currentIp,
                    'user_agent' => $request->userAgent(),
                ]);
                
                // For now, just log - don't block (can be made stricter later)
                // abort(403, 'Session security check failed. Please re-authenticate.');
            }
        }

        // Check user agent consistency
        if (session()->has('exam_user_agent_hash')) {
            $storedHash = session('exam_user_agent_hash');
            $currentHash = hash('sha256', $request->userAgent() ?? '');
            
            if ($storedHash !== $currentHash) {
                \Log::warning('Exam session user agent mismatch detected', [
                    'submission_id' => session('exam_submission_id'),
                    'stored_hash' => substr($storedHash, 0, 16),
                    'current_hash' => substr($currentHash, 0, 16),
                ]);
                
                // For now, just log - don't block
            }
        }

        return $next($request);
    }
}
