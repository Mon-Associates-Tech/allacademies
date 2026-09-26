const mathjax = require('mathjax');

let inputData = '';
process.stdin.on('data', chunk => { inputData += chunk.toString(); });

process.stdin.on('end', () => {
    const text = inputData;
    if (!text.trim()) process.exit(0);

    mathjax.init({
        loader: { load: ['input/tex', 'output/svg'] },
        tex: { packages: { '[+]': ['ams'] } },
        // ✅ CRITICAL: 'none' forces MathJax to draw paths instead of using <use> tags.
        // Dompdf does not support <use> tags and will render blank boxes without this.
        output: { svg: { fontCache: 'none' } }
    }).then((MathJax) => {

        const regex = /\$\$(.*?)\$\$|\\\[(.*?)\\\]|\$(.*?)\$|\\\((.*?)\\\)/gs;

        const processedText = text.replace(regex, (match, p1, p2, p3, p4) => {
            let latex = '';
            let isDisplay = false;

            if (p1 !== undefined) { latex = p1; isDisplay = true; }
            else if (p2 !== undefined) { latex = p2; isDisplay = true; }
            else if (p3 !== undefined) { latex = p3; isDisplay = false; }
            else if (p4 !== undefined) { latex = p4; isDisplay = false; }

            latex = latex.trim();
            if (!latex) return match;

            try {
                const node = MathJax.tex2svg(latex, { display: isDisplay });
                let svg = MathJax.startup.adaptor.outerHTML(node);

                // Remove the container wrapper
                svg = svg.replace(/<mjx-container[^>]*>/i, '').replace(/<\/mjx-container>/i, '');

                // Ensure namespace is present
                if (!svg.includes('xmlns=')) {
                    svg = svg.replace('<svg', '<svg xmlns="http://www.w3.org/2000/svg"');
                }

                // ✅ CRITICAL: Convert to Base64 Image.
                // Dompdf renders <img> tags much more reliably than raw <svg> tags.
                const base64Svg = Buffer.from(svg).toString('base64');

                // Return an <img> tag. We use inline styles for alignment.
                return `<img src="data:image/svg+xml;base64,${base64Svg}" class="math-img" style="vertical-align: middle; max-height: 2em; display: inline-block;" />`;

            } catch (e) {
                process.stderr.write(`MathJax Error on "${latex}": ${e.message}\n`);
                return `[Math Error]`;
            }
        });

        process.stdout.write(processedText);
        process.exit(0);

    }).catch(err => {
        process.stderr.write(`MathJax Init Error: ${err.message}\n`);
        process.exit(1);
    });
});
