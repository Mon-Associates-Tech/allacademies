<x-layouts.exam>
<x-examination-hub.navigation active="manage" />

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-1">
                <a href="{{ route('examination-hub.exams.show', $exam) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">{{ $exam->title }}</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span>Proctoring</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Proctoring Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $summaries->count() }} submission(s) with recorded violations.
            </p>
        </div>
        <a href="{{ route('examination-hub.submissions.index', $exam) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            ← All Submissions
        </a>
    </div>

    @if($summaries->isEmpty())
        <div class="rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700 p-16 text-center">
            <svg class="mx-auto w-12 h-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <p class="text-gray-500 dark:text-gray-400">No violations recorded for this exam.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($summaries as $row)
                @php
                    $submission = $row['submission'];
                    $proctoring = $row['proctoring'];
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-xl border {{ $proctoring['flagged'] ? 'border-red-300 dark:border-red-700' : 'border-gray-200 dark:border-gray-700' }} shadow-sm overflow-hidden">
                    <div class="flex items-center gap-4 px-5 py-4">

                        {{-- Flag indicator --}}
                        <div class="shrink-0">
                            @if($proctoring['flagged'])
                                <span class="inline-flex items-center justify-center w-10 h-10 bg-red-100 dark:bg-red-900/40 rounded-lg">
                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 7l2.55 2.4A1 1 0 0116 11H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd"/>
                                    </svg>
                                </span>
                            @else
                                <span class="inline-flex items-center justify-center w-10 h-10 bg-yellow-100 dark:bg-yellow-900/40 rounded-lg">
                                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                    </svg>
                                </span>
                            @endif
                        </div>

                        {{-- Participant info --}}
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 dark:text-white truncate">
                                {{ $submission->participant_name ?? 'Unknown' }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                {{ $submission->participant_email ?? '—' }}
                            </p>
                        </div>

                        {{-- Severity counts --}}
                        <div class="flex items-center gap-3 shrink-0">
                            @if($proctoring['high'] > 0)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400 px-2.5 py-1 rounded-full">
                                    ● High: {{ $proctoring['high'] }}
                                </span>
                            @endif
                            @if($proctoring['medium'] > 0)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400 px-2.5 py-1 rounded-full">
                                    ● Med: {{ $proctoring['medium'] }}
                                </span>
                            @endif
                            @if($proctoring['low'] > 0)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400 px-2.5 py-1 rounded-full">
                                    ● Low: {{ $proctoring['low'] }}
                                </span>
                            @endif
                            <a href="{{ route('examination-hub.proctoring.show', [$exam, $submission]) }}"
                               class="ml-2 px-3 py-1.5 text-xs font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                View Logs
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
</x-layouts.exam>
