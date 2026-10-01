@props(['tone' => 'primary'])

@php
    $tones = [
        'primary' => 'border-primary/25 bg-primary/5',
        'ok'      => 'border-ok/30 bg-ok/10',
        'warn'    => 'border-warn/30 bg-warn/10',
        'danger'  => 'border-accent/30 bg-accent/10',
    ];
    
    // Safely fallback to primary
    $toneClass = $tones[$tone] ?? $tones['primary'];
@endphp

<div {{ $attributes->merge(['class' => "rounded-md border p-4 {$toneClass}"]) }}>{{ $slot }}</div>