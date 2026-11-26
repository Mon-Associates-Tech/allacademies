<?php

namespace App\Livewire\Students\VirtualClassroom;

use App\Models\Classroom\SessionParticipant;
use App\Services\BigBlueButtonService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MyVirtualSessions extends Component
{
    use WithPagination;

    public $view = 'upcoming'; // upcoming, past, all

    public $search = '';

    protected $queryString = ['view', 'search'];

    public function joinSession($participantId)
    {
        $participant = SessionParticipant::findOrFail($participantId);
        $session = $participant->virtualSession;

        // Authorize
        if ($participant->user_id !== Auth::id()) {
            $this->dispatch('error', 'Unauthorized action.');

            return;
        }

        // Check if session is live
        if (! $session->isLive()) {
            $this->dispatch('error', 'This session is not currently live.');

            return;
        }

        // Get join URL
        try {
            $bbbService = app(BigBlueButtonService::class);
            $joinUrl = $bbbService->getJoinUrl($session, $participant);

            // Mark as joined
            $participant->markAsJoined();

            // Redirect to BBB
            return redirect()->away($joinUrl);

        } catch (\Exception $e) {
            $this->dispatch('error', 'Failed to join session: '.$e->getMessage());
        }
    }

    public function render()
    {
        $query = SessionParticipant::where('user_id', Auth::id())
            ->whereHas('virtualSession', fn ($session) => $session->whereNull('parent_session_id')->orWhereDoesntHave('parentSession'))
            ->with(['virtualSession.user', 'virtualSession.academicSubject']);

        if ($this->search) {
            $query->whereHas('virtualSession', function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        switch ($this->view) {
            case 'upcoming':
                $query->where(function ($participants) {
                    $participants->where(function ($parent) {
                        $parent->whereIn('status', ['invited', 'joined'])
                            ->whereHas('virtualSession', function ($session) {
                                $session->where('status', 'scheduled')
                                    ->where('scheduled_start', '>', now());
                            });
                    })->orWhereHas('virtualSession.childSessions', function ($occurrences) {
                        $occurrences->where('status', 'scheduled')
                            ->where('scheduled_start', '>', now())
                            ->whereHas('participants', function ($participant) {
                                $participant->where('user_id', Auth::id())
                                    ->whereIn('status', ['invited', 'joined']);
                            });
                    });
                })->orderBy('created_at', 'desc');
                break;
            case 'past':
                $query->where(function ($participants) {
                    $participants->whereHas('virtualSession', function ($session) {
                        $session->whereIn('status', ['ended', 'cancelled']);
                    })->orWhereHas('virtualSession.childSessions', function ($occurrences) {
                        $occurrences->whereIn('status', ['ended', 'cancelled'])
                            ->whereHas('participants', fn ($participant) => $participant->where('user_id', Auth::id()));
                    });
                })->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $participants = $query->with([
            'virtualSession.childSessions' => function ($occurrences) {
                $occurrences->whereHas('participants', fn ($participant) => $participant->where('user_id', Auth::id()));

                if ($this->view === 'upcoming') {
                    $occurrences->where('status', 'scheduled')
                        ->where('scheduled_start', '>', now());
                } elseif ($this->view === 'past') {
                    $occurrences->whereIn('status', ['ended', 'cancelled']);
                }

                $occurrences->with([
                    'participants' => fn ($participant) => $participant->where('user_id', Auth::id()),
                ])->orderBy('scheduled_start');
            },
        ])->paginate(10);

        return view('livewire.students.virtual-classroom.my-virtual-sessions', [
            'participants' => $participants,
        ]);
    }
}
