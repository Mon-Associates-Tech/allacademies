<?php

namespace App\Livewire\Teachers\VirtualClassroom;

use App\Models\Classroom\VirtualSession;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class SessionManager extends Component
{
    use WithPagination;

    public $view = 'upcoming'; // upcoming, past, all

    public $search = '';

    protected $queryString = ['view', 'search'];

    public function mount()
    {
        // Initialize
    }

    public function startSession($sessionId)
    {
        $session = VirtualSession::findOrFail($sessionId);

        // Authorize
        if ($session->user_id !== Auth::id()) {
            $this->dispatch('error', 'Unauthorized action.');

            return;
        }

        if (! $session->canStart()) {
            $this->dispatch('error', 'Session cannot be started yet.');

            return;
        }

        return redirect()->route('teachers.classroom.start', $session);
    }

    public function cancelSession($sessionId)
    {
        $session = VirtualSession::findOrFail($sessionId);

        if ($session->user_id !== Auth::id()) {
            $this->dispatch('error', 'Unauthorized action.');

            return;
        }

        $session->update(['status' => 'cancelled']);

        $this->dispatch('success', 'Session cancelled successfully.');
    }

    public function deleteSession($sessionId)
    {
        $session = VirtualSession::findOrFail($sessionId);

        if ($session->user_id !== Auth::id()) {
            $this->dispatch('error', 'Unauthorized action.');

            return;
        }

        $sessionTitle = $session->title;
        $sessionId = $session->id;

        $session->delete();

        // Log activity
        $session->logActivity('delete', 'Virtual Session Deleted', 'virtual_session', [
            'session_title' => $sessionTitle,
            'session_id' => $sessionId,
            'deleted_by' => auth()->user()?->name ?? 'Unknown',
        ]);

        $this->dispatch('success', 'Session deleted successfully.');
    }

    public function render()
    {
        $query = VirtualSession::where('user_id', Auth::id())
            ->where(function ($sessions) {
                $sessions->whereNull('parent_session_id')
                    ->orWhereDoesntHave('parentSession');
            });

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        switch ($this->view) {
            case 'upcoming':
                $upcomingOccurrences = fn ($occurrences) => $occurrences
                    ->where('status', 'scheduled')
                    ->where('scheduled_start', '>', now());

                $query->where(function ($sessions) use ($upcomingOccurrences) {
                    $sessions->where(function ($session) {
                        $session->where('status', 'scheduled')
                            ->where('scheduled_start', '>', now());
                    })->orWhereHas('childSessions', $upcomingOccurrences);
                })->orderBy('scheduled_start');
                break;
            case 'past':
                $pastOccurrences = fn ($occurrences) => $occurrences
                    ->whereIn('status', ['ended', 'cancelled']);

                $query->where(function ($sessions) use ($pastOccurrences) {
                    $sessions->whereIn('status', ['ended', 'cancelled'])
                        ->orWhereHas('childSessions', $pastOccurrences);
                })->orderBy('scheduled_start', 'desc');
                break;
            default:
                $query->orderBy('scheduled_start', 'desc');
        }

        $sessions = $query->with([
            'academicLevel',
            'academicGroup',
            'academicSubject',
            'participants',
            'childSessions' => function ($occurrences) {
                if ($this->view === 'upcoming') {
                    $occurrences->where('status', 'scheduled')
                        ->where('scheduled_start', '>', now());
                } elseif ($this->view === 'past') {
                    $occurrences->whereIn('status', ['ended', 'cancelled']);
                }

                $occurrences->orderBy('scheduled_start');
            },
        ])->paginate(10);

        return view('livewire.teachers.virtual-classroom.session-manager', [
            'sessions' => $sessions,
        ]);
    }
}
