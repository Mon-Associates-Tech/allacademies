@props(['tone' => 'muted'])

@php
    $tones = [
        'ok'      => 'bg-ok/10 text-ok ring-ok/30',
        'warn'    => 'bg-warn/10 text-warn ring-warn/30',
        'danger'  => 'bg-accent/10 text-accent ring-accent/30',
        'primary' => 'bg-primary/10 text-primary ring-primary/30',
        'muted'   => 'bg-muted/10 text-muted ring-muted/30',
    ];
    
    // Safely fallback to muted
    $toneClass = $tones[$tone] ?? $tones['muted'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 whitespace-nowrap rounded-full px-2 py-0.5 font-mono text-[10px] font-semibold uppercase tracking-wider ring-1 ring-inset {$toneClass}"]) }}>{{ $slot }}</span>