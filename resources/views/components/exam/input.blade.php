@props(['errorKey' => null])

@php
    $key = $errorKey
        ?? $attributes->get('wire:model')
        ?? $attributes->get('wire:model.live')
        ?? $attributes->get('wire:model.blur')
        ?? $attributes->get('wire:model.defer');
@endphp

<input {{ $attributes->merge(['class' => 'w-full rounded border border-line-strong bg-surface px-3 py-2.5 text-sm text-ink placeholder:text-muted transition hover:border-ink-soft focus:border-primary focus:outline-none focus:ring-[3px] focus:ring-primary/20']) }}>

@if($key)
    @error($key)
        <p class="mt-2 font-mono text-[11.5px] text-accent">▲ {{ $message }}</p>
    @enderror
@endif