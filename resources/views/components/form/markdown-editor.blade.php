@once
    @push('head')
        <script src="{{ asset('js/tinymce/tinymce.js') }}" referrerpolicy="origin"></script>
        <script>
            // Define the function globally before Alpine loads
            window.markdownEditor = function(initialMarkdown, editorId, height, wireName) {
                return {
                    preview: false,
                    markdown: initialMarkdown,
                    previewHtml: '',
                    editor: null,
                    editorId: editorId,
                    wireName: wireName,
                    initialized: false,
                    isInitializing: true,

                    initEditor() {
                        if (this.initialized || this.editor) return;

                        // Wait for TinyMCE to be available
                        if (typeof tinymce === 'undefined') {
                            console.error('TinyMCE not loaded');
                            return;
                        }

                        this.$nextTick(() => {
                            const editorElement = document.getElementById(this.editorId);
                            if (!editorElement) {
                                console.error('Editor element not found:', this.editorId);
                                return;
                            }

                            // Detect dark mode
                            const isDarkMode = document.documentElement.classList.contains('dark');

                            tinymce.init({
                                selector: '#' + this.editorId,
                                height: height,
                                menubar: false,
                                plugins: 'lists link image code autoresize',
                                toolbar: 'undo redo | bold italic strikethrough | blocks | bullist numlist | link image code',
                                toolbar_mode: 'sliding',
                                block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2; Heading 3=h3',
                                forced_root_block: 'p',
                                skin: isDarkMode ? 'oxide-dark' : 'oxide',
                                content_css: isDarkMode ? 'dark' : 'default',
                                content_style: isDarkMode
                                    ? 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.5; background-color: #1f2937; color: #f3f4f6; } p { margin: 0 0 16px; } img { max-width: 100%; height: auto; }'
                                    : 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.5; } p { margin: 0 0 16px; } img { max-width: 100%; height: auto; }',
                                paste_as_text: false,
                                promotion: false,
                                branding: false,
                                relative_urls: false,
                                remove_script_host: false,
                                setup: (editor) => {
                                    this.editor = editor;

                                    editor.on('init', () => {
                                        this.initialized = true;
                                        if (this.markdown) {
                                            editor.setContent(this.markdown);
                                        }
                                        // Mark initialization complete after a short delay
                                        setTimeout(() => {
                                            this.isInitializing = false;
                                        }, 300);
                                        console.log('TinyMCE initialized successfully');
                                    });

                                    editor.on('input change blur', () => {
                                        // Only update local markdown, don't sync with Livewire automatically
                                        if (!this.isInitializing) {
                                            const content = editor.getContent();
                                            this.markdown = content;
                                            // Sync to Livewire if wireName is set and $wire is available
                                            if (this.wireName && typeof this.$wire !== 'undefined') {
                                                this.$wire.set(this.wireName, content);
                                            }
                                        }
                                    });

                                    // Sync content to hidden input on form submit
                                    const form = editorElement.closest('form');
                                    if (form) {
                                        form.addEventListener('submit', (e) => {
                                            // Get the latest content from TinyMCE
                                            const content = editor.getContent();
                                            // Update the Alpine markdown property which updates the hidden input
                                            this.markdown = content;
                                            // Also update the hidden input directly as a fallback
                                            const hiddenInput = this.$refs.hiddenInput;
                                            if (hiddenInput) {
                                                hiddenInput.value = content;
                                            }
                                            console.log('Form submit - syncing TinyMCE content:', content.substring(0, 100));
                                        }, true); // Use capture phase to ensure this runs first
                                    }
                                },
                                init_instance_callback: (editor) => {
                                    console.log('TinyMCE instance created:', editor.id);
                                },
                                statusbar: false
                            }).catch(error => {
                                console.error('TinyMCE initialization failed:', error);
                            });
                        });
                    },

updatePreview() {
                        this.previewHtml = this.markdown || '<p class="text-gray-400">No content to preview</p>';

                        // Render math after preview updates
                        this.$nextTick(() => {
                            const previewEl = this.$el.querySelector('.markdown-preview');
                            if (previewEl && typeof window.renderMathInElement !== 'undefined') {
                                try {
                                    window.renderMathInElement(previewEl, {
                                        delimiters: [
                                            {left: '$$', right: '$$', display: true},
                                            {left: '$', right: '$', display: false},
                                            {left: '\\[', right: '\\]', display: true},
                                            {left: '\\(', right: '\\)', display: false}
                                        ],
                                        throwOnError: false,
                                        errorColor: '#cc0000',
                                        strict: false,
                                        trust: true
                                    });
                                } catch(e) {
                                    console.error('KaTeX preview rendering error:', e);
                                }
                            }
                        });
                    },

                    // Update editor content from external source (e.g., Livewire)
                    setContent(newContent) {
                        this.markdown = newContent || '';
                        if (this.editor && this.initialized) {
                            this.isInitializing = true;
                            this.editor.setContent(this.markdown);
                            setTimeout(() => {
                                this.isInitializing = false;
                            }, 300);
                        }
                    }
                }
            };
        </script>
    @endpush
@endonce

@props(['name', 'value' => null, 'label' => null, 'height' => 400, 'info' => null, 'required' => false])

@php
    $markdown = old($name, $value);
    // Generate a unique ID and replace dots with underscores to make it a valid CSS selector
    $uniqueId = str_replace('.', '_', uniqid());
    $editorId = 'markdown-editor-' . str_replace(['[', ']', '.'], ['_', '_', '_'], $name) . '_' . $uniqueId;
@endphp

<div class="space-y-1"
     x-data="markdownEditor(@js($markdown ?? ''), @js($editorId), @js($height), @js($name))"
     x-init="
        $nextTick(() => {
            initEditor();
        });
        $watch('preview', (value) => {
            if (value) {
                updatePreview();
            }
        });
        // Watch for Livewire property changes using $wire.$watch
        if (wireName && typeof $wire !== 'undefined') {
            $wire.$watch(wireName, (newValue) => {
                if (newValue !== markdown) {
                    setContent(newValue);
                }
            });
        }
        // Also listen for custom event to update content
        $el.addEventListener('update-editor-content', (e) => {
            if (e.detail && e.detail.content !== undefined) {
                setContent(e.detail.content);
            }
        });
     "
     :data-editor-id="editorId">

    @if($label)
        <label class="block text-sm tracking-tighter font-medium text-gray-700 dark:text-gray-300">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    @if($info)
        <p class="text-xs tracking-tight !-mt-0 pb-1 text-gray-500 dark:text-gray-400">{{ $info }}</p>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>Rich text editor</span>
                </div>
            </div>
        </div>

        <!-- Hidden input to ensure content is submitted -->
        <input type="hidden" name="{{ $name }}" :value="markdown" x-ref="hiddenInput">

        <!-- Editor Area -->
        <div x-show="!preview" class="bg-white dark:bg-gray-800" wire:ignore>
            <textarea
                :id="editorId"
                class="w-full border-0 focus:ring-0 dark:bg-gray-800 dark:text-white"
                style="min-height: {{ $height }}px; resize: vertical;"></textarea>
        </div>

        <!-- Preview Area -->
        <div
            x-show="preview"
            class="markdown-preview bg-white dark:bg-gray-800 p-4 overflow-auto prose prose-sm dark:prose-invert max-w-none"
            style="display: none; min-height: {{ $height }}px;"
            x-html="previewHtml">
        </div>
    </div>

    @error($name)
    <div class="text-xs font-medium text-red-600 dark:text-red-400 mt-1">{{ $message }}</div>
    @enderror
</div>

<style>
    .markdown-preview {
        word-wrap: break-word;
    }

    .markdown-preview p {
        margin-bottom: 1em;
    }

    .markdown-preview h1,
    .markdown-preview h2,
    .markdown-preview h3 {
        margin-top: 1em;
        margin-bottom: 0.5em;
        font-weight: bold;
    }

    .markdown-preview ul,
    .markdown-preview ol {
        margin-left: 1.5em;
        margin-bottom: 1em;
    }

    .markdown-preview strong {
        font-weight: bold;
    }

    .markdown-preview em {
        font-style: italic;
    }

    .markdown-preview img {
        max-width: 100%;
        height: auto;
    }
</style>
