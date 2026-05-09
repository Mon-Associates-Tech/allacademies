<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paint – {{ $book->title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100vh; overflow: hidden; background: #008080; }
        paint-app { display: block; width: 100vw; height: 100vh; position: fixed; top: 0; left: 0; }
    </style>
    @vite(['resources/js/app.js', 'resources/js/paint.js'])
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

                    const maxWidth = window.innerWidth;
                    const maxHeight = window.innerHeight - 100;
                    let width = img.naturalWidth || img.width;
                    let height = img.naturalHeight || img.height;

                    if (width > maxWidth || height > maxHeight) {
                        const ratio = Math.min(maxWidth / width, maxHeight / height);
                        width = Math.floor(width * ratio);
                        height = Math.floor(height * ratio);
                    }

                    ctx.canvas.width = ctx.previewCanvas.width = width;
                    ctx.canvas.height = ctx.previewCanvas.height = height;
                    ctx.context.fillStyle = 'white';
                    ctx.context.fillRect(0, 0, width, height);
                    ctx.context.drawImage(img, 0, 0, width, height);
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
