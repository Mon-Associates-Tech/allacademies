@props([
    'content' => '',
    'class' => 'prose prose-invert max-w-none dark:prose-invert',
    'inline' => false,
])

@php
$wrapperTag = $inline ? 'span' : 'div';
$innerClass = $inline ? 'inline-block align-top' : '';
@endphp

<{{ $wrapperTag }} 
    {{ $attributes->merge(['class' => $class . ($inline ? ' markdown-inline' : '')]) }}
    x-data="{
        htmlContent: '',
        init() {
            this.renderContent();
        },
        renderContent() {
            const rawContent = @js($content);
            if (!rawContent) {
                this.htmlContent = '';
                return;
            }
            
            // Render markdown and math safely
            this.htmlContent = window.renderMarkdownWithMath(rawContent);
            
            // If inline mode, strip block-level wrappers from output to ensure true inline rendering
            @if($inline)
            this.htmlContent = this.htmlContent
                .replace(/^<p>/i, '')
                .replace(/<\/p>$/i, '')
                .replace(/<p>/gi, ' ')
                .replace(/<\/p>/gi, ' ')
                .replace(/<div[^>]*>/gi, '')
                .replace(/<\/div>/gi, '')
                .trim();
            @endif
            
            this.$nextTick(() => {
                this.renderMath();
            });
        },
        renderMath() {
            // Secondary pass for math rendering to catch any dynamic DOM timing edge cases
            if (typeof window.renderMathInElement !== 'undefined') {
                const target = this.$el.querySelector('.math-content');
                if (target) {
                    window.renderMathInElement(target, {
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
                    });
                }
            }
        }
    }"
    x-init="init()">
    
    <{{ $wrapperTag }} class="math-content {{ $innerClass }}" x-html="htmlContent"></{{ $wrapperTag }}>
    
</{{ $wrapperTag }}>

@push('styles')
<style>
    /* Dark mode prose fixes */
    .dark .prose {
        --tw-prose-body: #e2e8f0;
        --tw-prose-headings: #f1f5f9;
        --tw-prose-lead: #cbd5e1;
        --tw-prose-links: #60a5fa;
        --tw-prose-bold: #f1f5f9;
        --tw-prose-counters: #94a3b8;
        --tw-prose-bullets: #64748b;
        --tw-prose-hr: #334155;
        --tw-prose-quotes: #f1f5f9;
        --tw-prose-quote-borders: #475569;
        --tw-prose-captions: #94a3b8;
        --tw-prose-code: #f1f5f9;
        --tw-prose-pre-code: #e2e8f0;
        --tw-prose-pre-bg: #1e293b;
        --tw-prose-th-borders: #475569;
        --tw-prose-td-borders: #334155;
    }
    
    .markdown-inline {
        display: inline-block;
        vertical-align: top;
        max-width: 100%;
    }
    .markdown-inline .math-content,
    .markdown-inline .math-content > p,
    .markdown-inline .math-content > *:first-child:last-child {
        display: inline !important;
        margin: 0 !important;
        padding: 0 !important;
        line-height: inherit;
    }
    .markdown-inline img,
    .markdown-inline code,
    .markdown-inline strong,
    .markdown-inline em,
    .markdown-inline sup,
    .markdown-inline sub {
        vertical-align: middle;
    }
    /* Ensure inline images behave correctly and don't break line height */
    .markdown-inline img {
        display: inline-block !important;
        max-height: 1.5em;
        margin: 0 !important;
        padding: 0 !important;
    }
</style>
@endpush