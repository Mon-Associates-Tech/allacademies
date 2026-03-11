<x-layouts.app>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-gray-50 to-slate-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto">

            <!-- Header Section -->
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-200/50 dark:border-gray-700/50 p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl shadow-lg">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </div>
                            @if($counts['unread'] > 0)
                                <div class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 rounded-full flex items-center justify-center animate-pulse">
                                    <span class="text-xs font-bold text-white">{{ $counts['unread'] > 9 ? '9+' : $counts['unread'] }}</span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 dark:from-gray-100 dark:to-gray-300 bg-clip-text text-transparent">
                                Notifications
                            </h1>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Stay updated with your latest activities and announcements
                            </p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-3">
                        @if($counts['unread'] > 0)
                            <button
                                onclick="markAllAsRead()"
                                class="group relative px-4 py-2.5 bg-gradient-to-r from-violet-500 to-purple-600 text-white rounded-xl text-sm font-medium hover:from-violet-600 hover:to-purple-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <span class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span>Mark All Read</span>
                                </span>
                            </button>
                        @endif
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium {{ $counts['unread'] > 0 ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' : 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' }}">
                            {{ $counts['unread'] }} unread
                        </span>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6">
                    <div class="bg-gradient-to-br from-violet-50 to-purple-50 dark:from-violet-900/20 dark:to-purple-900/20 rounded-xl p-4 border border-violet-200/50 dark:border-violet-700/50">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-violet-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-violet-900 dark:text-violet-100">{{ $counts['all'] }}</p>
                                <p class="text-xs text-violet-600 dark:text-violet-400">Total</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-xl p-4 border border-blue-200/50 dark:border-blue-700/50">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $counts['assignment'] }}</p>
                                <p class="text-xs text-blue-600 dark:text-blue-400">Assignments</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-4 border border-purple-200/50 dark:border-purple-700/50">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">{{ $counts['assessment'] }}</p>
                                <p class="text-xs text-purple-600 dark:text-purple-400">Assessments</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-gray-50 to-slate-50 dark:from-gray-800/50 dark:to-slate-800/50 rounded-xl p-4 border border-gray-200/50 dark:border-gray-700/50">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gray-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $counts['other'] }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Other</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/50 dark:border-gray-700/50 p-4 mb-6">
                <!-- Search Bar -->
                <form method="GET" action="{{ route('notifications.index') }}" class="mb-4">
                    <input type="hidden" name="filter" value="{{ $filter }}">
                    <input type="hidden" name="type" value="{{ $type }}">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all duration-200"
                            placeholder="Search notifications...">
                        @if($search)
                            <a href="{{ route('notifications.index', ['filter' => $filter, 'type' => $type]) }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </form>

                <!-- Filter Tabs - Read Status -->
                <div class="flex flex-wrap gap-2 mb-4">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400 self-center mr-2">Status:</span>
                    <a href="{{ route('notifications.index', ['filter' => 'all', 'type' => $type, 'search' => $search]) }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ $filter === 'all' ? 'bg-violet-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        All
                        <span class="ml-1 px-2 py-0.5 text-xs rounded-full {{ $filter === 'all' ? 'bg-white/20' : 'bg-gray-200 dark:bg-gray-600' }}">{{ $counts['all'] }}</span>
                    </a>
                    <a href="{{ route('notifications.index', ['filter' => 'unread', 'type' => $type, 'search' => $search]) }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ $filter === 'unread' ? 'bg-violet-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        Unread
                        <span class="ml-1 px-2 py-0.5 text-xs rounded-full {{ $filter === 'unread' ? 'bg-white/20' : 'bg-gray-200 dark:bg-gray-600' }}">{{ $counts['unread'] }}</span>
                    </a>
                    <a href="{{ route('notifications.index', ['filter' => 'read', 'type' => $type, 'search' => $search]) }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ $filter === 'read' ? 'bg-violet-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        Read
                        <span class="ml-1 px-2 py-0.5 text-xs rounded-full {{ $filter === 'read' ? 'bg-white/20' : 'bg-gray-200 dark:bg-gray-600' }}">{{ $counts['read'] }}</span>
                    </a>
                </div>

                <!-- Filter Tabs - Type -->
                <div class="flex flex-wrap gap-2">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400 self-center mr-2">Type:</span>
                    <a href="{{ route('notifications.index', ['filter' => $filter, 'type' => 'all', 'search' => $search]) }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ $type === 'all' ? 'bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 shadow-sm' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        📋 All Types
                    </a>
                    <a href="{{ route('notifications.index', ['filter' => $filter, 'type' => 'assignment', 'search' => $search]) }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ $type === 'assignment' ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        📚 Assignments
                        <span class="ml-1 px-2 py-0.5 text-xs rounded-full {{ $type === 'assignment' ? 'bg-white/20' : 'bg-gray-200 dark:bg-gray-600' }}">{{ $counts['assignment'] }}</span>
                    </a>
                    <a href="{{ route('notifications.index', ['filter' => $filter, 'type' => 'assessment', 'search' => $search]) }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ $type === 'assessment' ? 'bg-purple-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        📝 Assessments
                        <span class="ml-1 px-2 py-0.5 text-xs rounded-full {{ $type === 'assessment' ? 'bg-white/20' : 'bg-gray-200 dark:bg-gray-600' }}">{{ $counts['assessment'] }}</span>
                    </a>
                    <a href="{{ route('notifications.index', ['filter' => $filter, 'type' => 'other', 'search' => $search]) }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ $type === 'other' ? 'bg-gray-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        📣 Other
                        <span class="ml-1 px-2 py-0.5 text-xs rounded-full {{ $type === 'other' ? 'bg-white/20' : 'bg-gray-200 dark:bg-gray-600' }}">{{ $counts['other'] }}</span>
                    </a>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="space-y-4">
                @forelse($notifications as $notification)
                    <div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/50 dark:border-gray-700/50 overflow-hidden hover:shadow-lg transition-all duration-300 {{ is_null($notification['read_at']) ? 'ring-2 ring-violet-500/20' : '' }}">
                        <!-- Unread Indicator -->
                        @if(is_null($notification['read_at']))
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-violet-500 to-purple-600"></div>
                        @endif

                        <div class="p-5 sm:p-6">
                            <div class="flex items-start space-x-4">
                                <!-- Icon -->
                                <div class="flex-shrink-0">
                                    @if($notification['type'] === 'assignment')
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </div>
                                    @elseif($notification['type'] === 'assessment')
                                        <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-12 h-12 bg-gradient-to-br from-gray-400 to-gray-600 rounded-2xl flex items-center justify-center shadow-lg">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-2">
                                        <div class="flex items-center space-x-2 flex-wrap gap-y-1">
                                            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $notification['title'] }}
                                            </h3>
                                            @if(is_null($notification['read_at']))
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 dark:bg-violet-900/30 text-violet-800 dark:text-violet-300">
                                                    New
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $notification['type'] === 'assignment' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' :
                                                   ($notification['type'] === 'assessment' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300') }}">
                                                {{ ucfirst($notification['type']) }}
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                                {{ $notification['created_at']->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>

                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">
                                        {{ Str::limit($notification['message'], 150) }}
                                    </p>

                                    <!-- Additional Info -->
                                    @if(!empty($notification['data']))
                                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500 dark:text-gray-400 mb-3">
                                            @if(!empty($notification['data']['subject']))
                                                <span class="flex items-center">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                    </svg>
                                                    {{ $notification['data']['subject'] }}
                                                </span>
                                            @endif
                                            @if(!empty($notification['data']['teacher']))
                                                <span class="flex items-center">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                    {{ $notification['data']['teacher'] }}
                                                </span>
                                            @endif
                                            @if(!empty($notification['data']['score']))
                                                <span class="flex items-center font-medium {{ $notification['data']['score'] >= 70 ? 'text-green-600 dark:text-green-400' : ($notification['data']['score'] >= 50 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                                    </svg>
                                                    Score: {{ $notification['data']['score'] }}%
                                                </span>
                                            @endif
                                        </div>
                                    @endif

                                    <!-- Action Button -->
                                    <a href="{{ route('notifications.show', ['type' => $notification['type'], 'id' => $notification['id']]) }}"
                                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-violet-600 dark:text-violet-400 bg-violet-50 dark:bg-violet-900/20 rounded-lg hover:bg-violet-100 dark:hover:bg-violet-900/40 transition-colors duration-200">
                                        View Details
                                        <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Empty State -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/50 dark:border-gray-700/50 overflow-hidden">
                        <div class="text-center py-16 px-4">
                            <div class="relative mb-6">
                                <div class="w-24 h-24 mx-auto bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-3xl flex items-center justify-center shadow-inner">
                                    <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                </div>
                                @if($filter === 'all' && $type === 'all' && empty($search))
                                    <div class="absolute top-0 right-1/3 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center shadow-lg">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                @if($search)
                                    No results found
                                @elseif($filter === 'unread')
                                    All caught up!
                                @else
                                    No notifications
                                @endif
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 max-w-sm mx-auto mb-6">
                                @if($search)
                                    No notifications match your search "{{ $search }}". Try a different search term.
                                @elseif($filter === 'unread')
                                    You've read all your notifications. Great job staying on top of things!
                                @elseif($type !== 'all')
                                    No {{ $type }} notifications found. Check back later for updates.
                                @else
                                    You don't have any notifications yet. When you receive updates, they'll appear here.
                                @endif
                            </p>
                            @if($search || $filter !== 'all' || $type !== 'all')
                                <a href="{{ route('notifications.index') }}"
                                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-violet-600 dark:text-violet-400 bg-violet-50 dark:bg-violet-900/20 rounded-lg hover:bg-violet-100 dark:hover:bg-violet-900/40 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Clear Filters
                                </a>
                            @endif
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function markAllAsRead() {
            fetch('{{ route("notifications.mark-all-read") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
</x-layouts.app>
