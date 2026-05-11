{{-- ═══════════════════════════════════════════════════════════
     PAGE SHELL
═══════════════════════════════════════════════════════════ --}}
<x-layouts.app>
    <x-examinations-hub.navigation active="manage" />
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
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            
            {{-- Card Header --}}
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2 bg-slate-50/50 dark:bg-slate-800/30">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm uppercase tracking-wider" style="letter-spacing: 0.1em;">
                        {{ $exam->title }}
                    </h3>
                </div>
                <div>
                    @php
                        $statusStyle = $exam->status === 'published'
                            ? 'color:#065f46;background:#ecfdf5;border-color:#a7f3d0;'
                            : 'color:#475569;background:#f1f5f9;border-color:#e2e8f0;';
                    @endphp
                    <span class="inline-flex items-center justify-center text-xs font-semibold px-3 py-1 border"
                          style="border-radius: 2px; {{ $statusStyle }}">
                        {{ ucfirst($exam->status) }}
                    </span>
                </div>
            </div>

            <div class="p-5">
                @if($exam->description)
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-5">{{ $exam->description }}</p>
                @endif

                {{-- Metrics Grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                    {{-- Access Code --}}
                    <div class="bg-white dark:bg-slate-900 px-4 py-4 flex flex-col items-center justify-center text-center"
                         style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1" style="font-size: 10px; letter-spacing: 0.1em;">Access Code</p>
                        <p class="text-lg font-mono font-bold text-slate-900 dark:text-white" style="letter-spacing: -0.02em;">{{ $exam->access_code }}</p>
                    </div>

                    {{-- Sections --}}
                    <div class="bg-white dark:bg-slate-900 px-4 py-4 flex flex-col items-center justify-center text-center"
                         style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1" style="font-size: 10px; letter-spacing: 0.1em;">Sections</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white" style="letter-spacing: -0.04em;">{{ $exam->sections_count }}</p>
                    </div>

                    {{-- Questions --}}
                    <div class="bg-white dark:bg-slate-900 px-4 py-4 flex flex-col items-center justify-center text-center"
                         style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1" style="font-size: 10px; letter-spacing: 0.1em;">Questions</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white" style="letter-spacing: -0.04em;">{{ $exam->questions_count }}</p>
                    </div>

                    {{-- Submissions --}}
                    <div class="bg-white dark:bg-slate-900 px-4 py-4 flex flex-col items-center justify-center text-center"
                         style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1" style="font-size: 10px; letter-spacing: 0.1em;">Submissions</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white" style="letter-spacing: -0.04em;">{{ $exam->submissions_count }}</p>
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                    {{-- Schedule Info --}}
                    @if($exam->starts_at)
                        <div class="flex items-center gap-3 text-sm">
                            <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center"
                                 style="border-radius: 2px; background: linear-gradient(135deg, #1e293b, #334155);">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Scheduled</div>
                                <div class="font-semibold text-slate-900 dark:text-white">{{ $exam->starts_at->format('M d, Y \a\t h:i A') }}</div>
                                @if($exam->ends_at)
                                    <div class="text-xs text-slate-500 dark:text-slate-400">Ends: {{ $exam->ends_at->format('M d, Y \a\t h:i A') }}</div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-3 text-sm">
                            <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center"
                                 style="border-radius: 2px; background: linear-gradient(135deg, #64748b, #94a3b8);">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Schedule</div>
                                <div class="font-semibold text-slate-400 dark:text-slate-500">Not scheduled</div>
                            </div>
                        </div>
                    @endif

                    {{-- Action Buttons --}}
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('examinations-hub.exams.show', $exam) }}" 
                           class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all"
                           style="border-radius: 2px; background: linear-gradient(135deg, #1e293b, #334155); box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            View Details
                        </a>
                        @if(!$exam->starts_at || now()->lt($exam->starts_at))
                            <a href="{{ route('examinations-hub.exams.edit', $exam) }}" 
                               class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all"
                               style="border-radius: 2px; background: linear-gradient(135deg, #065f46, #059669); box-shadow: 0 2px 6px rgba(5,150,105,0.3);">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit
                            </a>
                        @endif
                        <a href="{{ route('examinations-hub.submissions.index', $exam) }}" 
                           class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200 transition-all border"
                           style="border-radius: 2px; border-color: rgba(0,0,0,0.06); background: linear-gradient(135deg, #f8fafc, #f1f5f9);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Submissions
                        </a>
                    </div>
                </div>
            </div>
        </div>
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
                <a href="{{ route('examinations-hub.create') }}" 
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