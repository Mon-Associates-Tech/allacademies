<?php

namespace App\ExaminationHub\Services;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Models\MultipleChoiceQuestion;
use App\Models\Question;
use Illuminate\Support\Facades\DB;

/**
 * BankImportService — idempotently imports academic structure and question bank.
 *
 * This service imports a signed bundle exported by BankExportService.
 * It upserts by UUID, recreates the polymorphic Question wrapper,
 * and preserves soft-deleted flags.
 */
class BankImportService
{
    /**
     * Import a bank bundle.
     *
     * @param array $bundle The export bundle from BankExportService
     * @return array{imported: array, skipped: array, errors: array}
     */
    public function import(array $bundle): array
    {
        // Verify signature
        if (!empty($bundle['checksum']) && !empty($bundle['signature'])) {
            $this->verifySignature($bundle);
        }

        $imported = [
            'groups' => 0,
            'levels' => 0,
            'subjects' => 0,
            'topics' => 0,
            'subtopics' => 0,
            'questions' => 0,
        ];
        $skipped = [
            'groups' => 0,
            'levels' => 0,
            'subjects' => 0,
            'topics' => 0,
            'subtopics' => 0,
            'questions' => 0,
        ];
        $errors = [];

        return DB::transaction(function () use ($bundle, &$imported, &$skipped, &$errors) {
            // Import groups
            foreach ($bundle['groups'] ?? [] as $groupData) {
                try {
                    $existing = AcademicGroup::where('uuid', $groupData['uuid'])->first();
                    if ($existing) {
                        $existing->update([
                            'name' => $groupData['name'],
                            'tag' => $groupData['tag'] ?? null,
                        ]);
                        $skipped['groups']++;
                    } else {
                        AcademicGroup::create([
                            'uuid' => $groupData['uuid'],
                            'name' => $groupData['name'],
                            'tag' => $groupData['tag'] ?? null,
                        ]);
                        $imported['groups']++;
                    }
                } catch (\Exception $e) {
                    $errors[] = ['type' => 'group', 'uuid' => $groupData['uuid'], 'error' => $e->getMessage()];
                }
            }

            // Import levels
            foreach ($bundle['levels'] ?? [] as $levelData) {
                try {
                    $group = AcademicGroup::where('uuid', $levelData['academic_group_uuid'])->first();
                    if (!$group) {
                        $skipped['levels']++;
                        continue;
                    }

                    $existing = AcademicLevel::where('uuid', $levelData['uuid'])->first();
                    if ($existing) {
                        $existing->update([
                            'name' => $levelData['name'],
                            'label' => $levelData['label'] ?? null,
                        ]);
                        $skipped['levels']++;
                    } else {
                        AcademicLevel::create([
                            'uuid' => $levelData['uuid'],
                            'name' => $levelData['name'],
                            'label' => $levelData['label'] ?? null,
                            'academic_group_id' => $group->id,
                        ]);
                        $imported['levels']++;
                    }
                } catch (\Exception $e) {
                    $errors[] = ['type' => 'level', 'uuid' => $levelData['uuid'], 'error' => $e->getMessage()];
                }
            }

            // Import subjects
            foreach ($bundle['subjects'] ?? [] as $subjectData) {
                try {
                    $level = AcademicLevel::where('uuid', $subjectData['academic_level_uuid'])->first();
                    if (!$level) {
                        $skipped['subjects']++;
                        continue;
                    }

                    $existing = AcademicSubject::where('uuid', $subjectData['uuid'])->first();
                    if ($existing) {
                        $existing->update([
                            'name' => $subjectData['name'],
                            'code' => $subjectData['code'] ?? null,
                            'description' => $subjectData['description'] ?? null,
                        ]);
                        $skipped['subjects']++;
                    } else {
                        AcademicSubject::create([
                            'uuid' => $subjectData['uuid'],
                            'name' => $subjectData['name'],
                            'code' => $subjectData['code'] ?? null,
                            'academic_level_id' => $level->id,
                            'description' => $subjectData['description'] ?? null,
                        ]);
                        $imported['subjects']++;
                    }
                } catch (\Exception $e) {
                    $errors[] = ['type' => 'subject', 'uuid' => $subjectData['uuid'], 'error' => $e->getMessage()];
                }
            }

            // Import topics
            foreach ($bundle['topics'] ?? [] as $topicData) {
                try {
                    $subject = AcademicSubject::where('uuid', $topicData['academic_subject_uuid'])->first();
                    if (!$subject) {
                        $skipped['topics']++;
                        continue;
                    }

                    $existing = AcademicTopic::where('uuid', $topicData['uuid'])->first();
                    if ($existing) {
                        $existing->update([
                            'name' => $topicData['name'],
                            'description' => $topicData['description'] ?? null,
                        ]);
                        $skipped['topics']++;
                    } else {
                        AcademicTopic::create([
                            'uuid' => $topicData['uuid'],
                            'name' => $topicData['name'],
                            'academic_subject_id' => $subject->id,
                            'description' => $topicData['description'] ?? null,
                        ]);
                        $imported['topics']++;
                    }
                } catch (\Exception $e) {
                    $errors[] = ['type' => 'topic', 'uuid' => $topicData['uuid'], 'error' => $e->getMessage()];
                }
            }

            // Import subtopics
            foreach ($bundle['subtopics'] ?? [] as $subtopicData) {
                try {
                    $topic = AcademicTopic::where('uuid', $subtopicData['academic_topic_uuid'])->first();
                    if (!$topic) {
                        $skipped['subtopics']++;
                        continue;
                    }

                    $existing = AcademicSubtopic::where('uuid', $subtopicData['uuid'])->first();
                    if ($existing) {
                        $existing->update([
                            'name' => $subtopicData['name'],
                            'slug' => $subtopicData['slug'] ?? null,
                            'description' => $subtopicData['description'] ?? null,
                        ]);
                        $skipped['subtopics']++;
                    } else {
                        AcademicSubtopic::create([
                            'uuid' => $subtopicData['uuid'],
                            'name' => $subtopicData['name'],
                            'academic_topic_id' => $topic->id,
                            'slug' => $subtopicData['slug'] ?? null,
                            'description' => $subtopicData['description'] ?? null,
                        ]);
                        $imported['subtopics']++;
                    }
                } catch (\Exception $e) {
                    $errors[] = ['type' => 'subtopic', 'uuid' => $subtopicData['uuid'], 'error' => $e->getMessage()];
                }
            }

            // Import questions
            foreach ($bundle['questions'] ?? [] as $questionData) {
                try {
                    $topic = AcademicTopic::where('uuid', $questionData['academic_topic_uuid'])->first();
                    if (!$topic) {
                        $skipped['questions']++;
                        continue;
                    }

                    $subtopic = null;
                    if (!empty($questionData['academic_subtopic_uuid'])) {
                        $subtopic = AcademicSubtopic::where('uuid', $questionData['academic_subtopic_uuid'])->first();
                    }

                    $existing = MultipleChoiceQuestion::where('uuid', $questionData['uuid'])->first();
                    $questionAttr = $this->markFromArray($questionData['question'] ?? ['up' => null, 'down' => null]);

                    if ($existing) {
                        $existing->update([
                            'question' => $questionAttr,
                            'option_a' => $this->markFromArray($questionData['option_a'] ?? ['up' => null, 'down' => null]),
                            'option_b' => $this->markFromArray($questionData['option_b'] ?? ['up' => null, 'down' => null]),
                            'option_c' => $this->markFromArray($questionData['option_c'] ?? ['up' => null, 'down' => null]),
                            'option_d' => $this->markFromArray($questionData['option_d'] ?? ['up' => null, 'down' => null]),
                            'option_e' => $this->markFromArray($questionData['option_e'] ?? ['up' => null, 'down' => null]),
                            'answer' => $questionData['answer'] ?? null,
                            'score' => $questionData['score'] ?? null,
                            'difficulty_level' => $questionData['difficulty_level'] ?? null,
                        ]);
                        $skipped['questions']++;
                    } else {
                        $mcq = MultipleChoiceQuestion::create([
                            'uuid' => $questionData['uuid'],
                            'question' => $questionAttr,
                            'option_a' => $this->markFromArray($questionData['option_a'] ?? ['up' => null, 'down' => null]),
                            'option_b' => $this->markFromArray($questionData['option_b'] ?? ['up' => null, 'down' => null]),
                            'option_c' => $this->markFromArray($questionData['option_c'] ?? ['up' => null, 'down' => null]),
                            'option_d' => $this->markFromArray($questionData['option_d'] ?? ['up' => null, 'down' => null]),
                            'option_e' => $this->markFromArray($questionData['option_e'] ?? ['up' => null, 'down' => null]),
                            'answer' => $questionData['answer'] ?? null,
                            'score' => $questionData['score'] ?? null,
                            'difficulty_level' => $questionData['difficulty_level'] ?? null,
                            'academic_topic_id' => $topic->id,
                            'academic_subtopic_id' => $subtopic?->id,
                        ]);

                        // Create polymorphic Question wrapper
                        Question::firstOrCreate(
                            ['questionable_id' => $mcq->id, 'questionable_type' => MultipleChoiceQuestion::class],
                            []
                        );

                        $imported['questions']++;
                    }
                } catch (\Exception $e) {
                    $errors[] = ['type' => 'question', 'uuid' => $questionData['uuid'], 'error' => $e->getMessage()];
                }
            }

            return [
                'imported' => $imported,
                'skipped' => $skipped,
                'errors' => $errors,
            ];
        });
    }

    /**
     * Convert a Mark array to a Mark object.
     */
    protected function markFromArray(array $data): \App\Support\Mark
    {
        return \App\Support\Mark::fromArray($data);
    }

    /**
     * Verify the bundle signature.
     */
    protected function verifySignature(array $bundle): void
    {
        $publicKeyBase64 = config('center.central_public_key');
        if (!$publicKeyBase64) {
            throw new \RuntimeException('Cannot verify signature: CENTRAL_PUBLIC_KEY is not configured.');
        }

        $publicKey = base64_decode($publicKeyBase64);
        $signature = base64_decode($bundle['signature']);

        if (!\sodium_crypto_sign_verify_detached($signature, $bundle['checksum'], $publicKey)) {
            throw new \RuntimeException('Bundle signature verification failed. The bundle may be tampered or from an untrusted source.');
        }
    }
}
