import { marked } from "marked";
import MarkdownIt from "markdown-it";
import tm from "markdown-it-texmath";
import katex from "katex";
import renderMathInElement from 'katex/dist/contrib/auto-render';

window.katex = katex;
window.renderMathInElement = renderMathInElement;

// Kept as-is: Examinations Hub's details.blade.php (and possibly other views)
// call window.marked.parse(...) directly for plain markdown with no math
// involved. Do not repoint this at markdown-it — the API isn't compatible
// (.parse() vs .render()).
window.marked = marked;

// A separate markdown-it pipeline, used only by renderMarkdownWithMath below,
// for content that mixes markdown with $...$ / $$...$$ / \(...\) / \[...\]
// math. This is what MockExam's x-ui.prose-content calls.
const md = new MarkdownIt({
    html: false,     // allow raw HTML in source, matching marked's default
    linkify: true,  // auto-link bare URLs, matching marked's default
    breaks: true,   // single newline -> <br>; verify this against real exam
                     // content — if authors already separate items with a
                     // blank line rather than a single Enter, this can add
                     // double spacing. Flip to false if you see that.
}).use(tm, {
    engine: katex,
    delimiters: ['dollars', 'brackets', 'backticks'], // $...$ / $$...$$ AND \(...\) / \[...\]
    katexOptions: {
        throwOnError: false,
        strict: false,
        trust: true,
    },
});

const decodeHTMLEntities = (function() {
    const element = document.createElement('textarea');
    return function(html) {
        element.innerHTML = html;
        return element.value;
    };
})();

window.renderMarkdownWithMath = function(content) {
    if (!content) return '';

    try {
        const decodedContent = decodeHTMLEntities(content);

                // Authors habitually wrap math in single backticks (`$x^2$`),
                    // treating it like inline code. markdown-it's `backticks` rule
                        // consumes that as a raw code span before texmath's math rule ever
                            // runs — code-span content is never recursively re-tokenized by
                                // any markdown engine, so this MUST run on the raw string, before
                                    // md.render() is called, or it has no effect.
                                        const unwrapped = decodedContent
                        .replace(/`(\$\$[\s\S]+?\$\$)`/g, '$1')
                    .replace(/`(\\\[[\s\S]+?\\\])`/g, '$1')
                    .replace(/`(\\\([\s\S]+?\\\))`/g, '$1')
                    .replace(/`(\$[^`$\n]+?\$)`/g, '$1');

        const allowedTags = [
            'img', 'p', 'br', 'strong', 'em', 'code', 'pre', 'a', 'ul', 'ol', 'li',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote', 'table', 'thead',
            'tbody', 'tr', 'th', 'td', 'span', 'div', 'math', 'annotation',
            'semantics', 'mrow', 'mi', 'mn', 'mo', 'mspace', 'mfrac', 'msup',
            'msub', 'mroot', 'mfenced', 'mtext', 'mpadded', 'mover', 'munder',
            'munderover', 'mstyle', 'merror', 'mphantom', 'menclose', 'action',
            'svg', 'path', 'circle', 'rect', 'line', 'polyline', 'polygon', 'g',
            'defs', 'use', 'foreignObject'
        ];

        const sanitizedContent = typeof DOMPurify !== 'undefined'
            ? DOMPurify.sanitize(unwrapped, { ALLOWED_TAGS: allowedTags })
            : unwrapped;

        // markdown-it-texmath renders $...$ / $$...$$ / \(...\) / \[...\]
        // straight to KaTeX HTML during md.render() — no separate DOM-walk
        // math pass needed for this path anymore.
        return md.render(sanitizedContent);
    } catch (e) {
        console.warn('Markdown/Math rendering error:', e);
        return content;
    }
};
