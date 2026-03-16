<?php

namespace App\Http\Controllers;

use App\Models\GeneralExamParticipant;
use App\Models\GeneralExamSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GeneralExamController extends Controller
{
    /**
     * Show the join assignment page.
     */
    public function join(?string $code = null): View
    {
        return view('public.general-exams.join', [
            'code' => $code,
        ]);
    }

    /**
     * Show the take assignment page.
     */
    public function take(GeneralExamSubmission $submission): View
    {
        return view('public.general-exams.take', [
            'submission' => $submission,
        ]);
    }

    /**
     * Show the assignment results page.
     */
    public function results(Request $request, ?string $token = null): View|RedirectResponse
    {
        $submission = null;
        $token = $token ?: $request->query('token');
        $needsEmail = false;

        if ($token) {
            $participant = GeneralExamParticipant::findByResultToken($token);

            if ($participant) {
                $email = strtolower(trim((string) ($request->input('email') ?? $request->query('email'))));

                if ($email === '') {
                    $needsEmail = true;
                } elseif (strtolower($participant->email) !== $email) {
                    return redirect()->back()->with('error', 'Email does not match this result link.');
                } else {
                    $submission = GeneralExamSubmission::where('participant_type', GeneralExamParticipant::class)
                        ->where('participant_id', $participant->id)
                        ->whereNotNull('submitted_at')
                        ->with(['assignment'])
                        ->orderByDesc('submitted_at')
                        ->orderByDesc('id')
                        ->first();
                }
            }
        }

        return view('public.general-exams.results', [
            'token' => $token,
            'submission' => $submission,
            'needsEmail' => $needsEmail,
        ]);
    }
}
