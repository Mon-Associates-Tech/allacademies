<div class="bg-gray-50 dark:bg-gray-900 p-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Administrative Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Welcome back, {{ auth()->user()->name }}</p>
        </div>
        <div class="flex items-center space-x-4">
            <select wire:model.live="selectedPeriod" class="rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                <option value="today">Today</option>
                <option value="week">This Week</option>
                <option value="month">This Month</option>
                <option value="year">This Year</option>
            </select>
        </div>
    </div>

    <!-- System Alerts -->
    @if(count($this->systemAlerts) > 0)
        <div class="mb-8 space-y-3 hidden">
            @foreach($this->systemAlerts as $alert)
                <div class="flex items-center justify-between p-4 rounded-lg border {{ $alert['type'] === 'warning' ? 'bg-yellow-50 border-yellow-200 text-yellow-800 dark:bg-yellow-900/20 dark:border-yellow-700 dark:text-yellow-200' : 'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-900/20 dark:border-blue-700 dark:text-blue-200' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            @if($alert['type'] === 'warning')
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            @else
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            @endif
                        </svg>
                        <span class="font-medium">{{ $alert['message'] }}</span>
                    </div>
                    <a href="{{ route($alert['route']) }}" class="text-sm font-semibold hover:underline">
                        {{ $alert['action'] }} →
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    <!-- System Health Overview -->
    <div class="mb-8 bg-white hidden dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">System Health</h2>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500 dark:text-gray-400">Score:</span>
                <span class="text-lg font-bold {{ $this->systemHealth['status'] === 'excellent' ? 'text-green-600' : ($this->systemHealth['status'] === 'good' ? 'text-blue-600' : ($this->systemHealth['status'] === 'fair' ? 'text-yellow-600' : 'text-red-600')) }}">
                    {{ $this->systemHealth['score'] }}/100
                </span>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                <div class="h-3 rounded-full {{ $this->systemHealth['status'] === 'excellent' ? 'bg-green-500' : ($this->systemHealth['status'] === 'good' ? 'bg-blue-500' : ($this->systemHealth['status'] === 'fair' ? 'bg-yellow-500' : 'bg-red-500')) }}"
                     style="width: {{ $this->systemHealth['score'] }}%"></div>
            </div>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">{{ $this->systemHealth['status'] }}</span>
        </div>
        @if(count($this->systemHealth['issues']) > 0)
            <div class="mt-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Issues requiring attention:</p>
                <ul class="mt-2 space-y-1">
                    @foreach($this->systemHealth['issues'] as $issue)
                        <li class="text-sm text-red-600 dark:text-red-400">• {{ str_replace('_', ' ', ucfirst($issue)) }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Quick Stats Grid -->
    <div class=" hidden grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- System Stats -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Users</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($this->systemStats['total_users']) }}</p>
                    <p class="text-xs text-green-600">{{ number_format($this->systemStats['active_users']) }} active this week</p>
                </div>
            </div>
        </div>

        <!-- Library Stats -->
        <div class="bg-white hidden dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Books</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($this->libraryStats['total_books']) }}</p>
                    <p class="text-xs text-blue-600">{{ number_format($this->libraryStats['active_borrowings']) }} currently borrowed</p>
                </div>
            </div>
        </div>

        <!-- Academic Stats -->
        <div class="bg-white hidden dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Active Subscriptions</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($this->academicStats['active_subscriptions']) }}</p>
                    <p class="text-xs text-purple-600">{{ number_format($this->academicStats['average_performance'], 1) }}% avg performance</p>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 dark:bg-indigo-900">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Recent Assessments</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($this->academicStats['recent_assessments']) }}</p>
                    <p class="text-xs text-indigo-600">{{ number_format($this->performanceMetrics['active_teams']) }} active teams</p>
                </div>
            </div>
        </div>
    </div>

    <!-- User Breakdown and Recent Activity -->
    <div class=" hidden grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- User Distribution -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">User Distribution</h3>
            <div class="space-y-3">
                @foreach($this->userBreakdown as $role => $count)
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300 capitalize">{{ str_replace('_', ' ', $role) }}</span>
                        <div class="flex items-center">
                            <span class="text-sm font-semibold text-gray-900 dark:text-white mr-2">{{ number_format($count) }}</span>
                            <div class="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $this->systemStats['total_users'] > 0 ? ($count / $this->systemStats['total_users']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Library Status -->
        <div class="bg-white hidden dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Library Overview</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Published Books</span>
                    <span class="text-lg font-semibold text-green-600">{{ number_format($this->libraryStats['published_books']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Pending Approval</span>
                    <span class="text-lg font-semibold text-yellow-600">{{ number_format($this->libraryStats['pending_approval']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Overdue Books</span>
                    <span class="text-lg font-semibold text-red-600">{{ number_format($this->libraryStats['overdue_books']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">New Borrowings</span>
                    <span class="text-lg font-semibold text-blue-600">{{ number_format($this->libraryStats['new_borrowings']) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class=" hidden grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
        <!-- New Users -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Users</h3>
            <div class="space-y-3">
                @forelse($this->recentActivity['new_users'] as $user)
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                            <span class="text-xs font-medium text-blue-600 dark:text-blue-300">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No recent users</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Borrowings -->
        <div class="bg-white hidden dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Borrowings</h3>
            <div class="space-y-3">
                @forelse($this->recentActivity['recent_borrowings'] as $borrowing)
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $borrowing->book->title ?? 'Unknown Book' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">by {{ $borrowing->student->user->name ?? 'Unknown Student' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $borrowing->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No recent borrowings</p>
                @endforelse
            </div>
        </div>

        <!-- Pending Approvals -->
        <div class="bg-white hidden dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pending Approvals</h3>
            <div class="space-y-3">
                @forelse($this->recentActivity['pending_approvals'] as $book)
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $book->title }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">by {{ $book->author->user->name ?? 'Unknown Author' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $book->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No pending approvals</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    @if($showQuickActions)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-0">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h3>
{{--                <button wire:click="$toggle('showQuickActions')" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">--}}
{{--                    Hide--}}
{{--                </button>--}}
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($this->quickActionItems as $action)
                    <a href="{{ route($action['route']) }}" class="relative p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-{{ $action['color'] }}-300 dark:hover:border-{{ $action['color'] }}-600 hover:shadow-md transition-all duration-200 group">
                        <div class="flex items-start space-x-3">
                            <div class="p-2 bg-{{ $action['color'] }}-100 dark:bg-{{ $action['color'] }}-900/50 rounded-lg">
                                <svg class="w-5 h-5 text-{{ $action['color'] }}-600 dark:text-{{ $action['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($action['icon'] === 'user-plus')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                    @elseif($action['icon'] === 'check-circle')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    @elseif($action['icon'] === 'exclamation-triangle')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    @elseif($action['icon'] === 'user-secret')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    @elseif($action['icon'] === 'chart-bar')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    @elseif($action['icon'] === 'cog')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    @endif
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-{{ $action['color'] }}-600 dark:group-hover:text-{{ $action['color'] }}-400">{{ $action['title'] }}</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $action['description'] }}</p>
                            </div>
                        </div>
                        @if(isset($action['badge']) && $action['badge'] > 0)
                            <div class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center">
                                {{ $action['badge'] }}
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
