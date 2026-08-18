<?php

namespace App\Console\Commands\ExaminationHub;

use App\ExaminationHub\Services\AnswerKeyResolutionService;
use App\Models\MultipleChoiceQuestion;
use Illuminate\Console\Command;

class SyncAndRegradeFromSource extends Command
{
    protected $signature = 'exam:sync-regrade
                            {mcq_ids?* : One or more MultipleChoiceQuestion IDs to sync from}
                            {--all-for-exam= : Sync all source questions belonging to a GeneralExam ID}
                            {--all : Sync all source questions across every exam}';

    protected $description = 'Sync exam questions from their source MCQs and regrade affected submissions synchronously';

    public function handle(AnswerKeyResolutionService $service): int
    {
        $mcqIds = array_map('intval', $this->argument('mcq_ids') ?? []);

        if ($this->option('all')) {
            $fromAll = \App\Models\MultipleChoiceQuestion::pluck('id')
                ->map(fn ($id) => (int) $id)
                ->toArray();

            $mcqIds = array_unique(array_merge($mcqIds, $fromAll));
        } elseif ($examId = $this->option('all-for-exam')) {
            $fromExam = \App\ExaminationHub\Models\GeneralExamQuestion::where('general_exam_id', $examId)
                ->whereNotNull('source_question_id')
                ->pluck('source_question_id')
                ->map(fn ($id) => (int) $id)
                ->toArray();

            $mcqIds = array_unique(array_merge($mcqIds, $fromExam));
        }

        if (empty($mcqIds)) {
            $this->error('No MCQ IDs found. Provide IDs, --all-for-exam=, or --all.');
            return self::FAILURE;
        }

        $this->info('Syncing ' . count($mcqIds) . ' source question(s) and regrading...');

        $result = $service->syncAndRegrade($mcqIds);

        $this->table(
            ['Exam questions synced', 'Submissions regraded', 'Failed'],
            [[$result['exam_questions_synced'], $result['submissions_regraded'], $result['failed']]]
        );

        if (! empty($result['errors'])) {
            foreach ($result['errors'] as $err) {
                $this->warn("Submission {$err['submission_id']}: {$err['error']}");
            }
        }

        return $result['failed'] > 0 ? self::FAILURE : self::SUCCESS;
    }
}
