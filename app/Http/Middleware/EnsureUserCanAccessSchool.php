<?php

namespace App\Http\Middleware;

use App\Models\School;
use Closure;
use Illuminate\Http\Request;

class EnsureUserCanAccessSchool
{
    public function handle(Request $request, Closure $next, string $schoolParameter = 'school')
    {
        $school = $request->route($schoolParameter);
        $schoolId = $school instanceof School ? $school->getKey() : ($school ?: $request->input('school_id'));
        $user = $request->user();

        abort_unless($user && $schoolId && $user->canAccessSchool($schoolId), 403, 'Unauthorized school access');

        $school = $school instanceof School ? $school : School::findOrFail($schoolId);
        app()->instance('current_school', $school);
        app()->instance('current_school_id', $school->id);
        $request->attributes->set('current_school', $school);

        return $next($request);
    }
}
