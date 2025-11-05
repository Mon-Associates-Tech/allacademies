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

            // 🧠 Parse PDF
            $parser = new Parser();
            $pdf = $parser->parseFile($pdfPath);
            $pages = $pdf->getPages();
            if (empty($pages)) {
                Log::error("❌ No pages found in PDF for book ID {$book->id}");
                return;
            }

            Log::info("📄 Total pages: " . count($pages));

            // 🧭 Normalize the table_of_contents
            $toc = $this->normalizeToc($book->table_of_contents);
            if (empty($toc)) {
                Log::error("❌ No valid chapters found in TOC for book ID {$book->id}");
                return;
            }

            Log::info("🧭 Normalized TOC: " . json_encode($toc));
            Log::info("🧭 Found " . count($toc) . " chapters in TOC.");

            $apiKey = config('services.openai.key');

            // ✅ Create proper folder structure
            $baseFolder = "audio-books/{$book->id}";
            $chaptersFolder = "{$baseFolder}/chapters";
            Storage::disk('public')->makeDirectory($chaptersFolder);

            $chapterAudios = [];

            // 🔁 Process each chapter
            foreach ($toc as $chapter) {
                $chapterNum = $chapter['chapter'];
                $pageStart = $chapter['page_start'];
                $pageEnd = $chapter['page_end'];

                Log::info("🎙 Processing Chapter {$chapterNum}: pages {$pageStart}–{$pageEnd}");

                // Extract text for this chapter
                $text = '';
                for ($i = $pageStart - 1; $i < $pageEnd && isset($pages[$i]); $i++) {
                    $text .= $pages[$i]->getText() . "\n";
                }

                // Clean the text
                $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
                $text = preg_replace('/[^\P{C}\n]+/u', '', $text);
                $text = preg_replace('/\s+/', ' ', $text);
                $text = trim($text);

                if (empty($text)) {
                    Log::warning("⚠️ No text extracted for Chapter {$chapterNum}");
                    continue;
                }

                // Split into chunks (800 characters each)
                $chunks = str_split($text, 800);
                $chunkPaths = [];

                foreach ($chunks as $chunkIndex => $chunk) {
                    $chunk = trim($chunk);
                    if (empty($chunk)) continue;

                    Log::info("🔊 Chapter {$chapterNum}, Chunk " . ($chunkIndex + 1) . "/" . count($chunks));

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
                        ])->timeout(300)
                            ->post('https://api.openai.com/v1/audio/speech', $payload);

                        if ($response->failed()) {
                            Log::error("❌ Failed chunk {$chunkIndex} in Chapter {$chapterNum}: " . $response->body());
                            continue;
                        }

                        $tempChunkPath = "{$chaptersFolder}/temp_chunk_ch{$chapterNum}_{$chunkIndex}.mp3";
                        Storage::disk('public')->put($tempChunkPath, $response->body());
                        $chunkPaths[] = Storage::disk('public')->path($tempChunkPath);

                        // Small rate limit pause
                        usleep(300000);
                    } catch (\Throwable $ex) {
                        Log::error("⚠️ Exception in chunk {$chunkIndex} of Chapter {$chapterNum}: " . $ex->getMessage());
                        continue;
                    }
                }

                // Merge chunks into one file per chapter
                if (!empty($chunkPaths)) {
                    $chapterPath = "{$chaptersFolder}/chapter{$chapterNum}.mp3";
                    $chapterFullPath = Storage::disk('public')->path($chapterPath);

                    $merged = fopen($chapterFullPath, 'wb');
                    foreach ($chunkPaths as $path) {
                        fwrite($merged, file_get_contents($path));
                        @unlink($path);
                    }
                    fclose($merged);

                    $chapterAudios[] = $chapterPath;
                    Log::info("✅ Created audio for Chapter {$chapterNum}");
                }
            }

            if (empty($chapterAudios)) {
                Log::error("❌ No chapter audios generated for book ID {$book->id}");
                return;
            }

            // ✅ Merge all chapters into a single audio file inside the same book folder
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

            // ✅ Update database
            $book->update(['has_audio' => true]);
            $book->media()->updateOrCreate(
                ['book_id' => $book->id],
                [
                    'chapter_audios' => $chapterAudios,
                    'single_audio' => $finalSingleAudio,
                ]
            );

            Log::info("✅ Completed audio conversion for book ID {$book->id} with " . count($chapterAudios) . " chapters");

        } catch (\Throwable $e) {
            Log::error("❌ Fatal error in ConvertBookToAudioJob (book ID {$this->bookId}): {$e->getMessage()}");
            Log::error($e->getTraceAsString());
        }
    }

    /**
     * 🧭 Normalize table_of_contents from JSON string to array of chapters.
     */
    private function normalizeToc($toc): array
    {
        if (empty($toc)) {
            return [];
        }

        // Decode JSON if it's a string
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

        // Filter and normalize each chapter
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
}
