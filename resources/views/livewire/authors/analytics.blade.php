<div class="space-y-6">
    <!-- Page Header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Book Analytics Dashboard</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Track your book performance and audience insights</p>
        </div>

        <!-- Export Options -->
        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
            <button wire:click="exportAnalytics('csv')" class="btn bg-green-500 hover:bg-green-600 text-white">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export CSV
            </button>
            <button wire:click="exportAnalytics('pdf')" class="btn bg-blue-500 hover:bg-blue-600 text-white">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export PDF
            </button>
            <button wire:click="exportAnalytics('excel')" class="btn bg-purple-500 hover:bg-purple-600 text-white">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                Export Excel
            </button>
        </div>
    </div>

    <!-- Filter Controls -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Time Period Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Time Period</label>
                <select wire:model.live="period" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-violet-500 dark:bg-gray-700 dark:text-white">
                    <option value="7">Last 7 Days</option>
                    <option value="30">Last 30 Days</option>
                    <option value="90">Last 3 Months</option>
                    <option value="180">Last 6 Months</option>
                    <option value="365">Last Year</option>
                </select>
            </div>

            <!-- Book Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Book</label>
                <select wire:model.live="selectedBook" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-violet-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Books</option>
                    @foreach($books as $book)
                        <option value="{{ $book->id }}">{{ $book->title }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Category Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                <select wire:model.live="selectedCategory" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-violet-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Refresh Button -->
            <div class="flex items-end">
                <button wire:click="loadAnalytics" class="btn bg-violet-500 hover:bg-violet-600 text-white w-full">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh Data
                </button>
            </div>
        </div>
    </div>

    <!-- Performance Summary Alert -->
    @if(isset($analytics['performance_summary']))
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-800 dark:text-blue-200">
                        <span class="font-medium">{{ $analytics['performance_summary']['message'] }}</span>
                        {{ $analytics['performance_summary']['details'] }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Key Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Views -->
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 rounded-lg shadow-sm p-6 border border-blue-200 dark:border-blue-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">Total Views</p>
                    <p class="text-3xl font-bold text-blue-900 dark:text-white">{{ number_format($analytics['total_views'] ?? 0) }}</p>
                    <p class="text-xs text-blue-500 dark:text-blue-300 mt-1">
                        @if(isset($analytics['views_change']))
                            <span class="inline-flex items-center {{ $analytics['views_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    @if($analytics['views_change'] >= 0)
                                        <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    @else
                                        <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    @endif
                                </svg>
                                {{ abs($analytics['views_change']) }}% vs last period
                            </span>
                        @else
                            <span class="inline-flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                </svg>
                                Book visibility
                            </span>
                        @endif
                    </p>
                </div>
                <div class="p-3 bg-blue-500 dark:bg-blue-600 rounded-full">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Subscriptions -->
        <div class="bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 rounded-lg shadow-sm p-6 border border-green-200 dark:border-green-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-600 dark:text-green-400 font-medium">Subscriptions</p>
                    <p class="text-3xl font-bold text-green-900 dark:text-white">{{ number_format($analytics['total_subscriptions'] ?? 0) }}</p>
                    <p class="text-xs text-green-500 dark:text-green-300 mt-1">
                        @if(isset($analytics['subscriptions_change']))
                            <span class="inline-flex items-center {{ $analytics['subscriptions_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    @if($analytics['subscriptions_change'] >= 0)
                                        <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    @else
                                        <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    @endif
                                </svg>
                                {{ abs($analytics['subscriptions_change']) }}% vs last period
                            </span>
                        @else
                            <span class="inline-flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Active readers
                            </span>
                        @endif
                    </p>
                </div>
                <div class="p-3 bg-green-500 dark:bg-green-600 rounded-full">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Borrowings -->
        <div class="bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900 dark:to-purple-800 rounded-lg shadow-sm p-6 border border-purple-200 dark:border-purple-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-purple-600 dark:text-purple-400 font-medium">Borrowings</p>
                    <p class="text-3xl font-bold text-purple-900 dark:text-white">{{ number_format($analytics['total_borrowings'] ?? 0) }}</p>
                    <p class="text-xs text-purple-500 dark:text-purple-300 mt-1">
                        @if(isset($analytics['borrowings_change']))
                            <span class="inline-flex items-center {{ $analytics['borrowings_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    @if($analytics['borrowings_change'] >= 0)
                                        <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    @else
                                        <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    @endif
                                </svg>
                                {{ abs($analytics['borrowings_change']) }}% vs last period
                            </span>
                        @else
                            <span class="inline-flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Book loans
                            </span>
                        @endif
                    </p>
                </div>
                <div class="p-3 bg-purple-500 dark:bg-purple-600 rounded-full">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Revenue -->
        <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 dark:from-yellow-900 dark:to-yellow-800 rounded-lg shadow-sm p-6 border border-yellow-200 dark:border-yellow-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-yellow-600 dark:text-yellow-400 font-medium">Revenue</p>
                    <p class="text-3xl font-bold text-yellow-900 dark:text-yellow-100">GHS {{ number_format($analytics['revenue'] ?? 0, 2) }}</p>
                    <p class="text-xs text-yellow-500 dark:text-yellow-300 mt-1">
                        @if(isset($analytics['revenue_change']))
                            <span class="inline-flex items-center {{ $analytics['revenue_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    @if($analytics['revenue_change'] >= 0)
                                        <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    @else
                                        <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    @endif
                                </svg>
                                {{ abs($analytics['revenue_change']) }}% vs last period
                            </span>
                        @else
                            <span class="inline-flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Total earnings
                            </span>
                        @endif
                    </p>
                </div>
                <div class="p-3 bg-yellow-500 dark:bg-yellow-600 rounded-full">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Subscription Trends Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Subscription Trends</h3>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                        Daily Subscriptions
                    </div>
                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                        Target
                    </div>
                </div>
            </div>
            <div class="h-64 flex items-center justify-center">
                <canvas id="subscriptionChart" class="w-full h-full"></canvas>
            </div>
        </div>

        <!-- Revenue Trends Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Revenue Trends</h3>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                        Daily Revenue
                    </div>
                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                        Average
                    </div>
                </div>
            </div>
            <div class="h-64 flex items-center justify-center">
                <canvas id="revenueChart" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>

    <!-- New Analytics Section: Geographic Distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Geographic Distribution -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Geographic Distribution</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Reader locations</p>
            </div>
            <div class="p-6">
                @if(isset($analytics['geographic_data']) && $analytics['geographic_data']->count() > 0)
                    <div class="space-y-4">
                        @foreach($analytics['geographic_data'] as $location => $count)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $location }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-900 dark:text-gray-100 font-medium">{{ $count }}</span>
                                    <div class="w-16 bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($count / $analytics['geographic_data']->sum()) * 100 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">No geographic data available</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Reading Patterns -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Reading Patterns</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Peak reading hours</p>
            </div>
            <div class="p-6">
                @if(isset($analytics['reading_patterns']) && $analytics['reading_patterns']->count() > 0)
                    <div class="space-y-4">
                        @foreach($analytics['reading_patterns'] as $hour => $activity)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $hour }}:00</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-900 dark:text-gray-100 font-medium">{{ $activity }}%</span>
                                    <div class="w-16 bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                        <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $activity }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">No reading pattern data available</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Engagement Metrics -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Engagement Metrics</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Reader interaction</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-blue-100 dark:bg-blue-800 rounded-full">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Avg. Reading Time</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Per session</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $analytics['avg_reading_time'] ?? '0' }} min</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-green-100 dark:bg-green-800 rounded-full">
                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Completion Rate</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Books finished</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-green-600 dark:text-green-400">{{ $analytics['completion_rate'] ?? '0' }}%</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-purple-100 dark:bg-purple-800 rounded-full">
                                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Reader Retention</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Return rate</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-purple-600 dark:text-purple-400">{{ $analytics['retention_rate'] ?? '0' }}%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Performing Books -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Top Performing Books</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Books with highest engagement</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="p-6">
                @if(isset($analytics['top_performing_books']) && $analytics['top_performing_books']->count() > 0)
                    <div class="space-y-4">
                        @foreach($analytics['top_performing_books'] as $index => $book)
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-gradient-to-r from-violet-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                            {{ $index + 1 }}
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        @if($book->cover_image)
                                            <img class="w-12 h-12 object-cover rounded-lg" src="{{ Storage::url($book->cover_image) }}" alt="{{ $book->title }}">
                                        @else
                                            <div class="w-12 h-12 bg-gray-300 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $book->title }}</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $book->bookCategory->name ?? 'No category' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="flex items-center space-x-4">
                                        <div class="text-center">
                                            <div class="text-sm font-medium text-green-600 dark:text-green-400">
                                                {{ $book->subscriptions_count }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">subs</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-sm font-medium text-purple-600 dark:text-purple-400">
                                                {{ $book->borrowings_count }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">loans</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-sm font-medium text-yellow-600 dark:text-yellow-400">
                                                GHS {{ number_format($book->annual_subscription_fee, 0) }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">revenue</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">No performance data available</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Subscriber Demographics -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Subscriber Demographics</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Reader distribution by role</p>
            </div>
            <div class="p-6">
                @if(isset($analytics['subscriber_demographics']) && $analytics['subscriber_demographics']->count() > 0)
                    <div class="space-y-4">
                        @foreach($analytics['subscriber_demographics'] as $role => $count)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-r from-violet-500 to-purple-600 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">{{ $role }}</span>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ number_format(($count / $analytics['subscriber_demographics']->sum()) * 100, 1) }}% of total</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $count }}</span>
                                    <div class="w-20 bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                        <div class="bg-violet-500 h-2 rounded-full" style="width: {{ ($count / $analytics['subscriber_demographics']->sum()) * 100 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">No demographic data available</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Enhanced Quick Insights -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-6">Quick Insights & Recommendations</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="flex items-center mb-3">
                    <div class="p-2 bg-blue-500 rounded-full mr-3">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">Most Popular Genre</h4>
                </div>
                <p class="text-lg font-bold text-blue-900 dark:text-blue-100">{{ $analytics['most_popular_genre'] ?? 'Fiction' }}</p>
                <p class="text-xs text-blue-600 dark:text-blue-300">{{ $analytics['genre_percentage'] ?? '45' }}% of all subscriptions</p>
            </div>

            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
                <div class="flex items-center mb-3">
                    <div class="p-2 bg-green-500 rounded-full mr-3">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h4 class="text-sm font-medium text-green-800 dark:text-green-200">Average Revenue per Book</h4>
                </div>
                <p class="text-lg font-bold text-green-900 dark:text-green-100">
                    GHS {{ number_format(($analytics['revenue'] ?? 0) / max(1, $books->count()), 2) }}
                </p>
                <p class="text-xs text-green-600 dark:text-green-300">{{ $analytics['revenue_trend'] ?? 'Stable' }} trend</p>
            </div>

            <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg border border-purple-200 dark:border-purple-800">
                <div class="flex items-center mb-3">
                    <div class="p-2 bg-purple-500 rounded-full mr-3">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <h4 class="text-sm font-medium text-purple-800 dark:text-purple-200">Engagement Rate</h4>
                </div>
                <p class="text-lg font-bold text-purple-900 dark:text-purple-100">
                    {{ number_format((($analytics['total_subscriptions'] ?? 0) / max(1, $analytics['total_views'] ?? 1)) * 100, 1) }}%
                </p>
                <p class="text-xs text-purple-600 dark:text-purple-300">Views to subscriptions ratio</p>
            </div>
        </div>

        <!-- Recommendations -->
        @if(isset($analytics['recommendations']))
            <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-200 mb-2">📈 Recommendations</h4>
                <ul class="text-sm text-yellow-700 dark:text-yellow-300 space-y-1">
                    @foreach($analytics['recommendations'] as $recommendation)
                        <li class="flex items-start">
                            <svg class="w-4 h-4 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ $recommendation }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Convert Laravel collections to JavaScript arrays
            const subscriptionData = @json(isset($chartData['subscriptions']) ? $chartData['subscriptions']->toArray() : []);
            const revenueData = @json(isset($chartData['revenue']) ? $chartData['revenue']->toArray() : []);

            // Subscription Chart with enhanced design
            const subscriptionCtx = document.getElementById('subscriptionChart').getContext('2d');
            const subscriptionChart = new Chart(subscriptionCtx, {
                type: 'line',
                data: {
                    labels: Object.keys(subscriptionData),
                    datasets: [{
                        label: 'Subscriptions',
                        data: Object.values(subscriptionData),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgb(59, 130, 246)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5
                    }, {
                        label: 'Target',
                        data: Object.keys(subscriptionData).map(() => Math.max(...Object.values(subscriptionData)) * 0.8),
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        fill: false,
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: 'rgba(59, 130, 246, 0.5)',
                            borderWidth: 1
                        }
                    }
                }
            });

            // Revenue Chart with enhanced design
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            const revenueChart = new Chart(revenueCtx, {
                type: 'bar',
                data: {
                    labels: Object.keys(revenueData),
                    datasets: [{
                        label: 'Revenue',
                        data: Object.values(revenueData),
                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                        borderColor: 'rgb(34, 197, 94)',
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false
                    }, {
                        label: 'Average',
                        data: Object.keys(revenueData).map(() => Object.values(revenueData).reduce((a, b) => a + b, 0) / Object.values(revenueData).length),
                        type: 'line',
                        borderColor: 'rgb(251, 191, 36)',
                        backgroundColor: 'rgba(251, 191, 36, 0.1)',
                        borderWidth: 2,
                        fill: false,
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: 'rgba(34, 197, 94, 0.5)',
                            borderWidth: 1
                        }
                    }
                }
            });

            // Refresh charts when Livewire updates
            document.addEventListener('livewire:updated', function() {
                // Get updated data
                const newSubscriptionData = @json(isset($chartData['subscriptions']) ? $chartData['subscriptions']->toArray() : []);
                const newRevenueData = @json(isset($chartData['revenue']) ? $chartData['revenue']->toArray() : []);

                // Update subscription chart
                subscriptionChart.data.labels = Object.keys(newSubscriptionData);
                subscriptionChart.data.datasets[0].data = Object.values(newSubscriptionData);
                subscriptionChart.update();

                // Update revenue chart
                revenueChart.data.labels = Object.keys(newRevenueData);
                revenueChart.data.datasets[0].data = Object.values(newRevenueData);
                revenueChart.update();
            });
        });
    </script>
@endpush
