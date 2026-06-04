<x-layouts.exam>
    <div class="h-screen flex items-center justify-center bg-slate-50 dark:bg-slate-950 px-4 sm:px-6">
        <div class="w-full max-w-lg">
            <div class="flex items-center justify-end mb-4">
                <x-snippets.theme-toggle />
            </div>

            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-hidden"
                 style="border-radius: 2px; box-shadow: 0 0 0 1px rgba(0,0,0,0.06), 0 20px 60px -10px rgba(0,0,0,0.12);">

                <div class="h-1 w-full" style="background: linear-gradient(90deg, #b45309, #d97706, #fbbf24);"></div>

                <div class="px-8 py-7">
                    <p class="text-xs font-medium tracking-widest uppercase text-amber-600 dark:text-amber-500 mb-1"
                       style="letter-spacing: 0.15em;">Online Examination</p>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white leading-snug mb-1"
                        style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                        {{ $exam->title }}
                    </h1>
                    @if($exam->instructions)
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">{{ $exam->instructions }}</p>
                    @endif
                </div>

                {{-- Countdown banner — only reason this page is shown --}}
                <div class="px-8 py-6 bg-amber-50 dark:bg-amber-950/30 border-t border-amber-200 dark:border-amber-800">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-amber-800 dark:text-amber-200">Exam not yet open</p>
                            <p class="text-sm text-amber-700 dark:text-amber-300 mt-1">
                                Opens {{ $exam->starts_at->format('F j, Y \a\t g:i A') }}
                            </p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <div id="countdown-display"
                                 class="text-3xl font-bold font-mono text-amber-800 dark:text-amber-200 tabular-nums">
                                --:--:--
                            </div>
                            <p class="text-xs text-amber-600 dark:text-amber-400 mt-0.5">until start</p>
                        </div>
                    </div>

                    <p class="mt-4 text-xs text-amber-600 dark:text-amber-500">
                        This page will redirect you automatically when the exam opens. You may keep this tab open.
                    </p>
                </div>

                <div class="px-8 py-5 border-t border-slate-100 dark:border-slate-800 grid grid-cols-3 gap-3 text-sm">
                    <div class="p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700"
                         style="border-radius: 2px;">
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1"
                           style="font-size: 10px;">Sections</p>
                        <p class="font-semibold text-slate-900 dark:text-white">{{ $exam->sections->count() }}</p>
                    </div>
                    <div class="p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700"
                         style="border-radius: 2px;">
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1"
                           style="font-size: 10px;">Questions</p>
                        <p class="font-semibold text-slate-900 dark:text-white">
                            {{ $exam->sections->sum('questions_count') }}
                        </p>
                    </div>
                    <div class="p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700"
                         style="border-radius: 2px;">
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1"
                           style="font-size: 10px;">Duration</p>
                        <p class="font-semibold text-slate-900 dark:text-white">
                            {{ $exam->duration_in_minutes ? $exam->duration_in_minutes . ' min' : 'No limit' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const startsAt = new Date('{{ $exam->starts_at->toISOString() }}');
            const display  = document.getElementById('countdown-display');

            function tick() {
                const diff = startsAt.getTime() - Date.now();

                if (diff <= 0) {
                    display.textContent = '00:00:00';
                    // Redirect to section 0 — the controller will handle it from here.
                    window.location.href = '{{ route('examination-hub.take.section', [$exam, 0]) }}';
                    return;
                }

                const h = Math.floor(diff / 3600000);
                const m = Math.floor((diff % 3600000) / 60000);
                const s = Math.floor((diff % 60000) / 1000);

                display.textContent =
                    String(h).padStart(2, '0') + ':' +
                    String(m).padStart(2, '0') + ':' +
                    String(s).padStart(2, '0');
            }

            tick();
            setInterval(tick, 1000);
        })();
    </script>
</x-layouts.exam>
