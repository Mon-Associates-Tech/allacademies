<?php

namespace App\Http\Controllers\Center;

use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamQuestion;
use App\ExaminationHub\Services\ExamPublishService;
use Illuminate\Http\Request;

/**
 * GradingCorrectionsController — exclude questions, fix answer keys, change marks.
 *
 * Only available on central instances (APP_ROLE=central).
 */
class GradingCorrectionsController
{
    /**
     * Show the grading corrections form for an exam.
     */
    public function show(GeneralExam $exam)
    {
        if (!$exam->published_at) {
            return redirect()
                ->route('center-sync.exams.show', $exam)
                ->with('error', 'This exam has not been published yet. Edit it directly instead.');
        }

        $exam->load(['sections.questions']);

        return view('center-sync.corrections.show', compact('exam'));
    }

    /**
     * Update grading corrections in bulk.
     */
    public function update(Request $request, GeneralExam $exam)
    {
        if (!$exam->published_at) {
            return redirect()
                ->back()
                ->with('error', 'This exam has not been published yet.');
        }

        $validated = $request->validate([
            'questions' => 'array',
            'questions.*.id' => 'required|integer|exists:general_exam_questions,id',
            'questions.*.excluded_from_grading' => 'boolean',
            'questions.*.award_marks_on_exclusion' => 'boolean',
            'questions.*.correct_answer' => 'nullable|string|max:1',
            'questions.*.marks' => 'nullable|numeric|min:0',
        ]);

        foreach ($validated['questions'] ?? [] as $change) {
            $question = GeneralExamQuestion::find($change['id']);
            if (!$question || $question->general_exam_id !== $exam->id) {
                continue;
            }

            $updates = [];
            if (array_key_exists('excluded_from_grading', $change)) {
                $updates['excluded_from_grading'] = $change['excluded_from_grading'];
            }
            if (array_key_exists('award_marks_on_exclusion', $change)) {
                $updates['award_marks_on_exclusion'] = $change['award_marks_on_exclusion'];
            }
            if (isset($change['correct_answer'])) {
                $updates['correct_answer'] = strtoupper(trim($change['correct_answer']));
            }
            if (isset($change['marks'])) {
                $updates['marks'] = number_format($change['marks'], 2, '.', '');
            }

            if (!empty($updates)) {
                $question->update($updates);
            }
        }

        // Create a new revision to propagate changes to centers
        $publishService = app(ExamPublishService::class);
        try {
            $publishService->revise($exam, auth()->id());

            return redirect()
                ->back()
                ->with('success', 'Corrections applied. A new revision has been created for centers to pull.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create revision: ' . $e->getMessage());
        }
    }

    /**
     * Exclude a single question from grading.
     */
    public function excludeQuestion(Request $request, GeneralExam $exam, $question)
    {
        $questionModel = GeneralExamQuestion::where('id', $question)
            ->where('general_exam_id', $exam->id)
            ->firstOrFail();

        $questionModel->update([
            'excluded_from_grading' => true,
            'award_marks_on_exclusion' => $request->boolean('award_marks'),
        ]);

        // Create revision
        $publishService = app(ExamPublishService::class);
        $publishService->revise($exam, auth()->id());

        return redirect()
            ->back()
            ->with('success', 'Question excluded from grading.');
    }

    /**
     * Restore a previously excluded question.
     */
    public function restoreQuestion(Request $request, GeneralExam $exam, $question)
    {
        $questionModel = GeneralExamQuestion::where('id', $question)
            ->where('general_exam_id', $exam->id)
            ->firstOrFail();

        $questionModel->update([
            'excluded_from_grading' => false,
            'award_marks_on_exclusion' => false,
        ]);

        // Create revision
        $publishService = app(ExamPublishService::class);
        $publishService->revise($exam, auth()->id());

        return redirect()
            ->back()
            ->with('success', 'Question restored to grading.');
    }
}
