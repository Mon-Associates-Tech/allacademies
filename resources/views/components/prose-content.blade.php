@props(['content' => null, 'size' => 'lg', 'mathSupport' => true])

@php
    $sizeClasses = match($size) {
        'sm' => 'prose-sm',
        'base' => 'prose-base',
        'lg' => 'prose-lg',
        'xl' => 'prose-xl',
        '2xl' => 'prose-2xl',
        default => 'prose-lg'
    };
@endphp

@once
    @push('scripts')
        <script>
            // Define math rendering configuration globally
            window.mathRenderConfig = {
                delimiters: [
                    {left: '$$', right: '$$', display: true},
                    {left: '$', right: '$', display: false},
                    {left: '\\[', right: '\\]', display: true},
                    {left: '\\(', right: '\\)', display: false}
                ],
                throwOnError: false,
                errorColor: '#cc0000',
                strict: false,
                trust: true
            };
        </script>
    @endpush
@endonce

<div {{ $attributes->merge(['class' => "prose {$sizeClasses} max-w-none
    prose-headings:text-gray-900 dark:prose-headings:text-gray-100
    prose-p:text-gray-700 dark:prose-p:text-gray-300
    prose-a:text-blue-600 dark:prose-a:text-blue-400
    prose-strong:text-gray-900 dark:prose-strong:text-gray-100 prose-strong:font-bold
    prose-em:text-gray-800 dark:prose-em:text-gray-200 prose-em:italic
    prose-code:text-pink-600 dark:prose-code:text-pink-400
    prose-code:bg-pink-50 dark:prose-code:bg-pink-900/20
    prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded
    prose-pre:bg-gray-900 dark:prose-pre:bg-gray-950 prose-pre:text-gray-100 prose-pre:rounded-lg
    prose-ul:list-disc prose-ul:my-4 prose-ul:pl-6
    prose-ol:list-decimal prose-ol:my-4 prose-ol:pl-6
    prose-li:my-1
    prose-blockquote:border-l-4 prose-blockquote:border-blue-500 prose-blockquote:pl-4 prose-blockquote:italic
    prose-img:rounded-lg prose-img:shadow-md
    prose-table:w-full
    prose-th:border prose-th:bg-gray-50 dark:prose-th:bg-gray-800 prose-th:p-3
    prose-td:border prose-td:p-3"]) }}"
@if($mathSupport)
    x-data="{
    renderMath() {
    if (typeof window.renderMathInElement !== 'undefined') {
    try {
    window.renderMathInElement(this.$el, window.mathRenderConfig);
    console.log('Math rendered successfully');
    } catch(e) {
    console.error('KaTeX error:', e);
    }
    }
    }
    }"
    x-init="$nextTick(() => setTimeout(() => renderMath(), 100))"
@endif>
{!! $content ?? $slot !!}
</div>
