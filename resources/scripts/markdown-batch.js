const MarkdownIt = require('markdown-it');
const tm = require('markdown-it-texmath');
const katex = require('katex');

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

function normalizeMathDelimiters(text) {
    return text
        .replace(/`(\$\$[\s\S]+?\$\$)`/g, '$1')
        .replace(/`(\\\[[\s\S]+?\\\])`/g, '$1')
        .replace(/`(\\\([\s\S]+?\\\))`/g, '$1')
        .replace(/`(\$[^`$\n]+?\$)`/g, '$1')
        .replace(/`\\\$\\\$([\s\S]+?)\\\$\\\$`/g, (_, inner) => `$$${inner}$$`)
        .replace(/`\\\$([^`]+?)\\\$`/g, (_, inner) => `$${inner}$`);
}

function normalizeLineBreaks(text) {
    return text.replace(/(\s*<br\s*\/?>\s*){1,}/gi, '\n\n');
}

const md = new MarkdownIt({
    html: false, // question-bank markdown doesn't need raw HTML passthrough —
                 // keep this off server-side so nothing unexpected reaches a PDF.
    linkify: true,
    breaks: true,
}).use(tm, {
    engine: katex,
    delimiters: ['dollars', 'brackets', 'backticks'],
    katexOptions: {
        throwOnError: false,
        strict: false,
        trust: true,
        output: 'html',
    },
});

let input = '';
process.stdin.on('data', (c) => (input += c));
process.stdin.on('end', () => {
    let items;
    try {
        items = JSON.parse(input || '[]');
    } catch (e) {
        process.stderr.write('Invalid JSON input: ' + e.message);
        process.exit(1);
    }

    const out = items.map((markdown) => {
        try {
            const codeStore = [];
            let text = protectFencedCode(String(markdown ?? ''), codeStore);
            text = normalizeMathDelimiters(text);
            text = normalizeLineBreaks(text);
            text = restoreFencedCode(text, codeStore);
            return {html: md.render(text)};
        } catch (e) {
            const escaped = String(markdown ?? '').replace(/[<>&]/g, (c) => ({
                '<': '&lt;',
                '>': '&gt;',
                '&': '&amp;'
            }[c]));
            return {html: '<code class="markdown-error">' + escaped + '</code>'};
        }
    });

    process.stdout.write(JSON.stringify(out));
});
