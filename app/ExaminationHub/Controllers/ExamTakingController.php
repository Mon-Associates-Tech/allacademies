<?php

namespace App\ExaminationHub\Controllers;

use App\ExaminationHub\Models\ExamParticipantHeartbeat;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\ExaminationHub\Services\ExamGradingService;
use App\ExaminationHub\Services\ExamParticipantAccessService;
use App\ExaminationHub\Services\LiveMonitoringService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExamTakingController extends Controller
{
    public function __construct(
        private readonly ExamParticipantAccessService $accessService,
        private readonly ExamGradingService $gradingService,
        private readonly LiveMonitoringService $monitoringService,
    ) {}

    public function join(): View
    {
        return view('examination-hub.take.join');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'access_code' => ['required', 'string'],
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'unique_code' => ['nullable', 'string', 'max:255'],
        ]);

        $exam = GeneralExam::findByAccessCode($data['access_code']);

        if (! $exam) {
            return back()->withErrors(['access_code' => 'Invalid access code.']);
        }

        if (! $exam->isActive()) {
            return back()->withErrors(['access_code' => 'This examination is not currently active.']);
        }

        // Delegate participant validation to the access service
        $access = $this->accessService->authorizeJoinByCode(
            $exam,
            $data['name'] ?? '',
            $data['email'] ?? '',
            $data['unique_code'] ?? null
        );

        if (! $access['allowed']) {
            return back()->withErrors(['access_code' => $access['message'] ?? 'You are not authorised to take this exam.']);
        }

        $submission = $this->resolveSubmission($exam, $data, $access);

        if (! $submission) {
            return back()->withErrors(['access_code' => 'You have reached the maximum number of attempts.']);
        }

        $heartbeat = $this->monitoringService->initializeSession($submission, [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        session([
            'exam_submission_id' => $submission->id,
            'exam_participant_data' => [
                'name' => $data['name'] ?? null,
                'email' => $data['email'] ?? null,
            ],
            'exam_heartbeat_token' => $heartbeat->session_token,
        ]);

        return redirect()->route('examination-hub.take.start', $exam);
    }

    public function start(GeneralExam $exam): View|RedirectResponse
    {
        $submission = $this->resolveSessionSubmission($exam);

        if (! $submission) {
            return redirect()->route('examination-hub.take.join')
                ->withErrors(['error' => 'Invalid session. Please join again.']);
        }

        if ($submission->submitted_at) {
            return redirect()->route('examination-hub.take.completed', $exam);
        }

        if ($submission->status === GeneralExamSubmission::STATUS_NOT_STARTED) {
            $submission->update([
                'status' => GeneralExamSubmission::STATUS_IN_PROGRESS,
                'started_at' => $submission->started_at ?? now(),
            ]);
        }

        $exam->load(['sections' => fn ($q) => $q->orderBy('order')->withCount('questions')]);

        // If there's a saved last position and the submission is in progress,
        // redirect the participant straight to that section/question.
        $lastPos = $submission->last_position ?? null;
        if ($submission->isInProgress() && is_array($lastPos) && isset($lastPos['section'])) {
            $sectionIndex = (int) $lastPos['section'];
            $questionIndex = (int) ($lastPos['question'] ?? 0);

            return redirect()->route('examination-hub.take.section', [$exam, $sectionIndex])
                ->with('restored_question', $questionIndex);
        }

        return view('examination-hub.take.start', [
            'exam' => $exam,
            'submission' => $submission,
            'proctoringEnabled' => (bool) $exam->proctoring_enabled,
        ]);
    }

    public function section(GeneralExam $exam, int $sectionIndex): View|RedirectResponse
    {
        $submission = $this->resolveSessionSubmission($exam);

        if (! $submission) {
            return redirect()->route('examination-hub.take.join')
                ->withErrors(['error' => 'Invalid session. Please join again.']);
        }

        if ($submission->submitted_at) {
            return redirect()->route('examination-hub.take.completed', $exam);
        }

        $exam->load(['sections' => fn ($q) => $q->orderBy('order')]);

        $section = $exam->sections->get($sectionIndex);

        if (! $section) {
            abort(404, 'Section not found.');
        }

        $questions = $this->resolveQuestionOrder($exam, $section, $submission);

        // Section start times are set by the client when the participant
        // explicitly begins the section (after acknowledging instructions).

        return view('examination-hub.take.section', [
            'exam' => $exam,
            'submission' => $submission,
            'section' => $section,
            'sectionIndex' => $sectionIndex,
            'questions' => $questions,
            'responses' => $submission->responses ?? [],
            'proctoringEnabled' => (bool) $exam->proctoring_enabled,
            'proctoringSessionId' => session('exam_heartbeat_token'),
            'sectionTitle' => $section->title,
            'sectionTimeLimit' => $section->time_limit_minutes ? $section->time_limit_minutes * 60 : null,
            'totalMarks' => $section->total_marks,
        ]);
    }

    public function saveResponse(Request $request, GeneralExam $exam): JsonResponse|RedirectResponse
    {
        $submission = $this->resolveSessionSubmission($exam);

        if (! $submission) {
            return $request->expectsJson()
                ? response()->json(['error' => 'Invalid session.'], 403)
                : abort(403);
        }

        if ($submission->submitted_at) {
            return $request->expectsJson()
                ? response()->json(['status' => 'already_submitted'])
                : redirect()->route('examination-hub.take.completed', $exam);
        }

        $data = $request->validate([
            'question_id' => ['required', 'integer', 'exists:general_exam_questions,id'],
            'response' => ['required', 'string'],
            'section_index' => ['required', 'integer'],
        ]);

        // Enforce section time limits server-side
        $exam->load(['sections' => fn ($q) => $q->orderBy('order')->with('questions')]);
        $section = $exam->sections->get($data['section_index']);
        if ($section && $section->time_limit_minutes) {
            $sectionStartTimes = $submission->section_start_times ?? [];
            $sectionKey = (string) $section->id;
            $startedAt = $sectionStartTimes[$sectionKey] ?? null;
            if (! $startedAt || now()->timestamp >= ($startedAt + ($section->time_limit_minutes * 60))) {
                return $request->expectsJson()
                    ? response()->json(['error' => 'Section time expired'], 403)
                    : back()->withErrors(['error' => 'Section time expired.']);
            }
        }

        $responses = $submission->responses ?? [];
        $responses[$data['question_id']] = [
            'response' => $data['response'],
            'answered_at' => now()->toIso8601String(),
        ];

        $submission->update(['responses' => $responses]);

        return $request->expectsJson()
            ? response()->json(['status' => 'saved', 'question_id' => $data['question_id']])
            : back()->with('success', 'Response saved.');
    }

    public function submit(Request $request, GeneralExam $exam): RedirectResponse
    {
        $submission = $this->resolveSessionSubmission($exam);

        if (! $submission) {
            abort(403, 'Invalid session.');
        }

        if ($submission->submitted_at) {
            return redirect()->route('examination-hub.take.completed', $exam);
        }

        DB::transaction(function () use ($submission) {
            $timeTaken = $submission->started_at
                ? (int) $submission->started_at->diffInMinutes(now())
                : 0;

            $submission->update([
                'submitted_at' => now(),
                'time_taken_minutes' => $timeTaken,
                'status' => GeneralExamSubmission::STATUS_SUBMITTED,
            ]);

            $heartbeat = ExamParticipantHeartbeat::where('general_exam_submission_id', $submission->id)->first();
            $heartbeat?->markCompleted();
        });

        // Dispatch grading as a background job — don't block the response
        $this->gradingService->dispatchGrading($submission);

        session()->forget(['exam_submission_id', 'exam_participant_data', 'exam_heartbeat_token']);

        return redirect()->route('examination-hub.take.completed', $exam);
    }

    public function completed(GeneralExam $exam): View
    {
        return view('examination-hub.take.completed', [
            'exam' => $exam,
            'participantEmail' => session('exam_participant_data.email'),
        ]);
    }

    // ─── Private helpers ─────────────────────────────────────────────────────

    private function resolveSessionSubmission(GeneralExam $exam): ?GeneralExamSubmission
    {
        $id = session('exam_submission_id');
        if (! $id) {
            return null;
        }

        $submission = GeneralExamSubmission::find($id);

        if (! $submission || $submission->general_exam_id !== $exam->id) {
            return null;
        }

        return $submission;
    }

    private function resolveSubmission(GeneralExam $exam, array $data, array $access): ?GeneralExamSubmission
    {
        if ($access['mode'] === 'configured' && isset($access['configured_participant'])) {
            $cp = $access['configured_participant'];
            $type = 'configured';
            $pid = $cp->id;
        } else {
            $type = 'general';
            $pid = ! empty($data['email']) ? abs(crc32($data['email'])) : null;
        }

        // First, check if there's an existing submission that hasn't been started yet
        // If so, we can reuse that submission instead of creating a new one
        $existingSubmission = GeneralExamSubmission::where([
            'general_exam_id' => $exam->id,
            'participant_type' => $type,
            'participant_id' => $pid,
        ])
        ->whereNull('submitted_at') // Not submitted yet
        ->where('status', GeneralExamSubmission::STATUS_NOT_STARTED) // Not started
        ->first();

        // If we find an existing submission that was created but never started, 
        // we can reuse it (meaning authentication failed previously)
        if ($existingSubmission) {
            return $existingSubmission;
        }

        // Check if the participant can attempt the exam based on max attempts
        if (! $exam->canParticipantAttempt($type, $pid)) {
            return null;
        }

        return GeneralExamSubmission::firstOrCreate(
            [
                'general_exam_id' => $exam->id,
                'participant_type' => $type,
                'participant_id' => $pid,
                'submitted_at' => null,
            ],
            [
                'participant_name' => $data['name'] ?? 'Anonymous',
                'participant_email' => $data['email'] ?? null,
                'started_at' => now(),
                'responses' => [],
                'score' => 0,
                'status' => GeneralExamSubmission::STATUS_NOT_STARTED,
            ]
        );
    }

    private function resolveQuestionOrder(GeneralExam $exam, $section, GeneralExamSubmission $submission): \Illuminate\Support\Collection
    {
        $questions = $section->questions;

        if (! ($exam->is_randomized || $section->is_randomized) || $questions->isEmpty()) {
            return $questions;
        }

        $randomizedOrder = $submission->randomized_question_order ?? [];
        $sectionKey = "section_{$section->id}";

        if (! isset($randomizedOrder[$sectionKey])) {
            $randomizedOrder[$sectionKey] = $questions->pluck('id')->shuffle()->values()->toArray();
            $submission->update(['randomized_question_order' => $randomizedOrder]);
        }

        return collect($randomizedOrder[$sectionKey])
            ->map(fn ($id) => $questions->firstWhere('id', $id))
            ->filter()
            ->values();
    }
}