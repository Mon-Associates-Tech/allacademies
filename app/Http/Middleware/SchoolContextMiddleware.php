<?php

namespace App\Http\Middleware;

use App\Models\School;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class SchoolContextMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Only apply to authenticated users
        if (! Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Set school context for users who can access cross-school data
        if ($user->canAccessCrossSchool()) {
            $this->setSchoolContext($request, $user);
        } else {
            // For regular users, ensure their school context is set
            if ($user->school_id) {
                app()->instance('current_school', $user->school);
            }
        }

        return $next($request);
    }

    /**
     * Set the school context based on session or request parameters
     */
    private function setSchoolContext(Request $request, $user): void
    {
        $schoolId = null;

        // Priority 1: Check for school_id in request (for API or direct access)
        if ($request->has('school_id')) {
            $requestSchoolId = $request->get('school_id');

            // Validate the school exists and user has access
            if ($this->validateSchoolAccess($requestSchoolId, $user)) {
                $schoolId = $requestSchoolId;
                // Store in session for subsequent requests
                session(['current_school_id' => $schoolId]);
            }
        }

        // Priority 2: Check session for current school
        if (! $schoolId && session()->has('current_school_id')) {
            $sessionSchoolId = session('current_school_id');

            if ($this->validateSchoolAccess($sessionSchoolId, $user)) {
                $schoolId = $sessionSchoolId;
            } else {
                // Invalid school in session, clear it
                session()->forget('current_school_id');
            }
        }

        // Priority 3: Default to user's own school for cross-school users
        if (! $schoolId && $user->school_id) {
            $schoolId = $user->school_id;
        }

        // Set the school context if we have a valid school
        if ($schoolId) {
            $school = School::find($schoolId);
            if ($school) {
                app()->instance('current_school', $school);

                // Also make it available as a request attribute
                $request->attributes->set('current_school', $school);
            }
        }
    }

    /**
     * Validate that user has access to the specified school
     */
    private function validateSchoolAccess($schoolId, $user): bool
    {
        if (! $schoolId) {
            return false;
        }

        // Super admins and owners can access any school
        if ($user->isSuperAdmin() || $user->hasRole('owner')) {
            return School::where('id', $schoolId)->exists();
        }

        // Regular users can only access their own school
        return $user->school_id == $schoolId;
    }
}
