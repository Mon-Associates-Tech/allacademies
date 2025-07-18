<!-- Returns Report -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="text-center">
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $reportData['total_returns'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Total Returns</div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="text-center">
            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $reportData['on_time_returns'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">On Time Returns</div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="text-center">
            <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $reportData['late_returns'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Late Returns</div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="text-center">
            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">${{ number_format($reportData['total_late_fees'] ?? 0, 2) }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Late Fees</div>
        </div>
    </div>
</div>

<!-- Additional Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Return Statistics</h3>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Average Days Borrowed:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $reportData['average_days_borrowed'] ?? 0 }} days</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Damaged Returns:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $reportData['damaged_returns'] ?? 0 }}</span>
            </div>
            @if (isset($reportData['condition_breakdown']))
                @foreach ($reportData['condition_breakdown'] as $condition => $count)
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ ucfirst($condition) }}:</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $count }}</span>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Return Rate</h3>
        <div class="h-32 flex items-end justify-center">
            @php
                $total = ($reportData['total_returns'] ?? 0);
                $onTimePercent = $total > 0 ? round(($reportData['on_time_returns'] ?? 0) / $total * 100) : 0;
                $latePercent = 100 - $onTimePercent;
            @endphp
            <div class="w-32 h-32 relative">
                <div class="absolute inset-0 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
                <div class="absolute inset-0 bg-green-500 rounded-full" style="clip-path: polygon(50% 50%, 50% 0%, {{ 50 + $onTimePercent/2 }}% 0%, 100% 50%, 50% 50%)"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $onTimePercent }}%</span>
                </div>
            </div>
        </div>
        <div class="flex justify-center mt-4 space-x-4">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                <span class="text-sm text-gray-600 dark:text-gray-400">On Time</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-red-500 rounded mr-2"></div>
                <span class="text-sm text-gray-600 dark:text-gray-400">Late</span>
            </div>
        </div>
    </div>
</div>
