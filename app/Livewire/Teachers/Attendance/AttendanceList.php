<?php

namespace App\Livewire\Teachers\Attendance;

use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\Attendance\Attendance;
use App\Models\Teacher;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceList extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortBy = 'date';
    public $sortDirection = 'desc';
    public $studentPerPage = 10;
    public $expandedAttendance = null;
    public $dateFrom;
    public $dateTo;
    public $selectedLevel;
    public $selectedSubject;
    public $selectedSession = '';
    public $attendanceRate;
    public $academicLevels;
    public ?Teacher $teacher;

    // Available Options
    public $subjects;
    public $sessions = ['morning', 'afternoon', 'full_day'];
    public $attendanceRates = [
        ['value' => '', 'label' => 'All Rates'],
        ['value' => '100', 'label' => '100% Attendance'],
        ['value' => '75', 'label' => '≥ 75% Attendance'],
        ['value' => '50', 'label' => '≥ 50% Attendance'],
        ['value' => '25', 'label' => '≥ 25% Attendance'],
        ['value' => '0', 'label' => '< 25% Attendance'],
    ];
    protected $paginationTheme = 'tailwind';
    protected $queryString = [
        'search' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'selectedLevel' => ['except' => ''],
        'selectedSubject' => ['except' => ''],
        'selectedSession' => ['except' => ''],
        'attendanceRate' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function toggleExpanded($attendanceId)
    {
        if ($this->expandedAttendance === $attendanceId) {
            $this->expandedAttendance = null;
        } else {
            $this->expandedAttendance = $attendanceId;
            $this->resetPage('students');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortBy = $field;
    }

    public function editAttendance($attendanceId)
    {
        return redirect()->route('teachers.attendance.take', ['attendance' => $attendanceId]);
    }

    public function mount()
    {
        $this->teacher = auth()->user()->teacher;

        if(!$this->teacher){
            $this->teacher = Teacher::withoutGlobalScopes()->where('user_id', auth()->user()->id)->first();
        }
        // Set default date range to current month
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');

        // Load academic levels assigned to the teacher
        $this->academicLevels = $this->teacher->academicLevels;

        // Initialize subjects as empty collection
        $this->subjects = collect();

        // If there's a selected level, load its subjects
        if ($this->selectedLevel) {
            $this->loadSubjectsForLevel($this->selectedLevel);
        }
    }

    public function loadSubjectsForLevel($levelId)
    {
        // Get subjects that are both assigned to the teacher and belong to the selected level
        $this->subjects = $this->teacher
            ->academicSubjects()
            ->where('academic_level_id', $levelId)
            ->orderBy('name')
            ->get();
    }

    public function updatedSelectedLevel($value)
    {
        if ($value) {
            $this->loadSubjectsForLevel($value);
        } else {
            $this->subjects = collect();
        }
        $this->selectedSubject = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = Attendance::where('teacher_id', $this->teacher->id)
            ->with([
                'academicLevel',
                'academicSubject',
                'attendanceRecords' => function ($query) {
                    $query->withCount(['student']);
                },
                'attendanceRecords.student.user'
            ]);

        // Apply filters
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('academicLevel', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })->orWhereHas('academicSubject', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })->orWhereHas('attendanceRecords.student.user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            });
        }

        if ($this->dateFrom) {
            $query->whereDate('date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('date', '<=', $this->dateTo);
        }

        if ($this->selectedLevel) {
            $query->where('academic_level_id', $this->selectedLevel);
        }

        if ($this->selectedSubject) {
            $query->where('academic_subject_id', $this->selectedSubject);
        }

        if ($this->selectedSession) {
            $query->where('session', $this->selectedSession);
        }

        if ($this->attendanceRate) {
            $rate = (int)$this->attendanceRate;
            $query->whereHas('attendanceRecords', function ($q) use ($rate) {
                $q->havingRaw('COUNT(CASE WHEN status = "present" THEN 1 END) * 100 / COUNT(*) >= ?', [$rate]);
            });
        }

        $query->orderBy($this->sortBy, $this->sortDirection);
        $attendances = $query->paginate($this->perPage);

        // Get paginated students for expanded attendance
        $paginatedStudents = null;
        if ($this->expandedAttendance) {
            $attendance = Attendance::find($this->expandedAttendance);
            if ($attendance) {
                $paginatedStudents = $attendance->attendanceRecords()
                    ->with('student.user')
                    ->when($this->search, function ($query) {
                        $query->whereHas('student.user', function ($q) {
                            $q->where('name', 'like', '%' . $this->search . '%');
                        });
                    })
                    ->paginate($this->studentPerPage, ['*'], 'students');
            }
        }

        return view('livewire.teachers.attendance.list', [
            'attendances' => $attendances,
            'paginatedStudents' => $paginatedStudents,
        ]);
    }
}
