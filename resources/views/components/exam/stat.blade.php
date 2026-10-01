@props(['label', 'value', 'tone' => null])

@php($toneClass = ['ok' => 'text-ok', 'danger' => 'text-accent', 'primary' => 'text-primary'][$tone] ?? 'text-ink')

<div {{ $attributes->merge(['class' => 'rounded-md border border-line bg-surface p-4 text-center shadow-[0_1px_2px_rgba(20,24,34,0.04)]']) }}>
    <p class="font-display text-2xl font-bold {{ $toneClass }}">{{ $value }}</p>
    <p class="mt-1 font-mono text-[10px] uppercase tracking-[0.14em] text-muted">{{ $label }}</p>
</div>