<?php

namespace App\ExaminationHub\Controllers;

use App\ExaminationHub\Contracts\ExamDashboardServiceInterface;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamParticipantGroup;
use App\ExaminationHub\Traits\EnsuresExamOwnership;
use App\Http\Controllers\Controller;
use App\Models\AcademicSubject;
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
            'search'         => $request->string('search')->toString(),
            'status'         => $request->string('status')->toString(),
            'subject'        => $request->string('subject')->toString(), // Will hold academic_subject_id
            'sort_by'        => $request->string('sort_by')->toString(),
            'sort_direction' => $request->string('sort_direction')->toString(),
        ];

        // Fetch subjects that belong to exams owned by this user for the filter dropdown
        $userSubjectIds = GeneralExam::where('user_id', (int) auth()->id())
            ->whereNotNull('academic_subject_id')
            ->distinct()
            ->pluck('academic_subject_id');
            
        $availableSubjects = AcademicSubject::whereIn('id', $userSubjectIds)->orderBy('name')->get();

        return view('examination-hub.dashboard.index', [
            'summary'           => $this->dashboardService->summaryForOwner((int) auth()->id()),
            'exams'             => $this->dashboardService->listForOwner((int) auth()->id(), $filters),
            'filters'           => $filters,
            'availableSubjects' => $availableSubjects,
        ]);
    }

    public function show(GeneralExam $exam): View
    {
        $this->ensureOwnerAccess($exam);
        $exam->loadCount([
            'sections',
            'questions as questions_count' => fn ($q) => $q->where('excluded_from_grading', false),
            'submissions',
        ]);
        $exam->load(['sections.questions', 'participantGroup', 'academicSubject']);

        $configuredParticipants = $exam->configuredParticipants()
            ->orderBy('name')
            ->get();

        $configuredParticipantSource = null;
        if ($exam->participantGroup) {
            $configuredParticipantSource = $exam->participantGroup->parent
                ? 'List: '.$exam->participantGroup->parent->name.', Programme: '.$exam->participantGroup->name
                : 'List: '.$exam->participantGroup->name;
        }

        return view('examination-hub.dashboard.show', [
            'exam'                      => $exam,
            'sectionNavigator'          => $this->dashboardService->sectionNavigator($exam),
            'configuredCount'           => $configuredParticipants->count(),
            'configuredParticipants'    => $configuredParticipants,
            'participantGroups'         => GeneralExamParticipantGroup::withCount('members')->orderBy('name')->get(),
            'configuredParticipantSource' => $configuredParticipantSource,
        ]);
    }

    public function manage(Request $request): View
    {
        // Parse combined sort parameter (e.g., "created_at_desc" -> field: "created_at", direction: "desc")
        $sortByCombined = $request->string('sort')->toString() ?: 'created_at_desc';
        $parts = explode('_', $sortByCombined);
        $direction = array_pop($parts);
        $field = implode('_', $parts);

        $filters = [
            'search'         => $request->string('search')->toString(),
            'status'         => $request->string('status')->toString(),
            'subject'        => $request->string('subject')->toString(),
            'sort_by'        => $field,
            'sort_direction' => $direction,
            'view'           => $request->string('view')->toString() ?: 'list', // 'list' or 'table'
        ];

        // Fetch subjects that belong to exams owned by this user for the filter dropdown
        $userSubjectIds = GeneralExam::where('user_id', (int) auth()->id())
            ->whereNotNull('academic_subject_id')
            ->distinct()
            ->pluck('academic_subject_id');
            
        $availableSubjects = AcademicSubject::whereIn('id', $userSubjectIds)->orderBy('name')->get();

        return view('examination-hub.dashboard.manage', [
            'exams'             => $this->dashboardService->listForOwner((int) auth()->id(), $filters),
            'filters'           => $filters,
            'availableSubjects' => $availableSubjects,
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