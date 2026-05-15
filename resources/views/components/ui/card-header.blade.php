@props([
    'title',
    'accent' => 'primary',
    'subtitle' => null,
    'actions' => null,
])

@php
    $accentGradients = [
        'primary' => 'from-violet-600 to-violet-400',
        'success' => 'from-emerald-600 to-emerald-400',
        'info' => 'from-blue-600 to-blue-400',
        'warning' => 'from-amber-600 to-amber-400',
        'danger' => 'from-red-600 to-red-400',
    ];

    $backgroundGradient = match($accent) {
        'success' => 'bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/30 dark:to-emerald-900/20',
        'info' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-900/20',
        'warning' => 'bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/30 dark:to-amber-900/20',
        'danger' => 'bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/30 dark:to-red-900/20',
        default => 'bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800/50 dark:to-slate-800',
    };
@endphp

<div
    class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center gap-2 {{ $backgroundGradient }}">
    <div
        class="w-1 h-5 rounded-[1px] bg-gradient-to-b {{ $accentGradients[$accent] ?? 'from-slate-600 to-slate-400' }}"></div>
    <div class="flex-1 min-w-0">
        <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-[0.1em]">
            {{ $title }}
        </h2>
        @if($subtitle)
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $subtitle }}</p>
        @endif
    </div>
    @if($actions)
        <div>{{ $actions }}</div>
    @endif
</div>
