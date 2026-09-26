@props(['content' => null, 'pdf' => false])

@php
    // 1. Aggressively extract the raw string, no matter the format
    $rawString = '';

    if ($content instanceof \App\Support\Mark) {
        $rawString = $content->up ?? $content->down ?? '';
    } elseif (is_array($content)) {
        $rawString = $content['markdown'] ?? $content['up'] ?? $content['down'] ?? '';
    } elseif (is_string($content)) {
        $decoded = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $rawString = $decoded['markdown'] ?? $decoded['up'] ?? $decoded['down'] ?? '';
        } else {
            $rawString = $content;
        }
    }

    $html = '';

    // 2. PDF MODE
    if ($pdf && is_string($rawString) && trim($rawString) !== '') {
        // ✅ CACHE VERSION BUMPED TO v2 TO FORCE RE-EVALUATION
        $html = cache()->remember(
            'viewer:pdf:v2:' . sha1($rawString),
            now()->addDays(7),
            function () use ($rawString) {
                $isHtml = preg_match('/^\s*<[a-z][^>]*>/i', trim($rawString));

                if ($isHtml) {
                    // Already HTML, just convert math to SVG
                    return \App\Helpers\MathHelper::renderMath($rawString);
                }

                // It's markdown, convert math then render through markdown service
                $textWithSvgs = \App\Helpers\MathHelper::renderMath($rawString);
                $service = app(\App\Support\MarkdownMathService::class);

                return method_exists($service, 'render')
                    ? $service->render($textWithSvgs)
                    : $textWithSvgs;
            }
        );
    }
    // 3. STANDARD WEB MODE (Your original logic)
    elseif (!$pdf) {
        $unrendered = fn (?string $s) => is_string($s) && preg_match('/(`\$|\$\$|\$\\\\[a-zA-Z(]|\\\\\(|\\\\\[)/', $s);
        $isHtmlBlock = fn (?string $s) => is_string($s) && preg_match('/^\s*<[a-z][^>]*>/i', $s);

        if (blank($html) || $unrendered($rawString)) {
            if (trim($rawString) !== '') {
                $html = cache()->remember(
                    'viewer:v3:' . sha1($rawString) . ($isHtmlBlock($rawString) ? ':html' : ':md'),
                    now()->addDays(7),
                    function () use ($rawString, $isHtmlBlock) {
                        $service = app(\App\Support\MarkdownMathService::class);

                        $renderHtml = function () use ($service, $rawString) {
                            return method_exists($service, 'renderHtmlWithMath')
                                ? $service->renderHtmlWithMath($rawString)
                                : $rawString;
                        };

                        if ($isHtmlBlock($rawString)) {
                            return $renderHtml();
                        }

                        $rendered = $service->render($rawString);

                        if (trim($rendered) === '') {
                            return $renderHtml();
                        }

                        return $rendered;
                    }
                );
            }
        }
    }
@endphp

<div {{ $attributes->class(['prose dark:prose-invert max-w-none', 'content-rendered']) }}>
    @if(trim((string) $html) !== '')
        {!! $html !!}
    @else
        {{-- ✅ BULLETPROOF FALLBACK: If cache failed but we have raw string, output it directly --}}
        @if($pdf && is_string($rawString) && trim($rawString) !== '')
            {!! $rawString !!}
        @elseif(config('app.debug'))
            <div class="rounded border border-amber-300 bg-amber-50 p-2 text-xs text-amber-800">
                markdown-viewer: empty input — received {{ is_object($content) ? get_class($content) : gettype($content) }}
                @if(is_string($content))
                    <div class="mt-1 break-all font-mono">{{ \Illuminate\Support\Str::limit($content, 140) }}</div>
                @endif
            </div>
        @endif
    @endif
</div>
