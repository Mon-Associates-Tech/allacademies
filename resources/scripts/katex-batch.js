const katex = require('katex');

let input = '';
process.stdin.on('data', c => (input += c));
process.stdin.on('end', () => {
    let items;
    try {
        items = JSON.parse(input || '[]');
    } catch (e) {
        process.stderr.write('Invalid JSON input: ' + e.message);
        process.exit(1);
    }

    const out = items.map(({ tex, displayMode }) => {
        try {
            return {
                html: katex.renderToString(tex, {
                    displayMode: !!displayMode,
                    throwOnError: false,
                    strict: false,
                    trust: true,
                    output: 'html',
                }),
            };
        } catch (e) {
            // Fall back to a visibly-broken-but-safe render rather than
            // failing the whole batch over one bad expression.
            return { html: '<code class="katex-error">' + tex.replace(/[<>&]/g, c => ({'<':'&lt;','>':'&gt;','&':'&amp;'}[c])) + '</code>' };
        }
    });

    process.stdout.write(JSON.stringify(out));
});