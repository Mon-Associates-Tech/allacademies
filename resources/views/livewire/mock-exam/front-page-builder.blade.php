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
   <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                        Front Page Content
                    </label>
                    <x-form.livewire-editor :livewire="'content'" :value="$content" :height="600" />
                </div>
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
                        'blocks' => $content,
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