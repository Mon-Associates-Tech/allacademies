<x-layouts.app>
    <x-examination-hub.navigation active="manage" />

    {{-- ═══════════════════════════════════════════════════════════
         PAGE SHELL
    ═══════════════════════════════════════════════════════════ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
         style="font-family: 'system-ui', -apple-system, sans-serif;">

        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden"
             style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #b45309, #d97706, #fbbf24);"></div>
            <div class="px-7 py-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-xs font-medium tracking-widest text-amber-400 uppercase mb-1" style="letter-spacing: 0.15em;">Examination Management</p>
                    <h1 class="text-2xl font-bold text-white leading-snug truncate" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                        {{ $exam->title }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2">
                        <span class="inline-flex items-center gap-1.5 text-xs text-slate-400">
                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            Code: <span class="font-mono font-semibold text-amber-400">{{ $exam->access_code }}</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-xs text-slate-400">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $exam->duration_in_minutes ?? 0 }} minutes
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-xs text-slate-400">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            {{ $exam->questions_count }} questions
                        </span>
                        @if($exam->is_randomized)
                            <span class="inline-flex items-center gap-1.5 text-xs text-purple-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                                Randomized
                            </span>
                        @endif
                        <span class="text-xs">
                            {{ $exam->starts_at ? 'Starts: '.$exam->starts_at->format('M d, Y \a\t h:i A') : 'No start date set' }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ route('examination-hub.live-monitoring.index', $exam) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white transition-all"
                       style="border-radius: 2px; background: linear-gradient(135deg, #065f46, #059669); box-shadow: 0 2px 8px rgba(5,150,105,0.3);">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-300 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-400"></span>
                        </span>
                        Live Monitoring
                    </a>
                    <a href="{{ route('examination-hub.exams.edit', $exam) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white transition-all"
                       style="border-radius: 2px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit Exam
                        @if($exam->starts_at && now()->gte($exam->starts_at) || $exam->submissions_count > 0)
                            <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        @endif
                    </a>
                    @if($exam->proctoring_enabled)
                        <a href="{{ route('examination-hub.proctoring.index', $exam) }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white transition-all"
                           style="border-radius: 2px; background: linear-gradient(135deg, #7c2d12, #b91c1c); box-shadow: 0 2px 8px rgba(185,28,28,0.3);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            Proctoring
                        </a>
                    @endif
                    <a href="{{ route('examination-hub.submissions.index', $exam) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white transition-all"
                       style="border-radius: 2px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        View Submissions
                    </a>
                </div>
            </div>
        </div>

        {{-- ── FLASH MESSAGES ── --}}
        @if(session('success'))
            <div class="flex items-start gap-3 px-5 py-4 border-l-4 border-emerald-500 bg-emerald-50 dark:bg-emerald-950/30"
                 style="border-radius: 2px;">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('warning'))
            <div class="flex items-start gap-3 px-5 py-4 border-l-4 border-amber-500 bg-amber-50 dark:bg-amber-950/30"
                 style="border-radius: 2px;">
                <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <p class="text-sm text-amber-800 dark:text-amber-300">{{ session('warning') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="flex items-start gap-3 px-5 py-4 border-l-4 border-red-500 bg-red-50 dark:bg-red-950/30"
                 style="border-radius: 2px;">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm text-red-800 dark:text-red-300">{{ session('error') }}</p>
            </div>
        @endif
        @if($errors->any())
            <div class="flex items-start gap-3 px-5 py-4 border-l-4 border-red-500 bg-red-50 dark:bg-red-950/30"
                 style="border-radius: 2px;">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="text-sm text-red-800 dark:text-red-300">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ── STATS STRIP ── --}}
        <div class="grid grid-cols-3 gap-4">
            @foreach([
                ['label' => 'Total Sections',   'value' => $exam->sections_count,    'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16'],
                ['label' => 'Total Questions',  'value' => $exam->questions_count,   'icon' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Submissions',      'value' => $exam->submissions_count, 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ] as $stat)
                <div class="bg-white dark:bg-slate-900 flex items-center gap-4 px-5 py-4"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0"
                         style="background: linear-gradient(135deg, #1e293b, #334155); border-radius: 2px;">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">{{ $stat['label'] }}</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white" style="letter-spacing: -0.03em;">{{ $stat['value'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── MAIN TWO-COLUMN GRID ── --}}
        <div class="grid md:grid-cols-2 gap-6 items-start">

            {{-- ┌─────────────────────────────────┐
                 │  SECTION NAVIGATOR               │
                 └─────────────────────────────────┘ --}}
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">

                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-1 h-5" style="background: linear-gradient(180deg, #b45309, #fbbf24); border-radius: 1px;"></div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase tracking-wider" style="letter-spacing: 0.08em;">Section Navigator</h2>
                    </div>
                    <span class="text-xs px-2 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400"
                          style="border-radius: 2px;">{{ $exam->sections_count }} sections</span>
                </div>

                <div class="p-5">
                    @if($exam->hardened_mode && $exam->starts_at && now()->lt($exam->starts_at))
                        <div class="flex items-start gap-3 p-4 mb-4 border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-950/30"
                             style="border-radius: 2px;">
                            <div class="w-8 h-8 flex items-center justify-center flex-shrink-0 bg-amber-100 dark:bg-amber-900"
                                 style="border-radius: 2px;">
                                <svg class="w-4 h-4 text-amber-700 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">Hardened Mode Active</p>
                                <p class="text-xs text-amber-700 dark:text-amber-400 mt-0.5 leading-relaxed">
                                    Questions are hidden until exam starts on
                                    <span class="font-medium">{{ $exam->starts_at->format('M d, Y \a\t h:i A') }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            @foreach($sectionNavigator as $section)
                                <div class="p-4 border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40"
                                     style="border-radius: 2px;">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex items-start gap-2.5">
                                            <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white flex-shrink-0 mt-0.5"
                                                  style="background: linear-gradient(135deg, #1e293b, #334155); border-radius: 2px;">
                                                {{ $section['index'] }}
                                            </span>
                                            <div>
                                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $section['title'] }}</p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                                    {{ $section['question_count'] }} questions
                                                    @if($section['time_limit_minutes'])
                                                        · {{ $section['time_limit_minutes'] }} min limit
                                                    @else
                                                        · No time limit
                                                    @endif
                                                </p>
                                                @if(!empty($section['instructions']))
                                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">{{ $section['instructions'] }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3 flex items-center gap-1.5 px-3 py-2 bg-slate-100 dark:bg-slate-800"
                                         style="border-radius: 2px;">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        <span class="text-xs text-slate-500 dark:text-slate-400">Questions hidden until start</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach($sectionNavigator as $section)
                                <div class="p-4 border border-slate-100 dark:border-slate-800 hover:border-amber-200 dark:hover:border-amber-800 transition-colors"
                                     style="border-radius: 2px;">
                                    <div class="flex items-start gap-2.5">
                                        <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white flex-shrink-0 mt-0.5"
                                              style="background: linear-gradient(135deg, #b45309, #d97706); border-radius: 2px;">
                                            {{ $section['index'] }}
                                        </span>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $section['title'] }}</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                                {{ $section['question_count'] }} questions
                                                @if($section['time_limit_minutes'])
                                                    · {{ $section['time_limit_minutes'] }} min limit
                                                @else
                                                    · No time limit
                                                @endif
                                            </p>
                                            @if(!empty($section['instructions']))
                                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed border-l-2 border-slate-200 dark:border-slate-700 pl-2">{{ $section['instructions'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- ┌─────────────────────────────────┐
                 │  RIGHT COLUMN                    │
                 └─────────────────────────────────┘ --}}
            <div class="space-y-5">

                {{-- ── EMAIL INVITATIONS ── --}}
                <div class="bg-white dark:bg-slate-900 overflow-hidden"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                        <div class="w-1 h-5" style="background: linear-gradient(180deg, #2563eb, #60a5fa); border-radius: 1px;"></div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase tracking-wider" style="letter-spacing: 0.08em;">Email Invitations & Reminders</h2>
                    </div>

                    <div class="p-5 space-y-5">
                        {{-- Info note --}}
                        <div class="flex items-start gap-2.5 p-3.5 bg-blue-50 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900"
                             style="border-radius: 2px;">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-xs text-blue-800 dark:text-blue-300 leading-relaxed">
                                Send invitations to all configured participants with exam details and a calendar file attachment.
                            </p>
                        </div>

                        {{-- Send invitations button --}}
                        <form action="{{ route('examination-hub.exams.send-invitations', $exam) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 py-2.5 text-sm font-semibold text-white transition-all"
                                    style="border-radius: 2px; background: linear-gradient(135deg, #1d4ed8, #2563eb); box-shadow: 0 2px 8px rgba(37,99,235,0.25);">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                Send Invitations Now
                            </button>
                        </form>

                        {{-- Divider --}}
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-100 dark:border-slate-800"></div></div>
                            <div class="relative flex justify-center"><span class="px-3 text-xs text-slate-400 bg-white dark:bg-slate-900 uppercase tracking-wider">Automated Reminders</span></div>
                        </div>

                        {{-- Reminder settings --}}
                        <form action="{{ route('examination-hub.exams.reminder-settings', $exam) }}" method="POST" class="space-y-3">
                            @csrf
                            <div class="flex items-center gap-3 px-3.5 py-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800"
                                 style="border-radius: 2px;">
                                <input type="checkbox" name="send_reminders" id="send_reminders" value="1"
                                       {{ $exam->send_reminders ? 'checked' : '' }}
                                       class="w-4 h-4 rounded text-amber-600 border-slate-300 dark:border-slate-600 focus:ring-amber-500">
                                <label for="send_reminders" class="text-sm font-medium text-slate-700 dark:text-slate-300">Enable Automatic Reminders</label>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5" style="font-size: 10px; letter-spacing: 0.1em;">Send Reminder On</label>
                                <input type="datetime-local" name="reminder_datetime"
                                       value="{{ $exam->reminder_datetime?->format('Y-m-d\TH:i') }}"
                                       class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
                                       style="border-radius: 2px;">
                            </div>

                            <button type="submit"
                                    class="w-full py-2 text-sm font-medium text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                                    style="border-radius: 2px;">
                                Save Reminder Settings
                            </button>
                        </form>

                        @if($exam->reminder_sent)
                            <div class="flex items-center gap-2 px-3.5 py-2.5 border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/30"
                                 style="border-radius: 2px;">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-xs text-emerald-800 dark:text-emerald-300">
                                    Reminder sent {{ $exam->reminder_sent_at->format('M d, Y \a\t h:i A') }}
                                </p>
                            </div>
                        @endif

                        <form action="{{ route('examination-hub.exams.send-reminder', $exam) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 py-2.5 text-sm font-semibold text-white transition-all"
                                    style="border-radius: 2px; background: linear-gradient(135deg, #b45309, #d97706); box-shadow: 0 2px 8px rgba(180,83,9,0.25);">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                Send Manual Reminder Now
                            </button>
                        </form>
                    </div>
                </div>

                {{-- ── PARTICIPANT SETTINGS ── --}}
                <div class="bg-white dark:bg-slate-900 overflow-hidden"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                            <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase tracking-wider" style="letter-spacing: 0.08em;">Participant Settings</h2>
                        </div>
                        <span class="text-xs px-2 py-1 font-medium text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-800"
                              style="border-radius: 2px;">{{ ucfirst($exam->participant_mode) }}</span>
                    </div>
                    <div class="p-5">
                        <dl class="space-y-2.5">
                            @foreach([
                                ['label' => 'Mode',            'value' => ucfirst($exam->participant_mode)],
                                ['label' => 'Match Rule',      'value' => ucfirst($exam->configured_match_mode ?? 'any')],
                                ['label' => 'Required Fields', 'value' => implode(', ', $exam->participant_required_fields ?? [])],
                                ['label' => 'Configured',      'value' => $configuredCount . ' participants'],
                            ] as $row)
                                <div class="flex items-center justify-between py-2 border-b border-slate-50 dark:border-slate-800 last:border-0">
                                    <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.08em;">{{ $row['label'] }}</dt>
                                    <dd class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $row['value'] }}</dd>
                                </div>
                            @endforeach
                        </dl>

                        <!-- Participant Mode Toggle Form -->
                        <form action="{{ route('examination-hub.exams.participant-mode', $exam) }}" method="POST" class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                            @csrf
                            @method('POST')
                            <div class="flex items-center gap-4">
                                <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Participant Mode:</label>
                                <div class="flex gap-2">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="participant_mode" value="general"
                                               {{ $exam->participant_mode === 'general' ? 'checked' : '' }}
                                               class="w-4 h-4 text-amber-600 border-slate-300 focus:ring-amber-500">
                                        <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">General</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="participant_mode" value="configured"
                                               {{ $exam->participant_mode === 'configured' ? 'checked' : '' }}
                                               class="w-4 h-4 text-amber-600 border-slate-300 focus:ring-amber-500">
                                        <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">Configured</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="participant_mode" value="both"
                                               {{ $exam->participant_mode === 'both' ? 'checked' : '' }}
                                               class="w-4 h-4 text-amber-600 border-slate-300 focus:ring-amber-500">
                                        <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">Both</span>
                                    </label>
                                </div>
                                <button type="submit" class="ml-4 px-3 py-1.5 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded border border-transparent focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500" style="border-radius: 2px;">
                                    Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ── RESULTS AVAILABILITY ── --}}
                <div class="bg-white dark:bg-slate-900 overflow-hidden"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                        <div class="w-1 h-5" style="background: linear-gradient(180deg, #b91c1c, #ef4444); border-radius: 1px;"></div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase tracking-wider" style="letter-spacing: 0.08em;">Proctoring Auto-Submit</h2>
                    </div>
                    <div class="p-5 space-y-4">
                        <form action="{{ route('examination-hub.exams.proctoring-settings', $exam) }}" method="POST" class="space-y-4">
                            @csrf
                            <label class="flex items-center gap-3">
                                <input type="checkbox" name="proctoring_enabled" value="1"
                                       {{ $exam->proctoring_enabled ? 'checked' : '' }}
                                       class="w-4 h-4 rounded text-amber-600 border-slate-300 dark:border-slate-600 focus:ring-amber-500">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Enable Proctoring</span>
                            </label>

                            <label class="flex items-center gap-3">
                                <input type="checkbox" name="auto_submit_on_violation" value="1"
                                       {{ $exam->auto_submit_on_violation ? 'checked' : '' }}
                                       class="w-4 h-4 rounded text-amber-600 border-slate-300 dark:border-slate-600 focus:ring-amber-500">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Enable Auto-Submit on Violations</span>
                            </label>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5" style="font-size: 10px; letter-spacing: 0.1em;">High Severity Threshold</label>
                                    <input type="number" min="0" max="100"
                                           name="auto_submit_high_severity_threshold"
                                           value="{{ old('auto_submit_high_severity_threshold', $exam->auto_submit_high_severity_threshold ?? 2) }}"
                                           class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
                                           style="border-radius: 2px;">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5" style="font-size: 10px; letter-spacing: 0.1em;">Medium Severity Threshold</label>
                                    <input type="number" min="0" max="100"
                                           name="auto_submit_medium_severity_threshold"
                                           value="{{ old('auto_submit_medium_severity_threshold', $exam->auto_submit_medium_severity_threshold ?? 5) }}"
                                           class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
                                           style="border-radius: 2px;">
                                </div>
                            </div>

                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Auto-submit triggers when both thresholds are met: high severity count >= high threshold and medium severity count >= medium threshold.
                            </p>

                            <div class="border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 p-3 space-y-2"
                                 style="border-radius: 2px;">
                                <p class="text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.08em;">
                                    Violation Types
                                </p>
                                <p class="text-xs text-slate-600 dark:text-slate-400">
                                    <span class="font-semibold text-red-700 dark:text-red-400">High:</span>
                                    exam exit, multiple faces, face mismatch.
                                </p>
                                <p class="text-xs text-slate-600 dark:text-slate-400">
                                    <span class="font-semibold text-amber-700 dark:text-amber-400">Medium:</span>
                                    tab switch, window blur, fullscreen exit, no face detected.
                                </p>
                                <p class="text-xs text-slate-600 dark:text-slate-400">
                                    <span class="font-semibold text-blue-700 dark:text-blue-400">Low:</span>
                                    copy attempt, paste attempt, keyboard shortcut, right click/context menu.
                                </p>
                            </div>

                            <button type="submit"
                                    class="w-full py-2 text-sm font-medium text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                                    style="border-radius: 2px;">
                                Save Proctoring Settings
                            </button>
                        </form>
                    </div>
                </div>

                {{-- ── ACTIVE VIOLATIONS (per-exam toggles) ── --}}
                @if($exam->proctoring_enabled)
                <div class="bg-white dark:bg-slate-900 overflow-hidden"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                        <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase tracking-wider" style="letter-spacing: 0.08em;">Violation Settings</h2>
                    </div>
                    <div class="p-5">
                        @php
                            $resolvedViolations = $exam->resolvedViolationSettings();
                            $violationMeta = [
                                'tab_switch'        => ['label' => 'Tab Switch',        'severity' => 'medium'],
                                'window_blur'       => ['label' => 'Window Blur',        'severity' => 'medium'],
                                'copy_attempt'      => ['label' => 'Copy Attempt',       'severity' => 'low'],
                                'paste_attempt'     => ['label' => 'Paste Attempt',      'severity' => 'low'],
                                'right_click'       => ['label' => 'Right Click',        'severity' => 'low'],
                                'keyboard_shortcut' => ['label' => 'Keyboard Shortcut',  'severity' => 'low'],
                                'fullscreen_exit'   => ['label' => 'Fullscreen Exit',    'severity' => 'medium'],
                                'exam_exit'         => ['label' => 'Exam Exit',          'severity' => 'high'],
                                'multiple_faces'    => ['label' => 'Multiple Faces',     'severity' => 'high'],
                                'no_face'           => ['label' => 'No Face Detected',   'severity' => 'medium'],
                                'face_mismatch'     => ['label' => 'Face Mismatch',      'severity' => 'high'],
                            ];
                            $severityColors = [
                                'high'   => 'text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-950/30 border-red-200 dark:border-red-800',
                                'medium' => 'text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/30 border-amber-200 dark:border-amber-800',
                                'low'    => 'text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/30 border-blue-200 dark:border-blue-800',
                            ];
                        @endphp
                        <form action="{{ route('examination-hub.exams.violation-settings', $exam) }}" method="POST" class="space-y-1">
                            @csrf
                            @foreach($violationMeta as $key => $meta)
                                <label class="flex items-center justify-between py-2.5 border-b border-slate-50 dark:border-slate-800 last:border-0 cursor-pointer group">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center text-xs font-semibold px-2 py-0.5 border {{ $severityColors[$meta['severity']] }}"
                                              style="border-radius: 2px;">{{ ucfirst($meta['severity']) }}</span>
                                        <span class="text-sm text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">
                                            {{ $meta['label'] }}
                                        </span>
                                    </div>
                                    <input type="hidden" name="violations[{{ $key }}]" value="0">
                                    <input type="checkbox"
                                           name="violations[{{ $key }}]"
                                           value="1"
                                           {{ ($resolvedViolations[$key] ?? true) ? 'checked' : '' }}
                                           class="w-4 h-4 rounded text-indigo-600 border-slate-300 dark:border-slate-600 focus:ring-indigo-500">
                                </label>
                            @endforeach
                            <div class="pt-3">
                                <button type="submit"
                                        class="w-full py-2 text-sm font-medium text-white transition-all"
                                        style="border-radius: 2px; background: linear-gradient(135deg, #4f46e5, #6366f1); box-shadow: 0 2px 8px rgba(99,102,241,0.25);">
                                    Save Violation Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif

                <div class="bg-white dark:bg-slate-900 overflow-hidden"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                        <div class="w-1 h-5" style="background: linear-gradient(180deg, #b45309, #d97706); border-radius: 1px;"></div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase tracking-wider" style="letter-spacing: 0.08em;">Results Availability</h2>
                    </div>
                    <div class="p-5 space-y-4">
                        <dl class="space-y-2.5">
                            <div class="flex items-center justify-between py-2 border-b border-slate-50 dark:border-slate-800">
                                <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.08em;">Visibility Mode</dt>
                                <dd class="text-sm font-semibold text-slate-800 dark:text-slate-200">
                                    @php
                                        $visibilityLabel = match($exam->result_visibility) {
                                            'immediate' => '⚡ Immediate',
                                            'after_due_date' => '📅 After End Date',
                                            'scheduled' => '🕐 Scheduled',
                                            'manual_release' => '🔒 Manual Release',
                                            default => 'Not Set'
                                        };
                                    @endphp
                                    {{ $visibilityLabel }}
                                </dd>
                            </div>
                            @if($exam->result_visibility === 'scheduled' && $exam->results_release_datetime)
                                <div class="flex items-center justify-between py-2 border-b border-slate-50 dark:border-slate-800">
                                    <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.08em;">Release Date</dt>
                                    <dd class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $exam->results_release_datetime->format('M d, Y \a\t h:i A') }}</dd>
                                </div>
                            @endif
                            <div class="flex items-center justify-between py-2">
                                <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.08em;">Currently Available</dt>
                                <dd>
                                    @if($exam->canShowResults())
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800"
                                              style="border-radius: 2px;">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Yes
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700"
                                              style="border-radius: 2px;">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            No
                                        </span>
                                    @endif
                                </dd>
                            </div>
                        </dl>

                        @if($exam->result_visibility === 'manual_release')
                            <form action="{{ route('examination-hub.exams.toggle-results', $exam) }}" method="POST">
                                @csrf
                                @if($exam->results_released)
                                    <button type="submit"
                                            class="w-full inline-flex items-center justify-center gap-2 py-2.5 text-sm font-semibold text-white transition-all"
                                            style="border-radius: 2px; background: linear-gradient(135deg, #dc2626, #ef4444); box-shadow: 0 2px 8px rgba(220,38,38,0.25);">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        Hide Results from Participants
                                    </button>
                                @else
                                    <button type="submit"
                                            class="w-full inline-flex items-center justify-center gap-2 py-2.5 text-sm font-semibold text-white transition-all"
                                            style="border-radius: 2px; background: linear-gradient(135deg, #059669, #10b981); box-shadow: 0 2px 8px rgba(5,150,105,0.25);">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                        Release Results to Participants
                                    </button>
                                @endif
                            </form>
                        @endif
                    </div>
                </div>

                {{-- ── ADD PARTICIPANT ── --}}
                <div class="bg-white dark:bg-slate-900 overflow-hidden"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                        <div class="w-1 h-5" style="background: linear-gradient(180deg, #059669, #34d399); border-radius: 1px;"></div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase tracking-wider" style="letter-spacing: 0.08em;">Add Participant</h2>
                    </div>
                    <div class="p-5">
                        <form action="{{ route('examination-hub.participants.configured.store', $exam) }}" method="POST" class="space-y-3">
                            @csrf
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1" style="font-size: 10px; letter-spacing: 0.1em;">First Name</label>
                                    <input name="first_name" placeholder="e.g. John" required
                                           class="w-full px-3 py-2.5 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
                                           style="border-radius: 2px;">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1" style="font-size: 10px; letter-spacing: 0.1em;">Last Name</label>
                                    <input name="last_name" placeholder="e.g. Mensah" required
                                           class="w-full px-3 py-2.5 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
                                           style="border-radius: 2px;">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1" style="font-size: 10px; letter-spacing: 0.1em;">Email Address</label>
                                <input name="email" type="email" placeholder="e.g. john@example.com" required
                                       class="w-full px-3 py-2.5 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
                                       style="border-radius: 2px;">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1" style="font-size: 10px; letter-spacing: 0.1em;">Unique Code <span class="normal-case text-slate-400">(optional)</span></label>
                                <input name="unique_code" placeholder="e.g. STU-2024-001"
                                       class="w-full px-3 py-2.5 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
                                       style="border-radius: 2px;">
                            </div>
                            <button type="submit"
                                    class="w-full py-2.5 text-sm font-semibold text-white transition-all"
                                    style="border-radius: 2px; background: linear-gradient(135deg, #0f172a, #1e293b); box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                                Add Participant
                            </button>
                        </form>
                    </div>
                </div>

                {{-- ── IMPORT CSV ── --}}
                <div class="bg-white dark:bg-slate-900 overflow-hidden"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                        <div class="w-1 h-5" style="background: linear-gradient(180deg, #0369a1, #38bdf8); border-radius: 1px;"></div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase tracking-wider" style="letter-spacing: 0.08em;">Import Participants</h2>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-2 px-3 py-2 mb-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700"
                             style="border-radius: 2px;">
                            <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-mono">CSV format: <span class="text-slate-700 dark:text-slate-300">(first_name, last_name) or name, email, unique_code</span></p>
                        </div>
                        <form action="{{ route('examination-hub.participants.configured.import', $exam) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1" style="font-size: 10px; letter-spacing: 0.1em;">Select CSV File</label>
                                <input type="file" name="participants_csv" accept=".csv" required
                                       class="w-full text-sm text-slate-600 dark:text-slate-400 file:mr-3 file:py-2 file:px-4 file:border-0 file:text-xs file:font-semibold file:text-white file:cursor-pointer border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 cursor-pointer"
                                       style="border-radius: 2px; --tw-file-border-radius: 2px;"
                                       onchange="this.parentElement.querySelector('style') || (()=>{ const s=document.createElement('style'); s.textContent='input[type=file]::file-selector-button{background:linear-gradient(135deg,#1e293b,#334155);border-radius:2px;}'; this.parentElement.appendChild(s); })()">
                            </div>
                            <button type="submit"
                                    class="w-full py-2.5 text-sm font-semibold text-white transition-all"
                                    style="border-radius: 2px; background: linear-gradient(135deg, #0369a1, #0284c7); box-shadow: 0 2px 8px rgba(3,105,161,0.25);">
                                Import from CSV
                            </button>
                        </form>
                    </div>
                </div>

            </div>{{-- /right column --}}
        </div>{{-- /grid --}}

        {{-- ── CONFIGURED PARTICIPANTS TABLE ── --}}
        @if($configuredParticipants->isNotEmpty())
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">

                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-1 h-5" style="background: linear-gradient(180deg, #b45309, #fbbf24); border-radius: 1px;"></div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase tracking-wider" style="letter-spacing: 0.08em;">Configured Participants</h2>
                    </div>
                    <span class="text-xs px-2 py-1 font-semibold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800"
                          style="border-radius: 2px;">{{ $configuredCount }} total</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Unique Code</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Status</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                            @foreach($configuredParticipants as $participant)
                                <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="px-6 py-3.5 inline-flex font-medium text-slate-800 dark:text-slate-200">
                                        <span>
                                               <x-avatar text-size="text-xs" class="w-7 h-7 mr-3" :name="$participant->name" :email="$participant->email" />
                                        </span>
                                            <span class="my-auto">{{ $participant->name }}</span>
                                    </td>
                                    <td class="px-6 py-3.5 text-slate-600 dark:text-slate-400">{{ $participant->email }}</td>
                                    <td class="px-6 py-3.5">
                                        @if($participant->unique_code)
                                            <span class="font-mono text-xs px-2 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300"
                                                  style="border-radius: 2px;">{{ $participant->unique_code }}</span>
                                        @else
                                            <span class="text-slate-400 dark:text-slate-600">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3.5">
                                        @if($participant->is_active)
                                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800"
                                                  style="border-radius: 2px;">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700"
                                                  style="border-radius: 2px;">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <div class="flex items-center justify-end gap-2">
                                            {{-- Toggle active/inactive --}}
                                            <form method="POST" action="{{ route('examination-hub.participants.configured.toggle', [$exam, $participant]) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium border transition-colors {{ $participant->is_active ? 'text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800 hover:bg-amber-50 dark:hover:bg-amber-950/30' : 'text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/30' }}"
                                                        style="border-radius: 2px;"
                                                        title="{{ $participant->is_active ? 'Deactivate' : 'Activate' }}">
                                                    @if($participant->is_active)
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                        Deactivate
                                                    @else
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        Activate
                                                    @endif
                                                </button>
                                            </form>
                                            
                                            {{-- Edit --}}
                                            <a href="{{ route('examination-hub.participants.configured.edit-form', [$exam, $participant]) }}"
                                               class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800 hover:bg-amber-50 dark:hover:bg-amber-950/30 transition-colors"
                                               style="border-radius: 2px;"
                                               title="Edit participant">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                Edit
                                            </a>

                                            {{-- Delete --}}
                                            <form method="POST" action="{{ route('examination-hub.participants.configured.destroy', [$exam, $participant]) }}"
                                                  onsubmit="return confirm('Remove {{ addslashes($participant->name) }} from this exam?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors"
                                                        style="border-radius: 2px;"
                                                        title="Remove participant">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    Remove
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>{{-- /container --}}
</x-layouts.app>
