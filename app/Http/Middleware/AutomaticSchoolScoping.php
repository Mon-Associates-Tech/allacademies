<?php

namespace App\Http\Middleware;

use App\Models\School;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutomaticSchoolScoping
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Only apply to authenticated users
        if (! Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Handle super admins and owners (cross-school users)
        if ($user->isSuperAdmin() || $user->hasRole('owner')) {
            // Priority 1: Check for school_id in request (for API or direct access)
            if ($request->has('school_id')) {
                $schoolId = $request->get('school_id');
                if (School::where('id', $schoolId)->exists()) {
                    $school = School::find($schoolId);
                    app()->instance('current_school', $school);
                    app()->instance('current_school_id', $schoolId);
                }
            }
            // Priority 2: Check session for current school
            elseif (session()->has('current_school_id')) {
                $schoolId = session('current_school_id');
                if ($schoolId !== null) {
                    $school = School::find($schoolId);
                    if ($school) {
                        app()->instance('current_school', $school);
                        app()->instance('current_school_id', $schoolId);
                    }
                } else {
                    // Explicitly set to null - they want to see all schools
                    app()->instance('current_school', null);
                    app()->instance('current_school_id', null);
                }
            }
            // Priority 3: Default behavior for super admins/owners - can see all schools
            else {
                // Set current_school_id to null to indicate "all schools" view
                app()->instance('current_school_id', null);

                // Only set current_school if they have a school_id
                if ($user->school_id) {
                    app()->instance('current_school', $user->school);
                } else {
                    app()->instance('current_school', null);
                }
            }

            return $next($request);
        }

        // For regular users and admins, apply school scoping
        if ($user->school_id) {
            app()->instance('current_school', $user->school);

            // Register a global scope resolver for Eloquent models
            app()->instance('current_school_id', $user->school_id);
        } else {
            // User has no school assigned - scope to nothing (no results)
            app()->instance('current_school_id', 0); // This will result in no matches
        }

        return $next($request);
    }
}
