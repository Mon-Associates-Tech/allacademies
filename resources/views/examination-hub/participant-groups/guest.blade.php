<x-layouts.exam>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        {{-- ── PAGE HEADER ── --}}
        <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-slate-900 to-slate-800 shadow-xl border border-slate-700/50">
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-indigo-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl"></div>
            <div class="relative px-6 py-8 sm:px-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">
                    Participant Groups & Exam References
                </h1>
                <p class="mt-2 text-sm sm:text-base text-slate-300 max-w-2xl">
                    Browse available participant groups and review the exams associated with each group.
                </p>
            </div>
            <div class="h-1 w-full bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500"></div>
        </div>

        {{-- ── SEARCH FORM ── --}}
        <form method="GET" class="bg-white dark:bg-slate-800/60 backdrop-blur-sm p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
                <div class="flex-1">
                    <label for="search" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Search</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input id="search" name="search" type="search" value="{{ old('search', $search ?? '') }}" placeholder="Search by code, student, email, or group"
                               class="block w-full pl-10 pr-3 py-2.5 text-sm text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition" />
                    </div>
                </div>
                <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Search
                </button>
            </div>
        </form>

        @if($exams->isEmpty())
            {{-- ── EMPTY STATE ── --}}
            <div class="bg-white dark:bg-slate-800/60 backdrop-blur-sm rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="p-10 sm:p-16 text-center">
                    <div class="w-16 h-16 mx-auto flex items-center justify-center mb-5 bg-indigo-50 dark:bg-indigo-500/10 rounded-full">
                        <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-3-3v6m10 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-1">No matching references found</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 max-w-sm mx-auto">Try adjusting your search terms or check back later for new exam references.</p>
                </div>
            </div>
        @else
            {{-- ── EXAM TABLE ── --}}
            <div class="bg-white dark:bg-slate-800/60 backdrop-blur-sm rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-6 bg-gradient-to-b from-indigo-500 to-blue-500 rounded-full"></div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase tracking-wider">Exam References</h2>
                    </div>
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-700 px-2.5 py-1 rounded-full">{{ $exams->count() }} Total</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wider">
                                <th class="px-5 py-3 text-left">Group</th>
                                <th class="px-5 py-3 text-left">Exam</th>
                                <th class="px-5 py-3 text-left">Code</th>
                                <th class="px-5 py-3 text-left">Duration</th>
                                <th class="px-5 py-3 text-left">Starts</th>
                                <th class="px-5 py-3 text-left">Ends</th>
                                <th class="px-5 py-3 text-right">Participants</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                            @foreach($exams as $exam)
                                @php
                                    // Format Duration
                                    $mins = $exam->duration_in_minutes;
                                    $durationStr = 'N/A';
                                    if ($mins !== null) {
                                        $h = floor($mins / 60);
                                        $m = $mins % 60;
                                        $durationStr = $h > 0 ? "{$h}h {$m}m" : "{$m}m";
                                    }

                                    // Determine Exam Status
                                    $now = now();
                                    $isActive = $exam->starts_at && $exam->starts_at <= $now && (!$exam->ends_at || $exam->ends_at >= $now);
                                    $isUpcoming = $exam->starts_at && $exam->starts_at > $now;
                                    $isEnded = $exam->ends_at && $exam->ends_at < $now;
                                @endphp
                                <tr class="group hover:bg-slate-50 dark:hover:bg-slate-900/30 transition-colors">
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center mr-3">
                                                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            </div>
                                            <span class="font-semibold text-slate-900 dark:text-white">{{ $exam->participantGroup?->name ?? 'Unassigned' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-slate-700 dark:text-slate-300">{{ $exam->title }}</td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <code class="px-2 py-1 text-xs font-medium text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-500/10 rounded-md">{{ $exam->access_code }}</code>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-slate-700 dark:text-slate-300">
                                        <span class="inline-flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $durationStr }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-slate-700 dark:text-slate-300">
                                        <div class="flex items-center gap-2">
                                            <span>{{ optional($exam->starts_at)->format('M j, Y') ?? 'TBD' }}</span>
                                            @if($isActive)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 ring-1 ring-inset ring-emerald-600/20">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                    Active
                                                </span>
                                            @elseif($isUpcoming)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 ring-1 ring-inset ring-amber-600/20">
                                                    Upcoming
                                                </span>
                                            @elseif($isEnded)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400 ring-1 ring-inset ring-slate-500/20">
                                                    Ended
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-slate-700 dark:text-slate-300">{{ optional($exam->ends_at)->format('M j, Y') ?? 'TBD' }}</td>
                                    <td class="px-5 py-4 whitespace-nowrap text-right">
                                        <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 rounded-lg transition-colors" onclick="document.getElementById('exam-participants-{{ $exam->id }}').classList.toggle('hidden'); this.querySelector('.chevron').classList.toggle('rotate-180')">
                                            <span>{{ $exam->configuredParticipants->count() }} Students</span>
                                            <svg class="w-3.5 h-3.5 chevron transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                <tr id="exam-participants-{{ $exam->id }}" class="hidden">
                                    <td colspan="7" class="px-5 py-5 bg-slate-50/50 dark:bg-slate-900/30">
                                        <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                                            <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                                                <h4 class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Configured Participants</h4>
                                            </div>
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full text-sm">
                                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                                                        @forelse($exam->configuredParticipants as $participant)
                                                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/30 transition-colors">
                                                                <td class="px-4 py-3 whitespace-nowrap">
                                                                    <div class="flex items-center">
                                                                        <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center mr-3 text-xs font-bold text-indigo-600 dark:text-indigo-400">
                                                                            {{ strtoupper(substr($participant->name, 0, 1)) }}
                                                                        </div>
                                                                        <div>
                                                                            <div class="font-medium text-slate-900 dark:text-white text-sm">{{ $participant->name }}</div>
                                                                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $participant->email }}</div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                                                    <code class="px-2 py-1 text-xs font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded">{{ $participant->unique_code ?? '-' }}</code>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="2" class="px-4 py-6 text-center text-sm text-slate-500 dark:text-slate-400">
                                                                    No configured participants found for this exam.
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-layouts.exam>