<?php

namespace App\ExaminationHub\Controllers;

use App\ExaminationHub\Contracts\ExamDashboardServiceInterface;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Traits\EnsuresExamOwnership;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    use EnsuresExamOwnership;

    public function __construct(private readonly ExamDashboardServiceInterface $dashboardService) {}

    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->string('search')->toString(),
            'status' => $request->string('status')->toString(),
        ];

        return view('examination-hub.dashboard.index', [
            'summary' => $this->dashboardService->summaryForOwner((int) auth()->id()),
            'exams' => $this->dashboardService->listForOwner((int) auth()->id(), $filters),
            'filters' => $filters,
        ]);
    }

    public function show(GeneralExam $exam): View
    {
        $this->ensureOwnerAccess($exam);
        $exam->loadCount(['sections', 'questions', 'submissions']);
        $exam->load('sections');

        $configuredParticipants = $exam->configuredParticipants()
            ->orderBy('name')
            ->get();

        return view('examination-hub.dashboard.show', [
            'exam' => $exam,
            'sectionNavigator' => $this->dashboardService->sectionNavigator($exam),
            'configuredCount' => $configuredParticipants->count(),
            'configuredParticipants' => $configuredParticipants,
        ]);
    }

    public function manage(Request $request): View
    {
        $filters = [
            'search' => $request->string('search')->toString(),
            'status' => $request->string('status')->toString(),
        ];

        return view('examination-hub.dashboard.manage', [
            'exams' => $this->dashboardService->listForOwner((int) auth()->id(), $filters),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('examination-hub.exams.create');
    }

    public function subscriptions(): View
    {
        return view('examination-hub.subscriptions.dashboard');
    }

    public function admin(): View
    {
        abort_unless(in_array((string) auth()->user()?->role?->value, ['admin', 'owner'], true), 403);

        return view('examination-hub.admin.index');
    }

    public function sendInvitations(GeneralExam $exam): RedirectResponse
    {
        // Moved to ExamSettingsController
        return app(ExamSettingsController::class)->sendInvitations($exam);
    }

    public function sendReminder(GeneralExam $exam): RedirectResponse
    {
        return app(ExamSettingsController::class)->sendReminder($exam);
    }

    public function updateReminderSettings(Request $request, GeneralExam $exam): RedirectResponse
    {
        return app(ExamSettingsController::class)->updateReminderSettings($request, $exam);
    }

    public function updateProctoringSettings(Request $request, GeneralExam $exam): RedirectResponse
    {
        return app(ExamSettingsController::class)->updateProctoringSettings($request, $exam);
    }

    public function toggleResults(GeneralExam $exam): RedirectResponse
    {
        return app(ExamSettingsController::class)->toggleResults($exam);
    }

    public function updateViolationSettings(Request $request, GeneralExam $exam): RedirectResponse
    {
        return app(ExamSettingsController::class)->updateViolationSettings($request, $exam);
    }
}
