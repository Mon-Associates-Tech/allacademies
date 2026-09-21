@props([
    'content' => null,
    'size' => 'base',
    'mathSupport' => true,
    'textColor' => null,
    'inline' => false,
])

@php
    $inline = filter_var($inline, FILTER_VALIDATE_BOOLEAN);
    $mathSupport = filter_var($mathSupport, FILTER_VALIDATE_BOOLEAN);

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

    if ($content instanceof \App\Support\Mark) {
        $htmlContent = $content->down;
        $markdownContent = $content->up;
    } elseif (is_string($content)) {
        if (preg_match('/<(p|div|table|img|iframe|h[1-6]|ul|ol|br)\b/i', $content)) {
            $htmlContent = $content;
        } else {
            $markdownContent = $content;
        }
    }

    if ($inline) {
        $baseProseClasses = 'prose-inline break-words';
    } else {
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
    }

    $tag = $inline ? 'span' : 'div';
@endphp

@once
    @push('scripts')
        <script>
            window.mathRenderConfig = {
                delimiters: [
                    // Backtick delimiters (Added)
                    {left: '``', right: '``', display: true},
                    {left: '`', right: '`', display: false},
                    // Existing delimiters
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

    <style>
        .prose-inline { display: inline; margin: 0; padding: 0; }
        .prose-inline p, .prose-inline div, .prose-inline h1, .prose-inline h2, .prose-inline h3,
        .prose-inline h4, .prose-inline h5, .prose-inline h6, .prose-inline ul, .prose-inline ol, .prose-inline li,
        .prose-inline blockquote {
            display: inline; margin: 0; padding: 0; font-size: inherit; line-height: inherit; border: 0;
        }
        .prose-inline .katex-display { margin: 0; }
        .prose-inline img { max-width: 100%; height: auto; display: inline-block; vertical-align: middle; }
    </style>
@endonce

{{--
    NOTICE: x-data is now a clean, single-line function call.
    This prevents Blade from breaking the HTML attribute with newlines or quotes.
--}}
<{{ $tag }}
    {{ $attributes->merge(['class' => $baseProseClasses . ($textColor ? " {$textColor}" : '')]) }}
    @if($mathSupport)
        x-data="proseMathRenderer(@js($markdownContent), @js($htmlContent), @js($inline))"
        x-init="initRenderer()"
    @endif
>
@if($htmlContent)
    {!! $htmlContent !!}
@elseif($markdownContent)
    <span class="hidden">{!! $markdownContent !!}</span>
@else
    {!! $slot !!}
@endif
</{{ $tag }}>

@once
<script>
    function proseMathRenderer(markdownContent, htmlContent, isInline) {
        return {
            initRenderer() {
                this.$nextTick(() => {
                    this.renderContent();
                });
            },
            renderContent() {
                // 1. If we only have markdown, parse it using your global JS function
                if (markdownContent && !htmlContent) {
                    if (typeof window.renderMarkdownWithMath === 'function') {
                        this.$el.innerHTML = window.renderMarkdownWithMath(markdownContent);
                    }
                }


                // 2. Inline mode: unwrap block wrappers so nothing breaks the line
                if (isInline) {
                    this.$el.querySelectorAll('p, h1, h2, h3, h4, h5, h6, div').forEach((el) => {
                        while (el.firstChild) el.parentNode.insertBefore(el.firstChild, el);
                        el.remove();
                    });
                }

                // 3. Render Math (KaTeX auto-render) on the cleaned HTML
                if (typeof window.renderMathInElement !== 'undefined') {
                    try {
                        window.renderMathInElement(this.$el, window.mathRenderConfig);
                    } catch(e) {
                        console.warn('KaTeX rendering error:', e);
                    this.$el.insertAdjacentHTML('beforeend', `<div style="color:#cc0000;font:11px monospace;border-top:1px dashed #cc0000;margin-top:4px;padding-top:2px;">⚠ render error: ${e.message}</div>`);                    }
                }
            }
        }
    }
</script>
@endonce
