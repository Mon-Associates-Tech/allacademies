<div>
    <!-- Header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Notifications</h1>
            <p class="text-gray-600 dark:text-gray-400">Stay updated with your ward's academic activities and important announcements</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            @if($this->hasUnreadNotifications)
                <button wire:click="$set('showMarkAllModal', true)"
                        class="bg-violet-600 hover:bg-violet-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Mark All as Read
                </button>
            @endif

            @if(!empty($selectedNotifications))
                <div class="flex items-center gap-2">
                    <button wire:click="markSelectedAsRead"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Mark Selected ({{ count($selectedNotifications) }})
                    </button>
                    <button wire:click="deleteSelected"
                            wire:confirm="Are you sure you want to delete the selected notifications?"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete
                    </button>
                    <button wire:click="deselectAll"
                            class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Deselect All
                    </button>
                </div>
            @else
                <button wire:click="selectAllOnPage"
                        class="text-violet-600 dark:text-violet-400 hover:text-violet-700 dark:hover:text-violet-300 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Select All on Page
                </button>
            @endif
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="mb-6 space-y-4">
        <!-- Search Bar -->
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input wire:model.live.debounce.300ms="search"
                   type="text"
                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent"
                   placeholder="Search notifications...">
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-2">
            <div class="flex flex-wrap gap-2">
                <button wire:click="filterNotifications('all')"
                        class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $filter === 'all' ? 'bg-violet-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    All
                    <span class="ml-1 px-2 py-0.5 text-xs rounded-full {{ $filter === 'all' ? 'bg-white/20' : 'bg-gray-200 dark:bg-gray-700' }}">
                        {{ $this->notificationCounts['all'] }}
                    </span>
                </button>
                <button wire:click="filterNotifications('unread')"
                        class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $filter === 'unread' ? 'bg-violet-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Unread
                    <span class="ml-1 px-2 py-0.5 text-xs rounded-full {{ $filter === 'unread' ? 'bg-white/20' : 'bg-gray-200 dark:bg-gray-700' }}">
                        {{ $this->notificationCounts['unread'] }}
                    </span>
                </button>
                <button wire:click="filterNotifications('read')"
                        class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $filter === 'read' ? 'bg-violet-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Read
                    <span class="ml-1 px-2 py-0.5 text-xs rounded-full {{ $filter === 'read' ? 'bg-white/20' : 'bg-gray-200 dark:bg-gray-700' }}">
                        {{ $this->notificationCounts['read'] }}
                    </span>
                </button>
                <button wire:click="filterNotifications('assignments')"
                        class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $filter === 'assignments' ? 'bg-violet-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Assignments
                    <span class="ml-1 px-2 py-0.5 text-xs rounded-full {{ $filter === 'assignments' ? 'bg-white/20' : 'bg-gray-200 dark:bg-gray-700' }}">
                        {{ $this->notificationCounts['assignments'] }}
                    </span>
                </button>
                <button wire:click="filterNotifications('attendance')"
                        class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $filter === 'attendance' ? 'bg-violet-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Attendance
                    <span class="ml-1 px-2 py-0.5 text-xs rounded-full {{ $filter === 'attendance' ? 'bg-white/20' : 'bg-gray-200 dark:bg-gray-700' }}">
                        {{ $this->notificationCounts['attendance'] }}
                    </span>
                </button>
                <button wire:click="filterNotifications('fees')"
                        class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $filter === 'fees' ? 'bg-violet-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Fees
                    <span class="ml-1 px-2 py-0.5 text-xs rounded-full {{ $filter === 'fees' ? 'bg-white/20' : 'bg-gray-200 dark:bg-gray-700' }}">
                        {{ $this->notificationCounts['fees'] }}
                    </span>
                </button>
                <button wire:click="filterNotifications('grades')"
                        class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $filter === 'grades' ? 'bg-violet-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Grades
                    <span class="ml-1 px-2 py-0.5 text-xs rounded-full {{ $filter === 'grades' ? 'bg-white/20' : 'bg-gray-200 dark:bg-gray-700' }}">
                        {{ $this->notificationCounts['grades'] }}
                    </span>
                </button>
                <button wire:click="filterNotifications('system')"
                        class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $filter === 'system' ? 'bg-violet-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    System
                    <span class="ml-1 px-2 py-0.5 text-xs rounded-full {{ $filter === 'system' ? 'bg-white/20' : 'bg-gray-200 dark:bg-gray-700' }}">
                        {{ $this->notificationCounts['system'] }}
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="space-y-3">
        @forelse($this->notifications as $notification)
            @php
                $iconType = $this->getNotificationIcon($notification);
                $colorType = $this->getNotificationColor($notification);
            @endphp

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md
                        {{ $notification->read_at ? 'opacity-75' : 'border-l-4 border-l-violet-500' }}">
                <div class="p-4">
                    <div class="flex items-start space-x-3">
                        <!-- Checkbox -->
                        <div class="flex-shrink-0 mt-1">
                            <input type="checkbox"
                                   wire:change="toggleNotificationSelection('{{ $notification->id }}')"
                                   {{ in_array($notification->id, $selectedNotifications) ? 'checked' : '' }}
                                   class="h-4 w-4 text-violet-600 focus:ring-violet-500 border-gray-300 rounded cursor-pointer">
                        </div>

                        <!-- Icon -->
                        <div class="flex-shrink-0">
                            @include('livewire.parent.partials.notification-icon', ['type' => $iconType, 'color' => $colorType])
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2 mb-1">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
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

                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                                <span class="flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                                <span>•</span>
                                <span>{{ class_basename($notification->type) }}</span>
                                @if($notification->read_at)
                                    <span>•</span>
                                    <span class="text-green-600 dark:text-green-400">
                                        Read {{ $notification->read_at->diffForHumans() }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center space-x-1">
                            @if(!$notification->read_at)
                                <button wire:click="markAsRead('{{ $notification->id }}')"
                                        title="Mark as read"
                                        class="p-2 text-gray-400 hover:text-green-600 dark:hover:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                            @endif

                            <button wire:click="deleteNotification('{{ $notification->id }}')"
                                    wire:confirm="Are you sure you want to delete this notification?"
                                    title="Delete"
                                    class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-gray-100">No notifications</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    @if($filter === 'all')
                        You don't have any notifications yet.
                    @elseif($search)
                        No notifications match your search "{{ $search }}".
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
                <div wire:click="$set('showMarkAllModal', false)"
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                     aria-hidden="true"></div>

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
                                <h3 class="text-lg leading-6 font-semibold text-gray-900 dark:text-gray-100" id="modal-title">
                                    Mark All as Read
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Are you sure you want to mark all {{ $this->notificationCounts['unread'] }} unread notification(s) as read? This action cannot be undone.
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
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
