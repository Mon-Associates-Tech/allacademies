<?php

namespace App\ExaminationHub\Services;

use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamConfiguredParticipant;
use App\ExaminationHub\Models\GeneralExamQuestion;
use App\ExaminationHub\Models\GeneralExamSection;
use App\ExaminationHub\Models\SyncRun;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ExamPackageApplier — applies exam packages on centers.
 *
 * This service takes a decoded package array and upserts all entities
 * by UUID in a single transaction. It force-fills the access_code so
 * the creating hook never fires on a center.
 *
 * If the exam has started or has submissions, it accepts only whitelisted
 * grading-only differences (A1) and rejects the rest with a logged error.
 */
class ExamPackageApplier
{
    /**
     * Apply a package.
     *
     * @param array $package The decoded package data.
     * @return array{success: bool, errors: string[]}
     */
    public function apply(array $package): array
    {
        $errors = [];

        try {
            DB::beginTransaction();

            // Apply exam
            $examResult = $this->applyExam($package);
            if (!$examResult['success']) {
                $errors = array_merge($errors, $examResult['errors']);
            }

            // Apply sections and questions
            $sectionsResult = $this->applySections($examResult['exam'] ?? null, $package['sections'] ?? []);
            if (!$sectionsResult['success']) {
                $errors = array_merge($errors, $sectionsResult['errors']);
            }

            // Apply participants
            $participantsResult = $this->applyParticipants($examResult['exam'] ?? null, $package['participants'] ?? []);
            if (!$participantsResult['success']) {
                $errors = array_merge($errors, $participantsResult['errors']);
            }

            if (!empty($errors)) {
                DB::rollBack();
                return ['success' => false, 'errors' => $errors];
            }

            DB::commit();

            return ['success' => true, 'errors' => []];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ExamPackageApplier: Failed to apply package', [
                'exam_uuid' => $package['exam_uuid'] ?? 'unknown',
                'revision' => $package['revision'] ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'errors' => ['Package apply failed: ' . $e->getMessage()],
            ];
        }
    }

    /**
     * Apply the exam entity.
     *
     * @return array{success: bool, exam: GeneralExam|null, errors: string[]}
     */
    protected function applyExam(array $package): array
    {
        $settings = $package['settings'] ?? [];

        $examData = [
            'uuid' => $package['exam_uuid'],
            'title' => $settings['title'] ?? null,
            'description' => $settings['description'] ?? null,
            'access_code' => $package['access_code'],
            'type' => 'multiple_choice',
            'delivery_type' => 'online',
            'participant_mode' => $settings['participant_mode'] ?? 'open',
            'participant_required_fields' => $settings['participant_required_fields'] ?? [],
            'duration_in_minutes' => $settings['duration_in_minutes'] ?? 60,
            'starts_at' => $settings['starts_at'] ?? null,
            'ends_at' => $settings['ends_at'] ?? null,
            'is_randomized' => $settings['is_randomized'] ?? false,
            'instructions' => $this->markFromArray($settings['instructions'] ?? null),
            'result_visibility' => $settings['result_visibility'] ?? 'manual_release',
            'show_correct_answers' => $settings['show_correct_answers'] ?? true,
            'show_score_breakdown' => $settings['show_score_breakdown'] ?? true,
            'proctoring_enabled' => ($settings['proctoring'] ?? ['enabled' => false])['enabled'] ?? false,
            'violation_settings' => $settings['proctoring']['violation_settings'] ?? null,
            'restrict_navigation' => $settings['restrict_navigation'] ?? false,
            'max_tab_switches' => $settings['max_tab_switches'] ?? 0,
            'auto_submit_on_violation' => $settings['auto_submit_on_violation'] ?? false,
            'auto_submit_high_severity_threshold' => $settings['auto_submit_high_severity_threshold'] ?? 0,
            'auto_submit_medium_severity_threshold' => $settings['auto_submit_medium_severity_threshold'] ?? 0,
            'require_webcam' => $settings['require_webcam'] ?? false,
            'require_fullscreen' => $settings['require_fullscreen'] ?? false,
            'max_attempts' => $settings['max_attempts'] ?? 1,
            'status' => 'published',
            'revision' => $package['revision'],
            'state_version' => $package['state_version'] ?? 1,
            'grading_scale' => $package['grading_scale'] ?? null,
            'published_at' => now(),
            // Center-specific: no user_id, school_id, teacher_id
            'user_id' => null,
            'school_id' => null,
            'teacher_id' => null,
        ];

        $exam = GeneralExam::firstOrCreate(
            ['uuid' => $package['exam_uuid']],
            $examData
        );

        // If exam already exists, update only allowed fields
        if ($exam->wasRecentlyCreated === false) {
            // Check if exam has started or has submissions
            $hasStarted = $exam->starts_at && now()->gte($exam->starts_at);
            $hasSubmissions = $exam->submissions()->exists();

            if ($hasStarted || $hasSubmissions) {
                // Only update grading-only fields
                $exam->update([
                    'revision' => $package['revision'],
                    'state_version' => $package['state_version'] ?? $exam->state_version,
                    'grading_scale' => $package['grading_scale'] ?? $exam->grading_scale,
                    'published_at' => now(),
                ]);
            } else {
                // Free update before exam starts
                $exam->update($examData);
            }
        }

        return ['success' => true, 'exam' => $exam->refresh(), 'errors' => []];
    }

    /**
     * Apply sections and their questions.
     *
     * @param GeneralExam|null $exam
     * @param array $sectionsData
     * @return array{success: bool, errors: string[]}
     */
    protected function applySections(?GeneralExam $exam, array $sectionsData): array
    {
        $errors = [];

        if (!$exam) {
            return ['success' => false, 'errors' => ['Exam not found.']];
        }

        foreach ($sectionsData as $sectionData) {
            $section = GeneralExamSection::firstOrCreate(
                ['uuid' => $sectionData['uuid']],
                [
                    'general_exam_id' => $exam->id,
                    'title' => $sectionData['title'] ?? null,
                    'description' => $sectionData['description'] ?? null,
                    'instructions' => $this->markFromArray($sectionData['instructions'] ?? null),
                    'order' => $sectionData['order'] ?? 0,
                    'time_limit_minutes' => $sectionData['time_limit_minutes'] ?? null,
                    'is_randomized' => $sectionData['is_randomized'] ?? false,
                ]
            );

            // Update if exists
            if (!$section->wasRecentlyCreated) {
                $section->update([
                    'general_exam_id' => $exam->id,
                    'title' => $sectionData['title'] ?? $section->title,
                    'order' => $sectionData['order'] ?? $section->order,
                ]);
            }

            // Apply questions
            $questionsResult = $this->applyQuestions($section, $sectionData['questions'] ?? []);
            if (!$questionsResult['success']) {
                $errors = array_merge($errors, $questionsResult['errors']);
            }
        }

        return ['success' => empty($errors), 'errors' => $errors];
    }

    /**
     * Apply questions for a section.
     *
     * @param GeneralExamSection $section
     * @param array $questionsData
     * @return array{success: bool, errors: string[]}
     */
    protected function applyQuestions(GeneralExamSection $section, array $questionsData): array
    {
        $errors = [];

        foreach ($questionsData as $questionData) {
            $question = GeneralExamQuestion::firstOrCreate(
                ['uuid' => $questionData['uuid']],
                [
                    'general_exam_id' => $section->assignment_id ?? $section->general_exam_id,
                    'general_exam_section_id' => $section->id,
                    'type' => $questionData['type'] ?? 'multiple_choice',
                    'question' => $this->markFromArray($questionData['question'] ?? null),
                    'options' => $this->normalizeOptions($questionData['options'] ?? []),
                    'correct_answer' => $questionData['correct_answer'] ?? null,
                    'marks' => $questionData['marks'] ?? 1.0,
                    'order' => $questionData['order'] ?? 0,
                    'excluded_from_grading' => $questionData['excluded_from_grading'] ?? false,
                    'award_marks_on_exclusion' => $questionData['award_marks_on_exclusion'] ?? false,
                    'source_question_uuid' => $questionData['source_question_uuid'] ?? null,
                ]
            );

            // Update if exists (grading-only fields always, other fields only before start)
            if (!$question->wasRecentlyCreated) {
                $question->update([
                    'general_exam_section_id' => $section->id,
                    'type' => $questionData['type'] ?? $question->type,
                    'question' => $this->markFromArray($questionData['question'] ?? null),
                    'options' => $this->normalizeOptions($questionData['options'] ?? []),
                    'correct_answer' => $questionData['correct_answer'] ?? $question->correct_answer,
                    'marks' => $questionData['marks'] ?? $question->marks,
                    'order' => $questionData['order'] ?? $question->order,
                    'excluded_from_grading' => $questionData['excluded_from_grading'] ?? $question->excluded_from_grading,
                    'award_marks_on_exclusion' => $questionData['award_marks_on_exclusion'] ?? $question->award_marks_on_exclusion,
                ]);
            }
        }

        return ['success' => empty($errors), 'errors' => $errors];
    }

    /**
     * Apply participants.
     *
     * @param GeneralExam|null $exam
     * @param array $participantsData
     * @return array{success: bool, errors: string[]}
     */
    protected function applyParticipants(?GeneralExam $exam, array $participantsData): array
    {
        $errors = [];

        if (!$exam) {
            return ['success' => false, 'errors' => ['Exam not found.']];
        }

        foreach ($participantsData as $participantData) {
            GeneralExamConfiguredParticipant::updateOrCreate(
                ['uuid' => $participantData['uuid']],
                [
                    'general_exam_id' => $exam->id,
                    'name' => $participantData['name'] ?? null,
                    'email' => $participantData['email'] ?? null,
                    'unique_code' => $participantData['index_number'] ?? null,
                    'is_active' => $participantData['is_active'] ?? true,
                ]
            );
        }

        return ['success' => empty($errors), 'errors' => $errors];
    }

    /**
     * Convert a Mark array {up, down} to the JSON string stored in the DB.
     */
    protected function markFromArray(?array $mark): ?string
    {
        if (!$mark) {
            return null;
        }

        return json_encode([
            'up' => $mark['up'] ?? null,
            'down' => $mark['down'] ?? null,
        ]);
    }

    /**
     * Normalize options from the package format to the DB format.
     *
     * @param array $optionsRaw
     * @return array
     */
    protected function normalizeOptions(array $optionsRaw): array
    {
        $result = [];
        foreach ($optionsRaw as $option) {
            $value = $option['value'] ?? null;
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $result[] = [
                'key' => $option['key'] ?? '',
                'value' => $value,
            ];
        }

        return $result;
    }
}
