@props([
    'id',
    'model',               // wire:model target, e.g. "pdfFile"
    'accept' => '*/*',     // MIME types to accept, e.g. "application/pdf"
    'current' => null,      // URL or path of the saved file (or null)
    'pending' => false,     // a new file is staged
    'uploaded' => false,    // file has been uploaded to disk
    'removeAction' => null, // Livewire method, e.g. "removeExistingPdfFile"
    'tag' => 'FILE',
    'icon' => 'file',       // icon type: file, pdf, audio, video
    'errorKey' => null,
])

@php($key = $errorKey ?? $model)

@if($current && !$pending && !$uploaded)
    <div class="mb-2.5 flex items-center gap-3.5 rounded border border-line bg-surface-2 p-2.5">
        @if($icon === 'pdf')
            <svg class="h-10 w-10 text-accent shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        @elseif($icon === 'audio')
            <svg class="h-10 w-10 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
            </svg>
        @elseif($icon === 'video')
            <svg class="h-10 w-10 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
        @else
            <svg class="h-10 w-10 text-muted shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        @endif
        <div class="min-w-0">
            <span class="mb-1 block font-mono text-[9.5px] tracking-[0.14em] text-muted">CURRENT FILE</span>
            <p class="text-sm font-medium text-ink truncate">{{ basename($current) }}</p>
            @if($removeAction)
                <button type="button" wire:click="{{ $removeAction }}"
                        class="mt-1 border-b border-dashed border-transparent text-[12.5px] font-semibold text-accent transition hover:border-accent focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">
                    Remove file
                </button>
            @endif
        </div>
    </div>
@elseif($pending)
    <p class="mb-2.5 font-mono text-[11.5px] text-ok">◉ NEW FILE SELECTED — APPLIES WHEN YOU SAVE</p>
@elseif($uploaded)
    <p class="mb-2.5 font-mono text-[11.5px] text-ok">✓ FILE UPLOADED SUCCESSFULLY</p>
@endif

<input type="file" id="{{ $id }}" accept="{{ $accept }}" wire:model="{{ $model }}" class="peer sr-only">

<label for="{{ $id }}"
       class="group flex cursor-pointer items-center gap-3 rounded border-[1.5px] border-dashed border-line-strong px-3.5 py-3 text-[13px] text-ink-soft transition hover:border-primary hover:bg-surface-2 peer-focus-visible:border-primary peer-focus-visible:ring-[3px] peer-focus-visible:ring-primary/20">
    @if($icon === 'pdf')
        <svg class="h-[18px] w-[18px] text-muted transition group-hover:-translate-y-0.5 group-hover:text-accent motion-reduce:transition-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
    @elseif($icon === 'audio')
        <svg class="h-[18px] w-[18px] text-muted transition group-hover:-translate-y-0.5 group-hover:text-primary motion-reduce:transition-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
        </svg>
    @elseif($icon === 'video')
        <svg class="h-[18px] w-[18px] text-muted transition group-hover:-translate-y-0.5 group-hover:text-primary motion-reduce:transition-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
        </svg>
    @else
        <svg class="h-[18px] w-[18px] text-muted transition group-hover:-translate-y-0.5 group-hover:text-primary motion-reduce:transition-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
        </svg>
    @endif
    <span><b class="font-semibold text-ink">{{ $current && !$uploaded ? 'Replace' : 'Upload' }} file</b> — click to browse</span>
    <span class="ml-auto rounded-[3px] border border-line bg-surface px-1.5 py-1 font-mono text-[9px] tracking-[0.14em] text-muted">{{ $tag }}</span>
</label>

@if($key)
    @error($key)
        <p class="mt-2 font-mono text-[11.5px] text-accent">▲ {{ $message }}</p>
    @enderror
@endif
