@props([
    'content' => null,
    'size' => 'base',
    'mathSupport' => true,
    'textColor' => null,
    'inline' => false,
    'static' => false,
])

@php
    $inline = filter_var($inline, FILTER_VALIDATE_BOOLEAN);

    $sizeClasses = match($size) {
        'sm' => 'prose-sm', 'base' => 'prose-base', 'lg' => 'prose-lg',
        'xl' => 'prose-xl', '2xl' => 'prose-2xl', default => 'prose-base'
    };

 $staticRender = filter_var($static, FILTER_VALIDATE_BOOLEAN);

if($staticRender){
     $staticHtml = app(\App\Support\StaticProse::class)->render($htmlContent ?? $markdownContent, $htmlContent !== null);
}

    $htmlContent = null;
    $markdownContent = null;

    if ($content instanceof \App\Support\Mark) {
        $htmlContent = $content->up;
        $markdownContent = $content->down;
    } elseif (is_string($content)) {
        if (preg_match('/<(p|div|table|img|iframe|h[1-6]|ul|ol|br)\b/i', $content)) {
            $htmlContent = $content;
        } else {
            $markdownContent = $content;
        }
    }

    /* 1) SERVER-SIDE MARKDOWN — Laravel ships league/commonmark (Str::markdown) */
    if ($markdownContent !== null) {
        $htmlContent = \Illuminate\Support\Str::markdown($markdownContent);
        $markdownContent = null;
    }

    /* 2) SERVER-SIDE KATEX — no-op unless you install the bridge below */
    $serverMath = class_exists(\App\Support\Katex::class);
    if ($serverMath && $htmlContent !== null) {
        $htmlContent = \App\Support\Katex::render($htmlContent);
    }

    if ($inline) {
        $baseProseClasses = 'prose-inline break-words';
    } else {
        $baseProseClasses = "prose {$sizeClasses} max-w-none break-words"; /* …your existing prose class list, unchanged… */
        if (!$textColor) {
            $baseProseClasses .= " prose-p:text-gray-700 prose-p:my-3 prose-p:leading-relaxed";
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
                    {left: '$$', right: '$$', display: true},
                    {left: '$', right: '$', display: false},
                    {left: '\\[', right: '\\]', display: true},
                    {left: '\\(', right: '\\)', display: false}
                ],
                throwOnError: false, errorColor: '#cc0000', strict: false, trust: true
            };
        </script>
    @endpush

    <style>
        .prose-inline { display: inline; margin: 0; padding: 0; }
        .prose-inline p, .prose-inline div, .prose-inline h1, .prose-inline h2, .prose-inline h3,
        .prose-inline h4, .prose-inline h5, .prose-inline h6, .prose-inline ul, .prose-inline ol,
        .prose-inline li, .prose-inline blockquote {
            display: inline; margin: 0; padding: 0; font-size: inherit; line-height: inherit; border: 0;
        }
        .prose-inline .katex-display { margin: 0; }
    </style>
@endonce

<{{ $tag }}
    {{ $attributes->merge(['class' => $baseProseClasses . ($textColor ? " {$textColor}" : '')]) }}
    @if(! $staticRender && ($mathSupport || $markdownContent))
    x-data="{ init() { this.$nextTick(() => this.renderContent()); },
renderContent() {
if (@js($mathSupport) && typeof window.renderMathInElement !== 'undefined' && window.mathRenderConfig) {
try { window.renderMathInElement(this.$el, window.mathRenderConfig); } catch(e) {}
}
}"
@endif
>
@if($staticRender)
    {!! $staticHtml !!}
@elseif($htmlContent)
    {!! $htmlContent !!}
@elseif($markdownContent)
    <span class="hidden">{!! $markdownContent !!}</span>
@else
    {!! $slot !!}
@endif
</{{ $tag }}>
