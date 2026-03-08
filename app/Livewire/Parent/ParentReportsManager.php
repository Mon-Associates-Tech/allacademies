<?php

namespace App\Livewire\Parent;

use App\Livewire\AppComponent;
use App\Models\AcademicSubject;
use App\Models\AssignmentSubmission;
use App\Models\Attendance\AttendanceRecord;
use App\Models\Student;
use App\Models\StudentParent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

class ParentReportsManager extends AppComponent
{
    use WithPagination;

    public $selectedWardId = null;

    public $selectedReportType = 'performance';

    public $selectedPeriod = 'current_term';

    public $selectedSubjectId = null;

    public $generatedReport = null;

    public $showReportPreview = false;

    public $activeTab = 'generate'; // 'generate' or 'history'

    public function mount()
    {
        $wards = $this->wards;
        if ($wards->isNotEmpty()) {
            $this->selectedWardId = $wards->first()->id;
        }
    }

    public function selectWard($wardId)
    {
        $this->selectedWardId = $wardId;
        $this->resetReport();
        $this->resetPage();
    }

    public function changeTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function generateReport()
    {
        $this->startLoading();

        try {
            $this->generatedReport = $this->buildReport();
            $this->showReportPreview = true;

            // Save report to history (you can create a reports table for this)
            // $this->saveReportToHistory($this->generatedReport);

            session()->flash('success', 'Report generated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error generating report: '.$e->getMessage());
        }

        $this->endLoading();
    }

    public function downloadReport($format = 'pdf')
    {
        $this->dispatch('download-report', [
            'format' => $format,
            'data' => $this->generatedReport,
        ]);
    }

    public function printReport()
    {
        $this->dispatch('print-report', ['data' => $this->generatedReport]);
    }

    private function resetReport()
    {
        $this->generatedReport = null;
        $this->showReportPreview = false;
    }

    private function buildReport()
    {
        if (! $this->selectedWard) {
            throw new \Exception('No ward selected');
        }

        switch ($this->selectedReportType) {
            case 'performance':
                return $this->buildPerformanceReport();
            case 'attendance':
                return $this->buildAttendanceReport();
            case 'progress':
                return $this->buildProgressReport();
            case 'comprehensive':
                return $this->buildComprehensiveReport();
            default:
                throw new \Exception('Invalid report type');
        }
    }

    private function buildPerformanceReport()
    {
        $dateRange = $this->getDateRange();

        $submissions = AssignmentSubmission::where('student_id', $this->selectedWardId)
            ->with(['assignment.academicSubject'])
            ->when($this->selectedSubjectId, function ($query) {
                $query->whereHas('assignment', function ($q) {
                    $q->where('academic_subject_id', $this->selectedSubjectId);
                });
            })
            ->when($dateRange, function ($query) use ($dateRange) {
                $query->whereBetween('submitted_at', $dateRange);
            })
            ->get();

        $subjectPerformance = $submissions->groupBy(function ($submission) {
            return $submission->assignment->academic_subject_id;
        })->map(function ($subjectSubmissions) {
            $subject = $subjectSubmissions->first()->assignment->academicSubject;
            $graded = $subjectSubmissions->filter(fn ($s) => $s->status === 'graded' && $s->total_marks > 0);

            return [
                'subject' => $subject,
                'count' => $subjectSubmissions->count(),
                'average' => $graded->isEmpty() ? 0 : $graded->avg(fn ($s) => ($s->score / $s->total_marks) * 100),
                'passed' => $graded->filter(fn ($s) => ($s->score / $s->total_marks) * 100 >= 50)->count(),
                'failed' => $graded->filter(fn ($s) => ($s->score / $s->total_marks) * 100 < 50)->count(),
                'highest' => $graded->isEmpty() ? 0 : $graded->max(fn ($s) => ($s->score / $s->total_marks) * 100),
                'lowest' => $graded->isEmpty() ? 0 : $graded->min(fn ($s) => ($s->score / $s->total_marks) * 100),
            ];
        });

        $gradedSubmissions = $submissions->filter(fn ($s) => $s->status === 'graded' && $s->total_marks > 0);
        $averageScore = $gradedSubmissions->isEmpty() ? 0 : $gradedSubmissions->avg(fn ($s) => ($s->score / $s->total_marks) * 100);

        return [
            'report_type' => 'performance',
            'ward' => $this->selectedWard,
            'date_range' => [
                'start' => $dateRange ? $dateRange[0] : Carbon::now()->subYear(),
                'end' => $dateRange ? $dateRange[1] : Carbon::now(),
            ],
            'subject' => $this->selectedSubjectId ? AcademicSubject::find($this->selectedSubjectId) : null,
            'generated_at' => now(),
            'summary' => [
                'total_assignments' => $submissions->count(),
                'average_score' => $averageScore,
                'passed_count' => $gradedSubmissions->filter(fn ($s) => ($s->score / $s->total_marks) * 100 >= 50)->count(),
                'pass_rate' => $gradedSubmissions->isEmpty() ? 0 : ($gradedSubmissions->filter(fn ($s) => ($s->score / $s->total_marks) * 100 >= 50)->count() / $gradedSubmissions->count()) * 100,
            ],
            'subject_breakdown' => $subjectPerformance,
            'assessments' => $submissions->sortByDesc('submitted_at')->take(20),
        ];
    }

    private function buildAttendanceReport()
    {
        $dateRange = $this->getDateRange();

        $attendanceRecords = AttendanceRecord::where('attendance_records.student_id', $this->selectedWardId)
            ->join('attendances', 'attendance_records.attendance_id', '=', 'attendances.id')
            ->when($dateRange, function ($query) use ($dateRange) {
                $query->whereBetween('attendances.date', $dateRange);
            })
            ->select('attendance_records.*', 'attendances.date', 'attendances.session')
            ->orderBy('attendances.date', 'desc')
            ->get();

        $totalDays = $attendanceRecords->count();
        $presentDays = $attendanceRecords->where('status', 'present')->count();
        $absentDays = $attendanceRecords->where('status', 'absent')->count();
        $lateDays = $attendanceRecords->where('status', 'late')->count();
        $attendanceRate = $totalDays > 0 ? ($presentDays / $totalDays) * 100 : 0;

        // Monthly breakdown
        $monthlyBreakdown = $attendanceRecords->groupBy(function ($record) {
            return Carbon::parse($record->date)->format('Y-m');
        })->map(function ($records, $month) {
            return [
                'month' => Carbon::createFromFormat('Y-m', $month)->format('F Y'),
                'total' => $records->count(),
                'present' => $records->where('status', 'present')->count(),
                'absent' => $records->where('status', 'absent')->count(),
                'late' => $records->where('status', 'late')->count(),
                'rate' => $records->count() > 0 ? ($records->where('status', 'present')->count() / $records->count()) * 100 : 0,
            ];
        });

        return [
            'report_type' => 'attendance',
            'ward' => $this->selectedWard,
            'date_range' => [
                'start' => $dateRange ? $dateRange[0] : Carbon::now()->subYear(),
                'end' => $dateRange ? $dateRange[1] : Carbon::now(),
            ],
            'generated_at' => now(),
            'summary' => [
                'total_days' => $totalDays,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'late_days' => $lateDays,
                'attendance_rate' => $attendanceRate,
            ],
            'monthly_breakdown' => $monthlyBreakdown,
            'recent_records' => $attendanceRecords->take(30),
        ];
    }

    private function buildProgressReport()
    {
        $dateRange = $this->getDateRange();

        $submissions = AssignmentSubmission::where('student_id', $this->selectedWardId)
            ->with(['assignment.academicSubject'])
            ->when($dateRange, function ($query) use ($dateRange) {
                $query->whereBetween('submitted_at', $dateRange);
            })
            ->orderBy('submitted_at')
            ->get();

        $progressData = $submissions->groupBy(function ($submission) {
            return $submission->assignment->academic_subject_id;
        })->map(function ($subjectSubmissions) {
            $subject = $subjectSubmissions->first()->assignment->academicSubject;
            $progressPoints = $subjectSubmissions->map(function ($submission) {
                return [
                    'date' => $submission->submitted_at->format('Y-m-d'),
                    'score' => $submission->total_marks > 0 ? ($submission->score / $submission->total_marks) * 100 : 0,
                    'title' => $submission->assignment->title,
                ];
            });

            $scores = $progressPoints->pluck('score');
            $trend = $this->calculateTrend($scores);

            return [
                'subject' => $subject,
                'progress_points' => $progressPoints,
                'trend' => $trend,
                'improvement' => $scores->count() > 1 ? ($scores->last() - $scores->first()) : 0,
            ];
        });

        return [
            'report_type' => 'progress',
            'ward' => $this->selectedWard,
            'date_range' => [
                'start' => $dateRange ? $dateRange[0] : Carbon::now()->subYear(),
                'end' => $dateRange ? $dateRange[1] : Carbon::now(),
            ],
            'generated_at' => now(),
            'progress_data' => $progressData,
        ];
    }

    private function buildComprehensiveReport()
    {
        return [
            'report_type' => 'comprehensive',
            'ward' => $this->selectedWard,
            'date_range' => [
                'start' => $dateRange = $this->getDateRange() ? $this->getDateRange()[0] : Carbon::now()->subYear(),
                'end' => $dateRange = $this->getDateRange() ? $this->getDateRange()[1] : Carbon::now(),
            ],
            'generated_at' => now(),
            'performance' => $this->buildPerformanceReport(),
            'attendance' => $this->buildAttendanceReport(),
            'progress' => $this->buildProgressReport(),
        ];
    }

    private function getDateRange()
    {
        switch ($this->selectedPeriod) {
            case 'current_term':
                return [Carbon::now()->startOfMonth(), Carbon::now()];
            case 'last_term':
                return [Carbon::now()->subMonths(3)->startOfMonth(), Carbon::now()->subMonths(3)->endOfMonth()];
            case 'current_year':
                return [Carbon::now()->startOfYear(), Carbon::now()];
            case 'last_year':
                return [Carbon::now()->subYear()->startOfYear(), Carbon::now()->subYear()->endOfYear()];
            case 'all_time':
                return null;
            default:
                return [Carbon::now()->startOfMonth(), Carbon::now()];
        }
    }

    private function calculateTrend($scores)
    {
        if ($scores->count() < 2) {
            return 'stable';
        }

        $recent = $scores->slice(-3)->avg();
        $earlier = $scores->slice(0, 3)->avg();

        if ($recent > $earlier + 5) {
            return 'improving';
        }
        if ($recent < $earlier - 5) {
            return 'declining';
        }

        return 'stable';
    }

    #[Computed]
    public function wards()
    {
        $students = StudentParent::withoutGlobalScopes()
            ->where('user_id', Auth::id())
            ->with([
                'students' => function ($query) {
                    $query->withoutGlobalScopes();
                },
                'students.user',
                'students.academicLevel.academicGroup',
                'students.studentGroup',
            ])
            ->get()
            ->flatMap(function ($parent) {
                return $parent->students;
            })
            ->unique('id');

        return $students->sortBy('user.name');
    }

    #[Computed]
    public function selectedWard()
    {
        if (! $this->selectedWardId) {
            return null;
        }

        return Student::withoutGlobalScopes()
            ->with([
                'user',
                'academicLevel.academicGroup',
            ])->find($this->selectedWardId);
    }

    #[Computed]
    public function availableSubjects()
    {
        if (! $this->selectedWard) {
            return collect();
        }

        return AcademicSubject::whereHas('assignments.submissions', function ($query) {
            $query->where('student_id', $this->selectedWardId);
        })->get();
    }

    #[Computed]
    public function reportTypes()
    {
        return [
            'performance' => 'Performance Report',
            'attendance' => 'Attendance Report',
            'progress' => 'Progress Report',
            'comprehensive' => 'Comprehensive Report',
        ];
    }

    #[Computed]
    public function periods()
    {
        return [
            'current_term' => 'Current Term',
            'last_term' => 'Last Term',
            'current_year' => 'Current Academic Year',
            'last_year' => 'Last Academic Year',
            'all_time' => 'All Time',
        ];
    }

    #[Computed]
    public function reportHistory()
    {
        // In production, fetch from a reports table
        return collect();
    }

    public function render()
    {
        return view('livewire.parent.reports');
    }
}
