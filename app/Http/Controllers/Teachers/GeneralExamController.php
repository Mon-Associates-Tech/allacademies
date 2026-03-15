<?php

namespace App\Http\Controllers\Teachers;

use App\Http\Controllers\Controller;
use App\Models\GeneralExam;
use App\Models\GeneralExamSubmission;
use Illuminate\View\View;

class GeneralExamController extends Controller
{
    /**
     * Display a listing of public assignments.
     */
    public function index(): View
    {
        return view('teachers.general-exams.index');
    }

    /**
     * Show the form for creating a new public assignment.
     */
    public function create(): View
    {
        return view('teachers.general-exams.create');
    }

    /**
     * Display the specified public assignment.
     */
    public function show(GeneralExam $assignment): View
    {
        return view('teachers.general-exams.show', [
            'assignment' => $assignment,
        ]);
    }

    /**
     * Show the form for editing the specified public assignment.
     */
    public function edit(GeneralExam $assignment): View
    {
        return view('teachers.general-exams.edit', [
            'assignment' => $assignment,
        ]);
    }

    /**
     * Display the results for a public assignment.
     */
    public function results(GeneralExam $assignment): View
    {
        return view('teachers.general-exams.results', [
            'assignment' => $assignment,
        ]);
    }

    /**
     * Show the grading form for a submission.
     */
    public function gradeSubmission(GeneralExamSubmission $submission): View
    {
        return view('teachers.general-exams.grade-submission', [
            'submission' => $submission,
        ]);
    }
}
