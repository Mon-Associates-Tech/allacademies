<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Stay updated with assignments, submissions, and important announcements
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    @if($this->getUnreadCount() > 0)
                        <button
                            wire:click="markAllAsRead"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Mark All Read
                        </button>
                    @endif
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        {{ $this->getUnreadCount() }} unread
                    </span>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                    <button
                        wire:click="$set('filter', 'all')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $filter === 'all' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                    >
                        All Notifications
                        <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs">
                            {{ count($notifications) }}
                        </span>
                    </button>
                    <button
                        wire:click="$set('filter', 'generic')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $filter === 'generic' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                    >
                        General
                        <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs">
                            {{ $this->getNotificationsByType('generic') }}
                        </span>
                    </button>
                    <button
                        wire:click="$set('filter', 'assignments')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $filter === 'assignments' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                    >
                        Assignments
                        <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs">
                            {{ $this->getNotificationsByType('assignment') }}
                        </span>
                    </button>
                    <button
                        wire:click="$set('filter', 'submissions')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $filter === 'submissions' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                    >
                        Submissions
                        <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs">
                            {{ $this->getNotificationsByType('submission') }}
                        </span>
                    </button>
                </nav>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
            @if(empty($notifications))
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 17H4l5 5v-5zM12 3v18" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No notifications</h3>
                    <p class="mt-1 text-sm text-gray-500">You're all caught up!</p>
                </div>
            @else
                <div class="divide-y divide-gray-200">
                    @foreach($notifications as $notification)
                        <div
                            class="p-6 hover:bg-gray-50 cursor-pointer transition-colors duration-200 {{ $notification['read_at'] ? 'opacity-75' : '' }}"
                            wire:click="showNotificationDetails('{{ $notification['id'] }}')"
                        >
                            <div class="flex items-start space-x-4">
                                <!-- Icon -->
                                <div class="flex-shrink-0">
                                    @if($notification['type'] === 'generic')
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 17H4l5 5v-5zM12 3v18" />
                                            </svg>
                                        </div>
                                    @elseif($notification['type'] === 'assignment')
                                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                        </div>
                                    @elseif($notification['type'] === 'submission')
                                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <h4 class="text-sm font-medium text-gray-900 truncate">
                                                {{ $notification['title'] }}
                                            </h4>
                                            @if(!$notification['read_at'])
                                                <span class="inline-block w-2 h-2 bg-blue-500 rounded-full"></span>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $notification['type'] === 'generic' ? 'bg-blue-100 text-blue-800' :
                                                   ($notification['type'] === 'assignment' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800') }}">
                                                {{ $notification['category'] }}
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-600 line-clamp-2">
                                        {{ $notification['message'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Notification Details Modal -->
    @if($showModal && $selectedNotification)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full">
                            <!-- Modal Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    @if($selectedNotification['type'] === 'generic')
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 17H4l5 5v-5zM12 3v18" />
                                            </svg>
                                        </div>
                                    @elseif($selectedNotification['type'] === 'assignment')
                                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                        </div>
                                    @elseif($selectedNotification['type'] === 'submission')
                                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">
                                            {{ $selectedNotification['title'] }}
                                        </h3>
                                        <p class="text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($selectedNotification['created_at'])->format('M j, Y \a\t g:i A') }}
                                        </p>
                                    </div>
                                </div>
                                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Modal Content -->
                            <div class="space-y-4">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Message</h4>
                                    <p class="text-sm text-gray-600">{{ $selectedNotification['message'] }}</p>
                                </div>

                                @if($selectedNotification['type'] === 'assignment')
                                    <div class="bg-green-50 p-4 rounded-lg">
                                        <h4 class="text-sm font-medium text-gray-900 mb-2">Assignment Details</h4>
                                        <dl class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                            <div>
                                                <dt class="text-xs font-medium text-gray-500">Subject</dt>
                                                <dd class="text-sm text-gray-900">{{ $selectedNotification['data']['subject'] }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-xs font-medium text-gray-500">Student</dt>
                                                <dd class="text-sm text-gray-900">{{ $selectedNotification['data']['student_name'] }}</dd>
                                            </div>
                                            @if($selectedNotification['data']['due_date'])
                                                <div>
                                                    <dt class="text-xs font-medium text-gray-500">Due Date</dt>
                                                    <dd class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($selectedNotification['data']['due_date'])->format('M j, Y g:i A') }}</dd>
                                                </div>
                                            @endif
                                            <div>
                                                <dt class="text-xs font-medium text-gray-500">Status</dt>
                                                <dd class="text-sm text-gray-900">{{ ucfirst($selectedNotification['data']['assignment_status']) }}</dd>
                                            </div>
                                        </dl>
                                    </div>
                                @elseif($selectedNotification['type'] === 'submission')
                                    <div class="bg-purple-50 p-4 rounded-lg">
                                        <h4 class="text-sm font-medium text-gray-900 mb-2">Submission Details</h4>
                                        <dl class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                            <div>
                                                <dt class="text-xs font-medium text-gray-500">Student</dt>
                                                <dd class="text-sm text-gray-900">{{ $selectedNotification['data']['student_name'] }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-xs font-medium text-gray-500">Assignment</dt>
                                                <dd class="text-sm text-gray-900">{{ $selectedNotification['data']['assignment_title'] }}</dd>
                                            </div>
                                            @if($selectedNotification['data']['score'])
                                                <div>
                                                    <dt class="text-xs font-medium text-gray-500">Score</dt>
                                                    <dd class="text-sm text-gray-900">{{ $selectedNotification['data']['score'] }}/{{ $selectedNotification['data']['total_marks'] }}</dd>
                                                </div>
                                            @endif
                                            <div>
                                                <dt class="text-xs font-medium text-gray-500">Time Spent</dt>
                                                <dd class="text-sm text-gray-900">{{ $selectedNotification['data']['time_spent'] }} minutes</dd>
                                            </div>
                                        </dl>
                                    </div>
                                @endif
                            </div>

                            <!-- Modal Actions -->
                            <div class="flex justify-end space-x-3 mt-6">
                                @if(!$selectedNotification['read_at'])
                                    <button
                                        wire:click="markAsRead('{{ $selectedNotification['id'] }}')"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    >
                                        Mark as Read
                                    </button>
                                @endif
                                <button
                                    wire:click="closeModal"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                >
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
