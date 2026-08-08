<x-layouts.app>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
     style="font-family: 'system-ui', -apple-system, sans-serif;">

    {{-- ── PAGE HEADER ── --}}
    <div class="overflow-hidden"
         style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
        <div class="h-1 w-full" style="background: linear-gradient(90deg, #7c3aed, #a78bfa, #c4b5fd);"></div>
        <div class="px-7 py-6 flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white leading-snug"
                    style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                    Mock Exams
                </h1>
                <p class="text-slate-400 mt-2 text-sm">
                    Create and manage mock examinations for your students.
                </p>
            </div>
            <div class="flex items-center gap-2 mt-1">
                <a href="{{ route('mock-exams.templates.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-300 border border-slate-600 hover:border-slate-400 transition-all"
                   style="border-radius: 2px;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                    </svg>
                    Templates
                </a>
                <a href="{{ route('mock-exams.grade-scales.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-300 border border-slate-600 hover:border-slate-400 transition-all"
                   style="border-radius: 2px;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                    Grading Scales
                </a>
                <a href="{{ route('mock-exams.create') }}"
                   class="inline-flex items-center gap-2 px-5 py-2 text-xs font-semibold text-white transition-all"
                   style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.4);">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Mock Exam
                </a>
            </div>
        </div>
    </div>

    {{-- ── FLASH MESSAGES ── --}}
    @if(session('success'))
        <div class="px-5 py-3 text-sm text-emerald-800 dark:text-emerald-200 flex items-center gap-2"
             style="border-radius: 2px; background: #f0fdf4; border: 1px solid #bbf7d0;">
            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {!! session('success') !!}
        </div>
    @endif

    {{-- ── EXAM LIST ── --}}
    @forelse($exams as $exam)
        <x-ui.card variant="default" :shadow="true">
            <x-ui.card-header :title="$exam->title" accent="primary">
                <x-slot:actions>
                    <div class="flex items-center gap-2">
                        {{-- Delivery badge --}}
                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold border"
                              style="border-radius: 2px;
                                     {{ $exam->delivery_type === 'print'
                                         ? 'color:#1e40af; background:#eff6ff; border-color:#bfdbfe;'
                                         : 'color:#4338ca; background:#eef2ff; border-color:#c7d2fe;' }}">
                            {{ ucfirst($exam->delivery_type) }}
                        </span>
                        {{-- Status badge --}}
                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold border"
                              style="border-radius: 2px;
                                     {{ $exam->status === 'published'
                                         ? 'color:#065f46; background:#ecfdf5; border-color:#6ee7b7;'
                                         : ($exam->status === 'closed'
                                             ? 'color:#374151; background:#f9fafb; border-color:#d1d5db;'
                                             : 'color:#92400e; background:#fffbeb; border-color:#fde68a;') }}">
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
                        ['label' => 'Delivery',       'value' => ucfirst($exam->delivery_type), 'mono' => false],
                        ['label' => 'Submissions',    'value' => $exam->submissions_count,       'mono' => false],
                    ] as $metric)
                        <div class="bg-white dark:bg-slate-900 px-4 py-4 flex flex-col items-center justify-center text-center"
                             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                            <p class="text-slate-500 dark:text-slate-400 uppercase mb-1"
                               style="font-size: 10px; letter-spacing: 0.12em;">
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
                    {{-- Schedule info --}}
                    @if($exam->starts_at)
                        <div class="flex items-center gap-3 text-sm">
                            <div class="w-9 h-9 flex-shrink-0 flex items-center justify-center"
                                 style="border-radius: 2px; background: linear-gradient(135deg, #1e293b, #334155);">
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

                    {{-- Actions --}}
                    <div class="flex flex-wrap gap-2">
                        <x-ui.button href="{{ route('mock-exams.show', $exam) }}" variant="secondary" size="sm" icon="eye">
                            Manage
                        </x-ui.button>

                        @if($exam->isPrint())
                            <x-ui.button href="{{ route('mock-exams.pdf', $exam) }}" variant="info" size="sm" icon="document-arrow-down">
                                Exam PDF
                            </x-ui.button>
                            <x-ui.button href="{{ route('mock-exams.pdf.answer-key', $exam) }}" variant="ghost" size="sm" icon="document-text">
                                Answer Key
                            </x-ui.button>
                        @else
                            <x-ui.button href="{{ route('mock-exams.results.index', $exam) }}" variant="ghost" size="sm" icon="chart-bar">
                                Results
                            </x-ui.button>
                        @endif

                        @if(!$exam->submissions()->exists())
                            <form method="POST" action="{{ route('mock-exams.destroy', $exam) }}"
                                  onsubmit="return confirm('Permanently delete this mock exam?')">
                                @csrf @method('DELETE')
                                <x-ui.button type="submit" variant="danger" size="sm" icon="trash"></x-ui.button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </x-ui.card>
    @empty
        <div class="bg-white dark:bg-slate-900 text-center py-16"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="flex flex-col items-center">
                <div class="w-16 h-16 flex items-center justify-center mb-4"
                     style="border-radius: 2px; background: linear-gradient(135deg, #64748b, #94a3b8);">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-lg font-semibold text-slate-900 dark:text-white">No mock exams yet</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Create your first mock exam to get started</p>
                <a href="{{ route('mock-exams.create') }}"
                   class="mt-6 inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white"
                   style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
                    Create First Mock Exam
                </a>
            </div>
        </div>
    @endforelse

    @if($exams->hasPages())
        <div class="bg-white dark:bg-slate-900 px-5 py-4"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            {{ $exams->links() }}
        </div>
    @endif

</div>
</x-layouts.app>