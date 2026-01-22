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
            'recordings',
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

    public function stopRecurrence()
    {
        if (! $this->session->isParentSession()) {
            $this->dispatch('error', 'This is not a recurring session.');

            return;
        }

        $this->session->stopRecurrence();
        $this->session->refresh();

        $this->dispatch('success', 'Recurring session stopped. Future sessions have been cancelled.');
    }

    public function deleteRecurringSeries()
    {
        if (! $this->session->isParentSession() && ! $this->session->isChildSession()) {
            $this->dispatch('error', 'This is not a recurring session.');

            return;
        }

        $parent = $this->session->isChildSession()
            ? $this->session->parentSession
            : $this->session;

        // Delete all future child sessions
        $parent->childSessions()
            ->where('status', 'scheduled')
            ->where('scheduled_start', '>', now())
            ->delete();

        // Delete parent
        $parent->delete();

        session()->flash('success', 'Recurring session series deleted successfully.');

        return redirect()->route('teachers.classroom.index');
    }

    public function render()
    {
        return view('livewire.teachers.virtual-classroom.session-details');
    }
}
