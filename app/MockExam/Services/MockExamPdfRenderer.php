<?php

namespace App\MockExam\Services;

use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;
use Throwable;

class MockExamPdfRenderer
{
    private const MAX_ATTEMPTS = 3;

    public function render(string $html): string
    {
        for ($attempt = 1; ; $attempt++) {
            try {
                return $this->renderAttempt($html);
            } catch (Throwable $exception) {
                if ($attempt >= self::MAX_ATTEMPTS || ! $this->isTransientConnectionFailure($exception)) {
                    throw $exception;
                }

                Log::warning('Retrying mock exam PDF generation after a Chromium connection reset.', [
                    'attempt' => $attempt,
                    'exception' => $exception::class,
                ]);

                usleep(250_000 * $attempt);
            }
        }
    }

    protected function renderAttempt(string $html): string
    {
        return Browsershot::html($html)
            ->setIncludePath('$PATH:/usr/local/bin')
            ->setNodeEnv(['PUPPETEER_CACHE_DIR' => config('app.puppeteer_cache_dir')])
            ->emulateMedia('print')
            ->showBackground()
            ->format('A4')
            ->margins(15, 15, 15, 15)
            ->noSandbox()
            ->setOption('args', ['--disable-dev-shm-usage'])
            ->timeout(120)
            ->pdf();
    }

    private function isTransientConnectionFailure(Throwable $exception): bool
    {
        $message = strtolower($exception->getMessage());

        return str_contains($message, 'socket hang up') || str_contains($message, 'econnreset');
    }
}
