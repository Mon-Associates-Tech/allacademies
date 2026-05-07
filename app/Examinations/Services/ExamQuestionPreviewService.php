<?php

namespace App\Examinations\Services;

use App\Models\EssayQuestion;
use App\Models\MultipleChoiceQuestion;
use App\Models\TrueOrFalseQuestion;
use App\Support\Mark;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ExamQuestionPreviewService
{
    public function __construct(
        private readonly ExamQuestionGenerationService $generationService
    ) {}

    public function generateForSections(array $sections, bool $hardenedMode = false): array
    {
        return collect($sections)->map(function (array $section, int $index) use ($hardenedMode) {
            if ($hardenedMode) {
                return $this->getPlaceholderForHardenedMode($section);
            }

            return match ($section['source_type'] ?? null) {
                'database' => $this->generateFromDatabase($section),
                'ai' => $this->generateFromAi($section),
                'mixed' => $this->generateMixed($section),
                'manual' => $this->getManualPlaceholder($section),
                default => [],
            };
        })->all();
    }

    private function generateFromDatabase(array $section): array
    {
        $count = (int) ($section['question_count'] ?? 0);
        $subjectId = $section['academic_subject_id'] ?? null;
        $topicIds = collect($section['topic_ids'] ?? [])->filter()->map(fn ($v) => (int) $v)->all();
        $subtopicIds = collect($section['subtopic_ids'] ?? [])->filter()->map(fn ($v) => (int) $v)->all();
        $questionType = $section['question_type'] ?? 'multiple_choice';

        if ($count <= 0 || ! $subjectId) {
            return [];
        }

        if ($questionType === 'mixed') {
            $third = max(1, (int) floor($count / 3));
            $mcq = $this->fetchMcq($subjectId, $topicIds, $subtopicIds, $third);
            $tof = $this->fetchTof($subjectId, $topicIds, $subtopicIds, $third);
            $essay = $this->fetchEssay($subjectId, $topicIds, $subtopicIds, $count - ($third * 2));

            return collect([$mcq, $tof, $essay])->flatten(1)->take($count)->values()->all();
        }

        return match ($questionType) {
            'multiple_choice' => $this->fetchMcq($subjectId, $topicIds, $subtopicIds, $count),
            'true_false' => $this->fetchTof($subjectId, $topicIds, $subtopicIds, $count),
            'short_answer', 'essay' => $this->fetchEssay($subjectId, $topicIds, $subtopicIds, $count),
            default => [],
        };
    }

    private function generateFromAi(array $section): array
    {
        $document = $section['document'] ?? null;
        $questionType = $section['question_type'] ?? 'multiple_choice';
        $count = (int) ($section['question_count'] ?? 0);

        if (!$document instanceof UploadedFile || $count <= 0) {
            return [];
        }

        try {
            return $this->generationService->generateFromDocument($document, $questionType, $count);
        } catch (\Exception $e) {
            Log::error('AI generation failed in preview', ['error' => $e->getMessage()]);
            return [];
        }
    }

    private function generateMixed(array $section): array
    {
        $dbCount = (int) ($section['database_count'] ?? 0);
        $aiCount = (int) ($section['ai_count'] ?? 0);
        $manualCount = (int) ($section['manual_count'] ?? 0);

        $questions = [];

        if ($dbCount > 0) {
            $dbSection = array_merge($section, ['question_count' => $dbCount]);
            $questions = array_merge($questions, $this->generateFromDatabase($dbSection));
        }

        if ($aiCount > 0 && isset($section['document'])) {
            $aiSection = array_merge($section, ['question_count' => $aiCount]);
            $questions = array_merge($questions, $this->generateFromAi($aiSection));
        }

        if ($manualCount > 0) {
            $questions = array_merge($questions, $this->getManualPlaceholder(['question_count' => $manualCount]));
        }

        return $questions;
    }

    private function getManualPlaceholder(array $section): array
    {
        $count = (int) ($section['question_count'] ?? 0);
        return array_fill(0, $count, [
            'type' => 'manual',
            'question' => '[Manual question to be added]',
            'placeholder' => true,
        ]);
    }

    private function getPlaceholderForHardenedMode(array $section): array
    {
        $count = (int) ($section['question_count'] ?? 0);
        return [
            'hardened' => true,
            'count' => $count,
            'message' => 'Questions hidden in hardened mode',
        ];
    }

    private function fetchMcq(int $subjectId, array $topicIds, array $subtopicIds, int $count): array
    {
        $query = MultipleChoiceQuestion::query();
        $this->applyHierarchyFilters($query, $subjectId, $topicIds, $subtopicIds);

        return $query->inRandomOrder()->limit($count)->get()->map(function ($q) {
            return [
                'type' => 'multiple_choice',
                'question' => $this->asText($q->question),
                'options' => array_values(array_filter([$this->asText($q->option_a), $this->asText($q->option_b), $this->asText($q->option_c), $this->asText($q->option_d), $this->asText($q->option_e)])),
                'answer' => strtoupper((string) $q->answer),
                'points' => (float) ($q->score ?? 1),
            ];
        })->all();
    }

    private function fetchTof(int $subjectId, array $topicIds, array $subtopicIds, int $count): array
    {
        $query = TrueOrFalseQuestion::query();
        $this->applyHierarchyFilters($query, $subjectId, $topicIds, $subtopicIds);

        return $query->inRandomOrder()->limit($count)->get()->map(function ($q) {
            return [
                'type' => 'true_false',
                'question' => $this->asText($q->question),
                'options' => ['True', 'False'],
                'answer' => $q->answer ? 'True' : 'False',
                'points' => (float) ($q->score ?? 1),
            ];
        })->all();
    }

    private function fetchEssay(int $subjectId, array $topicIds, array $subtopicIds, int $count): array
    {
        $query = EssayQuestion::query();
        $this->applyHierarchyFilters($query, $subjectId, $topicIds, $subtopicIds);

        return $query->inRandomOrder()->limit($count)->get()->map(function ($q) {
            return [
                'type' => 'essay',
                'question' => $this->asText($q->question),
                'answer' => $this->asText($q->answer),
                'points' => (float) ($q->score ?? 5),
            ];
        })->all();
    }

    private function applyHierarchyFilters($query, int $subjectId, array $topicIds, array $subtopicIds): void
    {
        $query->whereHas('topic', function ($q) use ($subjectId) {
            $q->where('academic_subject_id', $subjectId);
        });

        if (! empty($topicIds)) {
            $query->whereIn('academic_topic_id', $topicIds);
        }

        if (! empty($subtopicIds)) {
            $query->whereIn('academic_subtopic_id', $subtopicIds);
        }
    }

    private function asText(mixed $value): string
    {
        if ($value instanceof Mark) {
            return trim(strip_tags((string) ($value->down ?? '')));
        }

        return trim(strip_tags((string) $value));
    }
}

