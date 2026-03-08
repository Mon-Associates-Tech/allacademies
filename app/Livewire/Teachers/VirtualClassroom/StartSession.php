<?php

namespace App\Livewire\Teachers\VirtualClassroom;

use App\Models\Classroom\VirtualSession;
use App\Services\BigBlueButtonService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StartSession extends Component
{
    public VirtualSession $session;

    public $isCreating = false;

    public $error = null;

    public $joinUrl = null;

    public $meetingInfo = null;

    public function mount(VirtualSession $session)
    {
        // Authorize
        if ($session->teacher_id !== Auth::user()->teacher->id) {
            abort(403, 'Unauthorized');
        }

        $this->session = $session;

        // Check if already created in BBB
        if ($this->session->isLive()) {
            $this->checkMeetingStatus();
        }
    }

    public function startMeeting()
    {
        $this->isCreating = true;
        $this->error = null;

        try {
            $bbbService = app(BigBlueButtonService::class);

            // Create meeting in BBB
            $result = $bbbService->createMeeting($this->session);

            if ($result['success']) {
                // Update session
                $this->session->update([
                    'status' => 'live',
                    'actual_start' => now(),
                    'internal_meeting_id' => $result['internal_meeting_id'],
                    'bbb_create_response' => $result['raw_response'] ?? null,
                ]);

                // Get moderator join URL
                $this->joinUrl = $bbbService->getModeratorJoinUrl($this->session);

                $this->dispatch('success', 'Meeting created successfully!');

                // Optionally auto-join
                // return redirect()->away($this->joinUrl);
            } else {
                $this->error = $result['message'] ?? 'Failed to create meeting';
            }
        } catch (\Exception $e) {
            $this->error = 'Error: '.$e->getMessage();
        } finally {
            $this->isCreating = false;
        }
    }

    public function joinMeeting()
    {
        if (! $this->joinUrl) {
            $bbbService = app(BigBlueButtonService::class);
            $this->joinUrl = $bbbService->getModeratorJoinUrl($this->session);
        }

        return redirect()->away($this->joinUrl);
    }

    public function endMeeting()
    {
        try {
            $bbbService = app(BigBlueButtonService::class);

            if ($bbbService->endMeeting($this->session)) {
                $this->session->update([
                    'status' => 'ended',
                    'actual_end' => now(),
                ]);

                session()->flash('success', 'Meeting ended successfully.');

                return redirect()->route('teachers.classroom');
            } else {
                $this->error = 'Failed to end meeting';
            }
        } catch (\Exception $e) {
            $this->error = 'Error: '.$e->getMessage();
        }
    }

    public function checkMeetingStatus()
    {
        try {
            $bbbService = app(BigBlueButtonService::class);

            if ($bbbService->isMeetingRunning($this->session->meeting_id)) {
                $this->joinUrl = $bbbService->getModeratorJoinUrl($this->session);

                // Get meeting info
                $this->meetingInfo = $bbbService->getMeetingInfo($this->session->meeting_id);
            } else {
                // Meeting not running anymore
                if ($this->session->isLive()) {
                    $this->session->update([
                        'status' => 'ended',
                        'actual_end' => now(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            $this->error = 'Error checking meeting status: '.$e->getMessage();
        }
    }

    public function refreshMeetingInfo()
    {
        if ($this->session->isLive()) {
            $this->checkMeetingStatus();
        }
    }

    public function render()
    {
        return view('livewire.teachers.virtual-classroom.start-session');
    }
}
