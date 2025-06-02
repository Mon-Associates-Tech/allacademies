@once
    @push('head')
        <script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
    @endpush
@endonce

@props(['name', 'value' => null, 'label' => null, 'height' => 400])

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
                selector: '.rich-editor',
                height: {{ $height }},
                menubar: false,
                plugins: 'code lists table link image media paste markdown autoresize',
                toolbar: 'undo redo | bold italic strikethrough | h1 h2 h3 | bullist numlist | link image table code | formatselect',
                toolbar_mode: 'floating',
                content_style: `
                    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.5; }
                    .mce-content-body p { margin: 0 0 16px; }
                    img { max-width: 100%; height: auto; }
                `,
                paste_as_text: false, // Allow rich paste
                setup: (editor) => {
                    this.editor = editor;
                    editor.on('change', () => {
                        this.down = editor.getContent({format: 'markdown'});
                    });

                    // Convert plain URLs to markdown image syntax when pasting
                    editor.on('paste', (e) => {
                        setTimeout(() => {
                            let content = editor.getContent();
                            // Convert plain image URLs to markdown format
                            content = this.convertUrlsToMarkdown(content);
                            editor.setContent(content);
                        }, 100);
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

        // Convert plain image URLs to markdown image syntax
        convertUrlsToMarkdown(content) {
            // Match image URLs (common image extensions)
            const imageUrlRegex = /(https?:\/\/[^\s]+\.(?:jpg|jpeg|png|gif|webp|svg))   /gi;
            return content.replace(imageUrlRegex, (match) => {
                // Check if it's already in markdown format
                if (content.includes(`![`)) {
                    return match; // Already formatted
                }
                return `${match}`;
            });
        },



        updatePreview() {
            // Enhanced markdown parsing with better image support
            let markdownContent = this.down;

            // First, convert with marked.js
            let htmlContent = marked.parse(markdownContent);

htmlContent = htmlContent.replace(
    /<img([^>]+)>/gi,
    function(match, attributes) {
{{--        return '<img' + attributes + ' style="max-width: 100%; height: auto; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">';--}}
        return `<img alt=''  ${attributes}  style='max-width: 100%; height: auto; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);'>`;
}
);

// Also handle plain URLs that might not be converted
htmlContent = htmlContent.replace(
/(https?:\/\/[^\s]+\.(jpg|jpeg|png|gif|webp|svg))/gi,
`<img src='
     $1' alt='Image' style='max-width: 100%; height: auto; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);'>`
);

this.up = htmlContent;
},

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
                x-on:click="preview = true; updatePreview();"
                x-bind:class="preview ? 'bg-white border-gray-300 border-b-0 text-gray-900' : 'text-gray-600 hover:text-gray-900'"
                class="px-4 py-2 text-sm font-medium">
                Preview
            </button>
        </div>
    </div>

    <!-- Editor Area -->
    <div x-show="!preview" class="bg-white">
        <textarea id="{{ $name }}_down" name="{{ $name }}[down]" x-model="down" class="rich-editor w-full"></textarea>
    </div>

    <!-- Preview Area -->
    <div
        x-show="preview"
        x-html="up"
        class="markdown-body bg-white p-4 min-h-[{{ $height }}px] overflow-auto prose prose-sm max-w-none"
        style="display: none;">
    </div>
</div>

<!-- Store the HTML version -->
<textarea x-model="up" id="{{ $name }}_up" name="{{ $name }}[up]" class="hidden"></textarea>

@error($name.'.down')
<div class="text-xs font-medium text-red-600 mt-1">{{ $message }}</div>
@enderror
</div>
