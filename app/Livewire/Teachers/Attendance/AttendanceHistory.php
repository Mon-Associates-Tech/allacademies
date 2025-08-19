<?php

namespace App\Livewire\Teachers\Attendance;

use App\Models\Student;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\Attendance\AttendanceRecord;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceHistory extends Component
{
    use WithPagination;

    public $student;
    public $academicLevels;
    public $subjects;

    // Filters
    public $selectedLevel;
    public $selectedSubject;
    public $dateFrom;
    public $dateTo;
    public $statusFilter = 'all';

    public $groupBy = 'daily'; // daily, weekly, monthly
    public $perPage = 10;

    protected $queryString = [
        'selectedLevel' => ['except' => ''],
        'selectedSubject' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'groupBy' => ['except' => 'daily'],
        'page' => ['except' => 1],
    ];

    public function mount(Student $student)
    {
        $this->student = $student;
        $this->academicLevels = AcademicLevel::orderBy('name')->get();
        $this->subjects = collect();

        // Set default date range to current academic year or last 30 days
        $this->dateTo = now()->format('Y-m-d');
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
    }

    public function updatedSelectedLevel($value)
    {
        if ($value) {
            $this->subjects = AcademicSubject::where('academic_level_id', $value)
                ->orderBy('name')
                ->get();
        } else {
            $this->subjects = collect();
        }
        $this->selectedSubject = '';
        $this->resetPage();
    }

    public function getAttendanceStats()
    {
        $query = AttendanceRecord::query()
            ->join('attendances', 'attendance_records.attendance_id', '=', 'attendances.id')
            ->where('attendance_records.student_id', $this->student->id);

        if ($this->selectedLevel) {
            $query->where('attendances.academic_level_id', $this->selectedLevel);
        }

        if ($this->selectedSubject) {
            $query->where('attendances.academic_subject_id', $this->selectedSubject);
        }

        if ($this->dateFrom) {
            $query->whereDate('attendances.date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('attendances.date', '<=', $this->dateTo);
        }

        $totalSessions = $query->count();
        $presentCount = $query->clone()->where('attendance_records.status', 'present')->count();
        $absentCount = $query->clone()->where('attendance_records.status', 'absent')->count();

        return [
            'total' => $totalSessions,
            'present' => $presentCount,
            'absent' => $absentCount,
            'rate' => $totalSessions > 0 ? ($presentCount / $totalSessions) * 100 : 0
        ];
    }

    public function getAttendanceRecords()
    {
        $query = AttendanceRecord::query()
            ->with(['attendance.academicLevel', 'attendance.academicSubject'])
            ->join('attendances', 'attendance_records.attendance_id', '=', 'attendances.id')
            ->where('attendance_records.student_id', $this->student->id)
            ->select('attendance_records.*', 'attendances.date', 'attendances.session');

        if ($this->selectedLevel) {
            $query->where('attendances.academic_level_id', $this->selectedLevel);
        }

        if ($this->selectedSubject) {
            $query->where('attendances.academic_subject_id', $this->selectedSubject);
        }

        if ($this->dateFrom) {
            $query->whereDate('attendances.date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('attendances.date', '<=', $this->dateTo);
        }

        if ($this->statusFilter !== 'all') {
            $query->where('attendance_records.status', $this->statusFilter);
        }

        switch ($this->groupBy) {
            case 'weekly':
                $query->select(
                    DB::raw('YEARWEEK(attendances.date) as week'),
                    DB::raw('MIN(attendances.date) as start_date'),
                    DB::raw('MAX(attendances.date) as end_date'),
                    DB::raw('COUNT(*) as total_sessions'),
                    DB::raw('SUM(CASE WHEN attendance_records.status = "present" THEN 1 ELSE 0 END) as present_count'),
                    DB::raw('SUM(CASE WHEN attendance_records.status = "absent" THEN 1 ELSE 0 END) as absent_count')
                )
                    ->groupBy('week')
                    ->orderBy('week', 'desc');
                break;

            case 'monthly':
                $query->select(
                    DB::raw('DATE_FORMAT(attendances.date, "%Y-%m") as month'),
                    DB::raw('MIN(attendances.date) as start_date'),
                    DB::raw('MAX(attendances.date) as end_date'),
                    DB::raw('COUNT(*) as total_sessions'),
                    DB::raw('SUM(CASE WHEN attendance_records.status = "present" THEN 1 ELSE 0 END) as present_count'),
                    DB::raw('SUM(CASE WHEN attendance_records.status = "absent" THEN 1 ELSE 0 END) as absent_count')
                )
                    ->groupBy('month')
                    ->orderBy('month', 'desc');
                break;

            default: // daily
                $query->orderBy('attendances.date', 'desc')
                    ->orderBy('attendances.session');
                break;
        }

        return $query->paginate($this->perPage);
    }

    public function render()
    {
        $stats = $this->getAttendanceStats();
        $records = $this->getAttendanceRecords();

        return view('livewire.teachers.attendance.history', [
            'stats' => $stats,
            'records' => $records,
        ]);
    }
}
