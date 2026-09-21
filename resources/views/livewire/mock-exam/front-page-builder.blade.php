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
        {{-- LEFT: Editor / Upload --}}
        <div class="w-[450px] flex-shrink-0 flex flex-col border-r border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
            <div class="flex-1 overflow-y-auto p-4 space-y-4">

                {{-- Mode toggle --}}
                <div class="flex gap-1 p-1 bg-slate-100 dark:bg-slate-900" style="border-radius: 2px;">
                    <button type="button" wire:click="switchMode('editor')"
                        class="flex-1 px-3 py-2 text-xs font-semibold transition-colors {{ $mode === 'editor' ? 'bg-violet-600 text-white' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}"
                        style="border-radius: 2px;">
                        Write Front Page
                    </button>
                    <button type="button" wire:click="switchMode('upload')"
                        class="flex-1 px-3 py-2 text-xs font-semibold transition-colors {{ $mode === 'upload' ? 'bg-violet-600 text-white' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}"
                        style="border-radius: 2px;">
                        Upload PDF
                    </button>
                </div>

                @if($mode === 'editor')
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                            Front Page Content
                        </label>
                        <x-form.livewire-editor :livewire="'content'" :value="$content" :height="600" />
                    </div>
                @else
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                            Front Page PDF
                        </label>

                        @if($attachmentPdfImages)
                            <div class="flex items-center justify-between gap-3 p-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700" style="border-radius: 2px;">
                                <div class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 min-w-0">
                                    <svg class="w-4 h-4 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span class="truncate">{{ $attachmentOriginalName }}</span>
                                </div>
                                <div class="flex items-center gap-3 flex-shrink-0">
                                    <span class="text-xs text-slate-400">{{ count($attachmentPdfImages) }} page{{ count($attachmentPdfImages) === 1 ? '' : 's' }}</span>
                                    <button type="button" wire:click="removeDocument" class="text-xs font-medium text-red-600 hover:text-red-800">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        @else
                            <label class="flex flex-col items-center justify-center gap-2 p-8 border-2 border-dashed border-slate-300 dark:border-slate-600 cursor-pointer hover:border-violet-400 dark:hover:border-violet-500 transition-colors" style="border-radius: 2px;" wire:loading.class="opacity-50" wire:target="documentUpload">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                <span class="text-sm text-slate-500 dark:text-slate-400">
                                    <span wire:loading.remove wire:target="documentUpload">Click to upload a PDF</span>
                                    <span wire:loading wire:target="documentUpload">Uploading &amp; converting pages…</span>
                                </span>
                                <span class="text-xs text-slate-400">PDF only, max 10 MB</span>
                                <input type="file" wire:model="documentUpload" accept="application/pdf" class="hidden">
                            </label>
                        @endif

                        @error('documentUpload')
                            <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                        @enderror

                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-3">
                            The PDF is used exactly as designed — each page renders as-is on the front page(s), in place of the header built by the editor.
                        </p>
                    </div>
                @endif
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
                        'content' => $content,
                        'pdfImages' => $attachmentPdfImages,
                        'template' => $template,
                        'fontSize' => 11,
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