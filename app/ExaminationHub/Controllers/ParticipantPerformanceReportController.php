<?php

namespace App\ExaminationHub\Controllers;

use App\Http\Controllers\Controller;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ParticipantPerformanceReportController extends Controller
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

        return view('examination-hub.performance.index', [
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

        return view('examination-hub.performance.show', [
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
        return $this->exportPerformanceReport($request, $participantType, $participantId, 'csv');
    }

    public function exportExcel(Request $request, $participantType, $participantId)
    {
        return $this->exportPerformanceReport($request, $participantType, $participantId, 'xlsx');
    }

    private function exportPerformanceReport(Request $request, string $participantType, string $participantId, string $format)
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

        $filename = 'performance_report_' . str_replace([' ', ','], ['_', '_'], $participant->participant_name) . '_' . now()->format('Y-m-d_H-i-s');

        if ($format === 'xlsx') {
            $filename .= '.xlsx';
            $headers = [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => "attachment; filename={$filename}",
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
                'Pragma' => 'public',
            ];
        } else {
            $filename .= '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename={$filename}",
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
                'Pragma' => 'public',
            ];
        }

        $callback = function() use ($participant, $submissions, $metrics) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8 to ensure proper character encoding in Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Report Header
            fputcsv($file, ['PARTICIPANT PERFORMANCE REPORT']);
            fputcsv($file, ['']); // Empty row
            
            // Metadata
            fputcsv($file, ['REPORT METADATA']);
            fputcsv($file, ['Field', 'Value']);
            fputcsv($file, ['Report Title', 'Individual Participant Performance Report']);
            fputcsv($file, ['Generated On', now()->format('Y-m-d H:i:s T')]);
            fputcsv($file, ['Export Format Version', '1.0']);
            fputcsv($file, ['']); // Empty row

            // Participant Information
            fputcsv($file, ['PARTICIPANT INFORMATION']);
            fputcsv($file, ['Field', 'Value']);
            fputcsv($file, ['Name', $participant->participant_name]);
            fputcsv($file, ['Email', $participant->participant_email ?? 'N/A']);
            fputcsv($file, ['Participant Type', ucfirst(str_replace('_', ' ', $participant->participant_type))]);
            fputcsv($file, ['Participant ID', $participant->participant_id]);
            fputcsv($file, ['']); // Empty row

            // Overall Performance Metrics
            fputcsv($file, ['OVERALL PERFORMANCE METRICS']);
            fputcsv($file, [
                'Total Submissions',
                'Completed Submissions', 
                'Pending Submissions',
                'Average Score (%)',
                'Overall Grade',
                'Highest Score (%)',
                'Lowest Score (%)'
            ]);
            fputcsv($file, [
                $metrics['total_submissions'],
                $metrics['graded_submissions'],
                $metrics['pending_submissions'],
                number_format($metrics['average_percentage'], 2) . '%',
                $metrics['overall_grade'],
                number_format($metrics['highest_score'], 2) . '%',
                number_format($metrics['lowest_score'], 2) . '%'
            ]);
            fputcsv($file, ['']); // Empty row

            // Subject Performance Table
            fputcsv($file, ['PERFORMANCE BY SUBJECT']);
            fputcsv($file, [
                'Subject Name',
                'Number of Assessments',
                'Total Score Achieved',
                'Total Possible Marks',
                'Average Score (%)',
                'Average Grade'
            ]);
            
            if (!empty($metrics['subject_performance'])) {
                foreach ($metrics['subject_performance'] as $performance) {
                    fputcsv($file, [
                        $performance['subject']->name ?? 'Unknown Subject',
                        $performance['submissions_count'],
                        number_format($performance['total_score'], 2),
                        number_format($performance['total_marks'], 2),
                        number_format($performance['percentage'], 2) . '%',
                        $performance['average_grade']
                    ]);
                }
            } else {
                fputcsv($file, ['No subject data available']);
            }
            fputcsv($file, ['']); // Empty row

            // Grade Distribution
            fputcsv($file, ['GRADE DISTRIBUTION']);
            fputcsv($file, ['Grade', 'Count', 'Percentage']);
            $totalGraded = $metrics['graded_submissions'];
            foreach (['A+', 'A', 'B', 'C', 'D', 'F'] as $grade) {
                $count = $metrics['grade_distribution'][$grade] ?? 0;
                $percentage = $totalGraded > 0 ? number_format(($count / $totalGraded) * 100, 2) : 0;
                fputcsv($file, [$grade, $count, $percentage . '%']);
            }
            fputcsv($file, ['']); // Empty row

            // Detailed Submission Records
            fputcsv($file, ['DETAILED SUBMISSION RECORDS']);
            fputcsv($file, [
                'Exam Title',
                'Subject',
                'Score Achieved',
                'Total Marks',
                'Percentage',
                'Letter Grade',
                'Status',
                'Submitted Date',
                'Time Taken (minutes)',
                'Sections Count'
            ]);
            
            foreach ($submissions as $submission) {
                $subject = $submission->assignment->sections->first()?->academicSubject;
                $timeTaken = $submission->started_at && $submission->submitted_at 
                    ? $submission->started_at->diffInMinutes($submission->submitted_at) 
                    : 'N/A';
                    
                fputcsv($file, [
                    $submission->assignment->title,
                    $subject?->name ?? 'N/A',
                    $submission->score !== null ? number_format($submission->score, 2) : 'N/A',
                    $submission->total_marks !== null ? number_format($submission->total_marks, 2) : 'N/A',
                    $submission->percentage !== null ? number_format($submission->percentage, 2) . '%' : 'N/A',
                    $submission->grade ?? 'N/A',
                    ucfirst(str_replace('_', ' ', $submission->status)),
                    $submission->submitted_at?->format('Y-m-d H:i:s') ?? 'N/A',
                    $timeTaken,
                    $submission->assignment->sections->count()
                ]);
            }

            // Summary Footer
            fputcsv($file, ['']); // Empty row
            fputcsv($file, ['EXPORT SUMMARY']);
            fputcsv($file, ['Total Records Exported', count($submissions)]);
            fputcsv($file, ['Export Completed On', now()->format('Y-m-d H:i:s T')]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportAllExcel(Request $request)
    {
        // Get all participants with their performance metrics
        $participantsQuery = GeneralExamSubmission::query()
            ->select(
                'participant_type',
                'participant_id',
                \Illuminate\Support\Facades\DB::raw('COALESCE(MAX(participant_name), "Unknown") as participant_name'),
                \Illuminate\Support\Facades\DB::raw('COALESCE(MAX(participant_email), "N/A") as participant_email'),
                \Illuminate\Support\Facades\DB::raw('COUNT(*) as submission_count'),
                \Illuminate\Support\Facades\DB::raw('ROUND(AVG(COALESCE(percentage, 0)), 2) as avg_percentage'),
                \Illuminate\Support\Facades\DB::raw('CASE 
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
            $participantsQuery->having(\Illuminate\Support\Facades\DB::raw('COALESCE(MAX(participant_name), "")'), 'like', "%{$search}%")
                ->orHaving(\Illuminate\Support\Facades\DB::raw('COALESCE(MAX(participant_email), "")'), 'like', "%{$search}%");
        }

        $participants = $participantsQuery->get();

        $filename = 'all_participants_performance_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
            'Pragma' => 'public',
        ];

        $callback = function() use ($participants) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8 to ensure proper character encoding in Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Report Header
            fputcsv($file, ['ALL PARTICIPANTS PERFORMANCE REPORT']);
            fputcsv($file, ['']); // Empty row
            
            // Metadata
            fputcsv($file, ['REPORT METADATA']);
            fputcsv($file, ['Field', 'Value']);
            fputcsv($file, ['Report Title', 'All Participants Performance Report']);
            fputcsv($file, ['Generated On', now()->format('Y-m-d H:i:s T')]);
            fputcsv($file, ['Export Format Version', '1.0']);
            fputcsv($file, ['']); // Empty row

            // Participants Performance Data
            fputcsv($file, ['PARTICIPANTS PERFORMANCE DATA']);
            fputcsv($file, [
                'Participant Name',
                'Participant Email',
                'Participant Type',
                'Total Submissions',
                'Average Score (%)',
                'Overall Grade'
            ]);
            
            foreach ($participants as $participant) {
                fputcsv($file, [
                    $participant->participant_name,
                    $participant->participant_email,
                    ucfirst(str_replace('_', ' ', $participant->participant_type)),
                    $participant->submission_count,
                    number_format($participant->avg_percentage, 2) . '%',
                    $participant->avg_grade
                ]);
            }

            // Summary Footer
            fputcsv($file, ['']); // Empty row
            fputcsv($file, ['EXPORT SUMMARY']);
            fputcsv($file, ['Total Records Exported', count($participants)]);
            fputcsv($file, ['Export Completed On', now()->format('Y-m-d H:i:s T')]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportAllPdf(Request $request)
    {
        // Get all participants with their performance metrics
        $participantsQuery = GeneralExamSubmission::query()
            ->select(
                'participant_type',
                'participant_id',
                \Illuminate\Support\Facades\DB::raw('COALESCE(MAX(participant_name), "Unknown") as participant_name'),
                \Illuminate\Support\Facades\DB::raw('COALESCE(MAX(participant_email), "N/A") as participant_email'),
                \Illuminate\Support\Facades\DB::raw('COUNT(*) as submission_count'),
                \Illuminate\Support\Facades\DB::raw('ROUND(AVG(COALESCE(percentage, 0)), 2) as avg_percentage'),
                \Illuminate\Support\Facades\DB::raw('CASE 
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
            $participantsQuery->having(\Illuminate\Support\Facades\DB::raw('COALESCE(MAX(participant_name), "")'), 'like', "%{$search}%")
                ->orHaving(\Illuminate\Support\Facades\DB::raw('COALESCE(MAX(participant_email), "")'), 'like', "%{$search}%");
        }

        $participants = $participantsQuery->get();

        // Since we don't have a PDF library installed, we'll return a simple HTML response
        // that can be converted to PDF using a service or browser print functionality
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <title>All Participants Performance Report</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                h1 { color: #333; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .metadata { margin-bottom: 20px; }
                .summary { margin-top: 20px; }
            </style>
        </head>
        <body>
            <h1>ALL PARTICIPANTS PERFORMANCE REPORT</h1>
            
            <div class="metadata">
                <h3>REPORT METADATA</h3>
                <p><strong>Report Title:</strong> All Participants Performance Report</p>
                <p><strong>Generated On:</strong> ' . now()->format('Y-m-d H:i:s T') . '</p>
                <p><strong>Export Format Version:</strong> 1.0</p>
            </div>

            <h3>PARTICIPANTS PERFORMANCE DATA</h3>
            <table>
                <thead>
                    <tr>
                        <th>Participant Name</th>
                        <th>Participant Email</th>
                        <th>Participant Type</th>
                        <th>Total Submissions</th>
                        <th>Average Score (%)</th>
                        <th>Overall Grade</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($participants as $participant) {
            $html .= '
                    <tr>
                        <td>' . htmlspecialchars($participant->participant_name) . '</td>
                        <td>' . htmlspecialchars($participant->participant_email) . '</td>
                        <td>' . ucfirst(str_replace('_', ' ', $participant->participant_type)) . '</td>
                        <td>' . $participant->submission_count . '</td>
                        <td>' . number_format($participant->avg_percentage, 2) . '%</td>
                        <td>' . $participant->avg_grade . '</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>

            <div class="summary">
                <h3>EXPORT SUMMARY</h3>
                <p><strong>Total Records Exported:</strong> ' . count($participants) . '</p>
                <p><strong>Export Completed On:</strong> ' . now()->format('Y-m-d H:i:s T') . '</p>
            </div>
        </body>
        </html>';

        // Return the HTML content with appropriate headers for PDF conversion
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="all_participants_performance_' . now()->format('Y-m-d_H-i-s') . '.pdf"');
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