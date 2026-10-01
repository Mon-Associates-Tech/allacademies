@props(['title', 'description' => null, 'compact' => false])

<div {{ $attributes->merge(['class' => 'animate-rise rounded-md border-[1.5px] border-dashed border-line-strong bg-surface text-center ' . ($compact ? 'px-6 py-8' : 'px-7 py-14')]) }}>
    @if(!$compact)
        <div class="mx-auto mb-4 h-[74px] w-[58px] border border-line-strong bg-surface-2 shadow-[0_10px_20px_-12px_rgba(20,24,34,0.3)]" aria-hidden="true">
            <div class="space-y-2 px-2.5 pt-3.5">
                <span class="block h-0.5 bg-line-strong"></span>
                <span class="block h-0.5 bg-line-strong"></span>
                <span class="block h-0.5 bg-line-strong"></span>
            </div>
        </div>
    @endif
    <h2 class="font-display {{ $compact ? 'text-base' : 'text-xl' }} font-bold">{{ $title }}</h2>
    @if($description)
        <p class="mx-auto mt-1.5 max-w-[44ch] text-[13.5px] leading-relaxed text-ink-soft">{{ $description }}</p>
    @endif
</div>