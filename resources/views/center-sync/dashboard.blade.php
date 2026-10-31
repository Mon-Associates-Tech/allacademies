<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
         style="font-family: 'system-ui', -apple-system, sans-serif;">

        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden"
             style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #065f46, #059669, #10b981);"></div>
            <div class="px-7 py-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h1 class="text-2xl font-bold text-white leading-snug"
                        style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                        Multi-Center Dashboard
                    </h1>
                    @if($isAdmin && $allCenters->count() > 1)
                        <form method="GET" class="inline">
                            <select name="center_id" onchange="this.form.submit()"
                                class="px-3 py-2 text-sm border border-slate-600 rounded-sm bg-slate-800 text-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
                                <option value="">All Centers</option>
                                @foreach($allCenters as $center)
                                    <option value="{{ $center->id }}" {{ $selectedCenterId == $center->id ? 'selected' : '' }}>
                                        {{ $center->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── FLASH MESSAGES ── --}}
        @if(session('success'))
            <div class="bg-white dark:bg-slate-900 overflow-hidden rounded-sm border border-emerald-500/20 shadow-[0_1px_6px_rgba(5,150,105,0.08)]">
                <div class="px-5 py-4 flex items-center gap-2 bg-gradient-to-br from-emerald-50/[0.3] to-green-50/[0.1]">
                    <div class="w-1 h-5 bg-gradient-to-b from-emerald-600 to-emerald-400 rounded-sm"></div>
                    <p class="text-sm text-emerald-800 dark:text-emerald-200 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-white dark:bg-slate-900 overflow-hidden rounded-sm border border-red-500/20 shadow-[0_1px_6px_rgba(220,38,38,0.08)]">
                <div class="px-5 py-4 flex items-center gap-2 bg-gradient-to-br from-red-50/[0.3] to-orange-50/[0.1]">
                    <div class="w-1 h-5 bg-gradient-to-b from-red-600 to-red-400 rounded-sm"></div>
                    <p class="text-sm text-red-800 dark:text-red-200 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        {{-- ── METRICS STRIP ── --}}
        @if($centerStats->isNotEmpty())
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                {{-- Total Centers --}}
                <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                         style="background: linear-gradient(135deg, #1d4ed8, #3b82f6); border-radius: 2px;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider"
                           style="font-size: 10px; letter-spacing: 0.1em;">Centers</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-0.5"
                           style="letter-spacing: -0.04em;">{{ $centerStats->count() }}</p>
                    </div>
                </div>

                {{-- Total Candidates --}}
                <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                         style="background: linear-gradient(135deg, #7c3aed, #a78bfa); border-radius: 2px;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider"
                           style="font-size: 10px; letter-spacing: 0.1em;">Candidates</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-0.5"
                           style="letter-spacing: -0.04em;">{{ $centerStats->sum('total_candidates') }}</p>
                    </div>
                </div>

                {{-- Total Submissions --}}
                <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                         style="background: linear-gradient(135deg, #065f46, #059669); border-radius: 2px;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider"
                           style="font-size: 10px; letter-spacing: 0.1em;">Submitted</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-0.5"
                           style="letter-spacing: -0.04em;">{{ $centerStats->sum('submitted') }}</p>
                    </div>
                </div>

                {{-- Parity Failures --}}
                <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                         style="background: linear-gradient(135deg, #b45309, #d97706); border-radius: 2px;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider"
                           style="font-size: 10px; letter-spacing: 0.1em;">Parity Alerts</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-0.5"
                           style="letter-spacing: -0.04em;">{{ $centerStats->sum('parity_failures') }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- ── CENTER CARDS ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5"
                     style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                    style="letter-spacing: 0.1em;">Center Overview</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-0 divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($centerStats as $stats)
                    <div class="p-5 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-slate-900 dark:text-white">{{ $stats['name'] }}</h3>
                            <span class="px-2 py-0.5 text-xs font-medium rounded-sm
                                {{ $stats['is_active'] ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' }}">
                                {{ $stats['is_active'] ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-center mb-4">
                            <div class="bg-slate-50 dark:bg-slate-800/50 px-3 py-2 rounded-sm">
                                <div class="text-xs text-slate-500 dark:text-slate-400" style="font-size: 10px; letter-spacing: 0.08em;">CANDIDATES</div>
                                <div class="text-lg font-bold text-slate-900 dark:text-white">{{ $stats['total_candidates'] }}</div>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-800/50 px-3 py-2 rounded-sm">
                                <div class="text-xs text-slate-500 dark:text-slate-400" style="font-size: 10px; letter-spacing: 0.08em;">SUBMITTED</div>
                                <div class="text-lg font-bold text-slate-900 dark:text-white">{{ $stats['submitted'] }}</div>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-800/50 px-3 py-2 rounded-sm">
                                <div class="text-xs text-slate-500 dark:text-slate-400" style="font-size: 10px; letter-spacing: 0.08em;">COMPLETION</div>
                                <div class="text-lg font-bold text-slate-900 dark:text-white">{{ $stats['completion_rate'] }}%</div>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-800/50 px-3 py-2 rounded-sm">
                                <div class="text-xs text-slate-500 dark:text-slate-400" style="font-size: 10px; letter-spacing: 0.08em;">AVG SCORE</div>
                                <div class="text-lg font-bold text-slate-900 dark:text-white">{{ $stats['avg_score'] ?? 'N/A' }}</div>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-800/50 px-3 py-2 rounded-sm">
                                <div class="text-xs text-slate-500 dark:text-slate-400" style="font-size: 10px; letter-spacing: 0.08em;">FLAGGED</div>
                                <div class="text-lg font-bold text-red-600 dark:text-red-400">{{ $stats['flagged_proctoring'] }}</div>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-800/50 px-3 py-2 rounded-sm">
                                <div class="text-xs text-slate-500 dark:text-slate-400" style="font-size: 10px; letter-spacing: 0.08em;">BACKLOG</div>
                                <div class="text-lg font-bold {{ $stats['backlog'] > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-900 dark:text-white' }}">
                                    {{ $stats['backlog'] }}
                                </div>
                            </div>
                        </div>

                        <div class="text-xs text-slate-500 dark:text-slate-400 space-y-1">
                            <div>Last synced: {{ $stats['last_sync_at'] ? $stats['last_sync_at']->diffForHumans() : 'Never' }}</div>
                            <div>Last activity: {{ $stats['last_activity_at'] ? $stats['last_activity_at']->diffForHumans() : 'None' }}</div>
                            @if($stats['parity_failures'] > 0)
                                <div class="text-red-600 dark:text-red-400 font-medium">Parity failures: {{ $stats['parity_failures'] }}</div>
                            @endif
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('center-sync.centers.show', $stats['id']) }}"
                                class="inline-flex items-center gap-1 text-xs font-semibold text-amber-700 dark:text-amber-400 hover:text-amber-900 dark:hover:text-amber-300 transition-colors">
                                View Details
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-sm text-slate-400 dark:text-slate-500">
                        No centers configured. Create a center to get started.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ── PARITY ALERTS ── --}}
        @if($parityAlerts->isNotEmpty())
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5"
                         style="background: linear-gradient(180deg, #b45309, #fbbf24); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                        style="letter-spacing: 0.1em;">Parity Alerts ({{ $parityAlerts->count() }})</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Submission</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Exam</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Center Score</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Central Score</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Checked</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                            @foreach($parityAlerts as $alert)
                                <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="px-6 py-3.5 text-slate-800 dark:text-slate-200 font-semibold">{{ $alert->participant_name ?? 'Unknown' }}</td>
                                    <td class="px-6 py-3.5 text-slate-700 dark:text-slate-300">{{ $alert->exam?->title ?? 'Unknown' }}</td>
                                    <td class="px-6 py-3.5 text-slate-700 dark:text-slate-300">{{ $alert->score }}</td>
                                    <td class="px-6 py-3.5 text-slate-700 dark:text-slate-300">{{ $alert->central_score }}</td>
                                    <td class="px-6 py-3.5 text-slate-700 dark:text-slate-300">{{ $alert->parity_checked_at?->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- ── CROSS-CENTER DUPLICATES ── --}}
        @if(count($duplicateDetails) > 0)
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5"
                         style="background: linear-gradient(180deg, #dc2626, #f87171); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                        style="letter-spacing: 0.1em;">Cross-Center Duplicates ({{ count($duplicateDetails) }})</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Index Number</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Centers</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Exam</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                            @foreach($duplicateDetails as $indexNumber => $submissions)
                                <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="px-6 py-3.5 font-mono text-slate-800 dark:text-slate-200 font-semibold">{{ $indexNumber }}</td>
                                    <td class="px-6 py-3.5 text-slate-700 dark:text-slate-300">{{ $submissions->first()->participant_name ?? 'Unknown' }}</td>
                                    <td class="px-6 py-3.5 text-slate-700 dark:text-slate-300">{{ $submissions->pluck('center_id')->unique()->count() }} centers</td>
                                    <td class="px-6 py-3.5 text-slate-700 dark:text-slate-300">{{ $submissions->first()->exam?->title ?? 'Unknown' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>{{-- /container --}}
</x-layouts.app>
