<?php

namespace App\Http\Controllers;

use App\Models\Classroom\SessionParticipant;
use App\Models\Classroom\VirtualSession;
use App\Services\BigBlueButtonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class PublicVirtualSessionController extends Controller
{
    public function show(string $joinToken): View
    {
        return view('classroom.join', [
            'session' => $this->sessionForInvitation($joinToken),
            'joinToken' => $joinToken,
        ]);
    }

    public function join(Request $request, string $joinToken, BigBlueButtonService $bbbService): Response
    {
        $session = $this->sessionForInvitation($joinToken);

        if (! $session->allow_guest_login) {
            return response()->view('classroom.join', [
                'session' => $session,
                'joinToken' => $joinToken,
            ], 403);
        }

        if (! $session->isLive()) {
            return redirect()->route('classroom.join', $joinToken)
                ->withErrors(['session' => 'This session is not currently live.']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $participant = $this->participantFor($session, $validated['name']);
        $joinUrl = $bbbService->getJoinUrl($session, $participant);
        $participant->markAsJoined();

        return redirect()->away($joinUrl);
    }

    private function sessionForInvitation(string $joinToken): VirtualSession
    {
        $invitedSession = VirtualSession::where('join_token', $joinToken)->firstOrFail();

        if (! $invitedSession->isParentSession()) {
            return $invitedSession;
        }

        $occurrences = VirtualSession::where(function ($query) use ($invitedSession) {
            $query->whereKey($invitedSession->id)
                ->orWhere('parent_session_id', $invitedSession->id);
        });

        return (clone $occurrences)->where('status', 'live')->orderBy('scheduled_start')->first()
            ?? (clone $occurrences)->where('status', 'scheduled')
                ->where('scheduled_start', '>', now()->subMinutes(15))
                ->orderBy('scheduled_start')
                ->first()
            ?? $invitedSession;
    }

    private function participantFor(VirtualSession $session, string $name): SessionParticipant
    {
        $attributes = [
            'role' => Auth::check() ? 'attendee' : 'guest',
            'status' => 'invited',
            'full_name' => $name,
        ];

        if (! Auth::check()) {
            return $session->participants()->create($attributes + ['user_id' => null]);
        }

        $participant = $session->participants()->firstOrNew(['user_id' => Auth::id()]);
        $participant->fill($attributes);
        $participant->save();

        return $participant;
    }
}