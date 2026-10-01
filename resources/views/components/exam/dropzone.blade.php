@props([
    'id',
    'model',               // wire:model target, e.g. "pendingUploads.logo"
    'current' => null,      // URL of the saved image (or null)
    'pending' => false,     // a new file is staged
    'removeAction' => null, // Livewire method, e.g. "removeImage"
    'removeKey' => null,    // its argument, e.g. "logo"
    'tag' => 'HEADER ASSET',
    'errorKey' => null,
])

@php($key = $errorKey ?? $model)

@if($current && !$pending)
    <div class="mb-2.5 flex items-center gap-3.5 rounded border border-line bg-surface-2 p-2.5">
        <img src="{{ $current }}" alt="" class="h-12 w-auto max-w-[120px] rounded-[3px] border border-line bg-white p-1 object-contain">
        <div>
            <span class="mb-1 block font-mono text-[9.5px] tracking-[0.14em] text-muted">CURRENT FILE</span>
            @if($removeAction)
                <button type="button" wire:click="{{ $removeAction }}('{{ $removeKey }}')"
                        class="border-b border-dashed border-transparent text-[12.5px] font-semibold text-accent transition hover:border-accent focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">
                    Remove image
                </button>
            @endif
        </div>
    </div>
@elseif($pending)
    <p class="mb-2.5 font-mono text-[11.5px] text-ok">◉ NEW IMAGE SELECTED — APPLIES WHEN YOU SAVE</p>
@endif

<input type="file" id="{{ $id }}" accept="image/*" wire:model="{{ $model }}" class="peer sr-only">

<label for="{{ $id }}"
       class="group flex cursor-pointer items-center gap-3 rounded border-[1.5px] border-dashed border-line-strong px-3.5 py-3 text-[13px] text-ink-soft transition hover:border-primary hover:bg-surface-2 peer-focus-visible:border-primary peer-focus-visible:ring-[3px] peer-focus-visible:ring-primary/20">
    <svg class="h-[18px] w-[18px] text-muted transition group-hover:-translate-y-0.5 group-hover:text-primary motion-reduce:transition-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
    </svg>
    <span><b class="font-semibold text-ink">{{ $current ? 'Replace' : 'Upload' }} image</b> — click to browse</span>
    <span class="ml-auto rounded-[3px] border border-line bg-surface px-1.5 py-1 font-mono text-[9px] tracking-[0.14em] text-muted">{{ $tag }}</span>
</label>

@if($key)
    @error($key)
        <p class="mt-2 font-mono text-[11.5px] text-accent">▲ {{ $message }}</p>
    @enderror
@endif