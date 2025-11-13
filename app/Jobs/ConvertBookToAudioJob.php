<?php

namespace App\Jobs;

use App\Models\Book;
use App\Models\User;
use App\Notifications\BookAudioConversionCompleted;
use App\Notifications\BookAudioConversionFailed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class ConvertBookToAudioJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $bookId;
    public int $timeout = 0;
    public int $tries = 10; // Allow up to 10 attempts
    public int $maxExceptions = 3; // Max consecutive failures before giving up

    public function __construct(Book $book)
    {
        $this->bookId = $book->id;
    }

    public function handle(): void
    {
        ini_set('max_execution_time', 0);
        set_time_limit(0);

        $book = Book::find($this->bookId);
        if (!$book) {
            Log::error("❌ Book not found for ID {$this->bookId}");
            return;
        }

        // Update attempt counter
        $book->update([
            'audio_conversion_attempts' => $book->audio_conversion_attempts + 1,
            'audio_conversion_last_attempt' => now(),
        ]);

        try {
            $this->processConversion($book);
        } catch (\Throwable $e) {
            Log::error("❌ Fatal error in ConvertBookToAudioJob (book ID {$this->bookId}): {$e->getMessage()}");
            Log::error($e->getTraceAsString());

            // Don't fail completely - keep pending for retry
            $book->update(['audio_conversion_pending' => true]);

            // Only notify failure after multiple attempts
            if ($book->audio_conversion_attempts >= 3) {
                $this->notifyFailure($book, $e->getMessage());
            }

            throw $e; // Re-throw for queue retry mechanism
        }
    }

    private function processConversion(Book $book): void
    {
        // Load or initialize progress
        $progress = $book->audio_conversion_progress ?? [
            'pdf_parsed' => false,
            'completed_chapters' => [],
            'failed_chapters' => [],
            'current_chapter' => null,
        ];

        $relativePdfPath = $book->getAttributes()['content_url'] ?? null;
        if (!$relativePdfPath) {
            Log::error("❌ Missing content_url for book ID {$book->id}");
            $this->notifyFailure($book, 'Missing PDF file');
            return;
        }

        $pdfPath = Storage::disk('public')->path($relativePdfPath);
        if (!file_exists($pdfPath)) {
            Log::error("❌ PDF not found at path: {$pdfPath}");
            $this->notifyFailure($book, 'PDF file not found');
            return;
        }

        // Parse PDF (cached if already done)
        $parser = new Parser();
        $pdf = $parser->parseFile($pdfPath);
        $pages = $pdf->getPages();

        if (empty($pages)) {
            Log::error("❌ No pages found in PDF for book ID {$book->id}");
            $this->notifyFailure($book, 'No pages found in PDF');
            return;
        }

        Log::info("📄 Total pages: " . count($pages));

        // Normalize TOC
        $toc = $this->normalizeToc($book->table_of_contents);
        if (empty($toc)) {
            Log::error("❌ No valid chapters found in TOC for book ID {$book->id}");
            $this->notifyFailure($book, 'No valid chapters found');
            return;
        }

        Log::info("Found " . count($toc) . " chapters in TOC.");

        $apiKey = config('services.openai.key');
        $baseFolder = "audio-books/{$book->id}";
        $chaptersFolder = "{$baseFolder}/chapters";
        Storage::disk('public')->makeDirectory($chaptersFolder);

        $chapterAudios = [];

        // Process each chapter (skip already completed ones)
        foreach ($toc as $chapter) {
            $chapterNum = $chapter['chapter'];

            // Check if already completed
            if (in_array($chapterNum, $progress['completed_chapters'])) {
                Log::info("⏭️ Skipping already completed Chapter {$chapterNum}");

                // Add existing chapter file to array
                $chapterPath = "{$chaptersFolder}/chapter{$chapterNum}.mp3";
                if (Storage::disk('public')->exists($chapterPath)) {
                    $chapterAudios[] = $chapterPath;
                }
                continue;
            }

            // Update current chapter in progress
            $progress['current_chapter'] = $chapterNum;
            $book->update(['audio_conversion_progress' => $progress]);

            $pageStart = $chapter['page_start'];
            $pageEnd = $chapter['page_end'];

            Log::info("🎙 Processing Chapter {$chapterNum}: pages {$pageStart}–{$pageEnd}");

            try {
                // Extract text
                $text = '';
                for ($i = $pageStart - 1; $i < $pageEnd && isset($pages[$i]); $i++) {
                    $text .= $pages[$i]->getText() . "\n";
                }

                // Clean text
                $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
                $text = preg_replace('/[^\P{C}\n]+/u', '', $text);
                $text = preg_replace('/\s+/', ' ', $text);
                $text = trim($text);

                if (empty($text)) {
                    Log::warning("⚠️ No text extracted for Chapter {$chapterNum}");
                    $progress['failed_chapters'][] = $chapterNum;
                    $book->update(['audio_conversion_progress' => $progress]);
                    continue;
                }

                // Split into chunks
                $chunks = str_split($text, 800);
                $chunkPaths = [];

                foreach ($chunks as $chunkIndex => $chunk) {
                    $chunk = trim($chunk);
                    if (empty($chunk)) continue;

                    Log::info("🔊 Chapter {$chapterNum}, Chunk " . ($chunkIndex + 1) . "/" . count($chunks));

                    // Check if chunk file already exists
                    $tempChunkPath = "{$chaptersFolder}/temp_chunk_ch{$chapterNum}_{$chunkIndex}.mp3";
                    $fullChunkPath = Storage::disk('public')->path($tempChunkPath);

                    if (file_exists($fullChunkPath)) {
                        Log::info("⏭️ Using existing chunk file for Chapter {$chapterNum}, Chunk {$chunkIndex}");
                        $chunkPaths[] = $fullChunkPath;
                        continue;
                    }

                    $payload = [
                        'model' => 'gpt-4o-mini-tts',
                        'voice' => 'alloy',
                        'input' => $chunk,
                        'format' => 'mp3',
                    ];

                    $response = Http::withHeaders([
                        'Authorization' => "Bearer {$apiKey}",
                        'Content-Type' => 'application/json',
                    ])->timeout(300)
                        ->post('https://api.openai.com/v1/audio/speech', $payload);

                    if ($response->failed()) {
                        Log::error("❌ Failed chunk {$chunkIndex} in Chapter {$chapterNum}: " . $response->body());
                        continue;
                    }

                    Storage::disk('public')->put($tempChunkPath, $response->body());
                    $chunkPaths[] = $fullChunkPath;

                    // Rate limit pause
                    usleep(300000);
                }

                // Merge chunks into chapter file
                if (!empty($chunkPaths)) {
                    $chapterPath = "{$chaptersFolder}/chapter{$chapterNum}.mp3";
                    $chapterFullPath = Storage::disk('public')->path($chapterPath);

                    $merged = fopen($chapterFullPath, 'wb');
                    foreach ($chunkPaths as $path) {
                        if (file_exists($path)) {
                            fwrite($merged, file_get_contents($path));
                            @unlink($path); // Clean up temp chunks
                        }
                    }
                    fclose($merged);

                    $chapterAudios[] = $chapterPath;

                    // Mark chapter as completed
                    $progress['completed_chapters'][] = $chapterNum;
                    $progress['current_chapter'] = null;
                    $book->update(['audio_conversion_progress' => $progress]);

                    Log::info("✅ Created audio for Chapter {$chapterNum}");
                }

            } catch (\Throwable $ex) {
                Log::error("⚠️ Exception processing Chapter {$chapterNum}: " . $ex->getMessage());
                $progress['failed_chapters'][] = $chapterNum;
                $book->update(['audio_conversion_progress' => $progress]);

                // Don't fail the entire job, continue with next chapter
                continue;
            }
        }

        if (empty($chapterAudios)) {
            Log::error("❌ No chapter audios generated for book ID {$book->id}");
            $this->notifyFailure($book, 'No audio chapters were generated');
            return;
        }

        // Merge all chapters into single file
        $finalSingleAudio = "{$baseFolder}/book_audio.mp3";
        $finalSinglePath = Storage::disk('public')->path($finalSingleAudio);

        $mergedSingle = fopen($finalSinglePath, 'wb');
        foreach ($chapterAudios as $chPath) {
            $fullPath = Storage::disk('public')->path($chPath);
            if (file_exists($fullPath)) {
                fwrite($mergedSingle, file_get_contents($fullPath));
            }
        }
        fclose($mergedSingle);

        // Update database - conversion complete
        $book->update([
            'has_audio' => true,
            'audio_conversion_pending' => false,
            'audio_conversion_progress' => null, // Clear progress
        ]);

        $book->media()->updateOrCreate(
            ['book_id' => $book->id],
            [
                'chapter_audios' => $chapterAudios,
                'single_audio' => $finalSingleAudio,
            ]
        );

        Log::info("✅ Completed audio conversion for book ID {$book->id} with " . count($chapterAudios) . " chapters");
        $this->notifySuccess($book, count($chapterAudios));
    }

    private function normalizeToc($toc): array
    {
        if (empty($toc)) {
            return [];
        }

        if (is_string($toc)) {
            $decoded = json_decode($toc, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $toc = $decoded;
            } else {
                Log::error("❌ Failed to decode table_of_contents JSON: " . json_last_error_msg());
                return [];
            }
        }

        if (!is_array($toc)) {
            return [];
        }

        return collect($toc)
            ->filter(function ($ch) {
                return is_array($ch)
                    && isset($ch['chapter'], $ch['page_start'], $ch['page_end'])
                    && (int) $ch['chapter'] > 0;
            })
            ->map(function ($ch) {
                return [
                    'chapter' => (int) $ch['chapter'],
                    'title' => $ch['title'] ?? "Chapter {$ch['chapter']}",
                    'page_start' => (int) $ch['page_start'],
                    'page_end' => (int) $ch['page_end'],
                ];
            })
            ->sortBy('chapter')
            ->values()
            ->toArray();
    }

    private function notifySuccess(Book $book, int $chaptersCount): void
    {
        try {
            $user = User::find($book->audio_conversion_initiated_by);

            if ($user) {
                $user->notify(new BookAudioConversionCompleted($book, $chaptersCount));
                Log::info("📧 Notification sent to user {$user->id} for book {$book->id}");
            }

            if ($book->author && $book->author->user_id !== $book->audio_conversion_initiated_by) {
                $book->author->user->notify(new BookAudioConversionCompleted($book, $chaptersCount));
                Log::info("📧 Notification sent to author for book {$book->id}");
            }
        } catch (\Throwable $e) {
            Log::error("Failed to send success notification: " . $e->getMessage());
        }
    }

    private function notifyFailure(Book $book, string $errorMessage): void
    {
        try {
            $user = User::find($book->audio_conversion_initiated_by);

            if ($user) {
                $user->notify(new BookAudioConversionFailed($book, $errorMessage));
                Log::info("📧 Failure notification sent to user {$user->id} for book {$book->id}");
            }

            if ($book->author && $book->author->user_id !== $book->audio_conversion_initiated_by) {
                $book->author->user->notify(new BookAudioConversionFailed($book, $errorMessage));
                Log::info("📧 Failure notification sent to author for book {$book->id}");
            }
        } catch (\Throwable $e) {
            Log::error("Failed to send failure notification: " . $e->getMessage());
        }
    }
}
