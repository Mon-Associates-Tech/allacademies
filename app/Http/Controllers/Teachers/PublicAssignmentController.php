<?php

namespace App\Http\Controllers\Teachers;

use App\Http\Controllers\Controller;
use App\Models\PublicAssignment;
use App\Models\PublicAssignmentSubmission;
use Illuminate\View\View;

class PublicAssignmentController extends Controller
{
    /**
     * Display a listing of public assignments.
     */
    public function index(): View
    {
        return view('teachers.public-assignments.index');
    }

    /**
     * Show the form for creating a new public assignment.
     */
    public function create(): View
    {
        return view('teachers.public-assignments.create');
    }

    /**
     * Display the specified public assignment.
     */
    public function show(PublicAssignment $assignment): View
    {
        return view('teachers.public-assignments.show', [
            'assignment' => $assignment,
        ]);
    }

    /**
     * Show the form for editing the specified public assignment.
     */
    public function edit(PublicAssignment $assignment): View
    {
        return view('teachers.public-assignments.edit', [
            'assignment' => $assignment,
        ]);
    }

    /**
     * Display the results for a public assignment.
     */
    public function results(PublicAssignment $assignment): View
    {
        return view('teachers.public-assignments.results', [
            'assignment' => $assignment,
        ]);
    }

    /**
     * Show the grading form for a submission.
     */
    public function gradeSubmission(PublicAssignmentSubmission $submission): View
    {
        return view('teachers.public-assignments.grade-submission', [
            'submission' => $submission,
        ]);
    }
}
