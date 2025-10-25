<?php

namespace App\Livewire\Teachers\VirtualClassroom;

use App\Models\Classroom\VirtualSession;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SessionDetails extends Component
{
    public VirtualSession $session;
    public $activeTab = 'overview'; // overview, participants, recordings, settings

    public function mount(VirtualSession $session)
    {
        // Authorize
        if ($session->teacher_id !== Auth::user()->teacher->id) {
            abort(403, 'Unauthorized');
        }

        $this->session = $session->load([
            'academicLevel',
            'academicGroup',
            'academicSubject',
            'participants.user',
            'recordings'
        ]);
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function copyJoinLink()
    {
        // This will be handled by JavaScript
        $this->dispatch('link-copied', 'Join link copied to clipboard!');
    }

    public function deleteSession()
    {
        $this->session->delete();

        session()->flash('success', 'Session deleted successfully.');
        return redirect()->route('teachers.classroom');
    }

    public function render()
    {
        return view('livewire.teachers.virtual-classroom.session-details');
    }
}
