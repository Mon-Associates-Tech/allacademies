<?php

namespace App\Livewire\Students;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Assessment;
use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{


    public function render(Request $request)
    {
        $student = auth()->user();
        $tab = $request->query('tab', 'dashboard');

        if ($tab === 'dashboard') {
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
                ->avg('percentage_score') ?? 0;

            $overallScore = round($overallScore, 1);

            // Get performance by subject
            $subjectPerformance = Assessment::where('student_id', $student->id)
                ->where('status', 'completed')
                ->select('subject_id', DB::raw('AVG(percentage_score) as average_score'))
                ->groupBy('subject_id')
                ->with('subject')
                ->get()
                ->map(function($item) {
                    return [
                        'name' => $item->subject->name,
                        'score' => round($item->average_score, 1)
                    ];
                });

            return view('students.dashboard', [
                'activeTab' => $tab,
                'recentBooks' => $recentBooks,
                'bookCount' => $bookCount,
                'recentAssessments' => $recentAssessments,
                'upcomingActivities' => $upcomingActivities,
                'upcomingActivitiesCount' => $upcomingActivitiesCount,
                'overallScore' => $overallScore,
                'subjectPerformance' => $subjectPerformance
            ]);
        }

        return view('students.dashboard', [
            'activeTab' => $tab
        ]);
    }
}
