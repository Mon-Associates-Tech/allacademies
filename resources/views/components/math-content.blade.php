@props(['content', 'clientRender' => false])

@php
    $serverHtml = null;
    $raw = '';

    if ($content instanceof \App\Support\Mark) {
        $serverHtml = $content->html?->toHtml();
        $raw = $content->up ?? '';
    } elseif (is_array($content)) {
        $serverHtml = $content['down'] ?? null;
        $raw = $content['markdown'] ?? $content['up'] ?? '';
    } elseif (is_string($content)) {
        $decoded = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)
            && (isset($decoded['up']) || isset($decoded['down']) || isset($decoded['markdown']))) {
            $serverHtml = $decoded['down'] ?? null;
            $raw = $decoded['markdown'] ?? $decoded['up'] ?? '';
        } else {
            $raw = $content;
        }
    }

    $serverHtml = is_string($serverHtml) ? trim($serverHtml) : null;
    if ($serverHtml === '') $serverHtml = null;

    // A stored "down" that still contains unrendered math delimiters is NOT html — it's raw.
    $looksUnrendered = fn (?string $s) => is_string($s)
        && preg_match('/(`\$|\$\$|\$\\\\[a-zA-Z(]|\\\\\(|\\\\\[)/', $s);

    if ($serverHtml !== null && $looksUnrendered($serverHtml)) {
        $raw = $serverHtml;
        $serverHtml = null;
    }

    // DEFAULT: render on the server. Works with zero JS (print, PDF, paper views).
    if ($serverHtml === null && $raw !== '' && ! $clientRender) {
        $service = app(\App\Support\MarkdownMathService::class);

        $serverHtml = preg_match('/^\s*<[a-z]/i', $raw)
            // Legacy HTML block (your &radic; / Word-paste content)
            ? $service->cachedRender('html:' . $raw) === null ? '' : cache()->remember(
                  'math-content:v1:html:' . sha1($raw),
                  now()->addDays(7),
                  fn () => $service->renderHtmlWithMath($raw)
              )
            // Markdown block (your backticked `$...$` content)
            : $service->cachedRender(preg_replace('/<br\s*\/?>/i', "\n\n", $raw));
    }

    $needsClient = $serverHtml === null && $raw !== '';
@endphp

@if(! $needsClient)
    <span {{ $attributes->class(['prose dark:prose-invert max-w-none math-content']) }}>
        {!! $serverHtml !!}
    </span>
@else
    {{-- Opt-in interactive mode only (pass client-render) --}}
    <span
        x-data="mathContent(@js($raw))"
        x-init="boot()"
        {{ $attributes->class(['prose dark:prose-invert max-w-none math-content']) }}
    >
        <span x-ref="container">{{ $raw }}</span>
    </span>
@endif
