{{-- resources/views/components/ui/card-footer.blade.php --}}
@props([
    'variant' => 'default', // default, subtle
])

@php
    $variants = [
        'default' => 'border-t border-slate-200/50 dark:border-slate-800 pt-4',
        'subtle' => 'border-t border-slate-100/30 dark:border-slate-800/50 pt-4',
    ];
@endphp

<div {{ $attributes->class([$variants[$variant]]) }}>
    {{ $slot }}
</div>
