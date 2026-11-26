<?php

namespace Tests\Unit\Services;

use App\MockExam\Services\MockExamPdfRenderer;
use RuntimeException;
use Tests\TestCase;

class MockExamPdfRendererTest extends TestCase
{
    public function test_retries_transient_browser_connection_failures_until_success(): void
    {
        $renderer = new class extends MockExamPdfRenderer
        {
            public int $attempts = 0;

            protected function renderAttempt(string $html): string
            {
                $this->attempts++;

                if ($this->attempts < 3) {
                    throw new RuntimeException('ErrorEvent: socket hang up (ECONNRESET)');
                }

                return '%PDF-test';
            }
        };

        $this->assertSame('%PDF-test', $renderer->render('<html></html>'));
        $this->assertSame(3, $renderer->attempts);
    }

    public function test_does_not_retry_non_connection_failures(): void
    {
        $renderer = new class extends MockExamPdfRenderer
        {
            public int $attempts = 0;

            protected function renderAttempt(string $html): string
            {
                $this->attempts++;
                throw new RuntimeException('Invalid HTML content');
            }
        };

        try {
            $renderer->render('<html></html>');
            $this->fail('The rendering exception should be rethrown.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Invalid HTML content', $exception->getMessage());
            $this->assertSame(1, $renderer->attempts);
        }
    }

    public function test_rethrows_a_persistent_browser_connection_failure_after_one_retry(): void
    {
        $renderer = new class extends MockExamPdfRenderer
        {
            public int $attempts = 0;

            protected function renderAttempt(string $html): string
            {
                $this->attempts++;
                throw new RuntimeException('socket hang up');
            }
        };

        try {
            $renderer->render('<html></html>');
            $this->fail('The persistent rendering exception should be rethrown.');
        } catch (RuntimeException $exception) {
            $this->assertSame('socket hang up', $exception->getMessage());
            $this->assertSame(3, $renderer->attempts);
        }
    }
}
