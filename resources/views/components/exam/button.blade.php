@props(['target' => null, 'loading' => 'Saving…', 'variant' => 'primary', 'clientLoading' => false])

@php
    $variants = [
        'primary' => 'bg-primary text-on-primary shadow-[0_1px_0_rgba(20,24,34,0.2),0_8px_18px_-10px_rgba(20,24,34,0.5)] hover:bg-primary-hi',
        'accent'  => 'bg-accent text-white shadow-[0_1px_0_rgba(20,24,34,0.2),0_8px_18px_-10px_rgba(178,58,42,0.5)] hover:opacity-90',
        'danger'  => 'bg-accent text-white shadow-[0_1px_0_rgba(20,24,34,0.2),0_8px_18px_-10px_rgba(178,58,42,0.5)] hover:opacity-90',
        'ok'      => 'bg-ok text-white shadow-[0_1px_0_rgba(20,24,34,0.2),0_8px_18px_-10px_rgba(30,122,77,0.5)] hover:opacity-90',
        'warn'    => 'bg-warn text-white shadow-[0_1px_0_rgba(20,24,34,0.2),0_8px_18px_-10px_rgba(180,83,9,0.5)] hover:opacity-90',
        'ghost'   => 'border border-line-strong bg-transparent text-ink-soft hover:bg-surface-2 hover:text-ink',
    ];

    // Safely fallback to primary if an unknown variant is passed
    $variantClass = $variants[$variant] ?? $variants['primary'];
@endphp

<button
    {{ $attributes->merge(['class' => "inline-flex items-center justify-center whitespace-nowrap rounded px-5 py-2.5 text-[13.5px] font-semibold tracking-wide transition hover:-translate-y-px focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent active:translate-y-0 disabled:cursor-wait disabled:opacity-65 disabled:hover:translate-y-0 motion-reduce:transition-none {$variantClass}"]) }}
    @if($target) wire:loading.attr="disabled" wire:target="{{ $target }}" @endif
    @if($clientLoading) data-client-loading="true" @endif
>
    @if($target)
        <span wire:loading.remove wire:target="{{ $target }}">{{ $slot }}</span>
        <span wire:loading wire:target="{{ $target }}">
            <span class="mr-2 inline-block h-3.5 w-3.5 animate-spin rounded-full border-2 border-current border-t-transparent align-[-2px]"></span>{{ $loading }}
        </span>
    @elseif($clientLoading)
        <span class="client-btn-default inline-flex items-center">{{ $slot }}</span>
        <span class="client-btn-loading hidden inline-flex items-center">
            <span class="mr-2 inline-block h-3.5 w-3.5 animate-spin rounded-full border-2 border-current border-t-transparent align-[-2px]"></span>{{ $loading }}
        </span>
    @else
        {{ $slot }}
    @endif
</button>