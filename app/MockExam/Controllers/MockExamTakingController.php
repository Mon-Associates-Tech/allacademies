<?php

namespace App\MockExam\Controllers;

use App\Http\Controllers\Controller;
use App\MockExam\Models\MockExam;
use App\MockExam\Models\MockExamParticipant;
use App\MockExam\Models\MockExamSubmission;
use App\MockExam\Services\MockExamGradingService;
use App\MockExam\Services\MockExamParticipantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MockExamTakingController extends Controller
{
    public function __construct(
        private readonly MockExamParticipantService $participantService,
        private readonly MockExamGradingService     $gradingService
    ) {}

    // ─── Join page ────────────────────────────────────────────────────────────

    public function join(): View
    {
        return view('mock-exam.take.join');
    }

    // ─── Authenticate ─────────────────────────────────────────────────────────

    public function authenticate(Request $request): RedirectResponse
    {
        try {
            $data = $request->validate([
                'access_code' => ['required', 'string'],
                'name'        => ['nullable', 'string', 'max:255'],
                'email'       => ['nullable', 'email', 'max:255'],
                'unique_code' => ['nullable', 'string', 'max:64'],
            ]);

            $exam = MockExam::findByAccessCode($data['access_code']);

            if (! $exam) {
                return back()->withErrors(['access_code' => 'Invalid access code.']);
            }

            if (! $exam->isActive()) {
                return back()->withErrors(['access_code' => 'This examination is not currently active.']);
            }

            if ($exam->delivery_type === 'print') {
                return back()->withErrors(['access_code' => 'This examination is a printed exam and cannot be taken online.']);
            }

            // Validate required fields
            $requiredFields = $exam->participant_required_fields ?? ['name', 'email'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    return back()->withErrors([$field => "The {$field} field is required."])->withInput();
                }
            }

            // Authorise against configured list (if applicable)
            $authResult = $this->participantService->authorizeJoin(
                $exam,
                $data['name'] ?? '',
                $data['email'] ?? '',
                $data['unique_code'] ?? null
            );

            if (! $authResult['allowed']) {
                return back()->withErrors(['access_code' => $authResult['message'] ?? 'Access denied.']);
            }

            // Resolve participant identity
            [$participantType, $participantId, $participantName, $participantEmail] =
                $this->resolveParticipant($exam, $authResult, $data);

            if (! $exam->canParticipantAttempt($participantType, $participantId)) {
                return back()->withErrors(['access_code' => 'You have reached the maximum number of attempts for this exam.']);
            }

            $submission = $this->findOrCreateSubmission($exam, $participantType, $participantId, $participantName, $participantEmail);

            \Log::info('MockExam authenticate - submission created', [
                'submission_id' => $submission->id,
                'exam_id' => $exam->id,
                'participant_type' => $participantType,
                'participant_id' => $participantId,
            ]);

            // Store session with explicit put
            $request->session()->put('mock_exam_submission_id', $submission->id);
            $request->session()->put('mock_exam_participant_email', $participantEmail);
            $request->session()->put('mock_exam_participant_name', $participantName);
            $request->session()->put('mock_exam_id', $exam->id);
            
            \Log::info('MockExam authenticate - session stored', [
                'session_id' => $request->session()->getId(),
                'submission_id_in_session' => $request->session()->get('mock_exam_submission_id'),
            ]);

            // Pass submission ID in URL as backup
            $url = route('mock-exams.take.start', ['mockExam' => $exam->id]) . '?sid=' . $submission->id;
            \Log::info('MockExam authenticate - redirecting to', ['url' => $url]);
            return redirect($url);
        } catch (\Exception $e) {
            \Log::error('MockExam authenticate - exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    // ─── Start page ───────────────────────────────────────────────────────────

    public function start(Request $request, MockExam $exam): View|RedirectResponse
    {
        \Log::info('MockExam start - called', [
            'exam_id' => $exam->id,
            'session_id' => $request->session()->getId(),
            'sid_param' => $request->query('sid'),
            'session_submission_id' => session('mock_exam_submission_id'),
        ]);
        
        // Try to get submission ID from URL parameter first, then session
        $submissionId = $request->query('sid') ?? session('mock_exam_submission_id');
        
        \Log::info('MockExam start - resolved submission ID', [
            'submission_id' => $submissionId,
        ]);
        
        if (! $submissionId) {
            \Log::warning('MockExam start - no submission ID found');
            return redirect()->route('mock-exams.take.join')
                ->withErrors(['error' => 'Session expired. Please join again.']);
        }
        
        $submission = MockExamSubmission::find($submissionId);
        
        \Log::info('MockExam start - submission lookup', [
            'found' => $submission ? 'yes' : 'no',
            'exam_match' => $submission ? ($submission->mock_exam_id === $exam->id ? 'yes' : 'no') : 'n/a',
        ]);

        if (! $submission || $submission->mock_exam_id !== $exam->id) {
            \Log::warning('MockExam start - invalid submission', [
                'submission_id' => $submissionId,
                'submission_exam_id' => $submission?->mock_exam_id,
                'expected_exam_id' => $exam->id,
            ]);
            return redirect()->route('mock-exams.take.join')
                ->withErrors(['error' => 'Invalid session. Please join again.']);
        }
        
        // Store in session for subsequent requests
        $request->session()->put('mock_exam_submission_id', $submission->id);

        if ($submission->submitted_at) {
            return redirect()->route('mock-exams.take.completed', $exam);
        }

        if (! $submission->started_at) {
            $submission->start();
        }

        $exam->load(['subjectExams.academicSubject', 'subjectExams.sections']);

        $allSections   = $exam->subjectExams->flatMap(fn ($se) => $se->sections)->values();
        $totalSections = $allSections->count();

        return view('mock-exam.take.start', compact('exam', 'submission', 'totalSections'));
    }

    // ─── Section ─────────────────────────────────────────────────────────────

    public function section(MockExam $exam, int $sectionIndex): View|RedirectResponse
    {
        $submission = $this->resolveSession($exam);

        if (! $submission) {
            return redirect()->route('mock-exams.take.join')
                ->withErrors(['error' => 'Session expired. Please join again.']);
        }

        if ($submission->submitted_at) {
            return redirect()->route('mock-exams.take.completed', $exam);
        }

        $exam->load(['subjectExams.sections.questions' => fn ($q) => $q->orderBy('order')]);

        $allSections = $exam->subjectExams->flatMap(fn ($se) => $se->sections)->values();
        $section     = $allSections->get($sectionIndex);

        abort_unless($section, 404, 'Section not found.');

        $totalSections = $allSections->count();

        // Handle per-section randomisation
        $randomOrder = $submission->randomized_question_order ?? [];
        $sectionKey  = "section_{$section->id}";

        if ($section->is_randomized && ! isset($randomOrder[$sectionKey])) {
            $questionIds             = $section->questions->pluck('id')->shuffle()->values()->toArray();
            $randomOrder[$sectionKey] = $questionIds;
            $submission->update(['randomized_question_order' => $randomOrder]);
        }

        $questions = $section->getQuestionsForParticipant($randomOrder[$sectionKey] ?? null);
        $responses = $submission->responses ?? [];

        return view('mock-exam.take.section', compact(
            'exam', 'submission', 'section', 'sectionIndex',
            'totalSections', 'questions', 'responses'
        ));
    }

    // ─── Save response ────────────────────────────────────────────────────────

    public function saveResponse(Request $request, MockExam $exam): JsonResponse|RedirectResponse
    {
        $submission = $this->resolveSession($exam);

        if (! $submission || $submission->submitted_at) {
            return $request->expectsJson()
                ? response()->json(['error' => 'Invalid session.'], 403)
                : abort(403);
        }

        $data = $request->validate([
            'question_id'   => ['required', 'integer'],
            'response'      => ['required', 'string'],
            'section_index' => ['nullable', 'integer'],
        ]);

        $submission->saveResponse($data['question_id'], $data['response']);

        return $request->expectsJson()
            ? response()->json(['status' => 'saved', 'question_id' => $data['question_id']])
            : back()->with('success', 'Response saved.');
    }

    // ─── Submit ───────────────────────────────────────────────────────────────

    public function submit(Request $request, MockExam $exam): RedirectResponse
    {
        $submission = $this->resolveSession($exam);

        if (! $submission) {
            abort(403, 'Invalid session.');
        }

        if ($submission->submitted_at) {
            return redirect()->route('mock-exams.take.completed', $exam);
        }

        DB::transaction(function () use ($submission, $exam) {
            $submission->submit();
            $grade = $this->gradingService->resolveGrade(0, $exam->user_id); // placeholder, recalculated in autoGrade
            $submission->autoGrade(
                $this->gradingService->resolveGrade(0, $exam->user_id)
            );

            // Recalculate grade now that we have a real percentage
            $submission->refresh();
            $grade = $this->gradingService->resolveGrade((float) $submission->percentage, $exam->user_id);
            $submission->update(['grade' => $grade]);
        });

        // Store last submission ID for completed page
        session(['mock_exam_last_submission_id' => $submission->id]);
        session()->forget(['mock_exam_submission_id', 'mock_exam_participant_email', 'mock_exam_participant_name']);

        return redirect()->route('mock-exams.take.completed', $exam);
    }

    // ─── Completed ────────────────────────────────────────────────────────────

    public function completed(MockExam $exam): View
    {
        // Try to display results if allowed (participant may view via result token too)
        $submission = null;
        $submissionId = session('mock_exam_last_submission_id');
        if ($submissionId) {
            $submission = MockExamSubmission::find($submissionId);
        }

        return view('mock-exam.take.completed', compact('exam', 'submission'));
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    private function resolveSession(MockExam $exam): ?MockExamSubmission
    {
        $id = session('mock_exam_submission_id');

        if (! $id) {
            return null;
        }

        $submission = MockExamSubmission::find($id);

        if (! $submission || $submission->mock_exam_id !== $exam->id) {
            return null;
        }

        return $submission;
    }

    private function resolveParticipant(MockExam $exam, array $authResult, array $data): array
    {
        $name  = trim($data['name'] ?? '');
        $email = strtolower(trim($data['email'] ?? ''));

        if ($authResult['mode'] === 'configured') {
            $configured = $authResult['participant'];
            return [
                'configured',
                $configured->id,
                $name ?: $configured->name,
                $email ?: $configured->email,
            ];
        }

        // General participant – create or reuse a MockExamParticipant record
        $needsVerification = $exam->email_verification_required;
        $participant       = $this->participantService->createOrReuseParticipant($name, $email, ! $needsVerification);

        return ['general', $participant->id, $participant->name, $participant->email];
    }

    private function findOrCreateSubmission(
        MockExam $exam,
        string $type,
        int $participantId,
        string $name,
        string $email
    ): MockExamSubmission {
        // Find existing in-progress submission
        $existing = MockExamSubmission::where('mock_exam_id', $exam->id)
            ->where('participant_type', $type)
            ->where('participant_id', $participantId)
            ->whereNull('submitted_at')
            ->first();

        if ($existing) {
            return $existing;
        }

        // Create new submission
        return MockExamSubmission::create([
            'mock_exam_id'      => $exam->id,
            'participant_type'  => $type,
            'participant_id'    => $participantId,
            'participant_name'  => $name,
            'participant_email' => $email,
            'responses'         => [],
            'status'            => MockExamSubmission::STATUS_NOT_STARTED,
            'attempt_number'    => $this->getNextAttemptNumber($exam, $type, $participantId),
        ]);
    }

    private function getNextAttemptNumber(MockExam $exam, string $type, int $participantId): int
    {
        $lastAttempt = MockExamSubmission::where('mock_exam_id', $exam->id)
            ->where('participant_type', $type)
            ->where('participant_id', $participantId)
            ->whereNotNull('submitted_at')
            ->max('attempt_number');

        return ($lastAttempt ?? 0) + 1;
    }
}
