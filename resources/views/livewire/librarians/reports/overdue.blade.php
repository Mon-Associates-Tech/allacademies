<!-- Overdue Report -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="text-center">
            <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $reportData['total_overdue'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Total Overdue</div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="text-center">
            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $reportData['recent_overdue'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Recent (1-7 days)</div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="text-center">
            <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $reportData['moderate_overdue'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Moderate (8-30 days)</div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="text-center">
            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $reportData['severe_overdue'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Severe (30+ days)</div>
        </div>
    </div>
</div>

<!-- Potential Fees -->
<div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Potential Late Fees</h3>
    <div class="text-center">
        <div class="text-4xl font-bold text-green-600 dark:text-green-400">${{ number_format($reportData['total_potential_fees'] ?? 0, 2) }}</div>
        <div class="text-sm text-gray-600 dark:text-gray-400">Total Potential Collections</div>
    </div>
</div>

<!-- Most Overdue Students -->
@if (isset($reportData['most_overdue_students']) && !empty($reportData['most_overdue_students']))
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Most Overdue Students</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Overdue Books</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Days Overdue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Potential Fees</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($reportData['most_overdue_students'] as $data)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $data['student']['user']['name'] ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $data['count'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $data['total_days_overdue'] }} days
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                ${{ number_format($data['total_days_overdue'] * 0.50, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
