@props(['hint' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col items-stretch gap-4 text-center sm:flex-row sm:items-center sm:justify-between sm:text-left']) }}>
    @if($hint)
        <p class="font-mono text-[10px] tracking-[0.14em] text-muted">{{ $hint }}</p>
    @endif
    <div class="sm:ml-auto">{{ $slot }}</div>
</div>