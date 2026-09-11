import { marked } from "marked";
import katex from "katex";
import renderMathInElement from 'katex/dist/contrib/auto-render';

window.katex = katex;
window.renderMathInElement = renderMathInElement;
window.marked = marked;

// Helper to safely decode HTML entities (e.g., if Laravel pre-escaped the string)
const decodeHTMLEntities = (function() {
    const element = document.createElement('textarea');
    return function(html) {
        element.innerHTML = html;
        return element.value;
    };
})();

function unwrapBacktickedMath(content) {
    return content
        .replace(/`(\$\$[\s\S]+?\$\$)`/g, '$1')
        .replace(/`(\\\[[\s\S]+?\\\])`/g, '$1')
        .replace(/`(\\\([\s\S]+?\\\))`/g, '$1')
        .replace(/`(\$[^`$\n]+?\$)`/g, '$1');
}


function mergeAdjacentBacktickSpans(content) {
    // Some source content splits a single math expression across several
    // separate single-backtick spans (e.g. `$\hspace{0.3cm}` `(\alpha)\`
    // `2\frac{3}{4}` `\div ...$` instead of one clean `$...$` span).
    // Collapse any backtick spans separated by nothing but whitespace into
    // one span, so the unwrap step below sees the whole expression as a
    // single unit instead of four fragments.
    let prev;
    do {
        prev = content;
        content = content.replace(/`([^`\n]*)`(\s+)`([^`\n]*)`/g, '`$1$2$3`');
    } while (content !== prev);
    return content;
}

window.renderMarkdownWithMath = function(content) {
    if (!content) return '';

    try {
        // 1. Decode entities first to handle any pre-escaped content from the backend
        const decodedContent = decodeHTMLEntities(content);
        
        // 2. Merge fragments of a single math expression that got split across
        // several adjacent backtick spans, then unwrap the outer backticks.
        const merged = mergeAdjacentBacktickSpans(decodedContent);
        const mathifiedContent = unwrapBacktickedMath(merged);

        // 3. Anything still wrapped in backticks at this point is genuine inline
        // code, not math — leave it alone. `marked` will render it as <code>
        // correctly on its own; forcing every leftover backtick into $...$ is
        // what fragmented exam content into broken partial expressions.

        // 4. Sanitize input to prevent XSS, explicitly allowing images and common markdown/math tags
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
            ? DOMPurify.sanitize(mathifiedContent, { ALLOWED_TAGS: allowedTags }) 
            : mathifiedContent;

        // 5. Parse markdown with marked
        let htmlContent = marked.parse(sanitizedContent);

        // 6. Create temporary element for math rendering
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = htmlContent;

        // 7. Apply math rendering if available
        if (typeof window.renderMathInElement !== 'undefined') {
            window.renderMathInElement(tempDiv, {
                delimiters: [
                    // Existing delimiters (backticks are now safely handled in Step 3)
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

        return tempDiv.innerHTML;
    } catch (e) {
        console.warn('Markdown/Math rendering error:', e);
        return content; // Fallback to plain text
    }
};