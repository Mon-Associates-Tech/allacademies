@once
    @push('head')
        <script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
    @endpush
@endonce

{{--
    Rich Editor: Livewire component
    ─────────────────────────────────────────────────────────────────────────────
    Livewire usage (wire:model):
        <livewire:common.rich-editor wire:model="myProperty" :height="350" label="Body" />

    Plain-form usage (name= for POST forms):
        <livewire:common.rich-editor field-name="sections[0][title]" :value="$val" />

    Alpine x-for usage (do NOT use the Livewire component inside x-for).
    Use the global factory directly. Arguments: editorId, initialValue,
    fieldName, height, syncLivewire (false inside x-for):

        <div wire:ignore
             x-data="richEditorComponent('tpl-title-' + section._id, section.title || '', null, 300, false)"
             x-init="boot()">
            <textarea x-ref="hidden" class="hidden"
                      :name="'sections_config[' + idx + '][title]'"></textarea>
            <textarea :id="editorId" class="hidden"></textarea>
        </div>

    The editor dispatches a browser event 'rich-editor-updated' with { id, value }
    so parent Alpine components can also listen if needed.
--}}
<div
    wire:ignore
    x-data="richEditorComponent(@js($editorId), @js($value), @js($fieldName), @js($height))"
    x-init="boot()"
    data-rich-editor="{{ $editorId }}"
>
    @if($label)
        <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
            {{ $label }}@if($required)<span class="text-red-500 ml-0.5">*</span>@endif
        </label>
    @endif

    {{-- Hidden textarea: receives the HTML value for plain POST forms --}}
    @if($fieldName)
        <textarea name="{{ $fieldName }}" x-ref="hidden" class="hidden">{{ $value }}</textarea>
    @endif

    {{-- TinyMCE mounts here; wire:ignore keeps Livewire from touching this subtree --}}
    <textarea :id="editorId" class="hidden"></textarea>

    {{-- Rendered HTML preview (toggled via togglePreview()) --}}
    <div
        x-show="preview"
        x-html="previewHtml"
        class="markdown-preview prose prose-sm max-w-none dark:prose-invert p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 min-h-[120px]"
        style="border-radius: 2px; display: none;"
    ></div>
</div>

{{--
    NOTE: This script contains no Blade interpolation, so it can be moved as-is
    into a layout partial or resources/js/rich-editor.js. Do that if you need
    the factory on pages that never render the Livewire component (e.g. the
    x-for template form). The window guard below prevents double definition.
--}}
@once
<script>
/**
 * richEditorComponent: global Alpine data factory
 *
 * Works in:
 *  - Livewire components (syncs via $wire.setContent)
 *  - Plain POST forms   (writes to a hidden <textarea name="...">)
 *  - Alpine x-for loops (each instance has a unique editorId; pass syncLivewire = false)
 *
 * Never re-initialises TinyMCE on Livewire re-renders because the host
 * <div> carries wire:ignore.
 *
 * @param {string}      editorId      Unique DOM id for the TinyMCE textarea
 * @param {string}      initialValue  Initial HTML content
 * @param {string|null} fieldName     Form field name (informational)
 * @param {number}      height        Editor height in px
 * @param {boolean}     syncLivewire  Call setContent() on the closest Livewire component
 */
if (!window.richEditorComponent) {
    window.richEditorComponent = function (editorId, initialValue, fieldName, height = 300, syncLivewire = true) {
        return {
            editorId,
            fieldName,
            initialValue,
            preview: false,
            previewHtml: '',
            _editor: null,
            _booted: false,
            _destroyed: false,

            boot() {
                if (this._booted) return;
                this._booted = true;

                // Seed the hidden textarea so an unedited editor still submits its initial value
                if (this.$refs.hidden) {
                    this.$refs.hidden.value = this.initialValue || '';
                }

                const tryInit = () => {
                    if (this._destroyed) return;
                    if (typeof tinymce === 'undefined' || !document.getElementById(this.editorId)) {
                        return setTimeout(tryInit, 80);
                    }
                    this._initTinyMCE(this.initialValue);
                };

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', tryInit);
                } else {
                    tryInit();
                }
            },

            // Alpine calls this automatically when the element is removed (e.g. x-for section removed)
            destroy() {
                this._destroyed = true;
                if (typeof tinymce !== 'undefined') {
                    tinymce.get(this.editorId)?.remove();
                }
                this._editor = null;
            },

            _initTinyMCE(content) {
                const isDark = document.documentElement.classList.contains('dark');

                tinymce.init({
                    selector: '#' + this.editorId,
                    height: height,
                    menubar: false,
                    skin: isDark ? 'oxide-dark' : 'oxide',
                    content_css: isDark ? 'dark' : 'default',
                    plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount autoresize',
                    toolbar: 'undo redo | formatselect fontfamily fontsize | bold italic strikethrough forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image table | removeformat | code | help',
                    toolbar_mode: 'wrap',
                    content_style: "body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.5; background: #fff; color: #111; } body.dark { background: #1f2937 !important; color: #f3f4f6 !important; } .mce-content-body p { margin: 0 0 16px; } img { max-width: 100%; height: auto; }",
                    paste_as_text: false,
                    statusbar: false,
                    branding: false,
                    formats: {
                        bold: { inline: 'strong' },
                        italic: { inline: 'em' },
                        strikethrough: { inline: 'del' },
                    },
                    setup: (editor) => {
                        this._editor = editor;

                        editor.on('init', () => {
                            editor.setContent(content || '');
                            if (isDark) {
                                editor.getBody().classList.add('dark');
                                editor.getDoc().documentElement.classList.add('dark');
                            }
                        });

                        // Sync on every change with a small debounce
                        let _timer;
                        const sync = () => {
                            clearTimeout(_timer);
                            _timer = setTimeout(() => this._sync(editor.getContent()), 300);
                        };

                        editor.on('change keyup', sync);

                        // Immediate flush on blur so clicking Save never races the debounce
                        editor.on('blur', () => {
                            clearTimeout(_timer);
                            this._sync(editor.getContent());
                        });
                    },
                });
            },

            _sync(html) {
                // 1. Update hidden textarea for plain POST forms
                if (this.$refs.hidden) {
                    this.$refs.hidden.value = html;
                }

                // 2. Sync to Livewire only when this instance is the Livewire component's editor
                if (syncLivewire) {
                    const wireEl = this.$el.closest('[wire\\:id]');
                    if (wireEl) {
                        const component = Livewire.find(wireEl.getAttribute('wire:id'));
                        if (component) {
                            component.call('setContent', html);
                        }
                    }
                }

                // 3. Dispatch a browser event so parent Alpine scopes can listen
                this.$dispatch('rich-editor-updated', { id: this.editorId, value: html });

                // 4. Update preview if visible
                if (this.preview) this._renderPreview(html);
            },

            togglePreview() {
                this.preview = !this.preview;
                if (this.preview && this._editor) {
                    this._renderPreview(this._editor.getContent());
                }
            },

            _renderPreview(html) {
                this.previewHtml = html;
            },

            /** Public API: set content programmatically (e.g. from a parent component) */
            setContent(html) {
                if (this._editor) {
                    this._editor.setContent(html || '');
                }
                if (this.$refs.hidden) {
                    this.$refs.hidden.value = html || '';
                }
            },
        };
    };
}
</script>
@endonce