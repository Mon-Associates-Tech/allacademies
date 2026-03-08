<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\StudentParent;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function __invoke(Request $request, $reportId)
    {
        // Verify parent has access to this report
        $parentStudents = StudentParent::where('user_id', Auth::id())->pluck('student_id');

        // Get report data based on report ID and type
        $reportData = $this->generateReportData($reportId, $request->get('type', 'performance'));

        // Verify the report belongs to one of the parent's wards
        if (! $parentStudents->contains($reportData['student_id'])) {
            abort(403, 'Unauthorized access to this report.');
        }

        $pdf = PDF::loadView('parent.reports.pdf', $reportData);

        return $pdf->download("report-{$reportId}.pdf");
    }

    private function generateReportData($reportId, $type)
    {
        // This would contain the logic to generate report data
        // For now, return sample structure
        return [
            'student_id' => $reportId,
            'type' => $type,
            'data' => [],
            'generated_at' => now(),
        ];
    }
}
