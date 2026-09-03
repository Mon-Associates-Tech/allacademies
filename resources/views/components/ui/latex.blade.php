{{-- resources/views/components/ui/latex.blade.php --}}
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
                        window.katex.render(@js($expression), this.$el, {
                            displayMode: @js($display),
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