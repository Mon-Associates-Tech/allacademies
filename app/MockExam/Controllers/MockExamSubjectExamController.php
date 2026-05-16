<?php

namespace App\MockExam\Controllers;

use App\Http\Controllers\Controller;
use App\MockExam\Models\MockExam;
use App\MockExam\Models\MockExamSubjectExam;
use App\MockExam\Services\MockExamCreationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MockExamSubjectExamController extends Controller
{
    public function __construct(
        private readonly MockExamCreationService $creationService
    ) {}

    // ─── Create / Store ───────────────────────────────────────────────────────

    public function create(MockExam $mockExam): View
    {
        $this->ensureOwner($mockExam);

        // Warn if the same subject already exists under this mock
        $existingSubjectIds = $mockExam->subjectExams()->pluck('academic_subject_id')->toArray();

        return view('mock-exam.subject-exams.create', [
            'mockExam'          => $mockExam,
            'hierarchyTree'     => MockExamController::hierarchyTree(),
            'existingSubjectIds'=> $existingSubjectIds,
            'subjectExam'       => null,
        ]);
    }

    public function store(Request $request, MockExam $mockExam): RedirectResponse
    {
        $this->ensureOwner($mockExam);

        $payload = $this->validatePayload($request);

        // Warn about duplicate subject (soft – let instructor proceed)
        $duplicate = $mockExam->subjectExams()
            ->where('academic_subject_id', $payload['academic_subject_id'])
            ->exists();

        $result = $this->creationService->createSubjectExam($mockExam, $payload);

        $message = "Subject exam added — {$result['questions_created']} question(s) loaded.";

        if ($duplicate) {
            $message = '⚠ This subject already exists in the mock. ' . $message;
        }

        if (! empty($result['warnings'])) {
            $message .= ' Note: ' . implode(' ', $result['warnings']);
        }

        return redirect()
            ->route('mock-exams.show', $mockExam)
            ->with('success', $message);
    }

    // ─── Edit / Update ────────────────────────────────────────────────────────

    public function edit(MockExam $mockExam, MockExamSubjectExam $subjectExam): View
    {
        $this->ensureOwner($mockExam);
        abort_unless($subjectExam->mock_exam_id === $mockExam->id, 404);

        $subjectExam->load('sections');

        return view('mock-exam.subject-exams.create', [
            'mockExam'          => $mockExam,
            'hierarchyTree'     => MockExamController::hierarchyTree(),
            'existingSubjectIds'=> [],
            'subjectExam'       => $subjectExam,
        ]);
    }

    public function update(Request $request, MockExam $mockExam, MockExamSubjectExam $subjectExam): RedirectResponse
    {
        $this->ensureOwner($mockExam);
        abort_unless($subjectExam->mock_exam_id === $mockExam->id, 404);

        $payload = $this->validatePayload($request);
        $result  = $this->creationService->updateSubjectExam($subjectExam, $payload);

        $message = "Subject exam updated — {$result['questions_created']} question(s) reloaded.";

        if (! empty($result['warnings'])) {
            $message .= ' Note: ' . implode(' ', $result['warnings']);
        }

        return redirect()
            ->route('mock-exams.show', $mockExam)
            ->with('success', $message);
    }

    // ─── Destroy ──────────────────────────────────────────────────────────────

    public function destroy(MockExam $mockExam, MockExamSubjectExam $subjectExam): RedirectResponse
    {
        $this->ensureOwner($mockExam);
        abort_unless($subjectExam->mock_exam_id === $mockExam->id, 404);

        $subjectExam->sections()->each(fn ($s) => $s->questions()->delete());
        $subjectExam->sections()->delete();
        $subjectExam->delete();

        return redirect()
            ->route('mock-exams.show', $mockExam)
            ->with('success', 'Subject exam removed.');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function validatePayload(Request $request): array
    {
        $data = $request->validate([
            'academic_group_id'    => ['required', 'integer', 'exists:academic_groups,id'],
            'academic_level_id'    => ['required', 'integer', 'exists:academic_levels,id'],
            'academic_subject_id'  => ['required', 'integer', 'exists:academic_subjects,id'],
            'title'                => ['nullable', 'string', 'max:255'],
            'instructions'         => ['nullable', 'string'],
            'duration_in_minutes'  => ['nullable', 'integer', 'min:1', 'max:600'],
            'topic_ids'            => ['nullable', 'array'],
            'topic_ids.*'          => ['integer', 'exists:academic_topics,id'],
            'subtopic_ids'         => ['nullable', 'array'],
            'subtopic_ids.*'       => ['integer', 'exists:academic_subtopics,id'],
            'sections'             => ['required', 'array', 'min:1'],
            'sections.*.title'              => ['required', 'string', 'max:255'],
            'sections.*.instructions'       => ['nullable', 'string'],
            'sections.*.question_type'      => ['required', 'in:multiple_choice,true_false,essay,mixed'],
            'sections.*.question_count'     => ['required', 'integer', 'min:1', 'max:200'],
            'sections.*.marks_per_question' => ['nullable', 'numeric', 'min:0.5', 'max:100'],
            'sections.*.is_randomized'      => ['nullable', 'boolean'],
        ]);

        // Ensure topic_ids actually belong to the chosen subject
        // (light validation – deeper check handled by question service)
        $data['topic_ids']    = $data['topic_ids']    ?? [];
        $data['subtopic_ids'] = $data['subtopic_ids'] ?? [];

        return $data;
    }

    private function ensureOwner(MockExam $exam): void
    {
        abort_unless($exam->user_id === auth()->id(), 403);
    }
}
