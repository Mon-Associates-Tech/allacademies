<x-layouts.exam>
    <div class="min-h-screen bg-slate-50 dark:bg-slate-950">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8 sm:py-10">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800">
                    <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Examination</p>
                    <h1 class="mt-1 text-xl font-semibold text-slate-900 dark:text-white">{{ $exam->title }}</h1>
                    @if($exam->instructions)
                        <p class="mt-3 text-sm text-slate-600 dark:text-slate-300 whitespace-pre-line">{{ $exam->instructions }}</p>
                    @endif
                </div>

                <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                    <div class="p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-sm">
                        <p class="text-slate-500 dark:text-slate-400">Sections</p>
                        <p class="mt-1 font-semibold text-slate-900 dark:text-white">{{ $exam->sections->count() }}</p>
                    </div>
                    <div class="p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-sm">
                        <p class="text-slate-500 dark:text-slate-400">Questions</p>
                        <p class="mt-1 font-semibold text-slate-900 dark:text-white">{{ $exam->sections->sum('questions_count') }}</p>
                    </div>
                    <div class="p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-sm">
                        <p class="text-slate-500 dark:text-slate-400">Duration</p>
                        <p class="mt-1 font-semibold text-slate-900 dark:text-white">
                            {{ $exam->duration_in_minutes ? $exam->duration_in_minutes . ' minutes' : 'No limit' }}
                        </p>
                    </div>
                </div>

                @if($exam->proctoring_enabled)
                    <div class="px-6 py-4 bg-amber-50 dark:bg-amber-950/40 border-t border-amber-200 dark:border-amber-800 flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <div class="text-sm">
                            <p class="font-semibold text-amber-800 dark:text-amber-300">This exam is proctored</p>
                            <p class="mt-0.5 text-amber-700 dark:text-amber-400">Your session will be monitored. The exam must be taken in fullscreen mode. Exiting fullscreen, switching tabs, or copying will be recorded as violations.</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="mt-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Sections</h2>
                </div>

                <div class="divide-y divide-slate-200 dark:divide-slate-800">
                    @foreach($exam->sections as $index => $section)
                        <div class="px-6 py-4 flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-slate-900 dark:text-white">
                                    Section {{ $index + 1 }}: {{ $section->title }}
                                </p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                    {{ $section->questions_count }} questions
                                    @if($section->time_limit_minutes)
                                        · {{ $section->time_limit_minutes }} min limit
                                    @endif
                                </p>
                            </div>

                            @if($exam->proctoring_enabled && $index === 0)
                                {{-- Proctored: first section requires fullscreen entry --}}
                                <button onclick="enterFullscreenAndStart('{{ route('examination-hub.take.section', [$exam, 0]) }}')"
                                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-slate-800 hover:bg-slate-700 rounded-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                                    Enter Fullscreen & Start
                                </button>
                            @elseif($exam->proctoring_enabled)
                                <span class="text-xs text-slate-400 dark:text-slate-500 italic">Complete previous section first</span>
                            @else
                                <a href="{{ route('examination-hub.take.section', [$exam, $index]) }}"
                                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-slate-800 hover:bg-slate-700 rounded-sm">
                                    {{ $index === 0 ? 'Start' : 'Open' }}
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        function enterFullscreenAndStart(url) {
            const el = document.documentElement;
            const req = el.requestFullscreen || el.webkitRequestFullscreen || el.mozRequestFullScreen || el.msRequestFullscreen;
            if (req) {
                req.call(el).then(() => { window.location.href = url; })
                            .catch(() => { window.location.href = url; }); // navigate anyway if denied
            } else {
                window.location.href = url;
            }
        }
    </script>
</x-layouts.exam>
