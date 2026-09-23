import { readFileSync } from 'node:fs';
import { createRequire } from 'node:module';
import path from 'node:path';
import { createMarkedRenderer } from './create-renderer.mjs';

const require = createRequire(import.meta.url);

try {
    const raw = readFileSync(0, 'utf-8');
    const payload = JSON.parse(raw || '{}');

    const mode = payload.mode ?? 'html';
    const markdown = payload.markdown ?? '';
    const includeCss = payload.includeCss ?? true;

    const marked = createMarkedRenderer({
        output: mode === 'dompdf' ? 'html' : 'htmlAndMathml',
    });

    const html = marked.parse(markdown, { async: false });

    let css = '';

    if (includeCss) {
        const katexCssPath = require.resolve('katex/dist/katex.min.css');
        css = readFileSync(katexCssPath, 'utf8');

        const fontsDir = path.join(path.dirname(katexCssPath), 'fonts');

        css = css.replace(/url\(fonts\/([^?#)]+)[^)]*\)/g, (match, file) => {
            try {
                const fontPath = path.join(fontsDir, file);
                const data = readFileSync(fontPath);
                const ext = path.extname(file).toLowerCase();

                let mime = 'application/octet-stream';

                if (ext === '.woff2') mime = 'font/woff2';
                if (ext === '.woff') mime = 'font/woff';
                if (ext === '.ttf') mime = 'font/ttf';
                if (ext === '.otf') mime = 'font/otf';

                return `url(data:${mime};base64,${data.toString('base64')})`;
            } catch {
                return match;
            }
        });
    }

    process.stdout.write(
        JSON.stringify({
            html,
            css,
        })
    );
} catch (error) {
    process.stderr.write(
        JSON.stringify({
            message: error?.message ?? String(error),
        })
    );
    process.exit(1);
}
