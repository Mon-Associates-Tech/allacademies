<?php

namespace App\Http\Controllers\Examinations;

use App\Http\Controllers\Controller;
use App\Models\GeneralExam;
use App\Models\GeneralExamSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StudentPerformanceController extends Controller
{
    public function index(Request $request): View
    {
        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');

        $query = GeneralExamSubmission::query()
            ->select(
                'participant_type',
                'participant_id',
                DB::raw('COALESCE(MAX(participant_name), "Unknown") as participant_name'),
                DB::raw('COALESCE(MAX(participant_email), "N/A") as participant_email'),
                DB::raw('COUNT(*) as submission_count'),
                DB::raw('ROUND(AVG(COALESCE(percentage, 0)), 2) as avg_percentage'),
                DB::raw('CASE 
                    WHEN AVG(COALESCE(percentage, 0)) >= 90 THEN "A+"
                    WHEN AVG(COALESCE(percentage, 0)) >= 80 THEN "A"
                    WHEN AVG(COALESCE(percentage, 0)) >= 70 THEN "B"
                    WHEN AVG(COALESCE(percentage, 0)) >= 60 THEN "C"
                    WHEN AVG(COALESCE(percentage, 0)) >= 50 THEN "D"
                    ELSE "F"
                END as avg_grade')
            )
            ->whereNotNull('submitted_at')
            ->groupBy('participant_type', 'participant_id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->having(DB::raw('COALESCE(MAX(participant_name), "")'), 'like', "%{$search}%")
                ->orHaving(DB::raw('COALESCE(MAX(participant_email), "")'), 'like', "%{$search}%");
        }

        // Apply sorting after grouping
        switch ($sortBy) {
            case 'performance':
                $query->orderByRaw('avg_percentage ' . $sortOrder);
                break;
            case 'submissions':
                $query->orderByRaw('submission_count ' . $sortOrder);
                break;
            default:
                $query->orderByRaw('participant_name ' . $sortOrder);
        }

        $participants = $query->paginate(20)->appends($request->except('page'));

        // Chart data for performance overview
        $performanceDistribution = GeneralExamSubmission::whereNotNull('submitted_at')
            ->whereNotNull('percentage')
            ->selectRaw('CASE 
                WHEN percentage >= 80 THEN "Excellent (80-100%)" 
                WHEN percentage >= 60 THEN "Good (60-79%)" 
                WHEN percentage >= 50 THEN "Average (50-59%)" 
                ELSE "Below Average (<50%)" 
            END as performance_range')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('performance_range')
            ->pluck('count', 'performance_range');

        // Average grade distribution
        $gradeDistribution = GeneralExamSubmission::query()
            ->select(
                'participant_type',
                'participant_id',
                DB::raw('CASE 
                    WHEN AVG(COALESCE(percentage, 0)) >= 90 THEN "A+"
                    WHEN AVG(COALESCE(percentage, 0)) >= 80 THEN "A"
                    WHEN AVG(COALESCE(percentage, 0)) >= 70 THEN "B"
                    WHEN AVG(COALESCE(percentage, 0)) >= 60 THEN "C"
                    WHEN AVG(COALESCE(percentage, 0)) >= 50 THEN "D"
                    ELSE "F"
                END as grade')
            )
            ->whereNotNull('submitted_at')
            ->groupBy('participant_type', 'participant_id')
            ->get()
            ->groupBy('grade')
            ->map->count();

        // Top performers data
        $topPerformers = GeneralExamSubmission::query()
            ->select(
                'participant_type',
                'participant_id',
                DB::raw('COALESCE(MAX(participant_name), "Unknown") as participant_name')
            )
            ->selectRaw('ROUND(AVG(COALESCE(percentage, 0)), 2) as avg_percentage')
            ->whereNotNull('submitted_at')
            ->whereNotNull('percentage')
            ->groupBy('participant_type', 'participant_id')
            ->orderBy('avg_percentage', 'desc')
            ->limit(10)
            ->get();

        return view('examinations-hub.performance.index', [
            'participants' => $participants,
            'performanceDistribution' => $performanceDistribution,
            'gradeDistribution' => $gradeDistribution,
            'topPerformers' => $topPerformers,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
        ]);
    }

    public function show(Request $request, $participantType, $participantId): View
    {
        $participant = GeneralExamSubmission::where('participant_type', $participantType)
            ->where('participant_id', $participantId)
            ->firstOrFail();

        $subjectIds = $request->input('subjects', []);
        
        $submissionsQuery = GeneralExamSubmission::query()
            ->where('participant_type', $participantType)
            ->where('participant_id', $participantId)
            ->with(['assignment.sections.academicSubject'])
            ->latest('submitted_at');

        if (!empty($subjectIds)) {
            $submissionsQuery->whereHas('assignment.sections', function ($query) use ($subjectIds) {
                $query->whereIn('academic_subject_id', $subjectIds);
            });
        }

        $submissions = $submissionsQuery->get();

        $availableSubjects = GeneralExamSubmission::where('participant_type', $participantType)
            ->where('participant_id', $participantId)
            ->join('general_exams', 'general_exam_submissions.general_exam_id', '=', 'general_exams.id')
            ->join('general_exam_sections', 'general_exams.id', '=', 'general_exam_sections.general_exam_id')
            ->join('academic_subjects', 'general_exam_sections.academic_subject_id', '=', 'academic_subjects.id')
            ->select('academic_subjects.id', 'academic_subjects.name')
            ->distinct()
            ->get();

        $metrics = $this->calculateMetrics($submissions, $subjectIds);

        return view('examinations-hub.performance.show', [
            'participant' => $participant,
            'submissions' => $submissions,
            'metrics' => $metrics,
            'selectedSubjects' => $subjectIds,
            'availableSubjects' => $availableSubjects,
            'participantType' => $participantType,
            'participantId' => $participantId,
        ]);
    }

    public function export(Request $request, $participantType, $participantId)
    {
        $participant = GeneralExamSubmission::where('participant_type', $participantType)
            ->where('participant_id', $participantId)
            ->firstOrFail();

        $subjectIds = $request->input('subjects', []);
        
        $submissionsQuery = GeneralExamSubmission::query()
            ->where('participant_type', $participantType)
            ->where('participant_id', $participantId)
            ->with(['assignment.sections.academicSubject'])
            ->latest('submitted_at');

        if (!empty($subjectIds)) {
            $submissionsQuery->whereHas('assignment.sections', function ($query) use ($subjectIds) {
                $query->whereIn('academic_subject_id', $subjectIds);
            });
        }

        $submissions = $submissionsQuery->get();
        $metrics = $this->calculateMetrics($submissions, $subjectIds);

        $filename = 'performance_' . str_replace(' ', '_', $participant->participant_name) . '_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($participant, $submissions, $metrics) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['Participant Performance Report']);
            fputcsv($file, ['Generated:', now()->format('Y-m-d H:i:s')]);
            fputcsv($file, []);

            fputcsv($file, ['Participant Information']);
            fputcsv($file, ['Name:', $participant->participant_name]);
            fputcsv($file, ['Email:', $participant->participant_email ?? 'N/A']);
            fputcsv($file, ['Type:', ucfirst($participant->participant_type)]);
            fputcsv($file, []);

            fputcsv($file, ['Overall Performance']);
            fputcsv($file, ['Total Submissions:', $metrics['total_submissions']]);
            fputcsv($file, ['Graded Submissions:', $metrics['graded_submissions']]);
            fputcsv($file, ['Pending Submissions:', $metrics['pending_submissions']]);
            fputcsv($file, ['Average Percentage:', $metrics['average_percentage'] . '%']);
            fputcsv($file, ['Overall Grade:', $metrics['overall_grade']]);
            fputcsv($file, ['Highest Score:', $metrics['highest_score'] . '%']);
            fputcsv($file, ['Lowest Score:', $metrics['lowest_score'] . '%']);
            fputcsv($file, []);

            fputcsv($file, ['Performance by Subject']);
            fputcsv($file, ['Subject', 'Submissions', 'Score', 'Total Marks', 'Percentage', 'Grade']);
            foreach ($metrics['subject_performance'] as $performance) {
                fputcsv($file, [
                    $performance['subject']->name ?? 'Unknown',
                    $performance['submissions_count'],
                    $performance['total_score'],
                    $performance['total_marks'],
                    $performance['percentage'] . '%',
                    $performance['average_grade'],
                ]);
            }
            fputcsv($file, []);

            fputcsv($file, ['Grade Distribution']);
            fputcsv($file, ['Grade', 'Count']);
            foreach (['A+', 'A', 'B', 'C', 'D', 'F'] as $grade) {
                fputcsv($file, [$grade, $metrics['grade_distribution'][$grade] ?? 0]);
            }
            fputcsv($file, []);

            fputcsv($file, ['Detailed Submissions']);
            fputcsv($file, ['Exam', 'Subject', 'Score', 'Total Marks', 'Percentage', 'Grade', 'Status', 'Date']);
            foreach ($submissions as $submission) {
                $subject = $submission->assignment->sections->first()?->academicSubject;
                fputcsv($file, [
                    $submission->assignment->title,
                    $subject?->name ?? 'N/A',
                    $submission->score ?? '-',
                    $submission->total_marks ?? '-',
                    $submission->percentage ? $submission->percentage . '%' : '-',
                    $submission->grade ?? '-',
                    ucfirst(str_replace('_', ' ', $submission->status)),
                    $submission->submitted_at?->format('Y-m-d H:i') ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function calculateMetrics($submissions, $subjectIds): array
    {
        $totalSubmissions = $submissions->count();
        $gradedSubmissions = $submissions->whereIn('status', ['auto_graded', 'manually_reviewed', 'final']);

        $totalScore = $gradedSubmissions->sum('score');
        $totalMarks = $gradedSubmissions->sum('total_marks');
        $averagePercentage = $totalMarks > 0 ? ($totalScore / $totalMarks) * 100 : 0;

        $gradeDistribution = $gradedSubmissions->groupBy('grade')->map->count();
        
        $subjectPerformance = $gradedSubmissions->groupBy(function($submission) {
            return $submission->assignment->sections->first()?->academic_subject_id;
        })->map(function ($subjectSubmissions) {
            $subjectScore = $subjectSubmissions->sum('score');
            $subjectMarks = $subjectSubmissions->sum('total_marks');
            $subjectPercentage = $subjectMarks > 0 ? ($subjectScore / $subjectMarks) * 100 : 0;

            return [
                'subject' => $subjectSubmissions->first()->assignment->sections->first()?->academicSubject ?? null,
                'submissions_count' => $subjectSubmissions->count(),
                'total_score' => $subjectScore,
                'total_marks' => $subjectMarks,
                'percentage' => round($subjectPercentage, 2),
                'average_grade' => $this->calculateGrade($subjectPercentage),
            ];
        })->filter(fn($item) => $item['subject'] !== null)->values();

        $recentTrend = $gradedSubmissions->sortByDesc('submitted_at')->take(10)->pluck('percentage')->values();

        // Chart data for performance trends over time
        $trendData = $gradedSubmissions->sortBy('submitted_at')
            ->groupBy(function($submission) {
                return $submission->submitted_at->format('M d');
            })
            ->map(function($group) {
                return round($group->avg('percentage'), 2);
            });

        return [
            'total_submissions' => $totalSubmissions,
            'graded_submissions' => $gradedSubmissions->count(),
            'pending_submissions' => $totalSubmissions - $gradedSubmissions->count(),
            'total_score' => round($totalScore, 2),
            'total_marks' => round($totalMarks, 2),
            'average_percentage' => round($averagePercentage, 2),
            'overall_grade' => $this->calculateGrade($averagePercentage),
            'grade_distribution' => $gradeDistribution,
            'subject_performance' => $subjectPerformance,
            'recent_trend' => $recentTrend,
            'trend_data' => $trendData,
            'highest_score' => $gradedSubmissions->max('percentage') ?? 0,
            'lowest_score' => $gradedSubmissions->min('percentage') ?? 0,
        ];
    }

    private function calculateGrade(float $percentage): string
    {
        return match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B',
            $percentage >= 60 => 'C',
            $percentage >= 50 => 'D',
            default => 'F',
        };
    }
}
