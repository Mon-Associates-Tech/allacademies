<?php

namespace App\ExaminationHub\Controllers;

use App\ExaminationHub\Services\ExamPerformanceReportService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PerformanceReportController extends Controller
{
    public function __construct(
        private ExamPerformanceReportService $reportService
    ) {}

    public function index(): View
    {
        return view('examination-hub.reports.index');
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'use_ai' => ['nullable', 'boolean'],
        ]);

        $useAi = $validated['use_ai'] ?? true;

        $result = $this->reportService->generateReport([
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'school_id' => auth()->user()->school_id,
        ], $useAi);

        if (!$result['success']) {
            return back()->withErrors(['error' => $result['error']]);
        }

        return view('examination-hub.reports.show', [
            'report' => $result['report'],
            'data' => $result['data'],
            'usage' => $result['usage'],
            'type' => $result['type'] ?? 'ai',
        ]);
    }
}
