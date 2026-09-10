<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paint – {{ $book->title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; overflow: hidden; background: #008080; display: flex; flex-direction: column; }
        #resize-bar { display: flex; align-items: center; gap: 8px; padding: 4px 8px; background: #c0c0c0; border-bottom: 2px solid #808080; font-size: 12px; user-select: none; }
        #resize-bar label { font-size: 11px; }
        #resize-bar input[type=number] { width: 70px; padding: 1px 4px; font-size: 11px; border: 1px inset #808080; }
        #resize-bar button { padding: 2px 10px; font-size: 11px; cursor: pointer; }
        #save-status { font-size: 11px; margin-left: auto; color: #444; }
        paint-app { flex: 1; min-height: 0; width: 100%; }
    </style>
    @vite(['resources/js/app.js', 'resources/js/paint.js'])
</head>
<body>
    <div id="resize-bar">
        <label>W:</label><input type="number" id="canvas-w" min="100" step="10">
        <label>H:</label><input type="number" id="canvas-h" min="100" step="10">
        <button id="apply-resize">Apply</button>
        <button id="reset-resize">Reset</button>
        <button id="save-paint" style="margin-left:8px;">💾 Save</button>
        <span id="save-status"></span>
    </div>
    <paint-app id="paint"></paint-app>

    <script type="module">
        const rawImageUrl = new URL(window.location.href).searchParams.get('imageUrl');
        const imageUrl = rawImageUrl ? new URL(rawImageUrl, window.location.origin).href : null;
        const pageParam = imageUrl ? (new URL(imageUrl).searchParams.get('page') ?? 0) : 0;
        const bookId = {{ $book->id }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            || '{{ csrf_token() }}';

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

            const loadImageElement = async (src) => {
                const response = await fetch(src, { credentials: 'same-origin' });
                if (!response.ok) throw new Error(`Fetch failed: ${response.status} ${response.statusText}`);
                const blob = await response.blob();
                console.log('Blob type:', blob.type, 'size:', blob.size);
                const blobUrl = URL.createObjectURL(blob);
                return new Promise((resolve, reject) => {
                    const img = new Image();
                    img.decoding = 'async';
                    img.onload = () => { URL.revokeObjectURL(blobUrl); resolve(img); };
                    img.onerror = (e) => { console.error('Blob image load error', blob.type, blob.size, blobUrl); reject(e); };
                    img.src = blobUrl;
                });
            };

            const loadImageIntoCanvas = async (src) => {
                try {
                    const ctx = await waitForDrawingContext();
                    const img = await loadImageElement(src);

                    const naturalWidth = img.naturalWidth || img.width;
                    const naturalHeight = img.naturalHeight || img.height;
                    const scale = Math.min(1, window.innerWidth / naturalWidth);
                    const width = Math.floor(naturalWidth * scale);
                    const height = Math.floor(naturalHeight * scale);
                    ctx.canvas.width = ctx.previewCanvas.width = width;
                    ctx.canvas.height = ctx.previewCanvas.height = height;
                    ctx.context.imageSmoothingEnabled = true;
                    ctx.context.imageSmoothingQuality = 'high';
                    ctx.context.fillStyle = 'white';
                    ctx.context.fillRect(0, 0, width, height);
                    ctx.context.drawImage(img, 0, 0, width, height);
                    ctx.document.title = 'page.png';
                    ctx.document.dirty = false;
                    document.getElementById('canvas-w').value = width;
                    document.getElementById('canvas-h').value = height;
                } catch (e) {
                    console.error('Failed to load image into Paint:', e, { imageUrl });
                }
            };

            const initPaint = async () => {
                // Try loading saved paint first, fall back to original PDF page
                const saved = await fetch(`/books/${bookId}/paint-data?page=${pageParam}`, { credentials: 'same-origin' })
                    .then(r => r.json()).catch(() => ({ url: null }));
                await loadImageIntoCanvas(saved.url || imageUrl);
            };

            customElements.whenDefined('paint-app').then(initPaint);

            document.getElementById('apply-resize').addEventListener('click', () => {
                const ctx = paint.drawingContext;
                if (!ctx?.canvas) return;
                const w = parseInt(document.getElementById('canvas-w').value);
                const h = parseInt(document.getElementById('canvas-h').value);
                if (!w || !h) return;
                const imageData = ctx.context.getImageData(0, 0, ctx.canvas.width, ctx.canvas.height);
                ctx.canvas.width = ctx.previewCanvas.width = w;
                ctx.canvas.height = ctx.previewCanvas.height = h;
                ctx.context.fillStyle = 'white';
                ctx.context.fillRect(0, 0, w, h);
                ctx.context.putImageData(imageData, 0, 0);
            });

            document.getElementById('reset-resize').addEventListener('click', () => {
                const ctx = paint.drawingContext;
                if (!ctx?.canvas) return;
                document.getElementById('canvas-w').value = ctx.canvas.width;
                document.getElementById('canvas-h').value = ctx.canvas.height;
            });

            document.getElementById('save-paint').addEventListener('click', async () => {
                const ctx = paint.drawingContext;
                if (!ctx?.canvas) return;
                const status = document.getElementById('save-status');
                status.textContent = 'Saving...';
                const blob = await new Promise(resolve => ctx.canvas.toBlob(resolve, 'image/png'));
                const form = new FormData();
                form.append('image', blob, 'paint.png');
                form.append('page', pageParam);
                const res = await fetch(`/books/${bookId}/paint-data`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    credentials: 'same-origin',
                    body: form,
                });
                status.textContent = res.ok ? 'Saved ✓' : 'Save failed ✗';
                setTimeout(() => status.textContent = '', 3000);
            });
        }
    </script>
</body>
</html>
