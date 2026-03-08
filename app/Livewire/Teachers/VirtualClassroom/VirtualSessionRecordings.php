<?php

namespace App\Livewire\Teachers\VirtualClassroom;

use App\Models\Classroom\SessionRecording;
use App\Models\Classroom\VirtualSession;
use App\Services\BigBlueButtonService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class VirtualSessionRecordings extends Component
{
    use WithPagination;

    public VirtualSession $session;

    public $search = '';

    public $syncing = false;

    public function mount(VirtualSession $session)
    {
        // Authorize
        if ($session->teacher_id !== Auth::user()->teacher->id) {
            abort(403, 'Unauthorized');
        }

        $this->session = $session;
    }

    public function syncRecordings()
    {
        $this->syncing = true;

        try {
            $bbbService = app(BigBlueButtonService::class);
            $count = $bbbService->syncRecordings($this->session);

            $this->dispatch('success', "Synced {$count} recording(s).");
        } catch (\Exception $e) {
            $this->dispatch('error', 'Failed to sync recordings: '.$e->getMessage());
        } finally {
            $this->syncing = false;
        }
    }

    public function publishRecording($recordingId)
    {
        $recording = SessionRecording::findOrFail($recordingId);

        try {
            $bbbService = app(BigBlueButtonService::class);

            if ($bbbService->publishRecording($recording->recording_id, true)) {
                $recording->update([
                    'status' => 'published',
                    'published_at' => now(),
                ]);

                $this->dispatch('success', 'Recording published successfully.');
            } else {
                $this->dispatch('error', 'Failed to publish recording.');
            }
        } catch (\Exception $e) {
            $this->dispatch('error', 'Error: '.$e->getMessage());
        }
    }

    public function unpublishRecording($recordingId)
    {
        $recording = SessionRecording::findOrFail($recordingId);

        try {
            $bbbService = app(BigBlueButtonService::class);

            if ($bbbService->publishRecording($recording->recording_id, false)) {
                $recording->update([
                    'status' => 'unpublished',
                    'published_at' => null,
                ]);

                $this->dispatch('success', 'Recording unpublished successfully.');
            } else {
                $this->dispatch('error', 'Failed to unpublish recording.');
            }
        } catch (\Exception $e) {
            $this->dispatch('error', 'Error: '.$e->getMessage());
        }
    }

    public function deleteRecording($recordingId)
    {
        if (! confirm('Are you sure you want to delete this recording? This action cannot be undone.')) {
            return;
        }

        $recording = SessionRecording::findOrFail($recordingId);
        $recordingName = $recording->name;
        $sessionTitle = $recording->session?->title ?? 'Unknown Session';

        try {
            $bbbService = app(BigBlueButtonService::class);

            if ($bbbService->deleteRecording($recording->recording_id)) {
                $recording->update(['status' => 'deleted']);
                $recording->delete();

                // Log activity
                SessionRecording::logActivityForModel('delete', 'Session Recording Deleted', 'session_recording', [
                    'recording_name' => $recordingName,
                    'session_title' => $sessionTitle,
                    'recording_id' => $recordingId,
                    'deleted_by' => auth()->user()?->name ?? 'Unknown',
                ]);

                $this->dispatch('success', 'Recording deleted successfully.');
            } else {
                $this->dispatch('error', 'Failed to delete recording.');
            }
        } catch (\Exception $e) {
            $this->dispatch('error', 'Error: '.$e->getMessage());
        }
    }

    public function render()
    {
        $recordings = $this->session->recordings()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%");
            })
            ->orderBy('recorded_at', 'desc')
            ->paginate(10);

        return view('livewire.teachers.virtual-classroom.session-recordings', [
            'recordings' => $recordings,
        ]);
    }
}
