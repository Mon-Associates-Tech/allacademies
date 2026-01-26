<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Student;
use App\Models\StudentParent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AnalyticsController extends Controller
{
    public function performance(Request $request, Student $student)
    {
        // Verify parent has access to this student
        $this->authorizeParentAccess($student);

        $period = $request->get('period', 'month');
        $subjectId = $request->get('subject_id');

        $cacheKey = "analytics_performance_{$student->id}_{$period}_{$subjectId}";

        $analytics = Cache::remember($cacheKey, 300, function () use ($student, $period, $subjectId) {
            $query = Assessment::where('student_id', $student->id);

            if ($subjectId) {
                $query->where('academic_subject_id', $subjectId);
            }

            // Apply period filter
            $date = match ($period) {
                'week' => now()->subWeek(),
                'month' => now()->subMonth(),
                'quarter' => now()->subMonths(3),
                'year' => now()->subYear(),
                default => now()->subMonth()
            };

            $assessments = $query->where('created_at', '>=', $date)->get();

            return [
                'total_assessments' => $assessments->count(),
                'average_score' => $assessments->avg('score') ?? 0,
                'pass_rate' => $assessments->count() > 0 ?
                    ($assessments->where('passed', true)->count() / $assessments->count() * 100) : 0,
                'trend_data' => $this->getTrendData($assessments),
                'subject_breakdown' => $this->getSubjectBreakdown($assessments),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $analytics,
        ]);
    }

    public function subjects(Request $request, Student $student)
    {
        // Verify parent has access to this student
        $this->authorizeParentAccess($student);

        $subjects = $student->getAllAccessibleSubjects()->map(function ($subject) use ($student) {
            $assessments = Assessment::where('student_id', $student->id)
                ->where('academic_subject_id', $subject->id)
                ->get();

            return [
                'id' => $subject->id,
                'name' => $subject->name,
                'total_assessments' => $assessments->count(),
                'average_score' => $assessments->avg('score') ?? 0,
                'passed_assessments' => $assessments->where('passed', true)->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $subjects,
        ]);
    }

    private function authorizeParentAccess(Student $student)
    {
        $hasAccess = StudentParent::where('user_id', Auth::id())
            ->where('student_id', $student->id)
            ->exists();

        if (! $hasAccess) {
            abort(403, 'Unauthorized access to this student.');
        }
    }

    private function getTrendData($assessments)
    {
        return $assessments->groupBy(function ($assessment) {
            return $assessment->created_at->format('Y-m-d');
        })->map(function ($dayAssessments) {
            return [
                'date' => $dayAssessments->first()->created_at->format('Y-m-d'),
                'average_score' => $dayAssessments->avg('score'),
                'count' => $dayAssessments->count(),
            ];
        })->values();
    }

    private function getSubjectBreakdown($assessments)
    {
        return $assessments->groupBy('academic_subject_id')
            ->map(function ($subjectAssessments) {
                return [
                    'subject_id' => $subjectAssessments->first()->academic_subject_id,
                    'count' => $subjectAssessments->count(),
                    'average_score' => $subjectAssessments->avg('score'),
                    'passed' => $subjectAssessments->where('passed', true)->count(),
                ];
            })->values();
    }
}
