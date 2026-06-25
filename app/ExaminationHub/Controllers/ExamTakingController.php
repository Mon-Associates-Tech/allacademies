<?php

namespace App\ExaminationHub\Controllers;

use App\ExaminationHub\Models\ExamParticipantHeartbeat;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamConfiguredParticipant;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\ExaminationHub\Services\ExamGradingService;
use App\ExaminationHub\Services\ExamParticipantAccessService;
use App\ExaminationHub\Services\LiveMonitoringService;
use App\Http\Controllers\Controller;
use App\Notifications\ResultAccessNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
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
        session()->forget([
            'exam_submission_id',
            'exam_participant_data',
            'exam_heartbeat_token',
            'exam_ip_address',
            'exam_user_agent_hash',
            'exam_authenticated_at',
        ]);

        // Rate limiting: 5 attempts per 5 minutes per IP
        $key = 'exam_auth:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'access_code' => "Too many authentication attempts. Please try again in {$seconds} seconds."
            ])->withInput();
        }
        
        RateLimiter::hit($key, 300); // 5 minutes decay
        
        $data = $request->validate([
            'access_code' => ['required', 'string'],
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'unique_code' => ['nullable', 'string', 'max:255'],
        ]);

        $exam = GeneralExam::findByAccessCode($data['access_code']);

        if (! $exam) {
            return back()->withErrors(['access_code' => 'Invalid access code.'])->withInput();
        }

        if (! $exam->isActive()) {
            return back()->withErrors(['access_code' => 'This examination is not currently active.'])->withInput();
        }

        // Delegate participant validation to the access service
        $access = $this->accessService->authorizeJoinByCode(
            $exam,
            $data['name'] ?? '',
            $data['email'] ?? '',
            $data['unique_code'] ?? null
        );

        if (! $access['allowed']) {
            return back()->withErrors(['access_code' => $access['message'] ?? 'You are not authorised to take this exam.'])->withInput();
        }

        $submission = $this->resolveSubmission($exam, $data, $access);

        if (! $submission) {
            return back()->withErrors(['access_code' => 'You have reached the maximum number of attempts.'])->withInput();
        }

        $heartbeat = $this->monitoringService->initializeSession($submission, [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Session security hardening
        session()->regenerate(); // Prevent session fixation
        session([
            'exam_submission_id' => $submission->id,
            'exam_participant_data' => [
                'name' => $data['name'] ?? null,
                'email' => $data['email'] ?? null,
            ],
            'exam_heartbeat_token' => $heartbeat->session_token,
            'exam_ip_address' => $request->ip(), // Bind session to IP
            'exam_user_agent_hash' => hash('sha256', $request->userAgent()), // Device fingerprint
            'exam_authenticated_at' => now()->toIso8601String(),
        ]);

        return redirect()->route('examination-hub.take.preview', $exam);
    }

    public function preview(GeneralExam $exam): View|RedirectResponse
    {
        $submission = $this->resolveSessionSubmission($exam);

        if (! $submission) {
            return redirect()->route('examination-hub.take.join')
                ->withErrors(['error' => 'Invalid session. Please join again.']);
        }

        if ($submission->submitted_at) {
            return redirect()->route('examination-hub.take.completed', $exam);
        }

        // Load exam with required relationships
        $exam->load([
            'sections' => fn ($q) => $q->orderBy('order')->withCount('questions'),
            'academicSubject' => fn ($q) => $q->with('academicLevel'),
        ]);

        $participantData = session('exam_participant_data', []);
        $configuredParticipant = $this->resolveConfiguredParticipant($submission);

        return view('examination-hub.take.preview', [
            'exam'              => $exam,
            'submission'        => $submission,
            'candidateName'     => $configuredParticipant?->name ?? $submission->participant_name ?? $participantData['name'] ?? 'Anonymous',
            'candidateEmail'    => $configuredParticipant?->email ?? $submission->participant_email ?? $participantData['email'] ?? null,
            'proctoringEnabled' => (bool) $exam->proctoring_enabled,
        ]);
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

        // If the exam hasn't opened yet show the waiting/countdown page — that
        // is the only scenario where the start view adds value.
        if ($exam->starts_at && $exam->starts_at->isFuture()) {
            $exam->load(['sections' => fn ($q) => $q->orderBy('order')->withCount('questions')]);

            return view('examination-hub.take.start', [
                'exam'              => $exam,
                'submission'        => $submission,
                'proctoringEnabled' => (bool) $exam->proctoring_enabled,
            ]);
        }

        // Resume from the last saved position when the candidate returns mid-exam.
        $lastPos = $submission->last_position ?? null;
        if ($submission->isInProgress() && is_array($lastPos) && isset($lastPos['section'])) {
            return redirect()->route('examination-hub.take.section', [$exam, (int) $lastPos['section']])
                ->with('restored_question', (int) ($lastPos['question'] ?? 0));
        }

        // First visit — go straight to section 0.
        return redirect()->route('examination-hub.take.section', [$exam, 0]);
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

        $exam->load(['sections.questions' => fn ($q) => $q->orderBy('order')]);

        $section = $exam->sections->get($sectionIndex);

        if (! $section) {
            abort(404, 'Section not found.');
        }

        $questions = $this->resolveQuestionOrder($exam, $section, $submission);

        // Calculate time remaining.
        // Rule: for a single-section exam that has an exam-level duration, the exam
        // clock is authoritative (the section's own time_limit_minutes is ignored for
        // timing purposes so the two clocks never diverge).
        $timeRemaining    = null;
        $isSingleSection  = $exam->sections->count() === 1;

        if ($isSingleSection && $exam->duration_in_minutes && $submission->started_at) {
            $examEndsAt    = $submission->started_at->copy()->addMinutes($exam->duration_in_minutes);
            $timeRemaining = max(0, (int) now()->diffInSeconds($examEndsAt, false));
        } elseif ($isSingleSection && $exam->duration_in_minutes) {
            $timeRemaining = $exam->duration_in_minutes * 60;
        } elseif ($section->time_limit_minutes) {
            $sectionStartTimes = $submission->section_start_times ?? [];
            $sectionKey        = (string) $section->id;
            $startedAt         = $sectionStartTimes[$sectionKey] ?? null;

            if ($startedAt) {
                $elapsed       = now()->timestamp - $startedAt;
                $timeRemaining = max(0, ($section->time_limit_minutes * 60) - $elapsed);
            } else {
                $timeRemaining = $section->time_limit_minutes * 60;
            }
        }

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
            'timeRemaining' => $timeRemaining,
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

        // Enforce time limits server-side (exam clock for single-section, section clock otherwise)
        $exam->load(['sections' => fn ($q) => $q->orderBy('order')->with('questions')]);
        $section         = $exam->sections->get($data['section_index']);
        $isSingleSection = $exam->sections->count() === 1;

        if ($isSingleSection && $exam->duration_in_minutes) {
            if ($submission->started_at) {
                $examEndsAt = $submission->started_at->copy()->addMinutes($exam->duration_in_minutes);
                if (now()->greaterThanOrEqualTo($examEndsAt)) {
                    // Auto-submit the exam if it's past the time limit
                    $submission->update([
                        'submitted_at' => now(),
                        'time_taken_minutes' => (int) $submission->started_at->diffInMinutes(now()),
                        'status' => GeneralExamSubmission::STATUS_SUBMITTED,
                        'auto_submitted' => true,
                        'auto_submit_reason' => 'Time limit exceeded (server-side auto-submit)',
                    ]);

                    // Dispatch grading as a background job
                    $this->gradingService->dispatchGrading($submission);

                    // Redirect to the completed page
                    return $request->expectsJson()
                        ? response()->json(['status' => 'auto_submitted'])
                        : redirect()->route('examination-hub.take.completed', $exam);
                }
            }
        } elseif ($section && $section->time_limit_minutes) {
            $sectionStartTimes = $submission->section_start_times ?? [];
            $sectionKey        = (string) $section->id;
            $startedAt         = $sectionStartTimes[$sectionKey] ?? null;
            if (! $startedAt || now()->timestamp >= ($startedAt + ($section->time_limit_minutes * 60))) {
                // Auto-submit the exam if it's past the section time limit
                $submission->update([
                    'submitted_at' => now(),
                    'time_taken_minutes' => (int) $submission->started_at->diffInMinutes(now()),
                    'status' => GeneralExamSubmission::STATUS_SUBMITTED,
                    'auto_submitted' => true,
                    'auto_submit_reason' => "Section '{$section->title}' time limit exceeded (server-side auto-submit)",
                ]);

                // Dispatch grading as a background job
                $this->gradingService->dispatchGrading($submission);

                // Redirect to the completed page
                return $request->expectsJson()
                    ? response()->json(['status' => 'auto_submitted'])
                    : redirect()->route('examination-hub.take.completed', $exam);
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

        // Check if the submission has any responses saved
        $hasResponses = !empty($submission->responses) && is_array($submission->responses) && count($submission->responses) > 0;

        DB::transaction(function () use ($submission, $hasResponses) {
            $timeTaken = $submission->started_at
                ? (int) $submission->started_at->diffInMinutes(now())
                : 0;

            // Ensure that the submission status is properly updated
            $updateData = [
                'submitted_at' => now(),
                'time_taken_minutes' => $timeTaken,
                'status' => GeneralExamSubmission::STATUS_SUBMITTED,
            ];

            // If there are no responses, log this as a potential issue for review
            if (!$hasResponses) {
                $updateData['requires_manual_review'] = true;
                $updateData['teacher_feedback'] = 'No responses were recorded for this submission. May require manual review.';
            }

            $submission->update($updateData);

            $heartbeat = ExamParticipantHeartbeat::where('general_exam_submission_id', $submission->id)->first();
            $heartbeat?->markCompleted();
        });

        // Attempt to save any pending responses without blocking submission
        try {
            $this->savePendingResponses($request, $exam, $submission);
        } catch (\Exception $e) {
            \Log::error("Failed to save pending responses before submission: " . $e->getMessage());
            // Continue with submission even if saving fails
        }

        // Dispatch grading as a background job — don't block the response
        // Even if no responses were saved, the grading job will handle it appropriately
        $this->gradingService->dispatchGrading($submission);
        
        // Generate secure token for result access
        if ($submission->participant_email) {
            $token = Str::random(64);
            Cache::put("result_access:{$token}", [
                'submission_id' => $submission->id,
                'email' => $submission->participant_email,
                'expires_at' => now()->addDays(7),
            ], now()->addDays(7));
            
            // Send email notification with secure link
            try {
                $submission->notify(new ResultAccessNotification($submission, $token));
            } catch (\Exception $e) {
                \Log::error('Failed to send result access email: ' . $e->getMessage(), [
                    'submission_id' => $submission->id,
                    'email' => $submission->participant_email,
                ]);
            }
        }

        // Persist a lightweight completion record before clearing the main exam
        // session keys, so completed() can retrieve the submission even after
        // exam_submission_id has been forgotten.
        session([
            'exam_completed_submission_id'    => $submission->id,
            'exam_completed_participant_email' => $submission->participant_email,
        ]);

        session()->forget(['exam_submission_id', 'exam_participant_data', 'exam_heartbeat_token']);

        return redirect()->route('examination-hub.take.completed', $exam);
    }

    /**
     * Save any pending responses in the final submission request.
     */
    private function savePendingResponses(Request $request, GeneralExam $exam, GeneralExamSubmission $submission): void
    {
        $data = $request->validate([
            'responses' => ['sometimes', 'array'],
        ]);

        if (empty($data['responses']) || !is_array($data['responses'])) {
            return;
        }

        $responses = $submission->responses ?? [];

        $exam->load(['sections' => fn ($q) => $q->orderBy('order')->with('questions')]);

        foreach ($data['responses'] as $questionId => $response) {
            if (!is_numeric($questionId)) {
                continue;
            }

            $questionId = (int)$questionId;

            // Ensure the question belongs to the exam
            $found = false;
            foreach ($exam->sections as $section) {
                if ($section->questions->contains('id', $questionId)) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                continue;
            }

            $responses[$questionId] = [
                'response' => (string)$response,
                'answered_at' => now()->toIso8601String(),
            ];
        }

        $submission->update(['responses' => $responses]);
    }

    public function completed(GeneralExam $exam): View
    {
        // Two paths lead here; the session state differs between them:
        //
        // a) Auto-submit (Livewire performAutoSubmit): the Livewire component
        //    redirects client-side without touching the session, so
        //    exam_submission_id is still present and resolveSessionSubmission()
        //    returns the submission directly.
        //
        // b) Manual submit (submit() controller): the session is cleared before
        //    redirecting, so we fall back to exam_completed_submission_id which
        //    submit() writes just before calling session()->forget().
        $submission = $this->resolveSessionSubmission($exam);

        if (! $submission) {
            $id = session('exam_completed_submission_id');
            $submission = $id ? GeneralExamSubmission::find($id) : null;
        }

        // Derive email: live session key (auto-submit) → completion key (manual
        // submit) → submission model (last resort, covers both paths).
        $participantEmail = session('exam_participant_data.email')
                         ?? session('exam_completed_participant_email')
                         ?? $submission?->participant_email;

        return view('examination-hub.take.completed', [
            'exam'             => $exam,
            'submission'       => $submission,
            'participantEmail' => $participantEmail,
        ]);
    }
    
    /**
     * Show answer review page before final submission
     */
    public function review(GeneralExam $exam): View|RedirectResponse
    {
        $submission = $this->resolveSessionSubmission($exam);

        if (! $submission) {
            return redirect()->route('examination-hub.take.join')
                ->withErrors(['error' => 'Invalid session. Please join again.']);
        }

        if ($submission->submitted_at) {
            return redirect()->route('examination-hub.take.completed', $exam);
        }

        $exam->load(['sections.questions' => fn ($q) => $q->orderBy('order')]);

        // Build review data
        $reviewData = [];
        $totalQuestions = 0;
        $answeredQuestions = 0;

        foreach ($exam->sections as $sectionIndex => $section) {
            $sectionData = [
                'index' => $sectionIndex,
                'title' => $section->title,
                'questions' => [],
            ];

            foreach ($section->questions as $question) {
                $totalQuestions++;
                $isAnswered = isset($submission->responses[$question->id]);
                
                if ($isAnswered) {
                    $answeredQuestions++;
                }

                $sectionData['questions'][] = [
                    'id' => $question->id,
                    'question_text' => $question->question_text,
                    'question_type' => $question->question_type,
                    'marks' => $question->marks,
                    'response' => $submission->responses[$question->id]['response'] ?? null,
                    'is_answered' => $isAnswered,
                    'is_flagged' => isset($submission->flagged_questions[$question->id]),
                ];
            }

            $reviewData[] = $sectionData;
        }

        return view('examination-hub.take.review', [
            'exam' => $exam,
            'submission' => $submission,
            'reviewData' => $reviewData,
            'totalQuestions' => $totalQuestions,
            'answeredQuestions' => $answeredQuestions,
            'unansweredQuestions' => $totalQuestions - $answeredQuestions,
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
            $participantName = $cp->name;
            $participantEmail = $cp->email;
        } else {
            $type = 'general';
            $pid = ! empty($data['email']) ? abs(crc32($data['email'])) : null;
            $participantName = $data['name'] ?? 'Anonymous';
            $participantEmail = $data['email'] ?? null;
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
                'participant_name' => $participantName,
                'participant_email' => $participantEmail,
                'started_at' => now(),
                'responses' => [],
                'score' => 0,
                'status' => GeneralExamSubmission::STATUS_NOT_STARTED,
            ]
        );
    }

    private function resolveConfiguredParticipant(GeneralExamSubmission $submission): ?GeneralExamConfiguredParticipant
    {
        if (! in_array($submission->participant_type, ['configured', GeneralExamConfiguredParticipant::class], true)) {
            return null;
        }

        return GeneralExamConfiguredParticipant::query()
            ->whereKey($submission->participant_id)
            ->where('general_exam_id', $submission->general_exam_id)
            ->first();
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
