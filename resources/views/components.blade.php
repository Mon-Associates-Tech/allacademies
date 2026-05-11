{{-- resources/views/components/docs.blade.php --}}
<x-layouts.app>
    <x-slot:title>Examinations Hub - Component Library</x-slot:title>

        @php
        // ── Mock Variables for Documentation Preview ──
        // These simulate the data a real controller would pass to the view.
        
        // For Table Sorting
        $sortBy = $sortBy ?? 'name';
        $sortOrder = $sortOrder ?? 'asc';
        
        // For Button Loading States
        $saving = $saving ?? false;
        
        // Ensure $errors bag exists (if not shared by middleware)
        if (!isset($errors)) {
            $errors = new \Illuminate\Support\ViewErrorBag;
        }
    @endphp

    <style>
        /* Syntax highlighting simulation */
        .code-highlight { color: #c9d1d9; background: #0d1117; font-family: 'SF Mono', 'Monaco', 'Consolas', monospace; }
        .code-tag { color: #7ee787; }
        .code-attr { color: #d2a8ff; }
        .code-string { color: #a5d6ff; }
        .code-comment { color: #8b949e; font-style: italic; }

        /* Scrollbar for code blocks */
        .code-scroll::-webkit-scrollbar { height: 8px; width: 8px; }
        .code-scroll::-webkit-scrollbar-track { background: #161b22; }
        .code-scroll::-webkit-scrollbar-thumb { background: #30363d; border-radius: 4px; }
        .code-scroll::-webkit-scrollbar-thumb:hover { background: #484f58; }
    </style>

    {{-- ═══════════════════════════════════════════════════════════
         PAGE SHELL
    ═══════════════════════════════════════════════════════════ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
         style="font-family: 'system-ui', -apple-system, sans-serif;">

        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden"
             style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #7c3aed, #a78bfa, #c4b5fd);"></div>
            <div class="px-7 py-6">
                <h1 class="text-2xl font-bold text-white leading-snug" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                    Examinations Hub Component Library
                </h1>
                <p class="text-slate-400 mt-2 text-sm">
                    Reusable UI components with preview, code snippets, and clipboard integration.
                </p>
            </div>
        </div>

        {{-- ── TABLE OF CONTENTS ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #2563eb, #60a5fa); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Components Index</h2>
            </div>
            <div class="p-5">
                <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach([
                        ['id' => 'ui-card', 'name' => 'UI Card', 'icon' => 'document'],
                        ['id' => 'ui-card-header', 'name' => 'UI Card Header', 'icon' => 'document-text'],
                        ['id' => 'ui-button', 'name' => 'UI Button', 'icon' => 'cursor-click'],
                        ['id' => 'ui-input', 'name' => 'UI Input', 'icon' => 'pencil'],
                        ['id' => 'ui-badge', 'name' => 'UI Badge', 'icon' => 'tag'],
                        ['id' => 'ui-metric-card', 'name' => 'UI Metric Card', 'icon' => 'chart-bar'],
                        ['id' => 'ui-table', 'name' => 'UI Table', 'icon' => 'table'],
                        ['id' => 'ui-table-header', 'name' => 'UI Table Header', 'icon' => 'bars-arrow-down'],
                        ['id' => 'ui-table-cell', 'name' => 'UI Table Cell', 'icon' => 'squares-2x2'],
                        ['id' => 'page-shell', 'name' => 'Page Shell Layout', 'icon' => 'layout-grid'],
                    ] as $component)
                    <a href="#{{ $component['id'] }}" 
                       class="flex items-center gap-3 p-3 rounded-[2px] border border-slate-200 dark:border-slate-700 hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors text-sm font-medium text-slate-700 dark:text-slate-300">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        {{ $component['name'] }}
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             COMPONENT: UI Card
        ═══════════════════════════════════════════════════════════ --}}
        <div id="ui-card" class="space-y-5">
            <x-ui.card>
                <x-ui.card-header title="UI Card" accent="primary" subtitle="Base container with shadow, border, and background variants"/>
            </x-ui.card>

            {{-- Preview --}}
            <x-ui.card>
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider">Preview</h3>
                    </div>
                </div>
                <div class="p-5 bg-slate-50 dark:bg-slate-800/30">
                    <div class="grid md:grid-cols-3 gap-4">
                        {{-- Default --}}
                        <x-ui.card>
                            <div class="p-5">
                                <h4 class="font-semibold text-slate-900 dark:text-white mb-2">Default Card</h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400">Standard card with subtle border and shadow.</p>
                            </div>
                        </x-ui.card>

                        {{-- Success --}}
                        <x-ui.card variant="accent" accent="success">
                            <div class="p-5">
                                <h4 class="font-semibold text-emerald-900 dark:text-emerald-100 mb-2">Success Variant</h4>
                                <p class="text-sm text-emerald-700 dark:text-emerald-300">Used for confirmations and positive states.</p>
                            </div>
                        </x-ui.card>

                        {{-- Warning --}}
                        <x-ui.card variant="accent" accent="warning">
                            <div class="p-5">
                                <h4 class="font-semibold text-amber-900 dark:text-amber-100 mb-2">Warning Variant</h4>
                                <p class="text-sm text-amber-700 dark:text-amber-300">Used for alerts and attention-required states.</p>
                            </div>
                        </x-ui.card>
                    </div>
                </div>
            </x-ui.card>

            {{-- Code Snippet --}}
            <div x-data="codeSnippet" class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-1 h-5" style="background: linear-gradient(180deg, #2563eb, #60a5fa); border-radius: 1px;"></div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider">Usage</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="revealCode = !revealCode" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border rounded-[2px] transition-colors hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="revealCode ? 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' : 'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21'"/>
                            </svg>
                            <span x-text="revealCode ? 'Hide Code' : 'Show Code'"></span>
                        </button>
                        <button @click="copyToClipboard(`@php\n\n// Default Card\n<x-ui.card>\n    <div class=\"p-5\">\n        <h4 class=\"font-semibold text-slate-900 dark:text-white mb-2\">Title</h4>\n        <p class=\"text-sm text-slate-600 dark:text-slate-400\">Content goes here...</p>\n    </div>\n</x-ui.card>\n\n// Accent Variant\n<x-ui.card variant=\"accent\" accent=\"success\">\n    <div class=\"p-5\">\n        <h4 class=\"font-semibold text-emerald-900 dark:text-emerald-100 mb-2\">Success</h4>\n        <p class=\"text-sm text-emerald-700 dark:text-emerald-300\">Success variant with colored background.</p>\n    </div>\n</x-ui.card>\n\n// With Header\n<x-ui.card>\n    <x-ui.card-header title=\"Card Title\" subtitle=\"Optional subtitle\" accent=\"primary\"/>\n    <div class=\"p-5\">\n        <p class=\"text-sm text-slate-700 dark:text-slate-300\">Content goes here...</p>\n    </div>\n</x-ui.card>\n@endphp`)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border rounded-[2px] transition-colors hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="copied ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3'"/>
                            </svg>
                            <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                        </button>
                    </div>
                </div>
                <div x-show="revealCode" x-collapse class="border-t border-slate-100 dark:border-slate-800">
                    <div class="p-5 overflow-x-auto code-scroll">
                        <pre class="text-sm leading-relaxed"><code class="code-highlight"><span class="code-comment"># Default Card</span>
&lt;<span class="code-tag">x-ui.card</span>&gt;
    &lt;<span class="code-tag">div</span> <span class="code-attr">class</span>=<span class="code-string">"p-5"</span>&gt;
        &lt;<span class="code-tag">h4</span>&gt;Title&lt;/<span class="code-tag">h4</span>&gt;
        &lt;<span class="code-tag">p</span>&gt;Content goes here...&lt;/<span class="code-tag">p</span>&gt;
    &lt;/<span class="code-tag">div</span>&gt;
&lt;/<span class="code-tag">x-ui.card</span>&gt;

<span class="code-comment"># Accent Variants: primary, success, info, warning, danger</span>
&lt;<span class="code-tag">x-ui.card</span> <span class="code-attr">variant</span>=<span class="code-string">"accent"</span> <span class="code-attr">accent</span>=<span class="code-string">"success"</span>&gt;
    &lt;<span class="code-tag">div</span> <span class="code-attr">class</span>=<span class="code-string">"p-5"</span>&gt;
        &lt;<span class="code-tag">h4</span>&gt;Success Card&lt;/<span class="code-tag">h4</span>&gt;
        &lt;<span class="code-tag">p</span>&gt;Content...&lt;/<span class="code-tag">p</span>&gt;
    &lt;/<span class="code-tag">div</span>&gt;
&lt;/<span class="code-tag">x-ui.card</span>&gt;

<span class="code-comment"># With Header Component</span>
&lt;<span class="code-tag">x-ui.card</span>&gt;
    &lt;<span class="code-tag">x-ui.card-header</span> 
        <span class="code-attr">title</span>=<span class="code-string">"Card Title"</span> 
        <span class="code-attr">subtitle</span>=<span class="code-string">"Optional subtitle"</span> 
        <span class="code-attr">accent</span>=<span class="code-string">"primary"</span>
        <span class="code-attr">:actions</span>=<span class="code-string">"&lt;button&gt;Action&lt;/button&gt;"</span> /&gt;
    &lt;<span class="code-tag">div</span> <span class="code-attr">class</span>=<span class="code-string">"p-5"</span>&gt;
        &lt;<span class="code-tag">p</span>&gt;Content...&lt;/<span class="code-tag">p</span>&gt;
    &lt;/<span class="code-tag">div</span>&gt;
&lt;/<span class="code-tag">x-ui.card</span>&gt;</code></pre>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             COMPONENT: UI Button
        ═══════════════════════════════════════════════════════════ --}}
        <div id="ui-button" class="space-y-5 pt-8">
            <x-ui.card>
                <x-ui.card-header title="UI Button" accent="success" subtitle="Consistent button variants with size options and loading states"/>
            </x-ui.card>

            {{-- Preview --}}
            <x-ui.card>
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5" style="background: linear-gradient(180deg, #059669, #10b981); border-radius: 1px;"></div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider">Preview</h3>
                </div>
                <div class="p-5 space-y-6">
                    {{-- Variants --}}
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">Variants</p>
                        <div class="flex flex-wrap gap-3">
                            <x-ui.button variant="primary">Primary</x-ui.button>
                            <x-ui.button variant="secondary">Secondary</x-ui.button>
                            <x-ui.button variant="success">Success</x-ui.button>
                            <x-ui.button variant="danger">Danger</x-ui.button>
                            <x-ui.button variant="ghost">Ghost</x-ui.button>
                        </div>
                    </div>

                    {{-- Sizes --}}
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">Sizes</p>
                        <div class="flex flex-wrap items-end gap-3">
                            <x-ui.button variant="primary" size="sm">Small</x-ui.button>
                            <x-ui.button variant="primary" size="md">Medium</x-ui.button>
                            <x-ui.button variant="primary" size="lg">Large</x-ui.button>
                        </div>
                    </div>

                    {{-- With Icons --}}
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">With Icons</p>
                        <div class="flex flex-wrap gap-3">
                            <x-ui.button variant="primary" icon="arrow-left">Back</x-ui.button>
                            <x-ui.button variant="success" icon="check">Save</x-ui.button>
                            <x-ui.button variant="ghost" icon="arrow-right" icon-right>Next</x-ui.button>
                        </div>
                    </div>

                    {{-- Loading --}}
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">Loading State</p>
                        <x-ui.button variant="primary" loading>Loading...</x-ui.button>
                    </div>
                </div>
            </x-ui.card>

            {{-- Code --}}
            <div x-data="codeSnippet" class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-1 h-5" style="background: linear-gradient(180deg, #059669, #10b981); border-radius: 1px;"></div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider">Usage</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="revealCode = !revealCode" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border rounded-[2px] transition-colors hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="revealCode ? 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' : 'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21'"/>
                            </svg>
                            <span x-text="revealCode ? 'Hide Code' : 'Show Code'"></span>
                        </button>
                        <button @click="copyToClipboard(`&lt;x-ui.button variant=\"primary\" size=\"md\" icon=\"check\"&gt;\n    Save Changes\n&lt;/x-ui.button&gt;\n\n&lt;x-ui.button variant=\"danger\" icon=\"trash\" icon-right :loading=\"saving\"&gt;\n    Delete\n&lt;/x-ui.button&gt;\n\n&lt;x-ui.button variant=\"ghost\" size=\"sm\"&gt;\n    Cancel\n&lt;/x-ui.button&gt;`)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border rounded-[2px] transition-colors hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="copied ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3'"/>
                            </svg>
                            <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                        </button>
                    </div>
                </div>
                <div x-show="revealCode" x-collapse class="border-t border-slate-100 dark:border-slate-800">
                    <div class="p-5 overflow-x-auto code-scroll">
                        <pre class="text-sm leading-relaxed"><code class="code-highlight"><span class="code-comment"># Basic Usage</span>
&lt;<span class="code-tag">x-ui.button</span> <span class="code-attr">variant</span>=<span class="code-string">"primary"</span>&gt;Primary&lt;/<span class="code-tag">x-ui.button</span>&gt;
&lt;<span class="code-tag">x-ui.button</span> <span class="code-attr">variant</span>=<span class="code-string">"secondary"</span>&gt;Secondary&lt;/<span class="code-tag">x-ui.button</span>&gt;
&lt;<span class="code-tag">x-ui.button</span> <span class="code-attr">variant</span>=<span class="code-string">"success"</span>&gt;Success&lt;/<span class="code-tag">x-ui.button</span>&gt;
&lt;<span class="code-tag">x-ui.button</span> <span class="code-attr">variant</span>=<span class="code-string">"danger"</span>&gt;Danger&lt;/<span class="code-tag">x-ui.button</span>&gt;
&lt;<span class="code-tag">x-ui.button</span> <span class="code-attr">variant</span>=<span class="code-string">"ghost"</span>&gt;Ghost&lt;/<span class="code-tag">x-ui.button</span>&gt;

<span class="code-comment"># Sizes: sm, md, lg</span>
&lt;<span class="code-tag">x-ui.button</span> <span class="code-attr">variant</span>=<span class="code-string">"primary"</span> <span class="code-attr">size</span>=<span class="code-string">"sm"</span>&gt;Small&lt;/<span class="code-tag">x-ui.button</span>&gt;
&lt;<span class="code-tag">x-ui.button</span> <span class="code-attr">variant</span>=<span class="code-string">"primary"</span> <span class="code-attr">size</span>=<span class="code-string">"md"</span>&gt;Medium&lt;/<span class="code-tag">x-ui.button</span>&gt;
&lt;<span class="code-tag">x-ui.button</span> <span class="code-attr">variant</span>=<span class="code-string">"primary"</span> <span class="code-attr">size</span>=<span class="code-string">"lg"</span>&gt;Large&lt;/<span class="code-tag">x-ui.button</span>&gt;

<span class="code-comment"># With Icons (requires heroicons)</span>
&lt;<span class="code-tag">x-ui.button</span> <span class="code-attr">variant</span>=<span class="code-string">"success"</span> <span class="code-attr">icon</span>=<span class="code-string">"check"</span>&gt;Save&lt;/<span class="code-tag">x-ui.button</span>&gt;
&lt;<span class="code-tag">x-ui.button</span> <span class="code-attr">variant</span>=<span class="code-string">"ghost"</span> <span class="code-attr">icon</span>=<span class="code-string">"arrow-right"</span> <span class="code-attr">icon-right</span>&gt;Next&lt;/<span class="code-tag">x-ui.button</span>&gt;

<span class="code-comment"># Loading State</span>
&lt;<span class="code-tag">x-ui.button</span> <span class="code-attr">variant</span>=<span class="code-string">"primary"</span> <span class="code-attr">:loading</span>=<span class="code-string">"saving"</span>&gt;Save&lt;/<span class="code-tag">x-ui.button</span>&gt;

<span class="code-comment"># Full Width</span>
&lt;<span class="code-tag">x-ui.button</span> <span class="code-attr">variant</span>=<span class="code-string">"primary"</span> <span class="code-attr">full-width</span>&gt;Submit&lt;/<span class="code-tag">x-ui.button</span>&gt;</code></pre>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             COMPONENT: UI Input
        ═══════════════════════════════════════════════════════════ --}}
        <div id="ui-input" class="space-y-5 pt-8">
            <x-ui.card>
                <x-ui.card-header title="UI Input" accent="info" subtitle="Form inputs with labels, hints, errors, and icon support"/>
            </x-ui.card>

            {{-- Preview --}}
            <x-ui.card>
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5" style="background: linear-gradient(180deg, #2563eb, #60a5fa); border-radius: 1px;"></div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider">Preview</h3>
                </div>
                <div class="p-5 space-y-6 max-w-lg">
                    <x-ui.input 
                        label="Email Address" 
                        name="email" 
                        type="email" 
                        required 
                        icon="envelope"
                        hint="We'll never share your email."
                        placeholder="user@example.com"/>

                    <x-ui.input 
                        label="Password" 
                        name="password" 
                        type="password" 
                        required 
                        icon="lock-closed"
                        :error="$errors->first('password') ?? 'Password must be at least 8 characters'"
                        placeholder="••••••••"/>

                    <x-ui.input 
                        label="Search" 
                        name="search" 
                        icon="magnifying-glass"
                        placeholder="Type to search..." />
                </div>
            </x-ui.card>

            {{-- Code --}}
            <div x-data="codeSnippet" class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-1 h-5" style="background: linear-gradient(180deg, #2563eb, #60a5fa); border-radius: 1px;"></div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider">Usage</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="revealCode = !revealCode" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border rounded-[2px] transition-colors hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="revealCode ? 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' : 'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21'"/>
                            </svg>
                            <span x-text="revealCode ? 'Hide Code' : 'Show Code'"></span>
                        </button>
                        <button @click="copyToClipboard(`&lt;x-ui.input \n    label=\"Email Address\" \n    name=\"email\" \n    type=\"email\" \n    required \n    icon=\"envelope\"\n    hint=\"We'll never share your email.\"\n    placeholder=\"user@example.com\"\n    :error=\"$errors->first('email')\"\n/&gt;`)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border rounded-[2px] transition-colors hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="copied ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3'"/>
                            </svg>
                            <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                        </button>
                    </div>
                </div>
                <div x-show="revealCode" x-collapse class="border-t border-slate-100 dark:border-slate-800">
                    <div class="p-5 overflow-x-auto code-scroll">
                        <pre class="text-sm leading-relaxed"><code class="code-highlight">&lt;<span class="code-tag">x-ui.input</span> 
    <span class="code-attr">label</span>=<span class="code-string">"Email Address"</span> 
    <span class="code-attr">name</span>=<span class="code-string">"email"</span> 
    <span class="code-attr">type</span>=<span class="code-string">"email"</span> 
    <span class="code-attr">required</span> 
    <span class="code-attr">icon</span>=<span class="code-string">"envelope"</span>
    <span class="code-attr">hint</span>=<span class="code-string">"We'll never share your email."</span>
    <span class="code-attr">placeholder</span>=<span class="code-string">"user@example.com"</span>
    <span class="code-attr">:error</span>=<span class="code-string">"$errors->first('email')"</span>
/&gt;</code></pre>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             COMPONENT: UI Badge
        ═══════════════════════════════════════════════════════════ --}}
        <div id="ui-badge" class="space-y-5 pt-8">
            <x-ui.card>
                <x-ui.card-header title="UI Badge" accent="warning" subtitle="Status indicators with color variants and sizes"/>
            </x-ui.card>

            {{-- Preview --}}
            <x-ui.card>
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5" style="background: linear-gradient(180deg, #d97706, #f59e0b); border-radius: 1px;"></div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider">Preview</h3>
                </div>
                <div class="p-5 space-y-6">
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">Variants</p>
                        <div class="flex flex-wrap gap-3">
                            <x-ui.badge variant="default">Default</x-ui.badge>
                            <x-ui.badge variant="success">Success</x-ui.badge>
                            <x-ui.badge variant="info">Info</x-ui.badge>
                            <x-ui.badge variant="warning">Warning</x-ui.badge>
                            <x-ui.badge variant="danger">Danger</x-ui.badge>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">Sizes</p>
                        <div class="flex flex-wrap gap-3">
                            <x-ui.badge variant="info" size="sm">Small</x-ui.badge>
                            <x-ui.badge variant="info" size="md">Medium</x-ui.badge>
                        </div>
                    </div>
                </div>
            </x-ui.card>

            {{-- Code --}}
            <div x-data="codeSnippet" class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-1 h-5" style="background: linear-gradient(180deg, #d97706, #f59e0b); border-radius: 1px;"></div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider">Usage</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="revealCode = !revealCode" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border rounded-[2px] transition-colors hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="revealCode ? 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' : 'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21'"/>
                            </svg>
                            <span x-text="revealCode ? 'Hide Code' : 'Show Code'"></span>
                        </button>
                        <button @click="copyToClipboard(`&lt;x-ui.badge variant=\"success\" size=\"md\"&gt;Active&lt;/x-ui.badge&gt;`)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border rounded-[2px] transition-colors hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="copied ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3'"/>
                            </svg>
                            <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                        </button>
                    </div>
                </div>
                <div x-show="revealCode" x-collapse class="border-t border-slate-100 dark:border-slate-800">
                    <div class="p-5 overflow-x-auto code-scroll">
                        <pre class="text-sm leading-relaxed"><code class="code-highlight">&lt;<span class="code-tag">x-ui.badge</span> <span class="code-attr">variant</span>=<span class="code-string">"success"</span> <span class="code-attr">size</span>=<span class="code-string">"md"</span>&gt;Active&lt;/<span class="code-tag">x-ui.badge</span>&gt;
&lt;<span class="code-tag">x-ui.badge</span> <span class="code-attr">variant</span>=<span class="code-string">"warning"</span> <span class="code-attr">size</span>=<span class="code-string">"sm"</span>&gt;Pending&lt;/<span class="code-tag">x-ui.badge</span>&gt;
&lt;<span class="code-tag">x-ui.badge</span> <span class="code-attr">variant</span>=<span class="code-string">"danger"</span>&gt;Error&lt;/<span class="code-tag">x-ui.badge</span>&gt;</code></pre>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             COMPONENT: UI Metric Card
        ═══════════════════════════════════════════════════════════ --}}
        <div id="ui-metric-card" class="space-y-5 pt-8">
            <x-ui.card>
                <x-ui.card-header title="UI Metric Card" accent="primary" subtitle="Data display cards with icon, label, value, and trend indicator"/>
            </x-ui.card>

            {{-- Preview --}}
            <x-ui.card>
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider">Preview</h3>
                </div>
                <div class="p-5 bg-slate-50 dark:bg-slate-800/30">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <x-ui.metric-card label="Total Users" value="1,234" icon="users" accent="info" trend="up"/>
                        <x-ui.metric-card label="Revenue" value="$45.2k" suffix="USD" icon="currency-dollar" accent="success"/>
                        <x-ui.metric-card label="Pending" value="23" icon="clock" accent="warning" trend="down"/>
                        <x-ui.metric-card label="Errors" value="3" icon="exclamation-triangle" accent="danger"/>
                    </div>
                </div>
            </x-ui.card>

            {{-- Code --}}
            <div x-data="codeSnippet" class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider">Usage</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="revealCode = !revealCode" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border rounded-[2px] transition-colors hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="revealCode ? 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' : 'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21'"/>
                            </svg>
                            <span x-text="revealCode ? 'Hide Code' : 'Show Code'"></span>
                        </button>
                        <button @click="copyToClipboard(`&lt;x-ui.metric-card \n    label=\"Total Submissions\" \n    value=\"1,234\" \n    icon=\"document\"\n    accent=\"primary\"\n    trend=\"up\"\n/&gt;`)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border rounded-[2px] transition-colors hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="copied ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3'"/>
                            </svg>
                            <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                        </button>
                    </div>
                </div>
                <div x-show="revealCode" x-collapse class="border-t border-slate-100 dark:border-slate-800">
                    <div class="p-5 overflow-x-auto code-scroll">
                        <pre class="text-sm leading-relaxed"><code class="code-highlight">&lt;<span class="code-tag">x-ui.metric-card</span> 
    <span class="code-attr">label</span>=<span class="code-string">"Total Submissions"</span> 
    <span class="code-attr">value</span>=<span class="code-string">"1,234"</span> 
    <span class="code-attr">suffix</span>=<span class="code-string">"%"</span> <span class="code-comment">// optional</span>
    <span class="code-attr">icon</span>=<span class="code-string">"document"</span>
    <span class="code-attr">accent</span>=<span class="code-string">"primary"</span> <span class="code-comment">// primary, success, info, warning, danger</span>
    <span class="code-attr">trend</span>=<span class="code-string">"up"</span> <span class="code-comment">// up, down, neutral</span>
/&gt;</code></pre>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             COMPONENT: UI Table
        ═══════════════════════════════════════════════════════════ --}}
        <div id="ui-table" class="space-y-5 pt-8">
            <x-ui.card>
                <x-ui.card-header title="UI Table" accent="info" subtitle="Responsive tables with sortable headers and aligned cells"/>
            </x-ui.card>

            {{-- Preview --}}
            <x-ui.card>
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5" style="background: linear-gradient(180deg, #2563eb, #60a5fa); border-radius: 1px;"></div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider">Preview</h3>
                </div>
                <div class="p-5">
                    <x-ui.table>
                        <x-slot:header>
                            <x-ui.table-header sortable sort="name" :direction="$sortBy === 'name' ? $sortOrder : null">
                                Name
                            </x-ui.table-header>
                            <x-ui.table-header align="center">Status</x-ui.table-header>
                            <x-ui.table-header align="right">Actions</x-ui.table-header>
                        </x-slot:header>
                        
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                            <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors">
                                <x-ui.table-cell>
                                    <span class="font-semibold text-slate-900 dark:text-white">Alice Johnson</span>
                                </x-ui.table-cell>
                                <x-ui.table-cell align="center">
                                    <x-ui.badge variant="success">Active</x-ui.badge>
                                </x-ui.table-cell>
                                <x-ui.table-cell align="right">
                                    <a href="#" class="text-amber-700 dark:text-amber-400 hover:text-amber-900 transition-colors text-xs font-semibold">View →</a>
                                </x-ui.table-cell>
                            </tr>
                            <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors">
                                <x-ui.table-cell>
                                    <span class="font-semibold text-slate-900 dark:text-white">Bob Smith</span>
                                </x-ui.table-cell>
                                <x-ui.table-cell align="center">
                                    <x-ui.badge variant="warning">Pending</x-ui.badge>
                                </x-ui.table-cell>
                                <x-ui.table-cell align="right">
                                    <a href="#" class="text-amber-700 dark:text-amber-400 hover:text-amber-900 transition-colors text-xs font-semibold">View →</a>
                                </x-ui.table-cell>
                            </tr>
                            <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors">
                                <x-ui.table-cell>
                                    <span class="font-semibold text-slate-900 dark:text-white">Carol Davis</span>
                                </x-ui.table-cell>
                                <x-ui.table-cell align="center">
                                    <x-ui.badge variant="danger">Inactive</x-ui.badge>
                                </x-ui.table-cell>
                                <x-ui.table-cell align="right">
                                    <a href="#" class="text-amber-700 dark:text-amber-400 hover:text-amber-900 transition-colors text-xs font-semibold">View →</a>
                                </x-ui.table-cell>
                            </tr>
                        </tbody>
                    </x-ui.table>
                </div>
            </x-ui.card>

            {{-- Code --}}
            <div x-data="codeSnippet" class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-1 h-5" style="background: linear-gradient(180deg, #2563eb, #60a5fa); border-radius: 1px;"></div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider">Usage</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="revealCode = !revealCode" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border rounded-[2px] transition-colors hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="revealCode ? 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' : 'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21'"/>
                            </svg>
                            <span x-text="revealCode ? 'Hide Code' : 'Show Code'"></span>
                        </button>
                        <button @click="copyToClipboard(`&lt;x-ui.table&gt;\n    &lt;x-slot:header&gt;\n        &lt;x-ui.table-header sortable sort=\"name\" :direction=\"$sortBy === 'name' ? $sortOrder : null\"&gt;\n            Name\n        &lt;/x-ui.table-header&gt;\n        &lt;x-ui.table-header align=\"center\"&gt;Status&lt;/x-ui.table-header&gt;\n        &lt;x-ui.table-header align=\"right\"&gt;Actions&lt;/x-ui.table-header&gt;\n    &lt;/x-slot:header&gt;\n    \n    &lt;tbody class=\"divide-y divide-slate-50 dark:divide-slate-800\"&gt;\n        &lt;tr class=\"hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors\"&gt;\n            &lt;x-ui.table-cell&gt;\n                &lt;span class=\"font-semibold text-slate-900 dark:text-white\"&gt;User Name&lt;/span&gt;\n            &lt;/x-ui.table-cell&gt;\n            &lt;x-ui.table-cell align=\"center\"&gt;\n                &lt;x-ui.badge variant=\"success\"&gt;Active&lt;/x-ui.badge&gt;\n            &lt;/x-ui.table-cell&gt;\n            &lt;x-ui.table-cell align=\"right\"&gt;\n                &lt;a href=\"#\" class=\"text-amber-700 dark:text-amber-400\"&gt;View →&lt;/a&gt;\n            &lt;/x-ui.table-cell&gt;\n        &lt;/tr&gt;\n    &lt;/tbody&gt;\n&lt;/x-ui.table&gt;`)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border rounded-[2px] transition-colors hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="copied ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3'"/>
                            </svg>
                            <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                        </button>
                    </div>
                </div>
                <div x-show="revealCode" x-collapse class="border-t border-slate-100 dark:border-slate-800">
                    <div class="p-5 overflow-x-auto code-scroll">
                        <pre class="text-sm leading-relaxed"><code class="code-highlight">&lt;<span class="code-tag">x-ui.table</span>&gt;
    &lt;<span class="code-tag">x-slot</span>:<span class="code-attr">header</span>&gt;
        &lt;<span class="code-tag">x-ui.table-header</span> <span class="code-attr">sortable</span> <span class="code-attr">sort</span>=<span class="code-string">"name"</span> <span class="code-attr">:direction</span>=<span class="code-string">"$sortBy === 'name' ? $sortOrder : null"</span>&gt;
            Name
        &lt;/<span class="code-tag">x-ui.table-header</span>&gt;
        &lt;<span class="code-tag">x-ui.table-header</span> <span class="code-attr">align</span>=<span class="code-string">"center"</span>&gt;Status&lt;/<span class="code-tag">x-ui.table-header</span>&gt;
        &lt;<span class="code-tag">x-ui.table-header</span> <span class="code-attr">align</span>=<span class="code-string">"right"</span>&gt;Actions&lt;/<span class="code-tag">x-ui.table-header</span>&gt;
    &lt;/<span class="code-tag">x-slot</span>&gt;
    
    &lt;<span class="code-tag">tbody</span> <span class="code-attr">class</span>=<span class="code-string">"divide-y divide-slate-50 dark:divide-slate-800"</span>&gt;
        &lt;<span class="code-tag">tr</span> <span class="code-attr">class</span>=<span class="code-string">"hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors"</span>&gt;
            &lt;<span class="code-tag">x-ui.table-cell</span>&gt;
                &lt;<span class="code-tag">span</span> <span class="code-attr">class</span>=<span class="code-string">"font-semibold text-slate-900 dark:text-white"</span>&gt;User Name&lt;/<span class="code-tag">span</span>&gt;
            &lt;/<span class="code-tag">x-ui.table-cell</span>&gt;
            &lt;<span class="code-tag">x-ui.table-cell</span> <span class="code-attr">align</span>=<span class="code-string">"center"</span>&gt;
                &lt;<span class="code-tag">x-ui.badge</span> <span class="code-attr">variant</span>=<span class="code-string">"success"</span>&gt;Active&lt;/<span class="code-tag">x-ui.badge</span>&gt;
            &lt;/<span class="code-tag">x-ui.table-cell</span>&gt;
            &lt;<span class="code-tag">x-ui.table-cell</span> <span class="code-attr">align</span>=<span class="code-string">"right"</span>&gt;
                &lt;<span class="code-tag">a</span> <span class="code-attr">href</span>=<span class="code-string">"#"</span> <span class="code-attr">class</span>=<span class="code-string">"text-amber-700 dark:text-amber-400"</span>&gt;View →&lt;/<span class="code-tag">a</span>&gt;
            &lt;/<span class="code-tag">x-ui.table-cell</span>&gt;
        &lt;/<span class="code-tag">tr</span>&gt;
    &lt;/<span class="code-tag">tbody</span>&gt;
&lt;/<span class="code-tag">x-ui.table</span>&gt;</code></pre>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             COMPONENT: Page Shell Layout
        ═══════════════════════════════════════════════════════════ --}}
        <div id="page-shell" class="space-y-5 pt-8">
            <x-ui.card>
                <x-ui.card-header title="Page Shell Layout" accent="danger" subtitle="Consistent page wrapper with header, subtitle, and action slots"/>
            </x-ui.card>

            {{-- Preview --}}
            <x-ui.card>
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5" style="background: linear-gradient(180deg, #dc2626, #ef4444); border-radius: 1px;"></div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider">Preview</h3>
                </div>
                <div class="p-5 bg-slate-50 dark:bg-slate-800/30">
                    {{-- Simulated page shell preview --}}
                    <div class="max-w-4xl mx-auto">
                        <div class="overflow-hidden rounded-[2px] shadow-lg" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                            <div class="h-1 w-full" style="background: linear-gradient(90deg, #dc2626, #ef4444, #fca5a5);"></div>
                            <div class="px-7 py-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <h1 class="text-2xl font-bold text-white leading-snug" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                                        Page Title
                                    </h1>
                                    <p class="text-slate-400 mt-2 text-sm">Optional subtitle goes here</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button class="px-4 py-2 text-xs font-semibold rounded-[2px] bg-gradient-to-r from-slate-50 to-slate-100 text-slate-700 border border-slate-200">Cancel</button>
                                    <button class="px-4 py-2 text-xs font-semibold rounded-[2px] bg-gradient-to-r from-violet-600 to-violet-500 text-white shadow-[0_2px_10px_rgba(124,58,237,0.3)]">Save</button>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 p-6 bg-white dark:bg-slate-900 rounded-[2px] border border-slate-200 dark:border-slate-800 shadow-sm">
                            <p class="text-sm text-slate-600 dark:text-slate-400">Page content goes here. This component provides the consistent header, gradient background, and action bar pattern used throughout the Examinations Hub.</p>
                        </div>
                    </div>
                </div>
            </x-ui.card>

            {{-- Code --}}
            <div x-data="codeSnippet" class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-1 h-5" style="background: linear-gradient(180deg, #dc2626, #ef4444); border-radius: 1px;"></div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider">Usage</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="revealCode = !revealCode" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border rounded-[2px] transition-colors hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="revealCode ? 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' : 'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21'"/>
                            </svg>
                            <span x-text="revealCode ? 'Hide Code' : 'Show Code'"></span>
                        </button>
                        <button @click="copyToClipboard(`&lt;x-layout.page-shell \n    title=\"Examinations Hub\"\n    subtitle=\"Manage and monitor all examinations\"\n    header-gradient=\"primary\"\n    :back-link=\"[url => route('dashboard'), label => 'Back to Dashboard']\"\n&gt;\n    &lt;x-slot:actions&gt;\n        &lt;x-ui.button variant=\"ghost\" size=\"sm\" icon=\"arrow-left\"&gt;Cancel&lt;/x-ui.button&gt;\n        &lt;x-ui.button variant=\"primary\" size=\"sm\" icon=\"check\"&gt;Save&lt;/x-ui.button&gt;\n    &lt;/x-slot&gt;\n\n    &lt;!-- Page Content --&gt;\n    &lt;div class=\"space-y-7\"&gt;\n        &lt;x-ui.card&gt;\n            &lt;x-ui.card-header title=\"Section Title\" subtitle=\"Description\"/&gt;\n            &lt;div class=\"p-5\"&gt;Content&lt;/div&gt;\n        &lt;/x-ui.card&gt;\n    &lt;/div&gt;\n&lt;/x-layout.page-shell&gt;`)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border rounded-[2px] transition-colors hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="copied ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3'"/>
                            </svg>
                            <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                        </button>
                    </div>
                </div>
                <div x-show="revealCode" x-collapse class="border-t border-slate-100 dark:border-slate-800">
                    <div class="p-5 overflow-x-auto code-scroll">
                        <pre class="text-sm leading-relaxed"><code class="code-highlight">&lt;<span class="code-tag">x-layout.page-shell</span> 
    <span class="code-attr">title</span>=<span class="code-string">"Examinations Hub"</span>
    <span class="code-attr">subtitle</span>=<span class="code-string">"Manage and monitor all examinations"</span>
    <span class="code-attr">header-gradient</span>=<span class="code-string">"primary"</span>
    <span class="code-attr">:back-link</span>=<span class="code-string">"[url => route('dashboard'), label => 'Back to Dashboard']"</span>
&gt;
    &lt;<span class="code-tag">x-slot</span>:<span class="code-attr">actions</span>&gt;
        &lt;<span class="code-tag">x-ui.button</span> <span class="code-attr">variant</span>=<span class="code-string">"ghost"</span> <span class="code-attr">size</span>=<span class="code-string">"sm"</span> <span class="code-attr">icon</span>=<span class="code-string">"arrow-left"</span>&gt;Cancel&lt;/<span class="code-tag">x-ui.button</span>&gt;
        &lt;<span class="code-tag">x-ui.button</span> <span class="code-attr">variant</span>=<span class="code-string">"primary"</span> <span class="code-attr">size</span>=<span class="code-string">"sm"</span> <span class="code-attr">icon</span>=<span class="code-string">"check"</span>&gt;Save&lt;/<span class="code-tag">x-ui.button</span>&gt;
    &lt;/<span class="code-tag">x-slot</span>&gt;

    &lt;<span class="code-tag">div</span> <span class="code-attr">class</span>=<span class="code-string">"space-y-7"</span>&gt;
        &lt;<span class="code-tag">x-ui.card</span>&gt;
            &lt;<span class="code-tag">x-ui.card-header</span> <span class="code-attr">title</span>=<span class="code-string">"Section Title"</span> <span class="code-attr">subtitle</span>=<span class="code-string">"Description"</span>/&gt;
            &lt;<span class="code-tag">div</span> <span class="code-attr">class</span>=<span class="code-string">"p-5"</span>&gt;Content&lt;/<span class="code-tag">div</span>&gt;
        &lt;/<span class="code-tag">x-ui.card</span>&gt;
    &lt;/<span class="code-tag">div</span>&gt;
&lt;/<span class="code-tag">x-layout.page-shell</span>&gt;</code></pre>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /container --}}

    {{-- ── Alpine.js for Code Snippets ── --}}
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('codeSnippet', () => ({
            revealCode: false,
            copied: false,
            async copyToClipboard(text) {
                try {
                    await navigator.clipboard.writeText(text.trim());
                    this.copied = true;
                    setTimeout(() => { this.copied = false; }, 2000);
                } catch (err) {
                    // Fallback
                    const textarea = document.createElement('textarea');
                    textarea.value = text.trim();
                    document.body.appendChild(textarea);
                    textarea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textarea);
                    this.copied = true;
                    setTimeout(() => { this.copied = false; }, 2000);
                }
            }
        }));
    });
    </script>
</x-layouts.app>