<x-layouts.app>
    <x-examination-hub.navigation active="center-exams" />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
         style="font-family: 'system-ui', -apple-system, sans-serif;">

        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden"
             style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #065f46, #059669, #10b981);"></div>
            <div class="px-7 py-6">
                <a href="{{ route('center-sync.exams.index') }}"
                   class="inline-flex items-center gap-1 text-sm text-slate-400 hover:text-slate-200 transition-colors mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                    </svg>
                    Back to Exams
                </a>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h1 class="text-2xl font-bold text-white leading-snug"
                        style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                        {{ $exam->title }}
                    </h1>
                    <div class="flex gap-2">
                        @if($exam->status !== 'published')
                            <form method="POST" action="{{ route('center-sync.exams.publish', $exam) }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-sm bg-gradient-to-br from-emerald-600 to-emerald-400 shadow-[0_2px_10px_rgba(5,150,105,0.3)] hover:from-emerald-700 hover:to-emerald-500 transition-all">
                                    Publish
                                </button>
                            </form>
                        @else
                            <a href="{{ route('center-sync.corrections.show', $exam) }}"
                               class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-sm bg-gradient-to-br from-amber-600 to-amber-400 shadow-[0_2px_10px_rgba(217,119,6,0.3)] hover:from-amber-700 hover:to-amber-500 transition-all">
                                Republish
                            </a>
                            <form method="POST" action="{{ route('center-sync.exams.release-results', $exam) }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-sm bg-gradient-to-br from-violet-600 to-violet-400 shadow-[0_2px_10px_rgba(124,58,237,0.3)] hover:from-violet-700 hover:to-violet-500 transition-all">
                                    Release Results
                                </button>
                            </form>
                        @endif
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
        @if(session('error'))
            <div class="bg-white dark:bg-slate-900 overflow-hidden rounded-sm border border-red-500/20 shadow-[0_1px_6px_rgba(220,38,38,0.08)]">
                <div class="px-5 py-4 flex items-center gap-2 bg-gradient-to-br from-red-50/[0.3] to-orange-50/[0.1]">
                    <div class="w-1 h-5 bg-gradient-to-b from-red-600 to-red-400 rounded-sm"></div>
                    <p class="text-sm text-red-800 dark:text-red-200 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        {{-- ── EXAM INFO ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5"
                     style="background: linear-gradient(180deg, #1d4ed8, #3b82f6); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                    style="letter-spacing: 0.1em;">Exam Info</h2>
            </div>
            <div class="p-5">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex justify-between items-center">
                        <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Status</dt>
                        <dd>
                            @if($exam->status === 'published')
                                <span class="px-2 py-0.5 text-xs font-medium rounded-sm bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">Published</span>
                            @else
                                <span class="px-2 py-0.5 text-xs font-medium rounded-sm bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300">Draft</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Access Code</dt>
                        <dd class="font-mono text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $exam->access_code ?? 'Not set' }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Published</dt>
                        <dd class="text-sm text-slate-700 dark:text-slate-300">{{ $exam->published_at ? $exam->published_at->diffForHumans() : ($exam->status === 'published' ? 'Yes' : 'Not published') }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Revisions</dt>
                        <dd class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $exam->packages->count() ?? 0 }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Results Released</dt>
                        <dd class="text-sm text-slate-700 dark:text-slate-300">{{ $exam->results_released ? 'Yes' : 'No' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- ── PACKAGE HISTORY ── --}}
        @if($exam->packages && $exam->packages->isNotEmpty())
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5"
                         style="background: linear-gradient(180deg, #b45309, #d97706); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                        style="letter-spacing: 0.1em;">Package History</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Revision</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Checksum</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                            @foreach($exam->packages as $pkg)
                                <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="px-6 py-3.5 font-semibold text-slate-800 dark:text-slate-200">{{ $pkg->revision }}</td>
                                    <td class="px-6 py-3.5 font-mono text-xs text-slate-700 dark:text-slate-300">{{ Str::limit($pkg->checksum, 20) }}</td>
                                    <td class="px-6 py-3.5 text-slate-700 dark:text-slate-300">{{ $pkg->created_at->diffForHumans() }}</td>
                                    <td class="px-6 py-3.5">
                                        <a href="{{ route('center-sync.exams.package-detail', [$exam, $pkg->revision]) }}"
                                           class="inline-flex items-center gap-1 text-xs font-medium text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            View Details
                                        </a>
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
