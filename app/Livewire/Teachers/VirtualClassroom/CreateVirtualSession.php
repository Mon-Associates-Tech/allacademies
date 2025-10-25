<?php

namespace App\Livewire\Teachers\VirtualClassroom;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\Classroom\SessionParticipant;
use App\Models\Classroom\VirtualSession;
use App\Models\Student;
use App\Notifications\VirtualSessionInvitationNotification;
use Carbon\Carbon;
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

    // Recurring fields
    public $is_recurring = false;
    public $recurrence_pattern = 'weekly'; // daily, weekly, monthly
    public $recurrence_interval = 1;
    public $recurrence_days = []; // For weekly: [1,2,3,4,5] = Mon-Fri
    public $recurrence_end_type = 'never'; // never, on_date, after_occurrences
    public $recurrence_end_date = '';
    public $recurrence_occurrences = 10;

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
        'recurrence_pattern' => 'required_if:is_recurring,true|in:daily,weekly,monthly',
        'recurrence_interval' => 'required_if:is_recurring,true|integer|min:1|max:12',
        'recurrence_days' => 'required_if:recurrence_pattern,weekly|array',
        'recurrence_end_date' => 'nullable|date|after:scheduled_date',
        'recurrence_occurrences' => 'nullable|integer|min:1|max:52',
    ];

    public function mount()
    {
        $this->scheduled_date = now()->addDay()->format('Y-m-d');
        $this->scheduled_time = '10:00';
        $this->recurrence_end_date = now()->addMonths(3)->format('Y-m-d');
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
                    'is_recurring' => 'boolean',
                ]);

                if ($this->is_recurring) {
                    $this->validate([
                        'recurrence_pattern' => 'required|in:daily,weekly,monthly',
                        'recurrence_interval' => 'required|integer|min:1|max:12',
                    ]);

                    if ($this->recurrence_pattern === 'weekly') {
                        $this->validate([
                            'recurrence_days' => 'required|array|min:1',
                        ]);
                    }
                }
                break;
            case 2:
                // Academic context is optional
                break;
        }
    }

    public function updatedIsRecurring($value)
    {
        if (!$value) {
            $this->recurrence_pattern = 'weekly';
            $this->recurrence_interval = 1;
            $this->recurrence_days = [];
            $this->recurrence_end_type = 'never';
        } else {
            // Set default recurrence day to the scheduled day
            $dayOfWeek = Carbon::parse($this->scheduled_date)->dayOfWeekIso;
            $this->recurrence_days = [$dayOfWeek];
        }
    }

    public function updatedRecurrencePattern($value)
    {
        if ($value !== 'weekly') {
            $this->recurrence_days = [];
        } else {
            // Set default to scheduled day
            $dayOfWeek = Carbon::parse($this->scheduled_date)->dayOfWeekIso;
            $this->recurrence_days = [$dayOfWeek];
        }
    }

    public function updatedAcademicGroupId($value)
    {
        $this->academic_level_id = null;
        $this->academic_subject_id = null;
        $this->selectedStudents = [];
    }

    public function updatedAcademicLevelId($value)
    {
        $this->academic_subject_id = null;
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

            if ($this->is_recurring) {
                $this->createRecurringSessions($teacher);
            } else {
                $this->createSingleSession($teacher);
            }

            DB::commit();

            $message = $this->is_recurring
                ? 'Recurring virtual sessions created successfully!'
                : 'Virtual session created successfully!';

            session()->flash('success', $message);
            return redirect()->route('teachers.classroom.index');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('error', 'Failed to create session: ' . $e->getMessage());
        }
    }

    protected function createSingleSession($teacher, $scheduledStart = null, $parentSessionId = null)
    {
        if (!$scheduledStart) {
            $scheduledStart = $this->scheduled_date . ' ' . $this->scheduled_time;
        }

        $scheduledEnd = Carbon::parse($scheduledStart)->addMinutes($this->duration);

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
            'parent_session_id' => $parentSessionId,
            'is_recurring' => false,
        ]);

        // Add participants
        $this->addParticipants($session);

        return $session;
    }

    protected function createRecurringSessions($teacher)
    {
        $scheduledStart = Carbon::parse($this->scheduled_date . ' ' . $this->scheduled_time);

        // Determine end date
        $endDate = $this->getRecurrenceEndDate($scheduledStart);

        // Create parent session
        $parentSession = VirtualSession::create([
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
            'scheduled_end' => Carbon::parse($scheduledStart)->addMinutes($this->duration),
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
            'is_recurring' => true,
            'recurrence_pattern' => $this->recurrence_pattern,
            'recurrence_interval' => $this->recurrence_interval,
            'recurrence_days' => $this->recurrence_pattern === 'weekly' ? $this->recurrence_days : null,
            'recurrence_end_date' => $endDate,
            'recurrence_active' => true,
        ]);

        // Add participants to parent
        $this->addParticipants($parentSession);

        // Generate child sessions (up to next 8 weeks to avoid too many at once)
        $this->generateChildSessions($parentSession, min($endDate, now()->addWeeks(8)));
    }

    protected function getRecurrenceEndDate(Carbon $startDate): ?Carbon
    {
        return match($this->recurrence_end_type) {
            'on_date' => Carbon::parse($this->recurrence_end_date)->endOfDay(),
            'after_occurrences' => $this->calculateEndDateFromOccurrences($startDate),
            default => null, // never ends
        };
    }

    protected function calculateEndDateFromOccurrences(Carbon $startDate): Carbon
    {
        $occurrences = $this->recurrence_occurrences;
        $date = $startDate->copy();

        for ($i = 1; $i < $occurrences; $i++) {
            $date = $this->getNextOccurrence($date);
        }

        return $date;
    }

    protected function generateChildSessions(VirtualSession $parent, ?Carbon $untilDate)
    {
        $currentDate = Carbon::parse($parent->scheduled_start);
        $generatedCount = 0;
        $maxGenerate = 100; // Safety limit

        while ($generatedCount < $maxGenerate) {
            $currentDate = $this->getNextOccurrence($currentDate);

            // Stop if we've reached the end date
            if ($untilDate && $currentDate->gt($untilDate)) {
                break;
            }

            // Create child session
            $this->createSingleSession(
                $parent->teacher,
                $currentDate->format('Y-m-d H:i:s'),
                $parent->id
            );

            $generatedCount++;
        }
    }

    protected function getNextOccurrence(Carbon $date): Carbon
    {
        $next = $date->copy();

        switch ($this->recurrence_pattern) {
            case 'daily':
                $next->addDays($this->recurrence_interval);
                break;

            case 'weekly':
                // Find next occurrence based on selected days
                $currentDayOfWeek = $next->dayOfWeekIso;
                $found = false;

                for ($i = 1; $i <= 7; $i++) {
                    $next->addDay();
                    $nextDayOfWeek = $next->dayOfWeekIso;

                    if (in_array($nextDayOfWeek, $this->recurrence_days)) {
                        $found = true;
                        break;
                    }
                }

                // If we've cycled through the week, add interval weeks
                if (!$found || ($this->recurrence_interval > 1 && $next->dayOfWeekIso <= $currentDayOfWeek)) {
                    $next->addWeeks($this->recurrence_interval - 1);
                }
                break;

            case 'monthly':
                $next->addMonths($this->recurrence_interval);
                break;
        }

        return $next;
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

            // Send invitation notification (only for upcoming sessions)
            if ($session->scheduled_start->isFuture() && $session->scheduled_start->lte(now()->addWeeks(2))) {
                $student->user->notify(new VirtualSessionInvitationNotification($session, $participant));
            }
        }
    }

    protected function getStudentsToInvite()
    {
        $schoolId = Auth::user()->school_id;

        if (!$this->inviteAllStudents && !empty($this->selectedStudents)) {
            return Student::whereIn('id', $this->selectedStudents)
                ->where('school_id', $schoolId)
                ->with('user')
                ->get();
        }

        $query = Student::query()
            ->where('school_id', $schoolId)
            ->where('status', 'active')
            ->with('user');

        if ($this->academic_level_id) {
            $query->where('academic_level_id', $this->academic_level_id);
        }

        if ($this->academic_group_id) {
            $query->where('academic_group_id', $this->academic_group_id);
        }

        if (!$this->academic_level_id && !$this->academic_group_id) {
            $teacher = Auth::user()->teacher;
            $query->whereHas('teachers', function ($q) use ($teacher) {
                $q->where('teachers.id', $teacher->id);
            });
        }

        return $query->get();
    }

    public function render()
    {
        $teacher = Auth::user()->teacher;
        $schoolId = Auth::user()->school_id;

        $academicGroups = $teacher->academicGroups()
            ->whereHas('schools', function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId)
                    ->where('is_active', true);
            })
            ->orderBy('name')
            ->get();

        $academicLevels = collect();
        if ($this->academic_group_id) {
            $academicLevels = AcademicLevel::where('academic_group_id', $this->academic_group_id)
                ->whereHas('schools', function ($query) use ($schoolId) {
                    $query->where('school_id', $schoolId)
                        ->where('is_active', true);
                })
                ->orderBy('name')
                ->get();
        } else {
            $academicLevels = $teacher->academicLevels()
                ->whereHas('schools', function ($query) use ($schoolId) {
                    $query->where('school_id', $schoolId)
                        ->where('is_active', true);
                })
                ->orderBy('name')
                ->get();
        }

        $academicSubjects = collect();
        if ($this->academic_level_id) {
            $academicSubjects = AcademicSubject::where('academic_level_id', $this->academic_level_id)
                ->orderBy('name')
                ->get();
        } else {
            $academicSubjects = $teacher->subjects()
                ->orderBy('name')
                ->get();
        }

        $students = [];
        if (!$this->inviteAllStudents) {
            $students = $this->getAvailableStudents();
        }

        return view('livewire.teachers.virtual-classroom.create-session', [
            'academicGroups' => $academicGroups,
            'academicLevels' => $academicLevels,
            'academicSubjects' => $academicSubjects,
            'students' => $students,
        ]);
    }

    protected function getAvailableStudents()
    {
        $schoolId = Auth::user()->school_id;

        $query = Student::query()
//            ->where('school_id', $schoolId)
            ->where('status', 'active')
            ->with('user');

        if ($this->academic_level_id) {
            $query->where('academic_level_id', $this->academic_level_id);
        }

        if ($this->academic_group_id) {
            $query->where('academic_group_id', $this->academic_group_id);
        }

        return $query->get();
    }
}
