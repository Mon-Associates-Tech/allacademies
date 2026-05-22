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
                        <a href="{{ route('examination-hub.proctoring.index', $exam) }}"
                           class="hover:text-slate-300 transition-colors">Proctoring</a>
                        <x-heroicon-o-chevron-right class="w-3 h-3" />
                        <span class="text-slate-400">{{ $submission->participant_name ?? 'Submission #'.$submission->id }}</span>
                    </div>
                    <h1 class="text-2xl font-bold text-white leading-snug"
                        style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                        Proctoring Log
                    </h1>
                    <p class="text-slate-400 mt-2 text-sm">
                        {{ $submission->participant_name ?? 'Unknown' }}
                        @if($submission->participant_email)
                            &middot; {{ $submission->participant_email }}
                        @endif
                    </p>
                </div>
                <a href="{{ route('examination-hub.proctoring.index', $exam) }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all self-start sm:self-auto"
                   style="border-radius: 2px; background: linear-gradient(135deg, #334155, #475569); box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
                    <x-heroicon-o-arrow-left class="w-4 h-4" />
                    Back to Dashboard
                </a>
            </div>
        </div>

        {{-- ── FLAGGED BANNER ── --}}
        @if($summary['flagged'])
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(220,38,38,0.2); box-shadow: 0 1px 6px rgba(220,38,38,0.08);">
                <div class="px-5 py-4 flex items-center gap-3">
                    <div class="w-8 h-8 flex-shrink-0 flex items-center justify-center"
                         style="border-radius: 2px; background: linear-gradient(135deg, #b91c1c, #ef4444);">
                        <x-heroicon-o-flag class="w-4 h-4 text-white" />
                    </div>
                    <p class="text-sm text-red-700 dark:text-red-300">
                        This submission has been <strong>automatically flagged</strong> — violation thresholds were exceeded.
                    </p>
                </div>
            </div>
        @endif

        {{-- ── STAT CARDS ── --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach([
                ['label' => 'Total Events', 'value' => $summary['total'],  'grad' => 'linear-gradient(135deg,#334155,#475569)',  'icon' => 'heroicon-o-clipboard-document-list'],
                ['label' => 'High',         'value' => $summary['high'],   'grad' => 'linear-gradient(135deg,#b91c1c,#ef4444)',  'icon' => 'heroicon-o-exclamation-triangle'],
                ['label' => 'Medium',       'value' => $summary['medium'], 'grad' => 'linear-gradient(135deg,#b45309,#fbbf24)',  'icon' => 'heroicon-o-exclamation-circle'],
                ['label' => 'Low',          'value' => $summary['low'],    'grad' => 'linear-gradient(135deg,#1d4ed8,#60a5fa)',  'icon' => 'heroicon-o-information-circle'],
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

        {{-- ── EVENT BREAKDOWN ── --}}
        @if(!empty($summary['by_type']))
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                        style="letter-spacing: 0.1em;">Event Breakdown</h2>
                </div>
                <div class="p-5 flex flex-wrap gap-2">
                    @foreach($summary['by_type'] as $type => $info)
                        @php
                            $tagStyle = match($info['severity']) {
                                'high'   => 'background:linear-gradient(135deg,#fef2f2,#fee2e2);color:#b91c1c;border:1px solid rgba(185,28,28,0.15)',
                                'medium' => 'background:linear-gradient(135deg,#fffbeb,#fef3c7);color:#b45309;border:1px solid rgba(180,83,9,0.15)',
                                default  => 'background:linear-gradient(135deg,#eff6ff,#dbeafe);color:#1d4ed8;border:1px solid rgba(29,78,216,0.15)',
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5"
                              style="border-radius: 2px; {{ $tagStyle }}">
                            {{ str_replace('_', ' ', ucfirst($type)) }}
                            <span class="opacity-70">×{{ $info['count'] }}</span>
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ── FULL EVENT LOG ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                    style="letter-spacing: 0.1em;">Full Event Log</h2>
                @if($logs->isNotEmpty())
                    <span class="text-xs text-slate-400 font-normal normal-case">{{ $logs->count() }} events</span>
                @endif
            </div>

            @if($logs->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-12 h-12 mx-auto flex items-center justify-center mb-4"
                         style="border-radius: 2px; background: linear-gradient(135deg, #64748b, #94a3b8);">
                        <x-heroicon-o-clipboard-document-list class="w-6 h-6 text-white" />
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">No events logged for this submission.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Time</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Event</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Severity</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                        @foreach($logs as $log)
                            @php
                                $rowAccent = match($log->severity) {
                                    'high'   => 'hover:bg-red-50/40 dark:hover:bg-red-900/10',
                                    'medium' => 'hover:bg-amber-50/40 dark:hover:bg-amber-900/10',
                                    default  => 'hover:bg-blue-50/40 dark:hover:bg-slate-800/40',
                                };
                                $severityStyle = match($log->severity) {
                                    'high'   => 'background:linear-gradient(135deg,#fef2f2,#fee2e2);color:#b91c1c;border:1px solid rgba(185,28,28,0.15)',
                                    'medium' => 'background:linear-gradient(135deg,#fffbeb,#fef3c7);color:#b45309;border:1px solid rgba(180,83,9,0.15)',
                                    default  => 'background:linear-gradient(135deg,#eff6ff,#dbeafe);color:#1d4ed8;border:1px solid rgba(29,78,216,0.15)',
                                };
                            @endphp
                            <tr class="{{ $rowAccent }} transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-slate-700 dark:text-slate-300 font-medium">
                                        {{ $log->occurred_at->format('H:i:s') }}
                                    </p>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                                        {{ $log->occurred_at->format('d M Y') }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-800 dark:text-slate-200">
                                    {{ str_replace('_', ' ', ucfirst($log->event_type)) }}
                                </td>
                                <td class="px-6 py-4">
                                        <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1"
                                              style="border-radius: 2px; {{ $severityStyle }}">
                                            {{ ucfirst($log->severity) }}
                                        </span>
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
