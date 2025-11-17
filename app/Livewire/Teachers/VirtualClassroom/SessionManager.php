<?php

namespace App\Livewire\Teachers\VirtualClassroom;

use App\Models\Classroom\VirtualSession;
use App\Services\BigBlueButtonService;
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
        if ($session->teacher_id !== Auth::user()->teacher->id) {
            $this->dispatch('error', 'Unauthorized action.');
            return;
        }

        if (!$session->canStart()) {
            $this->dispatch('error', 'Session cannot be started yet.');
            return;
        }

        return redirect()->route('teachers.classroom.start', $session);
    }

    public function cancelSession($sessionId)
    {
        $session = VirtualSession::findOrFail($sessionId);

        if ($session->teacher_id !== Auth::user()->teacher->id) {
            $this->dispatch('error', 'Unauthorized action.');
            return;
        }

        $session->update(['status' => 'cancelled']);

        $this->dispatch('success', 'Session cancelled successfully.');
    }

    public function deleteSession($sessionId)
    {
        $session = VirtualSession::findOrFail($sessionId);

        if ($session->teacher_id !== Auth::user()->teacher->id) {
            $this->dispatch('error', 'Unauthorized action.');
            return;
        }

        $session->delete();

        $this->dispatch('success', 'Session deleted successfully.');
    }

    public function render()
    {
        $teacher = Auth::user()->teacher;

        $query = VirtualSession::where('teacher_id', $teacher->id);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        switch ($this->view) {
            case 'upcoming':
                $query->where('status', 'scheduled')
                      ->where('scheduled_start', '>', now())
                      ->orderBy('scheduled_start');
                break;
            case 'past':
                $query->whereIn('status', ['ended', 'cancelled'])
                      ->orderBy('scheduled_start', 'desc');
                break;
            default:
                $query->orderBy('scheduled_start', 'desc');
        }

        $sessions = $query->with([
            'academicLevel',
            'academicGroup',
            'academicSubject',
            'participants'
        ])->paginate(10);

        return view('livewire.teachers.virtual-classroom.session-manager', [
            'sessions' => $sessions,
        ]);
    }
}
