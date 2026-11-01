<?php

namespace App\ExaminationHub\Controllers;

use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Services\MultiCenterMonitoringService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Multi-center live monitoring controller.
 *
 * Provides a central view of all candidate activity across all examination
 * centers. Only available on central instances (APP_ROLE=central).
 */
class MultiCenterLiveMonitoringController extends Controller
{
    public function __construct(
        private readonly MultiCenterMonitoringService $monitoringService
    ) {}

    /**
     * Global live monitoring dashboard — all centers, all exams.
     */
    public function index(Request $request): View
    {
        $filters = [
            'center_id' => $request->string('center_id')->toString(),
            'exam_id' => $request->string('exam_id')->toString(),
            'status' => $request->string('status')->toString(),
        ];

        // Remove empty filters
        $filters = array_filter($filters, fn ($v) => $v !== '');

        $data = $this->monitoringService->getAllCentersMonitoringData(empty($filters) ? null : $filters);
        $exams = $this->monitoringService->getPublishedExamsWithCoverage();

        return view('examination-hub.live-monitoring.multi-center', [
            'initialData' => $data,
            'exams' => $exams,
            'filters' => $filters,
        ]);
    }

    /**
     * Single exam monitoring across all centers.
     */
    public function show(GeneralExam $exam): View
    {
        $data = $this->monitoringService->getExamMonitoringData($exam);

        return view('examination-hub.live-monitoring.multi-center-exam', [
            'exam' => $exam,
            'initialData' => $data,
        ]);
    }

    /**
     * API endpoint for polling multi-center data.
     */
    public function apiAllCenters(Request $request)
    {
        $filters = [
            'center_id' => $request->string('center_id')->toString(),
            'exam_id' => $request->string('exam_id')->toString(),
            'status' => $request->string('status')->toString(),
        ];

        $filters = array_filter($filters, fn ($v) => $v !== '');

        $data = $this->monitoringService->getAllCentersMonitoringData(empty($filters) ? null : $filters);

        return response()->json($data);
    }

    /**
     * API endpoint for polling a single exam's multi-center data.
     */
    public function apiExam(GeneralExam $exam)
    {
        $data = $this->monitoringService->getExamMonitoringData($exam);

        return response()->json($data);
    }
}
