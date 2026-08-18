import { marked } from "marked";
import katex from "katex";
import renderMathInElement from 'katex/dist/contrib/auto-render';

window.katex = katex;
window.renderMathInElement = renderMathInElement;
window.marked = marked;

// Helper to safely decode HTML entities (e.g., if Laravel pre-escaped the string)
// This prevents "&lt;p&gt;Text&lt;/p&gt;" from rendering as literal text on screen.
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
        // 1. Decode entities first to handle any pre-escaped content from the backend
        const decodedContent = decodeHTMLEntities(content);

        // 2. Sanitize input to prevent XSS, explicitly allowing images and common markdown/math tags
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
            ? DOMPurify.sanitize(decodedContent, { ALLOWED_TAGS: allowedTags }) 
            : decodedContent;

        // 3. Parse markdown with marked
        let htmlContent = marked.parse(sanitizedContent);

        // 4. Create temporary element for math rendering
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = htmlContent;

        // 5. Apply math rendering if available
        // Note: renderMathInElement automatically ignores <code> and <pre> tags, 
        // which correctly prevents math from rendering inside backticks.
        if (typeof window.renderMathInElement !== 'undefined') {
            window.renderMathInElement(tempDiv, {
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

        return tempDiv.innerHTML;
    } catch (e) {
        console.warn('Markdown/Math rendering error:', e);
        return content; // Fallback to plain text
    }
};