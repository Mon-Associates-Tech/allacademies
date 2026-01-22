<?php

namespace App\Livewire\Parent;

use App\Livewire\AppComponent;
use App\Models\Assessment;
use App\Models\Student;
use App\Models\StudentParent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

class WardPerformanceReportGenerator extends AppComponent
{
    public $selectedWardId = null;

    public $reportType = 'performance';

    public $dateRange = 'month';

    public $selectedSubjectId = null;

    public $customStartDate = null;

    public $customEndDate = null;

    public $showReportPreview = false;

    public $generatedReport = null;

    public function mount()
    {
        $wards = $this->wards;
        if ($wards->isNotEmpty()) {
            $this->selectedWardId = $wards->first()->id;
        }

        $this->customStartDate = now()->subMonth()->format('Y-m-d');
        $this->customEndDate = now()->format('Y-m-d');
    }

    public function selectWard($wardId)
    {
        $this->selectedWardId = $wardId;
        $this->selectedSubjectId = null;
        $this->showReportPreview = false;
    }

    public function generateReport()
    {
        $this->validate([
            'selectedWardId' => 'required|exists:students,id',
            'reportType' => 'required|in:performance,attendance,assessment,summary',
            'dateRange' => 'required|in:week,month,quarter,year,custom',
            'customStartDate' => 'required_if:dateRange,custom|date',
            'customEndDate' => 'required_if:dateRange,custom|date|after_or_equal:customStartDate',
        ]);

        $this->generatedReport = $this->buildReportData();
        $this->showReportPreview = true;
    }

    public function downloadReport($format = 'pdf')
    {
        if (! $this->generatedReport) {
            $this->generateReport();
        }

        // This would trigger the download via the controller
        return redirect()->route('parent.reports.download', [
            'report' => $this->selectedWardId,
            'type' => $this->reportType,
            'format' => $format,
            'start_date' => $this->getStartDate(),
            'end_date' => $this->getEndDate(),
            'subject_id' => $this->selectedSubjectId,
        ]);
    }

    #[Computed]
    public function wards()
    {
        $students = StudentParent::where('user_id', Auth::id())
            ->with(['students.user', 'students.academicLevel.academicGroup', 'students.studentGroup'])
            ->get()
            ->flatMap(function ($parent) {
                return $parent->students;
            })
            ->unique('id'); // Remove duplicates

        if ($this->searchTerm) {
            $students = $students->filter(function ($student) {
                return stripos($student->user->name, $this->searchTerm) !== false ||
                    stripos($student->academicLevel->name ?? '', $this->searchTerm) !== false ||
                    stripos($student->academicLevel->academicGroup->name ?? '', $this->searchTerm) !== false;
            });
        }

        return $students->sortBy($this->sortBy === 'name' ? 'user.name' : $this->sortBy,
            SORT_REGULAR, $this->sortDirection === 'desc');
    }

    #[Computed]
    public function selectedWard()
    {
        if (! $this->selectedWardId) {
            return null;
        }

        return Student::with([
            'user',
            'academicLevel.academicGroup',
            'academicGroup',
            'studentGroup',
        ])->find($this->selectedWardId);
    }

    #[Computed]
    public function availableSubjects()
    {
        if (! $this->selectedWard) {
            return collect();
        }

        return $this->selectedWard->getAllAccessibleSubjects();
    }

    private function buildReportData()
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

        $query = Assessment::where('student_id', $this->selectedWardId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['academicSubject', 'quiz', 'examination']);

        if ($this->selectedSubjectId) {
            $query->where('academic_subject_id', $this->selectedSubjectId);
        }

        $assessments = $query->get();

        return [
            'ward' => $this->selectedWard,
            'report_type' => $this->reportType,
            'date_range' => [
                'start' => $startDate,
                'end' => $endDate,
                'period' => $this->dateRange,
            ],
            'subject' => $this->selectedSubjectId ?
                $this->availableSubjects->find($this->selectedSubjectId) : null,
            'assessments' => $assessments,
            'summary' => [
                'total_assessments' => $assessments->count(),
                'average_score' => $assessments->avg('score') ?? 0,
                'highest_score' => $assessments->max('score') ?? 0,
                'lowest_score' => $assessments->min('score') ?? 0,
                'passed_count' => $assessments->where('passed', true)->count(),
                'failed_count' => $assessments->where('passed', false)->count(),
                'pass_rate' => $assessments->count() > 0 ?
                    ($assessments->where('passed', true)->count() / $assessments->count() * 100) : 0,
            ],
            'subject_breakdown' => $assessments->groupBy('academic_subject_id')
                ->map(function ($subjectAssessments) {
                    return [
                        'subject' => $subjectAssessments->first()->academicSubject,
                        'count' => $subjectAssessments->count(),
                        'average' => $subjectAssessments->avg('score'),
                        'passed' => $subjectAssessments->where('passed', true)->count(),
                        'failed' => $subjectAssessments->where('passed', false)->count(),
                    ];
                }),
            'monthly_trend' => $assessments->groupBy(function ($assessment) {
                return $assessment->created_at->format('Y-m');
            })->map(function ($monthAssessments) {
                return [
                    'month' => $monthAssessments->first()->created_at->format('M Y'),
                    'count' => $monthAssessments->count(),
                    'average' => $monthAssessments->avg('score'),
                    'passed' => $monthAssessments->where('passed', true)->count(),
                ];
            }),
            'generated_at' => now(),
            'generated_by' => Auth::user(),
        ];
    }

    private function getStartDate()
    {
        return match ($this->dateRange) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'quarter' => now()->subMonths(3),
            'year' => now()->subYear(),
            'custom' => Carbon::parse($this->customStartDate),
            default => now()->subMonth()
        };
    }

    private function getEndDate()
    {
        return match ($this->dateRange) {
            'custom' => Carbon::parse($this->customEndDate),
            default => now()
        };
    }

    public function render()
    {
        return view('livewire.parent.repo   rts');
    }
}
