<x-layouts.app>
    <x-examination-hub.navigation active="manage" />

    {{-- ═══════════════════════════════════════════════════════════
         PAGE SHELL
    ═══════════════════════════════════════════════════════════ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6"
         style="font-family: 'system-ui', -apple-system, sans-serif;">

        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden"
             style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #065f46, #059669, #10b981);"></div>
            <div class="px-7 py-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-white leading-snug" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                        Submissions
                    </h1>
                    <p class="text-slate-400 mt-1 text-sm">{{ $exam->title }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('examination-hub.exams.show', $exam) }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold transition-all border border-slate-200 dark:border-slate-600 bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-700 dark:to-slate-800 text-slate-700 dark:text-slate-200"
                       style="border-radius: 2px;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Exam
                    </a>
                    <a href="{{ route('examination-hub.submissions.export', $exam) }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all"
                       style="border-radius: 2px; background: linear-gradient(135deg, #065f46, #059669); box-shadow: 0 2px 10px rgba(5,150,105,0.3);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export CSV
                    </a>
                </div>
            </div>
        </div>



        {{-- ── METRICS STRIP ── --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            {{-- Total Submissions --}}
            <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                     style="border-radius: 2px; background: linear-gradient(135deg, #1d4ed8, #3b82f6);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">Total Submissions</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white mt-0.5" style="letter-spacing: -0.04em;">{{ $summary['total'] }}</p>
                </div>
            </div>

            {{-- Average Score --}}
            <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                     style="border-radius: 2px; background: linear-gradient(135deg, #065f46, #059669);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">Average Score</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white mt-0.5" style="letter-spacing: -0.04em;">{{ number_format($summary['avg_score'], 1) }}<span class="text-lg font-medium text-slate-500">%</span></p>
                </div>
            </div>

            {{-- Highest Score --}}
            <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                     style="border-radius: 2px; background: linear-gradient(135deg, #059669, #10b981);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">Highest Score</p>
                    <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 mt-0.5" style="letter-spacing: -0.04em;">{{ number_format($summary['max_score'], 1) }}<span class="text-lg font-medium text-slate-500">%</span></p>
                </div>
            </div>

            {{-- Lowest Score --}}
            <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                     style="border-radius: 2px; background: linear-gradient(135deg, #dc2626, #ef4444);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">Lowest Score</p>
                    <p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-0.5" style="letter-spacing: -0.04em;">{{ number_format($summary['min_score'], 1) }}<span class="text-lg font-medium text-slate-500">%</span></p>
                </div>
            </div>
        </div>

        {{-- ── BULK BONUS ── --}}
        <div class="no-print" x-data="{ open: false }">
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <button @click="open = !open"
                        class="w-full px-5 py-4 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors text-left">
                    <div class="flex items-center gap-2">
                        <div class="w-1 h-5" style="background: linear-gradient(180deg, #b45309, #d97706); border-radius: 1px;"></div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Apply Bonus to All Participants</h2>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-cloak class="px-5 pb-5">
                    @if(session('success'))
                        <div class="mb-3 px-4 py-2.5 text-sm text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800" style="border-radius: 2px;">
                            {{ session('success') }}
                        </div>
                    @endif
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">Bonus is added to each participant's percentage. Final score is capped at 100%.</p>
                    <form method="POST" action="{{ route('examination-hub.submissions.bonus-all', $exam) }}"
                          onsubmit="return confirm('Apply bonus to ALL submitted participants? This cannot be undone automatically.')">
                        @csrf
                        <div class="flex items-end gap-3 flex-wrap">
                            <div>
                                <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Bonus Points (max 100)</label>
                                <input type="number" name="bonus_points" step="0.5" min="0" max="100" required
                                       class="w-28 px-2.5 py-1.5 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500"
                                       style="border-radius: 2px;">
                            </div>
                            <div class="flex-1 min-w-48">
                                <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Reason (optional)</label>
                                <input type="text" name="bonus_reason" placeholder="e.g. Technical issue during exam"
                                       class="w-full px-2.5 py-1.5 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500"
                                       style="border-radius: 2px;">
                            </div>
                            <button type="submit"
                                    class="px-4 py-1.5 text-sm font-semibold text-white"
                                    style="border-radius: 2px; background: linear-gradient(135deg, #b45309, #d97706);">
                                Apply to All
                            </button>
                        </div>
                    </form>
                    <form method="POST" action="{{ route('examination-hub.submissions.bonus-all.remove', $exam) }}" class="mt-3"
                          onsubmit="return confirm('Remove bonus from ALL submissions for this exam?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 underline">Remove all bonuses</button>
                    </form>
                </div>
            </div>
        </div>

                {{-- ── TOOLBAR: FILTERS & SORT ── --}}
        <div class="bg-white dark:bg-slate-900 p-4 flex flex-col lg:flex-row lg:items-center justify-between gap-4"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            
            <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 shrink-0">
                <span class="font-semibold text-slate-900 dark:text-white">{{ $summary['total'] }}</span> Submissions found
            </div>

            <form method="GET" action="{{ route('examination-hub.submissions.index', $exam) }}" class="flex flex-wrap items-center gap-2 flex-1 justify-end">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search participant..." 
                           class="w-56 pl-9 pr-4 py-2 text-sm border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" style="border-radius: 2px;">
                </div>

                <select name="status" onchange="this.form.submit()" class="px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" style="border-radius: 2px;">
                    <option value="">All Statuses</option>
                    <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="in_progress" {{ ($filters['status'] ?? '') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="pending_review" {{ ($filters['status'] ?? '') === 'pending_review' ? 'selected' : '' }}>Pending Review</option>
                </select>

                <select name="sort" onchange="this.form.submit()" class="px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" style="border-radius: 2px;">
                    <option value="submitted_at_desc" {{ ($filters['sort'] ?? 'submitted_at_desc') === 'submitted_at_desc' ? 'selected' : '' }}>Newest First</option>
                    <option value="submitted_at_asc" {{ ($filters['sort'] ?? '') === 'submitted_at_asc' ? 'selected' : '' }}>Oldest First</option>
                    <option value="percentage_desc" {{ ($filters['sort'] ?? '') === 'percentage_desc' ? 'selected' : '' }}>Highest Score</option>
                    <option value="percentage_asc" {{ ($filters['sort'] ?? '') === 'percentage_asc' ? 'selected' : '' }}>Lowest Score</option>
                    <option value="time_taken_minutes_desc" {{ ($filters['sort'] ?? '') === 'time_taken_minutes_desc' ? 'selected' : '' }}>Longest Time</option>
                </select>

                @if(!empty($filters['search']) || !empty($filters['status']) || ($filters['sort'] ?? 'submitted_at_desc') !== 'submitted_at_desc')
                    <a href="{{ route('examination-hub.submissions.index', $exam) }}" class="inline-flex items-center gap-1 px-3 py-2 text-xs font-medium text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors" style="border-radius: 2px;">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- ── SUBMISSIONS TABLE ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #0369a1, #38bdf8); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Submission Records</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Participant</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Score</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Grade</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Time Taken</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Submitted</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                        @forelse($submissions as $submission)
                            <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-semibold text-slate-900 dark:text-white">{{ $submission->participant_name ?? $submission->getParticipantName() }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $submission->participant_email ?? $submission->getParticipantEmail() }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-semibold text-slate-900 dark:text-white">{{ number_format($submission->percentage ?? 0, 1) }}%</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $submission->score ?? 0 }}/{{ $submission->total_marks ?? 0 }}</p>
                                        @if(($submission->bonus_points ?? 0) > 0)
                                            <p class="text-xs text-amber-600 dark:text-amber-400">+{{ number_format($submission->bonus_points, 1) }} bonus</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $grade = $submission->grade ?? 'N/A';
                                        $gradeStyle = in_array($grade, ['A+', 'A'])
                                            ? 'color:#065f46;background:#ecfdf5;border-color:#a7f3d0;'
                                            : (in_array($grade, ['B', 'C'])
                                                ? 'color:#1d4ed8;background:#eff6ff;border-color:#bfdbfe;'
                                                : (in_array($grade, ['D', 'F'])
                                                    ? 'color:#991b1b;background:#fef2f2;border-color:#fecaca;'
                                                    : 'color:#475569;background:#f1f5f9;border-color:#e2e8f0;'));
                                    @endphp
                                    <span class="inline-flex items-center justify-center text-xs font-semibold px-2.5 py-1 border"
                                          style="border-radius: 2px; {{ $gradeStyle }}">
                                        {{ $grade }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-300">
                                    {{ $submission->time_taken_minutes ?? 0 }} min
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $status = $submission->status ?? 'unknown';
                                        $statusStyle = $status === 'completed'
                                            ? 'color:#065f46;background:#ecfdf5;border-color:#a7f3d0;'
                                            : ($status === 'in_progress'
                                                ? 'color:#92400e;background:#fffbeb;border-color:#fde68a;'
                                                : 'color:#475569;background:#f1f5f9;border-color:#e2e8f0;');
                                    @endphp
                                    <span class="inline-flex items-center justify-center text-xs font-semibold px-2.5 py-1 border"
                                          style="border-radius: 2px; {{ $statusStyle }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-300">
                                    {{ optional($submission->submitted_at)?->format('M d, Y H:i') ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($submission->requires_manual_review)
                                            <a href="{{ route('examination-hub.submissions.grade', [$exam, $submission]) }}"
                                               class="inline-flex items-center gap-1 text-xs font-semibold text-blue-700 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Grade
                                            </a>
                                        @endif
                                        <a href="{{ route('examination-hub.submissions.show', [$exam, $submission]) }}"
                                           class="inline-flex items-center gap-1 text-xs font-semibold text-amber-700 dark:text-amber-400 hover:text-amber-900 dark:hover:text-amber-300 transition-colors">
                                            View Details
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-400 dark:text-slate-500">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 flex items-center justify-center mb-3"
                                             style="border-radius: 2px; background: linear-gradient(135deg, #64748b, #94a3b8);">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <p>No submissions found matching your filters.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($submissions->hasPages())
                <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $submissions->withQueryString()->links() }}
                </div>
            @endif
        </div>

    </div>{{-- /container --}}
</x-layouts.app>