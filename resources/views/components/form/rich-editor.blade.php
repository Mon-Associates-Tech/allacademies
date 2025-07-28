@once
    @push('head')
        <script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
    @endpush
@endonce

@props(['name', 'value' => null, 'label' => null, 'height' => 400, 'info' => null, 'required' => false])

@php
    $mark = old($name, $value);
    $editorId = 'rich-editor-' . str_replace(['[', ']', '.'], ['_', '_', '_'], $name) . '_' . uniqid();

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
        editorId: '{{ $editorId }}',
        initialized: false,

        initEditor() {
            if (this.initialized || this.editor) return;

            // Wait for DOM to be ready
            this.$nextTick(() => {
                const editorElement = document.getElementById(this.editorId);
                if (!editorElement) {
                    console.error('Editor element not found:', this.editorId);
                    return;
                }

                tinymce.init({
                    selector: '#' + this.editorId,
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
                    paste_as_text: false,
                    setup: (editor) => {
                        this.editor = editor;

                        editor.on('init', () => {
                            this.initialized = true;
                            // Set initial content
                            editor.setContent(this.down || '');
                        });

                        editor.on('change keyup', () => {
                            this.down = editor.getContent({format: 'markdown'});
                            this.updatePreview();
                        });

                        // Convert plain URLs to markdown image syntax when pasting
                        editor.on('paste', (e) => {
                            setTimeout(() => {
                                let content = editor.getContent();
                                content = this.convertUrlsToMarkdown(content);
                                editor.setContent(content);
                                this.down = editor.getContent({format: 'markdown'});
                                this.updatePreview();
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
                }).catch(error => {
                    console.error('TinyMCE initialization failed:', error);
                });
            });
        },

        destroyEditor() {
            if (this.editor) {
                try {
                    tinymce.remove('#' + this.editorId);
                    this.editor = null;
                    this.initialized = false;
                } catch (error) {
                    console.error('Error destroying editor:', error);
                }
            }
        },

        // Convert plain image URLs to markdown image syntax
        convertUrlsToMarkdown(content) {
            const imageUrlRegex = /(https?:\/\/[^\s]+\.(?:jpg|jpeg|png|gif|webp|svg))/gi;
            return content.replace(imageUrlRegex, (match) => {
                if (content.includes(`![`)) {
                    return match;
                }
                return `${match}`;
            });
        },

        updatePreview() {
            let markdownContent = this.down;
            let htmlContent = marked.parse(markdownContent);

            // Handle image styling
            htmlContent = htmlContent.replace(
                /<img([^>]+)>/gi,
                function(match, attributes) {
                    return `<img alt='' ${attributes} style='max-width: 100%; height: auto; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);'>`;
                }
            );

            htmlContent = htmlContent.replace(
                /(https?:\/\/[^\s]+\.(jpg|jpeg|png|gif|webp|svg))/gi,
                `<img src='$1' alt='Image' style='max-width: 100%; height: auto; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);'>`
            );

            this.up = htmlContent;

            // Render LaTeX after updating HTML content
            this.$nextTick(() => {
                this.renderMath();
            });
        },

        renderMath() {
            if (typeof window.renderMathInElement !== 'undefined') {
                const previewElement = document.querySelector(`[data-editor-id='${this.editorId}'] .markdown-preview`);
                if (previewElement) {
                    window.renderMathInElement(previewElement, {
                        delimiters: [
                            {left: '$$', right: '$$', display: true},
                            {left: '$', right: '$', display: false},
                            {left: '\\[', right: '\\]', display: true},
                            {left: '\\(', right: '\\)', display: false}
                        ],
                        throwOnError: false,
                        errorColor: '#cc0000',
                        strict: false,
                        trust: false,
                        macros: {
                            '\\f': '#1f(#2)'
                        }
                    });
                }
            }
        },

        // Livewire hook to sync with wire:model
        syncWithLivewire() {
            if (this.editor && this.initialized) {
                const currentContent = this.editor.getContent({format: 'markdown'});
                if (currentContent !== this.down) {
                    this.down = currentContent;
                    this.updatePreview();
                }
            }
        }
     }"
     x-init="
        initEditor();
        updatePreview();
        // Watch for external changes to down property
        $watch('down', () => {
            if (editor && initialized && editor.getContent({format: 'markdown'}) !== down) {
                editor.setContent(down || '');
            }
            updatePreview();
        });
     "
     x-effect="updatePreview()"
     wire:ignore
     :data-editor-id="editorId">

    <label class="block text-sm tracking-tighter font-medium text-gray-700 dark:text-gray-300">{{ $label ?? ucfirst($name) }}
        @if(!empty($required))
            <span class="text-red-500">*</span>
        @endif
    </label>
    @if(!empty($info))
        <p class="text-xs tracking-tight !-mt-0 pb-1 text-gray-500 dark:text-gray-400">{{$info}}</p>
    @endif

    <div class="border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden">
        <!-- Toolbar -->
        <div class="bg-gray-50 dark:bg-gray-700 border-b border-gray-300 dark:border-gray-600 flex items-center">
            <div class="flex">
                <button
                    type="button"
                    x-on:click="preview = false"
                    x-bind:class="!preview ? 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 border-b-0 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white'"
                    class="px-4 py-2 text-sm font-medium border-r transition-colors duration-200">
                    Write
                </button>
                <button
                    type="button"
                    x-on:click="preview = true; updatePreview();"
                    x-bind:class="preview ? 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 border-b-0 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white'"
                    class="px-4 py-2 text-sm font-medium transition-colors duration-200">
                    Preview
                </button>
            </div>
            <div class="ml-auto px-4 py-2">
                <div class="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span>LaTeX: $formula$ or $$formula$$</span>
                </div>
            </div>
        </div>

        <!-- Editor Area -->
        <div x-show="!preview" class="bg-white dark:bg-gray-800">
            <textarea
                x-bind:id="editorId"
                wire:key="{{ $editorId }}"
                name="{{ $name }}[down]"
                x-model="down"
                class="w-full border-0 focus:ring-0 dark:bg-gray-800 dark:text-white"
                style="min-height: {{ $height }}px; resize: vertical;"></textarea>
        </div>

        <!-- Preview Area -->
        <div
            x-show="preview"
            x-html="up"
            class="markdown-preview markdown-body bg-white dark:bg-gray-800 p-4 overflow-auto prose prose-sm dark:prose-invert max-w-none"
            style="display: none; min-height: {{ $height }}px;">
        </div>
    </div>

    <!-- Store the HTML version -->
    <textarea x-model="up" x-bind:id="name + '_up'" name="{{ $name }}[up]" class="hidden"></textarea>

    @error($name.'.down')
    <div class="text-xs font-medium text-red-600 dark:text-red-400 mt-1">{{ $message }}</div>
    @enderror
</div>

<style>
    /* KaTeX math styling */
    .katex {
        font-size: 1.1em;
    }

    .katex-display {
        margin: 1em 0;
        text-align: center;
    }

    /* Dark mode support for KaTeX */
    .dark .katex {
        color: #e5e7eb;
    }

    .dark .katex .mord {
        color: #e5e7eb;
    }

    .dark .katex .mbin,
    .dark .katex .mrel,
    .dark .katex .mop {
        color: #9ca3af;
    }
</style>
