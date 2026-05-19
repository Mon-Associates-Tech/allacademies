<x-layouts.app >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7 font-sans">

        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden rounded-[2px] bg-gradient-to-br from-slate-900 to-slate-800 shadow-xl">
            <div class="h-1 w-full bg-gradient-to-r from-violet-600 via-violet-400 to-indigo-300"></div>
            <div class="px-7 py-6 flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-white leading-snug tracking-tight font-serif">
                        Results – {{ $mockExam->title }}
                    </h1>
                    <p class="text-slate-400 mt-2 text-sm">
                        View and manage submission results for this mock examination.
                    </p>
                </div>
                <div class="flex items-center gap-2 mt-1">
                    <x-ui.button
                        href="{{ route('mock-exams.show', $mockExam) }}"
                        variant="ghost"
                        size="sm"
                        icon="arrow-left"
                    >
                        Back to Exam
                    </x-ui.button>

                    @if($mockExam->result_visibility === 'manual_release' && !$mockExam->results_released)
                        <form method="POST" action="{{ route('mock-exams.results.release', $mockExam) }}">
                            @csrf
                            <x-ui.button type="submit" variant="success" size="sm" icon="eye">
                                Release Results
                            </x-ui.button>
                        </form>
                    @endif

                    @if($mockExam->results_released)
                        @if($mockExam->results_hidden_after_release)
                            {{-- Results are hidden - show unhide button --}}
                            <form method="POST" action="{{ route('mock-exams.results.unhide', $mockExam) }}">
                                @csrf
                                <x-ui.button type="submit" variant="primary" size="sm" icon="eye">
                                    Unhide Results
                                </x-ui.button>
                            </form>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-[2px] text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                                Hidden from Participants
                            </span>
                        @else
                            {{-- Results are visible - show hide button --}}
                            <form method="POST" action="{{ route('mock-exams.results.hide', $mockExam) }}" onsubmit="return confirm('Are you sure you want to hide results from participants? They will no longer be able to view their results.')">
                                @csrf
                                <x-ui.button type="submit" variant="danger" size="sm" icon="eye-slash">
                                    Hide Results
                                </x-ui.button>
                            </form>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-[2px] text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Visible to Participants
                            </span>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- ── FLASH MESSAGES ── --}}
        @if(session('success'))
            <x-ui.card variant="accent" accent="success" shadow="true">
                <div class="px-5 py-3 flex items-center gap-2">
                    <x-heroicon-o-check-circle class="w-4 h-4 text-emerald-500 shrink-0"/>
                    <p class="text-sm text-emerald-800 dark:text-emerald-200">{!! session('success') !!}</p>
                </div>
            </x-ui.card>
        @endif

        {{-- ── STATS ROW ── --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            @foreach([
                ['label' => 'Total', 'value' => $stats['total'], 'color' => 'slate'],
                ['label' => 'Submitted', 'value' => $stats['submitted'], 'color' => 'blue'],
                ['label' => 'Graded', 'value' => $stats['graded'], 'color' => 'emerald'],
                ['label' => 'Needs Review', 'value' => $stats['needs_review'], 'color' => 'amber'],
                ['label' => 'Avg %', 'value' => number_format($stats['avg_percentage'], 1).'%', 'color' => 'violet'],
            ] as $stat)
                <x-ui.card variant="default" shadow="true">
                    <div class="p-4 text-center">
                        <p class="text-xl font-bold text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400">
                            {{ $stat['value'] }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 uppercase tracking-widest text-[10px]">
                            {{ $stat['label'] }}
                        </p>
                    </div>
                </x-ui.card>
            @endforeach
        </div>

        {{-- ── SUBMISSIONS TABLE CARD ── --}}
        <x-ui.card variant="default" shadow="true">
            <x-ui.card-header title="Submissions" accent="primary" />

            @if($submissions->isEmpty())
                <div class="p-10 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center rounded-[2px] bg-gradient-to-br from-slate-500 to-slate-400">
                        <x-heroicon-o-document-text class="w-8 h-8 text-white"/>
                    </div>
                    <p class="text-lg font-semibold text-slate-900 dark:text-white">No submissions yet</p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Participants will appear here once they submit their responses.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                                <th class="text-left px-5 py-3 text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-widest">Participant</th>
                                <th class="text-left px-5 py-3 text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-widest">Submitted</th>
                                <th class="text-left px-5 py-3 text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-widest">Score</th>
                                <th class="text-left px-5 py-3 text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-widest">%</th>
                                <th class="text-left px-5 py-3 text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-widest">Grade</th>
                                <th class="text-left px-5 py-3 text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-widest">Status</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                            @foreach($submissions as $sub)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="px-5 py-3">
                                        <p class="font-medium text-slate-800 dark:text-slate-200">{{ $sub->participant_name }}</p>
                                        <p class="text-xs text-slate-400 dark:text-slate-500">{{ $sub->participant_email }}</p>
                                    </td>
                                    <td class="px-5 py-3 text-slate-500 dark:text-slate-400 text-xs">
                                        {{ $sub->submitted_at?->format('M d, Y H:i') ?? '—' }}
                                    </td>
                                    <td class="px-5 py-3 font-medium text-slate-700 dark:text-slate-300">
                                        {{ number_format($sub->score ?? 0, 1) }} / {{ number_format($sub->total_marks ?? 0, 1) }}
                                    </td>
                                    <td class="px-5 py-3 text-slate-600 dark:text-slate-300">
                                        {{ number_format($sub->percentage ?? 0, 1) }}%
                                    </td>
                                    <td class="px-5 py-3">
                                        @if($sub->grade)
                                            <span class="px-2 py-0.5 rounded-[2px] text-xs font-bold bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400 border border-violet-200 dark:border-violet-800">
                                                {{ $sub->grade }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 dark:text-slate-500 text-xs">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="text-xs text-slate-500 dark:text-slate-400">
                                            {{ str_replace('_', ' ', ucfirst($sub->status)) }}
                                        </span>
                                        @if($sub->requires_manual_review)
                                            <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-[2px] text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                                Review
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <x-ui.button
                                            href="{{ route('mock-exams.results.show', [$mockExam, $sub]) }}"
                                            variant="ghost"
                                            size="sm"
                                        >
                                            View →
                                        </x-ui.button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($submissions->hasPages())
                    <div class="px-5 py-3 border-t border-slate-200 dark:border-slate-800">
                        {{ $submissions->links() }}
                    </div>
                @endif
            @endif
        </x-ui.card>

    </div>
</x-layouts.app>