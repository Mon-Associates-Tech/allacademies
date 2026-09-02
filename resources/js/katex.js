const katex = require('katex');

let input = '';
process.stdin.on('data', c => (input += c));
process.stdin.on('end', () => {
    const items = JSON.parse(input || '[]');
    const out = items.map(({ tex, displayMode }) => {
        try {
            return {
                html: katex.renderToString(tex, {
                    displayMode,
                    throwOnError: false,
                    strict: false,
                    trust: true,
                    output: 'html',
                }),
            };
        } catch (e) {
            return { html: '<code>' + tex + '</code>' };
        }
    });
    process.stdout.write(JSON.stringify(out));
});
