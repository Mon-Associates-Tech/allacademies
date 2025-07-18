<div>
    <!-- Header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Notifications</h1>
            <p class="text-gray-600 dark:text-gray-400">Stay updated with your ward's academic activities and important announcements</p>
        </div>
        <div class="flex items-center space-x-3">
            @if($this->hasUnreadNotifications)
                <button wire:click="$set('showMarkAllModal', true)"
                        class="bg-violet-500 hover:bg-violet-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Mark All as Read
                </button>
            @endif

            @if(!empty($selectedNotifications))
                <button wire:click="markSelectedAsRead"
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Mark Selected as Read ({{ count($selectedNotifications) }})
                </button>
            @endif
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-1 mb-6">
        <div class="flex flex-wrap gap-1">
            <button wire:click="filterNotifications('all')"
                    class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $filter === 'all' ? 'bg-violet-500 text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100' }}">
                All ({{ $this->notificationCounts['all'] }})
            </button>
            <button wire:click="filterNotifications('unread')"
                    class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $filter === 'unread' ? 'bg-violet-500 text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100' }}">
                Unread ({{ $this->notificationCounts['unread'] }})
            </button>
            <button wire:click="filterNotifications('read')"
                    class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $filter === 'read' ? 'bg-violet-500 text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100' }}">
                Read ({{ $this->notificationCounts['read'] }})
            </button>
            <button wire:click="filterNotifications('academic')"
                    class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $filter === 'academic' ? 'bg-violet-500 text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100' }}">
                Academic ({{ $this->notificationCounts['academic'] }})
            </button>
            <button wire:click="filterNotifications('system')"
                    class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $filter === 'system' ? 'bg-violet-500 text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100' }}">
                System ({{ $this->notificationCounts['system'] }})
            </button>
            <button wire:click="filterNotifications('alerts')"
                    class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $filter === 'alerts' ? 'bg-violet-500 text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100' }}">
                Alerts ({{ $this->notificationCounts['alerts'] }})
            </button>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="space-y-4">
        @forelse($this->notifications as $notification)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700
                        {{ $notification->read_at ? 'opacity-75' : 'border-l-4 border-l-violet-500' }}">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-1">
                                <input type="checkbox"
                                       wire:change="toggleNotificationSelection('{{ $notification->id }}')"
                                       {{ in_array($notification->id, $selectedNotifications) ? 'checked' : '' }}
                                       class="h-4 w-4 text-violet-600 focus:ring-violet-500 border-gray-300 rounded">
                            </div>

                            <div class="flex-shrink-0">
                                @if(str_contains($notification->type, 'Academic'))
                                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                @elseif(str_contains($notification->type, 'System'))
                                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                @elseif(str_contains($notification->type, 'Alert'))
                                    <div class="w-10 h-10 bg-red-100 dark:bg-red-900/20 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-3.403-3.403A2.996 2.996 0 0118 12c0-1.657-1.343-3-3-3s-3 1.343-3 3 1.343 3 3 3c.6 0 1.158-.175 1.628-.462L20 17h-5z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2 mb-1">
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $notification->data['title'] ?? 'Notification' }}
                                    </h3>
                                    @if(!$notification->read_at)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-800 dark:bg-violet-900/20 dark:text-violet-300">
                                            New
                                        </span>
                                    @endif
                                </div>

                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                    {{ $notification->data['message'] ?? 'No message available' }}
                                </p>

                                <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                                    <span>{{ $notification->created_at->diffForHumans() }}</span>
                                    <span>•</span>
                                    <span>{{ class_basename($notification->type) }}</span>
                                    @if($notification->read_at)
                                        <span>•</span>
                                        <span>Read {{ $notification->read_at->diffForHumans() }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            @if(!$notification->read_at)
                                <button wire:click="markAsRead('{{ $notification->id }}')"
                                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                            @endif

                            <button wire:click="deleteNotification('{{ $notification->id }}')"
                                    wire:confirm="Are you sure you want to delete this notification?"
                                    class="text-gray-400 hover:text-red-600 dark:hover:text-red-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-3.403-3.403A2.996 2.996 0 0118 12c0-1.657-1.343-3-3-3s-3 1.343-3 3 1.343 3 3 3c.6 0 1.158-.175 1.628-.462L20 17h-5z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No notifications</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    @if($filter === 'all')
                        You don't have any notifications yet.
                    @else
                        No {{ $filter }} notifications found.
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $this->notifications->links() }}
    </div>

    <!-- Mark All Modal -->
    @if($showMarkAllModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-violet-100 dark:bg-violet-900/20 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                    Mark All as Read
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Are you sure you want to mark all notifications as read? This action cannot be undone.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="markAllAsRead"
                                type="button"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-violet-600 text-base font-medium text-white hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Mark All as Read
                        </button>
                        <button wire:click="$set('showMarkAllModal', false)"
                                type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
