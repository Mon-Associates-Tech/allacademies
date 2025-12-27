<?php

namespace App\Livewire\Students\VirtualClassroom;

use App\Models\Classroom\SessionRecording;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ViewSessionRecordings extends Component
{
    use WithPagination;

    public $search = '';
    public $subject_filter = null;

    public function render()
    {
        $recordings = SessionRecording::query()
            ->where('status', 'published')
            ->whereHas('virtualSession.participants', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%");
            })
            ->when($this->subject_filter, function ($query) {
                $query->whereHas('virtualSession', function ($q) {
                    $q->where('academic_subject_id', $this->subject_filter);
                });
            })
            ->with(['virtualSession.teacher.user', 'virtualSession.academicSubject'])
            ->orderBy('recorded_at', 'desc')
            ->paginate(12);

        return view('livewire.students.virtual-classroom.view-session-recordings', [
            'recordings' => $recordings,
        ]);
    }
}
