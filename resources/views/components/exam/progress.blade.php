@props(['value' => 0, 'tone' => null, 'size' => 'md'])

@php
    $v = max(0, min(100, (float) $value));
    $tone ??= $v >= 80 ? 'ok' : ($v >= 60 ? 'primary' : ($v >= 30 ? 'warn' : 'danger'));
    $bar = ['ok' => 'bg-ok', 'primary' => 'bg-primary', 'warn' => 'bg-warn', 'danger' => 'bg-accent'][$tone] ?? 'bg-primary';
    $h = ['sm' => 'h-1', 'md' => 'h-1.5', 'lg' => 'h-2.5'][$size] ?? 'h-1.5';
@endphp

<div {{ $attributes->merge(['class' => "w-full overflow-hidden rounded-full bg-line {$h}"]) }}
     role="progressbar" aria-valuenow="{{ round($v) }}" aria-valuemin="0" aria-valuemax="100">
    <div class="h-full rounded-full {{ $bar }} transition-all duration-300 motion-reduce:transition-none" style="width: {{ $v }}%"></div>
</div>
