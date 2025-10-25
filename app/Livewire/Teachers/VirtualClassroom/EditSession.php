<?php

namespace App\Livewire\Teachers\VirtualClassroom;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\Classroom\VirtualSession;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EditSession extends Component
{
    public VirtualSession $session;

    // Session Details
    public $title = '';
    public $description = '';
    public $type = 'live';

    // Scheduling
    public $scheduled_date = '';
    public $scheduled_time = '';
    public $duration = 60;

    // Academic Context
    public $academic_level_id = null;
    public $academic_group_id = null;
    public $academic_subject_id = null;

    // Settings
    public $allow_guest_login = false;
    public $auto_record = false;
    public $mute_on_start = false;
    public $webcams_only_for_moderator = false;
    public $max_participants = 100;
    public $guest_policy = 'ASK_MODERATOR';

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'scheduled_date' => 'required|date',
        'scheduled_time' => 'required',
        'duration' => 'required|integer|min:15|max:180',
        'academic_level_id' => 'nullable|exists:academic_levels,id',
        'academic_group_id' => 'nullable|exists:academic_groups,id',
        'academic_subject_id' => 'nullable|exists:academic_subjects,id',
    ];

    public function mount(VirtualSession $session)
    {
        // Authorize
        if ($session->teacher_id !== Auth::user()->teacher->id) {
            abort(403, 'Unauthorized');
        }

        // Can't edit live or ended sessions
        if (in_array($session->status, ['live', 'ended'])) {
            session()->flash('error', 'Cannot edit a live or ended session.');
            return redirect()->route('teachers.classroom.show', $session);
        }

        $this->session = $session;

        // Populate form fields
        $this->title = $session->title;
        $this->description = $session->description;
        $this->type = $session->type;
        $this->scheduled_date = $session->scheduled_start->format('Y-m-d');
        $this->scheduled_time = $session->scheduled_start->format('H:i');
        $this->duration = $session->duration_minutes;
        $this->academic_level_id = $session->academic_level_id;
        $this->academic_group_id = $session->academic_group_id;
        $this->academic_subject_id = $session->academic_subject_id;
        $this->allow_guest_login = $session->allow_guest_login;
        $this->auto_record = $session->auto_record;
        $this->mute_on_start = $session->mute_on_start;
        $this->webcams_only_for_moderator = $session->webcams_only_for_moderator;
        $this->max_participants = $session->max_participants;
        $this->guest_policy = $session->guest_policy;
    }

    public function updateSession()
    {
        $this->validate();

        try {
            $scheduledStart = $this->scheduled_date . ' ' . $this->scheduled_time;
            $scheduledEnd = date('Y-m-d H:i:s', strtotime($scheduledStart) + ($this->duration * 60));

            $this->session->update([
                'title' => $this->title,
                'description' => $this->description,
                'type' => $this->type,
                'scheduled_start' => $scheduledStart,
                'scheduled_end' => $scheduledEnd,
                'duration_minutes' => $this->duration,
                'academic_level_id' => $this->academic_level_id,
                'academic_group_id' => $this->academic_group_id,
                'academic_subject_id' => $this->academic_subject_id,
                'allow_guest_login' => $this->allow_guest_login,
                'auto_record' => $this->auto_record,
                'mute_on_start' => $this->mute_on_start,
                'webcams_only_for_moderator' => $this->webcams_only_for_moderator,
                'max_participants' => $this->max_participants,
                'guest_policy' => $this->guest_policy,
            ]);

            session()->flash('success', 'Session updated successfully!');
            return redirect()->route('teachers.classroom.show', $this->session);

        } catch (\Exception $e) {
            $this->dispatch('error', 'Failed to update session: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $academicLevels = AcademicLevel::where('school_id', Auth::user()->school_id)
            ->orderBy('name')
            ->get();

        $academicGroups = AcademicGroup::where('school_id', Auth::user()->school_id)
            ->orderBy('name')
            ->get();

        $academicSubjects = AcademicSubject::where('school_id', Auth::user()->school_id)
            ->orderBy('name')
            ->get();

        return view('livewire.teachers.virtual-classroom.edit-session', [
            'academicLevels' => $academicLevels,
            'academicGroups' => $academicGroups,
            'academicSubjects' => $academicSubjects,
        ]);
    }
}
