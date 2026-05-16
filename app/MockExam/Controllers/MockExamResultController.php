<?php

namespace App\MockExam\Controllers;

use App\Http\Controllers\Controller;
use App\MockExam\Models\MockExam;
use App\MockExam\Models\MockExamSubmission;
use App\MockExam\Services\MockExamGradingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MockExamResultController extends Controller
{
    public function __construct(
        private readonly MockExamGradingService $gradingService
    ) {}

    // ─── List all submissions ─────────────────────────────────────────────────

    public function index(MockExam $mockExam): View
    {
        $this->ensureOwner($mockExam);

        $submissions = $mockExam->submissions()
            ->orderByDesc('submitted_at')
            ->paginate(25);

        $stats = [
            'total'         => $mockExam->submissions()->count(),
            'submitted'     => $mockExam->submissions()->whereNotNull('submitted_at')->count(),
            'graded'        => $mockExam->submissions()->whereIn('status', ['auto_graded', 'manually_reviewed', 'final'])->count(),
            'needs_review'  => $mockExam->submissions()->where('requires_manual_review', true)->count(),
            'avg_percentage'=> round((float) $mockExam->submissions()->avg('percentage'), 2),
        ];

        return view('mock-exam.results.index', compact('mockExam', 'submissions', 'stats'));
    }

    // ─── Single submission detail / manual grading ────────────────────────────

    public function show(MockExam $mockExam, MockExamSubmission $submission): View
    {
        $this->ensureOwner($mockExam);
        abort_unless($submission->mock_exam_id === $mockExam->id, 404);

        $mockExam->load(['subjectExams.sections.questions']);

        // Build flat ordered list of questions for display
        $questions = $mockExam->subjectExams
            ->flatMap(fn ($se) => $se->sections)
            ->flatMap(fn ($s) => $s->questions)
            ->keyBy('id');

        return view('mock-exam.results.show', compact('mockExam', 'submission', 'questions'));
    }

    // ─── Manual grade a single question ──────────────────────────────────────

    public function grade(Request $request, MockExam $mockExam, MockExamSubmission $submission): RedirectResponse
    {
        $this->ensureOwner($mockExam);
        abort_unless($submission->mock_exam_id === $mockExam->id, 404);

        $data = $request->validate([
            'grades'             => ['required', 'array'],
            'grades.*.points'    => ['required', 'numeric', 'min:0'],
            'grades.*.feedback'  => ['nullable', 'string', 'max:1000'],
        ]);

        foreach ($data['grades'] as $questionId => $gradeData) {
            $submission->manualGradeQuestion(
                (int) $questionId,
                (float) $gradeData['points'],
                $gradeData['feedback'] ?? null
            );
        }

        return back()->with('success', 'Grades saved.');
    }

    // ─── Finalise grading ─────────────────────────────────────────────────────

    public function finalize(Request $request, MockExam $mockExam, MockExamSubmission $submission): RedirectResponse
    {
        $this->ensureOwner($mockExam);
        abort_unless($submission->mock_exam_id === $mockExam->id, 404);

        $data = $request->validate([
            'teacher_feedback' => ['nullable', 'string', 'max:2000'],
        ]);

        $grade = $this->gradingService->resolveGrade((float) $submission->percentage, $mockExam->user_id);

        $submission->finalizeGrading(
            (int) auth()->id(),
            $grade,
            $data['teacher_feedback'] ?? null
        );

        return back()->with('success', 'Submission finalised.');
    }

    // ─── Release results to all participants ──────────────────────────────────

    public function release(MockExam $mockExam): RedirectResponse
    {
        $this->ensureOwner($mockExam);

        abort_unless($mockExam->result_visibility === 'manual_release', 422, 'Only manual-release exams can be released this way.');

        $mockExam->releaseResults();

        return back()->with('success', 'Results released to all participants.');
    }

    private function ensureOwner(MockExam $exam): void
    {
        abort_unless($exam->user_id === auth()->id(), 403);
    }
}
