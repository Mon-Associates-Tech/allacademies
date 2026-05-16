<x-layouts.app :title="'Results – ' . $mockExam->title">

    <div class="flex items-center gap-3 mb-6">
        <x-ui.button href="{{ route('mock-exams.show', $mockExam) }}" variant="ghost" size="sm" icon="arrow-left">Back</x-ui.button>
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Results</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ $mockExam->title }}</p>
        </div>
        @if($mockExam->result_visibility === 'manual_release' && !$mockExam->results_released)
            <div class="ml-auto">
                <form method="POST" action="{{ route('mock-exams.results.release', $mockExam) }}">
                    @csrf
                    <x-ui.button type="submit" variant="success" icon="eye">Release Results</x-ui.button>
                </form>
            </div>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 rounded-[2px] bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Stats row --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
        @foreach([
            ['Total',       $stats['total'],          'slate'],
            ['Submitted',   $stats['submitted'],       'blue'],
            ['Graded',      $stats['graded'],          'emerald'],
            ['Needs Review',$stats['needs_review'],    'amber'],
            ['Avg %',       number_format($stats['avg_percentage'], 1).'%', 'violet'],
        ] as [$label, $value, $color])
        <x-ui.card>
            <div class="p-4 text-center">
                <p class="text-xl font-bold text-{{ $color }}-600 dark:text-{{ $color }}-400">{{ $value }}</p>
                <p class="text-xs text-slate-500 mt-0.5">{{ $label }}</p>
            </div>
        </x-ui.card>
        @endforeach
    </div>

    <x-ui.card>
        <x-ui.card-header title="Submissions" accent="primary" />
        @if($submissions->isEmpty())
            <div class="py-10 text-center text-sm text-slate-400">No submissions yet.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800">
                            <th class="text-left px-5 py-3 text-xs font-medium text-slate-500 uppercase">Participant</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-slate-500 uppercase">Submitted</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-slate-500 uppercase">Score</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-slate-500 uppercase">%</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-slate-500 uppercase">Grade</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-slate-500 uppercase">Status</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                        @foreach($submissions as $sub)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30">
                                <td class="px-5 py-3">
                                    <p class="font-medium text-slate-800 dark:text-slate-200">{{ $sub->participant_name }}</p>
                                    <p class="text-xs text-slate-400">{{ $sub->participant_email }}</p>
                                </td>
                                <td class="px-5 py-3 text-slate-500 text-xs">{{ $sub->submitted_at?->format('M d, Y H:i') ?? '—' }}</td>
                                <td class="px-5 py-3 font-medium text-slate-700 dark:text-slate-300">
                                    {{ number_format($sub->score ?? 0, 1) }} / {{ number_format($sub->total_marks ?? 0, 1) }}
                                </td>
                                <td class="px-5 py-3 text-slate-600 dark:text-slate-300">{{ number_format($sub->percentage ?? 0, 1) }}%</td>
                                <td class="px-5 py-3">
                                    @if($sub->grade)
                                        <span class="px-2 py-0.5 rounded text-xs font-bold bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400">{{ $sub->grade }}</span>
                                    @else <span class="text-slate-400 text-xs">—</span> @endif
                                </td>
                                <td class="px-5 py-3">
                                    <span class="text-xs text-slate-500">{{ str_replace('_', ' ', ucfirst($sub->status)) }}</span>
                                    @if($sub->requires_manual_review)
                                        <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-700">Review</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <a href="{{ route('mock-exams.results.show', [$mockExam, $sub]) }}"
                                       class="text-xs text-violet-600 hover:text-violet-800 font-medium">View →</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3 border-t border-slate-100 dark:border-slate-800">
                {{ $submissions->links() }}
            </div>
        @endif
    </x-ui.card>

</x-layouts.app>
