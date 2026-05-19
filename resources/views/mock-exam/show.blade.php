<x-layouts.app>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
     style="font-family: 'system-ui', -apple-system, sans-serif;"
     x-data="{ tab: '{{ session('tab', 'overview') }}' }">

    {{-- ── PAGE HEADER ── --}}
    <div class="overflow-hidden"
         style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
        <div class="h-1 w-full" style="background: linear-gradient(90deg, #7c3aed, #a78bfa, #c4b5fd);"></div>
        <div class="px-7 py-6 flex items-start justify-between gap-4">
            <div class="flex items-start gap-4">
                <a href="{{ route('mock-exams.index') }}"
                   class="flex items-center justify-center w-8 h-8 mt-1 text-slate-400 hover:text-white border border-slate-700 hover:border-slate-500 transition-all shrink-0"
                   style="border-radius: 2px;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <h1 class="text-2xl font-bold text-white leading-snug"
                            style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                            {{ $mockExam->title }}
                        </h1>
                        {{-- Status --}}
                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold border"
                              style="border-radius: 2px;
                                     {{ $mockExam->status === 'published'
                                         ? 'color:#065f46; background:#ecfdf5; border-color:#6ee7b7;'
                                         : ($mockExam->status === 'closed'
                                             ? 'color:#374151; background:#f9fafb; border-color:#d1d5db;'
                                             : 'color:#92400e; background:#fffbeb; border-color:#fde68a;') }}">
                            {{ ucfirst($mockExam->status) }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold border"
                              style="border-radius: 2px; color:#1e40af; background:#eff6ff; border-color:#bfdbfe;">
                            {{ ucfirst($mockExam->delivery_type) }}
                        </span>
                    </div>
                    @if($mockExam->description)
                        <p class="text-slate-400 mt-1 text-sm">{{ $mockExam->description }}</p>
                    @endif
                </div>
            </div>

            {{-- Header actions --}}
            <div class="flex items-center gap-2 shrink-0 mt-1">
                @if($mockExam->isOnline() && $mockExam->status === 'published')
                    <a href="{{ route('mock-exams.monitor', $mockExam) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white border border-emerald-500 transition-all"
                       style="border-radius: 2px; background: linear-gradient(135deg, #059669, #34d399);">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Live Monitor
                    </a>
                @endif
                @if($mockExam->isPrint())
                    <a href="{{ route('mock-exams.pdf.exam', $mockExam) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white border border-blue-500 transition-all"
                       style="border-radius: 2px; background: linear-gradient(135deg, #1d4ed8, #3b82f6);">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Exam PDF
                    </a>
                    <a href="{{ route('mock-exams.pdf.answer-key', $mockExam) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-slate-300 border border-slate-600 hover:border-slate-400 transition-all"
                       style="border-radius: 2px;">
                        Answer Key
                    </a>
                @endif
                @if(!$mockExam->submissions()->exists())
                    <a href="{{ route('mock-exams.edit', $mockExam) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-slate-300 border border-slate-600 hover:border-slate-400 transition-all"
                       style="border-radius: 2px;">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                @endif
            </div>
        </div>

        {{-- Tab navigation inside header --}}
        <div class="flex border-t border-slate-700/50 overflow-x-auto">
            @foreach([
                'overview'     => ['label' => 'Overview',         'count' => null],
                'subjects'     => ['label' => 'Subject Exams',    'count' => $mockExam->subjectExams->count()],
                'participants' => ['label' => 'Participants',     'count' => $mockExam->configuredParticipants->count()],
                'results'      => ['label' => 'Results',          'count' => $submissions->total()],
            ] as $key => $tab)
                <button @click="tab = '{{ $key }}'"
                        :class="tab === '{{ $key }}'
                            ? 'text-white border-b-2 border-violet-400 bg-white/5'
                            : 'text-slate-400 border-b-2 border-transparent hover:text-slate-200 hover:bg-white/5'"
                        class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wider transition-all whitespace-nowrap"
                        style="letter-spacing: 0.08em;">
                    {{ $tab['label'] }}
                    @if($tab['count'] !== null)
                        <span class="ml-1.5 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold rounded-full"
                              :class="tab === '{{ $key }}' ? 'bg-violet-500/30 text-violet-200' : 'bg-slate-700 text-slate-300'">
                            {{ $tab['count'] }}
                        </span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    {{-- ── FLASH MESSAGES ── --}}
    @if(session('success'))
        <div class="px-5 py-3 text-sm text-emerald-800 flex items-center gap-2"
             style="border-radius: 2px; background: #f0fdf4; border: 1px solid #bbf7d0;">
            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {!! session('success') !!}
        </div>
    @endif

    {{-- ════════════════════════════════════════
         TAB: OVERVIEW
    ════════════════════════════════════════ --}}
    <div x-show="tab === 'overview'" x-transition>

        {{-- Metrics --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-7">
            @foreach([
                ['label' => 'Subject Exams',  'value' => $mockExam->subjectExams->count(),                          'gradient' => 'from-violet-600 to-violet-400'],
                ['label' => 'Total Questions','value' => $mockExam->getTotalQuestions(),                             'gradient' => 'from-blue-600 to-blue-400'],
                ['label' => 'Total Marks',    'value' => number_format($mockExam->getTotalMarks(), 1),               'gradient' => 'from-emerald-600 to-emerald-400'],
                ['label' => 'Submissions',    'value' => $submissions->total(),                                       'gradient' => 'from-amber-600 to-amber-400'],
            ] as $stat)
                <div class="bg-white dark:bg-slate-900 overflow-hidden"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="h-0.5 w-full bg-gradient-to-r {{ $stat['gradient'] }}"></div>
                    <div class="px-4 py-5 text-center">
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stat['value'] }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">{{ $stat['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="grid md:grid-cols-2 gap-5">
            {{-- Exam Details --}}
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Exam Details</h2>
                </div>
                <div class="divide-y divide-slate-50 dark:divide-slate-800">
                    @if($mockExam->access_code && $mockExam->isOnline())
                        <div class="px-5 py-3.5 flex items-center justify-between" x-data="{ copied: false, shareUrl: '{{ route('mock-exams.take.join') }}?code={{ $mockExam->access_code }}' }">
                            <span class="text-xs text-slate-500 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">Access Code</span>
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-bold text-xl text-violet-600 dark:text-violet-400 tracking-widest">{{ $mockExam->access_code }}</span>
                                <button @click="navigator.clipboard.writeText('{{ $mockExam->access_code }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                        class="inline-flex items-center justify-center w-7 h-7 text-slate-400 hover:text-violet-600 border border-slate-200 dark:border-slate-700 hover:border-violet-400 transition-all"
                                        style="border-radius: 2px;"
                                        title="Copy access code">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                                <button @click="navigator.clipboard.writeText(shareUrl); copied = true; setTimeout(() => copied = false, 2000)"
                                        class="inline-flex items-center justify-center w-7 h-7 text-slate-400 hover:text-blue-600 border border-slate-200 dark:border-slate-700 hover:border-blue-400 transition-all"
                                        style="border-radius: 2px;"
                                        title="Copy share URL with code">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-5.367 3 3 0 00-5.367 5.367zm0 0L9 12m0 0l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 115.367-5.367 3 3 0 01-5.367 5.367z"/>
                                    </svg>
                                </button>
                                <span x-show="copied" x-transition
                                      class="text-xs text-emerald-600 font-medium">
                                    Copied!
                                </span>
                            </div>
                        </div>
                    @endif
                    @foreach([
                        ['Participant Mode',   ucfirst($mockExam->participant_mode)],
                        ['Result Visibility',  str_replace('_', ' ', ucfirst($mockExam->result_visibility))],
                        ['Max Attempts',       $mockExam->max_attempts],
                        ['Randomised',         $mockExam->is_randomized ? 'Yes' : 'No'],
                    ] as [$label, $value])
                        <div class="px-5 py-3.5 flex items-center justify-between">
                            <span class="text-xs text-slate-500 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">{{ $label }}</span>
                            <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $value }}</span>
                        </div>
                    @endforeach
                    @if($mockExam->starts_at)
                        <div class="px-5 py-3.5 flex items-center justify-between">
                            <span class="text-xs text-slate-500 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">Opens</span>
                            <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $mockExam->starts_at->format('M d, Y H:i') }}</span>
                        </div>
                    @endif
                    @if($mockExam->ends_at)
                        <div class="px-5 py-3.5 flex items-center justify-between">
                            <span class="text-xs text-slate-500 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">Closes</span>
                            <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $mockExam->ends_at->format('M d, Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Result Release / Instructions --}}
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                @if($mockExam->isOnline() && $mockExam->result_visibility === 'manual_release')
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                        <div class="w-1 h-5" style="background: linear-gradient(180deg, #d97706, #fbbf24); border-radius: 1px;"></div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Result Release</h2>
                    </div>
                    <div class="p-5">
                        @if($mockExam->results_released)
                            <div class="flex items-center gap-2 text-emerald-700 mb-3">
                                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-sm font-semibold">Results released {{ $mockExam->results_released_at?->format('M d, Y H:i') }}</span>
                            </div>
                        @else
                            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
                                Results are hidden from participants until you release them.
                            </p>
                            <form method="POST" action="{{ route('mock-exams.results.release', $mockExam) }}">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all"
                                        style="border-radius: 2px; background: linear-gradient(135deg, #059669, #34d399); box-shadow: 0 2px 8px rgba(5,150,105,0.3);">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Release Results to All Participants
                                </button>
                            </form>
                        @endif
                    </div>
                @else
                    @if($mockExam->instructions)
                        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                            <div class="w-1 h-5" style="background: linear-gradient(180deg, #0891b2, #22d3ee); border-radius: 1px;"></div>
                            <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Candidate Instructions</h2>
                        </div>
                        <div class="p-5 text-sm text-slate-600 dark:text-slate-400 whitespace-pre-wrap">{{ $mockExam->instructions }}</div>
                    @else
                        <div class="p-8 text-center text-sm text-slate-400">No special instructions set.</div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════
         TAB: SUBJECT EXAMS
    ════════════════════════════════════════ --}}
    <div x-show="tab === 'subjects'" x-transition>

        <div class="flex justify-end mb-4">
            <a href="{{ route('mock-exams.subject-exams.create', $mockExam) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all"
               style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Subject Exam
            </a>
        </div>

        @forelse($mockExam->subjectExams as $se)
            <div class="bg-white dark:bg-slate-900 overflow-hidden mb-4"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);"
                 x-data="{ expanded: false }">

                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <button @click="expanded = !expanded"
                                class="flex items-center justify-center w-6 h-6 text-slate-400 hover:text-violet-600 transition-colors">
                            <svg class="w-4 h-4 transition-transform" :class="expanded && 'rotate-90'"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        <div>
                            <p class="font-bold text-slate-900 dark:text-white text-sm">{{ $se->getDisplayTitle() }}</p>
                            <p class="text-xs text-slate-500">{{ $se->academicSubject?->name }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        {{-- Mini metrics --}}
                        <div class="hidden sm:flex items-center gap-4 text-right">
                            <div>
                                <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $se->sections->count() }}</p>
                                <p class="text-xs text-slate-400 uppercase" style="font-size:9px; letter-spacing:0.1em;">Sections</p>
                            </div>
                            <div>
                                <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $se->sections->sum(fn($s) => $s->questions->count()) }}</p>
                                <p class="text-xs text-slate-400 uppercase" style="font-size:9px; letter-spacing:0.1em;">Questions</p>
                            </div>
                            <div>
                                <p class="text-lg font-bold text-slate-900 dark:text-white">{{ number_format($se->sections->sum(fn($s) => $s->questions->sum('marks')), 1) }}</p>
                                <p class="text-xs text-slate-400 uppercase" style="font-size:9px; letter-spacing:0.1em;">Marks</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                            <a href="{{ route('mock-exams.subject-exams.edit', [$mockExam, $se]) }}"
                               class="inline-flex items-center justify-center w-8 h-8 text-slate-400 hover:text-violet-600 border border-slate-200 dark:border-slate-700 hover:border-violet-400 transition-all"
                               style="border-radius: 2px;">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('mock-exams.subject-exams.destroy', [$mockExam, $se]) }}"
                                  onsubmit="return confirm('Remove this subject exam and all its questions?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center justify-center w-8 h-8 text-slate-400 hover:text-red-500 border border-slate-200 dark:border-slate-700 hover:border-red-400 transition-all"
                                        style="border-radius: 2px;">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Sections (expanded) --}}
                <div x-show="expanded" x-transition>
                    @foreach($se->sections as $section)
                        <div class="px-5 py-3.5 border-b border-slate-50 dark:border-slate-800/50 last:border-0 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-1 h-4 bg-violet-300 dark:bg-violet-700" style="border-radius: 1px;"></div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                        Section {{ $loop->iteration }}: {{ $section->title }}
                                    </p>
                                    @if($section->instructions)
                                        <p class="text-xs text-slate-400 mt-0.5">{{ Str::limit($section->instructions, 80) }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right shrink-0 ml-4">
                                <p class="text-xs text-slate-500">
                                    {{ ucfirst(str_replace('_', ' ', $section->question_type)) }} ·
                                    {{ $section->questions->count() }} Qs ·
                                    {{ number_format($section->getTotalMarks(), 1) }} marks
                                    @if($section->is_randomized) · <span class="text-violet-500">Randomised</span> @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-slate-900 text-center py-12"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
                <p class="text-sm text-slate-400">No subject exams yet. Add one to get started.</p>
            </div>
        @endforelse
    </div>

    {{-- ════════════════════════════════════════
         TAB: PARTICIPANTS
    ════════════════════════════════════════ --}}
    <div x-show="tab === 'participants'" x-transition>

        @if($mockExam->participant_mode !== 'configured')
            <div class="bg-white dark:bg-slate-900 p-8 text-center"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    This exam uses <strong>general</strong> participant mode — no pre-registration required.
                </p>
            </div>
        @else
            <div class="grid md:grid-cols-2 gap-5 mb-5">

                {{-- Add single participant --}}
                <div class="bg-white dark:bg-slate-900 overflow-hidden"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                        <div class="w-1 h-5" style="background: linear-gradient(180deg, #059669, #34d399); border-radius: 1px;"></div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Add Participant</h2>
                    </div>
                    <form method="POST" action="{{ route('mock-exams.participants.store', $mockExam) }}" class="p-5 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required placeholder="Student full name"
                                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 dark:bg-slate-800 dark:text-white transition-all"
                                   style="border-radius: 2px;">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" required placeholder="student@example.com"
                                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 dark:bg-slate-800 dark:text-white transition-all"
                                   style="border-radius: 2px;">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Unique Code (optional)</label>
                            <input type="text" name="unique_code" placeholder="e.g. STU-001"
                                   class="w-full px-4 py-2.5 text-sm font-mono border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 dark:bg-slate-800 dark:text-white transition-all"
                                   style="border-radius: 2px;">
                        </div>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all w-full justify-center"
                                style="border-radius: 2px; background: linear-gradient(135deg, #059669, #34d399); box-shadow: 0 2px 8px rgba(5,150,105,0.3);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            Add Participant
                        </button>
                    </form>
                </div>

                {{-- CSV Import --}}
                <div class="bg-white dark:bg-slate-900 overflow-hidden"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                        <div class="w-1 h-5" style="background: linear-gradient(180deg, #2563eb, #60a5fa); border-radius: 1px;"></div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Import from CSV</h2>
                    </div>
                    <form method="POST" action="{{ route('mock-exams.participants.import', $mockExam) }}"
                          enctype="multipart/form-data" class="p-5 space-y-4">
                        @csrf
                        <div class="p-3 text-xs text-slate-500 dark:text-slate-400"
                             style="border-radius: 2px; background: #f8fafc; border: 1px dashed #cbd5e1;">
                            <p class="font-semibold text-slate-600 dark:text-slate-300 mb-1">CSV Format</p>
                            <p>Column order: <code class="bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded font-mono">name, email, unique_code</code></p>
                            <p class="mt-0.5">Header row is optional. Max file size: 2MB.</p>
                        </div>
                        <input type="file" name="csv_file" accept=".csv,.txt"
                               class="w-full text-sm text-slate-600 dark:text-slate-300 file:mr-3 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 dark:file:bg-blue-900/30 dark:file:text-blue-400 hover:file:bg-blue-100 transition-all"
                               style="border-radius: 2px;">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all w-full justify-center"
                                style="border-radius: 2px; background: linear-gradient(135deg, #1d4ed8, #3b82f6); box-shadow: 0 2px 8px rgba(29,78,216,0.3);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            Import CSV
                        </button>
                    </form>
                </div>
            </div>

            {{-- Participants Table --}}
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">
                            Registered Participants
                        </h2>
                    </div>
                    <span class="text-xs text-slate-500">{{ $mockExam->configuredParticipants->count() }} total</span>
                </div>

                @if($mockExam->configuredParticipants->isEmpty())
                    <div class="py-10 text-center text-sm text-slate-400">No participants added yet.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-100 dark:border-slate-800">
                                    <th class="text-left px-5 py-3 text-slate-500 font-semibold uppercase" style="font-size:10px; letter-spacing:0.1em;">Name</th>
                                    <th class="text-left px-5 py-3 text-slate-500 font-semibold uppercase" style="font-size:10px; letter-spacing:0.1em;">Email</th>
                                    <th class="text-left px-5 py-3 text-slate-500 font-semibold uppercase" style="font-size:10px; letter-spacing:0.1em;">Code</th>
                                    <th class="text-left px-5 py-3 text-slate-500 font-semibold uppercase" style="font-size:10px; letter-spacing:0.1em;">Status</th>
                                    <th class="px-5 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                                @foreach($mockExam->configuredParticipants as $p)
                                    <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition-colors">
                                        <td class="px-5 py-3.5 font-medium text-slate-800 dark:text-slate-200">{{ $p->name }}</td>
                                        <td class="px-5 py-3.5 text-slate-500">{{ $p->email }}</td>
                                        <td class="px-5 py-3.5 font-mono text-slate-500">{{ $p->unique_code ?? '—' }}</td>
                                        <td class="px-5 py-3.5">
                                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold border"
                                                  style="border-radius: 2px; {{ $p->is_active ? 'color:#065f46; background:#ecfdf5; border-color:#6ee7b7;' : 'color:#6b7280; background:#f9fafb; border-color:#d1d5db;' }}">
                                                {{ $p->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5 text-right">
                                            <form method="POST" action="{{ route('mock-exams.participants.destroy', [$mockExam, $p]) }}">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium transition-colors">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif
    </div>

    {{-- ════════════════════════════════════════
         TAB: RESULTS
    ════════════════════════════════════════ --}}
    <div x-show="tab === 'results'" x-transition>
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Submissions</h2>
                </div>
                <a href="{{ route('mock-exams.results.index', $mockExam) }}"
                   class="text-xs text-violet-600 hover:text-violet-800 font-medium transition-colors">View full results →</a>
            </div>

            @if($submissions->isEmpty())
                <div class="py-12 text-center text-sm text-slate-400">No submissions yet.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 dark:border-slate-800">
                                <th class="text-left px-5 py-3 text-slate-500 font-semibold uppercase" style="font-size:10px; letter-spacing:0.1em;">Participant</th>
                                <th class="text-left px-5 py-3 text-slate-500 font-semibold uppercase" style="font-size:10px; letter-spacing:0.1em;">Submitted</th>
                                <th class="text-left px-5 py-3 text-slate-500 font-semibold uppercase" style="font-size:10px; letter-spacing:0.1em;">Score</th>
                                <th class="text-left px-5 py-3 text-slate-500 font-semibold uppercase" style="font-size:10px; letter-spacing:0.1em;">Grade</th>
                                <th class="text-left px-5 py-3 text-slate-500 font-semibold uppercase" style="font-size:10px; letter-spacing:0.1em;">Status</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                            @foreach($submissions as $sub)
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <p class="font-medium text-slate-800 dark:text-slate-200">{{ $sub->participant_name }}</p>
                                        <p class="text-xs text-slate-400">{{ $sub->participant_email }}</p>
                                    </td>
                                    <td class="px-5 py-3.5 text-xs text-slate-500">{{ $sub->submitted_at?->format('M d, H:i') ?? '—' }}</td>
                                    <td class="px-5 py-3.5">
                                        <span class="font-semibold text-slate-700 dark:text-slate-300">{{ number_format($sub->score ?? 0, 1) }}</span>
                                        <span class="text-slate-400">/ {{ number_format($sub->total_marks ?? 0, 1) }}</span>
                                        <span class="text-xs text-slate-400 ml-1">({{ number_format($sub->percentage ?? 0, 1) }}%)</span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if($sub->grade)
                                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-bold border"
                                                  style="border-radius: 2px; color:#5b21b6; background:#f5f3ff; border-color:#ddd6fe;">
                                                {{ $sub->grade }}
                                            </span>
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="text-xs text-slate-500">{{ str_replace('_', ' ', ucfirst($sub->status)) }}</span>
                                        @if($sub->requires_manual_review)
                                            <span class="ml-1 inline-flex items-center px-1.5 py-0.5 text-xs font-semibold border"
                                                  style="border-radius:2px; color:#92400e; background:#fffbeb; border-color:#fde68a;">Review</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <a href="{{ route('mock-exams.results.show', [$mockExam, $sub]) }}"
                                           class="text-xs text-violet-600 hover:text-violet-800 font-medium transition-colors">Detail →</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-3 border-t border-slate-100 dark:border-slate-800">
                    {{ $submissions->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
</x-layouts.app>