<?php

namespace App\Jobs;

use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Models\QuestionImportBatch;
use App\Services\QuestionImport\DocumentAiQuestionImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class ProcessDocumentQuestionImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Job-level timeout (seconds). This is independent of any nginx/php-fpm
     * request timeout since the job runs in a separate `queue:work` process —
     * this is the real ceiling for slow AI responses. Set it generously above
     * ChatGPTService's own worst-case retry budget (~3 attempts x ~90s + sleeps).
     */
    public int $timeout = 1200;

    /**
     * ChatGPTService::sendChatRequest() already retries internally on
     * 429/503/502 and connection errors, so we don't want the queue layer
     * retrying the whole job on top of that — it would re-run extraction
     * and multiply AI cost/latency for no benefit.
     */
    public int $tries = 1;

    public function __construct(public readonly int $batchId) {}

    public function handle(DocumentAiQuestionImportService $service): void
    {

                set_time_limit(0);

        DB::reconnect();
        $batch = QuestionImportBatch::find($this->batchId);

        if (! $batch) {
            Log::warning('ProcessDocumentQuestionImportJob: batch not found', ['batch_id' => $this->batchId]);

            return;
        }

        $batch->update(['status' => QuestionImportBatch::STATUS_PROCESSING]);

        try {
            $topic = $batch->academic_topic_id ? AcademicTopic::find($batch->academic_topic_id) : null;

            if (! $topic) {
                throw new \RuntimeException('Topic not found for this import batch.');
            }

            $subtopic = $batch->academic_subtopic_id ? AcademicSubtopic::find($batch->academic_subtopic_id) : null;

            $fullPath = storage_path('app/'.$batch->file_path);

            if (! file_exists($fullPath)) {
                throw new \RuntimeException('Uploaded file is missing — it may have been cleaned up before processing started.');
            }

            // The file is on disk (stored during the original request), not an
            // active HTTP upload, so we wrap it with the $test=true flag to
            // bypass UploadedFile's is_uploaded_file() check.
            $uploadedFile = new UploadedFile(
                $fullPath,
                $batch->original_filename ?? basename($fullPath),
                null,
                null,
                true
            );

            $result = $service->preview($uploadedFile, $topic, $subtopic);

            $batch->update([
                'status' => QuestionImportBatch::STATUS_COMPLETED,
                'results' => $result['results'] ?? [],
                'errors' => $result['errors'] ?? [],
                'extraction_method' => $result['extraction_method'] ?? null,
                'completed_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Async question import failed', [
                'batch_id' => $this->batchId,
                'error' => $e->getMessage(),
            ]);

            $batch->update([
                'status' => QuestionImportBatch::STATUS_FAILED,
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);
        }
    }

    /**
     * Called by the queue worker if the job exceeds $timeout or otherwise
     * fails outside the try/catch in handle() (e.g. OOM, fatal error).
     */
    public function failed(\Throwable $e): void
    {
        $batch = QuestionImportBatch::find($this->batchId);

        $batch?->update([
            'status' => QuestionImportBatch::STATUS_FAILED,
            'error_message' => $e->getMessage(),
            'completed_at' => now(),
        ]);
    }
}
