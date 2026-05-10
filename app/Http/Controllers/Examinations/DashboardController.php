<?php

namespace App\Http\Controllers\Examinations;

use App\Examinations\Contracts\ExamDashboardServiceInterface;
use App\Examinations\Traits\EnsuresExamOwnership;
use App\Http\Controllers\Controller;
use App\Jobs\SendExamRemindersJob;
use App\Models\GeneralExam;
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

        return view('examinations-hub.dashboard.index', [
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
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('examinations-hub.dashboard.show', [
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

        return view('examinations-hub.dashboard.manage', [
            'exams' => $this->dashboardService->listForOwner((int) auth()->id(), $filters),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('examinations-hub.exams.create');
    }

    public function subscriptions(): View
    {
        return view('examinations-hub.subscriptions.dashboard');
    }

    public function admin(): View
    {
        abort_unless(in_array((string) auth()->user()?->role?->value, ['admin', 'owner'], true), 403);

        return view('examinations-hub.admin.index');
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
}
