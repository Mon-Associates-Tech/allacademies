<?php

namespace App\Http\Controllers;

use App\Models\PublicAssignmentSubmission;
use Illuminate\View\View;

class PublicAssignmentController extends Controller
{
    /**
     * Show the join assignment page.
     */
    public function join(?string $code = null): View
    {
        return view('public.assignments.join', [
            'code' => $code,
        ]);
    }

    /**
     * Show the take assignment page.
     */
    public function take(PublicAssignmentSubmission $submission): View
    {
        return view('public.assignments.take', [
            'submission' => $submission,
        ]);
    }

    /**
     * Show the assignment results page.
     */
    public function results(?string $token = null): View
    {
        return view('public.assignments.results', [
            'token' => $token,
        ]);
    }
}
