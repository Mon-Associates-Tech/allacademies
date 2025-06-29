<div class="p-6 m-0 rounded-lg min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-200">

    <!-- Enhanced Header with Time Range Selector -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Admin Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Welcome back, {{ auth()->user()->name }}</p>
        </div>

        <div class="flex items-center gap-4">
            <!-- Time Range Selector -->
            <select wire:model.live="timeRange" class="px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm">
                <option value="today">Today</option>
                <option value="week">This Week</option>
                <option value="month">This Month</option>
                <option value="year">This Year</option>
            </select>

            <!-- Quick Actions -->
            <div class="flex gap-3">
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add New User
                </button>
                <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Generate Report
                </button>
            </div>
        </div>
    </div>

    <!-- System Health Status -->
    <div class="mb-8 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">System Health</h3>
            <button wire:click="loadData" class="text-sm text-blue-600 hover:text-blue-800">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Refresh
            </button>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($systemHealth as $service => $health)
                <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="w-3 h-3 rounded-full {{ $health['status'] === 'healthy' ? 'bg-green-400' : ($health['status'] === 'warning' ? 'bg-yellow-400' : 'bg-red-400') }}"></div>
                    <div>
                        <div class="text-sm font-medium text-gray-800 dark:text-white capitalize">{{ $service }}</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400">{{ $health['message'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
        <!-- User Metrics -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Users</h3>
                <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Users</span>
                    <span class="text-lg font-bold text-gray-800 dark:text-white">{{ number_format($metrics['users']['total_users'] ?? 0) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">New Users</span>
                    <span class="text-sm font-semibold text-green-600">+{{ number_format($metrics['users']['new_users'] ?? 0) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Online Now</span>
                    <span class="text-sm font-semibold text-blue-600">{{ number_format($metrics['users']['active_users'] ?? 0) }}</span>
                </div>
            </div>
        </div>

        <!-- Activity Metrics -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Activity</h3>
                <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Assessments</span>
                    <span class="text-lg font-bold text-gray-800 dark:text-white">{{ number_format($metrics['activity']['assessments_completed'] ?? 0) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Avg Score</span>
                    <span class="text-sm font-semibold text-blue-600">{{ number_format($metrics['activity']['average_score'] ?? 0, 1) }}%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Login Sessions</span>
                    <span class="text-sm font-semibold text-green-600">{{ number_format($metrics['activity']['login_sessions'] ?? 0) }}</span>
                </div>
            </div>
        </div>

        <!-- System Alerts -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Alerts</h3>
                <div class="p-2 bg-orange-100 dark:bg-orange-900 rounded-lg">
                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
            </div>
            <div class="space-y-3">
                @if($alerts['overdue_books'] > 0)
                    <div class="flex justify-between items-center p-2 bg-red-50 dark:bg-red-900/20 rounded">
                        <span class="text-sm text-red-700 dark:text-red-400">Overdue Books</span>
                        <span class="text-sm font-bold text-red-600">{{ $alerts['overdue_books'] }}</span>
                    </div>
                @endif
                @if($alerts['pending_approvals'] > 0)
                    <div class="flex justify-between items-center p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded">
                        <span class="text-sm text-yellow-700 dark:text-yellow-400">Pending Approvals</span>
                        <span class="text-sm font-bold text-yellow-600">{{ $alerts['pending_approvals'] }}</span>
                    </div>
                @endif
                @if(empty(array_filter($alerts)))
                    <div class="text-center text-sm text-gray-500 dark:text-gray-400 py-4">
                        <svg class="w-8 h-8 mx-auto mb-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        All systems normal
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Quick Stats</h3>
                <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Students</span>
                    <span class="text-lg font-bold text-gray-800 dark:text-white">{{ number_format($metrics['users']['students'] ?? 0) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Teachers</span>
                    <span class="text-lg font-bold text-gray-800 dark:text-white">{{ number_format($metrics['users']['teachers'] ?? 0) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Books Borrowed</span>
                    <span class="text-sm font-semibold text-green-600">{{ number_format($metrics['activity']['books_borrowed'] ?? 0) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Feed -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Recent User Activity</h3>
            <div class="space-y-4">
                @forelse($recentActivities['recent_users'] ?? [] as $activity)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800 dark:text-white">{{ $activity['message'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $activity['time']->diffForHumans() }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full">
                        {{ ucfirst($activity['role']) }}
                    </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No recent activity</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Recent Assessments</h3>
            <div class="space-y-4">
                @forelse($recentActivities['recent_assessments'] ?? [] as $activity)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800 dark:text-white">{{ $activity['message'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $activity['time']->diffForHumans() }}</p>
                        </div>
                        <div class="text-right">
                        <span class="text-sm font-semibold {{ $activity['score'] >= 80 ? 'text-green-600' : ($activity['score'] >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $activity['score'] }}/{{ $activity['max_score'] }}
                        </span>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ round(($activity['score'] / $activity['max_score']) * 100, 1) }}%
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No recent assessments</p>
                @endforelse
            </div>
        </div>
    </div>

</div>

<script>
    // Auto-refresh data every 30 seconds
    setInterval(() => {
        @this.call('loadData');
    }, {{ $refreshInterval * 1000 }});
</script>
