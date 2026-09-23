const DELIMITERS = [
    { left: '$$', right: '$$', display: true },
    { left: '$', right: '$', display: false },
    { left: '\\[', right: '\\]', display: true },
    { left: '\\(', right: '\\)', display: false },
];

function stripBackticks(expr) {
    return expr
        .replace(/`(\$\$[\s\S]*?\$\$)`/g, '$1')
        .replace(/`(\$[^\n]*?\$)`/g, '$1');
}

const looksLikeHtml = (expr) => /^<[a-z][\s\S]*>/i.test(expr);
const hasProse = (expr) => /[a-zA-Z]/.test(expr);

function autoRender(el, expr) {
    el.innerHTML = expr;
    if (typeof window.renderMathInElement === 'function') {
        window.renderMathInElement(el, {
            delimiters: DELIMITERS,
            throwOnError: false,
            strict: false,
        });
    }
}

function renderPureMath(el, expr) {
    let tex = expr;
    let display = false;

    if (tex.startsWith('$$') && tex.endsWith('$$')) {
        tex = tex.slice(2, -2).trim(); display = true;
    } else if (tex.startsWith('$') && tex.endsWith('$')) {
        tex = tex.slice(1, -1).trim();
    } else if (tex.startsWith('\\[') && tex.endsWith('\\]')) {
        tex = tex.slice(2, -2).trim(); display = true;
    } else if (tex.startsWith('\\(') && tex.endsWith('\\)')) {
        tex = tex.slice(2, -2).trim();
    }

    window.katex.render(tex, el, {
        displayMode: display,
        throwOnError: false,
        strict: false,
        trust: true,
    });
}

window.mathContent = (raw = '') => ({
    raw,

    boot() {
        if (!this.raw || String(this.raw).trim() === '') return;
        this.$nextTick(() => this.renderMath());
    },

    renderMath() {
        const el = this.$refs.container;
        if (!el) return;

        const expr = stripBackticks(String(this.raw).trim());

        try {
            if (looksLikeHtml(expr)) {
                // Legacy HTML string: inject, then scan for math delimiters
                autoRender(el, expr);
            } else if (hasProse(expr) && typeof window.renderMarkdownWithMath === 'function') {
                // Markdown + text: your markdown-it/texmath pipeline
                el.innerHTML = window.renderMarkdownWithMath(expr);
            } else if (typeof window.katex !== 'undefined') {
                // Pure LaTeX expression: direct KaTeX render (playground behaviour)
                renderPureMath(el, expr);
            } else {
                autoRender(el, expr);
            }
        } catch (error) {
            // No innerHTML-with-quotes here: safe error path
            el.textContent = expr;
            el.classList.add('text-red-500', 'text-sm', 'font-mono');
            console.error('[math-content]', error);
        }
    },
});
