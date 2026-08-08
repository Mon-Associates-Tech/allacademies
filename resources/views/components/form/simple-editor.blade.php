@props(['name', 'value' => '', 'label' => null, 'height' => 300, 'required' => false])

@once
    @push('head')
        <script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
    @endpush
@endonce

@php
    $editorId = 'editor-' . str_replace(['[', ']', '.'], '_', $name) . '_' . uniqid();
    $content = old($name, $value);
@endphp

<div class="space-y-1">
    @if($label)
        <label for="{{ $editorId }}" class="block text-sm font-medium text-gray-700">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <textarea 
        id="{{ $editorId }}" 
        name="{{ $name }}"
        class="tinymce-simple-editor"
        {{ $attributes }}>{{ $content }}</textarea>

    @error($name)
        <p class="text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

@once
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof tinymce === 'undefined') return;
        
        const isDarkMode = document.documentElement.classList.contains('dark');
        
        tinymce.init({
            selector: '.tinymce-simple-editor',
            height: {{ $height }},
            menubar: false,
            plugins: 'lists link image table code',
            toolbar: 'undo redo | bold italic | bullist numlist | link image | code',
            statusbar: false,
            branding: false,
            skin: isDarkMode ? 'oxide-dark' : 'oxide',
            content_css: isDarkMode ? 'dark' : 'default',
            content_style: isDarkMode 
                ? 'body { background-color: #1f2937; color: #e5e7eb; }' 
                : 'body { background-color: #ffffff; color: #000000; }'
        });
    });
</script>
@endpush
@endonce
