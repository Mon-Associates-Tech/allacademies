<div class="p-4 lg:p-6 space-y-6">
    <!-- Header with Gradient -->
    <div class="bg-gradient-to-r from-violet-600 via-purple-600 to-indigo-600 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-white/20 backdrop-blur-sm rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">Notifications</h1>
                    <p class="text-violet-100 text-sm">Stay updated with your activities</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-center bg-white/10 backdrop-blur-sm rounded-lg px-4 py-2">
                    <p class="text-2xl font-bold">{{ $totalNotifications }}</p>
                    <p class="text-xs text-violet-100">Total</p>
                </div>
                @if($unreadCount > 0)
                <div class="text-center bg-red-500/80 backdrop-blur-sm rounded-lg px-4 py-2">
                    <p class="text-2xl font-bold">{{ $unreadCount }}</p>
                    <p class="text-xs text-red-100">Unread</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Assignments</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $assignmentCount }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/50 rounded-lg">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Assessments</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $assessmentCount }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Completed</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $completedAssignmentsCount }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-orange-100 dark:bg-orange-900/50 rounded-lg">
                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Pending</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ count($pendingAssignments) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <!-- Tabs -->
            <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                <button wire:click="setActiveTab('all')" class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $activeTab === 'all' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white' }}">
                    All
                </button>
                <button wire:click="setActiveTab('unread')" class="px-4 py-2 text-sm font-medium rounded-md transition-colors flex items-center gap-2 {{ $activeTab === 'unread' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white' }}">
                    Unread
                    @if($unreadCount > 0)
                    <span class="bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full">{{ $unreadCount }}</span>
                    @endif
                </button>
                <button wire:click="setActiveTab('read')" class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $activeTab === 'read' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white' }}">
                    Read
                </button>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <!-- Search -->
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search notifications..." class="w-full sm:w-64 pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                    <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Type Filter -->
                <select wire:model.live="typeFilter" class="px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-violet-500">
                    <option value="all">All Types</option>
                    <option value="assignment">Assignments</option>
                    <option value="assessment">Assessments</option>
                    <option value="other">Other</option>
                </select>

                <!-- Sort -->
                <select wire:model.live="sortBy" class="px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-violet-500">
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                </select>

                @if($unreadCount > 0)
                <button wire:click="markAllAsRead" class="px-4 py-2 text-sm font-medium text-violet-600 dark:text-violet-400 hover:bg-violet-50 dark:hover:bg-violet-900/20 rounded-lg transition-colors">
                    Mark all read
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        @if($notifications->count() > 0)
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($notifications as $notification)
            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors {{ !$notification['is_read'] ? 'bg-violet-50/50 dark:bg-violet-900/10' : '' }}" wire:key="notification-{{ $notification['id'] }}">
                <div class="flex items-start gap-4">
                    <!-- Icon -->
                    <div class="flex-shrink-0">
                        @if($notification['category'] === 'assignment')
                        <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        @elseif($notification['category'] === 'assessment')
                        <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900/50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                        @else
                        <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white {{ !$notification['is_read'] ? 'font-bold' : '' }}">
                                    {{ $notification['title'] }}
                                </p>
                                @if($notification['subject'])
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $notification['subject'] }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                @if(!$notification['is_read'])
                                <span class="w-2 h-2 bg-violet-500 rounded-full"></span>
                                @endif
                                <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ $notification['message'] }}</p>

                        <!-- Actions -->
                        <div class="flex items-center gap-3 mt-3">
                            @if($notification['category'] === 'assignment' && isset($notification['assignment_id']))
                            <button wire:click="startAssignment({{ $notification['assignment_id'] }})" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Start
                            </button>
                            @endif
                            @if($notification['category'] === 'assessment' && isset($notification['quiz_id']))
                            <button wire:click="viewQuizResults({{ $notification['quiz_id'] }})" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-purple-600 hover:bg-purple-700 rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                View Results
                            </button>
                            @endif
                            @if(!$notification['is_read'] && $notification['type'] !== 'assessment')
                            <button wire:click="markNotificationAsRead('{{ $notification['id'] }}', '{{ $notification['type'] }}')" class="text-xs text-gray-500 dark:text-gray-400 hover:text-violet-600 dark:hover:text-violet-400 transition-colors">
                                Mark as read
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <!-- Empty State -->
        <div class="p-12 text-center">
            <div class="w-16 h-16 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No notifications</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                @if($activeTab === 'unread')
                    You're all caught up! No unread notifications.
                @elseif($search)
                    No notifications match your search.
                @else
                    You don't have any notifications yet.
                @endif
            </p>
            @if($search || $typeFilter !== 'all' || $activeTab !== 'all')
            <button wire:click="clearFilters" class="mt-4 text-sm text-violet-600 dark:text-violet-400 hover:underline">
                Clear filters
            </button>
            @endif
        </div>
        @endif
    </div>

    <!-- Pending Assignments Alert -->
    @if(count($pendingAssignments) > 0)
    <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-xl p-4">
        <div class="flex items-start gap-3">
            <div class="p-2 bg-orange-100 dark:bg-orange-900/50 rounded-lg">
                <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-orange-800 dark:text-orange-200">{{ count($pendingAssignments) }} Pending Assignment(s)</h3>
                <div class="mt-2 space-y-2">
                    @foreach($pendingAssignments as $assignment)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-orange-700 dark:text-orange-300">{{ $assignment['title'] }} - {{ $assignment['subject'] }}</span>
                        <button wire:click="startAssignment({{ $assignment['id'] }})" class="text-orange-600 dark:text-orange-400 hover:underline font-medium">Start Now →</button>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
