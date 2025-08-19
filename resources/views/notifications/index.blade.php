<x-layouts.app>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Page header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Notifications</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">Stay updated with your latest notifications</p>
            </div>

            <!-- Mark all as read button -->
            <x-button.primary
                onclick="markAllAsRead()"
                class="btn bg-violet-500 hover:bg-violet-600 text-white"
            >
                <svg class="fill-current shrink-0 xs:hidden me-2" width="16" height="16" viewBox="0 0 16 16">
                    <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"/>
                </svg>
                <span class="max-xs:sr-only">Mark All Read</span>
            </x-button.primary>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl">
            <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                <h2 class="font-semibold text-gray-800 dark:text-gray-100">All Notifications</h2>
            </header>
            <div class="p-3">

                <!-- Generic Notifications -->
                @if($notifications && $notifications->count() > 0)
                    @foreach($notifications as $notification)
                        <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150 {{ is_null($notification['read_at']) ? 'bg-blue-50/50 dark:bg-blue-900/10 border-l-4 border-blue-500' : '' }}">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center">
                                    @if($notification['type'] === 'assignment' || str_contains($notification['notification_type'], 'Assignment'))
                                        📚
                                    @else
                                        📣
                                    @endif
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $notification['title'] }}
                                        @if(is_null($notification['read_at']))
                                            <span class="inline-block w-2 h-2 bg-blue-500 rounded-full ml-2"></span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $notification['created_at']->diffForHumans() }}
                                    </p>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    {{ Str::limit($notification['message'], 120) }}
                                </p>
                                <div class="mt-2">
                                    <a href="{{ route('notifications.show', ['type' => $notification['type'], 'id' => $notification['id']]) }}"
                                       class="text-xs text-violet-600 hover:text-violet-700 dark:text-violet-400 dark:hover:text-violet-300">
                                        Read more →
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Pagination for generic notifications -->
                    <div class="mt-6">
                        {{ $notifications->links() }}
                    </div>
                @endif

                <!-- Assignment Notifications -->
                @if($assignmentNotifications && $assignmentNotifications->count() > 0)
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Assignment Notifications</h3>
                        @foreach($assignmentNotifications as $notification)
                            <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150 {{ is_null($notification['read_at']) ? 'bg-blue-50/50 dark:bg-blue-900/10 border-l-4 border-blue-500' : '' }}">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                        📚
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $notification['title'] }}
                                            @if(is_null($notification['read_at']))
                                                <span class="inline-block w-2 h-2 bg-blue-500 rounded-full ml-2"></span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $notification['created_at']->diffForHumans() }}
                                        </p>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        {{ Str::limit($notification['message'], 120) }}
                                    </p>
                                    <div class="mt-2">
                                        <a href="{{ route('notifications.show', ['type' => $notification['type'], 'id' => $notification['id']]) }}"
                                           class="text-xs text-violet-600 hover:text-violet-700 dark:text-violet-400 dark:hover:text-violet-300">
                                            Read more →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Pagination for assignment notifications -->
                        <div class="mt-6">
                            {{ $assignmentNotifications->links() }}
                        </div>
                    </div>
                @endif

                <!-- Empty state -->
                @if((!$notifications || $notifications->count() === 0) && (!$assignmentNotifications || $assignmentNotifications->count() === 0))
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 17h5l-5 5v-5zM13.5 6H5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2V9.5L13.5 6z" />
                        </svg>
                        <h3 class="mt-4 text-sm font-medium text-gray-900 dark:text-gray-100">No notifications</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">You're all caught up! No new notifications.</p>
                    </div>
                @endif
            </div>
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
                });
        }
    </script>
</x-layouts.app>
