@props([
    'content' => null, // Mark object, HTML string, or raw markdown string
    'inline' => false,
])

@php
    $inline = filter_var($inline, FILTER_VALIDATE_BOOLEAN);

    $htmlContent = null;
    $markdownContent = null;

    if ($content instanceof \App\Support\Mark) {
        // `up` is pre-rendered HTML from the WYSIWYG editor (already has
        // KaTeX baked in) — prefer it, it needs no client-side work at all.
        // `down` is the raw markdown source, used only as a fallback.
        if (!empty($content->up)) {
            $htmlContent = $content->up;
        } elseif (!empty($content->down)) {
            $markdownContent = $content->down;
        }
    } elseif (is_string($content)) {
        if (preg_match('/<(p|div|table|img|iframe|h[1-6]|ul|ol|br)\b/i', $content)) {
            $htmlContent = $content;
        } else {
            $markdownContent = $content;
        }
    }

    $tag = $inline ? 'span' : 'div';
@endphp

@once
    <style>
        .exam-content-inline { display: inline; margin: 0; padding: 0; }
        .exam-content-inline p, .exam-content-inline div,
        .exam-content-inline h1, .exam-content-inline h2, .exam-content-inline h3,
        .exam-content-inline h4, .exam-content-inline h5, .exam-content-inline h6,
        .exam-content-inline ul, .exam-content-inline ol, .exam-content-inline li,
        .exam-content-inline blockquote {
            display: inline; margin: 0; padding: 0;
            font-size: inherit; line-height: inherit; border: 0;
        }
        .exam-content-inline .katex-display { display: inline; margin: 0; }
        .exam-content-inline img {
            max-width: 100%; height: auto; display: inline-block; vertical-align: middle;
        }
    </style>
@endonce

<{{ $tag }}
    {{ $attributes->merge(['class' => $inline ? 'exam-content-inline' : 'exam-content-block']) }}
    x-data="{
        init() {
            this.\$nextTick(() => {
                if (typeof window.ExamMarkdown === 'undefined') {
                    console.warn('[x-ui.exam-content] ExamMarkdown script not loaded.');
                    return;
                }
                if (@js((bool) $htmlContent)) {
                    window.ExamMarkdown.renderHtmlWithMathPass(@js($htmlContent), this.\$el);
                } else if (@js((bool) $markdownContent)) {
                    this.\$el.innerHTML = window.ExamMarkdown.renderMarkdown(@js($markdownContent));
                }
                if (@js($inline)) {
                    this.\$el.querySelectorAll('p, h1, h2, h3, h4, h5, h6').forEach((el) => {
                        while (el.firstChild) el.parentNode.insertBefore(el.firstChild, el);
                        el.remove();
                    });
                }
            });
        }
    }"
></{{ $tag }}>