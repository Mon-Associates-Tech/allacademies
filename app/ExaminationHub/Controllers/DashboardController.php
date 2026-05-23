<?php

namespace App\ExaminationHub\Controllers;

use App\ExaminationHub\Contracts\ExamDashboardServiceInterface;
use App\ExaminationHub\Traits\EnsuresExamOwnership;
use App\Http\Controllers\Controller;
use App\Jobs\SendExamRemindersJob;
use App\ExaminationHub\Models\GeneralExam;
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
        $this->ensureOwnerAccess($exam);

        $participantsCount = $exam->configuredParticipants()
            ->where('is_active', true)
            ->whereNotNull('email')
            ->count();

        if ($participantsCount === 0) {
            return back()->withErrors(['error' => 'No configured participants with email addresses found.']);
        }

        SendExamRemindersJob::dispatch($exam, false);

        return back()->with('success', "Invitations are being sent to {$participantsCount} participant(s).");
    }

    public function sendReminder(GeneralExam $exam): RedirectResponse
    {
        $this->ensureOwnerAccess($exam);

        $participantsCount = $exam->configuredParticipants()
            ->where('is_active', true)
            ->whereNotNull('email')
            ->count();

        if ($participantsCount === 0) {
            return back()->withErrors(['error' => 'No configured participants with email addresses found.']);
        }

        SendExamRemindersJob::dispatch($exam, true);

        return back()->with('success', "Reminders are being sent to {$participantsCount} participant(s).");
    }

    public function updateReminderSettings(Request $request, GeneralExam $exam): RedirectResponse
    {
        $this->ensureOwnerAccess($exam);

        $data = $request->validate([
            'send_reminders' => ['boolean'],
            'reminder_datetime' => ['nullable', 'date', 'after:now'],
        ]);

        $exam->update($data);

        return back()->with('success', 'Reminder settings updated successfully.');
    }

    public function updateProctoringSettings(Request $request, GeneralExam $exam): RedirectResponse
    {
        $this->ensureOwnerAccess($exam);

        $data = $request->validate([
            'proctoring_enabled' => ['nullable', 'boolean'],
            'auto_submit_on_violation' => ['nullable', 'boolean'],
            'auto_submit_high_severity_threshold' => ['nullable', 'integer', 'min:0', 'max:100'],
            'auto_submit_medium_severity_threshold' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $proctoringEnabled = $request->boolean('proctoring_enabled');
        $autoSubmitEnabled = $request->boolean('auto_submit_on_violation');

        $exam->update([
            'proctoring_enabled' => $proctoringEnabled,
            'auto_submit_on_violation' => $autoSubmitEnabled,
            'auto_submit_high_severity_threshold' => $data['auto_submit_high_severity_threshold'] ?? $exam->auto_submit_high_severity_threshold ?? 2,
            'auto_submit_medium_severity_threshold' => $data['auto_submit_medium_severity_threshold'] ?? $exam->auto_submit_medium_severity_threshold ?? 5,
        ]);

        return back()->with('success', 'Proctoring settings updated successfully.');
    }

    public function toggleResults(GeneralExam $exam): RedirectResponse
    {
        $this->ensureOwnerAccess($exam);

        if ($exam->result_visibility !== 'manual_release') {
            return back()->withErrors(['error' => 'Results can only be toggled for exams with manual release mode.']);
        }

        $exam->update(['results_released' => !$exam->results_released]);

        $message = $exam->results_released 
            ? 'Results have been released to participants.' 
            : 'Results have been hidden from participants.';

        return back()->with('success', $message);
    }

    public function updateViolationSettings(Request $request, GeneralExam $exam): RedirectResponse
    {
        $this->ensureOwnerAccess($exam);

        $keys = array_keys(config('proctoring.violations', []));

        $settings = [];
        foreach ($keys as $key) {
            $settings[$key] = $request->boolean('violations.' . $key);
        }

        $exam->update(['violation_settings' => $settings]);

        return back()->with('success', 'Violation settings saved.');
    }
    
}
