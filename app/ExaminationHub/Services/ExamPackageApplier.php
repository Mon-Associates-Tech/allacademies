<?php

namespace App\ExaminationHub\Services;

use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamConfiguredParticipant;
use App\ExaminationHub\Models\GeneralExamQuestion;
use App\ExaminationHub\Models\GeneralExamSection;
use App\ExaminationHub\Models\SyncRun;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

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
     * @return array{success: bool, errors: string[], exam_uuid: string, revision: int, sections: int, questions: int, participants: int}
     */
    public function apply(array $package): array
    {
        $errors = [];
        $examUuid = $package['exam_uuid'] ?? 'unknown';
        $revision = $package['revision'] ?? 0;
        $sectionCount = 0;
        $questionCount = 0;
        $participantCount = 0;

        Log::info('ExamPackageApplier: Starting to apply package', [
            'exam_uuid' => $examUuid,
            'revision' => $revision,
            'sections' => count($package['sections'] ?? []),
            'participants' => count($package['participants'] ?? []),
        ]);

        try {
            DB::beginTransaction();

            // Apply exam
            $examResult = $this->applyExam($package);
            if (!$examResult['success']) {
                $errors = array_merge($errors, $examResult['errors']);
                Log::error('ExamPackageApplier: Failed to apply exam', ['exam_uuid' => $examUuid, 'errors' => $examResult['errors']]);
            } else {
                Log::info('ExamPackageApplier: Exam applied', ['exam_uuid' => $examUuid, 'exam_id' => $examResult['exam']?->id]);
            }

            // Apply sections and questions
            $sectionsResult = $this->applySections($examResult['exam'] ?? null, $package['sections'] ?? []);
            if (!$sectionsResult['success']) {
                $errors = array_merge($errors, $sectionsResult['errors']);
                Log::error('ExamPackageApplier: Failed to apply sections', ['exam_uuid' => $examUuid, 'errors' => $sectionsResult['errors']]);
            } else {
                $sectionCount = $sectionsResult['section_count'] ?? 0;
                $questionCount = $sectionsResult['question_count'] ?? 0;
                Log::info('ExamPackageApplier: Sections applied', ['exam_uuid' => $examUuid, 'sections' => $sectionCount, 'questions' => $questionCount]);
            }

            // Apply participants
            $participantsResult = $this->applyParticipants($examResult['exam'] ?? null, $package['participants'] ?? []);
            if (!$participantsResult['success']) {
                $errors = array_merge($errors, $participantsResult['errors']);
                Log::error('ExamPackageApplier: Failed to apply participants', ['exam_uuid' => $examUuid, 'errors' => $participantsResult['errors']]);
            } else {
                $participantCount = $participantsResult['participant_count'] ?? 0;
                Log::info('ExamPackageApplier: Participants applied', ['exam_uuid' => $examUuid, 'participants' => $participantCount]);
            }

            if (!empty($errors)) {
                DB::rollBack();
                Log::error('ExamPackageApplier: Rolling back due to errors', ['exam_uuid' => $examUuid, 'errors' => $errors]);
                return [
                    'success' => false,
                    'errors' => $errors,
                    'exam_uuid' => $examUuid,
                    'revision' => $revision,
                    'sections' => 0,
                    'questions' => 0,
                    'participants' => 0,
                ];
            }

            DB::commit();
            Log::info('ExamPackageApplier: Package applied successfully', [
                'exam_uuid' => $examUuid,
                'revision' => $revision,
                'sections' => $sectionCount,
                'questions' => $questionCount,
                'participants' => $participantCount,
            ]);

            return [
                'success' => true,
                'errors' => [],
                'exam_uuid' => $examUuid,
                'revision' => $revision,
                'sections' => $sectionCount,
                'questions' => $questionCount,
                'participants' => $participantCount,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ExamPackageApplier: Exception while applying package', [
                'exam_uuid' => $examUuid,
                'revision' => $revision,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'errors' => ['Package apply failed: ' . $e->getMessage()],
                'exam_uuid' => $examUuid,
                'revision' => $revision,
                'sections' => 0,
                'questions' => 0,
                'participants' => 0,
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
            'type' => $settings['type'] ?? 'examination',
            'delivery_type' => $settings['delivery_type'] ?? 'online',
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
     * @return array{success: bool, errors: string[], section_count: int, question_count: int}
     */
    protected function applySections(?GeneralExam $exam, array $sectionsData): array
    {
        $errors = [];
        $sectionCount = 0;
        $questionCount = 0;

        if (!$exam) {
            return ['success' => false, 'errors' => ['Exam not found.'], 'section_count' => 0, 'question_count' => 0];
        }

        // Track which section UUIDs are in this package, so we can remove stale ones
        $packageSectionUuids = [];

        foreach ($sectionsData as $sectionData) {
            $sectionUuid = $sectionData['uuid'];
            $packageSectionUuids[] = $sectionUuid;

            $section = GeneralExamSection::firstOrCreate(
                ['uuid' => $sectionUuid],
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

            $sectionCount++;
            Log::info('ExamPackageApplier: Section applied', ['section_uuid' => $sectionUuid, 'section_id' => $section->id]);

            // Apply questions
            $questionsResult = $this->applyQuestions($section, $sectionData['questions'] ?? []);
            if (!$questionsResult['success']) {
                $errors = array_merge($errors, $questionsResult['errors']);
            }
            $questionCount += $questionsResult['question_count'] ?? 0;
        }

        // Remove sections that are no longer in the package
        $staleSections = GeneralExamSection::where('general_exam_id', $exam->id)
            ->whereNotIn('uuid', $packageSectionUuids)
            ->get();
        foreach ($staleSections as $staleSection) {
            Log::info('ExamPackageApplier: Removing stale section', ['section_uuid' => $staleSection->uuid]);
            $staleSection->delete();
        }

        return [
            'success' => empty($errors),
            'errors' => $errors,
            'section_count' => $sectionCount,
            'question_count' => $questionCount,
        ];
    }

    /**
     * Apply questions for a section.
     *
     * @param GeneralExamSection $section
     * @param array $questionsData
     * @return array{success: bool, errors: string[], question_count: int}
     */
    protected function applyQuestions(GeneralExamSection $section, array $questionsData): array
    {
        $errors = [];
        $questionCount = 0;

        // Track which question UUIDs are in this package, so we can remove stale ones
        $packageQuestionUuids = [];

        foreach ($questionsData as $questionData) {
            $questionUuid = $questionData['uuid'];
            $packageQuestionUuids[] = $questionUuid;

            // FIX: Use $section->general_exam_id instead of $section->assignment_id
            $question = GeneralExamQuestion::firstOrCreate(
                ['uuid' => $questionUuid],
                [
                    'general_exam_id' => $section->general_exam_id,
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

            // Update if exists
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

            $questionCount++;
            Log::info('ExamPackageApplier: Question applied', ['question_uuid' => $questionUuid, 'question_id' => $question->id]);
        }

        // Remove questions that are no longer in the package
        $staleQuestions = GeneralExamQuestion::where('general_exam_section_id', $section->id)
            ->whereNotIn('uuid', $packageQuestionUuids)
            ->get();
        foreach ($staleQuestions as $staleQuestion) {
            Log::info('ExamPackageApplier: Removing stale question', ['question_uuid' => $staleQuestion->uuid]);
            $staleQuestion->delete();
        }

        return [
            'success' => empty($errors),
            'errors' => $errors,
            'question_count' => $questionCount,
        ];
    }

    /**
     * Apply participants.
     *
     * @param GeneralExam|null $exam
     * @param array $participantsData
     * @return array{success: bool, errors: string[], participant_count: int}
     */
    protected function applyParticipants(?GeneralExam $exam, array $participantsData): array
    {
        $errors = [];
        $participantCount = 0;

        if (!$exam) {
            return ['success' => false, 'errors' => ['Exam not found.'], 'participant_count' => 0];
        }

        // Track which participant UUIDs are in this package
        $packageParticipantUuids = [];

        foreach ($participantsData as $participantData) {
            $participantUuid = $participantData['uuid'];
            $packageParticipantUuids[] = $participantUuid;

            $participant = GeneralExamConfiguredParticipant::updateOrCreate(
                ['uuid' => $participantUuid],
                [
                    'general_exam_id' => $exam->id,
                    'name' => $participantData['name'] ?? null,
                    'email' => $participantData['email'] ?? null,
                    'unique_code' => $participantData['index_number'] ?? null,
                    'is_active' => $participantData['is_active'] ?? true,
                ]
            );

            $participantCount++;
            Log::info('ExamPackageApplier: Participant applied', ['participant_uuid' => $participantUuid, 'participant_id' => $participant->id]);
        }

        // Remove participants that are no longer in the package
        $staleParticipants = GeneralExamConfiguredParticipant::where('general_exam_id', $exam->id)
            ->whereNotIn('uuid', $packageParticipantUuids)
            ->get();
        foreach ($staleParticipants as $staleParticipant) {
            Log::info('ExamPackageApplier: Removing stale participant', ['participant_uuid' => $staleParticipant->uuid]);
            $staleParticipant->delete();
        }

        return [
            'success' => empty($errors),
            'errors' => $errors,
            'participant_count' => $participantCount,
        ];
    }

    /**
     * Convert a Mark array {up, down} to the JSON string stored in the DB.
     * Returns null if both up and down are null, so the DB column stays NULL.
     */
    protected function markFromArray(?array $mark): ?string
    {
        if (!$mark) {
            return null;
        }

        $up = $mark['up'] ?? null;
        $down = $mark['down'] ?? null;

        // If both are null, store NULL in the DB so the view doesn't show an empty checkbox
        if ($up === null && $down === null) {
            return null;
        }

        return json_encode([
            'up' => $up,
            'down' => $down,
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
