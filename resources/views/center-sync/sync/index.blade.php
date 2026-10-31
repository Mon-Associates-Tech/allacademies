<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
         style="font-family: 'system-ui', -apple-system, sans-serif;">

        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden"
             style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #065f46, #059669, #10b981);"></div>
            <div class="px-7 py-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-white leading-snug"
                            style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                            Sync Status
                        </h1>
                        @if(!$isCentral)
                            <p class="text-sm text-slate-400 mt-1">Central server: <span class="text-slate-300">{{ config('center.central_url') }}</span></p>
                        @else
                            <p class="text-sm text-slate-400 mt-1">Monitor all examination centers' sync activity</p>
                        @endif
                    </div>
                    @if(!$isCentral)
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('center-sync.sync.pull') }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-sm bg-gradient-to-br from-blue-600 to-blue-400 shadow-[0_2px_10px_rgba(37,99,235,0.3)] hover:from-blue-700 hover:to-blue-500 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Pull Now
                                </button>
                            </form>
                            <form method="POST" action="{{ route('center-sync.sync.push') }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-sm bg-gradient-to-br from-violet-600 to-violet-400 shadow-[0_2px_10px_rgba(124,58,237,0.3)] hover:from-violet-700 hover:to-violet-500 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    Push Now
                                </button>
                            </form>
                        </div>
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

        @if($isCentral)
            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- CENTRAL MODE: All centers overview                          --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}

            {{-- ── SUMMARY METRICS ── --}}
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="bg-white dark:bg-slate-900 overflow-hidden"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-sm flex items-center justify-center"
                                 style="background: linear-gradient(135deg, #1e3a5f, #3b82f6);">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Total Centers</p>
                                <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $totalCenters }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-900 overflow-hidden"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-sm flex items-center justify-center"
                                 style="background: linear-gradient(135deg, #065f46, #10b981);">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Active</p>
                                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $activeCenters }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-900 overflow-hidden"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-sm flex items-center justify-center"
                                 style="background: linear-gradient(135deg, #92400e, #f59e0b);">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Stale</p>
                                <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $staleCenters }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-900 overflow-hidden"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-sm flex items-center justify-center"
                                 style="background: linear-gradient(135deg, #1d4ed8, #6366f1);">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Backlog</p>
                                <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $totalBacklog }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-900 overflow-hidden"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-sm flex items-center justify-center"
                                 style="background: linear-gradient(135deg, #991b1b, #ef4444);">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Parity Errors</p>
                                <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $totalParityFailures }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── FILTERS & SEARCH ── --}}
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4">
                    <form method="GET" action="{{ route('center-sync.sync.index') }}" class="flex flex-wrap items-center gap-4">
                        {{-- Search --}}
                        <div class="flex-1 min-w-[200px]">
                            <input type="text" name="search" value="{{ $searchQuery ?? '' }}"
                                   placeholder="Search by name or code..."
                                   class="w-full px-4 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-sm bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-violet-500">
                        </div>
                        {{-- Status filter --}}
                        <select name="status"
                                class="px-4 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-sm bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-500">
                            <option value="">All Statuses</option>
                            <option value="active" {{ ($statusFilter ?? '') === 'active' ? 'selected' : '' }}>Active (24h)</option>
                            <option value="stale" {{ ($statusFilter ?? '') === 'stale' ? 'selected' : '' }}>Stale (>24h)</option>
                            <option value="inactive" {{ ($statusFilter ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        {{-- Sort --}}
                        <select name="sort"
                                class="px-4 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-sm bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-500">
                            <option value="last_seen_at" {{ ($sortBy ?? 'last_seen_at') === 'last_seen_at' ? 'selected' : '' }}>Sort by Last Seen</option>
                            <option value="name" {{ ($sortBy ?? '') === 'name' ? 'selected' : '' }}>Sort by Name</option>
                            <option value="backlog" {{ ($sortBy ?? '') === 'backlog' ? 'selected' : '' }}>Sort by Backlog</option>
                            <option value="parity_failures" {{ ($sortBy ?? '') === 'parity_failures' ? 'selected' : '' }}>Sort by Parity Errors</option>
                        </select>
                        <select name="dir"
                                class="px-4 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-sm bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-500">
                            <option value="desc" {{ ($sortDir ?? 'desc') === 'desc' ? 'selected' : '' }}>Descending</option>
                            <option value="asc" {{ ($sortDir ?? '') === 'asc' ? 'selected' : '' }}>Ascending</option>
                        </select>
                        <button type="submit"
                                class="px-5 py-2 text-sm font-semibold text-white rounded-sm bg-gradient-to-br from-violet-600 to-violet-400 shadow-[0_2px_10px_rgba(124,58,237,0.3)] hover:from-violet-700 hover:to-violet-500 transition-all">
                            Apply
                        </button>
                        @if($searchQuery || $statusFilter || $sortBy !== 'last_seen_at' || $sortDir !== 'desc')
                            <a href="{{ route('center-sync.sync.index') }}"
                               class="px-5 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                                Clear
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            {{-- ── CENTER LIST ── --}}
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5"
                         style="background: linear-gradient(180deg, #065f46, #059669); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                        style="letter-spacing: 0.1em;">All Centers</h2>
                    <span class="ml-auto text-xs text-slate-400 dark:text-slate-500">{{ $centerData->count() }} center(s)</span>
                </div>

                @if($centerData->isEmpty())
                    <div class="px-5 py-12 text-center">
                        <svg class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <p class="text-slate-500 dark:text-slate-400 text-sm">No centers found. Create a center to get started.</p>
                        <a href="{{ route('center-sync.centers.create') }}"
                           class="inline-flex items-center gap-1 mt-3 text-sm font-medium text-violet-600 dark:text-violet-400 hover:underline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create Center
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                            <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Center</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Last Seen</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Last Pull</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Last Push</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Submissions</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Backlog</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Parity</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">App Version</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                                @foreach($centerData as $item)
                                    @php $c = $item['center'] @endphp
                                    <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors">
                                        <td class="px-6 py-3.5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-sm flex items-center justify-center text-xs font-bold text-white"
                                                     style="background: linear-gradient(135deg, #1e3a5f, #3b82f6);">
                                                    {{ strtoupper(substr($c->code, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-slate-800 dark:text-slate-200">{{ $c->name }}</p>
                                                    <p class="text-xs text-slate-400 dark:text-slate-500">{{ $c->code }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-3.5 text-center">
                                            @if(!$c->is_active)
                                                <span class="px-2 py-0.5 text-xs font-medium rounded-sm bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">Inactive</span>
                                            @elseif($item['is_stale'])
                                                <span class="px-2 py-0.5 text-xs font-medium rounded-sm bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">Stale</span>
                                            @else
                                                <span class="px-2 py-0.5 text-xs font-medium rounded-sm bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">Active</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3.5 text-center text-slate-700 dark:text-slate-300 text-xs">
                                            {{ $item['last_seen_diff'] ?? 'Never' }}
                                        </td>
                                        <td class="px-6 py-3.5 text-center text-slate-700 dark:text-slate-300 text-xs">
                                            {{ $item['last_pull_diff'] ?? 'Never' }}
                                        </td>
                                        <td class="px-6 py-3.5 text-center text-slate-700 dark:text-slate-300 text-xs">
                                            {{ $item['last_push_diff'] ?? 'Never' }}
                                        </td>
                                        <td class="px-6 py-3.5 text-center text-slate-700 dark:text-slate-300 font-medium">
                                            {{ $item['total_submissions'] }}
                                        </td>
                                        <td class="px-6 py-3.5 text-center">
                                            @if($item['backlog'] > 0)
                                                <span class="px-2 py-0.5 text-xs font-medium rounded-sm bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">{{ $item['backlog'] }}</span>
                                            @else
                                                <span class="text-slate-400 dark:text-slate-500 text-xs">0</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3.5 text-center">
                                            @if($item['parity_failures'] > 0)
                                                <span class="px-2 py-0.5 text-xs font-medium rounded-sm bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300">{{ $item['parity_failures'] }}</span>
                                            @else
                                                <span class="text-slate-400 dark:text-slate-500 text-xs">✓</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3.5 text-center text-slate-700 dark:text-slate-300 text-xs">
                                            {{ $c->app_version ?? '—' }}
                                        </td>
                                        <td class="px-6 py-3.5">
                                            <a href="{{ route('center-sync.centers.show', $c) }}"
                                               class="text-xs font-medium text-violet-600 dark:text-violet-400 hover:underline">
                                                Details →
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- ── RECENT SYNC RUNS ── --}}
            @if($recentSyncRuns->isNotEmpty())
                <div class="bg-white dark:bg-slate-900 overflow-hidden"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                        <div class="w-1 h-5"
                             style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                            style="letter-spacing: 0.1em;">Recent Sync Runs</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                            <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Direction</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Started</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Items</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Error</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                                @foreach($recentSyncRuns as $run)
                                    <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors">
                                        <td class="px-6 py-3.5">
                                            <span class="px-2 py-0.5 text-xs font-medium rounded-sm
                                                {{ $run->direction === 'pull' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-violet-100 text-violet-800 dark:bg-violet-900/40 dark:text-violet-300' }}">
                                                {{ $run->direction }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3.5">
                                            <span class="px-2 py-0.5 text-xs font-medium rounded-sm
                                                {{ $run->status === 'success' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' : ($run->status === 'failed' ? 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300') }}">
                                                {{ $run->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3.5 text-slate-700 dark:text-slate-300 text-xs">{{ $run->started_at->diffForHumans() }}</td>
                                        <td class="px-6 py-3.5 text-center text-slate-700 dark:text-slate-300 text-xs">{{ $run->items_ok ?? 0 }}/{{ $run->items_total ?? 0 }}</td>
                                        <td class="px-6 py-3.5 text-red-600 dark:text-red-400 text-xs">{{ $run->error ? Str::limit($run->error, 80) : '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        @else
            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- CENTER MODE: Local sync status                              --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}

            {{-- ── STATUS CARDS ── --}}
            <div class="grid lg:grid-cols-2 gap-6">
                {{-- Pull Status --}}
                <div class="bg-white dark:bg-slate-900 overflow-hidden"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                        <div class="w-1 h-5"
                             style="background: linear-gradient(180deg, #1d4ed8, #3b82f6); border-radius: 1px;"></div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                            style="letter-spacing: 0.1em;">Pull Status</h2>
                    </div>
                    <div class="p-5">
                        <dl class="space-y-4">
                            <div class="flex justify-between items-center">
                                <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Last Pull</dt>
                                <dd class="text-sm text-slate-700 dark:text-slate-300">{{ $lastPull ? $lastPull->started_at->diffForHumans() : 'Never' }}</dd>
                            </div>
                            <div class="flex justify-between items-center">
                                <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Status</dt>
                                <dd>
                                    @if($lastPull)
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-sm
                                            {{ $lastPull->status === 'success' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' }}">
                                            {{ $lastPull->status }}
                                        </span>
                                    @else
                                        <span class="text-sm text-slate-400 dark:text-slate-500">No data</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Push Status --}}
                <div class="bg-white dark:bg-slate-900 overflow-hidden"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                        <div class="w-1 h-5"
                             style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                            style="letter-spacing: 0.1em;">Push Status</h2>
                    </div>
                    <div class="p-5">
                        <dl class="space-y-4">
                            <div class="flex justify-between items-center">
                                <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Last Push</dt>
                                <dd class="text-sm text-slate-700 dark:text-slate-300">{{ $lastPush ? $lastPush->started_at->diffForHumans() : 'Never' }}</dd>
                            </div>
                            <div class="flex justify-between items-center">
                                <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Backlog</dt>
                                <dd>
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-sm
                                        {{ $backlog > 0 ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' }}">
                                        {{ $backlog }} pending
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            {{-- ── EXAM SYNC STATES ── --}}
            @if($examSyncStates->isNotEmpty())
                <div class="bg-white dark:bg-slate-900 overflow-hidden"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                        <div class="w-1 h-5"
                             style="background: linear-gradient(180deg, #065f46, #059669); border-radius: 1px;"></div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                            style="letter-spacing: 0.1em;">Exam Sync State</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                            <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Exam</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Applied Revision</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">State Version</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Applied At</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                                @foreach($examSyncStates as $state)
                                    <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors">
                                        <td class="px-6 py-3.5 font-semibold text-slate-800 dark:text-slate-200">{{ $state->exam?->title ?? $state->exam_uuid }}</td>
                                        <td class="px-6 py-3.5 text-center text-slate-700 dark:text-slate-300 font-medium">{{ $state->applied_revision }}</td>
                                        <td class="px-6 py-3.5 text-center text-slate-700 dark:text-slate-300">{{ $state->applied_state_version }}</td>
                                        <td class="px-6 py-3.5 text-slate-700 dark:text-slate-300">{{ $state->applied_at?->diffForHumans() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- ── FAILED SYNCS ── --}}
            @if($failedSyncs->isNotEmpty())
                <div class="bg-white dark:bg-slate-900 overflow-hidden"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                        <div class="w-1 h-5"
                             style="background: linear-gradient(180deg, #dc2626, #f87171); border-radius: 1px;"></div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                            style="letter-spacing: 0.1em;">Failed Syncs</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                            <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Direction</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Started</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Error</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                                @foreach($failedSyncs as $run)
                                    <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors">
                                        <td class="px-6 py-3.5 text-slate-700 dark:text-slate-300">{{ $run->direction }}</td>
                                        <td class="px-6 py-3.5 text-slate-700 dark:text-slate-300">{{ $run->started_at->diffForHumans() }}</td>
                                        <td class="px-6 py-3.5 text-red-600 dark:text-red-400 text-sm">{{ Str::limit($run->error ?? 'Unknown error', 100) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endif {{-- /isCentral --}}

    </div>{{-- /container --}}
</x-layouts.app>
