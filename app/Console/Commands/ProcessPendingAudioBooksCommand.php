<?php

namespace App\Console\Commands;

use App\Jobs\ConvertBookToAudioJob;
use App\Models\Book;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessPendingAudioBooksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:process-audio-conversion';

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
        $this->info('Starting audio conversion process for pending books...');

        // Find books that are marked as pending for audio conversion
        $pendingBooks = Book::where('audio_conversion_pending', true)
            ->whereNotNull('content_url')
            ->whereNotNull('table_of_contents')
            ->get();

        if ($pendingBooks->isEmpty()) {
            $this->info('No books pending audio conversion.');
            return 0;
        }

        $this->info("Found {$pendingBooks->count()} books pending audio conversion.");

        foreach ($pendingBooks as $book) {
            try {
                // Dispatch the job for each book
                ConvertBookToAudioJob::dispatch($book);
                $this->info("Dispatched audio conversion job for book ID: {$book->id}");
            } catch (\Exception $e) {
                $this->error("Failed to dispatch job for book ID: {$book->id} - {$e->getMessage()}");
                Log::error("Failed to dispatch audio conversion job for book ID: {$book->id}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $this->info('Completed audio conversion job dispatching.');
        return 0;
    }
}
