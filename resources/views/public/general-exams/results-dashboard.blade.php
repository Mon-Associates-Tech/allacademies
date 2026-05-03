<x-layouts.general-exam title="My Exam Results" pageName="Results Dashboard">
    <div class="py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">
            @if(($participant ?? null) && ($needsEmail ?? false))
                <div class="max-w-md mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 border border-gray-200 dark:border-gray-700">
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Confirm your email</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Enter the email you used for this assignment to access your full results dashboard.</p>
                    <form method="GET" action="{{ route('general-exams.results.dashboard', ['token' => $token]) }}">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                        <input type="email" name="email" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white mb-4">
                        <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">Continue</button>
                    </form>
                </div>
            @elseif(!($participant ?? null))
                <div class="text-center py-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Results Not Found</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">The result link is invalid or expired.</p>
                    <a href="{{ route('general-exams.join') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition-colors">
                        Join an Assignment
                    </a>
                </div>
            @else
                <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Results Dashboard</h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $participant->name }} ({{ $participant->email }})</p>
                    </div>
                    <form method="GET" action="{{ route('general-exams.results.dashboard', ['token' => $token]) }}" class="flex flex-col sm:flex-row gap-2">
                        <select name="subject" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white">
                            <option value="">All subjects</option>
                            @foreach($subjectOptions as $subject)
                                <option value="{{ $subject['id'] }}" @selected($selectedSubject === $subject['id'])>{{ $subject['name'] }}</option>
                            @endforeach
                        </select>
                        <select name="assigner" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white">
                            <option value="">All assigners</option>
                            @foreach($assignerOptions as $assigner)
                                <option value="{{ $assigner['id'] }}" @selected($selectedAssigner === $assigner['id'])>{{ $assigner['name'] }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">Filter</button>
                    </form>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Submissions</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['total_submissions'] ?? 0 }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Released</div>
                        <div class="text-2xl font-bold text-green-600">{{ $summary['results_released'] ?? 0 }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Average %</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($summary['average_percentage'] ?? 0, 1) }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Best %</div>
                        <div class="text-2xl font-bold text-indigo-600">{{ number_format($summary['best_percentage'] ?? 0, 1) }}</div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    @if($submissions->isEmpty())
                        <div class="p-8 text-center text-gray-500 dark:text-gray-400">No submissions found for the selected filters.</div>
                    @else
                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($submissions as $submission)
                                @php
                                    $subjects = optional($submission->assignment?->subscription)->subjects ?? collect();
                                @endphp
                                <div class="p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                                    <div>
                                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $submission->assignment->title ?? 'Assignment' }}</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $submission->assignment?->user?->name ?? 'Unknown assigner' }}
                                            • {{ $submission->submitted_at?->format('M d, Y H:i') ?? 'N/A' }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            @if($subjects->isEmpty())
                                                No subject assigned
                                            @else
                                                {{ $subjects->pluck('name')->join(', ') }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div class="text-right">
                                            <div class="text-xl font-bold {{ ($submission->percentage ?? 0) >= 70 ? 'text-green-600' : (($submission->percentage ?? 0) >= 50 ? 'text-amber-600' : 'text-red-600') }}">
                                                {{ number_format((float) ($submission->percentage ?? 0), 1) }}%
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ number_format((float) ($submission->score ?? 0), 1) }} / {{ number_format((float) ($submission->total_marks ?? 0), 1) }}
                                            </div>
                                        </div>
                                        @if($submission->canViewResults())
                                            <a href="{{ route('general-exams.results.submission', ['token' => $token, 'submission' => $submission->id]) }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                                                View Result
                                            </a>
                                        @else
                                            <span class="px-3 py-1 text-xs rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">Pending release</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-layouts.general-exam>
