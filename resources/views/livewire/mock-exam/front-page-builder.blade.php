<section>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6"
     style="font-family: 'system-ui', -apple-system, sans-serif;">

    {{-- ── STEP INDICATOR ──────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-0">
        {{-- Step 1 (active) --}}
        <div class="flex items-center gap-2.5 px-4 py-2.5"
             style="border-radius: 2px 0 0 2px; background: linear-gradient(135deg, #7c3aed, #6d28d9);">
            <span class="flex items-center justify-center w-5 h-5 rounded-full bg-white text-violet-700 text-xs font-bold shrink-0">1</span>
            <span class="text-white text-sm font-semibold whitespace-nowrap">Front Page</span>
        </div>
        {{-- Arrow --}}
        <div class="w-0 h-0 shrink-0" style="border-top: 20px solid transparent; border-bottom: 20px solid transparent; border-left: 12px solid #6d28d9;"></div>
        {{-- Step 2 (pending) --}}
        <div class="flex items-center gap-2.5 px-5 py-2.5 bg-slate-200 dark:bg-slate-700 ml-0"
             style="border-radius: 0 2px 2px 0;">
            <span class="flex items-center justify-center w-5 h-5 rounded-full bg-slate-400 dark:bg-slate-500 text-white text-xs font-bold shrink-0">2</span>
            <span class="text-slate-500 dark:text-slate-400 text-sm font-medium whitespace-nowrap">Template Details</span>
        </div>
    </div>

    {{-- ── PAGE HEADER ─────────────────────────────────────────────────────── --}}
    <div class="overflow-hidden"
         style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
        <div class="h-1 w-full" style="background: linear-gradient(90deg, #7c3aed, #a78bfa, #c4b5fd);"></div>
        <div class="px-7 py-6 flex items-start justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('mock-exams.templates.index') }}"
                   class="flex items-center justify-center w-8 h-8 text-slate-400 hover:text-white border border-slate-700 hover:border-slate-500 transition-all shrink-0"
                   style="border-radius: 2px;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-white leading-snug"
                        style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                        Front Page Designer
                    </h1>
                    <p class="text-slate-400 mt-1 text-sm">
                        Design the cover page candidates see before the exam begins. Add headings, rich text, images, dividers, and info tables.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── VALIDATION ERRORS ───────────────────────────────────────────────── --}}
    @if($errors->any())
        <div class="px-5 py-4 text-sm" style="border-radius: 2px; background: #fef2f2; border: 1px solid #fecaca;">
            <p class="font-semibold text-red-700 mb-1">Please fix the following:</p>
            <ul class="list-disc list-inside space-y-0.5 text-red-600">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    {{-- ── BLOCKS LIST ─────────────────────────────────────────────────────── --}}
    <div class="space-y-3">
        @forelse ($frontPageBlocks as $index => $block)
            <div wire:key="block-{{ $block['id'] }}">

                @php
                    $typeLabel = match($block['type']) {
                        'heading'    => 'Heading',
                        'richtext'   => 'Rich Text',
                        'image'      => 'Image',
                        'divider'    => 'Divider',
                        'info_table' => 'Info Table',
                        default      => $block['type'],
                    };
                    $typeBadgeClass = match($block['type']) {
                        'heading'    => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                        'richtext'   => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                        'image'      => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                        'divider'    => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400',
                        'info_table' => 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400',
                        default      => 'bg-slate-100 text-slate-600',
                    };
                    $leftBorder = match($block['type']) {
                        'heading'    => 'border-l-blue-400',
                        'richtext'   => 'border-l-emerald-400',
                        'image'      => 'border-l-amber-400',
                        'divider'    => 'border-l-slate-300',
                        'info_table' => 'border-l-violet-400',
                        default      => 'border-l-slate-300',
                    };
                @endphp

                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 border-l-4 {{ $leftBorder }} overflow-hidden"
                     style="border-radius: 2px; box-shadow: 0 1px 4px rgba(0,0,0,0.04);">

                    {{-- Block header --}}
                    <div class="px-4 py-2.5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/30">
                        <div class="flex items-center gap-2.5">
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold {{ $typeBadgeClass }}"
                                  style="border-radius: 2px;">{{ $typeLabel }}</span>
                            <span class="text-xs text-slate-400 dark:text-slate-500">Block {{ $index + 1 }}</span>
                        </div>

                        <div class="flex items-center gap-1">
                            @if ($index > 0)
                                <button type="button" wire:click="moveBlock({{ $index }}, 'up')"
                                        class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors"
                                        title="Move up" style="border-radius: 2px;">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                    </svg>
                                </button>
                            @endif
                            @if ($index < count($frontPageBlocks) - 1)
                                <button type="button" wire:click="moveBlock({{ $index }}, 'down')"
                                        class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors"
                                        title="Move down" style="border-radius: 2px;">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            @endif
                            <div class="w-px h-4 bg-slate-200 dark:bg-slate-700 mx-1"></div>
                            <button type="button" wire:click="removeBlock({{ $index }})"
                                    class="p-1.5 text-slate-400 hover:text-red-500 transition-colors"
                                    title="Remove block" style="border-radius: 2px;">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- ── Block body ─────────────────────────────────────── --}}
                    <div class="p-5">

                        {{-- ╔══════════════════════╗
                             ║  HEADING BLOCK       ║
                             ╚══════════════════════╝ --}}
                        @if ($block['type'] === 'heading')
                            <div class="flex items-center gap-3">
                                <select wire:model.live="frontPageBlocks.{{ $index }}.level"
                                        class="shrink-0 w-20 px-3 py-2 text-sm font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-400/30 focus:border-blue-400 transition-all"
                                        style="border-radius: 2px;">
                                    <option value="h1">H1</option>
                                    <option value="h2">H2</option>
                                    <option value="h3">H3</option>
                                </select>
                                <input type="text"
                                       wire:model.defer="frontPageBlocks.{{ $index }}.content"
                                       placeholder="Enter heading text…"
                                       class="flex-1 px-4 py-2 text-sm border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-400/30 focus:border-blue-400 transition-all placeholder-slate-400"
                                       style="border-radius: 2px;
                                              {{ $block['level'] === 'h1' ? 'font-size: 1.4rem; font-weight: 700;' : ($block['level'] === 'h2' ? 'font-size: 1.15rem; font-weight: 600;' : 'font-size: 1rem; font-weight: 600;') }}">
                            </div>

                        {{-- ╔══════════════════════╗
                             ║  RICH TEXT BLOCK     ║
                             ╚══════════════════════╝ --}}
                        @elseif ($block['type'] === 'richtext')
                            <div wire:ignore>
                                <div data-richtext-block="{{ $index }}"
                                     x-data="richTextBridge({{ $index }})">
                                    <x-form.rich-editor
                                        :name="'front_page_block_' . $index . '_content'"
                                        :value="$block['content'] ?? ''"
                                    />
                                </div>
                            </div>

                        {{-- ╔══════════════════════╗
                             ║  IMAGE BLOCK         ║
                             ╚══════════════════════╝ --}}
                        @elseif ($block['type'] === 'image')
                            @php
                                $sourceType = $block['source_type'] ?? 'url';
                            @endphp
                            <div x-data="{ sourceType: '{{ $sourceType }}' }" class="space-y-4">

                                {{-- Source toggle --}}
                                <div class="flex items-center gap-1 p-1 bg-slate-100 dark:bg-slate-800 w-fit"
                                     style="border-radius: 2px;">
                                    <button type="button"
                                            @click="sourceType = 'url'; $wire.set('frontPageBlocks.{{ $index }}.source_type', 'url')"
                                            :class="sourceType === 'url' ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-white shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                                            class="px-3 py-1 text-xs font-medium transition-all"
                                            style="border-radius: 2px;">
                                        URL
                                    </button>
                                    <button type="button"
                                            @click="sourceType = 'upload'; $wire.set('frontPageBlocks.{{ $index }}.source_type', 'upload')"
                                            :class="sourceType === 'upload' ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-white shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                                            class="px-3 py-1 text-xs font-medium transition-all"
                                            style="border-radius: 2px;">
                                        Upload
                                    </button>
                                </div>

                                {{-- URL input --}}
                                <div x-show="sourceType === 'url'" class="flex gap-2">
                                    <input type="url"
                                           wire:model.defer="frontPageBlocks.{{ $index }}.url_input"
                                           placeholder="https://example.com/image.png"
                                           class="flex-1 px-4 py-2 text-sm border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-400/30 focus:border-amber-400 transition-all placeholder-slate-400"
                                           style="border-radius: 2px;">
                                    <button type="button"
                                            wire:click="applyImageUrl({{ $index }})"
                                            class="px-4 py-2 text-xs font-semibold text-white transition-all shrink-0"
                                            style="border-radius: 2px; background: linear-gradient(135deg, #d97706, #b45309);">
                                        Apply URL
                                    </button>
                                </div>

                                {{-- File upload --}}
                                {{--
                                    imageUpload Alpine component owns the upload lifecycle.
                                    It uses this.$wire (Alpine magic) instead of a captured
                                    $wire closure reference — Alpine's magic property always
                                    returns the current Livewire proxy, which prevents the
                                    proxy's get-trap from being hit during scope serialisation
                                    and triggering the "toJSON not found" error.
                                --}}
                                <div x-show="sourceType === 'upload'"
                                     x-data="imageUpload({{ $index }})"
                                     class="space-y-2">
                                    <div class="flex items-center gap-3">
                                        <label class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-slate-600 dark:text-slate-300 border border-dashed border-slate-300 dark:border-slate-600 cursor-pointer hover:border-amber-400 hover:text-amber-600 transition-all"
                                               style="border-radius: 2px;"
                                               :class="uploading ? 'opacity-50 pointer-events-none' : ''">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span x-text="uploading ? 'Uploading…' : 'Choose image…'"></span>
                                            <input type="file"
                                                   class="hidden"
                                                   accept="image/*"
                                                   x-ref="fileInput"
                                                   @change="upload($event.target.files[0])">
                                        </label>
                                        <span class="text-xs text-slate-400">JPEG, PNG, GIF, WebP · max 3 MB</span>
                                    </div>
                                    <div x-show="uploading" class="text-xs text-slate-500 flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                        </svg>
                                        Uploading…
                                    </div>
                                    <p x-show="uploadError" x-text="uploadError"
                                       class="text-xs text-red-500 dark:text-red-400"></p>
                                </div>

                                {{-- Preview --}}
                                @if (!empty($block['src']))
                                    <div class="flex items-start gap-4 p-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700"
                                         style="border-radius: 2px;"
                                         x-on:block-image-ready.window="
                                             if ($event.detail.index === {{ $index }}) {
                                                 $el.querySelector('img').src = $event.detail.src;
                                             }">
                                        <img src="{{ $block['src'] }}"
                                             alt="{{ $block['alt'] ?? '' }}"
                                             class="max-h-28 object-contain bg-white border border-slate-200 dark:border-slate-600"
                                             style="border-radius: 2px; max-width: {{ ($block['width'] ?? 300) }}px;">
                                        <div class="text-xs text-slate-400 dark:text-slate-500 space-y-0.5">
                                            <div>Width: {{ $block['width'] ?? 300 }}px</div>
                                            <div>Align: {{ ucfirst($block['alignment'] ?? 'center') }}</div>
                                            @if ($block['alt'] ?? '')
                                                <div>Alt: {{ $block['alt'] }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                {{-- Alt text, width, alignment --}}
                                <div class="grid sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1.5" style="letter-spacing: 0.08em;">Alt text</label>
                                        <input type="text"
                                               wire:model.defer="frontPageBlocks.{{ $index }}.alt"
                                               placeholder="Describe the image"
                                               class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-400/30 focus:border-amber-400 transition-all placeholder-slate-400"
                                               style="border-radius: 2px;">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1.5" style="letter-spacing: 0.08em;">Width (px)</label>
                                        <input type="number"
                                               wire:model.defer="frontPageBlocks.{{ $index }}.width"
                                               min="50" max="1200" step="10"
                                               class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-400/30 focus:border-amber-400 transition-all"
                                               style="border-radius: 2px;">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1.5" style="letter-spacing: 0.08em;">Alignment</label>
                                        <select wire:model.live="frontPageBlocks.{{ $index }}.alignment"
                                                class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-400/30 focus:border-amber-400 transition-all"
                                                style="border-radius: 2px;">
                                            <option value="left">Left</option>
                                            <option value="center">Centre</option>
                                            <option value="right">Right</option>
                                        </select>
                                    </div>
                                </div>

                            </div>

                        {{-- ╔══════════════════════╗
                             ║  DIVIDER BLOCK       ║
                             ╚══════════════════════╝ --}}
                        @elseif ($block['type'] === 'divider')
                            <div class="py-3 text-center">
                                <div class="flex items-center gap-4">
                                    <div class="flex-1 border-t border-slate-300 dark:border-slate-600"></div>
                                    <span class="text-xs text-slate-400 dark:text-slate-500 italic">horizontal rule — no configuration needed</span>
                                    <div class="flex-1 border-t border-slate-300 dark:border-slate-600"></div>
                                </div>
                            </div>

                        {{-- ╔══════════════════════╗
                             ║  INFO TABLE BLOCK    ║
                             ╚══════════════════════╝ --}}
                        @elseif ($block['type'] === 'info_table')
                            @php
                                $infoFields = [
                                    'candidate_name' => 'Candidate Name',
                                    'index_number'   => 'Index Number',
                                    'date'           => 'Date',
                                    'duration'       => 'Duration',
                                    'subject'        => 'Subject',
                                    'grade'          => 'Grade / Class',
                                    'signature'      => 'Invigilator Signature',
                                    'score'          => 'Total Score',
                                ];
                                $activeFields = $block['fields'] ?? [];
                            @endphp

                            <div class="space-y-3">
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider" style="letter-spacing: 0.08em;">
                                    Select fields to display in the info table
                                </p>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    @foreach ($infoFields as $fieldKey => $fieldLabel)
                                        <label class="flex items-center gap-2 px-3 py-2 cursor-pointer border transition-all
                                                      {{ in_array($fieldKey, $activeFields) ? 'border-violet-400 bg-violet-50 dark:bg-violet-900/20 dark:border-violet-500' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-slate-300' }}"
                                               style="border-radius: 2px;"
                                               wire:click="toggleInfoField({{ $index }}, '{{ $fieldKey }}')">
                                            <input type="checkbox"
                                                   class="accent-violet-600 shrink-0 pointer-events-none"
                                                   {{ in_array($fieldKey, $activeFields) ? 'checked' : '' }}>
                                            <span class="text-xs text-slate-700 dark:text-slate-300 font-medium">{{ $fieldLabel }}</span>
                                        </label>
                                    @endforeach
                                </div>

                                {{-- Mini preview --}}
                                @if (count($activeFields) > 0)
                                    <div class="mt-3 p-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700" style="border-radius: 2px;">
                                        <p class="text-xs text-slate-400 mb-2 uppercase tracking-wider" style="letter-spacing: 0.07em;">Preview</p>
                                        <div class="grid grid-cols-2 gap-x-6 gap-y-1.5">
                                            @foreach ($activeFields as $f)
                                                @if (isset($infoFields[$f]))
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-xs font-medium text-slate-600 dark:text-slate-300 w-28 shrink-0">{{ $infoFields[$f] }}:</span>
                                                        <div class="flex-1 border-b border-dotted border-slate-400 dark:border-slate-600 h-4"></div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                    </div>{{-- /block body --}}
                </div>{{-- /block card --}}

            </div>
        @empty
            {{-- ── EMPTY STATE ──────────────────────────────────────────── --}}
            <div class="flex flex-col items-center justify-center py-16 border-2 border-dashed border-slate-200 dark:border-slate-700 text-center"
                 style="border-radius: 2px;">
                <div class="w-12 h-12 mb-4 flex items-center justify-center bg-slate-100 dark:bg-slate-800"
                     style="border-radius: 2px;">
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-slate-600 dark:text-slate-400 font-medium mb-1">No blocks yet</p>
                <p class="text-sm text-slate-400 dark:text-slate-500">Add your first block below to start designing the front page.</p>
            </div>
        @endforelse
    </div>

    {{-- ── ADD BLOCK BUTTON ─────────────────────────────────────────────────── --}}
    <div x-data="{ open: false }" class="relative" @click.outside="open = false">
        <button type="button"
                @click="open = !open"
                class="w-full flex items-center justify-center gap-2 px-5 py-3 text-sm font-medium border-2 border-dashed transition-all
                       text-slate-500 hover:text-violet-600 border-slate-300 dark:border-slate-600 hover:border-violet-400 dark:hover:border-violet-500
                       dark:text-slate-400 hover:bg-violet-50 dark:hover:bg-violet-900/10"
                style="border-radius: 2px;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Block
            <svg class="w-3.5 h-3.5 transition-transform duration-150" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div x-show="open"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-1"
             class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 z-20 w-80
                    bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden"
             style="border-radius: 2px;">

            <div class="px-3 py-2 border-b border-slate-100 dark:border-slate-700">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Choose block type</p>
            </div>

            @foreach ([
                ['heading',    '#3b82f6', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'Heading', 'H1, H2, or H3 text'],
                ['richtext',   '#10b981', 'M4 6h16M4 12h16M4 18h7',                                                                                                 'Rich Text', 'Formatted body content'],
                ['image',      '#f59e0b', 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'Image', 'From URL or file upload'],
                ['divider',    '#94a3b8', 'M20 12H4',                                                                                                               'Divider', 'Horizontal rule separator'],
                ['info_table', '#8b5cf6', 'M3 10h18M3 14h18M3 6h18M3 18h18',                                                                                       'Info Table', 'Candidate fields (name, date…)'],
            ] as [$type, $color, $icon, $label, $desc])
                <button type="button"
                        wire:click="addBlock('{{ $type }}')"
                        @click="open = false"
                        class="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors border-b border-slate-100 dark:border-slate-700/50 last:border-0">
                    <div class="flex items-center justify-center w-8 h-8 shrink-0"
                         style="border-radius: 2px; background: {{ $color }}22;">
                        <svg class="w-4 h-4" fill="none" stroke="{{ $color }}" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $label }}</div>
                        <div class="text-xs text-slate-400 dark:text-slate-500">{{ $desc }}</div>
                    </div>
                </button>
            @endforeach
        </div>
    </div>

    {{-- ── NAVIGATION ───────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between pt-2">
        <a href="{{ route('mock-exams.templates.index') }}"
           class="px-5 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
            Cancel
        </a>
        <button type="button"
                wire:click="proceed"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 px-7 py-2.5 text-sm font-semibold text-white transition-all hover:shadow-lg disabled:opacity-60"
                style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #6d28d9);">
            <span wire:loading.remove wire:target="proceed">
                Next: Template Details
                <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </span>
            <span wire:loading wire:target="proceed" class="flex items-center gap-2">
                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                Saving…
            </span>
        </button>
    </div>

</div>
</section>

@assets
<script>
Alpine.data('imageUpload', (index) => ({
    uploading: false,
    uploadError: null,

    upload(file) {
        if (!file) return;
        this.uploading = true;
        this.uploadError = null;

        this.$wire.upload(
            'pendingImage',
            file,
            () => {
                this.$wire.call('uploadBlockImage', index)
                    .then(() => { this.uploading = false; })
                    .catch(() => {
                        this.uploadError = 'Could not save the image. Please try again.';
                        this.uploading = false;
                    });
            },
            () => {
                this.uploadError = 'Upload failed. Max file size is 3 MB.';
                this.uploading = false;
            },
            () => {}
        );
    },
}));

Alpine.data('richTextBridge', (index) => ({
    init() {
        this.$nextTick(() => {
            const el = this.$el;
            if (!el) return;
            
            const $wire = this.$wire;

            const push = (html) => {
                $wire.call('updateBlockContent', index, html);
            };

            const textarea = el.querySelector('textarea');
            if (textarea) {
                const obs = new MutationObserver(() => push(textarea.value));
                obs.observe(textarea, { attributes: true, childList: false, characterData: true });
                textarea.addEventListener('input',  () => push(textarea.value));
                textarea.addEventListener('change', () => push(textarea.value));
            }

            el.addEventListener('input',         (e) => { if (e.target.closest('[contenteditable]')) push(e.target.innerHTML); });
            el.addEventListener('editor:update', (e) => push(e.detail?.html ?? e.detail?.content ?? ''));
            el.addEventListener('tiptap:change', (e) => push(e.detail?.html ?? ''));
            el.addEventListener('quill:change',  (e) => push(e.detail?.html ?? ''));

            const editable = el.querySelector('[contenteditable]');
            if (editable) {
                editable.addEventListener('blur', () => push(editable.innerHTML));
            }
        });
    },
}));
</script>
@endassets