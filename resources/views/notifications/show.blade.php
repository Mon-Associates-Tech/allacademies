<x-layouts.app>
    <div class="px-4 w-full max-w-5xl mx-auto">

        <!-- Back button with breadcrumb -->
        <div class="mb-4">
            <nav class="flex items-center space-x-4 text-sm">
                <a href="{{ route('notifications.index') }}"
                   class="flex items-center text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Notifications
                </a>
                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-700 dark:text-gray-300 font-medium">Notification Details</span>
            </nav>
        </div>

        <!-- Main notification card -->
        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700">

            <!-- Header with gradient background -->
            <div class="relative bg-gradient-to-r {{ $notificationData['type'] === 'assignment' ? 'from-blue-500 to-blue-600' : 'from-violet-500 to-violet-600' }} px-8 py-6">
                <div class="absolute inset-0 bg-black bg-opacity-10"></div>
                <div class="relative flex items-start justify-between">
                    <div class="flex items-start space-x-4">
                        <!-- Enhanced icon -->
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 rounded-xl bg-white bg-opacity-20 backdrop-blur-sm flex items-center justify-center text-2xl border border-white border-opacity-20">
                                @if($notificationData['type'] === 'assignment')
                                    📚
                                @else
                                    📣
                                @endif
                            </div>
                        </div>
                        <div class="flex-1">
                            <h1 class="text-2xl font-bold text-white mb-2 leading-tight">
                                {{ $notificationData['title'] }}
                            </h1>
                            <div class="flex items-center space-x-4 text-white text-opacity-90">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-sm">{{ $notificationData['created_at']->format('M j, Y \a\t g:i A') }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h4a1 1 0 011 1v2h4a1 1 0 110 2H3a1 1 0 110-2h4zM5 6v12a2 2 0 002 2h6a2 2 0 002-2V6H5z"></path>
                                    </svg>
                                    <span class="text-sm">{{ $notificationData['created_at']->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status badge -->
                    @if($notificationData['read_at'])
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Read
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200 animate-pulse">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                            New
                        </span>
                    @endif
                </div>
            </div>

            <!-- Content section -->
            <div class="px-8 py-8">
                <!-- Message content -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Message</h2>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border-l-4 {{ $notificationData['type'] === 'assignment' ? 'border-blue-500' : 'border-violet-500' }}">
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed text-base">
                            {{ $notificationData['message'] }}
                        </p>
                    </div>
                </div>

                @if($notificationData['type'] === 'assignment' && isset($notificationData['assignment']))
                    <!-- Assignment details section -->
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Assignment Details</h2>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 capitalize">
                                {{ $notificationData['data']['type'] }}
                            </span>
                        </div>

                        <!-- Assignment info cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Subject card -->
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl p-6 border border-blue-200 dark:border-blue-700/50">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Subject</p>
                                        <p class="text-lg font-bold text-blue-900 dark:text-blue-100">{{ $notificationData['data']['subject'] }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Teacher card -->
                            <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl p-6 border border-purple-200 dark:border-purple-700/50">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-purple-600 dark:text-purple-400">Teacher</p>
                                        <p class="text-lg font-bold text-purple-900 dark:text-purple-100">{{ $notificationData['data']['teacher'] }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Duration card -->
                            @if($notificationData['data']['duration_in_minutes'])
                                <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl p-6 border border-green-200 dark:border-green-700/50">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-green-600 dark:text-green-400">Duration</p>
                                            <p class="text-lg font-bold text-green-900 dark:text-green-100">{{ $notificationData['data']['duration_in_minutes'] }} min</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Schedule information -->
                        @if($notificationData['data']['starts_at'] || $notificationData['data']['ends_at'])
                            <div class="bg-gradient-to-r from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 rounded-xl p-6 border border-orange-200 dark:border-orange-700/50">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Schedule
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @if($notificationData['data']['starts_at'])
                                        <div>
                                            <dt class="text-sm font-medium text-orange-600 dark:text-orange-400 mb-2">Starts At</dt>
                                            <dd class="text-gray-900 dark:text-gray-100">
                                                <div class="flex items-center space-x-2">
                                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span class="font-semibold">{{ \Carbon\Carbon::parse($notificationData['data']['starts_at'])->format('M j, Y') }}</span>
                                                </div>
                                                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                    {{ \Carbon\Carbon::parse($notificationData['data']['starts_at'])->format('g:i A') }}
                                                    ({{ \Carbon\Carbon::parse($notificationData['data']['starts_at'])->diffForHumans() }})
                                                </div>
                                            </dd>
                                        </div>
                                    @endif

                                    @if($notificationData['data']['ends_at'])
                                        <div>
                                            <dt class="text-sm font-medium text-red-600 dark:text-red-400 mb-2">Ends At</dt>
                                            <dd class="text-gray-900 dark:text-gray-100">
                                                <div class="flex items-center space-x-2">
                                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span class="font-semibold">{{ \Carbon\Carbon::parse($notificationData['data']['ends_at'])->format('M j, Y') }}</span>
                                                </div>
                                                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                    {{ \Carbon\Carbon::parse($notificationData['data']['ends_at'])->format('g:i A') }}
                                                    ({{ \Carbon\Carbon::parse($notificationData['data']['ends_at'])->diffForHumans() }})
                                                </div>
                                            </dd>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Action buttons -->
                        @if(isset($notificationData['data']['assignment_id']))
                            <div class="flex flex-col sm:flex-row gap-4 pt-6">
                                <a href="#"
                                   class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View Assignment
                                </a>

                                <button
                                    onclick="window.history.back()"
                                    class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-xl text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    Go Back
                                </button>
                            </div>
                        @endif
                    </div>
                @else
                    <!-- For non-assignment notifications -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-6">
                        <button
                            onclick="window.history.back()"
                            class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-xl text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Go Back
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add some custom styles for animations -->
    <style>
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slide-in-up {
            animation: slideInUp 0.5s ease-out;
        }

        .hover-lift:hover {
            transform: translateY(-2px);
            transition: transform 0.2s ease-in-out;
        }
    </style>

    <script>
        // Add entrance animation
        document.addEventListener('DOMContentLoaded', function() {
            const mainCard = document.querySelector('.bg-white.dark\\:bg-gray-800');
            if (mainCard) {
                mainCard.classList.add('animate-slide-in-up');
            }
        });
    </script>
</x-layouts.app>
