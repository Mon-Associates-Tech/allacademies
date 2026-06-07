@props([
    'content' => '',
    'class' => 'prose max-w-none',
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
            let processedContent = @js($content);
            
            // Escape square brackets for KaTeX
            processedContent = processedContent.replace(/\\\[/g, '\\\\\\[');
            processedContent = processedContent.replace(/\\\]/g, '\\\\\\]');
            
            // Remove backticks if present
            processedContent = processedContent.replace(/`/g, '');
            
            this.htmlContent = window.renderMarkdownWithMath(processedContent);
            
            // If inline mode, strip block-level wrappers from output
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
    x-init="renderContent()">
    
    <{{ $wrapperTag }} class="math-content {{ $innerClass }}" x-html="htmlContent"></{{ $wrapperTag }}>
    
</{{ $wrapperTag }}>

{{-- Inline CSS for markdown-inline mode (include once globally) --}}
@push('styles')
<style>
    .markdown-inline .prose,
    .markdown-inline > p,
    .markdown-inline > *:first-child:last-child {
        display: inline !important;
        margin: 0 !important;
        padding: 0 !important;
        line-height: inherit;
    }
    .markdown-inline {
        display: inline-block;
        vertical-align: top;
        max-width: 100%;
    }
    .markdown-inline img,
    .markdown-inline code,
    .markdown-inline strong,
    .markdown-inline em,
    .markdown-inline sup,
    .markdown-inline sub {
        vertical-align: middle;
    }
</style>
@endpush