<?php

namespace App\MockExam\Services;

use App\MockExam\Models\MockExam;
use App\MockExam\Models\MockExamIdentityField;
use App\MockExam\Models\MockExamSubjectExam;
use App\MockExam\Models\MockExamTemplate;
use App\MockExam\Models\MockExamUserIdentity;
use App\Support\MarkdownMathService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MockExamPdfService
{
    public function __construct(
        private readonly MarkdownMathService $markdownMathService
    ) {}
    /**
     * For subject exams with no template_id, attempt to resolve a template
     * by matching academic_subject_id (falling back to any template with a
     * front_page_config that has blocks).
     */
    private function resolveTemplates(MockExam $mockExam): void
    {
        $subjectIds = $mockExam->subjectExams
            ->whereNull('template_id')
            ->pluck('academic_subject_id')
            ->filter()
            ->unique();

        if ($subjectIds->isEmpty()) {
            return;
        }

        $templates = MockExamTemplate::whereIn('academic_subject_id', $subjectIds)
            ->whereNotNull('front_page_config')
            ->get()
            ->keyBy('academic_subject_id');

        foreach ($mockExam->subjectExams as $se) {
            if ($se->template_id === null && isset($templates[$se->academic_subject_id])) {
                $se->setRelation('template', $templates[$se->academic_subject_id]);
            }
        }
    }

    private function resolveSubjectExamTemplate(MockExamSubjectExam $subjectExam): void
    {
        if ($subjectExam->template_id !== null || ! $subjectExam->academic_subject_id) {
            return;
        }

        $template = MockExamTemplate::where('academic_subject_id', $subjectExam->academic_subject_id)
            ->whereNotNull('front_page_config')
            ->latest()
            ->first();

        if ($template) {
            $subjectExam->setRelation('template', $template);
        }
    }

    /**
     * Generate a downloadable PDF of the exam paper (questions only).
     */
    public function generateExamPdf(MockExam $mockExam, float $fontSize = 10.5): Response
    {
        $mockExam->load([
            'subjectExams.academicSubject',
            'subjectExams.sections.questions',
            'subjectExams.template',
            'user',
        ]);

        $this->resolveTemplates($mockExam);
        $this->renderQuestionMath($this->allQuestions($mockExam));

        $pdf = Pdf::loadView('mock-exam.pdf.exam', [
            'mockExam' => $mockExam,
            'fontSize' => $fontSize,
            'identity' => $this->resolveIdentitySection(),
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('margin_top', 8)
            ->setOption('margin_right', 10)
            ->setOption('margin_bottom', 12)
            ->setOption('margin_left', 10);

        return $pdf->download($this->filename($mockExam, 'exam'));
    }

    /**
     * Generate a streamable PDF for preview.
     */
    public function previewExamPdf(MockExam $mockExam, float $fontSize = 10.5): Response
    {
        $mockExam->load([
            'subjectExams.academicSubject',
            'subjectExams.sections.questions',
            'subjectExams.template',
            'user',
        ]);

        $this->resolveTemplates($mockExam);
        $this->renderQuestionMath($this->allQuestions($mockExam));

        $pdf = Pdf::loadView('mock-exam.pdf.exam', [
            'mockExam' => $mockExam,
            'fontSize' => $fontSize,
            'identity' => $this->resolveIdentitySection(),
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('margin_top', 8)
            ->setOption('margin_right', 10)
            ->setOption('margin_bottom', 12)
            ->setOption('margin_left', 10);

        return $pdf->stream($this->filename($mockExam, 'exam'));
    }

    /**
     * Generate a downloadable PDF of the answer key (correct answers + explanations).
     */
    public function generateAnswerKeyPdf(MockExam $mockExam): Response
    {
        $mockExam->load([
            'subjectExams.academicSubject',
            'subjectExams.sections.questions',
        ]);

        $pdf = Pdf::loadView('mock-exam.pdf.answer-key', ['mockExam' => $mockExam])
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true);

        return $pdf->download($this->filename($mockExam, 'answer-key'));
    }

    /**
     * Generate a downloadable PDF for a single subject exam.
     */
    public function generateSubjectExamPdf(MockExamSubjectExam $subjectExam, float $fontSize = 10.5): Response
    {
        $subjectExam->load([
            'mockExam',
            'academicSubject',
            'academicLevel',
            'academicGroup',
            'sections.questions',
            'template',
        ]);

        $this->resolveSubjectExamTemplate($subjectExam);
        $this->renderQuestionMath($this->allQuestions($subjectExam));

        $pdf = Pdf::loadView('mock-exam.pdf.subject-exam', [
            'subjectExam' => $subjectExam,
            'fontSize' => $fontSize,
            'identity' => $this->resolveIdentitySection(),
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('margin_top', 8)
            ->setOption('margin_right', 10)
            ->setOption('margin_bottom', 12)
            ->setOption('margin_left', 10);

        return $pdf->download($this->subjectExamFilename($subjectExam));
    }

    /**
     * Generate a streamable PDF for subject exam preview.
     */
    public function previewSubjectExamPdf(MockExamSubjectExam $subjectExam, float $fontSize = 10.5): Response
    {
        $subjectExam->load([
            'mockExam',
            'academicSubject',
            'academicLevel',
            'academicGroup',
            'sections.questions',
            'template',
        ]);

        $this->resolveSubjectExamTemplate($subjectExam);
        $this->renderQuestionMath($this->allQuestions($subjectExam));

        $pdf = Pdf::loadView('mock-exam.pdf.subject-exam', [
            'subjectExam' => $subjectExam,
            'fontSize' => $fontSize,
            'identity' => $this->resolveIdentitySection(),
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('margin_top', 8)
            ->setOption('margin_right', 10)
            ->setOption('margin_bottom', 12)
            ->setOption('margin_left', 10);

        return $pdf->stream($this->subjectExamFilename($subjectExam));
    }

    private function filename(MockExam $mockExam, string $suffix): string
    {
        return Str::slug($mockExam->title) . '-' . $suffix . '.pdf';
    }

    private function subjectExamFilename(MockExamSubjectExam $subjectExam): string
    {
        $mockExamTitle = Str::slug($subjectExam->mockExam->title);
        $subjectName = Str::slug($subjectExam->academicSubject?->name ?? 'subject');
        return "{$mockExamTitle}-{$subjectName}.pdf";
    }


    /**
     * dompdf never executes JavaScript, so the Alpine/KaTeX rendering that
     * <x-ui.latex>/<x-ui.prose-content> do in the browser is completely inert
     * in generated PDFs — question_text and options would print as raw
     * markdown/LaTeX otherwise. Pre-render them to HTML server-side, in one
     * batched Node call, before the view ever sees them. Mutates the
     * in-memory models only for the current request — nothing is persisted.
     */
    private function renderQuestionMath(Collection $questions): void
    {
        $batch = [];

        // dd($questions);

        // Temporarily add as the first line inside renderQuestionMath():
        \Illuminate\Support\Facades\Log::info('renderQuestionMath running', ['question_count' => $questions->count()]);

        foreach ($questions as $question) {
            if (is_string($question->question_text) && !$this->looksLikeHtml($question->question_text)) {
                $batch["q{$question->id}_text"] = $question->question_text;
            }

            foreach ($question->getOptionsForDisplay() as $i => $option) {
                if (is_string($option) && !$this->looksLikeHtml($option)) {
                    $batch["q{$question->id}_opt{$i}"] = $option;
                }
            }
        }

        if ($batch === []) {
            return;
        }

        $rendered = $this->markdownMathService->renderMany($batch);

        foreach ($questions as $question) {
            if (isset($rendered["q{$question->id}_text"])) {
                $question->question_text = $this->unwrapSingleParagraph($rendered["q{$question->id}_text"]);
            }

            if (is_array($question->options)) {
                $renderedOptions = [];
                foreach ($question->options as $i => $option) {
                    $renderedOptions[$i] = isset($rendered["q{$question->id}_opt{$i}"])
                        ? $this->unwrapSingleParagraph($rendered["q{$question->id}_opt{$i}"])
                        : $option;
                }
                $question->options = $renderedOptions;
            }
        }
    }

    /**
     * Same heuristic x-ui.prose-content uses client-side: if the content
     * already contains block-level HTML tags, it was authored as HTML, not
     * markdown — leave it untouched rather than running it through CommonMark,
     * which would strip the tags (html_input => 'strip').
     */
    private function looksLikeHtml(string $content): bool
    {
        return (bool) preg_match('/<(p|div|table|img|iframe|h[1-6]|ul|ol|br)\b/i', $content);
    }

    /**
     * MarkdownMathService wraps single-line content in <p>...</p> (CommonMark's
     * normal behaviour). That's correct for multi-paragraph question stems, but
     * question_text/options render inline (<x-ui.latex inline="true">) next to
     * a "1." label — the browser-side component would strip that wrapper via
     * JS, but JS never runs under dompdf. Strip it here instead, only when the
     * whole string is exactly one paragraph, so multi-part question stems keep
     * their paragraph breaks.
     */
    private function unwrapSingleParagraph(string $html): string
    {
        return preg_replace('/^<p>(.*)<\/p>\s*$/is', '$1', trim($html)) ?? $html;
    }

    /**
     * Flatten every question across an exam (or a single subject exam) into
     * one collection, so renderQuestionMath() batches them in a single Node
     * call rather than spawning one process per question.
     */
    private function allQuestions(MockExam|MockExamSubjectExam $exam): Collection
    {
        $subjectExams = $exam instanceof MockExam ? $exam->subjectExams : collect([$exam]);

        return $subjectExams->flatMap(
            fn ($se) => $se->sections->flatMap(fn ($s) => $s->questions)
        );
    }

    private function resolveIdentitySection(): ?array
    {
        $user = Auth::user();

        if (! $user) {
            return null;
        }

        $fields = MockExamIdentityField::ordered()->get();

        if ($fields->isEmpty()) {
            return null;
        }

        $identity = MockExamUserIdentity::where('user_id', $user->id)->first();

        if (! $identity) {
            return null;
        }

        $entries = $fields
            ->map(function ($field) use ($identity) {
                $value = $identity->valueFor($field->key);

                return ($value !== null && $value !== '') ? [
                    'type'      => $field->type,
                    'pretext'   => $field->pretext,
                    'value'     => $value,
                    'font_size' => $field->font_size,
                    'same_row'  => $field->same_row,
                ] : null;
            })
            ->filter()
            ->values();

        if ($entries->isEmpty()) {
            return null;
        }

        // A same_row entry joins the row of the last entry that actually rendered
        // (i.e. had a value) — if the field it was meant to share a row with was
        // empty and got filtered out, it gracefully joins whatever came before it.
        $rows = [];

        foreach ($entries as $entry) {
            if ($entry['same_row'] && $rows !== []) {
                $rows[count($rows) - 1][] = $entry;
            } else {
                $rows[] = [$entry];
            }
        }

        return ['rows' => $rows];
    }
}
