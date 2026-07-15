<?php

namespace App\ExaminationHub\Controllers;

use App\ExaminationHub\Contracts\ExamParticipantAccessServiceInterface;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamConfiguredParticipant;
use App\ExaminationHub\Traits\EnsuresExamOwnership;
use App\Http\Controllers\Controller;
use App\Services\GeneralExam\GeneralExamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParticipantController extends Controller
{
    use EnsuresExamOwnership;

    public function __construct(
        private readonly ExamParticipantAccessServiceInterface $accessService,
        private readonly GeneralExamService $generalExamService
    ) {}

    public function joinEntry(): View
    {
        return view('examination-hub.join', ['exam' => null, 'code' => '']);
    }

    public function joinLookup(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50'],
        ]);

        $exam = GeneralExam::findByAccessCode($data['code']);
        if (! $exam) {
            return back()->withErrors(['code' => 'Invalid exam code.'])->withInput();
        }

        return redirect()->route('examination-hub.join', ['code' => $exam->access_code]);
    }

    public function storeConfigured(Request $request, GeneralExam $exam): RedirectResponse
    {
        $this->ensureOwnerAccess($exam);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'unique_code' => ['nullable', 'string', 'max:100'],
        ]);

        $this->accessService->registerConfiguredParticipant($exam, $data);

        return back()->with('success', 'Configured participant added.');
    }

    public function importConfigured(Request $request, GeneralExam $exam): RedirectResponse
    {
        $this->ensureOwnerAccess($exam);

        $request->validate([
            'participants_csv' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $result = $this->accessService->importConfiguredParticipants($exam, $request->file('participants_csv')->getRealPath());

        if (! $result['success']) {
            return back()->with('error', $result['errors'][0] ?? 'Could not import participants.');
        }

        $response = back()->with('success', "{$result['imported']} participant(s) imported successfully.");

        if (! empty($result['errors'])) {
            $response = $response->with('warning', count($result['errors']).' row(s) were skipped: '.implode(' ', $result['errors']));
        }

        return $response;
    }

    public function toggleConfigured(GeneralExam $exam, GeneralExamConfiguredParticipant $participant): RedirectResponse
    {
        $this->ensureOwnerAccess($exam);

        abort_unless($participant->general_exam_id === $exam->id, 404);

        $participant->update(['is_active' => ! $participant->is_active]);

        $status = $participant->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "{$participant->name} has been {$status}.");
    }

    public function destroyConfigured(GeneralExam $exam, GeneralExamConfiguredParticipant $participant): RedirectResponse
    {
        $this->ensureOwnerAccess($exam);

        abort_unless($participant->general_exam_id === $exam->id, 404);

        $name = $participant->name;
        $participant->delete();

        return back()->with('success', "{$name} has been removed.");
    }

    public function joinForm(string $code): View
    {
        $exam = GeneralExam::findByAccessCode($code);
        abort_unless($exam, 404);

        return view('examination-hub.join', ['exam' => $exam, 'code' => $exam->access_code]);
    }

    public function attemptJoin(Request $request, string $code): RedirectResponse
    {
        $exam = GeneralExam::findByAccessCode($code);
        abort_unless($exam, 404);

        $requiredFields = collect($exam->participant_required_fields ?? ['name', 'email'])->map(fn ($f) => strtolower((string) $f))->all();
        $nameRule = in_array('name', $requiredFields, true) ? ['required', 'string', 'max:255'] : ['nullable', 'string', 'max:255'];
        $emailRule = in_array('email', $requiredFields, true) ? ['required', 'email', 'max:255'] : ['nullable', 'email', 'max:255'];
        $codeRule = in_array('code', $requiredFields, true) ? ['required', 'string', 'max:100'] : ['nullable', 'string', 'max:100'];

        $data = $request->validate([
            'name' => $nameRule,
            'email' => $emailRule,
            'unique_code' => $codeRule,
        ]);

        if ($exam->starts_at && now()->lt($exam->starts_at)) {
            return back()->withErrors(['join' => 'This exam is not open yet.'])->withInput();
        }
        if ($exam->ends_at && now()->gt($exam->ends_at)) {
            return back()->withErrors(['join' => 'This exam is already closed.'])->withInput();
        }

        $access = $this->accessService->authorizeJoinByCode($exam, $data['name'], $data['email'], $data['unique_code'] ?? null);
        if (! ($access['allowed'] ?? false)) {
            return back()->withErrors(['join' => $access['message'] ?? 'You are not eligible to join this exam.'])->withInput();
        }

        $participant = $this->accessService->createOrReuseParticipant($data['name'], $data['email']);
        $submission = $this->generalExamService->getOrCreateSubmission($exam, \App\ExaminationHub\Models\GeneralExamParticipant::class, $participant->id, [
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('general-exams.take', $submission);
    }
}
