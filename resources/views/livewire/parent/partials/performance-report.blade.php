<!-- Summary Statistics -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
            {{ $data['summary']['total_assignments'] }}
        </div>
        <div class="text-sm text-gray-600 dark:text-gray-400">Total Assignments</div>
    </div>
    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
        <div class="text-2xl font-bold text-green-600 dark:text-green-400">
            {{ number_format($data['summary']['average_score'], 1) }}%
        </div>
        <div class="text-sm text-gray-600 dark:text-gray-400">Average Score</div>
    </div>
    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
            {{ $data['summary']['passed_count'] }}
        </div>
        <div class="text-sm text-gray-600 dark:text-gray-400">Passed</div>
    </div>
    <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
        <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">
            {{ number_format($data['summary']['pass_rate'], 1) }}%
        </div>
        <div class="text-sm text-gray-600 dark:text-gray-400">Pass Rate</div>
    </div>
</div>

<!-- Subject Breakdown -->
@if($data['subject_breakdown']->isNotEmpty())
    <div class="mb-8">
        <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Subject Breakdown</h4>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Subject</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Count</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Average</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Passed</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Failed</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($data['subject_breakdown'] as $breakdown)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $breakdown['subject']->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $breakdown['count'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ number_format($breakdown['average'], 1) }}%
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 dark:text-green-400">
                            {{ $breakdown['passed'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 dark:text-red-400">
                            {{ $breakdown['failed'] }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
