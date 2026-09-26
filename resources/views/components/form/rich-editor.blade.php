@props(['name', 'value' => null, 'label' => null, 'height' => 400, 'info' => null, 'required' => false, 'livewire' => null])

@once
    @push('head')
        <script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
    @endpush
@endonce

@php
    $mark = old($name, $value);
    $editorId = 'rich-editor-' . str_replace(['[', ']', '.'], ['_', '_', '_'], $name);

    if (is_array($mark)) {
        $mark = \App\Support\Mark::fromArray($mark);
    }

    // The editor's local "down" var means "current raw text in the box" —
    // that's Mark's `up` (raw source). Never seed it from Mark's `down`
    $down = $mark instanceof \App\Support\Mark ? ($mark->up ?? '') : (string) $mark;
@endphp

<section>
    <div class="space-y-1"
         x-data="{
            preview: false,
            down: @js($down),
            up: '',
            editor: null,
            editorId: '{{ $editorId }}',
            initialized: false,
            livewireModel: @js($livewire),

            initEditor() {
                if (this.initialized || this.editor) return;

                this.$nextTick(() => {
                    const editorElement = document.getElementById(this.editorId);
                    if (!editorElement) return;

                    const isDark = document.documentElement.classList.contains('dark');

                    tinymce.init({
                        selector: '#' + this.editorId,
                        height: {{ $height }},
                        menubar: false,
                        skin: isDark ? 'oxide-dark' : 'oxide',
                        content_css: isDark ? 'dark' : 'default',
                        plugins: 'code lists table link image media paste markdown autoresize',
                        toolbar: 'undo redo | bold italic strikethrough | h1 h2 h3 | bullist numlist | link image table code | formatselect',
                        toolbar_mode: 'floating',
                        content_style: `
                            body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.5; background-color: #ffffff; color: #111827; }
                            body.dark { background-color: #1f2937 !important; color: #f3f4f6 !important; }
                            body.dark a { color: #60a5fa; }
                            .mce-content-body p { margin: 0 0 16px; }
                            img { max-width: 100%; height: auto; }
                        `,
                        paste_as_text: false,
                        setup: (editor) => {
                            this.editor = editor;
                            editor.on('init', () => {
                                this.initialized = true;
                                editor.setContent(this.down || '');
                                if (isDark) {
                                    editor.getBody().classList.add('dark');
                                    editor.getDoc().documentElement.classList.add('dark');
                                }
                            });

                            editor.on('change keyup', () => {
                                this.down = editor.getContent({format: 'markdown'});
                            });

                            editor.on('blur', () => {
                                if (this.livewireModel && window.Livewire) {
                                    $wire.set(this.livewireModel, this.down);
                                }
                            });
                        },
                        formats: {
                            bold: {inline: 'strong'},
                            italic: {inline: 'em'},
                            strikethrough: {inline: 'del'}
                        },
                        statusbar: false,
                        branding: false,
                        markdown: { output: 'markdown' }
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

            updatePreview() {
                if (!this.down || !this.down.trim()) {
                    this.up = '';
                    return;
                }

                let htmlContent = '';

                // Use marked if available, otherwise fallback
                if (typeof window.marked !== 'undefined') {
                    htmlContent = window.marked.parse(this.down);
                } else {
                    htmlContent = this.down;
                }

                // 🛡️ SECURITY: Sanitize HTML to prevent XSS in the preview
                if (typeof window.DOMPurify !== 'undefined') {
                    htmlContent = window.DOMPurify.sanitize(htmlContent, {
                        ADD_ATTR: ['target', 'alt', 'src', 'href', 'class', 'style'],
                        ALLOW_UNKNOWN_PROTOCOLS: true
                    });
                }

                this.up = htmlContent;

                this.$nextTick(() => {
                    this.renderMath();
                });
            },

            renderMath() {
                const previewElement = this.$refs.previewPane;
                if (!previewElement) return;

                if (typeof window.renderMathInElement !== 'undefined') {
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
                        trust: false
                    });
                }
            },

            syncToLivewire(value) {
                if (this.livewireModel && window.Livewire) {
                    clearTimeout(window['lw_sync_' + this.editorId]);
                    window['lw_sync_' + this.editorId] = setTimeout(() => {
                        $wire.set(this.livewireModel, value);
                    }, 500);
                }
            }
         }"
         x-init="initEditor()"
         x-effect="updatePreview(); syncToLivewire(down);"
         wire:ignore
         :data-editor-id="editorId">

        <label class="block text-sm tracking-tighter font-medium text-gray-700 dark:text-gray-300">
            {{ $label ?? ucfirst($name) }}
            @if(!empty($required))
                <span class="text-red-500">*</span>
            @endif
        </label>

        @if(!empty($info))
            <p class="text-xs tracking-tight !-mt-0 pb-1 text-gray-500 dark:text-gray-400">{{ $info }}</p>
        @endif

        <div class="border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden">
            <div class="bg-gray-50 dark:bg-gray-700 border-b border-gray-300 dark:border-gray-600 flex items-center">
                <div class="flex">
                    <button type="button" x-on:click="preview = false"
                            x-bind:class="!preview ? 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 border-b-0 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white'"
                            class="px-4 py-2 text-sm font-medium border-r transition-colors duration-200">Write
                    </button>
                    <button type="button" x-on:click="preview = true"
                            x-bind:class="preview ? 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 border-b-0 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white'"
                            class="px-4 py-2 text-sm font-medium transition-colors duration-200">Preview
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

            <div x-show="!preview" class="bg-white dark:bg-gray-800">
                <textarea x-bind:id="editorId" wire:key="{{ $editorId }}" name="{{ $name }}[markdown]"
                          x-model="down" class="w-full border-0 focus:ring-0 dark:bg-gray-800 dark:text-white"
                          style="min-height: {{ $height }}px; resize: vertical;"></textarea>
            </div>

            {{-- 🛡️ Added x-ref for KaTeX targeting and prose classes for images --}}
            <div x-show="preview" x-ref="previewPane" x-html="up"
                 class="markdown-preview markdown-body bg-white dark:bg-gray-800 p-4 overflow-auto prose prose-sm dark:prose-invert max-w-none"
                 style="display: none; min-height: {{ $height }}px;"></div>
        </div>

        {{--
            IMPORTANT: This textarea intentionally has NO `name` attribute.
            We do not want to submit the client-rendered HTML to the server.
            The server will render the markdown itself via the Mark cast.
        --}}
        <textarea x-model="up" x-bind:id="name + '_up'" class="hidden"></textarea>

        @error($name.'.down')
        <div class="text-xs font-medium text-red-600 dark:text-red-400 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <style>
        .katex { font-size: 1.1em; }
        .katex-display { margin: 1em 0; text-align: center; }
        .dark .katex { color: #e5e7eb; }
        .dark .katex .mord { color: #e5e7eb; }
        .dark .katex .mbin, .dark .katex .mrel, .dark .katex .mop { color: #9ca3af; }

        /* Replaced regex image manipulation with clean CSS */
        .markdown-preview img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin: 1rem 0;
        }
    </style>
</section>
