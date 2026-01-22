<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Student;
use App\Models\StudentParent;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TerminalReportController extends Controller
{
    public function __invoke(Request $request, Student $student)
    {
        // Verify parent has access to this student
        $this->authorizeParentAccess($student);

        // Get terminal report data
        $reportData = $this->getTerminalReportData($student, $request);

        // Generate PDF for printing
        $pdf = PDF::loadView('parent.terminal-reports.print', $reportData);

        return $pdf->stream("terminal-report-{$student->id}.pdf");
    }

    public function download(Request $request, Student $student)
    {
        // Verify parent has access to this student
        $this->authorizeParentAccess($student);

        // Get terminal report data
        $reportData = $this->getTerminalReportData($student, $request);

        // Generate PDF for download
        $pdf = PDF::loadView('parent.terminal-reports.download', $reportData);

        return $pdf->download("terminal-report-{$student->user->name}-{$request->get('term', 'current')}.pdf");
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

    private function getTerminalReportData(Student $student, Request $request)
    {
        $term = $request->get('term', 'current');
        $year = $request->get('year', now()->year);

        // Get assessments for the specified term
        $assessments = Assessment::where('student_id', $student->id)
            ->with(['academicSubject', 'quiz', 'examination'])
            ->when($term !== 'all', function ($query) use ($year) {
                // Add term filtering logic based on your academic calendar
                return $query->whereYear('created_at', $year);
            })
            ->get();

        // Get subjects with performance data
        $subjects = $student->getAllAccessibleSubjects()->map(function ($subject) use ($assessments) {
            $subjectAssessments = $assessments->where('academic_subject_id', $subject->id);

            return [
                'subject' => $subject,
                'assessments' => $subjectAssessments,
                'average_score' => $subjectAssessments->avg('score') ?? 0,
                'total_assessments' => $subjectAssessments->count(),
                'passed_assessments' => $subjectAssessments->where('passed', true)->count(),
                'grade' => $this->calculateGrade($subjectAssessments->avg('score') ?? 0),
            ];
        });

        return [
            'student' => $student->load(['user', 'academicLevel.academicGroup', 'studentGroup']),
            'term' => $term,
            'year' => $year,
            'subjects' => $subjects,
            'overall_average' => $assessments->avg('score') ?? 0,
            'total_assessments' => $assessments->count(),
            'passed_assessments' => $assessments->where('passed', true)->count(),
            'overall_grade' => $this->calculateGrade($assessments->avg('score') ?? 0),
            'generated_at' => now(),
            'generated_by' => Auth::user(),
        ];
    }

    private function calculateGrade($score)
    {
        if ($score >= 90) {
            return 'A+';
        }
        if ($score >= 80) {
            return 'A';
        }
        if ($score >= 70) {
            return 'B+';
        }
        if ($score >= 60) {
            return 'B';
        }
        if ($score >= 50) {
            return 'C+';
        }
        if ($score >= 40) {
            return 'C';
        }
        if ($score >= 30) {
            return 'D';
        }

        return 'F';
    }
}
