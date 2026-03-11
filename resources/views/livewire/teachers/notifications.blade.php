<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <!-- Header Section -->
    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border-b border-gray-200/50 dark:border-gray-700/50 sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-lg">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </div>
                            @if($unreadCount > 0)
                                <div class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 rounded-full flex items-center justify-center animate-pulse">
                                    <span class="text-xs font-bold text-white">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 dark:from-gray-100 dark:to-gray-300 bg-clip-text text-transparent">
                                Notifications
                            </h1>
                            <div class="flex items-center space-x-4 mt-1">
                                @if($unreadCount > 0)
                                    <div class="flex items-center space-x-2">
                                        <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                                        <p class="text-sm font-medium text-blue-600 dark:text-blue-400">{{ $unreadCount }} unread</p>
                                    </div>
                                @else
                                    <div class="flex items-center space-x-2">
                                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                        <p class="text-sm font-medium text-green-600 dark:text-green-400">All caught up!</p>
                                    </div>
                                @endif
                                <span class="text-gray-300 dark:text-gray-600">•</span>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $totalNotifications }} total</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-3">
                        @if($unreadCount > 0)
                            <button
                                wire:click="markAllAsRead"
                                class="group relative px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl text-sm font-medium hover:from-emerald-600 hover:to-teal-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <span class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span>Mark All Read</span>
                                </span>
                            </button>
                        @endif

                        @if($search || $activeTab !== 'all' || $typeFilter !== 'all')
                            <button
                                wire:click="clearFilters"
                                class="group relative px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 shadow-sm">
                                <span class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    <span>Clear Filters</span>
                                </span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Search Bar -->
        <div class="mb-6">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    class="block w-full pl-12 pr-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent shadow-sm"
                    placeholder="Search notifications by title, message, subject, or student...">
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="mb-6 space-y-4">
            <!-- Read Status Tabs -->
            <div class="flex space-x-1 bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl p-1 shadow-sm border border-gray-200/50 dark:border-gray-700/50">
                <button
                    wire:click="setActiveTab('all')"
                    class="flex-1 sm:flex-none px-4 py-2 text-sm font-medium rounded-xl transition-all duration-200 {{ $activeTab === 'all' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100' }}">
                    All
                    <span class="ml-2 px-2 py-0.5 text-xs rounded-full {{ $activeTab === 'all' ? 'bg-gray-100 dark:bg-gray-600' : 'bg-gray-200 dark:bg-gray-700' }}">{{ $totalNotifications }}</span>
                </button>
                <button
                    wire:click="setActiveTab('unread')"
                    class="flex-1 sm:flex-none px-4 py-2 text-sm font-medium rounded-xl transition-all duration-200 {{ $activeTab === 'unread' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100' }}">
                    Unread
                    <span class="ml-2 px-2 py-0.5 text-xs rounded-full {{ $activeTab === 'unread' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' : 'bg-gray-200 dark:bg-gray-700' }}">{{ $unreadCount }}</span>
                </button>
                <button
                    wire:click="setActiveTab('read')"
                    class="flex-1 sm:flex-none px-4 py-2 text-sm font-medium rounded-xl transition-all duration-200 {{ $activeTab === 'read' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100' }}">
                    Read
                </button>
            </div>

            <!-- Type Filter Tabs -->
            <div class="flex flex-wrap gap-2">
                <button
                    wire:click="setTypeFilter('all')"
                    class="px-4 py-2 text-sm font-medium rounded-xl transition-all duration-200 {{ $typeFilter === 'all' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 border border-gray-200 dark:border-gray-700' }}">
                    All Types
                </button>
                <button
                    wire:click="setTypeFilter('assignment')"
                    class="px-4 py-2 text-sm font-medium rounded-xl transition-all duration-200 {{ $typeFilter === 'assignment' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 border border-gray-200 dark:border-gray-700' }}">
                    📚 Assignments
                    <span class="ml-1 px-2 py-0.5 text-xs rounded-full {{ $typeFilter === 'assignment' ? 'bg-white/20' : 'bg-gray-200 dark:bg-gray-700' }}">{{ $assignmentCount }}</span>
                </button>
                <button
                    wire:click="setTypeFilter('submission')"
                    class="px-4 py-2 text-sm font-medium rounded-xl transition-all duration-200 {{ $typeFilter === 'submission' ? 'bg-orange-600 text-white shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 border border-gray-200 dark:border-gray-700' }}">
                    📝 Submissions
                    <span class="ml-1 px-2 py-0.5 text-xs rounded-full {{ $typeFilter === 'submission' ? 'bg-white/20' : 'bg-gray-200 dark:bg-gray-700' }}">{{ $submissionCount }}</span>
                </button>
                <button
                    wire:click="setTypeFilter('system')"
                    class="px-4 py-2 text-sm font-medium rounded-xl transition-all duration-200 {{ $typeFilter === 'system' ? 'bg-gray-600 text-white shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 border border-gray-200 dark:border-gray-700' }}">
                    🔔 System
                    <span class="ml-1 px-2 py-0.5 text-xs rounded-full {{ $typeFilter === 'system' ? 'bg-white/20' : 'bg-gray-200 dark:bg-gray-700' }}">{{ $systemCount }}</span>
                </button>
            </div>
        </div>

        <!-- Quick Stats Cards -->
        @if(count($pendingGrading) > 0)
            <div class="mb-6 bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 rounded-2xl p-4 border border-orange-200 dark:border-orange-700/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-orange-900 dark:text-orange-100">Pending Grading</h3>
                            <p class="text-xs text-orange-700 dark:text-orange-300">{{ count($pendingGrading) }} submission(s) awaiting your review</p>
                        </div>
                    </div>
                    <button wire:click="setTypeFilter('submission')" class="text-sm font-medium text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300">
                        View All →
                    </button>
                </div>
            </div>
        @endif

        <!-- Notifications List -->
        <div class="space-y-4">
            @forelse($notifications as $notification)
                <div wire:key="notification-{{ $notification['id'] }}" class="group relative {{ $notification['is_read'] ? 'bg-white/70 dark:bg-gray-800/70' : 'bg-white dark:bg-gray-800' }} backdrop-blur-sm rounded-2xl border {{ $notification['is_read'] ? 'border-gray-200/50 dark:border-gray-700/50' : 'border-l-4 border-l-emerald-500 border-gray-200 dark:border-gray-700' }} shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden">
                    <!-- Unread Indicator -->
                    @if(!$notification['is_read'])
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-emerald-500 to-teal-600"></div>
                    @endif

                    <div class="p-6">
                        <div class="flex items-start space-x-4">
                            <!-- Icon -->
                            <div class="flex-shrink-0 relative">
                                @if($notification['type'] === 'assignment')
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253z"/>
                                        </svg>
                                    </div>
                                @elseif($notification['type'] === 'submission')
                                    <div class="w-12 h-12 bg-gradient-to-br {{ $notification['needs_grading'] ?? false ? 'from-orange-400 to-orange-600' : 'from-green-400 to-green-600' }} rounded-2xl flex items-center justify-center shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    @if($notification['needs_grading'] ?? false)
                                        <div class="absolute -top-1 -right-1 w-4 h-4 bg-orange-500 rounded-full flex items-center justify-center">
                                            <span class="text-xs text-white font-bold">!</span>
                                        </div>
                                    @endif
                                @else
                                    <div class="w-12 h-12 bg-gradient-to-br from-gray-400 to-gray-600 rounded-2xl flex items-center justify-center shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $notification['title'] }}
                                            </h3>
                                            @if(!$notification['is_read'])
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300">
                                                    New
                                                </span>
                                            @endif
                                            @if($notification['needs_grading'] ?? false)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300">
                                                    Needs Grading
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-gray-600 dark:text-gray-400 mb-3 leading-relaxed">
                                            {{ $notification['message'] }}
                                        </p>
                                        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-gray-500 dark:text-gray-400">
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span>{{ $notification['created_at']?->diffForHumans() ?? 'Unknown' }}</span>
                                            </div>
                                            @if($notification['subject'])
                                                <span class="text-gray-300 dark:text-gray-600">•</span>
                                                <span class="text-gray-500 dark:text-gray-400">{{ $notification['subject'] }}</span>
                                            @endif
                                            @if($notification['student'])
                                                <span class="text-gray-300 dark:text-gray-600">•</span>
                                                <span class="text-gray-500 dark:text-gray-400">{{ $notification['student'] }}</span>
                                            @endif
                                            @if(isset($notification['score']) && $notification['score'] !== null)
                                                <span class="text-gray-300 dark:text-gray-600">•</span>
                                                <span class="font-medium {{ $notification['score'] >= ($notification['total_marks'] * 0.5) ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                    Score: {{ $notification['score'] }}/{{ $notification['total_marks'] }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex items-center space-x-2 ml-4 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        @if(!$notification['is_read'])
                                            <button
                                                wire:click="markNotificationAsRead('{{ $notification['id'] }}', '{{ $notification['type'] }}')"
                                                class="p-2 text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition-colors duration-200"
                                                title="Mark as read">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </button>
                                        @endif

                                        @if($notification['type'] === 'system')
                                            <button
                                                wire:click="deleteNotification('{{ $notification['id'] }}', '{{ $notification['type'] }}')"
                                                wire:confirm="Are you sure you want to delete this notification?"
                                                class="p-2 text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors duration-200"
                                                title="Delete notification">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="text-center py-20">
                    <div class="relative mb-8">
                        <div class="w-24 h-24 mx-auto bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-3xl flex items-center justify-center shadow-inner">
                            <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        @if($activeTab === 'unread' && $unreadCount === 0)
                            <div class="absolute top-0 right-1/3 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-3">
                        @if($search)
                            No matching notifications
                        @elseif($activeTab === 'unread')
                            You're all caught up!
                        @elseif($typeFilter !== 'all')
                            No {{ $typeFilter }} notifications
                        @else
                            No notifications yet
                        @endif
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto leading-relaxed">
                        @if($search)
                            No notifications match your search "{{ $search }}". Try a different search term.
                        @elseif($activeTab === 'unread')
                            Great job! You've read all your notifications. Check back later for new updates.
                        @elseif($typeFilter !== 'all')
                            You don't have any {{ $typeFilter }} notifications at the moment.
                        @else
                            When students submit assignments or you receive system updates, they'll appear here.
                        @endif
                    </p>
                    @if($search || $activeTab !== 'all' || $typeFilter !== 'all')
                        <button
                            wire:click="clearFilters"
                            class="mt-6 px-6 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-medium hover:bg-emerald-700 transition-colors duration-200">
                            Clear Filters
                        </button>
                    @endif
                </div>
            @endforelse
        </div>
    </div>

    <!-- Loading State -->
    <div wire:loading.delay class="fixed inset-0 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-xl border border-gray-200/50 dark:border-gray-700/50">
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <div class="w-8 h-8 border-3 border-emerald-200 dark:border-emerald-800 border-t-emerald-600 rounded-full animate-spin"></div>
                </div>
                <div>
                    <p class="text-gray-900 dark:text-gray-100 font-medium">Loading notifications...</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">This won't take long</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .border-3 { border-width: 3px; }
    </style>
</div>
