<?php

namespace App\Jobs;

use App\Models\Book;
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

    public function __construct(Book $book)
    {
        $this->bookId = $book->id;
    }

    public function handle(): void
    {
        ini_set('max_execution_time', 0);
        set_time_limit(0);

        try {
            $book = Book::find($this->bookId);
            if (!$book) {
                Log::error("❌ Book not found for ID {$this->bookId}");
                return;
            }

            $relativePdfPath = $book->getAttributes()['content_url'] ?? null;
            if (!$relativePdfPath) {
                Log::error("❌ Missing content_url for book ID {$book->id}");
                return;
            }

            $pdfPath = Storage::disk('public')->path($relativePdfPath);
            if (!file_exists($pdfPath)) {
                Log::error("❌ PDF not found at path: {$pdfPath}");
                return;
            }

            // Extract pages
            $parser = new Parser();
            $pdf = $parser->parseFile($pdfPath);
            $pages = $pdf->getPages();

            if (empty($pages)) {
                Log::error("❌ No pages found in PDF for book ID {$book->id}");
                return;
            }

            Log::info("📄 Total pages: " . count($pages));

            $apiKey = config('services.openai.key');
            $chapterAudios = [];
            $pageBatchSize = 10;
            $volume = 1;

            $pageGroups = array_chunk($pages, $pageBatchSize);

            foreach ($pageGroups as $pageSet) {
                // Concatenate text for this 10-page batch
                $text = '';
                foreach ($pageSet as $page) {
                    $text .= $page->getText() . "\n";
                }

                $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
                $text = preg_replace('/[^\P{C}\n]+/u', '', $text);
                $text = preg_replace('/\s+/', ' ', $text);
                $text = trim($text);

                if (empty($text)) continue;

                // Split into smaller chunks to avoid TTS token limits (~800 chars)
                $chunks = str_split($text, 800);
                $volumeAudioPaths = [];

                foreach ($chunks as $chunkIndex => $chunk) {
                    if (empty(trim($chunk))) continue;

                    Log::info("🔊 Converting Volume {$volume}, Chunk " . ($chunkIndex + 1) . "/" . count($chunks));

                    $payload = [
                        'model' => 'gpt-4o-mini-tts',
                        'voice' => 'alloy',
                        'input' => $chunk,
                        'format' => 'mp3',
                    ];

                    try {
                        $response = Http::withHeaders([
                            'Authorization' => "Bearer {$apiKey}",
                            'Content-Type' => 'application/json',
                        ])->timeout(300)->post('https://api.openai.com/v1/audio/speech', $payload);

                        if ($response->failed()) {
                            Log::error("❌ Failed chunk {$chunkIndex} in Volume {$volume}: " . $response->body());
                            continue;
                        }

                        $chunkPath = "book-audio/temp_chunk_{$book->id}_vol{$volume}_{$chunkIndex}.mp3";
                        Storage::disk('public')->put($chunkPath, $response->body());
                        $volumeAudioPaths[] = Storage::disk('public')->path($chunkPath);

                        usleep(300000); // slight delay
                    } catch (\Throwable $ex) {
                        Log::error("⚠️ Exception in chunk {$chunkIndex}: " . $ex->getMessage());
                        continue;
                    }
                }

                // Merge chunks for this 10-page volume
                if (!empty($volumeAudioPaths)) {
                    $finalVolumePath = "book-audio/book-{$book->id}_vol{$volume}.mp3";
                    $merged = fopen(Storage::disk('public')->path($finalVolumePath), 'wb');
                    foreach ($volumeAudioPaths as $path) {
                        fwrite($merged, file_get_contents($path));
                        @unlink($path);
                    }
                    fclose($merged);

                    $chapterAudios[] = $finalVolumePath;
                }

                $volume++;
            }

            if (empty($chapterAudios)) {
                Log::error("❌ No audio volumes generated for book ID {$book->id}");
                return;
            }

            // Merge all volumes into **one final audio file**
            $finalSingleAudio = "book-audio/book-{$book->id}_audio.mp3";
            $finalSinglePath = Storage::disk('public')->path($finalSingleAudio);

            $mergedSingle = fopen($finalSinglePath, 'wb');
            foreach ($chapterAudios as $volPath) {
                fwrite($mergedSingle, file_get_contents(Storage::disk('public')->path($volPath)));
                @unlink(Storage::disk('public')->path($volPath)); // clean up temp volume
            }
            fclose($mergedSingle);

            $book->update(['has_audio' => true]);
            $book->media()->updateOrCreate(
                ['book_id' => $book->id],
                [
                    'chapter_audios' => $chapterAudios,    // keep all volume paths
                    'single_audio' => $finalSingleAudio,   // final merged audio
                ]
            );

            Log::info("✅ Completed audio conversion for book ID {$book->id} with " . count($chapterAudios) . " volumes");
        } catch (\Throwable $e) {
            Log::error("❌ Fatal error in ConvertBookToAudioJob (book ID {$this->bookId}): {$e->getMessage()}");
        }
    }
}
