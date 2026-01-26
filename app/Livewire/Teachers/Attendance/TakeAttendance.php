<?php

namespace App\Livewire\Teachers\Attendance;

use App\Models\AcademicSubject;
use App\Models\Attendance\Attendance;
use App\Models\Attendance\AttendanceRecord;
use Livewire\Component;

class TakeAttendance extends Component
{
    public $academicLevelId;

    public $academicSubjectId;

    public $date;

    public $session = 'morning';

    public $students = [];

    public $academicLevels = [];

    public $subjects = [];

    public $selectedStudents = [];

    public $attendanceId;

    public $searchQuery = '';

    public $allStudents = []; // Store all students for filtering

    protected $queryString = ['searchQuery'];

    protected $rules = [
        'academicLevelId' => 'required|exists:academic_levels,id',
        'academicSubjectId' => 'nullable|exists:academic_subjects,id',
        'date' => 'required|date',
        'session' => 'required|in:morning,afternoon,full_day',
        'selectedStudents' => 'required|array',
    ];

    public function mount($attendance = null)
    {
        $this->date = now()->format('Y-m-d');
        $this->academicLevels = auth()->user()->teacher->academicLevels->toArray();

        if ($attendance) {
            $this->loadExistingAttendance($attendance);
        }
    }

    public function updatedSearchQuery()
    {
        if ($this->allStudents) {
            $this->filterStudents();
        }
    }

    protected function filterStudents()
    {
        $searchQuery = strtolower($this->searchQuery);

        $this->students = collect($this->allStudents)
            ->filter(function ($student) use ($searchQuery) {
                return str_contains(strtolower($student['name']), $searchQuery);
            })
            ->toArray();
    }

    public function toggleAllStudents($present)
    {
        foreach ($this->students as $student) {
            $this->selectedStudents[$student['id']] = [
                'present' => $present,
                'reason' => $present ? null : $this->selectedStudents[$student['id']]['reason'] ?? null,
            ];
        }
    }

    public function studentPresenceChanged($studentId, $isPresent)
    {
        // Update the student's presence status
        $this->selectedStudents[$studentId]['present'] = $isPresent;

        // Clear reason if marked as present
        if ($isPresent) {
            $this->selectedStudents[$studentId]['reason'] = null;
        }
    }

    public function updatedAcademicLevelId($value)
    {
        if ($value) {
            $this->subjects = AcademicSubject::where('academic_level_id', $value)
                ->get()
                ->toArray();
            $this->loadStudents();
        } else {
            $this->subjects = [];
            $this->students = [];
            $this->selectedStudents = [];
            $this->allStudents = [];
        }

        $this->academicSubjectId = null;
    }

    public function loadStudents()
    {
        $students = auth()->user()->teacher
            ->assignedStudents()
            ->where('academic_level_id', $this->academicLevelId)
            ->with('user')
            ->get();

        $this->allStudents = $students->map(function ($student) {
            return [
                'id' => $student->id,
                'name' => $student->user->name,
            ];
        })->toArray();

        $this->students = $this->allStudents;

        // Initialize all students as present by default
        foreach ($this->allStudents as $student) {
            if (! isset($this->selectedStudents[$student['id']])) {
                $this->selectedStudents[$student['id']] = [
                    'present' => true,
                    'reason' => null,
                ];
            }
        }

        if ($this->searchQuery) {
            $this->filterStudents();
        }
    }

    //    public function toggleAllStudents($present)
    //    {
    //        foreach ($this->students as $student) {
    //            $this->selectedStudents[$student['id']]['present'] = $present;
    //            if ($present) {
    //                $this->selectedStudents[$student['id']]['reason'] = null;
    //            }
    //        }
    //    }

    public function toggleStudentPresence($studentId)
    {
        $this->selectedStudents[$studentId]['present'] = ! $this->selectedStudents[$studentId]['present'];
        if ($this->selectedStudents[$studentId]['present']) {
            $this->selectedStudents[$studentId]['reason'] = null;
        }
    }

    public function saveAttendance()
    {
        $this->validate();

        try {
            $attendance = Attendance::updateOrCreate(
                ['id' => $this->attendanceId],
                [
                    'teacher_id' => auth()->user()->teacher->id,
                    'academic_level_id' => $this->academicLevelId,
                    'academic_subject_id' => $this->academicSubjectId,
                    'date' => $this->date,
                    'session' => $this->session,
                ]
            );

            foreach ($this->selectedStudents as $studentId => $data) {
                AttendanceRecord::updateOrCreate(
                    [
                        'attendance_id' => $attendance->id,
                        'student_id' => $studentId,
                    ],
                    [
                        'status' => $data['present'] ? 'present' : 'absent',
                        'remarks' => $data['present'] ? null : $data['reason'],
                    ]
                );
            }

            session()->flash('message', 'Attendance saved successfully.');

            return redirect()->route('teachers.attendance.list');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to save attendance: '.$e->getMessage());
        }
    }

    public function loadExistingAttendance($attendanceId)
    {
        $attendance = Attendance::findOrFail($attendanceId);

        if ($attendance->teacher_id !== auth()->user()->teacher->id) {
            session()->flash('error', 'You do not have permission to edit this attendance.');

            return redirect()->route('teachers.attendance.list');
        }

        $this->attendanceId = $attendance->id;
        $this->academicLevelId = $attendance->academic_level_id;
        $this->academicSubjectId = $attendance->academic_subject_id;
        $this->date = $attendance->date->format('Y-m-d');
        $this->session = $attendance->session;

        $this->updatedAcademicLevelId($this->academicLevelId);

        // Load existing attendance records
        $records = $attendance->attendanceRecords->keyBy('student_id');

        foreach ($this->selectedStudents as $studentId => &$data) {
            if ($records->has($studentId)) {
                $record = $records->get($studentId);
                $data['present'] = $record->status === 'present';
                $data['reason'] = $record->remarks;
            }
        }
    }

    public function render()
    {
        return view('livewire.teachers.attendance.take');
    }
}
