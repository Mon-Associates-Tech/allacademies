<?php

namespace App\ExaminationHub\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class CenterScope
{
    /**
     * The user roles that bypass center scoping.
     */
    protected array $bypassRoles = ['admin', 'superadmin', 'owner'];

    /**
     * Handle an incoming request.
     *
     * Applies center-aware scoping to queries. Center users can only see
     * data from their own center. Admin users bypass this scope.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $role = config('center.role', 'central');

        // Only apply on center instances
        if ($role !== 'center') {
            return $next($request);
        }

        $user = $request->user();
        if (!$user) {
            return $next($request);
        }

        // Admin roles bypass center scoping
        $userRole = $user->role ?? null;
        if (in_array($userRole, $this->bypassRoles)) {
            return $next($request);
        }

        // Apply global scope for center-aware queries
        $centerId = $user->center_id ?? config('center.center_id');

        if ($centerId) {
            // Register global scopes for center-aware models
            \App\ExaminationHub\Models\GeneralExamSubmission::addGlobalScope('center', function (Builder $builder) use ($centerId) {
                // Show submissions owned by this center AND submissions that were
                // routed through this center's HAProxy to Central (server_origin='central'
                // but tagged with this center's ID via X-Forwarded-Center header).
                $builder->where(function ($q) use ($centerId) {
                    $q->where('center_id', $centerId)
                      ->orWhere(function ($q2) use ($centerId) {
                          // Submissions created on Central for this center's students
                          $q2->where('server_origin', 'central')
                              ->where('center_id', $centerId);
                      });
                });
            });

            \App\ExaminationHub\Models\ExamProctoringLog::addGlobalScope('center', function (Builder $builder) use ($centerId) {
                $builder->where('center_id', $centerId);
            });
        }

        return $next($request);
    }
}
