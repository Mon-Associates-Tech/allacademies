<?php

namespace App\Livewire\Parent;

use App\Livewire\AppComponent;
use App\Models\Student;
use App\Models\Assessment;
use App\Models\StudentParent;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

class Wards extends AppComponent
{
    use WithPagination;

    public $selectedWardId = null;
    public $showWardDetails = false;
    public $sortBy = 'name';
    public $sortDirection = 'asc';
    public $searchTerm = '';
    public $selectedPeriod = 'current_term';

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
        $this->showWardDetails = true;
    }

    public function closeWardDetails()
    {
        $this->showWardDetails = false;
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    #[Computed]
    public function wards()
    {
        $students = StudentParent::withoutGlobalScopes()
            ->where('user_id', Auth::id())
            ->with([
                'students' => function($query) {
                    $query->withoutGlobalScopes();
                },
                'students.user',
                'students.academicLevel.academicGroup',
                'students.studentGroup'
            ])
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
            'academicLevel.academicGroup',
            'academicGroup',
            'studentGroup',
            'assessments' => function($query) {
                $query->latest()->limit(10);
            }
        ])->find($this->selectedWardId);
    }

    #[Computed]
    public function wardPerformanceData()
    {
        if (!$this->selectedWard) return [];

        $assessments = Assessment::where('student_id', $this->selectedWardId)->get();
        $recentAssessments = $assessments->sortByDesc('created_at')->take(5);

        return [
            'total_assessments' => $assessments->count(),
            'average_score' => $assessments->avg('score') ?? 0,
            'passed_assessments' => $assessments->where('passed', true)->count(),
            'failed_assessments' => $assessments->where('passed', false)->count(),
            'recent_assessments' => $recentAssessments,
            'subjects_count' => $assessments->pluck('academic_subject_id')->unique()->count(),
            'performance_trend' => $this->calculatePerformanceTrend($assessments)
        ];
    }

    #[Computed]
    public function performanceOverview()
    {
        $wards = $this->wards;
        $totalAssessments = 0;
        $totalScore = 0;
        $assessmentCount = 0;
        $passedCount = 0;
        $wardsWithData = [];

        foreach ($wards as $ward) {
            $assessments = Assessment::where('student_id', $ward->id)->get();
            $wardAssessmentCount = $assessments->count();
            $wardAverage = $assessments->avg('score') ?? 0;
            $wardPassed = $assessments->where('passed', true)->count();

            $totalAssessments += $wardAssessmentCount;
            $totalScore += $wardAverage * $wardAssessmentCount;
            $assessmentCount += $wardAssessmentCount;
            $passedCount += $wardPassed;

            $wardsWithData[] = [
                'ward' => $ward,
                'assessments_count' => $wardAssessmentCount,
                'average_score' => $wardAverage,
                'passed_count' => $wardPassed,
                'performance_trend' => $this->calculatePerformanceTrend($assessments)
            ];
        }

        return [
            'total_wards' => $wards->count(),
            'total_assessments' => $totalAssessments,
            'overall_average' => $assessmentCount > 0 ? $totalScore / $assessmentCount : 0,
            'overall_pass_rate' => $totalAssessments > 0 ? ($passedCount / $totalAssessments) * 100 : 0,
            'wards_data' => $wardsWithData
        ];
    }

    #[Computed]
    public function subjectBreakdown()
    {
        if (!$this->selectedWard) return [];

        $assessments = Assessment::where('student_id', $this->selectedWardId)
            ->with('academicSubject')
            ->get()
            ->groupBy('academic_subject_id');

        $breakdown = [];
        foreach ($assessments as $subjectId => $subjectAssessments) {
            $subject = $subjectAssessments->first()->academicSubject;
            $breakdown[] = [
                'subject' => $subject,
                'assessments_count' => $subjectAssessments->count(),
                'average_score' => $subjectAssessments->avg('score'),
                'passed_count' => $subjectAssessments->where('passed', true)->count(),
                'last_assessment' => $subjectAssessments->sortByDesc('created_at')->first()
            ];
        }

        return collect($breakdown)->sortByDesc('average_score');
    }

    private function calculatePerformanceTrend($assessments)
    {
        if ($assessments->count() < 2) return 'stable';

        $recent = $assessments->sortByDesc('created_at')->take(5)->avg('score');
        $previous = $assessments->sortByDesc('created_at')->skip(5)->take(5)->avg('score');

        if ($recent > $previous + 5) return 'improving';
        if ($recent < $previous - 5) return 'declining';
        return 'stable';
    }

    public function render()
    {
        return view('livewire.parent.wards');
    }
}
