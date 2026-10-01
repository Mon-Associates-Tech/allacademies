@props([
    'index' => null, 'label' => null, 'for' => null, 'required' => false,
    'tag' => null, 'pretext' => null, 'preview' => null, 'bare' => false,
])

<div {{ $attributes->merge(['class' => $bare ? 'min-w-0' : 'border-b border-dashed border-line py-5 first:pt-4 last:border-b-0']) }}>
    @if($label || $index !== null || $tag)
        <div class="mb-2 flex items-center gap-2.5">
            @if($index !== null)
                <span class="rounded-[3px] bg-accent/10 px-1.5 py-0.5 font-mono text-[10.5px] tracking-wider text-accent">
                    {{ str_pad((string) $index, 2, '0', STR_PAD_LEFT) }}
                </span>
            @endif
            @if($label)
                <label @if($for) for="{{ $for }}" @endif class="flex-1 cursor-pointer text-sm font-semibold text-ink">
                    {{ $label }}@if($required)<span class="ml-0.5 font-bold text-accent" title="Required">*</span>@endif
                </label>
            @endif
            @if($tag)
                <span class="rounded-[3px] border border-line-strong px-1.5 py-0.5 font-mono text-[9.5px] tracking-[0.14em] text-muted">{{ $tag }}</span>
            @endif
        </div>
    @endif

    @if($pretext)
        <p class="mb-2.5 font-mono text-[11px] text-muted">
            PRINTS AS
            <span class="ml-1 rounded-[3px] border border-line bg-surface-2 px-1.5 py-0.5 text-ink-soft">
                {{ $pretext }} <b class="font-semibold text-ink">{{ $preview ?: '…' }}</b>
            </span>
        </p>
    @endif

    {{ $slot }}
</div>