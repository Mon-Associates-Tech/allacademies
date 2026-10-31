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
        if ($exam->status !== 'published') {
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
        if ($exam->status !== 'published') {
            return redirect()
                ->back()
                ->with('error', 'This exam has not been published yet.');
        }

        $validated = $request->validate([
            'questions' => 'array',
            'questions.*.id' => 'required|integer|exists:general_exam_questions,id',
            'questions.*.question' => 'nullable|string',
            'questions.*.excluded_from_grading' => 'boolean',
            'questions.*.award_marks_on_exclusion' => 'boolean',
            'questions.*.correct_answer' => 'nullable|string|max:1',
            'questions.*.marks' => 'nullable|numeric|min:0',
            'questions.*.options' => 'nullable|array',
            'questions.*.options.*.key' => 'nullable|string|max:1',
            'questions.*.options.*.value' => 'nullable|string',
        ]);

        foreach ($validated['questions'] ?? [] as $change) {
            $question = GeneralExamQuestion::find($change['id']);
            if (!$question || $question->general_exam_id !== $exam->id) {
                continue;
            }

            $updates = [];
            if (isset($change['question'])) {
                // Store question text as plain string — Mark cast will handle conversion
                $updates['question'] = trim($change['question']);
            }
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
            if (isset($change['options']) && is_array($change['options'])) {
                // Rebuild options array from form data
                $newOptions = [];
                foreach ($change['options'] as $opt) {
                    if (!empty($opt['key']) && isset($opt['value'])) {
                        $newOptions[] = ['key' => strtoupper($opt['key']), 'value' => $opt['value']];
                    }
                }
                if (!empty($newOptions)) {
                    $updates['options'] = $newOptions;
                }
            }

            if (!empty($updates)) {
                $question->update($updates);
            }
        }

        // Create a new revision to propagate changes to centers
        $publishService = app(ExamPublishService::class);
        $result = $publishService->revise($exam);

        if (!$result['success']) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', implode(' ', $result['errors']));
        }

        return redirect()
            ->back()
            ->with('success', 'Corrections applied. A new revision has been created for centers to pull.');
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
        $publishService->revise($exam);

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
        $publishService->revise($exam);

        return redirect()
            ->back()
            ->with('success', 'Question restored to grading.');
    }
}
