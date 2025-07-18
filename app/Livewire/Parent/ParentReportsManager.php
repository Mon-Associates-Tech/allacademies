<?php

namespace App\Livewire\Parent;

use App\Livewire\AppComponent;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\Assessment;
use App\Models\AcademicSubject;
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
    public $sortBy = 'date';
    public $sortDirection = 'desc';
    public $searchTerm = '';

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
    }

    public function selectReportType($type)
    {
        $this->selectedReportType = $type;
        $this->resetReport();
    }

    public function selectPeriod($period)
    {
        $this->selectedPeriod = $period;
        $this->resetReport();
    }

    public function selectSubject($subjectId)
    {
        $this->selectedSubjectId = $subjectId;
        $this->resetReport();
    }

    public function generateReport()
    {
        $this->startLoading();

        try {
            $this->generatedReport = $this->buildReport();
            $this->showReportPreview = true;
            session()->flash('success', 'Report generated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error generating report: ' . $e->getMessage());
        }

        $this->endLoading();
    }

    public function downloadReport($format = 'pdf')
    {
        // Implementation for downloading report
        $this->dispatch('download-report', [
            'format' => $format,
            'data' => $this->generatedReport
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
        if (!$this->selectedWard) {
            throw new \Exception('No ward selected');
        }

        switch ($this->selectedReportType) {
            case 'performance':
                return $this->buildPerformanceReport();
            case 'attendance':
                return $this->buildAttendanceReport();
            case 'progress':
                return $this->buildProgressReport();
            case 'subject_analysis':
                return $this->buildSubjectAnalysisReport();
            default:
                throw new \Exception('Invalid report type');
        }
    }

    private function buildPerformanceReport()
    {
        $assessments = Assessment::where('student_id', $this->selectedWardId)
            ->with(['academicSubject', 'assessmentType'])
            ->when($this->selectedSubjectId, function($query) {
                $query->where('academic_subject_id', $this->selectedSubjectId);
            })
            ->get();

        $subjectPerformance = $assessments->groupBy('academic_subject_id')->map(function($subjectAssessments) {
            $subject = $subjectAssessments->first()->academicSubject;
            return [
                'subject' => $subject,
                'assessments_count' => $subjectAssessments->count(),
                'average_score' => $subjectAssessments->avg('score'),
                'highest_score' => $subjectAssessments->max('score'),
                'lowest_score' => $subjectAssessments->min('score'),
                'passed_count' => $subjectAssessments->where('passed', true)->count(),
                'latest_assessment' => $subjectAssessments->sortByDesc('created_at')->first()
            ];
        });

        return [
            'type' => 'performance',
            'ward' => $this->selectedWard,
            'period' => $this->selectedPeriod,
            'generated_at' => now(),
            'overall_stats' => [
                'total_assessments' => $assessments->count(),
                'average_score' => $assessments->avg('score') ?? 0,
                'passed_assessments' => $assessments->where('passed', true)->count(),
                'failed_assessments' => $assessments->where('passed', false)->count(),
                'subjects_count' => $assessments->pluck('academic_subject_id')->unique()->count()
            ],
            'subject_performance' => $subjectPerformance,
            'recent_assessments' => $assessments->sortByDesc('created_at')->take(10)
        ];
    }

    private function buildAttendanceReport()
    {
        // Mock attendance data - replace with actual attendance model
        return [
            'type' => 'attendance',
            'ward' => $this->selectedWard,
            'period' => $this->selectedPeriod,
            'generated_at' => now(),
            'attendance_stats' => [
                'total_days' => 100,
                'present_days' => 95,
                'absent_days' => 5,
                'attendance_rate' => 95.0
            ],
            'monthly_breakdown' => [
                'January' => ['present' => 20, 'absent' => 1],
                'February' => ['present' => 18, 'absent' => 2],
                'March' => ['present' => 22, 'absent' => 0],
                // Add more months as needed
            ]
        ];
    }

    private function buildProgressReport()
    {
        $assessments = Assessment::where('student_id', $this->selectedWardId)
            ->with('academicSubject')
            ->orderBy('created_at')
            ->get();

        $progressData = [];
        foreach ($assessments->groupBy('academic_subject_id') as $subjectId => $subjectAssessments) {
            $subject = $subjectAssessments->first()->academicSubject;
            $progressData[] = [
                'subject' => $subject,
                'progress_points' => $subjectAssessments->map(function($assessment) {
                    return [
                        'date' => $assessment->created_at->format('Y-m-d'),
                        'score' => $assessment->score,
                        'passed' => $assessment->passed
                    ];
                })->values()
            ];
        }

        return [
            'type' => 'progress',
            'ward' => $this->selectedWard,
            'period' => $this->selectedPeriod,
            'generated_at' => now(),
            'progress_data' => $progressData,
            'improvement_areas' => $this->identifyImprovementAreas($assessments)
        ];
    }

    private function buildSubjectAnalysisReport()
    {
        if (!$this->selectedSubjectId) {
            throw new \Exception('Subject must be selected for subject analysis report');
        }

        $assessments = Assessment::where('student_id', $this->selectedWardId)
            ->where('academic_subject_id', $this->selectedSubjectId)
            ->with(['academicSubject', 'assessmentType'])
            ->get();

        return [
            'type' => 'subject_analysis',
            'ward' => $this->selectedWard,
            'subject' => AcademicSubject::find($this->selectedSubjectId),
            'period' => $this->selectedPeriod,
            'generated_at' => now(),
            'detailed_analysis' => [
                'total_assessments' => $assessments->count(),
                'average_score' => $assessments->avg('score') ?? 0,
                'score_distribution' => $this->calculateScoreDistribution($assessments),
                'difficulty_analysis' => $this->analyzeDifficulty($assessments),
                'time_series' => $assessments->sortBy('created_at')->map(function($assessment) {
                    return [
                        'date' => $assessment->created_at->format('Y-m-d'),
                        'score' => $assessment->score,
                        'type' => $assessment->assessmentType->name ?? 'Unknown'
                    ];
                })
            ]
        ];
    }

    private function identifyImprovementAreas($assessments)
    {
        $subjectAverages = $assessments->groupBy('academic_subject_id')
            ->map(function($subjectAssessments) {
                return [
                    'subject' => $subjectAssessments->first()->academicSubject,
                    'average' => $subjectAssessments->avg('score')
                ];
            })
            ->sortBy('average')
            ->take(3);

        return $subjectAverages->values();
    }

    private function calculateScoreDistribution($assessments)
    {
        $distribution = [
            'A (90-100)' => 0,
            'B (80-89)' => 0,
            'C (70-79)' => 0,
            'D (60-69)' => 0,
            'F (0-59)' => 0
        ];

        foreach ($assessments as $assessment) {
            $score = $assessment->score;
            if ($score >= 90) $distribution['A (90-100)']++;
            elseif ($score >= 80) $distribution['B (80-89)']++;
            elseif ($score >= 70) $distribution['C (70-79)']++;
            elseif ($score >= 60) $distribution['D (60-69)']++;
            else $distribution['F (0-59)']++;
        }

        return $distribution;
    }

    private function analyzeDifficulty($assessments)
    {
        $averageScore = $assessments->avg('score');

        if ($averageScore >= 80) return 'Easy';
        if ($averageScore >= 60) return 'Moderate';
        return 'Difficult';
    }

    #[Computed]
    public function wards()
    {
        $students = StudentParent::where('user_id', Auth::id())
            ->with(['students.user', 'students.academicLevel.academicGroup', 'students.studentGroup'])
            ->get()
            ->flatMap(function($parent) {
                return $parent->students;
            })
            ->unique('id'); // Remove duplicates

        if ($this->searchTerm) {
            $students = $students->filter(function($student) {
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
        if (!$this->selectedWardId) return null;

        return Student::with([
            'user',
            'academicLevel.academicGroup'
        ])->find($this->selectedWardId);
    }

    #[Computed]
    public function availableSubjects()
    {
        if (!$this->selectedWard) return collect();

        return AcademicSubject::whereHas('assessments', function($query) {
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
            'subject_analysis' => 'Subject Analysis'
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
            'all_time' => 'All Time'
        ];
    }

    public function render()
    {
        return view('livewire.parent.reports');
    }
}
