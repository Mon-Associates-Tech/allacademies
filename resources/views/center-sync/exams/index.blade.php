<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
         style="font-family: 'system-ui', -apple-system, sans-serif;">

        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden"
             style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #065f46, #059669, #10b981);"></div>
            <div class="px-7 py-6">
                <h1 class="text-2xl font-bold text-white leading-snug"
                    style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                    Published Exams
                </h1>
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

        {{-- ── EXAMS TABLE ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-1 h-5"
                         style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                        style="letter-spacing: 0.1em;">All Exams</h2>
                </div>

                <form method="GET" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                    <div class="relative flex-1 sm:flex-none">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title..."
                            class="w-full sm:w-64 pl-9 pr-4 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
                    </div>
                    <select name="status" onchange="this.form.submit()"
                        class="px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
                        <option value="">All Status</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                    @if(!empty(request('search')) || !empty(request('status')))
                        <a href="{{ route('center-sync.exams.index') }}" class="inline-flex items-center gap-1 px-3 py-2 text-xs font-medium text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-sm transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Clear
                        </a>
                    @endif
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Revisions</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Published</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Access Code</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                    @forelse($exams as $exam)
                        <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-3.5 font-semibold text-slate-800 dark:text-slate-200">{{ $exam->title }}</td>
                            <td class="px-6 py-3.5">
                                @if($exam->status === 'published')
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-sm bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">Published</span>
                                @else
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-sm bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300">Draft</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 text-center text-slate-700 dark:text-slate-300 font-medium">{{ $exam->packages_count ?? 0 }}</td>
                            <td class="px-6 py-3.5 text-slate-700 dark:text-slate-300">{{ $exam->published_at ? $exam->published_at->diffForHumans() : ($exam->status === 'published' ? 'Yes' : '-') }}</td>
                            <td class="px-6 py-3.5">
                                <span class="inline-flex items-center justify-center text-xs font-mono font-medium px-2.5 py-1 border border-slate-200 dark:border-slate-600 bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-700 dark:to-slate-800 text-slate-700 dark:text-slate-300" style="border-radius: 2px;">
                                    {{ $exam->access_code ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a class="inline-flex items-center gap-1 text-xs font-semibold text-amber-700 dark:text-amber-400 hover:text-amber-900 dark:hover:text-amber-300 transition-colors"
                                       href="{{ route('center-sync.exams.show', $exam) }}">
                                        View
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                    @if($exam->status !== 'published')
                                        <span class="text-slate-300 dark:text-slate-600">|</span>
                                        <form method="POST" action="{{ route('center-sync.exams.publish', $exam) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300 transition-colors">
                                                Publish
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-slate-300 dark:text-slate-600">|</span>
                                        <a class="text-xs font-semibold text-amber-700 dark:text-amber-400 hover:text-amber-900 dark:hover:text-amber-300 transition-colors"
                                           href="{{ route('center-sync.corrections.show', $exam) }}">
                                            Corrections
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-400 dark:text-slate-500">
                                No exams found.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800">
                {{ $exams->withQueryString()->links() }}
            </div>
        </div>

    </div>{{-- /container --}}
</x-layouts.app>
