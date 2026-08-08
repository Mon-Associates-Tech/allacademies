<?php

namespace App\MockExam\Controllers;

use App\Http\Controllers\Controller;
use App\MockExam\Models\MockExam;
use App\MockExam\Services\MockExamMonitoringService;
use Illuminate\Http\Request;

class MockExamMonitoringController extends Controller
{
    public function __construct(
        private readonly MockExamMonitoringService $monitoringService
    ) {
    }

    public function index(MockExam $mockExam)
    {
        //$this->authorize('view', $mockExam);

        return view('mock-exam.monitoring.dashboard', [
            'mockExam' => $mockExam,
        ]);
    }
}
