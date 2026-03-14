@props([
    'text',
    'tooltip' => 'Copy',
    'class' => '',
    'showText' => null, // optional alternate text to display
    'buttonClass' => '', // extra classes for the button
])

<div class="inline-flex items-center gap-2 {{ $class }}">
    <span class="truncate text-sm text-gray-700 dark:text-gray-300">
        {{ $showText ?? $text }}
    </span>
    <button type="button"
            onclick="window.__copyAssignment('{{ addslashes($text) }}')"
            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 {{ $buttonClass }}"
            title="{{ $tooltip }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
        </svg>
    </button>
</div>

@once
    <div id="copy-toast" class="hidden fixed top-4 right-4 bg-gray-900 text-white px-4 py-2 rounded-lg shadow-lg text-sm z-50 opacity-0 transition-opacity duration-200">
        Copied to clipboard
    </div>

    <script>
        (function () {
            if (window.__copyAssignment) return;

            const toast = document.getElementById('copy-toast');
            let toastTimer = null;

            function copyText(text) {
                if (!text) return Promise.reject('No text');

                if (navigator.clipboard && window.isSecureContext) {
                    return navigator.clipboard.writeText(text);
                }

                return new Promise((resolve, reject) => {
                    const textarea = document.createElement('textarea');
                    textarea.value = text;
                    textarea.style.position = 'fixed';
                    textarea.style.top = '-9999px';
                    document.body.appendChild(textarea);
                    textarea.focus();
                    textarea.select();
                    try {
                        document.execCommand('copy');
                        resolve();
                    } catch (err) {
                        reject(err);
                    } finally {
                        document.body.removeChild(textarea);
                    }
                });
            }

            function showToast() {
                if (!toast) return;
                toast.classList.remove('hidden', 'opacity-0');
                toast.classList.add('opacity-100');
                clearTimeout(toastTimer);
                toastTimer = setTimeout(() => {
                    toast.classList.add('opacity-0');
                    setTimeout(() => toast.classList.add('hidden'), 200);
                }, 1400);
            }

            window.__copyAssignment = function (text) {
                copyText(text).then(showToast).catch(() => {});
            };
        })();
    </script>
@endonce
