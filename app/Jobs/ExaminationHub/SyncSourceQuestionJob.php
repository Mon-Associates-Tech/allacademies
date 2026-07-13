<?php

namespace App\Jobs\ExaminationHub;

use App\ExaminationHub\Models\GeneralExamQuestion;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\Traits\HasQuestionAndAnswer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Propagates a source question update (MultipleChoiceQuestion, TrueOrFalseQuestion,
 * EssayQuestion) into every GeneralExamQuestion that references it via
 * source_question_id, then re-grades all affected submissions.
 */
class SyncSourceQuestionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasQuestionAndAnswer;

    public int $tries   = 3;
    public int $backoff = 10;

    public function __construct(
        public readonly string $sourceType,  // 'multiple_choice' | 'true_false' | 'essay'
        public readonly int    $sourceId
    ) {}

    public function handle(): void
    {
        $examQuestions = GeneralExamQuestion::where('source_question_id', $this->sourceId)
            ->where('type', $this->sourceType)
            ->get();

        if ($examQuestions->isEmpty()) {
            return;
        }

        $source = $this->resolveSource();

        if (! $source) {
            Log::warning("SyncSourceQuestionJob: source not found", [
                'type' => $this->sourceType,
                'id'   => $this->sourceId,
            ]);
            return;
        }

        $attributes = $this->buildAttributes($source);

        foreach ($examQuestions as $examQuestion) {
            // Skip questions that were manually edited after exam creation
            if ($examQuestion->is_edited) {
                Log::info("SyncSourceQuestionJob: skipping manually-edited exam question {$examQuestion->id}");
                continue;
            }

            $examQuestion->update($attributes);

            Log::info("SyncSourceQuestionJob: synced exam question {$examQuestion->id} from source {$this->sourceType}:{$this->sourceId}");
        }

        // Re-grade all affected submissions
        $updatedIds = $examQuestions->where('is_edited', false)->pluck('id')->all();

        if (empty($updatedIds)) {
            return;
        }

        $submissionIds = GeneralExamSubmission::whereIn('general_exam_id',
            GeneralExamQuestion::whereIn('id', $updatedIds)->distinct()->pluck('general_exam_id')
        )
            ->whereIn('status', [
                GeneralExamSubmission::STATUS_SUBMITTED,
                GeneralExamSubmission::STATUS_AUTO_GRADED,
                GeneralExamSubmission::STATUS_MANUALLY_REVIEWED,
            ])
            ->whereNotNull('submitted_at')
            ->pluck('id');

        foreach ($submissionIds as $submissionId) {
            RegradeSubmissionJob::dispatch($submissionId);
        }

        Log::info("SyncSourceQuestionJob: dispatched regrade for {$submissionIds->count()} submission(s)", [
            'source_type' => $this->sourceType,
            'source_id'   => $this->sourceId,
        ]);
    }

    private function resolveSource(): mixed
    {
        return match ($this->sourceType) {
            'multiple_choice' => \App\Models\MultipleChoiceQuestion::find($this->sourceId),
            'true_false'      => \App\Models\TrueOrFalseQuestion::find($this->sourceId),
            'essay'           => \App\Models\EssayQuestion::find($this->sourceId),
            default           => null,
        };
    }

    private function buildAttributes(mixed $source): array
    {
        $questionText = $this->extractBestText($source->question);

        $base = [
            'question'       => $questionText,
            'correct_answer' => null,
            'options'        => null,
            'marks'          => $source->score ?? 1,
            'difficulty'     => $this->normalizeDifficulty($source->difficulty_level ?? null),
        ];

        return match ($this->sourceType) {
            'multiple_choice' => array_merge($base, [
                'correct_answer' => strtoupper(trim($source->answer ?? '')),
                'options'        => $this->buildMcqOptions($source),
            ]),
            'true_false' => array_merge($base, [
                'correct_answer' => $source->answer ? 'true' : 'false',
                'options'        => [
                    ['key' => 'True',  'value' => 'True'],
                    ['key' => 'False', 'value' => 'False'],
                ],
            ]),
            'essay' => array_merge($base, [
                'correct_answer' => $this->extractBestText($source->answer),
            ]),
            default => $base,
        };
    }

    private function normalizeDifficulty(?string $value): string
    {
        return in_array($value, ['easy', 'medium', 'hard'], true) ? $value : 'medium';
    }

    private function buildMcqOptions(mixed $source): array
    {
        $options   = [];
        $optionMap = ['option_a' => 'A', 'option_b' => 'B', 'option_c' => 'C', 'option_d' => 'D', 'option_e' => 'E'];

        foreach ($optionMap as $field => $key) {
            $text = $this->extractBestText($source->$field ?? null);
            if (! empty($text)) {
                $options[] = ['key' => $key, 'value' => $text];
            }
        }

        return $options;
    }
}
