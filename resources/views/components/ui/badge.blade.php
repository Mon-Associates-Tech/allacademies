@props([
    'variant' => 'default', // default, success, info, warning, danger
    'size' => 'md', // sm, md
])

@php
$variants = [
    'default' => ['text' => 'text-slate-700 dark:text-slate-300', 'bg' => 'bg-slate-100 dark:bg-slate-800', 'border' => 'border-slate-200 dark:border-slate-700'],
    'success' => ['text' => 'text-emerald-700 dark:text-emerald-400', 'bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'border' => 'border-emerald-200 dark:border-emerald-800'],
    'info' => ['text' => 'text-blue-700 dark:text-blue-400', 'bg' => 'bg-blue-50 dark:bg-blue-900/20', 'border' => 'border-blue-200 dark:border-blue-800'],
    'warning' => ['text' => 'text-amber-700 dark:text-amber-400', 'bg' => 'bg-amber-50 dark:bg-amber-900/20', 'border' => 'border-amber-200 dark:border-amber-800'],
    'danger' => ['text' => 'text-red-700 dark:text-red-400', 'bg' => 'bg-red-50 dark:bg-red-900/20', 'border' => 'border-red-200 dark:border-red-800'],
];

$sizes = [
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-2.5 py-1 text-xs',
];

$style = $variants[$variant];
@endphp

<span {{ $attributes->class([
    'inline-flex items-center justify-center font-semibold border rounded-[2px]',
    $sizes[$size],
    $style['text'],
    $style['bg'],
    $style['border'],
]) }}
style="border-radius: var(--radius-sm);">
    {{ $slot }}
</span>