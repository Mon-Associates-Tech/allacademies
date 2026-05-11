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
@endphp

<div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2"
     style="background: linear-gradient(135deg, #f8fafc, #f1f5f9);">
    <div class="w-1 h-5 rounded-[1px]" 
         style="background: linear-gradient(180deg, var(--color-{{ $accent }}), var(--color-{{ $accent }}-light));">
    </div>
    <div class="flex-1 min-w-0">
        <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" 
            style="letter-spacing: 0.1em;">
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