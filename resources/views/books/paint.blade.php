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
    @vite('resources/js/paint.js')
</head>
<body>
    <paint-app id="paint"></paint-app>

    <script type="module">
        const imageUrl = new URL(window.location.href).searchParams.get('imageUrl');

        if (imageUrl) {
            const paint = document.getElementById('paint');

            const loadImage = async () => {
                try {
                    const response = await fetch(imageUrl, { credentials: 'include' });
                    const blob = await response.blob();
                    const file = new File([blob], 'page.png', { type: 'image/png' });

                    const tryOpen = () => {
                        if (typeof paint.openFile === 'function') {
                            paint.openFile(file);
                        } else {
                            setTimeout(tryOpen, 100);
                        }
                    };
                    tryOpen();
                } catch (e) {
                    console.error('Failed to load image into Paint:', e);
                }
            };

            customElements.whenDefined('paint-app').then(loadImage);
        }
    </script>
</body>
</html>
