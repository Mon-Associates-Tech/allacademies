import MarkdownIt from "markdown-it";
import tm from "markdown-it-texmath";
import katex from "katex";
import renderMathInElement from "katex/dist/contrib/auto-render";

/**
 * Question-bank content is frequently authored with math wrapped in single
 * backticks (`$...$`), and sometimes with the dollar signs themselves also
 * backslash-escaped (`\$...\$`) on top of that. Strip both forms down to
 * plain $...$ / $$...$$ / \(...\) / \[...\] before markdown-it ever sees the
 * text, so its own tokenizer — not a guessing regex — decides what's math.
 */
function normalizeMathDelimiters(text) {
    if (!text) return text;
    return text
        .replace(/`(\$\$[\s\S]+?\$\$)`/g, '$1')
        .replace(/`(\\\[[\s\S]+?\\\])`/g, '$1')
        .replace(/`(\\\([\s\S]+?\\\))`/g, '$1')
        .replace(/`(\$[^`$\n]+?\$)`/g, '$1')
        .replace(/`\\\$\\\$([\s\S]+?)\\\$\\\$`/g, (_, inner) => `$$${inner}$$`)
        .replace(/`\\\$([^`]+?)\\\$`/g, (_, inner) => `$${inner}$`);
}

/**
 * Protect real ```fenced``` code blocks from the single-backtick normalize
 * pass above, so a literal `$PATH`-style backtick span inside genuine code
 * (ICT/computing question content) is never mistaken for math.
 */
function protectFencedCode(text, store) {
    return text.replace(/```[\s\S]*?```/g, (match) => {
        const token = `\x02CODE${store.length}\x03`;
        store.push(match);
        return token;
    });
}
function restoreFencedCode(text, store) {
    return text.replace(/\x02CODE(\d+)\x03/g, (_, i) => store[Number(i)]);
}

const md = new MarkdownIt({
    html: true,
    linkify: true,
    breaks: true,
}).use(tm, {
    engine: katex,
    delimiters: ['dollars', 'brackets'],
    katexOptions: {
        throwOnError: false,
        strict: false,
        trust: true,
    },
});

const decodeHTMLEntities = (function() {
    const el = document.createElement('textarea');
    return function(html) {
        el.innerHTML = html;
        return el.value;
    };
})();

const ALLOWED_TAGS = [
    'img', 'p', 'br', 'strong', 'em', 'code', 'pre', 'a', 'ul', 'ol', 'li',
    'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote', 'table', 'thead',
    'tbody', 'tr', 'th', 'td', 'span', 'div', 'math', 'annotation',
    'semantics', 'mrow', 'mi', 'mn', 'mo', 'mspace', 'mfrac', 'msup',
    'msub', 'mroot', 'mfenced', 'mtext', 'mpadded', 'mover', 'munder',
    'munderover', 'mstyle', 'merror', 'mphantom', 'menclose', 'action',
    'svg', 'path', 'circle', 'rect', 'line', 'polyline', 'polygon', 'g',
    'defs', 'use', 'foreignObject'
];

function sanitize(html) {
    return typeof window.DOMPurify !== 'undefined'
        ? window.DOMPurify.sanitize(html, { ALLOWED_TAGS })
        : html;
}

/**
 * Render raw markdown (optionally containing math, in plain, backtick-wrapped,
 * or backslash-escaped form) to sanitized HTML.
 */
function renderMarkdown(content) {
    if (!content) return '';
    try {
        const decoded = decodeHTMLEntities(content);
        const codeStore = [];
        let text = protectFencedCode(decoded, codeStore);
        text = normalizeMathDelimiters(text);
        text = restoreFencedCode(text, codeStore);
        return md.render(sanitize(text));
    } catch (e) {
        console.warn('[ExamMarkdown] render error:', e);
        return content;
    }
}

/**
 * For content that's already HTML (e.g. a Mark object's pre-rendered `up`
 * field from a WYSIWYG editor) — display as-is, but run a KaTeX pass in case
 * any math delimiters were left unrendered by whatever produced it. Do not
 * use this for raw markdown; use renderMarkdown() instead.
 */
function renderHtmlWithMathPass(html, el) {
    if (!html) return '';
    el.innerHTML = sanitize(decodeHTMLEntities(html));

    try {
        renderMathInElement(el, {
            delimiters: [
                { left: '$$', right: '$$', display: true },
                { left: '$', right: '$', display: false },
                { left: '\\[', right: '\\]', display: true },
                { left: '\\(', right: '\\)', display: false },
            ],
            throwOnError: false,
            strict: false,
            trust: true,
        });
    } catch (e) {
        console.warn('[ExamMarkdown] math pass error:', e);
    }

    return el.innerHTML;
}

window.ExamMarkdown = { renderMarkdown, renderHtmlWithMathPass };