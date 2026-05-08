<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paint – {{ $book->title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; overflow: hidden; background: #008080; }
        paint-app { display: block; width: 100%; height: 100%; }
    </style>
    <!-- @vite('resources/js/paint.js') -->
</head>
<body>
    <paint-app id="paint"></paint-app>

    <script type="module">
        const imageUrl = new URL(window.location.href).searchParams.get('imageUrl');

        if (imageUrl) {
            const paint = document.getElementById('paint');
            const waitForDrawingContext = async (timeoutMs = 10000) => {
                const started = Date.now();
                while (Date.now() - started < timeoutMs) {
                    const ctx = paint?.drawingContext;
                    if (ctx?.canvas && ctx?.previewCanvas && ctx?.context) {
                        return ctx;
                    }
                    await new Promise((resolve) => setTimeout(resolve, 50));
                }
                throw new Error('Paint drawing context did not initialize in time.');
            };

            const loadImageElement = (src) => new Promise((resolve, reject) => {
                const img = new Image();
                img.decoding = 'async';
                img.onload = () => resolve(img);
                img.onerror = reject;
                img.src = src;
            });

            const loadImageIntoCanvas = async () => {
                try {
                    const ctx = await waitForDrawingContext();
                    const img = await loadImageElement(imageUrl);

                    ctx.canvas.width = ctx.previewCanvas.width = img.naturalWidth || img.width;
                    ctx.canvas.height = ctx.previewCanvas.height = img.naturalHeight || img.height;
                    ctx.context.fillStyle = 'white';
                    ctx.context.fillRect(0, 0, ctx.canvas.width, ctx.canvas.height);
                    ctx.context.drawImage(img, 0, 0, ctx.canvas.width, ctx.canvas.height);
                    ctx.document.title = 'page.png';
                    ctx.document.dirty = false;
                } catch (e) {
                    console.error('Failed to load image into Paint:', e, { imageUrl });
                }
            };

            customElements.whenDefined('paint-app').then(loadImageIntoCanvas);
        }
    </script>
</body>
</html>
