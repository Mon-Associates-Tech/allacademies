import { marked } from "marked";
import katex from "katex";
import renderMathInElement from 'katex/dist/contrib/auto-render';

window.katex               = katex;
window.renderMathInElement = renderMathInElement;
window.marked = marked;

// Configure marked with proper LaTeX support
const renderer = new marked.Renderer();
const originalCodespan = renderer.codespan;
const originalCode = renderer.code;

renderer.codespan = function(code) {
    if (code.charAt(0) === '$' && code.charAt(code.length - 1) === '$') {
        try {
            return katex.renderToString(code.slice(1, -1), {
                throwOnError: false
            });
        } catch (e) {
            return originalCodespan.call(this, code);
        }
    }
    return originalCodespan.call(this, code);
};

renderer.code = function(code, lang, escaped) {
    if (code.charAt(0) === '$' && code.charAt(code.length - 1) === '$') {
        try {
            return katex.renderToString(code.slice(1, -1), {
                throwOnError: false,
                displayMode: true
            });
        } catch (e) {
            return originalCode.call(this, code, lang, escaped);
        }
    }
    return originalCode.call(this, code, lang, escaped);
};

marked.setOptions({ renderer });
window.katex = katex;
window.renderMathInElement = renderMathInElement;

// Safe markdown and math rendering function
window.renderMarkdownWithMath = function(content) {
    if (!content) return '';

    try {
        // Sanitize input to prevent XSS
        const sanitizedContent = DOMPurify ? DOMPurify.sanitize(content) : content;

        // Parse markdown with marked
        let htmlContent = marked.parse(sanitizedContent);

        // Create temporary element for math rendering
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = htmlContent;

        // Apply math rendering if available
        if (typeof renderMathInElement !== 'undefined') {
            renderMathInElement(tempDiv, {
                delimiters: [
                    {left: '$$', right: '$$', display: true},
                    {left: '$', right: '$', display: false},
                    {left: '\\[', right: '\\]', display: true},
                    {left: '\\(', right: '\\)', display: false}
                ],
                throwOnError: false,
                errorColor: '#cc0000',
                strict: false,
                trust: false
            });
        }

        return tempDiv.innerHTML;
    } catch (e) {
        console.warn('Markdown/Math rendering error:', e);
        return content; // Fallback to plain text
    }
};