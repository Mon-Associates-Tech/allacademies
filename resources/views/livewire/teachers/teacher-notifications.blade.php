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
                            @if($this->getUnreadCount() > 0)
                                <div class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 rounded-full flex items-center justify-center animate-pulse">
                                    <span class="text-xs font-bold text-white">{{ $this->getUnreadCount() > 9 ? '9+' : $this->getUnreadCount() }}</span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 dark:from-gray-100 dark:to-gray-300 bg-clip-text text-transparent">
                                Notifications
                            </h1>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Stay updated with assignments, submissions, and announcements
                            </p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-3">
                        @if($this->getUnreadCount() > 0)
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
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium {{ $this->getUnreadCount() > 0 ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' : 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' }}">
                            {{ $this->getUnreadCount() }} unread
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filter Tabs -->
        <div class="mb-6">
            <div class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-200/50 dark:border-gray-700/50 overflow-hidden">
                <nav class="flex flex-wrap" aria-label="Tabs">
                    <button
                        wire:click="$set('filter', 'all')"
                        class="flex-1 sm:flex-none px-6 py-4 text-sm font-medium border-b-2 transition-all duration-200 {{ $filter === 'all' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 bg-emerald-50/50 dark:bg-emerald-900/20' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
                        All Notifications
                        <span class="ml-2 px-2.5 py-0.5 rounded-full text-xs {{ $filter === 'all' ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-300' }}">
                            {{ count($notifications) }}
                        </span>
                    </button>
                    <button
                        wire:click="$set('filter', 'generic')"
                        class="flex-1 sm:flex-none px-6 py-4 text-sm font-medium border-b-2 transition-all duration-200 {{ $filter === 'generic' ? 'border-blue-500 text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/20' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
                        🔔 General
                        <span class="ml-2 px-2.5 py-0.5 rounded-full text-xs {{ $filter === 'generic' ? 'bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-300' }}">
                            {{ $this->getNotificationsByType('generic') }}
                        </span>
                    </button>
                    <button
                        wire:click="$set('filter', 'assignments')"
                        class="flex-1 sm:flex-none px-6 py-4 text-sm font-medium border-b-2 transition-all duration-200 {{ $filter === 'assignments' ? 'border-green-500 text-green-600 dark:text-green-400 bg-green-50/50 dark:bg-green-900/20' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
                        📚 Assignments
                        <span class="ml-2 px-2.5 py-0.5 rounded-full text-xs {{ $filter === 'assignments' ? 'bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-300' }}">
                            {{ $this->getNotificationsByType('assignment') }}
                        </span>
                    </button>
                    <button
                        wire:click="$set('filter', 'submissions')"
                        class="flex-1 sm:flex-none px-6 py-4 text-sm font-medium border-b-2 transition-all duration-200 {{ $filter === 'submissions' ? 'border-purple-500 text-purple-600 dark:text-purple-400 bg-purple-50/50 dark:bg-purple-900/20' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
                        📝 Submissions
                        <span class="ml-2 px-2.5 py-0.5 rounded-full text-xs {{ $filter === 'submissions' ? 'bg-purple-100 dark:bg-purple-900/40 text-purple-800 dark:text-purple-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-300' }}">
                            {{ $this->getNotificationsByType('submission') }}
                        </span>
                    </button>
                </nav>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="space-y-4">
            @if(empty($notifications))
                <!-- Empty State -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/50 dark:border-gray-700/50 overflow-hidden">
                    <div class="text-center py-16 px-4">
                        <div class="relative mb-6">
                            <div class="w-20 h-20 mx-auto bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-3xl flex items-center justify-center shadow-inner">
                                <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </div>
                            <div class="absolute top-0 right-1/3 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">No notifications</h3>
                        <p class="text-gray-600 dark:text-gray-400 max-w-sm mx-auto">You're all caught up! When students submit assignments or you receive updates, they'll appear here.</p>
                    </div>
                </div>
            @else
                @foreach($notifications as $notification)
                    <div
                        wire:key="notification-{{ $notification['id'] }}"
                        class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/50 dark:border-gray-700/50 overflow-hidden hover:shadow-lg transition-all duration-300 cursor-pointer {{ $notification['read_at'] ? 'opacity-80' : '' }}"
                        wire:click="showNotificationDetails('{{ $notification['id'] }}')"
                    >
                        <!-- Unread Indicator -->
                        @if(!$notification['read_at'])
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-emerald-500 to-teal-600"></div>
                        @endif

                        <div class="p-6">
                            <div class="flex items-start space-x-4">
                                <!-- Icon -->
                                <div class="flex-shrink-0">
                                    @if($notification['type'] === 'generic')
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                            </svg>
                                        </div>
                                    @elseif($notification['type'] === 'assignment')
                                        <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </div>
                                    @elseif($notification['type'] === 'submission')
                                        <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center space-x-2">
                                            <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100 truncate">
                                                {{ $notification['title'] }}
                                            </h4>
                                            @if(!$notification['read_at'])
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300">
                                                    New
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $notification['type'] === 'generic' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' :
                                                   ($notification['type'] === 'assignment' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300') }}">
                                                {{ $notification['category'] }}
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                        {{ $notification['message'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Notification Details Modal -->
    @if($showModal && $selectedNotification)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/60 dark:bg-gray-900/80 backdrop-blur-sm transition-opacity" wire:click="closeModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-200/50 dark:border-gray-700/50">
                    <!-- Modal Header -->
                    <div class="relative bg-gradient-to-r {{ $selectedNotification['type'] === 'assignment' ? 'from-green-500 to-emerald-600' : ($selectedNotification['type'] === 'submission' ? 'from-purple-500 to-purple-600' : 'from-blue-500 to-blue-600') }} px-6 py-5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                @if($selectedNotification['type'] === 'generic')
                                    <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                    </div>
                                @elseif($selectedNotification['type'] === 'assignment')
                                    <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                @elseif($selectedNotification['type'] === 'submission')
                                    <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <h3 class="text-lg font-bold text-white">
                                        {{ $selectedNotification['title'] }}
                                    </h3>
                                    <p class="text-sm text-white/80">
                                        {{ \Carbon\Carbon::parse($selectedNotification['created_at'])->format('M j, Y \a\t g:i A') }}
                                    </p>
                                </div>
                            </div>
                            <button wire:click="closeModal" class="text-white/80 hover:text-white transition-colors p-2 hover:bg-white/10 rounded-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Content -->
                    <div class="px-6 py-6 space-y-6">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">Message</h4>
                            <p class="text-gray-600 dark:text-gray-400 leading-relaxed">{{ $selectedNotification['message'] }}</p>
                        </div>

                        @if($selectedNotification['type'] === 'assignment')
                            <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-5 border border-green-200 dark:border-green-700/50">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Assignment Details
                                </h4>
                                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-3">
                                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Subject</dt>
                                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $selectedNotification['data']['subject'] }}</dd>
                                    </div>
                                    <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-3">
                                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Student</dt>
                                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $selectedNotification['data']['student_name'] }}</dd>
                                    </div>
                                    @if($selectedNotification['data']['due_date'])
                                        <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-3">
                                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Due Date</dt>
                                            <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($selectedNotification['data']['due_date'])->format('M j, Y g:i A') }}</dd>
                                        </div>
                                    @endif
                                    <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-3">
                                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</dt>
                                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ ucfirst($selectedNotification['data']['assignment_status']) }}</dd>
                                    </div>
                                </dl>
                            </div>
                        @elseif($selectedNotification['type'] === 'submission')
                            <div class="bg-gradient-to-br from-purple-50 to-violet-50 dark:from-purple-900/20 dark:to-violet-900/20 rounded-xl p-5 border border-purple-200 dark:border-purple-700/50">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Submission Details
                                </h4>
                                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-3">
                                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Student</dt>
                                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $selectedNotification['data']['student_name'] }}</dd>
                                    </div>
                                    <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-3">
                                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Assignment</dt>
                                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $selectedNotification['data']['assignment_title'] }}</dd>
                                    </div>
                                    @if($selectedNotification['data']['score'])
                                        <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-3">
                                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Score</dt>
                                            <dd class="mt-1 text-sm font-semibold {{ $selectedNotification['data']['score'] >= ($selectedNotification['data']['total_marks'] * 0.5) ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                {{ $selectedNotification['data']['score'] }}/{{ $selectedNotification['data']['total_marks'] }}
                                            </dd>
                                        </div>
                                    @endif
                                    <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-3">
                                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Time Spent</dt>
                                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $selectedNotification['data']['time_spent'] }} minutes</dd>
                                    </div>
                                </dl>
                            </div>
                        @endif
                    </div>

                    <!-- Modal Actions -->
                    <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 flex flex-col sm:flex-row justify-end gap-3">
                        @if(!$selectedNotification['read_at'])
                            <button
                                wire:click="markAsRead('{{ $selectedNotification['id'] }}')"
                                class="inline-flex items-center justify-center px-4 py-2.5 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-xl text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Mark as Read
                            </button>
                        @endif
                        <button
                            wire:click="closeModal"
                            class="inline-flex items-center justify-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all shadow-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Loading State -->
    <div wire:loading.delay class="fixed inset-0 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-xl border border-gray-200/50 dark:border-gray-700/50">
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <div class="w-8 h-8 border-3 border-emerald-200 dark:border-emerald-800 border-t-emerald-600 rounded-full animate-spin"></div>
                </div>
                <div>
                    <p class="text-gray-900 dark:text-gray-100 font-medium">Loading...</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Please wait</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .border-3 { border-width: 3px; }
    </style>
</div>
