<?php


namespace App\Http\Controllers;

use App\Models\School;

class BaseSchoolController extends Controller
{
    protected School $school;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('school.context');

        $this->middleware(function ($request, $next) {
            $this->school = app('current_school');
            return $next($request);
        });
    }

    protected function authorize($permission)
    {
        $user = auth()->user();

        // Superadmin can access everything
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Check school-specific permissions
        if (!$user->canAccessSchool($this->school->id)) {
            abort(403);
        }

        // Add your permission logic here
        return true;
    }
}
