<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\PublicAssignmentSubmission;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PublicAssignmentController extends Controller
{
    /**
     * Display a listing of the student's public assignment submissions.
     */
    public function index(): View|RedirectResponse
    {
        $student = Student::where('user_id', auth()->id())->first();

        if (! $student) {
            return redirect()->route('dashboard')->with('error', 'Student profile not found.');
        }

        $submissions = PublicAssignmentSubmission::where('participant_type', Student::class)
            ->where('participant_id', $student->id)
            ->with('assignment')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('students.public-assignments.index', [
            'submissions' => $submissions,
        ]);
    }

    /**
     * Display the result for a specific submission.
     */
    public function result(PublicAssignmentSubmission $submission): View
    {
        return view('students.public-assignments.result', [
            'submission' => $submission,
        ]);
    }
}
