<?php

namespace App\ExaminationHub\Controllers;

use App\ExaminationHub\Models\ExamParticipantHeartbeat;
use App\ExaminationHub\Models\ExamReadmissionGrant;
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
use Illuminate\Support\Facades\Log;
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

    // ─── Public routes ────────────────────────────────────────────────────────

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
                'access_code' => "Too many authentication attempts. Please try again in {$seconds} seconds.",
            ])->withInput();
        }

        RateLimiter::hit($key, 300);

        $data = $request->validate([
            'access_code' => ['required', 'string'],
            'name'        => ['nullable', 'string', 'max:255'],
            'email'       => ['nullable', 'email', 'max:255'],
            'unique_code' => ['nullable', 'string', 'max:255'],
        ]);

        $exam = GeneralExam::findByAccessCode($data['access_code']);

        if (! $exam) {
            return back()->withErrors(['access_code' => 'Invalid access code.'])->withInput();
        }

        if (! $exam->isActive()) {
            return back()->withErrors(['access_code' => 'This examination is not currently active.'])->withInput();
        }

        $access = $this->accessService->authorizeJoinByCode(
            $exam,
            $data['name'] ?? '',
            $data['email'] ?? '',
            $data['unique_code'] ?? null
        );

        if (! $access['allowed']) {
            return back()->withErrors([
                'access_code' => $access['message'] ?? 'You are not authorised to take this exam.',
            ])->withInput();
        }

        // resolveSubmission handles three cases:
        //  1. Existing not-started submission → reuse (auth-retry scenario)
        //  2. Active readmission grant        → apply (continue or fresh)
        //  3. Normal first attempt            → create new
        //  Returns null when max_attempts reached and no grant exists.
        $submission = $this->resolveSubmission($exam, $data, $access);

        if (! $submission) {
            return back()->withErrors([
                'access_code' => 'You have reached the maximum number of attempts for this examination.',
            ])->withInput();
        }

        $heartbeat = $this->monitoringService->initializeSession($submission, [
            'ip'         => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // ── Single-session enforcement ────────────────────────────────────────
        // Generate a fresh device token and write it to the submission row.
        // Any existing PHP session (e.g. device 1) still holds the old token.
        // HeartbeatController will detect the mismatch on device 1's next poll
        // and return status:'session_superseded', which kicks that device out.
        $deviceToken = Str::random(64);
        $submission->update(['device_token' => $deviceToken]);

        // Session security hardening
        session()->regenerate();
        session([
            'exam_submission_id'    => $submission->id,
            'exam_participant_data' => [
                'name'  => $data['name'] ?? null,
                'email' => $data['email'] ?? null,
            ],
            'exam_heartbeat_token'    => $heartbeat->session_token,
            'exam_device_token'       => $deviceToken,   // ← enforces single-session
            'exam_ip_address'         => $request->ip(),
            'exam_user_agent_hash'    => hash('sha256', $request->userAgent()),
            'exam_authenticated_at'   => now()->toIso8601String(),
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

        $exam->load([
            'sections'       => fn ($q) => $q->orderBy('order')->withCount(['questions as questions_count' => fn ($q) => $q->where('excluded_from_grading', false)]),
            'academicSubject' => fn ($q) => $q->with('academicLevel'),
        ]);

        $participantData      = session('exam_participant_data', []);
        $configuredParticipant = $this->resolveConfiguredParticipant($submission);

        return view('examination-hub.take.preview', [
            'exam'              => $exam,
            'submission'        => $submission,
            'candidateName'     => $configuredParticipant?->name
                                   ?? $submission->participant_name
                                   ?? $participantData['name']
                                   ?? 'Anonymous',
            'candidateEmail'    => $configuredParticipant?->email
                                   ?? $submission->participant_email
                                   ?? $participantData['email']
                                   ?? null,
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
            // Do NOT set started_at here — the timer must only begin when the
            // participant explicitly clicks "Begin Section". Setting it here
            // would start the admin monitoring clock before the exam begins.
            $submission->update(['status' => GeneralExamSubmission::STATUS_IN_PROGRESS]);
        }

        // Show waiting/countdown view if the exam window hasn't opened yet
        if ($exam->starts_at && $exam->starts_at->isFuture()) {
            $exam->load(['sections' => fn ($q) => $q->orderBy('order')->withCount(['questions as questions_count' => fn ($q) => $q->where('excluded_from_grading', false)])]);

            return view('examination-hub.take.start', [
                'exam'              => $exam,
                'submission'        => $submission,
                'proctoringEnabled' => (bool) $exam->proctoring_enabled,
            ]);
        }

        // Resume from the last saved position when the candidate returns mid-exam
        $lastPos = $submission->last_position ?? null;
        if ($submission->isInProgress() && is_array($lastPos) && isset($lastPos['section'])) {
            return redirect()->route('examination-hub.take.section', [$exam, (int) $lastPos['section']])
                ->with('restored_question', (int) ($lastPos['question'] ?? 0));
        }

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

        $exam->load(['sections.questions' => fn ($q) => $q->orderBy('order')->where('excluded_from_grading', false)]);

        $section = $exam->sections->get($sectionIndex);

        if (! $section) {
            abort(404, 'Section not found.');
        }

        // ── Ensure exam has started ────────────────────────────────────────────
        // The start time is now managed in the Livewire component when the user
        // explicitly begins the section, so no longer setting started_at here
        // if (! $submission->started_at) {
        //     $submission->update([
        //         'started_at' => now(),
        //         'status'     => GeneralExamSubmission::STATUS_IN_PROGRESS,
        //     ]);
        //     $submission->refresh();
        // }

        $questions       = $this->resolveQuestionOrder($exam, $section, $submission);
        $isSingleSection = $exam->sections->count() === 1;

        // ── Time remaining calculation ────────────────────────────────────────
        //
        // ALWAYS use exam-level duration (getRemainingTime), never section times.
        // This ensures:
        //   1. Timer never shows more time remaining than exam's total duration
        //   2. Timer correctly decreases as elapsed time increases
        //   3. Extensions granted by admin are included automatically
        //   4. Consistent behavior across page reloads and rejoins
        //
        // DO NOT pass timeRemaining to the view initially. The timer component is
        // part of the Livewire component which is rendered even while the preview
        // panel is showing. If we pass timeRemaining here, the timer will initialize
        // and start counting down before the user clicks "Begin Section".
        //
        // Instead, pass null and let the timer wait for the first sync event (via
        // the heartbeat), which will initialize it with the correct remaining time.
        // This prevents the timer from counting down during the preview phase.

        $timeRemaining = null;

//        if ($exam->duration_in_minutes) {
//            // At this point, started_at is guaranteed to be set (see above)
//            // So getRemainingTime() will always return the correct remaining time
//            $timeRemaining = $submission->getRemainingTime();
//        }

        return view('examination-hub.take.section', [
            'exam'                => $exam,
            'submission'          => $submission,
            'section'             => $section,
            'sectionIndex'        => $sectionIndex,
            'questions'           => $questions,
            'responses'           => $submission->responses ?? [],
            'proctoringEnabled'   => (bool) $exam->proctoring_enabled,
            'proctoringSessionId' => session('exam_heartbeat_token'),
            'sectionTitle'        => $section->title,
            'sectionTimeLimit'    => $section->time_limit_minutes
                                     ? $section->time_limit_minutes * 60
                                     : null,
            'timeRemaining'       => $timeRemaining,
            'totalMarks'          => $section->total_marks,
            'isSingleSection'     => $isSingleSection,
            // Expose extra time so the frontend timer can show a "+N min" badge
            'extraTimeMinutes'    => $submission->extra_time_minutes ?? 0,
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
            'question_id'   => ['required', 'integer', 'exists:general_exam_questions,id'],
            'response'      => ['required', 'string'],
            'section_index' => ['required', 'integer'],
        ]);

        $exam->load(['sections' => fn ($q) => $q->orderBy('order')->with('questions')]);
        $section         = $exam->sections->get($data['section_index']);
        $isSingleSection = $exam->sections->count() === 1;

        // ── Server-side time enforcement (respects extra_time_minutes) ────────
        if ($isSingleSection && $exam->duration_in_minutes && $submission->started_at) {
            // getRemainingTime() subtracts elapsed from (base duration + extra time)
            $remaining = $submission->getRemainingTime();

            if ($remaining !== null && $remaining <= 0) {
                return $this->autoSubmitExpired(
                    $submission,
                    $exam,
                    $request,
                    'Time limit exceeded (server-side auto-submit)'
                );
            }
        } elseif ($section && $section->time_limit_minutes) {
            $sectionStartTimes = $submission->section_start_times ?? [];
            $sectionKey        = (string) $section->id;
            $startedAt         = $sectionStartTimes[$sectionKey] ?? null;

            if ($startedAt) {
                $totalAllowed   = $submission->getTotalAllowedSeconds();
                $examEndsAt     = $submission->started_at
                    ? $submission->started_at->timestamp + $totalAllowed
                    : null;
                $sectionEndTime = $startedAt + ($section->time_limit_minutes * 60);
                if ($examEndsAt !== null) {
                    $sectionEndTime = min($sectionEndTime, $examEndsAt);
                }

                if (now()->timestamp >= $sectionEndTime) {
                    return $this->autoSubmitExpired(
                        $submission,
                        $exam,
                        $request,
                        "Section '{$section->title}' time limit exceeded (server-side auto-submit)"
                    );
                }
            }
        }

        $responses = $submission->responses ?? [];
        $responses[$data['question_id']] = [
            'response'    => $data['response'],
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

        $hasResponses = ! empty($submission->responses)
                        && is_array($submission->responses)
                        && count($submission->responses) > 0;

        DB::transaction(function () use ($submission, $hasResponses) {
            $timeTaken = $submission->started_at
                ? (int) $submission->started_at->diffInMinutes(now())
                : 0;

            $updateData = [
                'submitted_at'       => now(),
                'time_taken_minutes' => $timeTaken,
                'status'             => GeneralExamSubmission::STATUS_SUBMITTED,
            ];

            if (! $hasResponses) {
                $updateData['requires_manual_review'] = true;
                $updateData['teacher_feedback']       = 'No responses were recorded for this submission. May require manual review.';
            }

            $submission->update($updateData);

            ExamParticipantHeartbeat::where('general_exam_submission_id', $submission->id)
                ->first()
                ?->markCompleted();
        });

        // Flush any client-side pending responses without blocking the submission
        try {
            $this->savePendingResponses($request, $exam, $submission);
        } catch (\Exception $e) {
            Log::error('Failed to save pending responses before submission: ' . $e->getMessage());
        }

        $this->gradingService->dispatchGrading($submission);

        // Generate a secure token for result access and e-mail the link
        if ($submission->participant_email) {
            $token = Str::random(64);
            Cache::put("result_access:{$token}", [
                'submission_id' => $submission->id,
                'email'         => $submission->participant_email,
                'expires_at'    => now()->addDays(7),
            ], now()->addDays(7));

            try {
                $submission->notify(new ResultAccessNotification($submission, $token));
            } catch (\Exception $e) {
                Log::error('Failed to send result access email: ' . $e->getMessage(), [
                    'submission_id' => $submission->id,
                    'email'         => $submission->participant_email,
                ]);
            }
        }

        // Persist a lightweight completion record before clearing the main exam
        // session keys so completed() can retrieve the submission after forget().
        session([
            'exam_completed_submission_id'    => $submission->id,
            'exam_completed_participant_email' => $submission->participant_email,
        ]);

        session()->forget(['exam_submission_id', 'exam_participant_data', 'exam_heartbeat_token']);

        return redirect()->route('examination-hub.take.completed', $exam);
    }

    public function completed(GeneralExam $exam): View
    {
        // Two paths lead here; session state differs between them:
        //
        // a) Auto-submit (Livewire performAutoSubmit): exam_submission_id is still
        //    present so resolveSessionSubmission() returns the submission directly.
        // b) Manual submit (submit()): session is cleared before redirect, so we
        //    fall back to exam_completed_submission_id written by submit().
        $submission = $this->resolveSessionSubmission($exam);

        if (! $submission) {
            $id         = session('exam_completed_submission_id');
            $submission = $id ? GeneralExamSubmission::find($id) : null;
        }

        $participantEmail = session('exam_participant_data.email')
                         ?? session('exam_completed_participant_email')
                         ?? $submission?->participant_email;

        return view('examination-hub.take.completed', [
            'exam'             => $exam,
            'submission'       => $submission,
            'participantEmail' => $participantEmail,
        ]);
    }

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

        $exam->load(['sections.questions' => fn ($q) => $q->orderBy('order')->where('excluded_from_grading', false)]);

        $reviewData          = [];
        $totalQuestions      = 0;
        $answeredQuestions   = 0;

        foreach ($exam->sections as $sectionIndex => $section) {
            $sectionData = [
                'index'     => $sectionIndex,
                'title'     => $section->title,
                'questions' => [],
            ];

            foreach ($section->questions as $question) {
                $totalQuestions++;
                $isAnswered = isset($submission->responses[$question->id]);

                if ($isAnswered) {
                    $answeredQuestions++;
                }

                $sectionData['questions'][] = [
                    'id'            => $question->id,
                    'question_text' => $question->question_text,
                    'question_type' => $question->question_type,
                    'marks'         => $question->marks,
                    'response'      => $submission->responses[$question->id]['response'] ?? null,
                    'is_answered'   => $isAnswered,
                    'is_flagged'    => isset($submission->flagged_questions[$question->id]),
                ];
            }

            $reviewData[] = $sectionData;
        }

        return view('examination-hub.take.review', [
            'exam'                => $exam,
            'submission'          => $submission,
            'reviewData'          => $reviewData,
            'totalQuestions'      => $totalQuestions,
            'answeredQuestions'   => $answeredQuestions,
            'unansweredQuestions' => $totalQuestions - $answeredQuestions,
        ]);
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    /**
     * Resolve the submission for the current session.
     * Returns null when session is missing or belongs to a different exam.
     */
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

    /**
     * Determine which submission to use for the authenticating participant.
     *
     * Resolution order:
     *  1. Existing not-started submission    → reuse (covers auth-retry)
     *  2. Active readmission grant           → apply (continue or fresh start)
     *  3. canParticipantAttempt check        → null if max attempts reached
     *  4. firstOrCreate a new submission
     *
     * Returns null when the candidate has exhausted all attempts and no grant exists.
     */
    private function resolveSubmission(GeneralExam $exam, array $data, array $access): ?GeneralExamSubmission
    {
        // Determine participant identity from the access-service result
        if ($access['mode'] === 'configured' && isset($access['configured_participant'])) {
            $cp               = $access['configured_participant'];
            $type             = 'configured';
            $pid              = $cp->id;
            $participantName  = $cp->name;
            $participantEmail = $cp->email;
        } else {
            $type             = 'general';
            $pid              = ! empty($data['email']) ? abs(crc32($data['email'])) : null;
            $participantName  = $data['name'] ?? 'Anonymous';
            $participantEmail = $data['email'] ?? null;
        }

        // ── 1. Reuse an existing not-yet-started submission ──────────────────
        // This happens when the candidate successfully authenticated in a previous
        // request but never reached the preview page (e.g. network drop), so a
        // blank submission row already exists.
        $existingNotStarted = GeneralExamSubmission::where([
            'general_exam_id'  => $exam->id,
            'participant_type' => $type,
            'participant_id'   => $pid,
        ])
        ->whereNull('submitted_at')
        ->where('status', GeneralExamSubmission::STATUS_NOT_STARTED)
        ->first();

        if ($existingNotStarted) {
            return $existingNotStarted;
        }

        // ── 2. Check for an active readmission grant ─────────────────────────
        // An admin may have allowed this candidate to re-enter after they already
        // submitted (or were terminated).  The grant bypasses max_attempts.
        $grant = ExamReadmissionGrant::activeForParticipantOnExam($exam->id, $type, $pid);

        if ($grant) {
            return $this->applyReadmissionGrant(
                $grant,
                $type,
                $pid,
                $participantName,
                $participantEmail
            );
        }

        // ── 3. Enforce max attempts ──────────────────────────────────────────
        if (! $exam->canParticipantAttempt($type, $pid)) {
            return null;
        }

        // ── 4. Create (or find an in-progress) submission ────────────────────
        return GeneralExamSubmission::firstOrCreate(
            [
                'general_exam_id'  => $exam->id,
                'participant_type' => $type,
                'participant_id'   => $pid,
                'submitted_at'     => null,
            ],
            [
                'participant_name'  => $participantName,
                'participant_email' => $participantEmail,
                'started_at'        => null, // set when participant clicks "Begin Section"
                'responses'         => [],
                'score'             => 0,
                'status'            => GeneralExamSubmission::STATUS_NOT_STARTED,
            ]
        );
    }

    /**
     * Consume an active readmission grant and return the submission to use.
     *
     * continue → re-open the original submission so the candidate picks up where
     *            they left off. All previous responses are intact.
     * fresh    → create a brand-new submission; carry over any admin-granted
     *            extra time. The original submission is kept as a historical record.
     */
    private function applyReadmissionGrant(
        ExamReadmissionGrant $grant,
        string $participantType,
        int|null $participantId,
        string $participantName,
        ?string $participantEmail
    ): GeneralExamSubmission {
        $original = $grant->originalSubmission;

        if ($grant->mode === 'continue') {
            // Re-open the existing submission:
            //  • clears submitted_at so it is no longer treated as submitted
            //  • resets status to in_progress
            //  • preserves all responses and started_at
            //  • any admin-granted extra time already lives on the row
            $original->reopenForContinue();
            $grant->markUsed();

            Log::info('Readmission grant applied (continue)', [
                'grant_id'      => $grant->id,
                'submission_id' => $original->id,
            ]);

            return $original;
        }

        // ── Fresh start ──────────────────────────────────────────────────────
        $newSubmission = GeneralExamSubmission::create([
            'general_exam_id'  => $original->general_exam_id,
            'participant_type' => $participantType,
            'participant_id'   => $participantId,
            'participant_name'  => $participantName,
            'participant_email' => $participantEmail,
            // Increment attempt number so the audit trail is clear
            'attempt_number'   => ($original->attempt_number ?? 1) + 1,
            'status'           => GeneralExamSubmission::STATUS_NOT_STARTED,
            'responses'        => [],
            'score'            => 0,
            'started_at'       => now(),
            'ip_address'       => request()->ip(),
            'user_agent'       => request()->userAgent(),
        ]);

        // Carry over any extra time that the admin granted alongside the readmission
        if (($original->extra_time_minutes ?? 0) > 0) {
            $newSubmission->extendTime($original->extra_time_minutes, $grant->granted_by);
        }

        $grant->markUsed($newSubmission->id);

        Log::info('Readmission grant applied (fresh)', [
            'grant_id'          => $grant->id,
            'original_id'       => $original->id,
            'new_submission_id' => $newSubmission->id,
        ]);

        return $newSubmission;
    }

    /**
     * Auto-submit the exam because the time limit (base + extra) has been exceeded,
     * then dispatch grading and return the appropriate response type.
     */
    private function autoSubmitExpired(
        GeneralExamSubmission $submission,
        GeneralExam $exam,
        Request $request,
        string $reason
    ): JsonResponse|RedirectResponse {
        DB::transaction(function () use ($submission, $reason) {
            $timeTaken = $submission->started_at
                ? (int) $submission->started_at->diffInMinutes(now())
                : 0;

            $submission->update([
                'submitted_at'       => now(),
                'time_taken_minutes' => $timeTaken,
                'status'             => GeneralExamSubmission::STATUS_SUBMITTED,
                'auto_submitted'     => true,
                'auto_submit_reason' => $reason,
            ]);

            ExamParticipantHeartbeat::where('general_exam_submission_id', $submission->id)
                ->first()
                ?->markCompleted();
        });

        $this->gradingService->dispatchGrading($submission);

        return $request->expectsJson()
            ? response()->json(['status' => 'auto_submitted'])
            : redirect()->route('examination-hub.take.completed', $exam);
    }

    /**
     * Flush any pending responses the client sent as part of the final submit request.
     * Runs after the submission row is already written, so failures are non-fatal.
     */
    private function savePendingResponses(
        Request $request,
        GeneralExam $exam,
        GeneralExamSubmission $submission
    ): void {
        $data = $request->validate(['responses' => ['sometimes', 'array']]);

        if (empty($data['responses']) || ! is_array($data['responses'])) {
            return;
        }

        $exam->load(['sections' => fn ($q) => $q->orderBy('order')->with(['questions' => fn ($q) => $q->where('excluded_from_grading', false)])]);

        // Build a flat set of valid question IDs that belong to this exam
        $validIds = $exam->sections
            ->flatMap(fn ($s) => $s->questions->pluck('id'))
            ->flip()
            ->all(); // keyed by id for O(1) lookup

        $responses = $submission->responses ?? [];

        foreach ($data['responses'] as $questionId => $response) {
            if (! is_numeric($questionId) || ! isset($validIds[(int) $questionId])) {
                continue;
            }

            $responses[(int) $questionId] = [
                'response'    => (string) $response,
                'answered_at' => now()->toIso8601String(),
            ];
        }

        $submission->update(['responses' => $responses]);
    }

    /**
     * Resolve the GeneralExamConfiguredParticipant linked to a submission, if any.
     */
    private function resolveConfiguredParticipant(
        GeneralExamSubmission $submission
    ): ?GeneralExamConfiguredParticipant {
        if (! in_array($submission->participant_type, ['configured', GeneralExamConfiguredParticipant::class], true)) {
            return null;
        }

        return GeneralExamConfiguredParticipant::query()
            ->whereKey($submission->participant_id)
            ->where('general_exam_id', $submission->general_exam_id)
            ->first();
    }

    /**
     * Return the questions for a section in the correct display order.
     * If randomisation is enabled the order is seeded once and persisted on the
     * submission so it is stable across page reloads.
     */
    private function resolveQuestionOrder(
        GeneralExam $exam,
        $section,
        GeneralExamSubmission $submission
    ): \Illuminate\Support\Collection {
        $questions = $section->questions;

        if (! ($exam->is_randomized || $section->is_randomized) || $questions->isEmpty()) {
            return $questions;
        }

        $randomizedOrder = $submission->randomized_question_order ?? [];
        $sectionKey      = "section_{$section->id}";

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
