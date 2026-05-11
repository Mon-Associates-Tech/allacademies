@props([
    'label',
    'value',
    'suffix' => null,
    'icon' => null,
    'accent' => 'primary',
    'trend' => null, // 'up', 'down', 'neutral'
])

@php
$accents = [
    'primary' => 'from-violet-600 to-violet-500',
    'success' => 'from-emerald-600 to-emerald-500',
    'info' => 'from-blue-600 to-blue-500',
    'warning' => 'from-amber-600 to-amber-500',
    'danger' => 'from-red-600 to-red-500',
];

$trendIcons = [
    'up' => ['icon' => 'arrow-trending-up', 'color' => 'text-emerald-600'],
    'down' => ['icon' => 'arrow-trending-down', 'color' => 'text-red-600'],
    'neutral' => ['icon' => 'minus', 'color' => 'text-slate-400'],
];
@endphp

<x-ui.card class="px-5 py-5 flex items-center gap-4">
    @if($icon)
        <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center rounded-[2px]"
             style="background: linear-gradient(135deg, var(--color-{{ explode('-', $accents[$accent])[1] ?? 'primary' }}), var(--color-{{ explode('-', $accents[$accent])[2] ?? 'primary-light' }}));">
            <x-dynamic-component :component="'heroicon-o-'.$icon" class="w-5 h-5 text-white"/>
        </div>
    @endif
    
    <div class="min-w-0">
        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" 
           style="font-size: var(--text-xs); letter-spacing: var(--tracking-wider);">
            {{ $label }}
        </p>
        <div class="flex items-baseline gap-1 mt-0.5">
            <p class="text-3xl font-bold text-slate-900 dark:text-white truncate" 
               style="letter-spacing: var(--tracking-tight);">
                {{ $value }}
            </p>
            @if($suffix)
                <span class="text-lg font-medium text-slate-500">{{ $suffix }}</span>
            @endif
        </div>
        @if($trend && isset($trendIcons[$trend]))
            <div class="flex items-center gap-1 mt-1">
                <x-dynamic-component :component="'heroicon-o-'.$trendIcons[$trend]['icon']" 
                                   class="w-3.5 h-3.5 {{ $trendIcons[$trend]['color'] }}"/>
                <span class="text-xs text-slate-500">vs last period</span>
            </div>
        @endif
    </div>
</x-ui.card>