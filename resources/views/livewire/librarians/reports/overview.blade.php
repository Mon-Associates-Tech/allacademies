<!-- Overview Report -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Total Books -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Books</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $reportData['total_books'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Total Copies -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Copies</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $reportData['total_copies'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Available Copies -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Available Copies</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $reportData['available_copies'] ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Activity Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="text-center">
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $reportData['total_borrows'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Total Borrows</div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="text-center">
            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $reportData['total_returns'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Total Returns</div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="text-center">
            <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $reportData['overdue_books'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Overdue Books</div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="text-center">
            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">${{ number_format($reportData['late_fees_collected'] ?? 0, 2) }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Late Fees Collected</div>
        </div>
    </div>
</div>

<!-- Daily Activity Chart -->
@if (isset($chartData['daily_activity']) && !empty($chartData['daily_activity']))
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Daily Activity</h3>
        <div class="h-64 flex items-end justify-between space-x-1">
            @foreach (array_slice($chartData['daily_activity'], -14) as $day)
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full flex flex-col items-center space-y-1">
                        <div class="w-full bg-blue-200 dark:bg-blue-800 rounded-t" style="height: {{ max(4, ($day['borrows'] ?? 0) * 8) }}px"></div>
                        <div class="w-full bg-green-200 dark:bg-green-800 rounded-b" style="height: {{ max(4, ($day['returns'] ?? 0) * 8) }}px"></div>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-2 rotate-45">
                        {{ \Carbon\Carbon::parse($day['date'])->format('M j') }}
                    </div>
                </div>
            @endforeach
        </div>
        <div class="flex justify-center mt-4 space-x-4">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-blue-200 dark:bg-blue-800 rounded mr-2"></div>
                <span class="text-sm text-gray-600 dark:text-gray-400">Borrows</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-green-200 dark:bg-green-800 rounded mr-2"></div>
                <span class="text-sm text-gray-600 dark:text-gray-400">Returns</span>
            </div>
        </div>
    </div>
@endif
