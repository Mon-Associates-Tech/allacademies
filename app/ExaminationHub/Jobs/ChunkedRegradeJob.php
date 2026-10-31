<?php

namespace App\ExaminationHub\Jobs;

use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\ExaminationHub\Models\SubmissionAnswer;
use App\ExaminationHub\Services\ExamGradingService;
use App\ExaminationHub\Services\McqGrader;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * ChunkedRegradeJob — regrades submissions in chunks of ~50.
 *
 * Used by both the revision workflow (Phase 6) and the toggleGrading path.
 * Each chunk processes up to 50 submissions, then dispatches the next chunk.
 * This avoids holding the web request open for large exams.
 */
class ChunkedRegradeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Chunk size for regrading.
     */
    const CHUNK_SIZE = 50;

    /**
     * Create a new job instance.
     *
     * @param int $examId The exam to regrade
     * @param int $offset The starting offset for this chunk
     */
    public function __construct(
        protected int $examId,
        protected int $offset = 0
    ) {}

    /**
     * Execute the job.
     */
    public function handle(ExamGradingService $gradingService): void
    {
        $exam = GeneralExam::find($this->examId);
        if (!$exam) {
            return;
        }

        // Get submissions that need regrading
        $submissions = GeneralExamSubmission::where('general_exam_id', $this->examId)
            ->whereIn('status', [
                GeneralExamSubmission::STATUS_SUBMITTED,
                GeneralExamSubmission::STATUS_AUTO_GRADED,
                GeneralExamSubmission::STATUS_MANUALLY_REVIEWED,
            ])
            ->orderBy('id')
            ->skip($this->offset)
            ->limit(self::CHUNK_SIZE)
            ->get();

        if ($submissions->isEmpty()) {
            return;
        }

        // Grade each submission in this chunk
        foreach ($submissions as $submission) {
            try {
                $gradingService->grade($submission);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error(
                    "ChunkedRegradeJob failed for submission {$submission->id}: " . $e->getMessage()
                );
            }
        }

        // Dispatch next chunk if there are more submissions
        $nextOffset = $this->offset + self::CHUNK_SIZE;
        $remaining = GeneralExamSubmission::where('general_exam_id', $this->examId)
            ->whereIn('status', [
                GeneralExamSubmission::STATUS_SUBMITTED,
                GeneralExamSubmission::STATUS_AUTO_GRADED,
                GeneralExamSubmission::STATUS_MANUALLY_REVIEWED,
            ])
            ->skip($nextOffset)
            ->limit(1)
            ->count();

        if ($remaining > 0) {
            static::dispatch($this->examId, $nextOffset)->delay(now()->addSeconds(1));
        }
    }

    /**
     * Start a chunked regrade for an exam from the beginning.
     */
    public static function startForExam(int $examId): void
    {
        static::dispatch($examId, 0);
    }
}
