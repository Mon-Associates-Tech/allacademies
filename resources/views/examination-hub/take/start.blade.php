<x-layouts.exam>
    {{-- ═══════════════════════════════════════════════════════════
         PAGE SHELL
    ═══════════════════════════════════════════════════════════ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
         style="font-family: 'system-ui', -apple-system, sans-serif;">

        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden"
             style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #2563eb, #60a5fa, #93c5fd);"></div>
            <div class="px-7 py-6">
                <h1 class="text-2xl font-bold text-white leading-snug" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                    {{ $exam->title }}
                </h1>
                @if($exam->description)
                    <p class="text-slate-400 mt-2 text-sm">{{ $exam->description }}</p>
                @endif
            </div>
        </div>

        {{-- ── METRICS STRIP ── --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Duration --}}
            <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                     style="border-radius: 2px; background: linear-gradient(135deg, #1d4ed8, #3b82f6);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">Duration</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white mt-0.5" style="letter-spacing: -0.04em;">{{ $exam->duration_in_minutes ?? 'Unlimited' }}<span class="text-base font-medium text-slate-500"> min</span></p>
                </div>
            </div>

            {{-- Sections --}}
            <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                     style="border-radius: 2px; background: linear-gradient(135deg, #065f46, #059669);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">Sections</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white mt-0.5" style="letter-spacing: -0.04em;">{{ $exam->sections->count() }}</p>
                </div>
            </div>

            {{-- Total Questions --}}
            <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                     style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">Total Questions</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white mt-0.5" style="letter-spacing: -0.04em;">{{ $exam->sections->sum('question_count') }}</p>
                </div>
            </div>
        </div>

        {{-- ── INSTRUCTIONS CARD ── --}}
        @if($exam->instructions)
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(217,119,6,0.2); box-shadow: 0 1px 6px rgba(217,119,6,0.08);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2"
                     style="background: linear-gradient(135deg, #fffbeb, #fef3c7);">
                    <div class="w-1 h-5" style="background: linear-gradient(180deg, #b45309, #d97706); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Important Instructions</h2>
                </div>
                <div class="p-5">
                    <div class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line">{{ $exam->instructions }}</div>
                </div>
            </div>
        @endif

        {{-- ── SECTIONS LIST ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Examination Sections</h2>
            </div>
            <div class="p-5 space-y-4">
                @foreach($exam->sections as $index => $section)
                    <div class="p-4 border hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors"
                         style="border-radius: 2px; border-color: rgba(0,0,0,0.06);">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-slate-900 dark:text-white">{{ $index + 1 }}. {{ $section->title }}</h3>
                                @if($section->description)
                                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">{{ $section->description }}</p>
                                @endif
                                <div class="flex items-center gap-3 mt-2 text-xs text-slate-500 dark:text-slate-400">
                                    <span>{{ $section->question_count }} questions</span>
                                    @if($section->time_limit_minutes)
                                        <span>• {{ $section->time_limit_minutes }} minutes</span>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('examination-hub.take.section', [$exam, $index]) }}" 
                               class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-white transition-all"
                               style="border-radius: 2px; background: linear-gradient(135deg, #1e293b, #334155); box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                                Start Section
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── WARNING CARD ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(220,38,38,0.2); box-shadow: 0 1px 6px rgba(220,38,38,0.08);">
            <div class="px-5 py-4 flex items-center gap-3">
                <div class="w-8 h-8 flex-shrink-0 flex items-center justify-center"
                     style="border-radius: 2px; background: linear-gradient(135deg, #dc2626, #ef4444);">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <p class="text-sm text-slate-700 dark:text-slate-300">
                    <strong>Note:</strong> Once you start, the timer will begin. Make sure you're ready before proceeding.
                </p>
            </div>
        </div>

        {{-- ── FORM ACTIONS ── --}}
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-2">
            <a href="{{ route('examination-hub.take.join') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200 transition-all border"
               style="border-radius: 2px; border-color: rgba(0,0,0,0.06); background: linear-gradient(135deg, #f8fafc, #f1f5f9);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Exit
            </a>
            <a href="{{ route('examination-hub.take.section', [$exam, 0]) }}" 
               class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white transition-all"
               style="border-radius: 2px; background: linear-gradient(135deg, #2563eb, #60a5fa); box-shadow: 0 2px 10px rgba(37,99,235,0.3);">
                Begin Examination
            </a>
        </div>

    </div>{{-- /container --}}
</x-layouts.exam>