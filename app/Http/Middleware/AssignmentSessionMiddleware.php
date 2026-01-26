<?php

namespace App\Http\Middleware;

use App\Models\AssignmentSubmission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentSessionMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $student = Auth::user()->student;
        if (! $student) {
            return $next($request);
        }

        // Check if student has an active assignment submission
        $activeSubmission = AssignmentSubmission::where('student_id', $student->id)
            ->where('status', 'in_progress')
            ->whereHas('assignment', function ($query) {
                $query->where('restrict_navigation', true)
                    ->where('starts_at', '<=', now())
                    ->where('ends_at', '>', now());
            })
            ->with('assignment')
            ->first();

        if ($activeSubmission) {
            $allowedRoutes = [
                'students.assignment.take',
                'logout',
            ];

            $currentRoute = $request->route()->getName();

            // If trying to access restricted route, redirect back to assignment
            if (! in_array($currentRoute, $allowedRoutes)) {
                session()->flash('warning', 'You have an active assignment in progress. Please complete or submit it before navigating away.');

                return redirect()->route('students.assignment.take', [
                    'assignment' => $activeSubmission->assignment_id,
                ]);
            }
        }

        return $next($request);
    }
}
