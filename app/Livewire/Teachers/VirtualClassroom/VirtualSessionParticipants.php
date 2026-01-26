<?php

namespace App\Livewire\Teachers\VirtualClassroom;

use App\Models\Classroom\SessionParticipant;
use App\Models\Classroom\VirtualSession;
use App\Models\Student;
use App\Notifications\VirtualSessionInvitationNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class VirtualSessionParticipants extends Component
{
    use WithPagination;

    public VirtualSession $session;

    public $search = '';

    public $statusFilter = 'all'; // all, invited, joined, left, declined

    public $showAddModal = false;

    public $selectedStudents = [];

    public function mount(VirtualSession $session)
    {
        // Authorize
        if ($session->teacher_id !== Auth::user()->teacher->id) {
            abort(403, 'Unauthorized');
        }

        $this->session = $session;
    }

    public function toggleAddModal()
    {
        $this->showAddModal = ! $this->showAddModal;
        $this->selectedStudents = [];
    }

    public function addParticipants()
    {
        if (empty($this->selectedStudents)) {
            $this->dispatch('error', 'Please select at least one student.');

            return;
        }

        $added = 0;
        foreach ($this->selectedStudents as $studentId) {
            // Check if already participant
            $exists = $this->session->participants()
                ->where('user_id', Student::find($studentId)->user_id)
                ->exists();

            if (! $exists) {
                $student = Student::with('user')->find($studentId);

                $participant = SessionParticipant::create([
                    'virtual_session_id' => $this->session->id,
                    'user_id' => $student->user_id,
                    'role' => 'attendee',
                    'status' => 'invited',
                    'full_name' => $student->user->name,
                    'invited_at' => now(),
                    'invited_by' => Auth::id(),
                ]);

                // Send invitation
                $student->user->notify(new VirtualSessionInvitationNotification($this->session, $participant));
                $added++;
            }
        }

        $this->dispatch('success', "Added {$added} participant(s) successfully.");
        $this->toggleAddModal();
    }

    public function removeParticipant($participantId)
    {
        $participant = SessionParticipant::findOrFail($participantId);

        if ($participant->virtual_session_id !== $this->session->id) {
            $this->dispatch('error', 'Invalid participant.');

            return;
        }

        $participant->delete();
        $this->dispatch('success', 'Participant removed successfully.');
    }

    public function resendInvitation($participantId)
    {
        $participant = SessionParticipant::with('user')->findOrFail($participantId);

        if ($participant->virtual_session_id !== $this->session->id) {
            $this->dispatch('error', 'Invalid participant.');

            return;
        }

        $participant->user->notify(new VirtualSessionInvitationNotification($this->session, $participant));
        $this->dispatch('success', 'Invitation resent successfully.');
    }

    public function render()
    {
        $participants = $this->session->participants()
            ->with('user')
            ->when($this->search, function ($query) {
                $query->where('full_name', 'like', "%{$this->search}%")
                    ->orWhereHas('user', function ($q) {
                        $q->where('email', 'like', "%{$this->search}%");
                    });
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('invited_at', 'desc')
            ->paginate(20);

        // Get available students to add
        $availableStudents = collect();
        if ($this->showAddModal) {
            $existingUserIds = $this->session->participants()->pluck('user_id')->toArray();

            $query = Student::with('user')
                ->whereNotIn('user_id', $existingUserIds);

            if ($this->session->academic_level_id) {
                $query->where('academic_level_id', $this->session->academic_level_id);
            }

            if ($this->session->academic_group_id) {
                $query->where('academic_group_id', $this->session->academic_group_id);
            }

            $availableStudents = $query->get();
        }

        return view('livewire.teachers.virtual-classroom.session-participants', [
            'participants' => $participants,
            'availableStudents' => $availableStudents,
        ]);
    }
}
