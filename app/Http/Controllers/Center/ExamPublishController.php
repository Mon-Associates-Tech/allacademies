<?php

namespace App\Http\Controllers\Center;

use App\ExaminationHub\Models\ExamPackage;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Services\ExamPackageBuilder;
use App\ExaminationHub\Services\ExamPublishService;
use Illuminate\Http\Request;

/**
 * ExamPublishController — publish exams, create revisions, and release results.
 *
 * Only available on central instances (APP_ROLE=central).
 */
class ExamPublishController
{
    /**
     * List all exams with publish status.
     */
    public function index(Request $request)
    {
        $query = GeneralExam::withCount('packages')
            ->orderByDesc('published_at')
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'published':
                    $query->whereNotNull('published_at');
                    break;
                case 'draft':
                    $query->whereNull('published_at');
                    break;
            }
        }

        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $exams = $query->paginate(20);

        return view('center-sync.exams.index', compact('exams'));
    }

    /**
     * Show exam detail with package history.
     */
    public function show(GeneralExam $exam)
    {
        $exam->load(['packages' => function ($q) {
            $q->orderByDesc('revision');
        }]);

        $latestPackage = $exam->packages->first();

        return view('center-sync.exams.show', compact('exam', 'latestPackage'));
    }

    /**
     * Publish an exam (create revision 1).
     */
    public function publish(Request $request, GeneralExam $exam)
    {
        $builder = app(ExamPackageBuilder::class);
        $publishService = app(ExamPublishService::class);

        try {
            $publishService->publish($exam, auth()->id());

            return redirect()
                ->route('center-sync.exams.show', $exam)
                ->with('success', 'Exam published successfully. Centers can now pull this exam.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to publish exam: ' . $e->getMessage());
        }
    }

    /**
     * Show the revision form (grading-only corrections).
     */
    public function reviseForm(GeneralExam $exam)
    {
        if (!$exam->published_at) {
            return redirect()
                ->route('center-sync.exams.show', $exam)
                ->with('error', 'This exam has not been published yet.');
        }

        $exam->load(['sections.questions']);

        return view('center-sync.exams.revise', compact('exam'));
    }

    /**
     * Create a new revision with grading-only changes.
     */
    public function revise(Request $request, GeneralExam $exam)
    {
        if (!$exam->published_at) {
            return redirect()
                ->back()
                ->with('error', 'This exam has not been published yet.');
        }

        $validated = $request->validate([
            'excluded_questions' => 'array',
            'excluded_questions.*.question_id' => 'required|integer',
            'excluded_questions.*.excluded' => 'boolean',
            'excluded_questions.*.award_marks' => 'boolean',
            'answer_keys' => 'array',
            'answer_keys.*.question_id' => 'required|integer',
            'answer_keys.*.correct_answer' => 'required|string|max:1',
            'marks' => 'array',
            'marks.*.question_id' => 'required|integer',
            'marks.*.marks' => 'required|numeric|min:0',
        ]);

        // Apply grading-only changes
        if (isset($validated['excluded_questions'])) {
            foreach ($validated['excluded_questions'] as $change) {
                $question = $exam->questions()->find($change['question_id']);
                if ($question) {
                    $question->update([
                        'excluded_from_grading' => $change['excluded'],
                        'award_marks_on_exclusion' => $change['award_marks'] ?? false,
                    ]);
                }
            }
        }

        if (isset($validated['answer_keys'])) {
            foreach ($validated['answer_keys'] as $change) {
                $question = $exam->questions()->find($change['question_id']);
                if ($question) {
                    $question->update([
                        'correct_answer' => strtoupper(trim($change['correct_answer'])),
                    ]);
                }
            }
        }

        if (isset($validated['marks'])) {
            foreach ($validated['marks'] as $change) {
                $question = $exam->questions()->find($change['question_id']);
                if ($question) {
                    $question->update([
                        'marks' => number_format($change['marks'], 2, '.', ''),
                    ]);
                }
            }
        }

        // Create new revision
        $publishService = app(ExamPublishService::class);
        try {
            $publishService->revise($exam, auth()->id());

            return redirect()
                ->route('center-sync.exams.show', $exam)
                ->with('success', 'Revision created. Centers will pull the updated grading rules.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create revision: ' . $e->getMessage());
        }
    }

    /**
     * Release results for an exam.
     */
    public function releaseResults(Request $request, GeneralExam $exam)
    {
        $exam->update([
            'results_released' => true,
            'state_version' => ($exam->state_version ?? 1) + 1,
        ]);

        return redirect()
            ->route('center-sync.exams.show', $exam)
            ->with('success', 'Results released. Candidates can now view their scores.');
    }
}
