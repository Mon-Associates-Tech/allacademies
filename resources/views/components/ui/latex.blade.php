@props([
    'expression' => null, // raw TeX, e.g. "\frac{1}{2}" — skips markdown entirely
    'display' => false,   // display mode for $expression
    'content' => null,    // Mark, HTML, or markdown — delegates to x-ui.prose-content
    'pdf' => false,        // render for PDF (dompdf) instead of browser
])

@if($expression !== null)
    @php
        $display = filter_var($display, FILTER_VALIDATE_BOOLEAN);
    @endphp

    @if($pdf)
        @php
            $wrap = $display ? '$$' : '$';
            $rendered = app(\App\Support\MarkdownMathService::class)
                ->cachedRender($wrap . $expression . $wrap);

            // cachedRender runs full CommonMark, which wraps a bare expression
            // in a <p>; strip it since this is meant to sit inline.
            $rendered = preg_replace('/^<p>|<\/p>\s*$/', '', trim($rendered));
        @endphp

        <span {{ $attributes->merge(['class' => 'katex-expr']) }}>{!! $rendered !!}</span>
    @else
        <span
            {{ $attributes->merge(['class' => 'katex-expr']) }}
            x-data
            x-init="
                $nextTick(() => {
                    if (typeof window.katex === 'undefined') return;
                    try {
                        let expr = @js($expression).trim();
                        expr = expr.replace(/^`|`$/g, '').trim();
                        let displayMode = @js($display);

                        if (expr.startsWith('$$') && expr.endsWith('$$')) {
                            expr = expr.slice(2, -2).trim();
                            displayMode = true;
                        } else if (expr.startsWith('$') && expr.endsWith('$')) {
                            expr = expr.slice(1, -1).trim();
                        }

                        window.katex.render(expr, $el, {
                            displayMode,
                            throwOnError: false,
                            strict: false,
                            trust: true,
                        });
                    } catch (e) {
                        console.warn('KaTeX render error:', e);
                        $el.textContent = @js($expression);
                    }
                });
            "
        ></span>
    @endif
@else
    <x-prose-content :content="$content" inline size="sm" :pdf="$pdf" {{ $attributes }} />
@endif
