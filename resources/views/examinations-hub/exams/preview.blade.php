<x-layouts.app>
    {{-- ═══════════════════════════════════════════════════════════
         PAGE SHELL
    ═══════════════════════════════════════════════════════════ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
         style="font-family: 'system-ui', -apple-system, sans-serif;">

        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden"
             style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #7c3aed, #a78bfa, #c4b5fd);"></div>
            <div class="px-7 py-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-white leading-snug" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                        Examination Preview
                    </h1>
                    <p class="text-slate-400 mt-2 text-sm">
                        Review and edit questions before creating the examination
                    </p>
                </div>
                @if($hardenedMode)
                    <span class="inline-flex items-center justify-center text-xs font-semibold px-3 py-1 border"
                          style="border-radius: 2px; color:#92400e;background:#fffbeb;border-color:#fde68a;">
                        🔒 Hardened Mode
                    </span>
                @endif
            </div>
        </div>

        {{-- ── EXAM CONFIGURATION CARD ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #2563eb, #60a5fa); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Exam Configuration</h2>
            </div>
            <div class="p-5">
                <h3 class="font-bold text-slate-900 dark:text-white text-lg mb-4" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                    {{ $payload['title'] }}
                </h3>
                <div class="grid md:grid-cols-2 gap-5">
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Description</p>
                            <p class="text-sm text-slate-700 dark:text-slate-300 mt-1">{{ $payload['description'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Duration</p>
                            <p class="text-sm text-slate-700 dark:text-slate-300 mt-1">{{ $payload['duration_in_minutes'] ?? 'Not set' }} minutes</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Status</p>
                            <p class="text-sm text-slate-700 dark:text-slate-300 mt-1">{{ ucfirst($payload['status']) }}</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Start</p>
                            <p class="text-sm text-slate-700 dark:text-slate-300 mt-1">{{ $payload['starts_at'] }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">End</p>
                            <p class="text-sm text-slate-700 dark:text-slate-300 mt-1">{{ $payload['ends_at'] }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Participant Mode</p>
                            <p class="text-sm text-slate-700 dark:text-slate-300 mt-1">{{ ucfirst($payload['participant_mode']) }}</p>
                        </div>
                    </div>
                </div>
                @if(!empty($payload['instructions']))
                    <div class="mt-5 p-4 border"
                         style="border-radius: 2px; color:#1d4ed8;background:#eff6ff;border-color:#bfdbfe;">
                        <p class="text-xs font-semibold uppercase tracking-wider" style="letter-spacing: 0.08em;">Instructions</p>
                        <p class="text-sm text-slate-700 dark:text-slate-300 mt-2">{{ $payload['instructions'] }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ── SECTION OVERVIEW CARD ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Section Overview</h2>
            </div>
            <div class="p-5 space-y-4">
                @foreach($payload['sections'] as $i => $section)
                    <div class="p-4 border hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors"
                         style="border-radius: 2px; border-color: rgba(0,0,0,0.06);">
                        <div class="flex items-center justify-between gap-3">
                            <h3 class="font-semibold text-slate-900 dark:text-white">{{ $i + 1 }}. {{ $section['title'] }}</h3>
                            <span class="inline-flex items-center justify-center text-xs font-semibold px-2.5 py-1 border"
                                  style="border-radius: 2px; color:#1d4ed8;background:#eff6ff;border-color:#bfdbfe;">
                                {{ $section['source_type'] }}
                            </span>
                        </div>
                        <div class="grid grid-cols-3 gap-3 mt-3">
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Type</p>
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mt-1">{{ str_replace('_', ' ', $section['question_type']) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Questions</p>
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mt-1">{{ $section['question_count'] }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Time</p>
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mt-1">{{ $section['time_limit_minutes'] ?: 'No limit' }} min</p>
                            </div>
                        </div>
                        @if(!empty($section['instructions']))
                            <p class="text-sm text-slate-600 dark:text-slate-400 mt-3">{{ $section['instructions'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── QUESTION EDITOR (Livewire) ── --}}
        <form method="POST" action="{{ route('examinations-hub.create.store') }}">
            @csrf
            <input type="hidden" name="payload_json" value="{{ $payloadJson }}">
            
            @livewire('examinations.question-editor', [
                'sections' => $payload['sections'],
                'questions' => $generatedQuestions,
                'hardenedMode' => $hardenedMode,
            ], key('question-editor-'.md5($payloadJson)))

            {{-- ── FORM ACTIONS ── --}}
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-2">
                <a href="{{ route('examinations-hub.create', ['draft' => $payloadJson]) }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200 transition-all border"
                   style="border-radius: 2px; border-color: rgba(0,0,0,0.06); background: linear-gradient(135deg, #f8fafc, #f1f5f9);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Edit
                </a>
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white transition-all"
                        style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
                    Create Examination
                </button>
            </div>
        </form>

    </div>{{-- /container --}}
</x-layouts.app>