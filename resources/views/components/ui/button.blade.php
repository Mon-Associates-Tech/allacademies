@props([
    'variant' => 'primary', // primary, secondary, ghost, danger, success
    'size' => 'md', // sm, md, lg
    'icon' => null,
    'iconRight' => false,
    'loading' => false,
    'fullWidth' => false,
])

@php
$variants = [
    'primary' => [
        'bg' => 'bg-gradient-to-r from-violet-600 to-violet-500',
        'hover' => 'hover:from-violet-700 hover:to-violet-600',
        'text' => 'text-white',
        'shadow' => 'shadow-[0_2px_10px_rgba(124,58,237,0.3)]',
    ],
    'secondary' => [
        'bg' => 'bg-gradient-to-r from-slate-800 to-slate-700',
        'hover' => 'hover:from-slate-900 hover:to-slate-800',
        'text' => 'text-white',
        'shadow' => 'shadow-[0_2px_6px_rgba(0,0,0,0.15)]',
    ],
    'success' => [
        'bg' => 'bg-gradient-to-r from-emerald-600 to-emerald-500',
        'hover' => 'hover:from-emerald-700 hover:to-emerald-600',
        'text' => 'text-white',
        'shadow' => 'shadow-[0_2px_10px_rgba(5,150,105,0.3)]',
    ],
    'danger' => [
        'bg' => 'bg-gradient-to-r from-red-600 to-red-500',
        'hover' => 'hover:from-red-700 hover:to-red-600',
        'text' => 'text-white',
        'shadow' => 'shadow-[0_2px_10px_rgba(220,38,38,0.3)]',
    ],
    'ghost' => [
        'bg' => 'bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-700',
        'hover' => 'hover:bg-slate-100 dark:hover:bg-slate-700',
        'text' => 'text-slate-700 dark:text-slate-200',
        'border' => 'border border-slate-200 dark:border-slate-700',
    ],
];

$sizes = [
    'sm' => 'px-4 py-2 text-xs',
    'md' => 'px-5 py-2.5 text-sm',
    'lg' => 'px-6 py-3 text-base',
];

$classes = collect($variants[$variant])
    ->merge($sizes[$size])
    ->merge([
        'inline-flex items-center justify-center gap-2 font-semibold rounded-[2px] transition-all',
        $fullWidth ? 'w-full' : '',
        $loading ? 'opacity-75 cursor-wait' : '',
    ])
    ->implode(' ');
@endphp

<button {{ $attributes->class($classes) }}
        style="border-radius: var(--radius-sm); {{ $variants[$variant]['shadow'] ?? '' }}"
        {{ $loading ? 'disabled' : '' }}>
    @if($loading)
        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
    @elseif($icon && !$iconRight)
        <x-dynamic-component :component="'heroicon-o-'.$icon" class="w-4 h-4"/>
    @endif
    
    {{ $slot }}
    
    @if($icon && $iconRight)
        <x-dynamic-component :component="'heroicon-o-'.$icon" class="w-4 h-4"/>
    @endif
</button>