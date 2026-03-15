<x-layouts.app>
    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Public Assignments</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">View your submissions and results for public assignments</p>
                </div>
                <a href="{{ route('general-exams.join') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Join New Assignment
                </a>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg text-green-700 dark:text-green-300">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg text-red-700 dark:text-red-300">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Submissions List -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                @if($submissions->count() > 0)
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($submissions as $submission)
                            <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <h3 class="font-semibold text-gray-900 dark:text-white">
                                                {{ $submission->assignment->title ?? 'Assignment' }}
                                            </h3>
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                                @if($submission->status === 'completed') bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300
                                                @elseif($submission->status === 'in_progress') bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300
                                                @elseif($submission->status === 'graded') bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300
                                                @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300
                                                @endif">
                                                {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                                            </span>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                {{ $submission->created_at->format('M d, Y') }}
                                            </span>
                                            @if($submission->submitted_at)
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Submitted {{ $submission->submitted_at->diffForHumans() }}
                                                </span>
                                            @endif
                                            <span>Attempt #{{ $submission->attempt_number ?? 1 }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        @if($submission->score !== null)
                                            <div class="text-right">
                                                <div class="text-2xl font-bold {{ $submission->score >= 70 ? 'text-green-600' : ($submission->score >= 50 ? 'text-amber-600' : 'text-red-600') }}">
                                                    {{ number_format($submission->score, 1) }}%
                                                </div>
                                                <div class="text-xs text-gray-500">Score</div>
                                            </div>
                                        @endif
                                        @if($submission->status === 'in_progress')
                                            <a href="{{ route('general-exams.take', $submission) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                Continue
                                            </a>
                                        @elseif($submission->status === 'completed' || $submission->status === 'graded')
                                            <a href="{{ route('student.general-exams.result', $submission) }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                View Result
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($submissions->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                            {{ $submissions->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No submissions yet</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">You haven't taken any public assignments yet.</p>
                        <a href="{{ route('general-exams.join') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">
                            Join an Assignment
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
