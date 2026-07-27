{{-- ═══════════════════════════════════════════════════════════
     MANAGE EXAMINATIONS PAGE
═══════════════════════════════════════════════════════════ --}}
<x-layouts.app>
    <x-examination-hub.navigation active="manage" />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6"
         style="font-family: 'system-ui', -apple-system, sans-serif;">

        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden"
             style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #7c3aed, #a78bfa, #c4b5fd);"></div>
            <div class="px-7 py-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-white leading-snug" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                        Manage Examinations
                    </h1>
                    <p class="text-slate-400 mt-1 text-sm">
                        View, filter, and manage all your examinations
                    </p>
                </div>
                <a href="{{ route('examination-hub.create') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all shrink-0"
                   style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Examination
                </a>
            </div>
        </div>

        {{-- ── TOOLBAR: VIEW TOGGLE & FILTERS ── --}}
        <div class="bg-white dark:bg-slate-900 p-4 flex flex-col lg:flex-row lg:items-center justify-between gap-4"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            
            {{-- View Toggle --}}
            <div class="flex items-center bg-slate-100 dark:bg-slate-800 p-1 shrink-0" style="border-radius: 2px;">
                <a href="{{ route('examination-hub.manage', array_merge($filters, ['view' => 'list'])) }}" 
                   class="flex items-center gap-2 px-3 py-1.5 text-sm font-medium transition-all {{ ($filters['view'] ?? 'list') === 'list' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}"
                   style="border-radius: 2px;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    List
                </a>
                <a href="{{ route('examination-hub.manage', array_merge($filters, ['view' => 'table'])) }}" 
                   class="flex items-center gap-2 px-3 py-1.5 text-sm font-medium transition-all {{ ($filters['view'] ?? 'list') === 'table' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}"
                   style="border-radius: 2px;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    Table
                </a>
            </div>

            {{-- Filters Form --}}
            <form method="GET" action="{{ route('examination-hub.manage') }}" class="flex flex-wrap items-center gap-2 flex-1 justify-end">
                <input type="hidden" name="view" value="{{ $filters['view'] ?? 'list' }}">
                
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search title, code..." 
                           class="w-48 pl-9 pr-4 py-2 text-sm border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all" style="border-radius: 2px;">
                </div>

                <select name="subject" onchange="this.form.submit()" class="px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all" style="border-radius: 2px;">
                    <option value="">All Subjects</option>
                    @foreach($availableSubjects as $subj)
                        <option value="{{ $subj->id }}" {{ ($filters['subject'] ?? '') == $subj->id ? 'selected' : '' }}>{{ $subj->name }}</option>
                    @endforeach
                </select>

                <select name="status" onchange="this.form.submit()" class="px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all" style="border-radius: 2px;">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ ($filters['status'] ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ ($filters['status'] ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="archived" {{ ($filters['status'] ?? '') === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>

                <select name="sort" onchange="this.form.submit()" class="px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all" style="border-radius: 2px;">
                    <option value="created_at_desc" {{ ($filters['sort_by'] ?? 'created_at') === 'created_at' && ($filters['sort_direction'] ?? 'desc') === 'desc' ? 'selected' : '' }}>Newest First</option>
                    <option value="created_at_asc" {{ ($filters['sort_by'] ?? 'created_at') === 'created_at' && ($filters['sort_direction'] ?? 'desc') === 'asc' ? 'selected' : '' }}>Oldest First</option>
                    <option value="title_asc" {{ ($filters['sort_by'] ?? '') === 'title' && ($filters['sort_direction'] ?? '') === 'asc' ? 'selected' : '' }}>Title (A-Z)</option>
                    <option value="title_desc" {{ ($filters['sort_by'] ?? '') === 'title' && ($filters['sort_direction'] ?? '') === 'desc' ? 'selected' : '' }}>Title (Z-A)</option>
                    <option value="subject_asc" {{ ($filters['sort_by'] ?? '') === 'subject' && ($filters['sort_direction'] ?? '') === 'asc' ? 'selected' : '' }}>Subject (A-Z)</option>
                </select>

                @if(!empty($filters['search']) || !empty($filters['status']) || !empty($filters['subject']) || ($filters['sort_by'] ?? 'created_at') !== 'created_at' || ($filters['sort_direction'] ?? 'desc') !== 'desc')
                    <a href="{{ route('examination-hub.manage', ['view' => $filters['view'] ?? 'list']) }}" class="inline-flex items-center gap-1 px-3 py-2 text-xs font-medium text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors" style="border-radius: 2px;">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- ── CONTENT AREA ── --}}
        @if(($filters['view'] ?? 'list') === 'table')
            {{-- TABLE VIEW --}}
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Subject</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Code</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Sections</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Questions</th>
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
                                <td class="px-6 py-3.5 text-slate-700 dark:text-slate-300">
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-md bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                        {{ $exam->academicSubject->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5">
                                   <span class="inline-flex items-center justify-center text-xs font-mono font-medium px-2.5 py-1 border border-slate-200 dark:border-slate-600 bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-700 dark:to-slate-800 text-slate-700 dark:text-slate-300" style="border-radius: 2px;">
                                        {{ $exam->access_code }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-center">
                                    <span class="inline-flex items-center justify-center text-xs font-semibold px-2.5 py-1 border"
                                        @if($exam->status === 'published')
                                            style="border-radius: 2px; color: #065f46; background-color: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.2);"
                                        @elseif($exam->status === 'archived')
                                            style="border-radius: 2px; color: #475569; background-color: rgba(100, 116, 139, 0.1); border-color: rgba(100, 116, 139, 0.2);"
                                        @else
                                            style="border-radius: 2px; color: #92400e; background-color: rgba(245, 158, 11, 0.1); border-color: rgba(245, 158, 11, 0.2);"
                                        @endif>
                                        {{ ucfirst($exam->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-center text-slate-700 dark:text-slate-300 font-medium">
                                    {{ $exam->sections_count }}
                                </td>
                                <td class="px-6 py-3.5 text-center text-slate-700 dark:text-slate-300 font-medium">
                                    {{ $exam->questions_count }}
                                </td>
                                <td class="px-6 py-3.5 text-center text-slate-700 dark:text-slate-300 font-medium">
                                    {{ $exam->submissions_count }}
                                </td>
                                <td class="px-6 py-3.5 text-right">
                                    <div class="inline-flex items-center gap-2 justify-end">
                                        <a class="inline-flex items-center gap-1 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-amber-700 dark:hover:text-amber-400 transition-colors"
                                           href="{{ route('examination-hub.exams.show', $exam) }}" title="View Details">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        @if(!$exam->starts_at || now()->lt($exam->starts_at))
                                            <a class="inline-flex items-center gap-1 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors"
                                               href="{{ route('examination-hub.exams.edit', $exam) }}" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                        @endif
                                        <a class="inline-flex items-center gap-1 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-blue-700 dark:hover:text-blue-400 transition-colors"
                                           href="{{ route('examination-hub.submissions.index', $exam) }}" title="Submissions">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-sm text-slate-400 dark:text-slate-500">
                                    No examinations found matching your filters.
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
                    <x-ui.card variant="default" shadow="true">
                        <x-ui.card-header title="{{ $exam->title }}" accent="primary">
                            <x-slot:actions>
                                <div class="flex items-center gap-2">
                                    @if($exam->academicSubject)
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-md bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700" style="border-radius: 2px;">
                                            {{ $exam->academicSubject->name }}
                                        </span>
                                    @endif
                                    <span class="inline-flex items-center justify-center text-xs font-semibold px-3 py-1 border"
                                        @if($exam->status === 'published')
                                            style="border-radius: 2px; color: #065f46; background-color: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.2);"
                                        @elseif($exam->status === 'archived')
                                            style="border-radius: 2px; color: #475569; background-color: rgba(100, 116, 139, 0.1); border-color: rgba(100, 116, 139, 0.2);"
                                        @else
                                            style="border-radius: 2px; color: #92400e; background-color: rgba(245, 158, 11, 0.1); border-color: rgba(245, 158, 11, 0.2);"
                                        @endif>
                                        {{ ucfirst($exam->status) }}
                                    </span>
                                </div>
                            </x-slot:actions>
                        </x-ui.card-header>

                        <div class="p-5">
                            @if($exam->description)
                                <p class="text-sm text-slate-600 dark:text-slate-400 mb-5 line-clamp-2">{{ $exam->description }}</p>
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
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Try adjusting your filters or create a new examination</p>
                            <a href="{{ route('examination-hub.exams.create') }}"
                               class="mt-6 inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-white transition-all"
                               style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
                                Create Your First Exam
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        @endif

        {{-- Pagination --}}
        @if($exams->hasPages())
            <div class="bg-white dark:bg-slate-900 px-5 py-4"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                {{ $exams->withQueryString()->links() }}
            </div>
        @endif

    </div>{{-- /container --}}
</x-layouts.app>