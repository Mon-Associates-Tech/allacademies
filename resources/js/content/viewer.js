import DOMPurify from 'dompurify';
import 'katex/dist/katex.min.css';
import * as markedModule from 'marked';

const marked = markedModule.Marked
    ? new markedModule.Marked()
    : (markedModule.marked ?? markedModule.default);

window.contentViewer = (initial = '') => ({
    raw: initial,
    html: '',
    init() {
        this.render();
        this.$watch('raw', () => this.render());
    },
    render() {
        const md = this.raw ?? '';
        if (!md.trim()) { this.html = ''; return; }
        try {
            this.html = DOMPurify.sanitize(marked.parse(md, { async: false }), {
                USE_PROFILES: { html: true, mathMl: true, svg: true },
                ADD_ATTR: ['style', 'class', 'aria-hidden'],
            });
        } catch (e) {
            console.error('[contentViewer]', e);
            this.html = md;
        }
    },
});
