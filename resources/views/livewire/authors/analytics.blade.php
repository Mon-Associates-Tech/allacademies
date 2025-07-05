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
                Export Report
            </button>
        </div>
    </div>

    <!-- Filter Controls -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Time Period Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Time Period</label>
                <select wire:model="period" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-violet-500 dark:bg-gray-700 dark:text-white">
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
                <select wire:model="selectedBook" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-violet-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Books</option>
                    @foreach($books as $book)
                        <option value="{{ $book->id }}">{{ $book->title }}</option>
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

    <!-- Key Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Views -->
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 rounded-lg shadow-sm p-6 border border-blue-200 dark:border-blue-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">Total Views</p>
                    <p class="text-3xl font-bold text-blue-900 dark:text-white">{{ number_format($analytics['total_views'] ?? 0) }}</p>
                    <p class="text-xs text-blue-500 dark:text-blue-300 mt-1">
                        <span class="inline-flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                            </svg>
                            Book visibility
                        </span>
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
                        <span class="inline-flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Active readers
                        </span>
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
                        <span class="inline-flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Book loans
                        </span>
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
                    <p class="text-3xl font-bold text-yellow-900 dark:text-yellow-100">${{ number_format($analytics['revenue'] ?? 0, 2) }}</p>
                    <p class="text-xs text-yellow-500 dark:text-yellow-300 mt-1">
                        <span class="inline-flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Total earnings
                        </span>
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
                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                    Daily Subscriptions
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
                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                    Daily Revenue
                </div>
            </div>
            <div class="h-64 flex items-center justify-center">
                <canvas id="revenueChart" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>

    <!-- Performance Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Performing Books -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Top Performing Books</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Books with highest engagement</p>
            </div>
            <div class="p-6">
                @if(isset($analytics['top_performing_books']) && $analytics['top_performing_books']->count() > 0)
                    <div class="space-y-4">
                        @foreach($analytics['top_performing_books'] as $book)
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center space-x-3">
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
                                    <div class="text-sm font-medium text-gray-800 dark:text-gray-100">
                                        {{ $book->subscriptions_count }} subs
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $book->borrowings_count }} borrowings
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
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-3 h-3 bg-violet-500 rounded-full"></div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">{{ $role }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-900 dark:text-gray-100 font-medium">{{ $count }}</span>
                                    <div class="w-16 bg-gray-200 dark:bg-gray-600 rounded-full h-2">
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

    <!-- Quick Insights -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Quick Insights</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">Most Popular Genre</h4>
                <p class="text-lg font-bold text-blue-900 dark:text-blue-100">Fiction</p>
                <p class="text-xs text-blue-600 dark:text-blue-300">Based on subscription patterns</p>
            </div>
            <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg">
                <h4 class="text-sm font-medium text-green-800 dark:text-green-200">Average Revenue per Book</h4>
                <p class="text-lg font-bold text-green-900 dark:text-green-100">
                    ${{ number_format(($analytics['revenue'] ?? 0) / max(1, $books->count()), 2) }}
                </p>
                <p class="text-xs text-green-600 dark:text-green-300">Monthly average</p>
            </div>
            <div class="bg-purple-50 dark:bg-purple-900 p-4 rounded-lg">
                <h4 class="text-sm font-medium text-purple-800 dark:text-purple-200">Engagement Rate</h4>
                <p class="text-lg font-bold text-purple-900 dark:text-purple-100">
                    {{ number_format((($analytics['total_subscriptions'] ?? 0) / max(1, $analytics['total_views'] ?? 1)) * 100, 1) }}%
                </p>
                <p class="text-xs text-purple-600 dark:text-purple-300">Views to subscriptions</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Convert Laravel collections to JavaScript arrays
            const subscriptionData = @json(isset($chartData['subscriptions']) ? $chartData['subscriptions']->toArray() : []);
            const revenueData = @json(isset($chartData['revenue']) ? $chartData['revenue']->toArray() : []);

            // Subscription Chart
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
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
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
                            display: false
                        }
                    }
                }
            });

            // Revenue Chart
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
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
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
                            display: false
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
