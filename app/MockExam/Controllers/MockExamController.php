<?php

namespace App\MockExam\Controllers;

use App\Http\Controllers\Controller;
use App\MockExam\Models\MockExam;
use App\MockExam\Services\MockExamCreationService;
use App\Models\AcademicGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MockExamController extends Controller
{
    public function __construct(
        private readonly MockExamCreationService $creationService
    ) {
        $this->middleware(fn ($request, $next) => $this->ensureInstructor($next));
    }

    // ─── Index ────────────────────────────────────────────────────────────────

    public function index(): View
    {
        $exams = MockExam::where('user_id', auth()->id())
            ->withCount(['subjectExams', 'submissions'])
            ->latest()
            ->paginate(15);

        return view('mock-exam.index', compact('exams'));
    }

    // ─── Create / Store ───────────────────────────────────────────────────────

    public function create(): View
    {
        return view('mock-exam.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $this->validateExamPayload($request);

        $exam = $this->creationService->createExam((int) auth()->id(), $payload);

        return redirect()
            ->route('mock-exams.show', $exam)
            ->with('success', 'Mock exam created. Now add subject exams below.');
    }

    // ─── Show ─────────────────────────────────────────────────────────────────

    public function show(MockExam $mockExam): View
    {
        $this->ensureOwner($mockExam);

        $mockExam->load([
            'subjectExams.academicSubject',
            'subjectExams.academicLevel',
            'subjectExams.sections.questions',
            'configuredParticipants',
        ]);

        $submissions = $mockExam->submissions()
            ->latest('submitted_at')
            ->paginate(20);

        return view('mock-exam.show', compact('mockExam', 'submissions'));
    }

    // ─── Edit / Update ────────────────────────────────────────────────────────

    public function edit(MockExam $mockExam): View
    {
        $this->ensureOwner($mockExam);
        abort_if($mockExam->submissions()->exists(), 422, 'This exam already has submissions and cannot be edited.');

        return view('mock-exam.create', ['exam' => $mockExam]);
    }

    public function update(Request $request, MockExam $mockExam): RedirectResponse
    {
        $this->ensureOwner($mockExam);
        abort_if($mockExam->submissions()->exists(), 422, 'This exam already has submissions and cannot be edited.');

        $payload = $this->validateExamPayload($request);
        $this->creationService->updateExam($mockExam, $payload);

        return redirect()
            ->route('mock-exams.show', $mockExam)
            ->with('success', 'Mock exam updated.');
    }

    // ─── Destroy ──────────────────────────────────────────────────────────────

    public function destroy(MockExam $mockExam): RedirectResponse
    {
        $this->ensureOwner($mockExam);
        $mockExam->delete();

        return redirect()
            ->route('mock-exams.index')
            ->with('success', 'Mock exam deleted.');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /** Build the complete academic hierarchy tree for Alpine.js cascading dropdowns. */
    public static function hierarchyTree(): array
    {
        return MockExam::hierarchyTree();
        
    }

    private function validateExamPayload(Request $request): array
    {
        return $request->validate([
            'title'                         => ['required', 'string', 'max:255'],
            'description'                   => ['nullable', 'string'],
            'instructions'                  => ['nullable', 'string'],
            'status'                        => ['required', 'in:draft,published'],
            'delivery_type'                 => ['required', 'in:online,print'],
            'participant_mode'              => ['required_if:delivery_type,online', 'in:general,configured'],
            'configured_match_mode'         => ['nullable', 'in:any,both'],
            'participant_required_fields'   => ['nullable', 'array'],
            'participant_required_fields.*' => ['in:name,email,code'],
            'email_verification_required'   => ['nullable', 'boolean'],
            'result_visibility'             => ['required_if:delivery_type,online', 'in:immediate,after_due_date,manual_release,scheduled'],
            'results_release_datetime'      => ['nullable', 'date', 'required_if:result_visibility,scheduled'],
            'starts_at'                     => ['nullable', 'date'],
            'ends_at'                       => ['nullable', 'date', 'after:starts_at'],
            'is_randomized'                 => ['nullable', 'boolean'],
            'max_attempts'                  => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);
    }

    private function ensureOwner(MockExam $exam): void
    {
        abort_unless($exam->user_id === auth()->id(), 403);
    }

    private function ensureInstructor(\Closure $next): mixed
    {
        abort_unless(
            in_array(auth()->user()?->role->value, ['admin', 'owner', 'teacher'], true),
            403,
            'Only instructors can access this area.'
        );

        return $next(request());
    }
}
