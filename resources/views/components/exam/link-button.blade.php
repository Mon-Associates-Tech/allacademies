@props(['tone' => 'primary'])

@php
    $tones = [
        'primary' => 'text-primary hover:text-primary-hi focus-visible:outline-primary',
        'danger'  => 'text-accent focus-visible:outline-accent',
        'muted'   => 'text-muted hover:text-ink focus-visible:outline-primary',
    ];
@endphp

<button type="button" {{ $attributes->merge(['class' => "whitespace-nowrap border-b border-dashed border-transparent font-mono text-[11px] font-semibold uppercase tracking-wider transition hover:border-current focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 {$tones[$tone]}"]) }}>{{ $slot }}</button>