<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GeneralExamController extends Controller
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

        $submissions = GeneralExamSubmission::where('participant_type', Student::class)
            ->where('participant_id', $student->id)
            ->with('assignment')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('students.general-exams.index', [
            'submissions' => $submissions,
        ]);
    }

    /**
     * Display the result for a specific submission.
     */
    public function result(Request $request, GeneralExamSubmission $submission): View|RedirectResponse
    {
        $email = strtolower(trim((string) $request->input('email')));
        if ($email === '') {
            return redirect()->back()->with('error', 'Please provide the email used for this assignment.');
        }

        $participantEmail = null;

        if ($submission->participant_type === Student::class) {
            $student = Student::where('id', $submission->participant_id)->with('user')->first();
            $participantEmail = strtolower($student?->user?->email ?? '');
        } else {
            $participantEmail = strtolower(optional($submission->participant)->email ?? '');
        }

        if ($participantEmail === '' || $participantEmail !== $email) {
            abort(403, 'Email does not match the submission.');
        }

        return view('students.general-exams.result', [
            'submission' => $submission,
        ]);
    }
}
