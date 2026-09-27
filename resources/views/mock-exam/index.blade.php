<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7" style="font-family: 'system-ui', -apple-system, sans-serif;">

        {{-- ── PAGE HEADER ── --}}
        <x-mock-header title="Mock Exams" >
            <x-slot:actions>
                <a href="{{ route('mock-exams.identity-fields.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-300 border border-slate-600 hover:border-slate-400 transition-all" style="border-radius: 2px;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                    Identity Fields
                </a>
                <a href="{{ route('mock-exams.templates.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-300 border border-slate-600 hover:border-slate-400 transition-all" style="border-radius: 2px;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                    Templates
                </a>
                <a href="{{ route('mock-exams.grade-scales.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-300 border border-slate-600 hover:border-slate-400 transition-all" style="border-radius: 2px;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    Grading
                </a>
                <a href="{{ route('mock-exams.create') }}" class="inline-flex items-center gap-2 px-5 py-2 text-xs font-semibold text-white transition-all" style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.4);">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Mock
                </a>
            </x-slot:actions>
        </x-mock-header>

        {{-- ── FLASH MESSAGES ── --}}
        @if(session('success'))
            <div class="px-5 py-3 text-sm text-emerald-800 dark:text-emerald-200 flex items-center gap-2" style="border-radius: 2px; background: #f0fdf4; border: 1px solid #bbf7d0;">
                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {!! session('success') !!}
            </div>
        @endif

        {{-- ── TOOLBAR: VIEW TOGGLE & FILTERS ── --}}
        <div class="bg-white dark:bg-slate-900 p-4 flex flex-col lg:flex-row lg:items-center justify-between gap-4" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">

            {{-- View Toggle --}}
            <div class="flex items-center bg-slate-100 dark:bg-slate-800 p-1 shrink-0" style="border-radius: 2px;">
                <a href="{{ route('mock-exams.index', array_merge($filters, ['view' => 'list'])) }}"
                   class="flex items-center gap-2 px-3 py-1.5 text-sm font-medium transition-all {{ ($filters['view'] ?? 'list') === 'list' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}"
                   style="border-radius: 2px;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    List
                </a>
                <a href="{{ route('mock-exams.index', array_merge($filters, ['view' => 'table'])) }}"
                   class="flex items-center gap-2 px-3 py-1.5 text-sm font-medium transition-all {{ ($filters['view'] ?? 'list') === 'table' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}"
                   style="border-radius: 2px;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    Table
                </a>
            </div>

            {{-- Filters Form --}}
            <form method="GET" action="{{ route('mock-exams.index') }}" class="flex flex-wrap items-center gap-2 flex-1 justify-end">
                <input type="hidden" name="view" value="{{ $filters['view'] ?? 'list' }}">

                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search title, code..."
                           class="w-48 pl-9 pr-4 py-2 text-sm border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all" style="border-radius: 2px;">
                </div>

                <select name="status" onchange="this.form.submit()" class="px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all" style="border-radius: 2px;">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ ($filters['status'] ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ ($filters['status'] ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                </select>

                <select name="sort_by" onchange="this.form.submit()" class="px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all" style="border-radius: 2px;">
                    <option value="created_at" {{ ($filters['sort_by'] ?? 'created_at') === 'created_at' ? 'selected' : '' }}>Date</option>
                    <option value="title" {{ ($filters['sort_by'] ?? '') === 'title' ? 'selected' : '' }}>Title</option>
                    <option value="status" {{ ($filters['sort_by'] ?? '') === 'status' ? 'selected' : '' }}>Status</option>
                </select>

                <select name="sort_direction" onchange="this.form.submit()" class="px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all" style="border-radius: 2px;">
                    <option value="desc" {{ ($filters['sort_direction'] ?? 'desc') === 'desc' ? 'selected' : '' }}>Descending</option>
                    <option value="asc" {{ ($filters['sort_direction'] ?? '') === 'asc' ? 'selected' : '' }}>Ascending</option>
                </select>

                @if(!empty($filters['search']) || !empty($filters['status']) || ($filters['sort_by'] ?? 'created_at') !== 'created_at' || ($filters['sort_direction'] ?? 'desc') !== 'desc')
                    <a href="{{ route('mock-exams.index', ['view' => $filters['view'] ?? 'list']) }}" class="inline-flex items-center gap-1 px-3 py-2 text-xs font-medium text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors" style="border-radius: 2px;">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- ── CONTENT AREA ── --}}
        @if(($filters['view'] ?? 'list') === 'table')
            {{-- TABLE VIEW --}}
            <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Code</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Subjects</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Submissions</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                        @forelse($exams as $exam)
                            <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-6 py-3.5 font-semibold text-slate-800 dark:text-slate-200">
                                    {{ $exam->title }}
                                    @if($exam->description)
                                        <p class="text-xs font-normal text-slate-500 dark:text-slate-400 mt-1 line-clamp-1">{{ $exam->description }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5">
                                <span class="inline-flex items-center justify-center text-xs font-mono font-medium px-2.5 py-1 border border-slate-200 dark:border-slate-600 bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-700 dark:to-slate-800 text-slate-700 dark:text-slate-300" style="border-radius: 2px;">
                                     {{ $exam->access_code ?? 'N/A' }}
                                 </span>
                                </td>
                                <td class="px-6 py-3.5 text-center">
                                 <span class="inline-flex items-center justify-center text-xs font-semibold px-2.5 py-1 border"
                                       @if($exam->status === 'published')
                                           style="border-radius: 2px; color: #065f46; background-color: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.2);"
                                       @else
                                           style="border-radius: 2px; color: #92400e; background-color: rgba(245, 158, 11, 0.1); border-color: rgba(245, 158, 11, 0.2);"
                                     @endif>
                                     {{ ucfirst($exam->status) }}
                                 </span>
                                </td>
                                <td class="px-6 py-3.5 text-center text-slate-700 dark:text-slate-300 font-medium">
                                    {{ $exam->subject_exams_count }}
                                </td>
                                <td class="px-6 py-3.5 text-center text-slate-700 dark:text-slate-300 font-medium">
                                    {{ $exam->submissions_count }}
                                </td>
                                <td class="px-6 py-3.5 text-right">
                                    <div class="inline-flex items-center gap-2 justify-end">
                                        <a class="inline-flex items-center gap-1 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-amber-700 dark:hover:text-amber-400 transition-colors"
                                           href="{{ route('mock-exams.show', $exam) }}" title="View Details">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        @if($exam->submissions_count == 0)
                                            <a class="inline-flex items-center gap-1 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors"
                                               href="{{ route('mock-exams.edit', $exam) }}" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                            <form method="POST" action="{{ route('mock-exams.destroy', $exam) }}" class="inline" onsubmit="return confirm('Permanently delete this mock exam?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="inline-flex items-center gap-1 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-red-700 dark:hover:text-red-400 transition-colors" title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-400 dark:text-slate-500">
                                    No mock exams found matching your filters.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            {{-- LIST VIEW (Cards) --}}
            <div class="space-y-4">
                @forelse($exams as $exam)
                    <x-ui.card variant="default" :shadow="true">
                        <x-ui.card-header :title="$exam->title" accent="primary">
                            <x-slot:actions>
                                <div class="flex items-center gap-2">
                                 <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold border"
                                       style="border-radius: 2px;
                                          {{ $exam->status === 'published'
                                              ? 'color:#065f46; background:#ecfdf5; border-color:#6ee7b7;'
                                              : 'color:#92400e; background:#fffbeb; border-color:#fde68a;' }}">
                                 {{ ucfirst($exam->status) }}
                             </span>
                                </div>
                            </x-slot:actions>
                        </x-ui.card-header>
                        <div class="p-5">
                            @if($exam->description)
                                <p class="text-sm text-slate-600 dark:text-slate-400 mb-5">{{ $exam->description }}</p>
                            @endif
                            {{-- Metrics Grid --}}
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
                                @foreach([
                                    ['label' => 'Access Code',    'value' => $exam->access_code ?? '—',    'mono' => true],
                                    ['label' => 'Subject Exams',  'value' => $exam->subject_exams_count,    'mono' => false],
                                    ['label' => 'Delivery',       'value' => ucfirst($exam->delivery_type ?? 'N/A'), 'mono' => false],
                                    ['label' => 'Submissions',    'value' => $exam->submissions_count,       'mono' => false],
                                ] as $metric)
                                    <div class="bg-white dark:bg-slate-900 px-4 py-4 flex flex-col items-center justify-center text-center" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                                        <p class="text-slate-500 dark:text-slate-400 uppercase mb-1" style="font-size: 10px; letter-spacing: 0.12em;">
                                            {{ $metric['label'] }}
                                        </p>
                                        <p class="font-bold text-slate-900 dark:text-white text-lg tracking-tight {{ $metric['mono'] ? 'font-mono' : '' }}">
                                            {{ $metric['value'] }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                            {{-- Footer row --}}
                            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                @if($exam->starts_at)
                                    <div class="flex items-center gap-3 text-sm">
                                        <div class="w-9 h-9 flex-shrink-0 flex items-center justify-center" style="border-radius: 2px; background: linear-gradient(135deg, #1e293b, #334155);">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-xs text-slate-500 uppercase tracking-widest" style="font-size:10px;">Opens</div>
                                            <div class="font-semibold text-slate-900 dark:text-white">{{ $exam->starts_at->format('M d, Y \a\t H:i') }}</div>
                                            @if($exam->ends_at)
                                                <div class="text-xs text-slate-500">Closes {{ $exam->ends_at->format('M d, Y \a\t H:i') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="text-sm text-slate-400">No schedule set</div>
                                @endif

                                <div class="flex flex-wrap gap-2">
                                    <x-ui.button href="{{ route('mock-exams.show', $exam) }}" variant="secondary" size="sm" icon="eye">Manage</x-ui.button>

                                    @if($exam->isPrint())
{{--                                        <x-ui.button href="{{ route('mock-exams.pdf', $exam) }}" variant="info" size="sm" icon="document-arrow-down">Exam PDF</x-ui.button>--}}
{{--                                        <x-ui.button href="{{ route('mock-exams.pdf.answer-key', $exam) }}" variant="ghost" size="sm" icon="document-text">Answer Key</x-ui.button>--}}
                                    @else
                                        <x-ui.button href="{{ route('mock-exams.results.index', $exam) }}" variant="ghost" size="sm" icon="chart-bar">Results</x-ui.button>
                                    @endif

                                    @if($exam->submissions_count == 0)
                                        <x-ui.button href="{{ route('mock-exams.edit', $exam) }}" variant="success" size="sm" icon="pencil">Edit</x-ui.button>
                                        <form method="POST" action="{{ route('mock-exams.destroy', $exam) }}" onsubmit="return confirm('Permanently delete this mock exam?')">
                                            @csrf @method('DELETE')
                                            <x-ui.button type="submit" variant="danger" size="sm" icon="trash"></x-ui.button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </x-ui.card>
                @empty
                    <div class="bg-white dark:bg-slate-900 text-center py-16" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 flex items-center justify-center mb-4" style="border-radius: 2px; background: linear-gradient(135deg, #64748b, #94a3b8);">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <p class="text-lg font-semibold text-slate-900 dark:text-white">No mock exams found</p>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Try adjusting your filters or create a new mock exam</p>
                            <a href="{{ route('mock-exams.create') }}" class="mt-6 inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white" style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
                                Create First Mock Exam
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        @endif

        {{-- Pagination --}}
        @if($exams->hasPages())
            <div class="bg-white dark:bg-slate-900 px-5 py-4" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                {{ $exams->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
