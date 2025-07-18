<!-- Borrowing Report -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="text-center">
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $reportData['total_borrows'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Total Borrows</div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="text-center">
            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $reportData['unique_students'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Unique Students</div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="text-center">
            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $reportData['unique_books'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Unique Books</div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="text-center">
            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $reportData['average_per_day'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Avg. Per Day</div>
        </div>
    </div>
</div>

<!-- Most Active Students -->
@if (isset($reportData['most_active_students']) && !empty($reportData['most_active_students']))
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Most Active Students</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Borrows</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($reportData['most_active_students'] as $index => $data)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $data['student']['user']['name'] ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $data['count'] }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
