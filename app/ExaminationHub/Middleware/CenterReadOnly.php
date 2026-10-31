<?php

namespace App\ExaminationHub\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CenterReadOnly
{
    /**
     * Handle an incoming request on a center instance.
     *
     * Blocks all content creation and editing routes when APP_ROLE=center.
     * Returns 403 Forbidden for blocked routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $role = config('center.role', 'central');

        if ($role !== 'center') {
            return $next($request);
        }

        // Allow GET requests for monitoring and viewing
        if ($request->isMethod('get')) {
            return $next($request);
        }

        // Block all POST, PUT, PATCH, DELETE requests on content routes
        $blockedPrefixes = [
            'examinations/create',
            'examinations/quick-save',
            'examinations/exams/*/edit',
            'examinations/exams/*/send-invitations',
            'examinations/exams/*/send-reminder',
            'examinations/exams/*/reminder-settings',
            'examinations/exams/*/proctoring-settings',
            'examinations/exams/*/participant-mode',
            'examinations/exams/*/toggle-results',
            'examinations/exams/*/violation-settings',
            'examinations/exams/*/questions/*/toggle-grading',
            'examinations/exams/*/participants/configured',
            'examinations/exams/*/participants/*/edit',
            'examinations/exams/*/participants/*',
            'examinations/exams/*/submissions/*/manual-grade',
            'examinations/exams/*/submissions/*/bonus',
            'examinations/exams/*/submissions/bonus-all',
            'examinations/grading-system',
            'examinations/reports/generate',
        ];

        $path = $request->path();
        foreach ($blockedPrefixes as $prefix) {
            $pattern = str_replace('*', '([^/]+)', $prefix);
            if (preg_match('#^' . $pattern . '#', $path)) {
                return response()->json([
                    'error' => 'Not allowed on center instance. Content management is only available on the central server.',
                ], Response::HTTP_FORBIDDEN);
            }
        }

        return $next($request);
    }
}
