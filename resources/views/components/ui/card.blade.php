{{-- resources/views/components/ui/card.blade.php --}}
@props([
    'variant' => 'default', // default, header, accent
    'accent' => 'primary', // primary, success, info, warning, danger
    'borderless' => false,
    'shadow' => true,
])

@php
$variants = [
    'default' => [
        'bg' => 'bg-white dark:bg-slate-900',
        'border' => $borderless ? '' : 'border border-slate-200 dark:border-slate-700',
        'shadow' => $shadow ? 'shadow-sm' : '',
    ],
    'header' => [
        'bg' => 'bg-gradient-to-br from-slate-900 to-slate-800',
        'border' => '',
        'shadow' => 'shadow-lg',
        'text' => 'text-white',
    ],
    'accent' => [
        'bg' => match($accent) {
            'success' => 'bg-emerald-50 dark:bg-emerald-900/20',
            'info' => 'bg-blue-50 dark:bg-blue-900/20',
            'warning' => 'bg-amber-50 dark:bg-amber-900/20',
            'danger' => 'bg-red-50 dark:bg-red-900/20',
            default => 'bg-slate-50 dark:bg-slate-800/50',
        },
        'border' => match($accent) {
            'success' => 'border-emerald-200 dark:border-emerald-800',
            'info' => 'border-blue-200 dark:border-blue-800',
            'warning' => 'border-amber-200 dark:border-amber-800',
            'danger' => 'border-red-200 dark:border-red-800',
            default => 'border-slate-200 dark:border-slate-700',
        },
        'shadow' => $shadow ? 'shadow-sm' : '',
    ],
];

$classes = collect($variants[$variant])
    ->filter()
    ->implode(' ');
@endphp

<div {{ $attributes->class([
    'overflow-hidden rounded-[2px]',
    $classes,
    'transition-all duration-200 hover:shadow-md' => $shadow,
]) }}>
    {{ $slot }}
</div>