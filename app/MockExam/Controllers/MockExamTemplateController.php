<?php

namespace App\MockExam\Controllers;

use App\Http\Controllers\Controller;
use App\MockExam\Models\MockExam;
use App\MockExam\Models\MockExamTemplate;
use App\MockExam\Services\MockExamCreationService;
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

    // ─── Step 1 – Front Page Builder ──────────────────────────────────────────

    /**
     * Show the Livewire FrontPageBuilder for a NEW template (Step 1, create flow).
     */
    public function create(): View
    {
        \Log::info('MockExamTemplateController.create method called');
        
        try {
            $hierarchyTree = MockExamController::hierarchyTree();
            \Log::info('Hierarchy tree loaded successfully', ['count' => count($hierarchyTree)]);
        } catch (\Exception $e) {
            \Log::error('Error loading hierarchy tree for mock exam template creation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $hierarchyTree = [];
        }
        
        \Log::info('About to return view for mock exam template creation');
        
        try {
            $result = view('mock-exam.templates.create', [
                'template' => null,
                'hierarchyTree' => $hierarchyTree,
            ]);
            \Log::info('View created successfully, about to return');
            return $result;
        } catch (\Exception $e) {
            \Log::error('Error creating view for mock exam template creation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Show the Livewire FrontPageBuilder for an EXISTING template (Step 1, edit flow).
     * The component is pre-seeded with the template's current front_page_config.
     */
    public function editFrontPage(MockExamTemplate $template): View
    {
        $this->ensureOwner($template);

        return view('mock-exam.templates.front-page-builder', [
            'template' => $template,
            'hierarchyTree' => MockExamController::hierarchyTree(),
        ]);
    }

    // ─── Step 2 – Template Details Form ───────────────────────────────────────

    /**
     * Show Step 2 for a NEW template.
     * Redirects back to Step 1 if front_page_config is not yet in the session
     * (prevents direct URL access without completing Step 1).
     */
    public function configureCreate(): View|RedirectResponse
    {
        if (! session()->has('template_front_page_config')) {
            return redirect()
                ->route('mock-exams.templates.create')
                ->with('info', 'Please complete the front page first.');
        }

        return view('mock-exam.templates.configure', [
            'hierarchyTree' => MockExamController::hierarchyTree(),
            'template' => null,
            'frontPageConfigJson' => session('template_front_page_config', '{"blocks":[]}'),
        ]);
    }

    /**
     * Show Step 2 for an EXISTING template.
     * If the user just came from editFrontPage() the session will have fresh
     * front_page_config; otherwise we fall back to the template's stored value.
     */
    public function configureEdit(MockExamTemplate $template): View
    {
        $this->ensureOwner($template);

        $frontPageConfigJson = session(
            'template_front_page_config',
            json_encode($template->front_page_config ?? ['blocks' => []])
        );

        return view('mock-exam.templates.configure', [
            'hierarchyTree' => MockExamController::hierarchyTree(),
            'template' => $template,
            'frontPageConfigJson' => $frontPageConfigJson,
        ]);
    }

    // ─── Store / Update ────────────────────────────────────────────────────────

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
            'front_page_config'        => $this->decodeFrontPageConfig($payload['front_page_config'] ?? null),
        ]);

        // Session data has been consumed — clear it.
        session()->forget('template_front_page_config');

        return redirect()
            ->route('mock-exams.templates.index')
            ->with('success', 'Template created successfully.');
    }

    public function update(Request $request, MockExamTemplate $template): RedirectResponse
    {
        $this->ensureOwner($template);

        $payload = $this->validateTemplatePayload($request);

        $template->update([
            'academic_group_id' => $payload['academic_group_id'] ?? null,
            'academic_level_id' => $payload['academic_level_id'] ?? null,
            'academic_subject_id' => $payload['academic_subject_id'],
            'name' => $payload['name'],
            'description' => $payload['description'] ?? null,
            'is_active' => (bool) ($payload['is_active'] ?? true),
            'default_duration_minutes' => $payload['default_duration_minutes'] ?? null,
            'topic_ids' => $payload['topic_ids'] ?? [],
            'subtopic_ids' => $payload['subtopic_ids'] ?? [],
            'sections_config' => $payload['sections_config'],
            'front_page_config' => $this->decodeFrontPageConfig($payload['front_page_config'] ?? null),
        ]);

        session()->forget('template_front_page_config');

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

    // ─── Index ─────────────────────────────────────────────────────────────────

    public function index(): View
    {
        $templates = MockExamTemplate::where('user_id', auth()->id())
            ->with(['academicSubject', 'academicLevel', 'academicGroup'])
            ->latest()
            ->paginate(20);

        return view('mock-exam.templates.index', compact('templates'));
    }

    // ─── Quick Generate Exam from Template ────────────────────────────────────

    public function quickGenerate(Request $request, MockExam $mockExam): RedirectResponse
    {
        $this->ensureOwner($mockExam);

        $validated = $request->validate([
            'template_id' => ['required', 'integer', 'exists:mock_exam_templates,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'instructions' => ['nullable', 'string'],
        ]);

        $template = MockExamTemplate::findOrFail($validated['template_id']);
        $this->ensureOwner($template);

        $overrides = array_filter([
            'title' => $validated['title'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
        ]);

        $result = $this->creationService->createSubjectExamFromTemplate(
            $mockExam,
            $template,
            $overrides
        );

        $message = "Subject exam generated from template — {$result['questions_created']} question(s) loaded.";

        if (! empty($result['warnings'])) {
            $message .= ' Note: '.implode(' ', $result['warnings']);
        }

        return redirect()
            ->route('mock-exams.show', $mockExam)
            ->with('success', $message);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Decode the JSON string coming from the hidden <input> in configure.blade.php.
     * Returns null when the value is absent or malformed so the column stays nullable.
     */
    private function decodeFrontPageConfig(?string $json): ?array
    {
        if (! $json) {
            return null;
        }

        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : null;
    }

    private function validateTemplatePayload(Request $request): array
    {
        $data = $request->validate([
            'academic_group_id' => ['nullable', 'integer', 'exists:academic_groups,id'],
            'academic_level_id' => ['nullable', 'integer', 'exists:academic_levels,id'],
            'academic_subject_id' => ['required', 'integer', 'exists:academic_subjects,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'default_duration_minutes' => ['nullable', 'integer', 'min:1', 'max:600'],
            'topic_ids' => ['nullable', 'array'],
            'topic_ids.*' => ['integer', 'exists:academic_topics,id'],
            'subtopic_ids' => ['nullable', 'array'],
            'subtopic_ids.*' => ['integer', 'exists:academic_subtopics,id'],
            'sections_config' => ['required', 'array', 'min:1'],
            'sections_config.*.title' => ['required', 'string', 'max:255'],
            'sections_config.*.instructions' => ['nullable', 'string'],
            'sections_config.*.question_type' => ['required', 'in:multiple_choice,true_false,essay,mixed'],
            'sections_config.*.question_count' => ['required', 'integer', 'min:1', 'max:200'],
            'sections_config.*.marks_per_question' => ['nullable', 'numeric', 'min:0.5', 'max:100'],
            'sections_config.*.is_randomized' => ['nullable', 'boolean'],
            // Passed as a JSON string from the hidden input in configure.blade.php
            'front_page_config' => ['nullable', 'string'],
        ]);

        $data['topic_ids'] = $data['topic_ids'] ?? [];
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
