import { Marked } from 'marked';
import markedKatex from 'marked-katex-extension';

export function createMarkedRenderer(options = {}) {
    const marked = new Marked();

    const katexOptions = {
        throwOnError: false,
        errorColor: '#b91c1c',
        strict: false,
        trust: false,
        maxExpand: 500,
        output: options.output ?? 'htmlAndMathml',
        ...(options.katex ?? {}),
    };

    marked.use({
        gfm: true,
        breaks: false,
    });

    marked.use(markedKatex(katexOptions));

    return marked;
}
