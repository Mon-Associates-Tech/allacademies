<x-layouts.app>
    <x-examination-hub.navigation active="performance" />

    {{-- ═══════════════════════════════════════════════════════════
         PAGE SHELL
    ═══════════════════════════════════════════════════════════ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
         style="font-family: 'system-ui', -apple-system, sans-serif;">

        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden"
             style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #b45309, #d97706, #fbbf24);"></div>
            <div class="px-7 py-6 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
                <div class="min-w-0">
                    <a href="{{ route('examination-hub.performance.index') }}"
                       class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-400 hover:text-amber-400 transition-colors mb-3">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Back to Participants
                    </a>
                    <h1 class="text-2xl font-bold text-white leading-snug" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                        {{ $participant->participant_name }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-2">
                        <span class="text-xs text-slate-400">
                            {{ $participant->participant_email ?? 'No email on record' }}
                        </span>
                        <span class="text-slate-700">·</span>
                        <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-400 px-2 py-0.5 bg-amber-400/10 border border-amber-400/20"
                              style="border-radius: 2px;">
                            {{ ucfirst($participant->participant_type) }} Participant
                        </span>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('examination-hub.performance.export', [$participantType, $participantId]) }}{{ !empty($selectedSubjects) ? '?' . http_build_query(['subjects' => $selectedSubjects]) : '' }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all"
                       style="border-radius: 2px; background: linear-gradient(135deg, #065f46, #059669); box-shadow: 0 2px 10px rgba(5,150,105,0.3);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export Report
                    </a>
                </div>
            </div>
        </div>

        {{-- ── SUBJECT FILTER ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-3.5 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #2563eb, #60a5fa); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Filter by Subject</h2>
            </div>
            <div class="p-5">
                <form method="GET" class="space-y-4">
                    @if($availableSubjects->isNotEmpty())
                        <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5">
                            @foreach($availableSubjects as $subject)
                                <label class="flex items-center gap-2.5 px-3 py-2.5 cursor-pointer transition-all border
                                    {{ in_array($subject->id, $selectedSubjects) 
                                        ? 'border-amber-300 dark:border-amber-700 bg-amber-50 dark:bg-amber-950/30' 
                                        : 'border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40 hover:border-slate-200 dark:hover:border-slate-700' }}"
                                     style="border-radius: 2px;">
                                    <input
                                        type="checkbox"
                                        name="subjects[]"
                                        value="{{ $subject->id }}"
                                        {{ in_array($subject->id, $selectedSubjects) ? 'checked' : '' }}
                                        class="w-4 h-4 rounded text-amber-600 border-slate-300 dark:border-slate-600 focus:ring-amber-500"
                                    >
                                    <span class="text-sm text-slate-700 dark:text-slate-300 truncate">{{ $subject->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-500 dark:text-slate-400">No subjects available for filtering.</p>
                    @endif
                    <div class="flex items-center gap-3 pt-1">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white transition-all"
                                style="border-radius: 2px; background: linear-gradient(135deg, #1e293b, #334155); box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                            Apply Filter
                        </button>
                        @if(!empty($selectedSubjects))
                            <a href="{{ request()->url() }}" class="text-sm text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition-colors">Clear filters</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- ── METRICS STRIP ── --}}
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Total Submissions --}}
            <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                     style="background: linear-gradient(135deg, #1d4ed8, #3b82f6); border-radius: 2px;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">Total Submissions</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white mt-0.5" style="letter-spacing: -0.04em;">{{ $metrics['total_submissions'] }}</p>
                </div>
            </div>

            {{-- Average Score --}}
            <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                     style="background: linear-gradient(135deg, #065f46, #059669); border-radius: 2px;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">Average Score</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white mt-0.5" style="letter-spacing: -0.04em;">{{ $metrics['average_percentage'] }}<span class="text-lg font-medium text-slate-500">%</span></p>
                </div>
            </div>

            {{-- Overall Grade --}}
            <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                     style="background: linear-gradient(135deg, #b45309, #d97706); border-radius: 2px;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">Overall Grade</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white mt-0.5" style="letter-spacing: -0.04em;">{{ $metrics['overall_grade'] }}</p>
                </div>
            </div>

            {{-- Pending Review --}}
            <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                     style="background: linear-gradient(135deg, #92400e, #b45309); border-radius: 2px;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">Pending Review</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white mt-0.5" style="letter-spacing: -0.04em;">{{ $metrics['pending_submissions'] }}</p>
                </div>
            </div>
        </div>

        {{-- ── CHARTS ROW ── --}}
        <div class="grid lg:grid-cols-2 gap-6">
            {{-- Performance Trend --}}
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5" style="background: linear-gradient(180deg, #2563eb, #60a5fa); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Performance Trend</h2>
                </div>
                <div class="p-5">
                    <div class="h-64">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Grade Distribution --}}
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5" style="background: linear-gradient(180deg, #b45309, #fbbf24); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Grade Distribution</h2>
                </div>
                <div class="p-5">
                    <div class="h-64">
                        <canvas id="gradeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── SUBJECT PERFORMANCE ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Performance by Subject</h2>
            </div>

            <div class="p-5 border-b border-slate-50 dark:border-slate-800">
                <div class="h-72">
                    <canvas id="subjectChart"></canvas>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Subject</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Submissions</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Score</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Percentage</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Grade</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                        @forelse($metrics['subject_performance'] as $performance)
                            <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-6 py-3.5 font-semibold text-slate-800 dark:text-slate-200">
                                    {{ $performance['subject']->name ?? 'Unknown' }}
                                </td>
                                <td class="px-6 py-3.5 text-center text-slate-600 dark:text-slate-400">
                                    {{ $performance['submissions_count'] }}
                                </td>
                                <td class="px-6 py-3.5 text-center">
                                    <span class="font-mono text-xs font-medium text-slate-700 dark:text-slate-300 px-2 py-1 bg-slate-100 dark:bg-slate-800"
                                          style="border-radius: 2px;">
                                        {{ $performance['total_score'] }} / {{ $performance['total_marks'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-center">
                                    @php
                                        $pct = $performance['percentage'];
                                        $pctStyle = $pct >= 80
                                            ? 'color:#065f46;background:#ecfdf5;border-color:#a7f3d0;'
                                            : ($pct >= 60
                                                ? 'color:#1d4ed8;background:#eff6ff;border-color:#bfdbfe;'
                                                : ($pct >= 50
                                                    ? 'color:#92400e;background:#fffbeb;border-color:#fde68a;'
                                                    : 'color:#991b1b;background:#fef2f2;border-color:#fecaca;'));
                                    @endphp
                                    <span class="inline-block text-xs font-bold px-2.5 py-1 border"
                                          style="border-radius: 2px; {{ $pctStyle }}">
                                        {{ $pct }}%
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-center font-bold text-slate-800 dark:text-slate-200">
                                    {{ $performance['average_grade'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-400 dark:text-slate-500">
                                    No subject performance data available
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── GRADE SUMMARY ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #b45309, #fbbf24); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Grade Summary</h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
                    @php
                        $gradeConfig = [
                            'A+' => ['bg' => 'linear-gradient(135deg,#065f46,#059669)', 'light' => '#ecfdf5', 'border' => '#a7f3d0', 'text' => '#065f46'],
                            'A'  => ['bg' => 'linear-gradient(135deg,#1d4ed8,#3b82f6)', 'light' => '#eff6ff', 'border' => '#bfdbfe', 'text' => '#1d4ed8'],
                            'B'  => ['bg' => 'linear-gradient(135deg,#5b21b6,#7c3aed)', 'light' => '#f5f3ff', 'border' => '#ddd6fe', 'text' => '#5b21b6'],
                            'C'  => ['bg' => 'linear-gradient(135deg,#92400e,#d97706)', 'light' => '#fffbeb', 'border' => '#fde68a', 'text' => '#92400e'],
                            'D'  => ['bg' => 'linear-gradient(135deg,#c2410c,#f97316)', 'light' => '#fff7ed', 'border' => '#fed7aa', 'text' => '#c2410c'],
                            'F'  => ['bg' => 'linear-gradient(135deg,#991b1b,#ef4444)', 'light' => '#fef2f2', 'border' => '#fecaca', 'text' => '#991b1b'],
                        ];
                    @endphp
                    @foreach($gradeConfig as $grade => $cfg)
                        @php $count = $metrics['grade_distribution'][$grade] ?? 0; @endphp
                        <div class="flex flex-col items-center justify-center py-5 border"
                             style="border-radius: 2px; background: {{ $count > 0 ? $cfg['light'] : '#f8fafc' }}; border-color: {{ $count > 0 ? $cfg['border'] : 'rgba(0,0,0,0.06)' }};">
                            <span class="text-3xl font-bold mb-1"
                                  style="letter-spacing: -0.04em; color: {{ $count > 0 ? $cfg['text'] : '#94a3b8' }};">
                                {{ $count }}
                            </span>
                            <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-white mb-1"
                                  style="border-radius: 2px; background: {{ $count > 0 ? $cfg['bg'] : 'linear-gradient(135deg,#94a3b8,#cbd5e1)' }};">
                                {{ $grade }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── RECENT SUBMISSIONS TABLE ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #0369a1, #38bdf8); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Recent Submissions</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Exam</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Subject</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Score</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Grade</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Date</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                        @forelse($submissions as $submission)
                            <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-6 py-3.5 font-medium text-slate-800 dark:text-slate-200">
                                    {{ $submission->assignment->title }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-600 dark:text-slate-400">
                                    {{ $submission->assignment->sections->first()?->academicSubject->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-3.5 text-center">
                                    <span class="font-mono text-xs font-medium px-2 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300"
                                          style="border-radius: 2px;">
                                        {{ $submission->score ?? '—' }} / {{ $submission->total_marks ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-center font-bold text-slate-800 dark:text-slate-200">
                                    {{ $submission->grade ?? '—' }}
                                </td>
                                <td class="px-6 py-3.5 text-center">
                                    @php
                                        $status = $submission->status;
                                        if (in_array($status, ['auto_graded', 'final'])) {
                                            $sStyle = 'color:#065f46;background:#ecfdf5;border-color:#a7f3d0;';
                                            $dot = '#059669';
                                        } elseif ($status === 'submitted') {
                                            $sStyle = 'color:#92400e;background:#fffbeb;border-color:#fde68a;';
                                            $dot = '#d97706';
                                        } else {
                                            $sStyle = 'color:#475569;background:#f1f5f9;border-color:#e2e8f0;';
                                            $dot = '#94a3b8';
                                        }
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 border"
                                          style="border-radius: 2px; {{ $sStyle }}">
                                        <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $dot }};"></span>
                                        {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-center text-slate-500 dark:text-slate-400 text-xs">
                                    {{ $submission->submitted_at?->format('M d, Y') ?? '—' }}
                                </td>
                                <td class="px-6 py-3.5 text-right">
                                    <a href="{{ route('examination-hub.submissions.show', [$submission->assignment, $submission]) }}"
                                       class="inline-flex items-center gap-1 text-xs font-semibold text-amber-700 dark:text-amber-400 hover:text-amber-900 dark:hover:text-amber-300 transition-colors">
                                        View Details
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-400 dark:text-slate-500">
                                    No submissions found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>{{-- /container --}}

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const isDark = document.documentElement.classList.contains('dark');
            const gridColor   = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
            const tickColor   = isDark ? '#94a3b8' : '#64748b';
            const tooltipBg   = 'rgba(15,23,42,0.92)';

            // ── Performance Trend ──────────────────────────────
            const trendData = @json($metrics['trend_data']);
            new Chart(document.getElementById('trendChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: Object.keys(trendData),
                    datasets: [{
                        label: 'Performance %',
                        data: Object.values(trendData),
                        borderColor: '#d97706',
                        backgroundColor: 'rgba(217,119,6,0.08)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#d97706',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: { color: gridColor },
                            ticks: { color: tickColor, callback: v => v + '%', font: { size: 11 } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: tickColor, font: { size: 11 } }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: tooltipBg,
                            padding: 12,
                            cornerRadius: 2,
                            callbacks: { label: ctx => 'Score: ' + ctx.parsed.y + '%' }
                        }
                    }
                }
            });

            // ── Grade Distribution ─────────────────────────────
            const gradeData = @json($metrics['grade_distribution']);
            new Chart(document.getElementById('gradeChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['A+', 'A', 'B', 'C', 'D', 'F'],
                    datasets: [{
                        label: 'Count',
                        data: ['A+', 'A', 'B', 'C', 'D', 'F'].map(g => gradeData[g] || 0),
                        backgroundColor: [
                            'rgba(5,150,105,0.85)',
                            'rgba(37,99,235,0.85)',
                            'rgba(124,58,237,0.85)',
                            'rgba(217,119,6,0.85)',
                            'rgba(234,88,12,0.85)',
                            'rgba(220,38,38,0.85)'
                        ],
                        borderColor: [
                            'rgb(5,150,105)',
                            'rgb(37,99,235)',
                            'rgb(124,58,237)',
                            'rgb(217,119,6)',
                            'rgb(234,88,12)',
                            'rgb(220,38,38)'
                        ],
                        borderWidth: 1.5,
                        borderRadius: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: gridColor },
                            ticks: { color: tickColor, stepSize: 1, font: { size: 11 } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: tickColor, font: { size: 11 } }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: tooltipBg,
                            padding: 12,
                            cornerRadius: 2
                        }
                    }
                }
            });

            // ── Subject Performance ────────────────────────────
            const subjectData = @json($metrics['subject_performance']);
            new Chart(document.getElementById('subjectChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: subjectData.map(s => s.subject?.name || 'Unknown'),
                    datasets: [{
                        label: 'Percentage',
                        data: subjectData.map(s => s.percentage),
                        backgroundColor: 'rgba(124,58,237,0.75)',
                        borderColor: 'rgb(124,58,237)',
                        borderWidth: 1.5,
                        borderRadius: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 100,
                            grid: { color: gridColor },
                            ticks: { color: tickColor, callback: v => v + '%', font: { size: 11 } }
                        },
                        y: {
                            grid: { display: false },
                            ticks: { color: tickColor, font: { size: 11 } }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: tooltipBg,
                            padding: 12,
                            cornerRadius: 2,
                            callbacks: {
                                label: function (ctx) {
                                    const s = subjectData[ctx.dataIndex];
                                    return [
                                        'Score: ' + s.total_score + ' / ' + s.total_marks,
                                        'Percentage: ' + s.percentage + '%',
                                        'Grade: ' + s.average_grade
                                    ];
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-layouts.app>