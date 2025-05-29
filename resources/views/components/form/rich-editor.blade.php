@once
    @push('head')
        <script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
    @endpush
@endonce

@props(['name', 'value' => null, 'label' => null, 'height' => 300])

@php
    $mark = old($name, $value);

    if (is_array($mark)) {
        $mark = \App\Support\Mark::fromArray($mark);
    }

    $down = $mark instanceof \App\Support\Mark ? ($mark->down ?? '') : (string) $mark;
    $up = $mark instanceof \App\Support\Mark ? ($mark->up ?? '') : '';
@endphp

<div class="space-y-1"
     x-data="{
        preview: false,
        down: @js($down),
        up: @js($up),
        editor: null,
        initEditor() {
            if (this.editor) return;

            tinymce.init({
                selector: '#{{ $name }}_down',
                height: {{ $height }},
                menubar: false,
                plugins: 'code lists table link image media paste markdown autoresize',
                toolbar: 'undo redo | bold italic strikethrough | h1 h2 h3 | bullist numlist | link image table code | formatselect',
                toolbar_mode: 'floating',
                content_style: `
                    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.5; }
                    .mce-content-body p { margin: 0 0 16px; }
                `,
                paste_as_text: true,
                setup: (editor) => {
                    this.editor = editor;
                    editor.on('change', () => {
                        this.down = editor.getContent({format: 'markdown'});
                    });
                },
                formats: {
                    bold: {inline: 'strong'},
                    italic: {inline: 'em'},
                    strikethrough: {inline: 'del'}
                },
                statusbar: false,
                branding: false,
                markdown: {
                    output: 'markdown'
                }
            });
        },
        updatePreview() {
            this.up = marked.parse(this.down);
        }
     }"
     x-init="initEditor(); updatePreview();"
     x-effect="updatePreview()"
     wire:ignore
>
    <label class="block text-sm tracking-wide font-medium text-gray-700">{{ $label ?? ucfirst($name) }}</label>

    <div class="border border-gray-300 rounded-lg overflow-hidden">
        <!-- Toolbar -->
        <div class="bg-gray-50 border-b border-gray-300 flex items-center">
            <div class="flex">
                <button
                    type="button"
                    x-on:click="preview = false"
                    x-bind:class="!preview ? 'bg-white border-gray-300 border-b-0 text-gray-900' : 'text-gray-600 hover:text-gray-900'"
                    class="px-4 py-2 text-sm font-medium border-r">
                    Write
                </button>
                <button
                    type="button"
                    x-on:click="preview = true"
                    x-bind:class="preview ? 'bg-white border-gray-300 border-b-0 text-gray-900' : 'text-gray-600 hover:text-gray-900'"
                    class="px-4 py-2 text-sm font-medium">
                    Preview
                </button>
            </div>
        </div>

        <!-- Editor Area -->
        <div x-show="!preview" class="bg-white">
            <textarea id="{{ $name }}_down" name="{{ $name }}[down]" x-model="down" class="w-full"></textarea>
        </div>

        <!-- Preview Area -->
        <div
            x-show="preview"
            x-html="up"
            class="markdown-body bg-white p-4 min-h-[{{ $height }}px] overflow-auto"
            style="display: none;">
        </div>
    </div>

    <!-- Store the HTML version -->
    <textarea x-model="up" id="{{ $name }}_up" name="{{ $name }}[up]" class="hidden"></textarea>

    @error($name.'.down')
        <div class="text-xs font-medium text-red-600 mt-1">{{ $message }}</div>
    @enderror
</div>
