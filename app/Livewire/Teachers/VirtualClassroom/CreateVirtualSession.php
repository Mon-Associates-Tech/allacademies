<?php

namespace App\Livewire\Teachers\VirtualClassroom;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\Classroom\SessionParticipant;
use App\Models\Classroom\VirtualSession;
use App\Models\Student;
use App\Notifications\VirtualSessionInvitationNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CreateVirtualSession extends Component
{
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

    // Participants
    public $selectedStudents = [];
    public $inviteAllStudents = true;

    // UI State
    public $currentStep = 1;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'scheduled_date' => 'required|date|after_or_equal:today',
        'scheduled_time' => 'required',
        'duration' => 'required|integer|min:15|max:180',
        'academic_level_id' => 'nullable|exists:academic_levels,id',
        'academic_group_id' => 'nullable|exists:academic_groups,id',
        'academic_subject_id' => 'nullable|exists:academic_subjects,id',
    ];

    public function mount()
    {
        $this->scheduled_date = now()->addDay()->format('Y-m-d');
        $this->scheduled_time = '10:00';
    }

    public function nextStep()
    {
        $this->validateCurrentStep();
        $this->currentStep++;
    }

    public function previousStep()
    {
        $this->currentStep--;
    }

    protected function validateCurrentStep()
    {
        switch ($this->currentStep) {
            case 1:
                $this->validate([
                    'title' => 'required|string|max:255',
                    'description' => 'nullable|string',
                    'scheduled_date' => 'required|date|after_or_equal:today',
                    'scheduled_time' => 'required',
                    'duration' => 'required|integer|min:15|max:180',
                ]);
                break;
            case 2:
                // Academic context is optional
                break;
        }
    }

    public function updatedAcademicLevelId($value)
    {
        $this->academic_group_id = null;
        $this->selectedStudents = [];
    }

    public function updatedAcademicGroupId($value)
    {
        $this->selectedStudents = [];
    }

    public function updatedInviteAllStudents($value)
    {
        if ($value) {
            $this->selectedStudents = [];
        }
    }

    public function createSession()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            $teacher = Auth::user()->teacher;
            $scheduledStart = $this->scheduled_date . ' ' . $this->scheduled_time;
            $scheduledEnd = date('Y-m-d H:i:s', strtotime($scheduledStart) + ($this->duration * 60));

            // Create session
            $session = VirtualSession::create([
                'school_id' => Auth::user()->school_id,
                'teacher_id' => $teacher->id,
                'academic_level_id' => $this->academic_level_id,
                'academic_group_id' => $this->academic_group_id,
                'academic_subject_id' => $this->academic_subject_id,
                'title' => $this->title,
                'description' => $this->description,
                'type' => $this->type,
                'status' => 'scheduled',
                'scheduled_start' => $scheduledStart,
                'scheduled_end' => $scheduledEnd,
                'duration_minutes' => $this->duration,
                'allow_guest_login' => $this->allow_guest_login,
                'auto_record' => $this->auto_record,
                'mute_on_start' => $this->mute_on_start,
                'webcams_only_for_moderator' => $this->webcams_only_for_moderator,
                'max_participants' => $this->max_participants,
                'guest_policy' => $this->guest_policy,
                'meeting_id' => 'session-' . time() . '-' . rand(1000, 9999),
                'attendee_password' => bin2hex(random_bytes(6)),
                'moderator_password' => bin2hex(random_bytes(6)),
            ]);

            // Add participants
            $this->addParticipants($session);

            DB::commit();

            session()->flash('success', 'Virtual session created successfully!');
            return redirect()->route('teachers.classroom.index');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('error', 'Failed to create session: ' . $e->getMessage());
        }
    }

    protected function addParticipants(VirtualSession $session)
    {
        $students = $this->getStudentsToInvite();

        foreach ($students as $student) {
            $participant = SessionParticipant::create([
                'virtual_session_id' => $session->id,
                'user_id' => $student->user_id,
                'role' => 'attendee',
                'status' => 'invited',
                'full_name' => $student->user->name,
                'invited_at' => now(),
                'invited_by' => Auth::id(),
            ]);

            // Send invitation notification
            $student->user->notify(new VirtualSessionInvitationNotification($session, $participant));
        }
    }

    protected function getStudentsToInvite()
    {
        if (!$this->inviteAllStudents && !empty($this->selectedStudents)) {
            return Student::whereIn('id', $this->selectedStudents)->with('user')->get();
        }

        $query = Student::query()->with('user');

        if ($this->academic_level_id) {
            $query->where('academic_level_id', $this->academic_level_id);
        }

        if ($this->academic_group_id) {
            $query->where('academic_group_id', $this->academic_group_id);
        }

        if (!$this->academic_level_id && !$this->academic_group_id) {
            // Get all students assigned to this teacher
            $teacher = Auth::user()->teacher;
            $query->whereHas('teachers', function ($q) use ($teacher) {
                $q->where('teachers.id', $teacher->id);
            });
        }

        return $query->get();
    }

    public function render()
    {
        $academicLevels = AcademicLevel::where('school_id', Auth::user()->school_id)
            ->orderBy('name')
            ->get();

        $academicGroups = [];
        if ($this->academic_level_id) {
            $academicGroups = AcademicGroup::where('school_id', Auth::user()->school_id)
                ->orderBy('name')
                ->get();
        }

        $academicSubjects = AcademicSubject::where('school_id', Auth::user()->school_id)
            ->orderBy('name')
            ->get();

        $students = [];
        if (!$this->inviteAllStudents) {
            $students = $this->getAvailableStudents();
        }

        return view('livewire.teachers.virtual-classroom.create-session', [
            'academicLevels' => $academicLevels,
            'academicGroups' => $academicGroups,
            'academicSubjects' => $academicSubjects,
            'students' => $students,
        ]);
    }

    protected function getAvailableStudents()
    {
        $query = Student::query()->with('user');

        if ($this->academic_level_id) {
            $query->where('academic_level_id', $this->academic_level_id);
        }

        if ($this->academic_group_id) {
            $query->where('academic_group_id', $this->academic_group_id);
        }

        return $query->get();
    }
}
