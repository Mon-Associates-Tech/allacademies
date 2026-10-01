@props(['errorKey' => null, 'placeholder' => null])

@php
    $key = $errorKey
        ?? $attributes->get('wire:model')
        ?? $attributes->get('wire:model.live')
        ?? $attributes->get('wire:model.blur')
        ?? $attributes->get('wire:model.defer');
@endphp

<div class="relative">
    <select {{ $attributes->merge(['class' => 'w-full appearance-none rounded border border-line-strong bg-surface px-3 py-2.5 pr-9 text-sm text-ink transition hover:border-ink-soft focus:border-primary focus:outline-none focus:ring-[3px] focus:ring-primary/20 disabled:cursor-not-allowed disabled:opacity-50']) }}>
        @if($placeholder !== null)
            <option value="">{{ $placeholder }}</option>
        @endif
        {{ $slot }}
    </select>
    <svg class="pointer-events-none absolute right-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
</div>

@if($key)
    @error($key)
        <p class="mt-2 font-mono text-[11.5px] text-accent">▲ {{ $message }}</p>
    @enderror
@endif