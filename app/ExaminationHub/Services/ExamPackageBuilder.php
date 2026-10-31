<?php

namespace App\ExaminationHub\Services;

use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamConfiguredParticipant;
use App\ExaminationHub\Models\GeneralExamQuestion;
use App\ExaminationHub\Models\GeneralExamSection;
use App\Support\Mark;
use Illuminate\Support\Facades\DB;

/**
 * ExamPackageBuilder — builds canonical exam packages on central.
 *
 * This service reads an exam, its sections, questions, roster, and resolved
 * grading scale, then produces a canonical JSON payload suitable for signing
 * and distribution to centers.
 *
 * Key guarantees:
 * - Only UUIDs, never local integer IDs
 * - Canonical serialization: keys sorted, UTF-8, no pretty printing
 * - Mark values stored as {up, down} arrays
 * - Byte-identical output for the same input (deterministic)
 */
class ExamPackageBuilder
{
    /**
     * Build a canonical package array for the given exam.
     *
     * @return array{
     *     exam_uuid: string,
     *     revision: int,
     *     state_version: int,
     *     access_code: string,
     *     settings: array,
     *     grading_scale: array,
     *     sections: array,
     *     participants: array,
     *     checksum: string,
     *     signature: string
     * }
     */
    public function build(GeneralExam $exam): array
    {
        // Load all related data in one go
        $exam->load([
            'sections.questions',
            'configuredParticipants',
        ]);

        $payload = $this->buildPayload($exam);

        // Compute checksum over canonical JSON
        $canonicalJson = $this->toCanonicalJson($payload);
        $checksum = hash('sha256', $canonicalJson);

        // Sign with Ed25519
        $signature = $this->sign($checksum);

        // Add checksum and signature, then re-sort the full package
        $package = array_merge($payload, [
            'checksum' => $checksum,
            'signature' => $signature,
        ]);

        // Re-sort the full package so compress() produces the same JSON
        $this->sortArrayRecursive($package);

        return $package;
    }

    /**
     * Build the payload array without checksum/signature.
     */
    protected function buildPayload(GeneralExam $exam): array
    {
        $nextRevision = ($exam->revision ?? 0) + 1;

        return [
            'exam_uuid' => $exam->uuid,
            'revision' => $nextRevision,
            'state_version' => $exam->state_version ?? 1,
            'access_code' => $exam->access_code,
            'settings' => $this->buildSettings($exam),
            'grading_scale' => $this->buildGradingScale($exam),
            'sections' => $this->buildSections($exam->sections),
            'participants' => $this->buildParticipants($exam->configuredParticipants),
        ];
    }

    /**
     * Build the settings block from the exam model.
     */
    protected function buildSettings(GeneralExam $exam): array
    {
        $proctoring = [
            'enabled' => (bool) $exam->proctoring_enabled,
            'violation_settings' => $exam->resolvedViolationSettings(),
        ];

        return [
            'title' => $exam->title,
            'description' => $exam->description,
            'type' => $exam->type ?? 'examination',
            'delivery_type' => $exam->delivery_type ?? 'online',
            'starts_at' => $exam->starts_at?->toIso8601String(),
            'ends_at' => $exam->ends_at?->toIso8601String(),
            'duration_in_minutes' => (int) $exam->duration_in_minutes,
            'max_attempts' => (int) $exam->max_attempts,
            'is_randomized' => (bool) $exam->is_randomized,
            'participant_mode' => $exam->participant_mode,
            'participant_required_fields' => $exam->participant_required_fields ?? [],
            'proctoring' => $proctoring,
            'result_visibility' => $exam->result_visibility,
            'instructions' => $this->markToArray($exam->instructions),
            'show_correct_answers' => (bool) $exam->show_correct_answers,
            'show_score_breakdown' => (bool) $exam->show_score_breakdown,
            'restrict_navigation' => (bool) $exam->restrict_navigation,
            'max_tab_switches' => (int) ($exam->max_tab_switches ?? 0),
            'auto_submit_on_violation' => (bool) $exam->auto_submit_on_violation,
            'auto_submit_high_severity_threshold' => (int) ($exam->auto_submit_high_severity_threshold ?? 0),
            'auto_submit_medium_severity_threshold' => (int) ($exam->auto_submit_medium_severity_threshold ?? 0),
            'require_webcam' => (bool) $exam->require_webcam,
            'require_fullscreen' => (bool) $exam->require_fullscreen,
        ];
    }

    /**
     * Build the grading scale array.
     * Uses the embedded grading_scale if present, otherwise generates the default.
     */
    protected function buildGradingScale(GeneralExam $exam): array
    {
        if (!empty($exam->grading_scale)) {
            return $exam->grading_scale;
        }

        return GradeScale::defaultArray();
    }

    /**
     * Build the sections array with questions.
     *
     * @param \Illuminate\Database\Eloquent\Collection $sections
     * @return array
     */
    protected function buildSections($sections): array
    {
        $result = [];

        foreach ($sections->sortBy('order') as $section) {
            $sectionData = [
                'uuid' => $section->uuid,
                'order' => (int) $section->order,
                'title' => $section->title,
                'description' => $section->description,
                'instructions' => $this->markToArray($section->instructions),
                'time_limit_minutes' => $section->time_limit_minutes,
                'is_randomized' => (bool) $section->is_randomized,
                'questions' => $this->buildQuestions($section->questions),
            ];
            $result[] = $sectionData;
        }

        return $result;
    }

    /**
     * Build the questions array.
     *
     * @param \Illuminate\Database\Eloquent\Collection $questions
     * @return array
     */
    protected function buildQuestions($questions): array
    {
        $result = [];

        foreach ($questions->sortBy('order') as $question) {
            $questionData = [
                'uuid' => $question->uuid,
                'order' => (int) $question->order,
                'type' => $question->type,
                'marks' => $this->formatMarks($question->marks),
                'question' => $this->markToArray($question->question),
                'options' => $this->buildOptions($question->options),
                'correct_answer' => $question->correct_answer,
                'excluded_from_grading' => (bool) $question->excluded_from_grading,
                'award_marks_on_exclusion' => (bool) $question->award_marks_on_exclusion,
                'source_question_uuid' => $question->source_question_uuid,
            ];
            $result[] = $questionData;
        }

        return $result;
    }

    /**
     * Build the options array from the question's options JSON.
     *
     * Options are stored as an array of objects with 'key' and 'value'.
     * The value is a Mark object.
     *
     * @param array|null $optionsRaw
     * @return array
     */
    protected function buildOptions(?array $optionsRaw): array
    {
        if (!$optionsRaw) {
            return [];
        }

        $result = [];
        foreach ($optionsRaw as $option) {
            $value = $option['value'] ?? null;
            $result[] = [
                'key' => $option['key'] ?? '',
                'value' => is_string($value) ? Mark::fromString($value)->toArray() : ($value ?? ['up' => null, 'down' => null]),
            ];
        }

        return $result;
    }

    /**
     * Build the participants array.
     *
     * @param \Illuminate\Database\Eloquent\Collection $participants
     * @return array
     */
    protected function buildParticipants($participants): array
    {
        $result = [];

        foreach ($participants as $participant) {
            $result[] = [
                'uuid' => $participant->uuid,
                'index_number' => $participant->unique_code,
                'name' => $participant->name,
                'email' => $participant->email,
                'is_active' => (bool) $participant->is_active,
            ];
        }

        return $result;
    }

    /**
     * Format marks as a string with 2 decimal places for canonical output.
     */
    protected function formatMarks($marks): string
    {
        if ($marks === null) {
            return '0.00';
        }

        return number_format((float) $marks, 2, '.', '');
    }

    /**
     * Convert a Mark object to a canonical array {up, down}.
     */
    protected function markToArray($value): array
    {
        if ($value instanceof Mark) {
            return $value->toArray();
        }

        if (is_string($value)) {
            return Mark::fromString($value)->toArray();
        }

        if (is_array($value)) {
            return [
                'up' => $value['up'] ?? null,
                'down' => $value['down'] ?? null,
            ];
        }

        return ['up' => null, 'down' => null];
    }

    /**
     * Serialize to canonical JSON: keys sorted, UTF-8, no pretty printing.
     */
    public function toCanonicalJson(array $data): string
    {
        // Sort keys recursively for deterministic output
        $this->sortArrayRecursive($data);

        // JSON_UNESCAPED_UNICODE ensures UTF-8 characters are not escaped
        // JSON_PRESERVE_ZERO_FRACTION ensures 1.0 stays as 1.0, not 1
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION);

        if ($json === false) {
            throw new \RuntimeException('Failed to encode package to JSON: ' . json_last_error_msg());
        }

        return $json;
    }

    /**
     * Sort array keys recursively for deterministic JSON output.
     * Uses SORT_STRING to ensure consistent ordering across PHP versions.
     */
    protected function sortArrayRecursive(array &$array): void
    {
        $keys = array_keys($array);
        sort($keys, SORT_STRING);
        $sorted = [];
        foreach ($keys as $key) {
            $value = &$array[$key];
            if (is_array($value)) {
                $this->sortArrayRecursive($value);
            }
            $sorted[$key] = $value;
        }
        $array = $sorted;
    }

    /**
     * Sign the checksum using Ed25519 private key from config.
     * Returns base64-encoded signature.
     */
    protected function sign(string $checksum): string
    {
        $privateKeyBase64 = config('center.central_private_key');

        if (!$privateKeyBase64) {
            throw new \RuntimeException(
                'Cannot sign package: CENTRAL_PRIVATE_KEY is not configured. '
                . 'Run "php artisan sync:keys:generate" to generate keys.'
            );
        }

        $privateKey = base64_decode($privateKeyBase64);
        if ($privateKey === false) {
            throw new \RuntimeException('Invalid CENTRAL_PRIVATE_KEY: not valid base64.');
        }

        try {
            $signature = \sodium_crypto_sign_detached($checksum, $privateKey);
            return base64_encode($signature);
        } catch (\Exception $e) {
            throw new \RuntimeException('Ed25519 signing failed: ' . $e->getMessage());
        }
    }

    /**
     * Compress the full package (including checksum and signature) into gzip.
     */
    public function compress(array $package): string
    {
        $json = $this->toCanonicalJson($package);
        $compressed = gzencode($json);

        if ($compressed === false) {
            throw new \RuntimeException('Failed to compress package payload.');
        }

        return $compressed;
    }

    /**
     * Validate the exam before publishing.
     *
     * @return string[] Array of error messages. Empty if valid.
     */
    public function validate(GeneralExam $exam): array
    {
        $errors = [];

        $exam->load(['sections.questions', 'configuredParticipants']);

        // Check access code
        if (empty($exam->access_code)) {
            $errors[] = 'Exam must have an access code before publishing.';
        }

        // Check sections exist
        if ($exam->sections->isEmpty()) {
            $errors[] = 'Exam must have at least one section.';
        }

        // Check questions exist
        $totalQuestions = 0;
        foreach ($exam->sections as $section) {
            foreach ($section->questions as $question) {
                $totalQuestions++;

                // Only MCQ and True/False are supported in this release
                if (!in_array($question->type, [GeneralExamQuestion::TYPE_MULTIPLE_CHOICE, GeneralExamQuestion::TYPE_TRUE_FALSE])) {
                    $errors[] = "Question {$question->uuid} has type '{$question->type}' which is not supported. Only multiple_choice and true_false are allowed.";
                }

                // Check answer key is present
                if (empty($question->correct_answer)) {
                    $errors[] = "Question {$question->uuid} is missing a correct answer key.";
                }
            }
        }

        if ($totalQuestions === 0) {
            $errors[] = 'Exam must have at least one question.';
        }

        // Check roster for configured mode
        if ($exam->participant_mode === 'configured' && $exam->configuredParticipants->isEmpty()) {
            $errors[] = 'Exam uses configured participant mode but has no participants. Add participants before publishing.';
        }

        // Check schedule
        if (!$exam->starts_at) {
            $errors[] = 'Exam must have a start time.';
        }

        if (!$exam->ends_at) {
            $errors[] = 'Exam must have an end time.';
        }

        if ($exam->starts_at && $exam->ends_at && $exam->starts_at >= $exam->ends_at) {
            $errors[] = 'Exam end time must be after start time.';
        }

        // Check duration
        if (!$exam->duration_in_minutes || $exam->duration_in_minutes <= 0) {
            $errors[] = 'Exam must have a positive duration in minutes.';
        }

        // Check private key is configured
        if (!config('center.central_private_key')) {
            $errors[] = 'CENTRAL_PRIVATE_KEY is not configured. Run "php artisan sync:keys:generate" to generate keys.';
        }

        return $errors;
    }
}
