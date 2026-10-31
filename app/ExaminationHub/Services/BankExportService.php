<?php

namespace App\ExaminationHub\Services;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Models\MultipleChoiceQuestion;
use Illuminate\Support\Facades\DB;

/**
 * BankExportService — exports academic structure and question bank as a signed bundle.
 *
 * This service exports selected groups, levels, or subjects down to MCQs
 * as one signed bundle. It maps or drops user FKs and includes soft-deleted flags.
 */
class BankExportService
{
    /**
     * Export academic structure by type and ID.
     *
     * @param string $type One of: group, level, subject
     * @param int $id The ID of the item to export
     * @return string Path to the temporary export file
     */
    public function export(string $type, int $id): string
    {
        $payload = match ($type) {
            'group' => $this->exportGroups([$id]),
            'level' => $this->exportLevels([$id]),
            'subject' => $this->exportSubjects([$id]),
            default => throw new \InvalidArgumentException("Unknown export type: {$type}"),
        };

        $tempFile = tempnam(sys_get_temp_dir(), 'bank-export-') . '.json';
        file_put_contents($tempFile, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $tempFile;
    }

    /**
     * Export academic structure starting from groups.
     *
     * @param int[] $groupIds Array of group IDs to export
     * @return array The export payload
     */
    public function exportGroups(array $groupIds): array
    {
        $groups = AcademicGroup::with([
            'academicLevels.academicSubjects.topics.subtopics.multipleChoiceQuestions',
            'academicLevels.academicSubjects.topics.multipleChoiceQuestions',
        ])->whereIn('id', $groupIds)->get();

        return $this->buildPayload($groups);
    }

    /**
     * Export academic structure starting from levels.
     *
     * @param int[] $levelIds Array of level IDs to export
     * @return array The export payload
     */
    public function exportLevels(array $levelIds): array
    {
        $levels = AcademicLevel::with([
            'academicSubjects.topics.subtopics.multipleChoiceQuestions',
            'academicSubjects.topics.multipleChoiceQuestions',
        ])->whereIn('id', $levelIds)->get();

        $groups = collect();
        foreach ($levels as $level) {
            $group = $level->academicGroup;
            if ($group && !$groups->contains('id', $group->id)) {
                $groups->push($group);
            }
        }

        return $this->buildPayload($groups, null, $levels);
    }

    /**
     * Export academic structure starting from subjects.
     *
     * @param int[] $subjectIds Array of subject IDs to export
     * @return array The export payload
     */
    public function exportSubjects(array $subjectIds): array
    {
        $subjects = AcademicSubject::with([
            'topics.subtopics.multipleChoiceQuestions',
            'topics.multipleChoiceQuestions',
        ])->whereIn('id', $subjectIds)->get();

        $groups = collect();
        foreach ($subjects as $subject) {
            $level = $subject->academicLevel;
            if ($level) {
                $group = $level->academicGroup;
                if ($group && !$groups->contains('id', $group->id)) {
                    $groups->push($group);
                }
            }
        }

        return $this->buildPayload($groups, $subjects);
    }

    /**
     * Build the export payload.
     *
     * @param \Illuminate\Support\Collection $groups Collection of AcademicGroup models
     * @param \Illuminate\Support\Collection|null $subjects Optional collection of AcademicSubject models
     * @param \Illuminate\Support\Collection|null $levels Optional collection of AcademicLevel models
     * @return array
     */
    protected function buildPayload($groups, $subjects = null, $levels = null): array
    {
        $payload = [
            'version' => '1.0',
            'exported_at' => now()->toIso8601String(),
            'groups' => [],
            'levels' => [],
            'subjects' => [],
            'topics' => [],
            'subtopics' => [],
            'questions' => [],
        ];

        // Track exported UUIDs to avoid duplicates
        $exportedLevelUuids = [];
        $exportedSubjectUuids = [];
        $exportedTopicUuids = [];
        $exportedSubtopicUuids = [];
        $exportedQuestionUuids = [];

        // Export groups
        foreach ($groups as $group) {
            $payload['groups'][] = [
                'uuid' => $group->uuid,
                'name' => $group->name,
                'tag' => $group->tag,
                'deleted_at' => $group->deleted_at?->toIso8601String(),
            ];

            // Export levels
            foreach ($group->academicLevels as $level) {
                $exportedLevelUuids[] = $level->uuid;
                $payload['levels'][] = [
                    'uuid' => $level->uuid,
                    'name' => $level->name,
                    'label' => $level->label,
                    'academic_group_uuid' => $group->uuid,
                    'deleted_at' => $level->deleted_at?->toIso8601String(),
                ];

                // Export subjects
                foreach ($level->academicSubjects as $subject) {
                    $exportedSubjectUuids[] = $subject->uuid;
                    $subjectData = [
                        'uuid' => $subject->uuid,
                        'name' => $subject->name,
                        'code' => $subject->code,
                        'academic_level_uuid' => $level->uuid,
                        'description' => $subject->description,
                        'deleted_at' => $subject->deleted_at?->toIso8601String(),
                    ];
                    $payload['subjects'][] = $subjectData;

                    // Export topics and questions
                    $this->exportTopicsForSubject($subject, $payload, $exportedTopicUuids, $exportedSubtopicUuids, $exportedQuestionUuids);
                }
            }
        }

        // If levels were passed directly (exportLevels)
        if ($levels) {
            foreach ($levels as $level) {
                if (!in_array($level->uuid, $exportedLevelUuids)) {
                    $exportedLevelUuids[] = $level->uuid;
                    // Add the parent group if not already exported
                    $group = $level->academicGroup;
                    if ($group && !collect($payload['groups'])->contains('uuid', $group->uuid)) {
                        $payload['groups'][] = [
                            'uuid' => $group->uuid,
                            'name' => $group->name,
                            'tag' => $group->tag,
                            'deleted_at' => $group->deleted_at?->toIso8601String(),
                        ];
                    }
                    $payload['levels'][] = [
                        'uuid' => $level->uuid,
                        'name' => $level->name,
                        'label' => $level->label,
                        'academic_group_uuid' => $group->uuid ?? null,
                        'deleted_at' => $level->deleted_at?->toIso8601String(),
                    ];

                    foreach ($level->academicSubjects as $subject) {
                        if (!in_array($subject->uuid, $exportedSubjectUuids)) {
                            $exportedSubjectUuids[] = $subject->uuid;
                            $payload['subjects'][] = [
                                'uuid' => $subject->uuid,
                                'name' => $subject->name,
                                'code' => $subject->code,
                                'academic_level_uuid' => $level->uuid,
                                'description' => $subject->description,
                                'deleted_at' => $subject->deleted_at?->toIso8601String(),
                            ];
                            $this->exportTopicsForSubject($subject, $payload, $exportedTopicUuids, $exportedSubtopicUuids, $exportedQuestionUuids);
                        }
                    }
                }
            }
        }

        // If subjects were passed directly (not through groups)
        if ($subjects) {
            foreach ($subjects as $subject) {
                if (!in_array($subject->uuid, $exportedSubjectUuids)) {
                    $exportedSubjectUuids[] = $subject->uuid;
                    // Add parent level and group if missing
                    $level = $subject->academicLevel;
                    if ($level && !in_array($level->uuid, $exportedLevelUuids)) {
                        $exportedLevelUuids[] = $level->uuid;
                        $group = $level->academicGroup;
                        if ($group && !collect($payload['groups'])->contains('uuid', $group->uuid)) {
                            $payload['groups'][] = [
                                'uuid' => $group->uuid,
                                'name' => $group->name,
                                'tag' => $group->tag,
                                'deleted_at' => $group->deleted_at?->toIso8601String(),
                            ];
                        }
                        $payload['levels'][] = [
                            'uuid' => $level->uuid,
                            'name' => $level->name,
                            'label' => $level->label,
                            'academic_group_uuid' => $group->uuid ?? null,
                            'deleted_at' => $level->deleted_at?->toIso8601String(),
                        ];
                    }
                    $payload['subjects'][] = [
                        'uuid' => $subject->uuid,
                        'name' => $subject->name,
                        'code' => $subject->code,
                        'academic_level_uuid' => $level->uuid ?? null,
                        'description' => $subject->description,
                        'deleted_at' => $subject->deleted_at?->toIso8601String(),
                    ];
                    $this->exportTopicsForSubject($subject, $payload, $exportedTopicUuids, $exportedSubtopicUuids, $exportedQuestionUuids);
                }
            }
        }

        // Sign the payload
        $canonicalJson = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION);
        $checksum = hash('sha256', $canonicalJson);
        $signature = $this->sign($checksum);

        $payload['checksum'] = $checksum;
        $payload['signature'] = $signature;

        return $payload;
    }

    /**
     * Export topics, subtopics, and questions for a subject.
     */
    protected function exportTopicsForSubject(
        AcademicSubject $subject,
        array &$payload,
        array &$exportedTopicUuids,
        array &$exportedSubtopicUuids,
        array &$exportedQuestionUuids
    ): void {
        foreach ($subject->topics as $topic) {
            if (in_array($topic->uuid, $exportedTopicUuids)) {
                continue;
            }
            $exportedTopicUuids[] = $topic->uuid;

            $payload['topics'][] = [
                'uuid' => $topic->uuid,
                'name' => $topic->name,
                'academic_subject_uuid' => $subject->uuid,
                'description' => $topic->description,
                'deleted_at' => $topic->deleted_at?->toIso8601String(),
            ];

            // Export MCQs for this topic
            foreach ($topic->multipleChoiceQuestions as $mcq) {
                if (!in_array($mcq->uuid, $exportedQuestionUuids)) {
                    $exportedQuestionUuids[] = $mcq->uuid;
                    $payload['questions'][] = $this->exportQuestion($mcq, $topic->uuid, null);
                }
            }

            // Export subtopics
            foreach ($topic->subtopics as $subtopic) {
                if (in_array($subtopic->uuid, $exportedSubtopicUuids)) {
                    continue;
                }
                $exportedSubtopicUuids[] = $subtopic->uuid;

                $payload['subtopics'][] = [
                    'uuid' => $subtopic->uuid,
                    'name' => $subtopic->name,
                    'academic_topic_uuid' => $topic->uuid,
                    'slug' => $subtopic->slug,
                    'description' => $subtopic->description,
                ];

                // Export MCQs for this subtopic
                foreach ($subtopic->multipleChoiceQuestions as $mcq) {
                    if (!in_array($mcq->uuid, $exportedQuestionUuids)) {
                        $exportedQuestionUuids[] = $mcq->uuid;
                        $payload['questions'][] = $this->exportQuestion($mcq, $topic->uuid, $subtopic->uuid);
                    }
                }
            }
        }
    }

    /**
     * Export a single MCQ.
     */
    protected function exportQuestion(MultipleChoiceQuestion $mcq, ?string $topicUuid, ?string $subtopicUuid): array
    {
        return [
            'uuid' => $mcq->uuid,
            'question' => $mcq->question instanceof \App\Support\Mark
                ? $mcq->question->toArray()
                : ['up' => $mcq->question, 'down' => null],
            'option_a' => $this->markToArray($mcq->option_a),
            'option_b' => $this->markToArray($mcq->option_b),
            'option_c' => $this->markToArray($mcq->option_c),
            'option_d' => $this->markToArray($mcq->option_d),
            'option_e' => $this->markToArray($mcq->option_e),
            'answer' => $mcq->answer,
            'score' => $mcq->score,
            'difficulty_level' => $mcq->difficulty_level,
            'academic_topic_uuid' => $topicUuid,
            'academic_subtopic_uuid' => $subtopicUuid,
            'deleted_at' => $mcq->deleted_at?->toIso8601String(),
        ];
    }

    /**
     * Convert a Mark object to array.
     */
    protected function markToArray($value): array
    {
        if ($value instanceof \App\Support\Mark) {
            return $value->toArray();
        }
        if (is_string($value)) {
            return \App\Support\Mark::fromString($value)->toArray();
        }
        return ['up' => $value ?? null, 'down' => null];
    }

    /**
     * Sign the checksum using Ed25519.
     */
    protected function sign(string $checksum): string
    {
        $privateKeyBase64 = config('center.central_private_key');
        if (!$privateKeyBase64) {
            return '';
        }

        $privateKey = base64_decode($privateKeyBase64);
        $signature = sodium_crypto_sign_detached($checksum, $privateKey);
        return base64_encode($signature);
    }
}
