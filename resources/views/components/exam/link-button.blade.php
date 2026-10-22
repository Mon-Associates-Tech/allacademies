@props(['tone' => 'primary'])

@php
    $tones = [
        'primary' => 'text-primary hover:text-primary-hi focus-visible:outline-primary',
        'danger'  => 'text-accent focus-visible:outline-accent',
        'ok'      => 'text-ok hover:text-ok focus-visible:outline-ok',
        'warn'    => 'text-warn hover:text-warn focus-visible:outline-warn',
        'muted'   => 'text-muted hover:text-ink focus-visible:outline-primary',
    ];
    
    // Safely fallback to primary if an unknown tone is passed
    $toneClass = $tones[$tone] ?? $tones['primary'];
@endphp

<button type="button" {{ $attributes->merge(['class' => "inline-flex items-center whitespace-nowrap border-b border-dashed border-transparent font-mono text-[11px] font-semibold uppercase tracking-wider transition hover:border-current focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 {$toneClass}"]) }}>{{ $slot }}</button>