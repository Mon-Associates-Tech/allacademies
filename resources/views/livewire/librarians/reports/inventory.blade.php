<!-- Inventory Report -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="text-center">
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $reportData['total_books'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Total Books</div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="text-center">
            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $reportData['total_copies'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Total Copies</div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="text-center">
            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $reportData['available_copies'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Available Copies</div>
        </div>
    </div>
</div>

<!-- Status Breakdown -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="text-center">
            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $reportData['borrowed_copies'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Borrowed</div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="text-center">
            <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $reportData['damaged_copies'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Damaged</div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="text-center">
            <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $reportData['lost_copies'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Lost</div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="text-center">
            <div class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $reportData['out_of_stock_books'] ?? 0 }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Out of Stock</div>
        </div>
    </div>
</div>

<!-- Books by Category -->
@if (isset($reportData['books_by_category']) && !empty($reportData['books_by_category']))
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Books by Category</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Books</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Copies</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Available</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($reportData['books_by_category'] as $category)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $category['category'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $category['book_count'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $category['total_copies'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $category['available_copies'] }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

<!-- Low Stock Alert -->
@if (($reportData['low_stock_books'] ?? 0) > 0)
    <div class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-xl p-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Low Stock Alert</h3>
                <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                    {{ $reportData['low_stock_books'] }} books have 2 or fewer copies available. Consider ordering more copies.
                </p>
            </div>
        </div>
    </div>
@endif
