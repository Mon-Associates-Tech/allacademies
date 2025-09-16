<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Gate;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected function getCurrentSchool()
    {
        return app('current_school') ?: auth()->user()?->school;
    }

    protected function ensureSchoolAccess($schoolId = null)
    {
        $schoolId = $schoolId ?: $this->getCurrentSchool()?->id;

        if (!$schoolId || !Gate::allows('access-school', $schoolId)) {
            abort(403, 'Unauthorized school access');
        }

        return $schoolId;
    }

    protected function applyCrossSchoolCheck()
    {
        if (!Gate::allows('cross-school-access')) {
            abort(403, 'Unauthorized cross-school access');
        }
    }
}
