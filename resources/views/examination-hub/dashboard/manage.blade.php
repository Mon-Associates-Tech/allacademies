{{-- ═══════════════════════════════════════════════════════════
     PAGE SHELL
═══════════════════════════════════════════════════════════ --}}
<x-layouts.app>
    <x-examination-hub.navigation active="manage" />
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
     style="font-family: 'system-ui', -apple-system, sans-serif;">

    {{-- ── PAGE HEADER ── --}}
    <div class="overflow-hidden"
         style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
        <div class="h-1 w-full" style="background: linear-gradient(90deg, #7c3aed, #a78bfa, #c4b5fd);"></div>
        <div class="px-7 py-6">
            <h1 class="text-2xl font-bold text-white leading-snug" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                Manage Examinations
            </h1>
            <p class="text-slate-400 mt-2 text-sm">
                View, filter, and manage all your examinations
            </p>
        </div>
    </div>

    {{-- ── FILTER CARD ── --}}
    <div class="bg-white dark:bg-slate-900 overflow-hidden"
         style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
            <div class="w-1 h-5" style="background: linear-gradient(180deg, #2563eb, #60a5fa); border-radius: 1px;"></div>
            <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Filter Examinations</h2>
        </div>
        <div class="p-5">
            <form method="GET" class="grid md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Search</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ $filters['search'] ?? '' }}"
                        placeholder="Search by title or access code..."
                        class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                        style="border-radius: 2px;"
                    >
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Status</label>
                    <select name="status" class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all" style="border-radius: 2px;">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ ($filters['status'] ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ ($filters['status'] ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all w-full"
                            style="border-radius: 2px; background: linear-gradient(135deg, #1e293b, #334155); box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── EXAMINATIONS LIST ── --}}
    @forelse($exams as $exam)
        <x-ui.card variant="default" shadow="true">
            <x-ui.card-header title="{{ $exam->title }}" accent="primary">
                <x-slot:actions>
        <span class="inline-flex items-center justify-center text-xs font-semibold px-3 py-1 border rounded-[2px]
            @if($exam->status === 'published')
                text-emerald-800 bg-emerald-50 border-emerald-200 dark:text-emerald-200 dark:bg-emerald-900/30 dark:border-emerald-800
            @else
                text-slate-700 bg-slate-100 border-slate-200 dark:text-slate-300 dark:bg-slate-800 dark:border-slate-700
            @endif">
            {{ ucfirst($exam->status) }}
        </span>
                </x-slot:actions>
            </x-ui.card-header>

            <div class="p-5">
                @if($exam->description)
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-5">{{ $exam->description }}</p>
                @endif

                {{-- Metrics Grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                    @foreach([
    ['label' => 'Access Code', 'value' => $exam->access_code, 'size' => 'lg', 'mono' => true],
    ['label' => 'Sections',     'value' => $exam->sections_count, 'size' => '2xl', 'mono' => false],
    ['label' => 'Questions',    'value' => $exam->questions_count, 'size' => '2xl', 'mono' => false],
    ['label' => 'Submissions',  'value' => $exam->submissions_count, 'size' => '2xl', 'mono' => false],
] as $metric)
                        <div class="bg-white dark:bg-slate-900 px-4 py-4 flex flex-col items-center justify-center text-center rounded-[2px] border border-slate-200/50 dark:border-slate-800 shadow-sm">
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1 text-[10px]">
                                {{ $metric['label'] }}
                            </p>
                            <p class="font-bold text-slate-900 dark:text-white tracking-tight {{ $metric['mono'] ? 'font-mono' : '' }} text-{{ $metric['size'] }}">
                                {{ $metric['value'] }}
                            </p>
                        </div>
                    @endforeach
                </div>

                <x-ui.card-footer>
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        {{-- Schedule Info --}}
                        @if($exam->starts_at)
                            <div class="flex items-center gap-3 text-sm">
                                <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center rounded-[2px] bg-gradient-to-br from-slate-800 to-slate-700">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-widest">Scheduled</div>
                                    <div class="font-semibold text-slate-900 dark:text-white">{{ $exam->starts_at->format('M d, Y \a\t h:i A') }}</div>
                                    @if($exam->ends_at)
                                        <div class="text-xs text-slate-500 dark:text-slate-400">Ends: {{ $exam->ends_at->format('M d, Y \a\t h:i A') }}</div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="flex items-center gap-3 text-sm">
                                <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center rounded-[2px] bg-gradient-to-br from-slate-500 to-slate-400">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-widest">Schedule</div>
                                    <div class="font-semibold text-slate-400 dark:text-slate-500">Not scheduled</div>
                                </div>
                            </div>
                        @endif

                        {{-- Action Buttons --}}
                        <div class="flex flex-wrap gap-2">
                            <x-ui.button
                                variant="secondary"
                                size="md"
                                icon="eye"
                                href="{{ route('examination-hub.exams.show', $exam) }}"
                            >
                                View Details
                            </x-ui.button>

                            @if(!$exam->starts_at || now()->lt($exam->starts_at))
                                <x-ui.button
                                    variant="success"
                                    size="md"
                                    icon="pencil"
                                    href="{{ route('examination-hub.exams.edit', $exam) }}"
                                >
                                    Edit
                                </x-ui.button>
                            @endif

                            <x-ui.button
                                variant="ghost"
                                size="md"
                                icon="document-text"
                                href="{{ route('examination-hub.submissions.index', $exam) }}"
                            >
                                Submissions
                            </x-ui.button>
                        </div>
                    </div>
                </x-ui.card-footer>
            </div>
        </x-ui.card>
    @empty
        {{-- Empty State --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden text-center py-16"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="flex flex-col items-center">
                <div class="w-16 h-16 flex items-center justify-center mb-4"
                     style="border-radius: 2px; background: linear-gradient(135deg, #64748b, #94a3b8);">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <p class="text-lg font-semibold text-slate-900 dark:text-white">No examinations found</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Get started by creating your first examination</p>
                <a href="{{ route('examination-hub.create') }}"
                   class="mt-6 inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-white transition-all"
                   style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
                    Create Your First Exam
                </a>
            </div>
        </div>
    @endforelse

    {{-- Pagination --}}
    @if($exams->hasPages())
        <div class="bg-white dark:bg-slate-900 px-5 py-4"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            {{ $exams->links() }}
        </div>
    @endif

</div>{{-- /container --}}
</x-layouts.app>
