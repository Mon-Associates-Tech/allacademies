<?php

namespace App\Http\Middleware;

use App\Models\School;
use Auth;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class SchoolContextMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        // Set current school context
        $currentSchool = null;

        if ($user->isSuperAdmin() || $user->hasRole('owner')) {
            // Super admin/owner can switch between schools
            $schoolId = $request->header('X-School-ID') ?:
                session('current_school_id') ?:
                    $request->route('school');

            if ($schoolId) {
                $currentSchool = School::find($schoolId);
                session(['current_school_id' => $schoolId]);
            }
        } else {
            // Regular users are locked to their school
            $currentSchool = $user->school;
        }

        // Share with views and set in app context
        if ($currentSchool) {
            app()->instance('current_school', $currentSchool);
            View::share('currentSchool', $currentSchool);
            config(['app.current_school_id' => $currentSchool->id]);
        }
        return $next($request);
    }
}
