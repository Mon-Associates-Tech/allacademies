@props(['content' => null])

@php
    $html = null;
    $source = null;

    if ($content instanceof \App\Support\Mark) {
        $html = $content->html?->toHtml();
        $source = $content->up;
    } elseif (is_array($content)) {
        $html = $content['down'] ?? null;
        $source = $content['markdown'] ?? $content['up'] ?? null;
    } elseif (is_string($content)) {
        $decoded = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $html = $decoded['down'] ?? null;
            $source = $decoded['markdown'] ?? $decoded['up'] ?? null;
        } else {
            $source = $content;
        }
    }

    $unrendered = fn (?string $s) => is_string($s)
        && preg_match('/(`\$|\$\$|\$\\\\[a-zA-Z(]|\\\\\(|\\\\\[)/', $s);

    // A string that IS a raw HTML block: CommonMark 'strip' would delete it wholesale.
    $isHtmlBlock = fn (?string $s) => is_string($s) && preg_match('/^\s*<[a-z][^>]*>/i', $s);

    if (blank($html) || $unrendered($html)) {
        $markdown = $source ?? $html ?? '';

        if (trim($markdown) !== '') {
            $html = cache()->remember(
                'viewer:v3:' . sha1($markdown) . ($isHtmlBlock($markdown) ? ':html' : ':md'),
                now()->addDays(7),
                function () use ($markdown, $isHtmlBlock) {
                    $service = app(\App\Support\MarkdownMathService::class);

                    $renderHtml = function () use ($service, $markdown) {
                        return method_exists($service, 'renderHtmlWithMath')
                            ? $service->renderHtmlWithMath($markdown)
                            : $markdown; // last resort: trusted internal content, output as-is
                    };

                    if ($isHtmlBlock($markdown)) {
                        return $renderHtml();
                    }

                    $rendered = $service->render($markdown);

                    // Safety net: CommonMark stripped everything (mixed HTML blocks)
                    if (trim($rendered) === '') {
                        return $renderHtml();
                    }

                    return $rendered;
                }
            );
        }
    }
@endphp

<div {{ $attributes->class(['prose dark:prose-invert max-w-none', 'content-rendered']) }}>
    @if(trim((string) $html) !== '')
        {!! $html !!}
    @elseif(config('app.debug'))
        @php
            $preview = is_string($content)
                ? \Illuminate\Support\Str::limit(str_replace(["\r", "\n"], ' ', $content), 140)
                : null;
            $decodedDbg = is_string($content) ? json_decode($content, true) : null;
        @endphp
        <div class="rounded border border-amber-300 bg-amber-50 p-2 text-xs text-amber-800">
            markdown-viewer: empty input — received
            <code>{{ is_object($content) ? get_class($content) : gettype($content) }}</code>
            @if($content instanceof \App\Support\Mark)
                (up: {{ $content->up === null ? 'null' : strlen($content->up).' chars' }},
                down: {{ $content->down === null ? 'null' : strlen($content->down).' chars' }})
            @elseif(is_string($content))
                <div class="mt-1 break-all font-mono">{{ $preview === '' ? '(empty string)' : $preview }}</div>
                @if(is_array($decodedDbg))
                    <div class="mt-1">JSON keys: {{ implode(', ', array_keys($decodedDbg)) ?: '(none)' }}</div>
                @endif
            @endif
        </div>
    @endif
</div>
