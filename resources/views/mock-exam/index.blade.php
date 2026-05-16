<x-layouts.app :title="'Mock Exams'">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Mock Exams</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Create and manage mock examinations for your students.</p>
        </div>
        <div class="flex items-center gap-2">
            <x-ui.button href="{{ route('mock-exams.grade-scales.index') }}" variant="ghost" icon="adjustments-horizontal">
                Grading Scales
            </x-ui.button>
            <x-ui.button href="{{ route('mock-exams.create') }}" variant="primary" icon="plus">
                New Mock Exam
            </x-ui.button>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-4 px-4 py-3 rounded-[2px] bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400">
            {{ session('success') }}
        </div>
    @endif

    {{-- Exam grid --}}
    @forelse($exams as $exam)
        <x-ui.card class="mb-4">
            <div class="p-5 flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <a href="{{ route('mock-exams.show', $exam) }}"
                           class="font-semibold text-slate-900 dark:text-white hover:text-violet-600 dark:hover:text-violet-400 transition-colors text-sm">
                            {{ $exam->title }}
                        </a>

                        {{-- Status badge --}}
                        @php
                            $badgeClass = match($exam->status) {
                                'published' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                'closed'    => 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400',
                                default     => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                            };
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                            {{ ucfirst($exam->status) }}
                        </span>

                        {{-- Delivery badge --}}
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                            {{ ucfirst($exam->delivery_type) }}
                        </span>
                    </div>

                    @if($exam->description)
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-1">{{ $exam->description }}</p>
                    @endif

                    <div class="flex items-center gap-4 mt-2">
                        <span class="text-xs text-slate-400 dark:text-slate-500">
                            <span class="font-medium text-slate-600 dark:text-slate-300">{{ $exam->subject_exams_count }}</span> subject exam(s)
                        </span>
                        <span class="text-xs text-slate-400 dark:text-slate-500">
                            <span class="font-medium text-slate-600 dark:text-slate-300">{{ $exam->submissions_count }}</span> submission(s)
                        </span>
                        @if($exam->starts_at)
                            <span class="text-xs text-slate-400 dark:text-slate-500">
                                Starts {{ $exam->starts_at->format('M d, Y H:i') }}
                            </span>
                        @endif
                        @if($exam->access_code && $exam->isOnline())
                            <span class="text-xs font-mono bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded text-slate-600 dark:text-slate-300">
                                {{ $exam->access_code }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-1 shrink-0">
                    <x-ui.button href="{{ route('mock-exams.show', $exam) }}" variant="ghost" size="sm" icon="eye">
                        View
                    </x-ui.button>

                    @if($exam->isPrint())
                        <x-ui.button href="{{ route('mock-exams.pdf.exam', $exam) }}" variant="ghost" size="sm" icon="document-arrow-down">
                            PDF
                        </x-ui.button>
                    @endif

                    <form method="POST" action="{{ route('mock-exams.destroy', $exam) }}"
                          onsubmit="return confirm('Delete this mock exam?')">
                        @csrf @method('DELETE')
                        <x-ui.button type="submit" variant="ghost" size="sm" icon="trash">
                        </x-ui.button>
                    </form>
                </div>
            </div>
        </x-ui.card>
    @empty
        <x-ui.card>
            <div class="py-16 text-center">
                <x-heroicon-o-document-text class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-600 mb-3" />
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">No mock exams yet</p>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Create your first mock exam to get started.</p>
                <div class="mt-4">
                    <x-ui.button href="{{ route('mock-exams.create') }}" variant="primary" icon="plus">
                        New Mock Exam
                    </x-ui.button>
                </div>
            </div>
        </x-ui.card>
    @endforelse

    <div class="mt-4">{{ $exams->links() }}</div>

</x-layouts.app>
