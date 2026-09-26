<?php

namespace App\MockExam\Services;

use App\MockExam\Models\MockExam;
use App\MockExam\Models\MockExamIdentityField;
use App\MockExam\Models\MockExamSubjectExam;
use App\MockExam\Models\MockExamTemplate;
use App\MockExam\Models\MockExamUserIdentity;
use Illuminate\Support\Facades\Auth;

class MockExamPdfService
{
    /**
     * Data for the full exam view.
     */
    public function examData(MockExam $mockExam, float $fontSize = 10.5): array
    {
        $mockExam->load([
            'subjectExams.academicSubject',
            'subjectExams.sections.questions',
            'subjectExams.template',
            'user',
        ]);

        $this->resolveTemplates($mockExam);

        return [
            'mockExam' => $mockExam,
            'fontSize' => $fontSize,
            'identity' => $this->resolveIdentitySection(),
        ];
    }

    /**
     * Data for the answer key view.
     */
    public function answerKeyData(MockExam $mockExam): array
    {
        $mockExam->load([
            'subjectExams.academicSubject',
            'subjectExams.sections.questions',
        ]);

        return [
            'mockExam' => $mockExam,
        ];
    }

    /**
     * Data for a single subject exam view.
     */
    public function subjectExamData(MockExamSubjectExam $subjectExam, float $fontSize = 10.5): array
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

        return [
            'subjectExam' => $subjectExam,
            'fontSize' => $fontSize,
            'identity' => $this->resolveIdentitySection(),
        ];
    }

    /**
     * For subject exams with no template_id, attempt to resolve a template
     * by matching academic_subject_id.
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

        foreach ($mockExam->subjectExams as $subjectExam) {
            if ($subjectExam->template_id === null && isset($templates[$subjectExam->academic_subject_id])) {
                $subjectExam->setRelation('template', $templates[$subjectExam->academic_subject_id]);
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
                    'type' => $field->type,
                    'pretext' => $field->pretext,
                    'value' => $value,
                    'font_size' => $field->font_size,
                    'same_row' => $field->same_row,
                ] : null;
            })
            ->filter()
            ->values();

        if ($entries->isEmpty()) {
            return null;
        }

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
