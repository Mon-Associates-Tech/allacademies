@props(['livewire', 'value' => '', 'label' => null, 'height' => 300, 'required' => false, 'info' => null])

@once
    @push('head')
        <script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
    @endpush
@endonce

@php
    $editorId = 'lw-editor-' . substr(md5($livewire . uniqid()), 0, 10);
    $content  = old($livewire, $value);
@endphp

<div wire:ignore>
    @if($label)
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            {{ $label }}@if($required) <span class="text-red-500">*</span>@endif
        </label>
    @endif
    @if($info)
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ $info }}</p>
    @endif
    <textarea id="{{ $editorId }}" class="w-full"></textarea>
</div>

<script>
(function () {
    const editorId  = @js($editorId);
    const initial   = @js($content);
    const lwProp    = @js($livewire);
    const height    = @js($height);
    const isDark    = document.documentElement.classList.contains('dark');

    function boot() {
        if (typeof tinymce === 'undefined' || !document.getElementById(editorId)) {
            return setTimeout(boot, 80);
        }

        tinymce.init({
            selector: '#' + editorId,
            height: height,
            menubar: false,
            skin: isDark ? 'oxide-dark' : 'oxide',
            content_css: isDark ? 'dark' : 'default',
            plugins: 'code lists table link image media paste autoresize',
            toolbar: 'undo redo | bold italic strikethrough | h1 h2 h3 | bullist numlist | link image table code | formatselect',
            toolbar_mode: 'floating',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.5; }',
            paste_as_text: false,
            statusbar: false,
            branding: false,
            setup(editor) {
                editor.on('init', () => editor.setContent(initial || ''));

                const sync = () => {
                    const el = document.getElementById(editorId);
                    const wireEl = el?.closest('[wire\\:id]') || el?.getRootNode()?.host?.closest('[wire\\:id]');
                    const wireId = wireEl?.getAttribute('wire:id');
                    const component = wireId ? Livewire.find(wireId) : null;
                    if (component) component.set(lwProp, editor.getContent());
                };

                editor.on('blur', sync);
                editor.on('change', sync);
            },
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
</script>
