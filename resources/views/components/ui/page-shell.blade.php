@props([
    'title',
    'subtitle' => null,
    'headerGradient' => 'primary',
    'backLink' => null,
])

@php
$headerGradients = [
    'primary' => 'from-violet-600 to-violet-400',
    'success' => 'from-emerald-600 to-emerald-400',
    'info' => 'from-blue-600 to-blue-400',
    'warning' => 'from-amber-600 to-amber-400',
    'danger' => 'from-red-600 to-red-400',
];
@endphp

{{-- Page Shell Container --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
     style="font-family: var(--font-sans);">

    {{-- Page Header --}}
    <div class="overflow-hidden rounded-[2px]"
         style="background: var(--gradient-header); box-shadow: var(--shadow-header);">
        <div class="h-1 w-full" 
             style="background: linear-gradient(90deg, var(--color-{{ explode('-', $headerGradients[$headerGradient])[1] ?? 'primary' }}), var(--color-{{ explode('-', $headerGradients[$headerGradient])[2] ?? 'primary-light' }}), var(--color-{{ explode('-', $headerGradients[$headerGradient])[3] ?? 'primary' }}-light));">
        </div>
        <div class="px-7 py-6 {{ $backLink ? 'flex flex-col sm:flex-row sm:items-center justify-between gap-4' : '' }}">
            @if($backLink)
                <a href="{{ $backLink['url'] }}" 
                   class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-400 hover:text-amber-400 transition-colors mb-3">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    {{ $backLink['label'] ?? 'Back' }}
                </a>
            @endif
            
            <div>
                <h1 class="text-2xl font-bold text-white leading-snug" 
                    style="letter-spacing: var(--tracking-normal); font-family: var(--font-serif);">
                    {{ $title }}
                </h1>
                @if($subtitle)
                    <p class="text-slate-400 mt-2 text-sm">{{ $subtitle }}</p>
                @endif
            </div>
            
            @if(isset($actions))
                <div class="flex items-center gap-3">
                    {{ $actions }}
                </div>
            @endif
        </div>
    </div>

    {{-- Page Content --}}
    {{ $slot }}

</div>