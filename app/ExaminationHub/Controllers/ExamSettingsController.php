<?php

namespace App\ExaminationHub\Controllers;

use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Traits\EnsuresExamOwnership;
use App\Http\Controllers\Controller;
use App\Jobs\SendExamRemindersJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExamSettingsController extends Controller
{
    use EnsuresExamOwnership;

    // ─── Invitations & Reminders ─────────────────────────────────────────────

    public function sendInvitations(GeneralExam $exam): RedirectResponse
    {
        $this->ensureOwnerAccess($exam);

        $count = $exam->configuredParticipants()
            ->where('is_active', true)
            ->whereNotNull('email')
            ->count();

        if ($count === 0) {
            return back()->withErrors(['error' => 'No configured participants with email addresses found.']);
        }

        SendExamRemindersJob::dispatch($exam, false);

        return back()->with('success', "Invitations are being sent to {$count} participant(s).");
    }

    public function sendReminder(GeneralExam $exam): RedirectResponse
    {
        $this->ensureOwnerAccess($exam);

        $count = $exam->configuredParticipants()
            ->where('is_active', true)
            ->whereNotNull('email')
            ->count();

        if ($count === 0) {
            return back()->withErrors(['error' => 'No configured participants with email addresses found.']);
        }

        SendExamRemindersJob::dispatch($exam, true);

        return back()->with('success', "Reminders are being sent to {$count} participant(s).");
    }

    public function updateReminderSettings(Request $request, GeneralExam $exam): RedirectResponse
    {
        $this->ensureOwnerAccess($exam);

        $data = $request->validate([
            'send_reminders' => ['boolean'],
            'reminder_datetime' => ['nullable', 'date', 'after:now'],
        ]);

        $exam->update($data);

        return back()->with('success', 'Reminder settings updated.');
    }

    // ─── Proctoring ──────────────────────────────────────────────────────────

    public function updateProctoringSettings(Request $request, GeneralExam $exam): RedirectResponse
    {
        $this->ensureOwnerAccess($exam);

        $data = $request->validate([
            'proctoring_enabled' => ['nullable', 'boolean'],
            'auto_submit_on_violation' => ['nullable', 'boolean'],
            'auto_submit_high_severity_threshold' => ['nullable', 'integer', 'min:0', 'max:100'],
            'auto_submit_medium_severity_threshold' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $exam->update([
            'proctoring_enabled' => $request->boolean('proctoring_enabled'),
            'auto_submit_on_violation' => $request->boolean('auto_submit_on_violation'),
            'auto_submit_high_severity_threshold' => $data['auto_submit_high_severity_threshold'] ?? $exam->auto_submit_high_severity_threshold ?? 2,
            'auto_submit_medium_severity_threshold' => $data['auto_submit_medium_severity_threshold'] ?? $exam->auto_submit_medium_severity_threshold ?? 5,
        ]);

        return back()->with('success', 'Proctoring settings updated.');
    }

    // ─── Participant Mode ────────────────────────────────────────────────────

    public function updateParticipantMode(Request $request, GeneralExam $exam): RedirectResponse
    {
        $this->ensureOwnerAccess($exam);

        $data = $request->validate([
            'participant_mode' => ['required', 'in:general,configured,both']
        ]);

        $exam->update([
            'participant_mode' => $data['participant_mode']
        ]);

        return back()->with('success', 'Participant mode updated successfully.');
    }

    public function updateViolationSettings(Request $request, GeneralExam $exam): RedirectResponse
    {
        $this->ensureOwnerAccess($exam);

        $keys = array_keys(config('proctoring.violations', []));
        $settings = [];

        foreach ($keys as $key) {
            $settings[$key] = $request->boolean("violations.{$key}");
        }

        $exam->update(['violation_settings' => $settings]);

        return back()->with('success', 'Violation settings saved.');
    }

    // ─── Results ─────────────────────────────────────────────────────────────

    public function toggleResults(GeneralExam $exam): RedirectResponse
    {
        $this->ensureOwnerAccess($exam);

        if ($exam->result_visibility !== 'manual_release') {
            return back()->withErrors(['error' => 'Results can only be toggled for exams with manual release mode.']);
        }

        $exam->update(['results_released' => ! $exam->results_released]);

        $message = $exam->results_released
            ? 'Results released to participants.'
            : 'Results hidden from participants.';

        return back()->with('success', $message);
    }
}