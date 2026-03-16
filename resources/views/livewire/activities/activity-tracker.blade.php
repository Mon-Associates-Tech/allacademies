<div class="space-y-6 pb-8">
    <!-- Page Header -->
    <div class="space-y-4">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Activity Tracker</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Monitor all your actions and interactions across the platform</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Activities -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow">
            <div class="px-6 py-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Activities</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900/30">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Activities -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow">
            <div class="px-6 py-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Today</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['today'] }}</p>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-green-100 dark:bg-green-900/30">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- This Week -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow">
            <div class="px-6 py-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">This Week</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['this_week'] }}</p>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-purple-100 dark:bg-purple-900/30">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 012 12V7a2 2 0 012-2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- This Month -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow">
            <div class="px-6 py-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">This Month</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['this_month'] }}</p>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-orange-100 dark:bg-orange-900/30">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Filters</h2>
            @if($searchTerm || $filterCategory || $filterType || $sortBy !== 'recent')
                <button
                    wire:click="clearFilters"
                    class="px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition"
                >
                    Clear All
                </button>
            @endif
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <!-- Search Input -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <input
                    type="text"
                    wire:model.live="searchTerm"
                    placeholder="Search activities..."
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                />
            </div>

            <!-- Category Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                <select
                    wire:model.live="filterCategory"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                >
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}">{{ ucfirst($category) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Activity Type Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Activity Type</label>
                <select
                    wire:model.live="filterType"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                >
                    <option value="">All Types</option>
                    @foreach($activityTypes as $type)
                        <option value="{{ $type }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Sort Options -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sort By</label>
                <select
                    wire:model.live="sortBy"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                >
                    <option value="recent">Most Recent</option>
                    <option value="oldest">Oldest First</option>
                    <option value="alphabetical">Alphabetical</option>
                    <option value="category">By Category</option>
                </select>
            </div>

            <!-- Per Page -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Per Page</label>
                <select
                    wire:model.live="perPage"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                >
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Activities List -->
    <div class="space-y-3">
        @if($activities->count() > 0)
            @foreach($activities as $activity)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all hover:border-gray-300 dark:hover:border-gray-600 overflow-hidden">
                    <div class="px-6 py-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <!-- Activity Header -->
                                <div class="flex items-center gap-3 mb-2">
                                    <!-- Activity Type Icon -->
                                    <div class="flex-shrink-0">
                                        @php
                                            $iconClass = 'w-5 h-5';
                                            $bgClass = 'bg-gray-100 dark:bg-gray-700';
                                            $textClass = 'text-gray-600 dark:text-gray-400';

                                            match($activity->activity_type) {
                                                'view' => [$bgClass = 'bg-blue-100 dark:bg-blue-900/30', $textClass = 'text-blue-600 dark:text-blue-400'],
                                                'create' => [$bgClass = 'bg-green-100 dark:bg-green-900/30', $textClass = 'text-green-600 dark:text-green-400'],
                                                'update' => [$bgClass = 'bg-yellow-100 dark:bg-yellow-900/30', $textClass = 'text-yellow-600 dark:text-yellow-400'],
                                                'delete' => [$bgClass = 'bg-red-100 dark:bg-red-900/30', $textClass = 'text-red-600 dark:text-red-400'],
                                                'download' => [$bgClass = 'bg-purple-100 dark:bg-purple-900/30', $textClass = 'text-purple-600 dark:text-purple-400'],
                                                'saveResource' => [$bgClass = 'bg-indigo-100 dark:bg-indigo-900/30', $textClass = 'text-indigo-600 dark:text-indigo-400'],
                                                'login', 'logout' => [$bgClass = 'bg-cyan-100 dark:bg-cyan-900/30', $textClass = 'text-cyan-600 dark:text-cyan-400'],
                                                'submit' => [$bgClass = 'bg-teal-100 dark:bg-teal-900/30', $textClass = 'text-teal-600 dark:text-teal-400'],
                                                'purchase' => [$bgClass = 'bg-pink-100 dark:bg-pink-900/30', $textClass = 'text-pink-600 dark:text-pink-400'],
                                                default => [$bgClass = 'bg-gray-100 dark:bg-gray-700', $textClass = 'text-gray-600 dark:text-gray-400'],
                                            };
                                        @endphp
                                        <div class="flex items-center justify-center w-10 h-10 rounded-lg {{ $bgClass }}">
                                            @switch($activity->activity_type)
                                                @case('view')
                                                    <svg class="{{ $iconClass }} {{ $textClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                @break
                                                @case('create')
                                                    <svg class="{{ $iconClass }} {{ $textClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                    </svg>
                                                @break
                                                @case('update')
                                                    <svg class="{{ $iconClass }} {{ $textClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                @break
                                                @case('delete')
                                                    <svg class="{{ $iconClass }} {{ $textClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3H4v2h16V7h-3.5z"/>
                                                    </svg>
                                                @break
                                                @case('download')
                                                    <svg class="{{ $iconClass }} {{ $textClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                    </svg>
                                                @break
                                                @case('saveResource')
                                                    <svg class="{{ $iconClass }} {{ $textClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12"/>
                                                    </svg>
                                                @break
                                                @case('login')
                                                    <svg class="{{ $iconClass }} {{ $textClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v2a2 2 0 01-2 2H7a2 2 0 01-2-2v-2"/>
                                                    </svg>
                                                @break
                                                @case('logout')
                                                    <svg class="{{ $iconClass }} {{ $textClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-2"/>
                                                    </svg>
                                                @break
                                                @case('submit')
                                                    <svg class="{{ $iconClass }} {{ $textClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                @break
                                                @case('purchase')
                                                    <svg class="{{ $iconClass }} {{ $textClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                    </svg>
                                                @break
                                                @default
                                                    <svg class="{{ $iconClass }} {{ $textClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                    </svg>
                                            @endswitch
                                        </div>
                                    </div>

                                    <!-- Activity Name and Category -->
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                            {{ $activity->activity_name }}
                                        </h3>
                                        <div class="mt-1 flex flex-wrap items-center gap-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                                {{ ucfirst($activity->category) }}
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $activity->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Activity Description -->
                                @if($activity->description)
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $activity->description }}
                                    </p>
                                @endif

                                <!-- Metadata Section -->
                                @if($activity->metadata && count($activity->metadata) > 0)
                                    @php
                                        // Filter metadata to only show useful resource information
                                        $usefulMetadata = [];
                                        $technicalKeys = ['path', 'method', 'route', 'query_params', 'resource_id'];

                                        foreach ($activity->metadata as $key => $value) {
                                            if (!in_array($key, $technicalKeys) && $value && !empty($value)) {
                                                // For nested arrays like user_data, student_data, etc.
                                                if (is_array($value)) {
                                                    foreach ($value as $subKey => $subValue) {
                                                        if ($subValue && !empty($subValue)) {
                                                            $usefulMetadata[ucfirst(str_replace('_', ' ', $subKey))] = $subValue;
                                                        }
                                                    }
                                                } else {
                                                    $usefulMetadata[ucfirst(str_replace('_', ' ', $key))] = $value;
                                                }
                                            }
                                        }
                                    @endphp

                                    @if(count($usefulMetadata) > 0)
                                        <div class="mt-3 bg-gray-50 dark:bg-gray-700/50 rounded px-3 py-2">
                                            <div class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Details:</div>
                                            <div class="space-y-1">
                                                @foreach($usefulMetadata as $key => $value)
                                                    <div class="text-xs flex justify-between">
                                                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ $key }}:</span>
                                                        <span class="text-gray-600 dark:text-gray-400 ml-2 text-right max-w-xs truncate">{{ $value }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endif

                                <!-- Related Subject -->
                                @if($activity->subject)
                                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        <span class="font-medium">Related to:</span> {{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }}
                                    </div>
                                @endif
                            </div>

                            <!-- Timestamp -->
                            <div class="ml-4 flex-shrink-0 text-right">
                                <p class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                    {{ $activity->created_at->format('M d, Y') }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                    {{ $activity->created_at->format('H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Pagination -->
            <div class="mt-6">
                {{ $activities->links(data: ['scrollTo' => false]) }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No activities found</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    @if($searchTerm || $filterCategory || $filterType)
                        Try adjusting your filters to find what you're looking for.
                    @else
                        Your activity tracker is empty. Start using the platform to see your activities here.
                    @endif
                </p>
                @if($searchTerm || $filterCategory || $filterType)
                    <button
                        wire:click="clearFilters"
                        class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition"
                    >
                        Clear Filters
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>
