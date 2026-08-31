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
                'is_randomized' => (bool) ($payload['is_randomized'] ?? false),
                'starts_at' => $payload['starts_at'] ?? null,
                'ends_at' => $payload['ends_at'] ?? null,
                'status' => $payload['status'] ?? 'draft',
                'hardened_mode' => $payload['hardened_mode'] ?? false,
                'participant_mode' => $payload['participant_mode'],
                'participant_required_fields' => $payload['participant_required_fields'] ?? [],
                'configured_match_mode' => $payload['configured_match_mode'] ?? 'any',
                'participant_group_id' => $payload['participant_group_id'] ?? null,
                'max_attempts' => 1,
                'result_visibility' => 'manual_release',
            ]);

            // Filter out empty/blank sections from payload before creating
            $validSections = collect($payload['sections'] ?? [])
                ->filter(function ($section) {
                    // Require at least a title for new sections
                    return !empty($section['title']);
                })
                ->values()
                ->toArray();

            foreach ($validSections as $index => $section) {
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
                'is_randomized' => (bool) ($payload['is_randomized'] ?? false),
                'starts_at' => $payload['starts_at'] ?? null,
                'ends_at' => $payload['ends_at'] ?? null,
                'status' => $payload['status'] ?? 'draft',
                'hardened_mode' => $payload['hardened_mode'] ?? false,
                'participant_mode' => $payload['participant_mode'],
                'participant_required_fields' => $payload['participant_required_fields'] ?? [],
                'configured_match_mode' => $payload['configured_match_mode'] ?? 'any',
                'participant_group_id' => $payload['participant_group_id'] ?? null,
            ]);

            // Diff sections: update existing, create new, delete removed
            $incomingSectionIds = collect($payload['sections'] ?? [])
                ->pluck('id')
                ->filter()
                ->values();

            // Delete sections that are no longer in the payload
            // Only delete sections that have NO submissions with responses for their questions
            $sectionsToDelete = $exam->sections()
                ->whereNotIn('id', $incomingSectionIds)
                ->get();

            foreach ($sectionsToDelete as $section) {
                // Check if any submission has responses for questions in this section
                $questionIds = $section->questions->pluck('id')->toArray();
                
                if (empty($questionIds)) {
                    // No questions in this section, safe to delete
                    $section->delete();
                    continue;
                }

                // Check if any submission has responses for these question IDs
                $hasResponses = \App\ExaminationHub\Models\GeneralExamSubmission::where('general_exam_id', $exam->id)
                    ->whereNotNull('responses')
                    ->get()
                    ->contains(function ($submission) use ($questionIds) {
                        $responses = $submission->responses ?? [];
                        return !empty(array_intersect(array_keys($responses), $questionIds));
                    });

                // Only delete if no submissions have responses for these questions
                if (!$hasResponses) {
                    $section->delete();
                }
            }

            // Filter out empty/blank sections from payload before processing
            $originalCount = count($payload['sections'] ?? []);
            $validSections = collect($payload['sections'] ?? [])
                ->filter(function ($sectionData, $index) {
                    // Keep sections that have an ID (existing sections)
                    if (!empty($sectionData['id'])) {
                        return true;
                    }
                    
                    // For new sections, require at least a title
                    // Also filter out sections that are just blank/default templates
                    if (empty($sectionData['title'])) {
                        \Illuminate\Support\Facades\Log::warning('Filtering out section without title', ['index' => $index, 'data' => $sectionData]);
                        return false;
                    }
                    
                    return true;
                })
                ->values()
                ->toArray();
            
            if (count($validSections) !== $originalCount) {
                \Illuminate\Support\Facades\Log::info('Filtered sections', [
                    'original_count' => $originalCount,
                    'filtered_count' => count($validSections),
                    'removed' => $originalCount - count($validSections),
                ]);
            }

            foreach ($validSections as $index => $sectionData) {
                \Illuminate\Support\Facades\Log::debug('Processing section', [
                    'index' => $index,
                    'has_id' => !empty($sectionData['id']),
                    'id' => $sectionData['id'] ?? null,
                    'title' => $sectionData['title'],
                ]);

                $attributes = [
                    'title' => $sectionData['title'],
                    'instructions' => $sectionData['instructions'] ?? null,
                    'description' => $sectionData['description'] ?? null,
                    'order' => $index + 1,
                    'time_limit_minutes' => $sectionData['time_limit_minutes'] ?? null,
                    'source_type' => $sectionData['source_type'],
                    'question_type' => $sectionData['question_type'],
                    'question_count' => (int) ($sectionData['question_count'] ?? 0),
                    'academic_group_id' => $sectionData['academic_group_id'] ?? null,
                    'academic_level_id' => $sectionData['academic_level_id'] ?? null,
                    'academic_subject_id' => $sectionData['academic_subject_id'] ?? null,
                    'topic_ids' => $sectionData['topic_ids'] ?? [],
                    'subtopic_ids' => $sectionData['subtopic_ids'] ?? [],
                    'is_randomized' => (bool) ($sectionData['is_randomized'] ?? false),
                ];

                if (! empty($sectionData['id'])) {
                    \Illuminate\Support\Facades\Log::info('Updating existing section', ['section_id' => $sectionData['id']]);
                    $exam->sections()->where('id', $sectionData['id'])->update($attributes);
                } else {
                    \Illuminate\Support\Facades\Log::info('Creating new section', ['title' => $sectionData['title']]);
                    $exam->sections()->create($attributes);
                }
            }

            return $exam->fresh(['sections']);
        });
    }
}
