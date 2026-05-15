<x-layouts.app>
    <x-examination-hub.navigation active="manage" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
         style="font-family: 'system-ui', -apple-system, sans-serif;">

        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden"
             style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #7c3aed, #a78bfa, #c4b5fd);"></div>
            <div class="px-7 py-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    {{-- Breadcrumb --}}
                    <div class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                        <a href="{{ route('examination-hub.exams.show', $exam) }}"
                           class="hover:text-slate-300 transition-colors">{{ $exam->title }}</a>
                        <x-heroicon-o-chevron-right class="w-3 h-3" />
                        <span class="text-slate-400">Proctoring</span>
                    </div>
                    <h1 class="text-2xl font-bold text-white leading-snug"
                        style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                        Proctoring Dashboard
                    </h1>
                    <p class="text-slate-400 mt-2 text-sm">
                        {{ $summaries->count() }} {{ Str::plural('submission', $summaries->count()) }} with recorded violations
                    </p>
                </div>
                <a href="{{ route('examination-hub.submissions.index', $exam) }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all self-start sm:self-auto"
                   style="border-radius: 2px; background: linear-gradient(135deg, #334155, #475569); box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
                    <x-heroicon-o-arrow-left class="w-4 h-4" />
                    All Submissions
                </a>
            </div>
        </div>

        {{-- ── SUMMARY STATS ── --}}
        @if($summaries->isNotEmpty())
            @php
                $totalHigh   = $summaries->sum(fn($r) => $r['proctoring']['high']);
                $totalMedium = $summaries->sum(fn($r) => $r['proctoring']['medium']);
                $totalLow    = $summaries->sum(fn($r) => $r['proctoring']['low']);
                $flagged     = $summaries->where('proctoring.flagged', true)->count();
            @endphp
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach([
                    ['label' => 'Flagged',        'value' => $flagged,     'grad' => 'linear-gradient(135deg,#b91c1c,#ef4444)', 'icon' => 'heroicon-o-flag'],
                    ['label' => 'High Severity',   'value' => $totalHigh,   'grad' => 'linear-gradient(135deg,#b91c1c,#f97316)', 'icon' => 'heroicon-o-exclamation-triangle'],
                    ['label' => 'Med Severity',    'value' => $totalMedium, 'grad' => 'linear-gradient(135deg,#b45309,#fbbf24)', 'icon' => 'heroicon-o-exclamation-circle'],
                    ['label' => 'Low Severity',    'value' => $totalLow,    'grad' => 'linear-gradient(135deg,#1d4ed8,#60a5fa)', 'icon' => 'heroicon-o-information-circle'],
                ] as $stat)
                    <div class="bg-white dark:bg-slate-900 overflow-hidden"
                         style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                        <div class="h-0.5 w-full" style="background: {{ $stat['grad'] }};"></div>
                        <div class="px-5 py-4 flex items-center gap-3">
                            <div class="w-9 h-9 flex-shrink-0 flex items-center justify-center"
                                 style="border-radius: 2px; background: {{ $stat['grad'] }};">
                                <x-dynamic-component :component="$stat['icon']" class="w-4 h-4 text-white" />
                            </div>
                            <div>
                                <p class="text-xl font-bold text-slate-900 dark:text-white leading-none">
                                    {{ $stat['value'] }}
                                </p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $stat['label'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- ── VIOLATIONS TABLE ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                    style="letter-spacing: 0.1em;">Violation Reports</h2>
            </div>

            @if($summaries->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-12 h-12 mx-auto flex items-center justify-center mb-4"
                         style="border-radius: 2px; background: linear-gradient(135deg, #065f46, #059669);">
                        <x-heroicon-o-shield-check class="w-6 h-6 text-white" />
                    </div>
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">No violations recorded</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        All submissions for this exam are clean.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Participant</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">High</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Medium</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Low</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                        @foreach($summaries as $row)
                            @php
                                $submission = $row['submission'];
                                $proctoring = $row['proctoring'];
                            @endphp
                            <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="font-medium text-slate-900 dark:text-white">
                                        {{ $submission->participant_name ?? 'Unknown' }}
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        {{ $submission->participant_email ?? '—' }}
                                    </p>
                                </td>

                                <td class="px-6 py-4">
                                    @if($proctoring['high'] > 0)
                                        <span class="inline-flex items-center justify-center text-xs font-bold px-2.5 py-1"
                                              style="border-radius: 2px; background: linear-gradient(135deg,#fef2f2,#fee2e2); color: #b91c1c; border: 1px solid rgba(185,28,28,0.15);">
                                                {{ $proctoring['high'] }}
                                            </span>
                                    @else
                                        <span class="text-slate-300 dark:text-slate-600">—</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    @if($proctoring['medium'] > 0)
                                        <span class="inline-flex items-center justify-center text-xs font-bold px-2.5 py-1"
                                              style="border-radius: 2px; background: linear-gradient(135deg,#fffbeb,#fef3c7); color: #b45309; border: 1px solid rgba(180,83,9,0.15);">
                                                {{ $proctoring['medium'] }}
                                            </span>
                                    @else
                                        <span class="text-slate-300 dark:text-slate-600">—</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    @if($proctoring['low'] > 0)
                                        <span class="inline-flex items-center justify-center text-xs font-bold px-2.5 py-1"
                                              style="border-radius: 2px; background: linear-gradient(135deg,#eff6ff,#dbeafe); color: #1d4ed8; border: 1px solid rgba(29,78,216,0.15);">
                                                {{ $proctoring['low'] }}
                                            </span>
                                    @else
                                        <span class="text-slate-300 dark:text-slate-600">—</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    @if($proctoring['flagged'])
                                        <x-ui.badge variant="danger" size="sm">Flagged</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="warning" size="sm">Review</x-ui.badge>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('examination-hub.proctoring.show', [$exam, $submission]) }}"
                                       class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors">
                                        View Logs
                                        <x-heroicon-o-arrow-right class="w-3.5 h-3.5" />
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</x-layouts.app>
