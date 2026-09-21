@props([
    'expression' => null, // raw TeX, e.g. "\frac{1}{2}" — skips markdown entirely
    'display' => false,   // display mode for $expression
    'content' => null,    // Mark, HTML, or markdown — delegates to x-ui.prose-content
])

@if($expression !== null)
    @php($display = filter_var($display, FILTER_VALIDATE_BOOLEAN))
    <span
        {{ $attributes->merge(['class' => 'katex-expr']) }}
        x-data="{
            init() {
                this.$nextTick(() => {
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
                        window.katex.render(expr, this.$el, {

                            displayMode,
                             throwOnError: false,
                             strict: false,
                             trust: true,
                         });
                    } catch (e) {
                        console.warn('KaTeX render error:', e);
                        this.$el.textContent = @js($expression);
                    }
                });
            }
        }"
    ></span>
@else
    <x-prose-content :content="$content" inline size="sm" {{ $attributes }} />
@endif
