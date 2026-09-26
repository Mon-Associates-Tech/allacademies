@props([
    'title' => '',
    'subtitle' => '',
    'backUrl' => null,
])

<div class="overflow-hidden"
     style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
    <div class="h-1 w-full" style="background: linear-gradient(90deg, #7c3aed, #a78bfa, #c4b5fd);"></div>

    {{-- Added responsive flex-col on mobile to prevent squishing --}}
    <div class="px-7 py-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            @if($backUrl)
                <a href="{{ $backUrl }}"
                   class="flex items-center justify-center w-8 h-8 text-slate-400 hover:text-white border border-slate-700 hover:border-slate-500 transition-all"
                   style="border-radius: 2px;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
            @endif
            <div>
                <h1 class="text-2xl font-bold text-white leading-snug"
                    style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                    {{ $title }}
                </h1>
                @if($subtitle)
                    <p class="text-slate-400 mt-1 text-sm">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>
        </div>

        @isset($actions)
            <div class="flex items-center flex-wrap gap-2">
                {{ $actions }}
            </div>
        @endisset
    </div>
</div>
