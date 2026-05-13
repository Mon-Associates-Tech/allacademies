<?php

namespace App\ExaminationHub\Services;

use App\ExaminationHub\Contracts\ExamCreationServiceInterface;
use App\ExaminationHub\Models\GeneralExam;
use Illuminate\Support\Facades\DB;

class ExamCreationService implements ExamCreationServiceInterface
{
    public function createExam(int $userId, array $payload): GeneralExam
    {
        return DB::transaction(function () use ($userId, $payload) {
            $exam = GeneralExam::create([
                'title' => $payload['title'],
                'description' => $payload['description'] ?? null,
                'instructions' => $payload['instructions'] ?? null,
                'user_id' => $userId,
                'teacher_id' => optional(auth()->user()?->teacher)->id,
                'academic_subject_id' => $payload['academic_subject_id'] ?? null,
                'type' => 'examination',
                'delivery_type' => 'online',
                'duration_in_minutes' => $payload['duration_in_minutes'] ?? null,
                'starts_at' => $payload['starts_at'] ?? null,
                'ends_at' => $payload['ends_at'] ?? null,
                'status' => $payload['status'] ?? 'draft',
                'hardened_mode' => $payload['hardened_mode'] ?? false,
                'participant_mode' => $payload['participant_mode'],
                'participant_required_fields' => $payload['participant_required_fields'] ?? [],
                'configured_match_mode' => $payload['configured_match_mode'] ?? 'any',
                'max_attempts' => 1,
                'result_visibility' => 'manual_release',
            ]);

            foreach (($payload['sections'] ?? []) as $index => $section) {
                $exam->sections()->create([
                    'title' => $section['title'],
                    'instructions' => $section['instructions'] ?? null,
                    'description' => $section['description'] ?? null,
                    'order' => $index + 1,
                    'time_limit_minutes' => $section['time_limit_minutes'] ?? null,
                    'source_type' => $section['source_type'],
                    'question_type' => $section['question_type'],
                    'question_count' => (int) ($section['question_count'] ?? 0),
                    'academic_group_id' => $section['academic_group_id'] ?? null,
                    'academic_level_id' => $section['academic_level_id'] ?? null,
                    'academic_subject_id' => $section['academic_subject_id'] ?? null,
                    'topic_ids' => $section['topic_ids'] ?? [],
                    'subtopic_ids' => $section['subtopic_ids'] ?? [],
                    'is_randomized' => (bool) ($section['is_randomized'] ?? false),
                ]);
            }

            return $exam->fresh(['sections']);
        });
    }

    public function updateExam(GeneralExam $exam, int $userId, array $payload): GeneralExam
    {
        return DB::transaction(function () use ($exam, $userId, $payload) {
            $exam->update([
                'title' => $payload['title'],
                'description' => $payload['description'] ?? null,
                'instructions' => $payload['instructions'] ?? null,
                'user_id' => $userId,
                'academic_subject_id' => $payload['academic_subject_id'] ?? null,
                'duration_in_minutes' => $payload['duration_in_minutes'] ?? null,
                'starts_at' => $payload['starts_at'] ?? null,
                'ends_at' => $payload['ends_at'] ?? null,
                'status' => $payload['status'] ?? 'draft',
                'hardened_mode' => $payload['hardened_mode'] ?? false,
                'participant_mode' => $payload['participant_mode'],
                'participant_required_fields' => $payload['participant_required_fields'] ?? [],
                'configured_match_mode' => $payload['configured_match_mode'] ?? 'any',
            ]);

            $exam->sections()->delete();
            foreach (($payload['sections'] ?? []) as $index => $section) {
                $exam->sections()->create([
                    'title' => $section['title'],
                    'instructions' => $section['instructions'] ?? null,
                    'description' => $section['description'] ?? null,
                    'order' => $index + 1,
                    'time_limit_minutes' => $section['time_limit_minutes'] ?? null,
                    'source_type' => $section['source_type'],
                    'question_type' => $section['question_type'],
                    'question_count' => (int) ($section['question_count'] ?? 0),
                    'academic_group_id' => $section['academic_group_id'] ?? null,
                    'academic_level_id' => $section['academic_level_id'] ?? null,
                    'academic_subject_id' => $section['academic_subject_id'] ?? null,
                    'topic_ids' => $section['topic_ids'] ?? [],
                    'subtopic_ids' => $section['subtopic_ids'] ?? [],
                    'is_randomized' => (bool) ($section['is_randomized'] ?? false),
                ]);
            }

            return $exam->fresh(['sections']);
        });
    }
}
