<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">My Report Cards</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($reportCards as $reportCard)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-4 py-3">
                    <h3 class="text-lg font-medium text-white">{{ $reportCard->term }}</h3>
                    <p class="text-sm text-blue-100">{{ $reportCard->configuration?->academicPeriod?->academic_year ?? 'N/A' }}</p>
                </div>

                <div class="p-4">
                    <div class="space-y-3">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Level</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $reportCard->configuration?->academicLevel?->name ?? 'N/A' }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Subjects</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $reportCard->grades->count() }} subjects</dd>
                        </div>

                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Average Score</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ number_format($reportCard->grades->avg('total_score'), 2) }}%
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Status</dt>
                            <dd>
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                    {{ $reportCard->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                    {{ $reportCard->status === 'published' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}">
                                    {{ ucfirst($reportCard->status) }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Generated</dt>
                            <dd class="text-xs text-gray-600 dark:text-gray-400">{{ $reportCard->generated_at->format('M d, Y') }}</dd>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button wire:click="downloadReportCard({{ $reportCard->id }})"
                                class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download PDF
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white dark:bg-gray-800 shadow rounded-lg p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No report cards available</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Your report cards will appear here when they are published.</p>
            </div>
        @endforelse
    </div>
</div>
