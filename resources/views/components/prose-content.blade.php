@props([
    'content' => null, // Can be a string, or an App\Support\Mark object
    'size' => 'base',
    'mathSupport' => true,
    'textColor' => null
])

@php
    $sizeClasses = match($size) {
        'sm' => 'prose-sm',
        'base' => 'prose-base',
        'lg' => 'prose-lg',
        'xl' => 'prose-xl',
        '2xl' => 'prose-2xl',
        default => 'prose-base'
    };

    $htmlContent = null;
    $markdownContent = null;

    // Intelligently determine if we have HTML or Markdown
    if ($content instanceof \App\Support\Mark) {
        $htmlContent = $content->up;
        $markdownContent = $content->down;
    } elseif (is_string($content)) {
        // Heuristic: if it contains block-level HTML tags, treat as HTML
        if (preg_match('/<(p|div|table|img|iframe|h[1-6]|ul|ol|br)\b/i', $content)) {
            $htmlContent = $content;
        } else {
            $markdownContent = $content;
        }
    }

    // Base prose classes (Tailwind Typography)
    $baseProseClasses = "prose {$sizeClasses} max-w-none break-words
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
        prose-img:rounded-lg prose-img:shadow-md prose-img:max-w-full
        prose-table:w-full
        prose-th:border prose-th:bg-gray-50 dark:prose-th:bg-gray-800 prose-th:p-3
        prose-td:border prose-td:p-3";

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

<div
    {{ $attributes->merge(['class' => $baseProseClasses . ($textColor ? " {$textColor}" : '')]) }}
    @if($mathSupport || $markdownContent)
        x-data="{
            init() {
                this.$nextTick(() => this.renderContent());
            },
            renderContent() {
                // 1. If we only have markdown, parse it using your global JS function
                if (@js($markdownContent && !$htmlContent)) {
                    if (typeof window.renderMarkdownWithMath === 'function') {
                        this.$el.innerHTML = window.renderMarkdownWithMath(@js($markdownContent));
                    }
                }

                // 2. Render Math (KaTeX) on the final HTML
                if (@js($mathSupport) && typeof window.renderMathInElement !== 'undefined') {
                    try {
                        window.renderMathInElement(this.$el, window.mathRenderConfig);
                    } catch(e) {
                        console.warn('KaTeX rendering error:', e);
                    }
                }
            }
        }"
    @endif
>
    {{-- Render the pre-generated HTML from TinyMCE directly --}}
    @if($htmlContent)
        {!! $htmlContent !!}
    @elseif($markdownContent)
        {{-- Fallback: If only markdown is available, JS will populate this via innerHTML --}}
        <span class="hidden">{!! $markdownContent !!}</span>
    @else
        {!! $slot !!}
    @endif
</div>
