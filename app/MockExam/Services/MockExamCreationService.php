<?php

namespace App\MockExam\Services;

use App\MockExam\Models\MockExam;
use App\MockExam\Models\MockExamSection;
use App\MockExam\Models\MockExamSubjectExam;
use App\MockExam\Models\MockExamTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MockExamCreationService
{
    public function __construct(
        private readonly MockExamQuestionService $questionService
    ) {}

    // ─── MockExam CRUD ───────────────────────────────────────────────────────

    public function createExam(int $userId, array $payload): MockExam
    {
        return MockExam::create([
            'user_id'                     => $userId,
            'title'                       => $payload['title'],
            'description'                 => $payload['description'] ?? null,
            'instructions'                => $payload['instructions'] ?? null,
            'status'                      => $payload['status'] ?? 'draft',
            'delivery_type'               => $payload['delivery_type'] ?? 'online',
            'participant_mode'            => $payload['participant_mode'] ?? 'general',
            'configured_match_mode'       => $payload['configured_match_mode'] ?? 'any',
            'participant_required_fields' => $payload['participant_required_fields'] ?? ['name', 'email'],
            'email_verification_required' => (bool) ($payload['email_verification_required'] ?? false),
            'result_visibility'           => $payload['result_visibility'] ?? 'manual_release',
            'results_release_datetime'    => $payload['results_release_datetime'] ?? null,
            'starts_at'                   => $payload['starts_at'] ?? null,
            'ends_at'                     => $payload['ends_at'] ?? null,
            'is_randomized'               => (bool) ($payload['is_randomized'] ?? false),
            'max_attempts'                => (int) ($payload['max_attempts'] ?? 1),
        ]);
    }

    public function updateExam(MockExam $exam, array $payload): MockExam
    {
        $exam->update([
            'title'                       => $payload['title'],
            'description'                 => $payload['description'] ?? null,
            'instructions'                => $payload['instructions'] ?? null,
            'status'                      => $payload['status'] ?? 'draft',
            'delivery_type'               => $payload['delivery_type'] ?? $exam->delivery_type,
            'participant_mode'            => $payload['participant_mode'] ?? $exam->participant_mode,
            'configured_match_mode'       => $payload['configured_match_mode'] ?? 'any',
            'participant_required_fields' => $payload['participant_required_fields'] ?? $exam->participant_required_fields,
            'email_verification_required' => (bool) ($payload['email_verification_required'] ?? false),
            'result_visibility'           => $payload['result_visibility'] ?? $exam->result_visibility,
            'results_release_datetime'    => $payload['results_release_datetime'] ?? null,
            'starts_at'                   => $payload['starts_at'] ?? null,
            'ends_at'                     => $payload['ends_at'] ?? null,
            'is_randomized'               => (bool) ($payload['is_randomized'] ?? false),
            'max_attempts'                => (int) ($payload['max_attempts'] ?? 1),
        ]);

        return $exam->fresh();
    }

    // ─── Subject Exam CRUD ───────────────────────────────────────────────────

    /**
     * Create a subject exam with its sections and auto-pull questions from the bank.
     *
     * @return array{subject_exam: MockExamSubjectExam, questions_created: int, warnings: list<string>}
     */
    public function createSubjectExam(MockExam $mockExam, array $payload): array
    {
        return DB::transaction(function () use ($mockExam, $payload) {
            $order = $mockExam->subjectExams()->count() + 1;

            $subjectExam = $mockExam->subjectExams()->create([
                'mock_exam_id'        => $mockExam->id,
                'template_id'         => $payload['template_id'] ?? null, // Include template_id if provided
                'academic_group_id'   => $payload['academic_group_id'],
                'academic_level_id'   => $payload['academic_level_id'],
                'academic_subject_id' => $payload['academic_subject_id'],
                'title'               => $payload['title'] ?? null,
                'instructions'        => $payload['instructions'] ?? null,
                'order'               => $order,
                'duration_in_minutes' => $payload['duration_in_minutes'] ?? null,
                'topic_ids'           => $payload['topic_ids'] ?? [],
                'subtopic_ids'        => $payload['subtopic_ids'] ?? [],
            ]);

            $totalCreated = 0;
            $warnings     = [];

            foreach (($payload['sections'] ?? []) as $idx => $sectionData) {
                $section = $subjectExam->sections()->create([
                    'title'              => $sectionData['title'],
                    'instructions'       => $sectionData['instructions'] ?? null,
                    'order'              => $idx + 1,
                    'question_type'      => $sectionData['question_type'],
                    'question_count'     => (int) ($sectionData['question_count'] ?? 0),
                    'marks_per_question' => (float) ($sectionData['marks_per_question'] ?? 1),
                    'is_randomized'      => (bool) ($sectionData['is_randomized'] ?? false),
                ]);

                $created = $this->questionService->pullQuestionsForSection(
                    $section,
                    $payload['subtopic_ids'] ?? [],
                    $payload['topic_ids'] ?? [],
                    (int) $payload['academic_subject_id']
                );

                $totalCreated += $created;
                $requested     = (int) ($sectionData['question_count'] ?? 0);

                if ($created < $requested) {
                    $warnings[] = "Section \"{$sectionData['title']}\": requested {$requested} question(s), found {$created}.";
                }
            }

            Log::info('MockExamCreationService: subject exam created', [
                'mock_exam_id'     => $mockExam->id,
                'subject_exam_id'  => $subjectExam->id,
                'questions_created'=> $totalCreated,
                'warnings'         => $warnings,
            ]);

            return [
                'subject_exam'      => $subjectExam->fresh(['sections.questions', 'academicSubject']),
                'questions_created' => $totalCreated,
                'warnings'          => $warnings,
            ];
        });
    }

    /**
     * Replace a subject exam's sections and questions entirely.
     */
    public function updateSubjectExam(MockExamSubjectExam $subjectExam, array $payload): array
    {
        return DB::transaction(function () use ($subjectExam, $payload) {
            $subjectExam->update([
                'academic_group_id'   => $payload['academic_group_id'],
                'academic_level_id'   => $payload['academic_level_id'],
                'academic_subject_id' => $payload['academic_subject_id'],
                'title'               => $payload['title'] ?? null,
                'instructions'        => $payload['instructions'] ?? null,
                'duration_in_minutes' => $payload['duration_in_minutes'] ?? null,
                'topic_ids'           => $payload['topic_ids'] ?? [],
                'subtopic_ids'        => $payload['subtopic_ids'] ?? [],
                'template_id'         => $payload['template_id'] ?? null, // Update template_id if provided
            ]);

            // Drop and rebuild sections + questions
            $subjectExam->sections()->each(fn ($s) => $s->questions()->delete());
            $subjectExam->sections()->delete();

            $totalCreated = 0;
            $warnings     = [];

            foreach (($payload['sections'] ?? []) as $idx => $sectionData) {
                $section = $subjectExam->sections()->create([
                    'title'              => $sectionData['title'],
                    'instructions'       => $sectionData['instructions'] ?? null,
                    'order'              => $idx + 1,
                    'question_type'      => $sectionData['question_type'],
                    'question_count'     => (int) ($sectionData['question_count'] ?? 0),
                    'marks_per_question' => (float) ($sectionData['marks_per_question'] ?? 1),
                    'is_randomized'      => (bool) ($sectionData['is_randomized'] ?? false),
                ]);

                $created = $this->questionService->pullQuestionsForSection(
                    $section,
                    $payload['subtopic_ids'] ?? [],
                    $payload['topic_ids'] ?? [],
                    (int) $payload['academic_subject_id']
                );

                $totalCreated += $created;
                $requested     = (int) ($sectionData['question_count'] ?? 0);

                if ($created < $requested) {
                    $warnings[] = "Section \"{$sectionData['title']}\": requested {$requested} question(s), found {$created}.";
                }
            }

            Log::info('MockExamCreationService: subject exam updated', [
                'subject_exam_id'    => $subjectExam->id,
                'questions_created'  => $totalCreated,
                'warnings'           => $warnings,
            ]);

            return [
                'subject_exam'      => $subjectExam->fresh(['sections.questions', 'academicSubject']),
                'questions_created' => $totalCreated,
                'warnings'          => $warnings,
            ];
        });
    }

    /**
     * Create a subject exam from a predefined template.
     *
     * @param MockExam $mockExam The parent mock exam
     * @param MockExamTemplate $template The template to use
     * @param array $overrides Optional overrides for title, instructions, duration, etc.
     * @return array{subject_exam: MockExamSubjectExam, questions_created: int, warnings: list<string>}
     */
    public function createSubjectExamFromTemplate(
        MockExam $mockExam,
        MockExamTemplate $template,
        array $overrides = []
    ): array {
        // Build payload from template with optional overrides
        $payload = $template->toSubjectExamPayload();
        
        // Apply overrides
        if (isset($overrides['title'])) {
            $payload['title'] = $overrides['title'];
        }
        if (isset($overrides['instructions'])) {
            $payload['instructions'] = $overrides['instructions'];
        }
        if (isset($overrides['duration_in_minutes'])) {
            $payload['duration_in_minutes'] = $overrides['duration_in_minutes'];
        }
        if (isset($overrides['topic_ids'])) {
            $payload['topic_ids'] = $overrides['topic_ids'];
        }
        if (isset($overrides['subtopic_ids'])) {
            $payload['subtopic_ids'] = $overrides['subtopic_ids'];
        }
        
        // Add template_id to the payload
        $payload['template_id'] = $template->id;

        // Delegate to existing createSubjectExam method
        return $this->createSubjectExam($mockExam, $payload);
    }
}