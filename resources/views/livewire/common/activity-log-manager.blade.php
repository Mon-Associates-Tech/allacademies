
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Activity Log Manager</h1>
                    <p class="mt-2 text-sm text-gray-600">Track and monitor all academic system activities</p>
                </div>
                <div class="flex space-x-2">
                    <button
                        wire:click="refreshData"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Refresh
                    </button>
                    <button
                        wire:click="exportActivities"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export
                    </button>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-md">
                {{ session('error') }}
            </div>
        @endif

        <!-- Navigation Tabs -->
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex space-x-8">
                <button
                    wire:click="$set('activeTab', 'all')"
                    class="@if($activeTab === 'all') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                >
                    All Activities
                </button>
                <button
                    wire:click="$set('activeTab', 'model')"
                    class="@if($activeTab === 'model') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                >
                    By Model
                </button>
                <button
                    wire:click="$set('activeTab', 'user')"
                    class="@if($activeTab === 'user') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                >
                    By User
                </button>
                <button
                    wire:click="$set('activeTab', 'date')"
                    class="@if($activeTab === 'date') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                >
                    Date Range
                </button>
                <button
                    wire:click="$set('activeTab', 'instance')"
                    class="@if($activeTab === 'instance') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                >
                    Model Instance
                </button>
                <button
                    wire:click="$set('activeTab', 'statistics')"
                    class="@if($activeTab === 'statistics') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                >
                    Statistics
                </button>
            </nav>
        </div>

        <!-- Filters Section -->
        @if($activeTab !== 'statistics')
            <div class="bg-gray-50 rounded-lg p-4 w-full mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 w-full lg:grid-cols-4 gap-4">
                    <!-- Search -->
                    @if($activeTab === 'all' || $activeTab === 'search')
                        <div class="w-full">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <div class="flex w-full">
                                <input
                                    type="text"
                                    wire:model.defer="searchKeyword"
                                    placeholder="Search activities..."
                                    class="flex-1 rounded-l-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                >
                                <button
                                    wire:click="search"
                                    class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 shadow-sm text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Model Type Filter -->
                    @if(in_array($activeTab, ['model', 'instance']))
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Model Type</label>
                            <select
                                wire:model="selectedModelType"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            >
                                <option value="">All Models</option>
                                @foreach($modelTypes as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Model Instance Filter -->
                    @if($activeTab === 'instance' && $selectedModelType)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Model Instance</label>
                            <select
                                wire:model="selectedModelId"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            >
                                <option value="">Select Instance</option>
                                @foreach($modelInstances as $instance)
                                    <option value="{{ $instance['id'] }}">{{ $instance['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- User Filter -->
                    @if($activeTab === 'user')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                            <select
                                wire:model="selectedUserId"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            >
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Date Range -->
                    @if($activeTab === 'date')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                            <input
                                type="date"
                                wire:model.defer="startDate"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                            <input
                                type="date"
                                wire:model.defer="endDate"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            >
                        </div>
                    @endif

                    <!-- Limit Per Page -->
                    <div class="">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Items Per Page</label>
                        <select
                            wire:model="limitPerPage"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        >
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-4 flex space-x-2">
                    @if(in_array($activeTab, ['model', 'user', 'date', 'instance']))
                        <button
                            wire:click="filterBy{{ ucfirst($activeTab) }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700"
                        >
                            Apply Filter
                        </button>
                    @endif

                    <button
                        wire:click="resetFilters"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                    >
                        Reset Filters
                    </button>
                </div>
            </div>
        @endif

        <!-- Statistics View -->
        @if($activeTab === 'statistics')
            <div wire:loading.remove wire:target="loadActivities">
                @if(!empty($statistics))
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <!-- Total Activities -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-indigo-500 rounded-md flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Total Activities</dt>
                                            <dd class="text-lg font-medium text-gray-900">{{ number_format($statistics['total_activities'] ?? 0) }}</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activities -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Last 7 Days</dt>
                                            <dd class="text-lg font-medium text-gray-900">{{ number_format($statistics['recent_activity_count'] ?? 0) }}</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activities by Type Chart -->
                    @if(!empty($statistics['activities_by_type']))
                        <div class="bg-white shadow rounded-lg p-6 mb-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Activities by Model Type</h3>
                            <div class="space-y-3">
                                @foreach($statistics['activities_by_type'] as $type => $count)
                                    <div class="flex items-center">
                                        <div class="flex-1 flex items-center">
                                            <span class="text-sm font-medium text-gray-900 w-32">{{ ucfirst($type) }}</span>
                                            <div class="flex-1 mx-4">
                                                <div class="bg-gray-200 rounded-full h-2">
                                                    <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ ($count / $statistics['total_activities']) * 100 }}%"></div>
                                                </div>
                                            </div>
                                            <span class="text-sm text-gray-500">{{ $count }} ({{ round(($count / $statistics['total_activities']) * 100, 1) }}%)</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Activities by Action -->
                    @if(!empty($statistics['activities_by_action']))
                        <div class="bg-white shadow rounded-lg p-6 mb-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Activities by Action Type</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($statistics['activities_by_action'] as $action => $count)
                                    <div class="text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        @if($action === 'created') bg-green-100 text-green-800
                                        @elseif($action === 'updated') bg-blue-100 text-blue-800
                                        @elseif($action === 'deleted') bg-red-100 text-red-800
                                        @elseif($action === 'restored') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($action) }}
                                    </span>
                                        <div class="mt-2 text-2xl font-semibold text-gray-900">{{ $count }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Top Users -->
                    @if(!empty($statistics['activities_by_user']))
                        <div class="bg-white shadow rounded-lg p-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Most Active Users</h3>
                            <div class="space-y-3">
                                @foreach(array_slice($statistics['activities_by_user'], 0, 10, true) as $userId => $userData)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                                    <span class="text-xs font-medium text-gray-700">{{ substr($userData['user_name'], 0, 2) }}</span>
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900">{{ $userData['user_name'] }}</p>
                                            </div>
                                        </div>
                                        <span class="text-sm text-gray-500">{{ $userData['count'] }} activities</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500">No statistics available.</p>
                    </div>
                @endif
            </div>
        @else
            <!-- Activities List -->
            <div wire:loading.remove wire:target="loadActivities">
                @if(count($activities) > 0)
                    <div class="bg-white shadow overflow-hidden sm:rounded-md">
                        <ul class="divide-y divide-gray-200">
                            @foreach($activities as $activity)
                                <li class="px-6 py-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($activity['action_type'] === 'created') bg-green-100 text-green-800
                                                @elseif($activity['action_type'] === 'updated') bg-blue-100 text-blue-800
                                                @elseif($activity['action_type'] === 'deleted') bg-red-100 text-red-800
                                                @elseif($activity['action_type'] === 'restored') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($activity['action_type']) }}
                                            </span>
                                            </div>
                                            <div class="ml-4 flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">
                                                    {{ $activity['description'] }}
                                                </p>
                                                <div class="mt-2 flex items-center text-sm text-gray-500 space-x-4">
                                                <span class="flex items-center">
                                                    <svg class="flex-shrink-0 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    {{ $activity['user_name'] }}
                                                </span>
                                                    <span class="flex items-center">
                                                    <svg class="flex-shrink-0 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    {{ $activity['time_ago'] }}
                                                </span>
                                                    @if($activity['model_data'])
                                                        <span class="flex items-center">
                                                        <svg class="flex-shrink-0 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        {{ $activity['model_data']['type'] }} #{{ $activity['model_data']['id'] }}
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="text-sm text-gray-400">{{ $activity['formatted_date'] }}</span>
                                            <!-- Expandable details button -->
                                            <button
                                                onclick="toggleDetails({{ $activity['id'] }})"
                                                class="ml-4 inline-flex items-center p-1 border border-transparent rounded-full shadow-sm text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                            >
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Expandable Details -->
                                    <div id="details-{{ $activity['id'] }}" class="hidden mt-4 pl-4 border-l-2 border-gray-200">
                                        @if(!empty($activity['changes']['old']) || !empty($activity['changes']['new']))
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                @if(!empty($activity['changes']['old']) && $activity['action_type'] !== 'created')
                                                    <div>
                                                        <h4 class="text-sm font-medium text-gray-900 mb-2">Old Values</h4>
                                                        <div class="bg-red-50 rounded-md p-3">
                                                            @foreach($activity['changes']['old'] as $field => $value)
                                                                <div class="text-sm">
                                                                    <span class="font-medium text-red-800">{{ $field }}:</span>
                                                                    <span class="text-red-600 ml-1">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                                @if(!empty($activity['changes']['new']) && $activity['action_type'] !== 'deleted')
                                                    <div>
                                                        <h4 class="text-sm font-medium text-gray-900 mb-2">New Values</h4>
                                                        <div class="bg-green-50 rounded-md p-3">
                                                            @foreach($activity['changes']['new'] as $field => $value)
                                                                <div class="text-sm">
                                                                    <span class="font-medium text-green-800">{{ $field }}:</span>
                                                                    <span class="text-green-600 ml-1">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <p class="text-sm text-gray-500 italic">No detailed changes recorded.</p>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Load More Button -->
                    <div class="mt-6 text-center">
                        <button
                            wire:click="loadActivities"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Load More Activities
                        </button>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No activities found</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            @if($activeTab === 'search' && $searchKeyword)
                                No activities match your search criteria "{{ $searchKeyword }}"
                            @elseif($activeTab === 'date' && $startDate && $endDate)
                                No activities found between {{ $startDate }} and {{ $endDate }}
                            @elseif($activeTab === 'user' && $selectedUserId)
                                No activities found for the selected user
                            @elseif($activeTab === 'model' && $selectedModelType)
                                No activities found for {{ $modelTypes[$selectedModelType] ?? $selectedModelType }}
                            @else
                                No activities have been recorded yet
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        @endif

        <!-- Loading State -->
        <div wire:loading wire:target="loadActivities" class="flex justify-center py-8">
            <div class="inline-flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm text-gray-600">Loading activities...</span>
            </div>
        </div>
    </div>

    <script>
        function toggleDetails(activityId) {
            const element = document.getElementById('details-' + activityId);
            if (element.classList.contains('hidden')) {
                element.classList.remove('hidden');
            } else {
                element.classList.add('hidden');
            }
        }
    </script>


