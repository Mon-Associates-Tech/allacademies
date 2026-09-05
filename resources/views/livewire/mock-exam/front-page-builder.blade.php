<section class="h-screen flex flex-col bg-slate-50 dark:bg-slate-900">
    {{-- Top Header --}}
    <div class="flex-shrink-0 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('mock-exams.templates.index') }}" class="text-slate-500 hover:text-slate-800 dark:hover:text-slate-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-lg font-bold text-slate-900 dark:text-white">Front Page Designer</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400">Design the cover page candidates see before the exam begins.</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($templateId)
                <button type="button" wire:click="saveFrontPage" wire:loading.attr="disabled" class="px-4 py-2 text-sm font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded hover:bg-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800">
                    <span wire:loading.remove wire:target="saveFrontPage">Save</span>
                    <span wire:loading wire:target="saveFrontPage">Saving...</span>
                </button>
            @endif
            <button type="button" wire:click="proceed" wire:loading.attr="disabled" class="px-4 py-2 text-sm font-semibold text-white bg-violet-600 rounded hover:bg-violet-700 shadow-sm">
                Next: Template Details
            </button>
        </div>
    </div>

    {{-- Main Split Pane --}}
    <div class="flex-1 flex overflow-hidden">
        {{-- LEFT: Block Editor --}}
        <div class="w-[450px] flex-shrink-0 flex flex-col border-r border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                {{-- Add Block Button --}}
                <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                    <button type="button" @click="open = !open" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium border-2 border-dashed border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-400 rounded hover:border-violet-400 hover:text-violet-600 dark:hover:border-violet-500 dark:hover:text-violet-400 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Block
                    </button>
                    <div x-show="open" x-transition class="absolute left-0 right-0 bottom-full mb-2 z-20 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl rounded overflow-hidden">
                        @foreach ([
                            ['heading', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'Heading', 'H1, H2, or H3 text'],
                            ['richtext', 'M4 6h16M4 12h16M4 18h7', 'Rich Text', 'Formatted body content'],
                            ['image', 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'Image', 'From URL or file upload'],
                            ['divider', 'M20 12H4', 'Divider', 'Horizontal rule separator'],
                            ['info_table', 'M3 10h18M3 14h18M3 6h18M3 18h18', 'Info Table', 'Candidate fields (name, date…)'],
                        ] as [$type, $icon, $label, $desc])
                            <button type="button" wire:click="addBlock('{{ $type }}')" @click="open = false" class="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors border-b border-slate-100 dark:border-slate-700/50 last:border-0">
                                <div class="flex items-center justify-center w-8 h-8 shrink-0 bg-violet-100 dark:bg-violet-900/30 rounded">
                                    <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $label }}</div>
                                    <div class="text-xs text-slate-400 dark:text-slate-500">{{ $desc }}</div>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Blocks List --}}
                @forelse ($frontPageBlocks as $index => $block)
                    <div wire:key="block-{{ $block['id'] }}" class="bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded overflow-hidden">
                        <div class="px-3 py-2 border-b border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">{{ str_replace('_', ' ', $block['type']) }}</span>
                            <div class="flex items-center gap-1">
                                @if ($index > 0)
                                    <button wire:click="moveBlock({{ $index }}, 'up')" class="p-1 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg></button>
                                @endif
                                @if ($index < count($frontPageBlocks) - 1)
                                    <button wire:click="moveBlock({{ $index }}, 'down')" class="p-1 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></button>
                                @endif
                                <button wire:click="removeBlock({{ $index }})" class="p-1 text-slate-400 hover:text-red-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </div>
                        </div>
                        
                        <div class="p-3 space-y-3">
                            @if ($block['type'] === 'heading')
                                <div class="flex items-center gap-2">
                                    <select wire:model.live="frontPageBlocks.{{ $index }}.level" class="w-20 px-2 py-1.5 text-sm border border-slate-300 dark:border-slate-600 rounded bg-white dark:bg-slate-800">
                                        <option value="h1">H1</option>
                                        <option value="h2">H2</option>
                                        <option value="h3">H3</option>
                                    </select>
                                    <input type="text" wire:model.live.debounce.300ms="frontPageBlocks.{{ $index }}.content" placeholder="Heading text..." class="flex-1 px-3 py-1.5 text-sm border border-slate-300 dark:border-slate-600 rounded bg-white dark:bg-slate-800">
                                </div>
                            @elseif ($block['type'] === 'richtext')
                                <x-form.livewire-editor :livewire="'frontPageBlocks.' . $index . '.content'" :value="$block['content'] ?? ''" :height="150" />
                            @elseif ($block['type'] === 'image')
                                <div class="space-y-2">
                                    <input type="text" wire:model.live.debounce.300ms="frontPageBlocks.{{ $index }}.src" placeholder="Image URL" class="w-full px-3 py-1.5 text-sm border border-slate-300 dark:border-slate-600 rounded bg-white dark:bg-slate-800">
                                    <div class="flex gap-2">
                                        <input type="number" wire:model.live.debounce.300ms="frontPageBlocks.{{ $index }}.width" placeholder="Width (px)" class="w-24 px-3 py-1.5 text-sm border border-slate-300 dark:border-slate-600 rounded bg-white dark:bg-slate-800">
                                        <select wire:model.live="frontPageBlocks.{{ $index }}.alignment" class="flex-1 px-3 py-1.5 text-sm border border-slate-300 dark:border-slate-600 rounded bg-white dark:bg-slate-800">
                                            <option value="left">Left</option>
                                            <option value="center">Center</option>
                                            <option value="right">Right</option>
                                        </select>
                                    </div>
                                    {{-- Keep your existing Alpine upload logic here if needed, simplified for brevity --}}
                                </div>
                            @elseif ($block['type'] === 'divider')
                                <div class="py-2 text-center text-xs text-slate-400 italic">Horizontal Divider (no config needed)</div>
                            @elseif ($block['type'] === 'info_table')
                                @php
                                    $infoFields = ['candidate_name' => 'Candidate Name', 'index_number' => 'Index Number', 'date' => 'Date', 'duration' => 'Duration', 'subject' => 'Subject', 'grade' => 'Grade / Class', 'signature' => 'Invigilator Signature', 'score' => 'Total Score'];
                                    $activeFields = $block['fields'] ?? [];
                                @endphp
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach ($infoFields as $fieldKey => $fieldLabel)
                                        <label class="flex items-center gap-2 p-2 border rounded cursor-pointer {{ in_array($fieldKey, $activeFields) ? 'border-violet-500 bg-violet-50 dark:bg-violet-900/20' : 'border-slate-200 dark:border-slate-700' }}">
                                            <input type="checkbox" class="accent-violet-600" {{ in_array($fieldKey, $activeFields) ? 'checked' : '' }} wire:click="toggleInfoField({{ $index }}, '{{ $fieldKey }}')">
                                            <span class="text-xs text-slate-700 dark:text-slate-300">{{ $fieldLabel }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded">
                        <p class="text-slate-500 dark:text-slate-400 text-sm">No blocks yet. Add one to get started.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- RIGHT: Live Preview --}}
        <div class="flex-1 bg-slate-100 dark:bg-slate-950 overflow-y-auto p-8 flex justify-center">
            <div class="flex flex-col items-center">
                <div class="mb-4 flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Live A4 Preview
                </div>
                
                {{-- The Preview Component --}}
                <div class="bg-white" style="width: 210mm; min-height: 297mm;">
                    @include('mock-exam.pdf.partials.front-page-preview', [
                        'blocks' => $frontPageBlocks,
                        'template' => $template,
                        'fontSize' => 11
                    ])
                </div>
            </div>
        </div>
    </div>
</section>

@assets
<script>
// Keep your existing Alpine imageUpload logic here
Alpine.data('imageUpload', (index) => ({
    uploading: false,
    uploadError: null,
    upload(file) {
        if (!file) return;
        this.uploading = true;
        this.uploadError = null;
        this.$wire.upload('pendingImage', file,
            () => {
                this.$wire.call('uploadBlockImage', index)
                    .then(() => { this.uploading = false; })
                    .catch(() => { this.uploadError = 'Could not save the image.'; this.uploading = false; });
            },
            () => { this.uploadError = 'Upload failed. Max 3 MB.'; this.uploading = false; },
            () => {}
        );
    },
}));
</script>
@endassets