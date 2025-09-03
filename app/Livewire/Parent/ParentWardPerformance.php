<?php

namespace App\Livewire\Parent;

use App\Livewire\AppComponent;
use App\Models\Student;
use App\Models\Assessment;
use App\Models\AcademicSubject;
use App\Models\StudentParent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

class ParentWardPerformance extends AppComponent
{
    use WithPagination;

    public $selectedWardId = null;
    public $selectedSubjectId = null;
    public $selectedPeriod = 'all';
    public $selectedAssessmentType = 'all';
    public $viewMode = 'overview'; // overview, detailed, analytics
    public $searchTerm = '';
    public $sortBy = 'name';
    public $sortDirection = 'desc';
    public $availablePeriods = ['all', 'week', 'month', 'quarter', 'year'];
    public $dateRange = null;

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
        $this->selectedSubjectId = null;
        $this->resetPage();
    }

    public function selectSubject($subjectId)
    {
        $this->selectedSubjectId = $subjectId;
        $this->resetPage();
    }

    public function changeViewMode($mode)
    {
        $this->viewMode = $mode;
        $this->resetPage();
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
            'academicLevel.academicGroup',
            'user'
        ])->find($this->selectedWardId);
    }

    #[Computed]
    public function availableSubjects()
    {
        if (!$this->selectedWard) return collect();

        return $this->selectedWard->getAllAccessibleSubjects();
    }

    #[Computed]
    public function assessments()
    {
        if (!$this->selectedWardId) return collect();

        $query = Assessment::where('student_id', $this->selectedWardId)
            ->with(['subject']);

        if ($this->selectedSubjectId) {
            $query->where('subject_id', $this->selectedSubjectId);
        }

        if ($this->selectedAssessmentType !== 'all') {
            if ($this->selectedAssessmentType === 'quiz') {
                $query->whereNotNull('quiz_id');
            } elseif ($this->selectedAssessmentType === 'exam') {
                $query->whereNotNull('examination_id');
            }
        }

        if ($this->selectedPeriod !== 'all') {
            $date = match($this->selectedPeriod) {
                'week' => now()->subWeek(),
                'month' => now()->subMonth(),
                'quarter' => now()->subMonths(3),
                'year' => now()->subYear(),
                default => null
            };

            if ($date) {
                $query->where('created_at', '>=', $date);
            }
        }

        return $query->latest()->paginate(10);
    }

    #[Computed]
    public function performanceAnalytics()
    {
        if (!$this->selectedWardId) return [];

        $cacheKey = "performance_analytics_{$this->selectedWardId}_{$this->selectedSubjectId}_{$this->selectedPeriod}";

        return Cache::remember($cacheKey, 300, function() {
            $assessments = Assessment::where('student_id', $this->selectedWardId);

            if ($this->selectedSubjectId) {
                $assessments->where('subject_id', $this->selectedSubjectId);
            }

            if ($this->selectedPeriod !== 'all') {
                $date = match($this->selectedPeriod) {
                    'week' => now()->subWeek(),
                    'month' => now()->subMonth(),
                    'quarter' => now()->subMonths(3),
                    'year' => now()->subYear(),
                    default => null
                };

                if ($date) {
                    $assessments->where('created_at', '>=', $date);
                }
            }

            $assessments = $assessments->get();

            return [
                'total_assessments' => $assessments->count(),
                'average_score' => $assessments->avg('score') ?? 0,
                'highest_score' => $assessments->max('score') ?? 0,
                'lowest_score' => $assessments->min('score') ?? 0,
                'passed_count' => $assessments->where('passed', true)->count(),
                'failed_count' => $assessments->where('passed', false)->count(),
                'pass_rate' => $assessments->count() > 0 ?
                    ($assessments->where('passed', true)->count() / $assessments->count() * 100) : 0,
                'subject_breakdown' => $assessments->groupBy('academic_subject_id')
                    ->map(function($subjectAssessments) {
                        return [
                            'count' => $subjectAssessments->count(),
                            'average' => $subjectAssessments->avg('score'),
                            'passed' => $subjectAssessments->where('passed', true)->count(),
                        ];
                    }),
                'monthly_trend' => $assessments->groupBy(function($assessment) {
                    return $assessment->created_at->format('Y-m');
                })->map(function($monthAssessments) {
                    return [
                        'count' => $monthAssessments->count(),
                        'average' => $monthAssessments->avg('score'),
                    ];
                }),
                'performance_trend' => $this->calculatePerformanceTrend($assessments),
            ];
        });
    }

    private function calculatePerformanceTrend($assessments)
    {
        if ($assessments->count() < 2) return 'stable';

        $recent = $assessments->sortBy('created_at')->take(-5)->avg('score');
        $previous = $assessments->sortBy('created_at')->skip(-10)->take(5)->avg('score');

        if ($recent > $previous + 5) return 'improving';
        if ($recent < $previous - 5) return 'declining';
        return 'stable';
    }

    public function render()
    {
        return view('livewire.parent.performance');
    }
}
