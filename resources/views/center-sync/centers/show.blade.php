<x-layouts.app>
    <x-examination-hub.navigation active="center-centers" />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
         style="font-family: 'system-ui', -apple-system, sans-serif;">

        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden"
             style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #065f46, #059669, #10b981);"></div>
            <div class="px-7 py-6">
                <a href="{{ route('center-sync.centers.index') }}"
                   class="inline-flex items-center gap-1 text-sm text-slate-400 hover:text-slate-200 transition-colors mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                    </svg>
                    Back to Centers
                </a>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl font-bold text-white leading-snug"
                                style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                                {{ $center->name }}
                            </h1>
                            <span class="px-2 py-0.5 text-xs font-medium rounded-sm
                                {{ $center->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' }}">
                                {{ $center->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <p class="text-slate-400 mt-1 text-sm font-mono">Code: {{ $center->code }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('center-sync.centers.edit', $center) }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200 transition-all border border-slate-200/50 rounded-sm bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800/50 dark:to-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('center-sync.centers.regenerate-token', $center) }}" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all rounded-sm bg-gradient-to-br from-amber-600 to-amber-400 shadow-[0_2px_10px_rgba(217,119,6,0.3)] hover:from-amber-700 hover:to-amber-500">
                                Regenerate Token
                            </button>
                        </form>
                    </div>
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

        {{-- ── TOKEN DISPLAY ── --}}
        @if(session('center_token') && session('center_id') == $center->id)
            <div class="bg-white dark:bg-slate-900 overflow-hidden rounded-sm border border-blue-500/20 shadow-[0_1px_6px_rgba(37,99,235,0.08)]">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center gap-2 bg-gradient-to-br from-blue-50/[0.3] to-indigo-50/[0.1]">
                    <div class="w-1 h-5 bg-gradient-to-b from-blue-600 to-blue-400 rounded-sm"></div>
                    <h2 class="font-bold text-blue-800 dark:text-blue-300 text-xs uppercase tracking-widest">Sync Token</h2>
                </div>
                <div class="p-5">
                    <p class="text-sm text-blue-700 dark:text-blue-400 mb-3">Copy this token before navigating away — it won't be shown again.</p>
                    <div class="flex gap-2">
                        <input type="text" readonly value="{{ session('center_token') }}"
                            class="flex-1 px-4 py-2.5 text-sm font-mono border border-slate-300 dark:border-slate-700 rounded-sm bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100"
                            id="center-token">
                        <button onclick="navigator.clipboard.writeText(document.getElementById('center-token').value)"
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-sm bg-gradient-to-br from-blue-600 to-blue-400 hover:from-blue-700 hover:to-blue-500 transition-all">
                            Copy
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- ── INFO & STATS GRID ── --}}
        <div class="grid lg:grid-cols-2 gap-6">
            {{-- Center Info --}}
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5"
                         style="background: linear-gradient(180deg, #1d4ed8, #3b82f6); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                        style="letter-spacing: 0.1em;">Center Info</h2>
                </div>
                <div class="p-5">
                    <dl class="space-y-4">
                        <div class="flex justify-between items-center">
                            <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Code</dt>
                            <dd class="font-mono text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $center->code }}</dd>
                        </div>
                        <div class="flex justify-between items-center">
                            <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Last Seen</dt>
                            <dd class="text-sm text-slate-700 dark:text-slate-300">{{ $center->last_seen_at ? $center->last_seen_at->diffForHumans() : 'Never' }}</dd>
                        </div>
                        <div class="flex justify-between items-center">
                            <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Last Pull</dt>
                            <dd class="text-sm text-slate-700 dark:text-slate-300">{{ $center->last_pull_at ? $center->last_pull_at->diffForHumans() : 'Never' }}</dd>
                        </div>
                        <div class="flex justify-between items-center">
                            <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Last Push</dt>
                            <dd class="text-sm text-slate-700 dark:text-slate-300">{{ $center->last_push_at ? $center->last_push_at->diffForHumans() : 'Never' }}</dd>
                        </div>
                        <div class="flex justify-between items-center">
                            <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">App Version</dt>
                            <dd class="text-sm text-slate-700 dark:text-slate-300">{{ $center->app_version ?? 'Unknown' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Statistics --}}
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5"
                         style="background: linear-gradient(180deg, #065f46, #059669); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                        style="letter-spacing: 0.1em;">Statistics</h2>
                </div>
                <div class="p-5">
                    <dl class="space-y-4">
                        <div class="flex justify-between items-center">
                            <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Total Candidates</dt>
                            <dd class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $stats['total_candidates'] ?? 0 }}</dd>
                        </div>
                        <div class="flex justify-between items-center">
                            <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Submitted</dt>
                            <dd class="text-sm text-slate-700 dark:text-slate-300">{{ $stats['submitted'] ?? 0 }}</dd>
                        </div>
                        <div class="flex justify-between items-center">
                            <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">In Progress</dt>
                            <dd class="text-sm text-slate-700 dark:text-slate-300">{{ $stats['in_progress'] ?? 0 }}</dd>
                        </div>
                        <div class="flex justify-between items-center">
                            <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Completion Rate</dt>
                            <dd class="text-sm text-slate-700 dark:text-slate-300">{{ $stats['completion_rate'] ?? 0 }}%</dd>
                        </div>
                        <div class="flex justify-between items-center">
                            <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Avg Score</dt>
                            <dd class="text-sm text-slate-700 dark:text-slate-300">{{ $stats['avg_score'] ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between items-center">
                            <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Flagged Proctoring</dt>
                            <dd class="text-sm font-semibold text-red-600 dark:text-red-400">{{ $stats['flagged_proctoring'] ?? 0 }}</dd>
                        </div>
                        <div class="flex justify-between items-center">
                            <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Backlog</dt>
                            <dd class="text-sm font-semibold {{ ($stats['backlog'] ?? 0) > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-700 dark:text-slate-300' }}">{{ $stats['backlog'] ?? 0 }}</dd>
                        </div>
                        <div class="flex justify-between items-center">
                            <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Parity Failures</dt>
                            <dd class="text-sm font-semibold text-red-600 dark:text-red-400">{{ $stats['parity_failures'] ?? 0 }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        {{-- ── RECENT SUBMISSIONS ── --}}
        @if($recentSubmissions->isNotEmpty())
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5"
                         style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                        style="letter-spacing: 0.1em;">Recent Submissions</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Exam</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Score</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Submitted</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                            @foreach($recentSubmissions as $sub)
                                <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="px-6 py-3.5 font-semibold text-slate-800 dark:text-slate-200">{{ $sub->participant_name ?? 'Unknown' }}</td>
                                    <td class="px-6 py-3.5 text-slate-700 dark:text-slate-300">{{ $sub->exam?->title ?? 'Unknown' }}</td>
                                    <td class="px-6 py-3.5 text-slate-700 dark:text-slate-300">{{ $sub->status }}</td>
                                    <td class="px-6 py-3.5 text-slate-700 dark:text-slate-300">{{ $sub->score ?? '-' }}</td>
                                    <td class="px-6 py-3.5 text-slate-700 dark:text-slate-300">{{ $sub->submitted_at?->diffForHumans() ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- ── RECENT SYNC RUNS ── --}}
        @if($recentSyncRuns->isNotEmpty())
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5"
                         style="background: linear-gradient(180deg, #b45309, #d97706); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                        style="letter-spacing: 0.1em;">Recent Sync Runs</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Direction</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Items</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Started</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                            @foreach($recentSyncRuns as $run)
                                <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="px-6 py-3.5 text-slate-700 dark:text-slate-300">{{ $run->direction }}</td>
                                    <td class="px-6 py-3.5">
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-sm
                                            {{ $run->status === 'success' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' : ($run->status === 'failed' ? 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' : 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300') }}">
                                            {{ $run->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 text-slate-700 dark:text-slate-300">{{ $run->items_ok ?? 0 }}/{{ $run->items_total ?? 0 }}</td>
                                    <td class="px-6 py-3.5 text-slate-700 dark:text-slate-300">{{ $run->started_at?->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>{{-- /container --}}
</x-layouts.app>
