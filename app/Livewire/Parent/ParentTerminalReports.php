<?php

namespace App\Livewire\Parent;

use App\Livewire\AppComponent;
use App\Models\Assessment;
use App\Models\Student;
use App\Models\StudentParent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

class ParentTerminalReports extends AppComponent
{
    public $selectedWardId = null;

    public $selectedTerm = 'first_term';

    public $selectedYear = null;

    public $generatedReport = null;

    public $showReportPreview = false;

    public function mount()
    {
        $wards = $this->wards;
        if ($wards->isNotEmpty()) {
            $this->selectedWardId = $wards->first()->id;
        }
        $this->selectedYear = now()->year;
    }

    public function selectWard($wardId)
    {
        $this->selectedWardId = $wardId;
        $this->resetReport();
    }

    public function generateTerminalReport()
    {
        $this->startLoading();

        try {
            $this->generatedReport = $this->buildTerminalReport();
            $this->showReportPreview = true;
            session()->flash('success', 'Terminal report generated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error generating terminal report: '.$e->getMessage());
        }

        $this->endLoading();
    }

    public function downloadTerminalReport($format = 'pdf')
    {
        $this->dispatch('download-terminal-report', [
            'format' => $format,
            'data' => $this->generatedReport,
        ]);
    }

    public function printTerminalReport()
    {
        $this->dispatch('print-terminal-report', ['data' => $this->generatedReport]);
    }

    private function resetReport()
    {
        $this->generatedReport = null;
        $this->showReportPreview = false;
    }

    private function buildTerminalReport()
    {
        if (! $this->selectedWard) {
            throw new \Exception('No ward selected');
        }

        $assessments = Assessment::where('student_id', $this->selectedWardId)
            ->with(['academicSubject', 'assessmentType'])
            ->whereYear('created_at', $this->selectedYear)
            ->get();

        $subjectPerformance = $assessments->groupBy('academic_subject_id')->map(function ($subjectAssessments) {
            $subject = $subjectAssessments->first()->academicSubject;
            $averageScore = $subjectAssessments->avg('score');

            return [
                'subject' => $subject,
                'total_assessments' => $subjectAssessments->count(),
                'average_score' => $averageScore,
                'highest_score' => $subjectAssessments->max('score'),
                'lowest_score' => $subjectAssessments->min('score'),
                'grade' => $this->calculateGrade($averageScore),
                'remarks' => $this->generateRemarks($averageScore),
            ];
        });

        $overallSummary = $this->calculateOverallSummary($assessments);

        return [
            'ward' => $this->selectedWard,
            'term' => $this->selectedTerm,
            'year' => $this->selectedYear,
            'generated_at' => now(),
            'subjects' => $subjectPerformance,
            'overall_summary' => $overallSummary,
            'teacher_comments' => $this->generateTeacherComments($overallSummary),
            'parent_comments' => '', // To be filled by parent
            'next_term_begins' => $this->calculateNextTermDate(),
        ];
    }

    private function calculateOverallSummary($assessments)
    {
        $totalAssessments = $assessments->count();
        $averageScore = $assessments->avg('score') ?? 0;
        $passedAssessments = $assessments->where('passed', true)->count();
        $failedAssessments = $assessments->where('passed', false)->count();

        return [
            'total_assessments' => $totalAssessments,
            'average_score' => $averageScore,
            'passed_assessments' => $passedAssessments,
            'failed_assessments' => $failedAssessments,
            'highest_score' => $assessments->max('score') ?? 0,
            'lowest_score' => $assessments->min('score') ?? 0,
            'overall_grade' => $this->calculateGrade($averageScore),
            'class_rank' => $this->calculateClassRank($averageScore),
            'conduct_grade' => 'A', // Mock data
            'attendance_rate' => '95%', // Mock data
        ];
    }

    private function calculateGrade($score)
    {
        if ($score >= 90) {
            return 'A';
        }
        if ($score >= 80) {
            return 'B';
        }
        if ($score >= 70) {
            return 'C';
        }
        if ($score >= 60) {
            return 'D';
        }

        return 'F';
    }

    private function calculateClassRank($averageScore)
    {
        // Mock implementation - in real app, compare with other students
        return rand(1, 30).' out of '.rand(25, 35);
    }

    private function generateRemarks($averageScore)
    {
        if ($averageScore >= 90) {
            return 'Excellent performance!';
        }
        if ($averageScore >= 80) {
            return 'Very good work!';
        }
        if ($averageScore >= 70) {
            return 'Good effort!';
        }
        if ($averageScore >= 60) {
            return 'Needs improvement';
        }

        return 'Requires additional support';
    }

    private function generateTeacherComments($overallSummary)
    {
        $averageScore = $overallSummary['average_score'];
        $grade = $overallSummary['overall_grade'];

        if ($averageScore >= 90) {
            return "Exceptional performance throughout the term. {$this->selectedWard->user->name} demonstrates excellent understanding of all subjects and consistently produces high-quality work. Keep up the excellent work!";
        } elseif ($averageScore >= 80) {
            return "Very good performance this term. {$this->selectedWard->user->name} shows strong understanding of most subjects with room for minor improvements. Continue the good work!";
        } elseif ($averageScore >= 70) {
            return "Good progress this term. {$this->selectedWard->user->name} demonstrates adequate understanding of subjects but could benefit from more consistent study habits.";
        } elseif ($averageScore >= 60) {
            return "Fair performance this term. {$this->selectedWard->user->name} needs to put in more effort to improve understanding of key concepts in several subjects.";
        } else {
            return "Needs significant improvement. {$this->selectedWard->user->name} requires additional support and more focused study to meet academic standards.";
        }
    }

    private function calculateNextTermDate()
    {
        // Mock implementation - replace with actual academic calendar
        return Carbon::now()->addMonths(1)->format('M d, Y');
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
        ])->find($this->selectedWardId);
    }

    #[Computed]
    public function availableTerms()
    {
        return [
            'first_term' => 'First Term',
            'second_term' => 'Second Term',
            'third_term' => 'Third Term',
        ];
    }

    #[Computed]
    public function availableYears()
    {
        $currentYear = now()->year;

        return range($currentYear - 2, $currentYear + 1);
    }

    public function render()
    {
        return view('livewire.parent.terminal-reports');
    }
}
