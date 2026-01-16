<?php

namespace App\Console\Commands;

use App\Jobs\ConvertBookToAudioJob;
use App\Models\Book;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessPendingAudioBooksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:process-audio-conversion
                            {--manual : Execute conversion immediately instead of dispatching to queue}
                            {--limit=1 : Number of books to process}
                            {--book= : Specific book ID to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process books that need audio conversion';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isManual = $this->option('manual');
        $limit = (int) $this->option('limit');
        $bookId = $this->option('book');

        $this->info('🎬 Starting audio conversion process for pending books...');

        if ($isManual) {
            $this->warn('⚡ Running in MANUAL mode - conversion will execute immediately');
        } else {
            $this->info('📋 Running in QUEUE mode - jobs will be dispatched to queue');
        }

        // Find books that are marked as pending for audio conversion
        $query = Book::where('audio_conversion_pending', true)
            ->whereNotNull('content_url')
            ->whereNotNull('table_of_contents');

        // If specific book ID is provided, only process that book
        if ($bookId) {
            $query->where('id', $bookId);
            $this->info("🎯 Processing specific book ID: {$bookId}");
        } else {
            $query->limit($limit);
        }

        $pendingBooks = $query->get();

        if ($pendingBooks->isEmpty()) {
            $this->info('✅ No books pending audio conversion.');

            return 0;
        }

        $this->info("📚 Found {$pendingBooks->count()} book(s) pending audio conversion.");

        $successCount = 0;
        $failCount = 0;

        foreach ($pendingBooks as $book) {
            $this->newLine();
            $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->info("📖 Processing: {$book->title} (ID: {$book->id})");
            $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

            try {
                if ($isManual) {
                    // Execute conversion immediately
                    $this->line('⏳ Converting book to audio (this may take several minutes)...');

                    // Create and execute the job directly
                    $job = new ConvertBookToAudioJob($book);
                    $job->handle();

                    $this->info("✅ Completed audio conversion for book ID: {$book->id}");
                    $successCount++;
                } else {
                    // Dispatch the job to queue
                    ConvertBookToAudioJob::dispatch($book);
                    $this->info("📤 Dispatched audio conversion job for book ID: {$book->id}");
                    $successCount++;
                }
            } catch (Exception $e) {
                $this->error("❌ Failed to process book ID: {$book->id}");
                $this->error("Error: {$e->getMessage()}");

                Log::error("Failed to process audio conversion for book ID: {$book->id}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                $failCount++;
            }
        }

        // Summary
        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('📊 SUMMARY');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info("✅ Successful: {$successCount}");
        if ($failCount > 0) {
            $this->error("❌ Failed: {$failCount}");
        }
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        if ($isManual) {
            $this->info('🎉 Manual conversion process completed!');
        } else {
            $this->info('🎉 Job dispatching completed!');
        }

        return $failCount > 0 ? 1 : 0;
    }
}
