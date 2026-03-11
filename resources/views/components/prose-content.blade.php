@props(['content' => null, 'size' => 'base', 'mathSupport' => true, 'markdown' => true, 'textColor' => null])

@php
    $sizeClasses = match($size) {
        'sm' => 'prose-sm',
        'base' => 'prose-base',
        'lg' => 'prose-lg',
        'xl' => 'prose-xl',
        '2xl' => 'prose-2xl',
        default => 'prose-base'
    };

    // Parse markdown content if enabled
    $parsedContent = $content;
    if ($markdown && $content) {
        // Convert <br> tags to newlines before stripping HTML
        // This preserves line breaks that were added by rich text editors
        $cleanContent = preg_replace('/<br\s*\/?>/i', "\n", $content);

        // Convert closing </p> tags to double newlines to preserve paragraph breaks
        $cleanContent = preg_replace('/<\/p>\s*<p>/i', "\n\n", $cleanContent);

        // Strip any remaining HTML tags to get clean text
        // This handles cases where content was saved with HTML wrapping markdown
        $cleanContent = strip_tags($cleanContent);

        // Decode any HTML entities
        $cleanContent = html_entity_decode($cleanContent, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Normalize multiple newlines to double newlines for proper paragraph separation
        $cleanContent = preg_replace('/\n{3,}/', "\n\n", $cleanContent);

        // Parse the clean markdown content
        $parsedContent = \Illuminate\Support\Str::markdown($cleanContent, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }

    // Build base prose classes
    $baseProseClasses = "prose {$sizeClasses} max-w-none
    prose-headings:text-gray-900 dark:prose-headings:text-gray-100
    prose-headings:font-semibold prose-headings:leading-tight
    prose-h1:text-2xl prose-h1:mt-6 prose-h1:mb-4
    prose-h2:text-xl prose-h2:mt-5 prose-h2:mb-3
    prose-h3:text-lg prose-h3:mt-4 prose-h3:mb-2
    prose-h4:text-base prose-h4:mt-3 prose-h4:mb-2
    prose-a:text-blue-600 dark:prose-a:text-blue-400
    prose-strong:font-bold
    prose-em:italic
    prose-code:text-pink-600 dark:prose-code:text-pink-400
    prose-code:bg-pink-50 dark:prose-code:bg-pink-900/20
    prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded
    prose-pre:bg-gray-900 dark:prose-pre:bg-gray-950 prose-pre:text-gray-100 prose-pre:rounded-lg
    prose-ul:list-disc prose-ul:my-3 prose-ul:pl-6
    prose-ol:list-decimal prose-ol:my-3 prose-ol:pl-6
    prose-li:my-1
    prose-blockquote:border-l-4 prose-blockquote:border-blue-500 prose-blockquote:pl-4 prose-blockquote:italic
    prose-img:rounded-lg prose-img:shadow-md
    prose-table:w-full
    prose-th:border prose-th:bg-gray-50 dark:prose-th:bg-gray-800 prose-th:p-3
    prose-td:border prose-td:p-3";

    // Add default text colors only if no custom textColor is provided
    if (!$textColor) {
        $baseProseClasses .= " prose-p:text-gray-700 dark:prose-p:text-gray-300 prose-p:my-3 prose-p:leading-relaxed
        prose-strong:text-gray-900 dark:prose-strong:text-gray-100
        prose-em:text-gray-800 dark:prose-em:text-gray-200";
    } else {
        $baseProseClasses .= " prose-p:my-3 prose-p:leading-relaxed";
    }
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

<div {{ $attributes->merge(['class' => $baseProseClasses . ($textColor ? " {$textColor}" : '')]) }}"
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
{!! $parsedContent ?? $slot !!}
</div>
