<?php

namespace App\MockExam\Controllers;

use App\Http\Controllers\Controller;
use App\MockExam\Models\MockExam;
use App\MockExam\Services\MockExamCreationService;
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

    // ─── Index (Enhanced with Filters, Search, Sorting & View Toggle) ───────────
    public function index(Request $request): View
    {
        $filters = [
            'search'         => $request->string('search')->toString(),
            'status'         => $request->string('status')->toString(),
            'sort_by'        => $request->string('sort_by')->toString() ?: 'created_at',
            'sort_direction' => $request->string('sort_direction')->toString() ?: 'desc',
            'view'           => $request->string('view')->toString() ?: 'list', // 'list' or 'table'
        ];

        $query = MockExam::where('user_id', auth()->id())
            ->withCount(['subjectExams', 'submissions']);

        // Apply Search
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('access_code', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Apply Status Filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply Sorting securely
        $allowedSortFields = ['title', 'status', 'created_at', 'starts_at'];
        $sortField = in_array($filters['sort_by'], $allowedSortFields) ? $filters['sort_by'] : 'created_at';
        $sortDirection = in_array(strtolower($filters['sort_direction']), ['asc', 'desc']) ? $filters['sort_direction'] : 'desc';

        $query->orderBy($sortField, $sortDirection);

        $exams = $query->paginate(15)->withQueryString();

        return view('mock-exam.index', compact('exams', 'filters'));
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
