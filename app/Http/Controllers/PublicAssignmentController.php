<?php

namespace App\Http\Controllers;

use App\Models\PublicAssignmentParticipant;
use App\Models\PublicAssignmentSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
    public function results(Request $request, ?string $token = null): View|RedirectResponse
    {
        $submission = null;
        $token = $token ?: $request->query('token');
        $needsEmail = false;

        if ($token) {
            $participant = PublicAssignmentParticipant::findByResultToken($token);

            if ($participant) {
                $email = strtolower(trim((string) ($request->input('email') ?? $request->query('email'))));

                if ($email === '') {
                    $needsEmail = true;
                } elseif (strtolower($participant->email) !== $email) {
                    return redirect()->back()->with('error', 'Email does not match this result link.');
                } else {
                    $submission = PublicAssignmentSubmission::where('participant_type', PublicAssignmentParticipant::class)
                        ->where('participant_id', $participant->id)
                        ->whereNotNull('submitted_at')
                        ->with(['assignment'])
                        ->orderByDesc('submitted_at')
                        ->orderByDesc('id')
                        ->first();
                }
            }
        }

        return view('public.assignments.results', [
            'token' => $token,
            'submission' => $submission,
            'needsEmail' => $needsEmail,
        ]);
    }
}
