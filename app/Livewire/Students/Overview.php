<?php

namespace App\Livewire\Students;

use App\Models\Activity;
use App\Models\Assessment;
use App\Models\Book;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;

class Overview extends Component
{

    #[Url]
    public $activeTab = 'overview';

    protected $listeners = ['studentTabChanged' => 'setActiveTab'];

    public function setActiveTab($tab): void
    {
        $this->activeTab = $tab;
        $this->dispatch('tabChanged', $tab);
    }

    public function mount(): void
    {
        if(!$this->activeTab){
            $this->activeTab = 'overview';
        }
    }

    public function render()
    {
        $student = auth()->user();
            // Get books the student has access to
            $recentBooks = Book::whereHas('students', function($query) use ($student) {
                $query->where('student_id', $student->id);
            })->latest()->take(5)->get();

            $bookCount = Book::whereHas('students', function($query) use ($student) {
                $query->where('student_id', $student->id);
            })->count();

            // Get recent assessments
            $recentAssessments = Assessment::where('student_id', $student->id)
                ->with(['subject', 'topic'])
                ->latest()
                ->take(5)
                ->get();

            // Get upcoming activities/events
            $upcomingActivities = Activity::forStudent($student->id)
                ->upcoming()
                ->with(['subject', 'group'])
                ->take(5)
                ->get();

            $upcomingActivitiesCount = Activity::forStudent($student->id)
                ->upcoming()
                ->count();

            // Calculate overall performance score
            $overallScore = Assessment::where('student_id', $student->id)
                ->where('status', 'completed')
                ->avg('score') ?? 0;

            $overallScore = round($overallScore, 1);

            // Get performance by subject
            $subjectPerformance = Assessment::where('student_id', $student->id)
                ->where('status', 'completed')
                ->select('subject_id', DB::raw('AVG(score) as average_score'))
                ->groupBy('subject_id')
                ->with('subject')
                ->get()
                ->map(function($item) {
                    return [
                        'name' => $item->subject->name,
                        'score' => round($item->average_score, 1)
                    ];
                });

            return view('livewire.students.overview', [
                'recentBooks' => $recentBooks,
                'bookCount' => $bookCount,
                'recentAssessments' => $recentAssessments,
                'upcomingActivities' => $upcomingActivities,
                'upcomingActivitiesCount' => $upcomingActivitiesCount,
                'overallScore' => $overallScore,
                'subjectPerformance' => $subjectPerformance
            ]);

    }
}
