<?php

namespace App\MockExam\Controllers;

use App\Http\Controllers\Controller;
use App\MockExam\Models\MockExam;
use App\MockExam\Models\MockExamTemplate;
use App\MockExam\Services\MockExamCreationService;
use App\Models\AcademicGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MockExamTemplateController extends Controller
{
    public function __construct(
        private readonly MockExamCreationService $creationService
    ) {
        $this->middleware(fn ($request, $next) => $this->ensureInstructor($next));
    }

    // ─── Index - List all templates ──────────────────────────────────────────

    public function index(): View
    {
        $templates = MockExamTemplate::where('user_id', auth()->id())
            ->with(['academicSubject', 'academicLevel', 'academicGroup'])
            ->latest()
            ->paginate(20);

        return view('mock-exam.templates.index', compact('templates'));
    }

    // ─── Create / Store ───────────────────────────────────────────────────────

    public function create(): View
    {
        return view('mock-exam.templates.create', [
            'hierarchyTree' => MockExamController::hierarchyTree(),
            'template'      => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $this->validateTemplatePayload($request);

        MockExamTemplate::create([
            'user_id'                  => auth()->id(),
            'academic_group_id'        => $payload['academic_group_id'] ?? null,
            'academic_level_id'        => $payload['academic_level_id'] ?? null,
            'academic_subject_id'      => $payload['academic_subject_id'],
            'name'                     => $payload['name'],
            'description'              => $payload['description'] ?? null,
            'is_active'                => (bool) ($payload['is_active'] ?? true),
            'default_duration_minutes' => $payload['default_duration_minutes'] ?? null,
            'topic_ids'                => $payload['topic_ids'] ?? [],
            'subtopic_ids'             => $payload['subtopic_ids'] ?? [],
            'sections_config'          => $payload['sections_config'],
        ]);

        return redirect()
            ->route('mock-exams.templates.index')
            ->with('success', 'Template created successfully.');
    }

    // ─── Edit / Update ────────────────────────────────────────────────────────

    public function edit(MockExamTemplate $template): View
    {
        $this->ensureOwner($template);

        return view('mock-exam.templates.create', [
            'hierarchyTree' => MockExamController::hierarchyTree(),
            'template'      => $template,
        ]);
    }

    public function update(Request $request, MockExamTemplate $template): RedirectResponse
    {
        $this->ensureOwner($template);

        $payload = $this->validateTemplatePayload($request);

        $template->update([
            'academic_group_id'        => $payload['academic_group_id'] ?? null,
            'academic_level_id'        => $payload['academic_level_id'] ?? null,
            'academic_subject_id'      => $payload['academic_subject_id'],
            'name'                     => $payload['name'],
            'description'              => $payload['description'] ?? null,
            'is_active'                => (bool) ($payload['is_active'] ?? true),
            'default_duration_minutes' => $payload['default_duration_minutes'] ?? null,
            'topic_ids'                => $payload['topic_ids'] ?? [],
            'subtopic_ids'             => $payload['subtopic_ids'] ?? [],
            'sections_config'          => $payload['sections_config'],
        ]);

        return redirect()
            ->route('mock-exams.templates.index')
            ->with('success', 'Template updated successfully.');
    }

    // ─── Destroy ──────────────────────────────────────────────────────────────

    public function destroy(MockExamTemplate $template): RedirectResponse
    {
        $this->ensureOwner($template);
        $template->delete();

        return redirect()
            ->route('mock-exams.templates.index')
            ->with('success', 'Template deleted.');
    }

    // ─── Quick Generate Exam from Template ────────────────────────────────────

    /**
     * Generate a subject exam from template for a specific mock exam.
     * This is the "quick generate" endpoint that bypasses manual configuration.
     */
    public function quickGenerate(Request $request, MockExam $mockExam): RedirectResponse
    {
        $this->ensureOwner($mockExam);

        $validated = $request->validate([
            'template_id' => ['required', 'integer', 'exists:mock_exam_templates,id'],
            'title'       => ['nullable', 'string', 'max:255'],
            'instructions'=> ['nullable', 'string'],
        ]);

        $template = MockExamTemplate::findOrFail($validated['template_id']);
        $this->ensureOwner($template);

        // Check if template's subject matches what we need
        // (We allow any subject since admin might want to use template across subjects)

        $overrides = [];
        if (!empty($validated['title'])) {
            $overrides['title'] = $validated['title'];
        }
        if (!empty($validated['instructions'])) {
            $overrides['instructions'] = $validated['instructions'];
        }

        $result = $this->creationService->createSubjectExamFromTemplate(
            $mockExam,
            $template,
            $overrides
        );

        $message = "Subject exam generated from template — {$result['questions_created']} question(s) loaded.";

        if (!empty($result['warnings'])) {
            $message .= ' Note: ' . implode(' ', $result['warnings']);
        }

        return redirect()
            ->route('mock-exams.show', $mockExam)
            ->with('success', $message);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function validateTemplatePayload(Request $request): array
    {
        $data = $request->validate([
            'academic_group_id'        => ['nullable', 'integer', 'exists:academic_groups,id'],
            'academic_level_id'        => ['nullable', 'integer', 'exists:academic_levels,id'],
            'academic_subject_id'      => ['required', 'integer', 'exists:academic_subjects,id'],
            'name'                     => ['required', 'string', 'max:255'],
            'description'              => ['nullable', 'string'],
            'is_active'                => ['nullable', 'boolean'],
            'default_duration_minutes' => ['nullable', 'integer', 'min:1', 'max:600'],
            'topic_ids'                => ['nullable', 'array'],
            'topic_ids.*'              => ['integer', 'exists:academic_topics,id'],
            'subtopic_ids'             => ['nullable', 'array'],
            'subtopic_ids.*'           => ['integer', 'exists:academic_subtopics,id'],
            'sections_config'          => ['required', 'array', 'min:1'],
            'sections_config.*.title'              => ['required', 'string', 'max:255'],
            'sections_config.*.instructions'       => ['nullable', 'string'],
            'sections_config.*.question_type'      => ['required', 'in:multiple_choice,true_false,essay,mixed'],
            'sections_config.*.question_count'     => ['required', 'integer', 'min:1', 'max:200'],
            'sections_config.*.marks_per_question' => ['nullable', 'numeric', 'min:0.5', 'max:100'],
            'sections_config.*.is_randomized'      => ['nullable', 'boolean'],
        ]);

        $data['topic_ids']    = $data['topic_ids'] ?? [];
        $data['subtopic_ids'] = $data['subtopic_ids'] ?? [];

        return $data;
    }

    private function ensureOwner(MockExamTemplate|MockExam $resource): void
    {
        abort_unless($resource->user_id === auth()->id(), 403);
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
