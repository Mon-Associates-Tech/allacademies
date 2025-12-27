<div
    class="min-h-screen rounded-xl bg-gradient-to-br from-gray-50 via-white to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Enhanced Header Section -->
        <div class="relative mb-8 overflow-hidden rounded-2xl">
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-600 via-green-600 to-teal-600 opacity-90"></div>
            <div class="absolute inset-0 bg-black opacity-20"></div>
            <div class="absolute inset-0"
                 style="background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\' viewBox=\'0 0 100 100\'%3E%3Cg fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.08\'%3E%3Cpath d=\'M96 95h4v1h-4v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9zm-1 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>

            <div class="relative px-8 py-12">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div class="mb-6 lg:mb-0">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-white/20 rounded-full backdrop-blur-sm mr-4">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-3xl lg:text-4xl font-bold text-white mb-2">Revenue Analytics</h1>
                                <p class="text-emerald-100 text-lg">Track your earnings and financial performance</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 border border-white/30">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-emerald-100 text-sm font-medium">Your Net Revenue (90%)</p>
                                    <p class="text-2xl font-bold text-white">
                                        GHS {{ number_format($revenueData['total_net_revenue'], 2) }}</p>
                                    <p class="text-xs text-emerald-100 mt-1">Gross:
                                        GHS {{ number_format($revenueData['total_gross_revenue'], 2) }}</p>
                                </div>
                                <div class="p-2 bg-white/20 rounded-lg">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 border border-white/30">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-emerald-100 text-sm font-medium">Platform Fee (10%)</p>
                                    <p class="text-2xl font-bold text-white">
                                        GHS {{ number_format($revenueData['total_platform_fees'], 2) }}</p>
                                    <p class="text-xs text-emerald-100 mt-1">{{ $revenueData['total_payments'] }}
                                        payments</p>
                                </div>
                                <div class="p-2 bg-white/20 rounded-lg">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 mb-8 overflow-hidden">
            <div
                class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                    </svg>
                    Revenue Controls
                </h3>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Date Range Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Date Range
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            <button wire:click="updateDateRange('7')"
                                    class="px-3 py-2 text-xs font-medium rounded-lg {{ $dateRange == '7' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors">
                                7 Days
                            </button>
                            <button wire:click="updateDateRange('30')"
                                    class="px-3 py-2 text-xs font-medium rounded-lg {{ $dateRange == '30' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors">
                                30 Days
                            </button>
                            <button wire:click="updateDateRange('90')"
                                    class="px-3 py-2 text-xs font-medium rounded-lg {{ $dateRange == '90' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors">
                                90 Days
                            </button>
                        </div>
                    </div>

                    <!-- Period Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            View Period
                        </label>
                        <select wire:model.live="selectedPeriod"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>

                    <!-- Book Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Filter by Book
                        </label>
                        <select wire:model.live="selectedBook"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="all">All Books</option>
                            @foreach($books as $book)
                                <option value="{{ $book->id }}">{{ $book->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Stats Grid -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-emerald-100 dark:bg-emerald-900/20 rounded-lg">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
                @if($revenueData['growth_percentage'] > 0)
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                ↗️ {{ $revenueData['growth_percentage'] }}%
            </span>
                @elseif($revenueData['growth_percentage'] < 0)
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                ↘️ {{ abs($revenueData['growth_percentage']) }}%
            </span>
                @endif
            </div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Your Net Revenue (90%)</h3>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                GHS {{ number_format($revenueData['total_net_revenue'], 2) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Gross:
                GHS {{ number_format($revenueData['total_gross_revenue'], 2) }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Revenue Chart -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Revenue Trend</h3>
                    <div class="flex items-center space-x-2">
                        <span class="w-3 h-3 bg-emerald-500 rounded-full"></span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Daily Revenue</span>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Top Performing Books -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Top Performing Books</h3>
                <div class="space-y-4">
                    @foreach($topBooks as $bookData)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg overflow-hidden">
                                    <img src="{{$bookData['book']->cover_image}}" alt="{{ $bookData['book']->title }}"
                                         class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $bookData['book']->title }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $bookData['subscriptions'] }}
                                        subscriptions</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400">
                                    GHS {{ number_format($bookData['net_revenue'], 2) }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Avg: GHS {{ number_format($bookData['average_price'], 2) }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Revenue Projections -->
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Revenue Projections</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                        GHS {{ number_format($projections['next_month_projection'], 2) }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Next Month</div>
                </div>
                <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                        GHS {{ number_format($projections['quarterly_projection'], 2) }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Next Quarter</div>
                </div>
                <div class="text-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                    <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                        GHS {{ number_format($projections['yearly_projection'], 2) }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Next Year</div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div
                class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Transactions</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Customer
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Book
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Amount
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Date
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($recentTransactions as $transactionData)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <x-avatar :name="$transactionData['payment']->bookSubscription->user->name"
                                              avatar="{{ $transactionData['payment']->bookSubscription->user->avatar }}"
                                              class="mr-3 h-8 w-8"/>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $transactionData['payment']->bookSubscription->user->name }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $transactionData['payment']->bookSubscription->user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $transactionData['payment']->bookSubscription->book->title }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-emerald-600 dark:text-emerald-400">
                                    GHS {{ number_format($transactionData['net_amount'], 2) }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Gross: GHS {{ number_format($transactionData['gross_amount'], 2) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $transactionData['payment']->created_at->format('M j, Y') }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const chartData = @json($chartData);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Revenue',
                    data: chartData.revenues,
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return 'GHS ' + value.toFixed(2);
                            }
                        }
                    }
                }
            }
        });
    });
</script>
