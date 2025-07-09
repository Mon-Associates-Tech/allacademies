<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <!-- Header Section -->
    <div class="bg-white/80 backdrop-blur-sm border-b border-gray-200/50 sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl shadow-lg">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM11 16l-4-4h3V6h2v6h3l-4 4zm5-10V3a1 1 0 00-1-1H8a1 1 0 00-1 1v3m8 0H8m8 0v2a1 1 0 001 1h2a1 1 0 001-1v-2m0 0V6a1 1 0 00-1-1h-2a1 1 0 00-1 1v2z"/>
                                </svg>
                            </div>
                            @if($unreadCount > 0)
                                <div class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 rounded-full flex items-center justify-center">
                                    <span class="text-xs font-bold text-white">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                                Notifications
                            </h1>
                            <div class="flex items-center space-x-4 mt-2">
                                @if($unreadCount > 0)
                                    <div class="flex items-center space-x-2">
                                        <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                                        <p class="text-sm font-medium text-blue-600">{{ $unreadCount }} unread notification{{ $unreadCount > 1 ? 's' : '' }}</p>
                                    </div>
                                @else
                                    <div class="flex items-center space-x-2">
                                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                        <p class="text-sm font-medium text-green-600">All caught up!</p>
                                    </div>
                                @endif
                                <span class="text-gray-300">•</span>
                                <p class="text-sm text-gray-500">{{ $notifications->count() }} total</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-3">
                        <button
                            wire:click="toggleShowAll"
                            class="group relative px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 shadow-sm hover:shadow-md">
                            <span class="flex items-center space-x-2">
                                <svg class="w-4 h-4 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                </svg>
                                <span>{{ $showAll ? 'Show Unread' : 'Show All' }}</span>
                            </span>
                        </button>

                        @if($unreadCount > 0)
                            <button
                                wire:click="markAllAsRead"
                                class="group relative px-4 py-2.5 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl text-sm font-medium hover:from-blue-600 hover:to-purple-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <span class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span>Mark All Read</span>
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
        <!-- Filter Tabs -->
        <div class="mb-8">
            <div class="flex space-x-1 bg-white/60 backdrop-blur-sm rounded-2xl p-1 shadow-sm border border-gray-200/50">
                <button
                    wire:click="$set('filterType', 'all')"
                    class="px-4 py-2 text-sm font-medium rounded-xl transition-all duration-200 {{ !isset($filterType) || $filterType === 'all' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                    All Notifications
                </button>
                <button
                    wire:click="$set('filterType', 'unread')"
                    class="px-4 py-2 text-sm font-medium rounded-xl transition-all duration-200 {{ $filterType === 'unread' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                    Unread
                </button>
                <button
                    wire:click="$set('filterType', 'books')"
                    class="px-4 py-2 text-sm font-medium rounded-xl transition-all duration-200 {{ $filterType === 'books' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                    Books
                </button>
                <button
                    wire:click="$set('filterType', 'reviews')"
                    class="px-4 py-2 text-sm font-medium rounded-xl transition-all duration-200 {{ $filterType === 'reviews' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                    Reviews
                </button>
            </div>
        </div>

        <!-- Notifications Grid -->
        <div class="space-y-4">
            @forelse($notifications as $notification)
                <div class="group relative {{ $notification->read_at ? 'bg-white/70' : 'bg-white' }} backdrop-blur-sm rounded-2xl border {{ $notification->read_at ? 'border-gray-200/50' : 'border-blue-200' }} shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden">
                    <!-- Unread Indicator -->
                    @if(!$notification->read_at)
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-blue-500 to-purple-600"></div>
                    @endif

                    <div class="p-6">
                        <div class="flex items-start space-x-4">
                            <!-- Enhanced Icon -->
                            <div class="flex-shrink-0 relative">
                                @if($notification->type === 'App\Notifications\BookPublished')
                                    <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253z"/>
                                        </svg>
                                    </div>
                                    <div class="absolute -top-1 -right-1 w-4 h-4 bg-white rounded-full flex items-center justify-center">
                                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                    </div>
                                @elseif($notification->type === 'App\Notifications\BookReviewed')
                                    <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-12 h-12 bg-gradient-to-br from-gray-400 to-gray-500 rounded-2xl flex items-center justify-center shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Enhanced Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                {{ $notification->data['title'] ?? 'Notification' }}
                                            </h3>
                                            @if(!$notification->read_at)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    New
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-gray-600 mb-3 leading-relaxed">
                                            {{ $notification->data['message'] ?? 'You have a new notification.' }}
                                        </p>
                                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span>{{ $notification->created_at->diffForHumans() }}</span>
                                            </div>
                                            @if($notification->data['category'] ?? false)
                                                <span class="text-gray-300">•</span>
                                                <span class="text-gray-500">{{ $notification->data['category'] }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Enhanced Actions -->
                                    <div class="flex items-center space-x-2 ml-4 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        @if(!$notification->read_at)
                                            <button
                                                wire:click="markAsRead('{{ $notification->id }}')"
                                                class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                                                title="Mark as read">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </button>
                                        @endif

                                        <button
                                            wire:click="deleteNotification('{{ $notification->id }}')"
                                            class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                            title="Delete notification">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Enhanced Empty State -->
                <div class="text-center py-20">
                    <div class="relative mb-8">
                        <div class="w-24 h-24 mx-auto bg-gradient-to-br from-gray-100 to-gray-200 rounded-3xl flex items-center justify-center shadow-inner">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM11 16l-4-4h3V6h2v6h3l-4 4zm5-10V3a1 1 0 00-1-1H8a1 1 0 00-1 1v3m8 0H8m8 0v2a1 1 0 001 1h2a1 1 0 001-1v-2m0 0V6a1 1 0 00-1-1h-2a1 1 0 00-1 1v2z"/>
                            </svg>
                        </div>
                        <div class="absolute -top-2 -right-2 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">
                        {{ $showAll ? "You're all set!" : "No unread notifications" }}
                    </h3>
                    <p class="text-gray-600 max-w-md mx-auto leading-relaxed">
                        {{ $showAll ? "You don't have any notifications yet. When you do, they'll appear here." : "You're all caught up! Check back later for new notifications." }}
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Enhanced Loading State -->
    <div wire:loading class="fixed inset-0 bg-white/80 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-6 shadow-xl border border-gray-200/50">
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <div class="w-8 h-8 border-3 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                    <div class="absolute inset-0 w-8 h-8 border-3 border-transparent border-r-purple-600 rounded-full animate-spin" style="animation-delay: 0.15s;"></div>
                </div>
                <div>
                    <p class="text-gray-900 font-medium">Updating notifications...</p>
                    <p class="text-sm text-gray-500">This won't take long</p>
                </div>
            </div>
        </div>
    </div>


    <style>
        .border-3 { border-width: 3px; }

        @keyframes slideIn {
            from { transform: translateX(-100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .group:hover .group-hover\:animate-slideIn {
            animation: slideIn 0.3s ease-out;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced notification events with toast notifications
            Livewire.on('notification-updated', () => {
                showToast('Notification marked as read', 'success');
            });

            Livewire.on('notification-deleted', () => {
                showToast('Notification deleted', 'success');
            });

            // Simple toast function
            function showToast(message, type = 'info') {
                const toast = document.createElement('div');
                toast.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transition-all duration-300 ${
                    type === 'success' ? 'bg-green-500 text-white' : 'bg-blue-500 text-white'
                }`;
                toast.textContent = message;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.style.transform = 'translateX(100%)';
                    setTimeout(() => document.body.removeChild(toast), 300);
                }, 3000);
            }
        });
    </script>
</div>

