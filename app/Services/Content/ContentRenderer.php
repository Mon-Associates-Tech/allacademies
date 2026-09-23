<?php

namespace App\Services\Content;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Spatie\Browsershot\Browsershot;

class ContentRenderer
{
    private const VERSION = 'v1';

    public function toStaticHtml(string $markdown, string $mode = 'html'): array
    {
        $key = 'content:' . self::VERSION . ':' . $mode . ':' . sha1($markdown);

        return Cache::rememberForever($key, function () use ($markdown, $mode) {
            return $this->runNode([
                'markdown' => $markdown,
                'mode' => $mode,
                'includeCss' => true,
            ]);
        });
    }

    public function pdf(string $markdown, array $options = []): string
    {
        $hash = sha1(self::VERSION . 'pdf' . json_encode($options) . $markdown);
        $path = 'content/pdf/' . $hash . '.pdf';

        if (Storage::exists($path)) {
            return Storage::get($path);
        }

        ['html' => $body, 'css' => $css] = $this->toStaticHtml($markdown, 'pdf');

        $html = view('content.pdf-document', [
            'body' => $body,
            'css' => $css,
        ])->render();

        $browsershot = Browsershot::html($html)
            ->noSandbox()
            ->emulateMedia('print')
            ->showBackground()
            ->format($options['format'] ?? 'A4')
            ->margins(
                $options['marginTop'] ?? 20,
                $options['marginRight'] ?? 20,
                $options['marginBottom'] ?? 20,
                $options['marginLeft'] ?? 20
            )
            ->setOption('args', [
                '--disable-dev-shm-usage',
                '--force-color-profile=srgb',
            ])
            ->timeout($options['timeout'] ?? 60);

        if (! empty($options['chromePath'])) {
            $browsershot->setChromePath($options['chromePath']);
        }

        $pdf = $browsershot->pdf();

        Storage::put($path, $pdf);

        return $pdf;
    }

    public function dompdf(string $markdown): string
    {
        ['html' => $body, 'css' => $css] = $this->toStaticHtml($markdown, 'dompdf');

        $html = view('content.pdf-document', [
            'body' => $body,
            'css' => $css,
        ])->render();

        $dompdf = app('dompdf.wrapper');
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();

        return $dompdf->output();
    }

    protected function runNode(array $payload): array
    {
        $process = Process::timeout(config('services.renderer.timeout', 30))
            ->input(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR))
            ->run([
                config('services.renderer.node', 'node'),
                config('services.renderer.script', resource_path('js/content/render.server.mjs')),
            ]);

        if ($process->failed()) {
            report(new RuntimeException('Content renderer failed: ' . $process->errorOutput()));

            throw new RuntimeException('Could not render markdown/math.');
        }

        return json_decode($process->output(), true, 512, JSON_THROW_ON_ERROR);
    }
}
