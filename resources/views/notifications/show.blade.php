<x-layouts.app>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-gray-50 to-slate-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-8">
        <div class="px-4 w-full max-w-4xl mx-auto">

            <!-- Back Navigation -->
            <div class="mb-6">
                <a href="{{ route('notifications.index') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 group">
                    <svg class="w-4 h-4 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Notifications
                </a>
            </div>

            <!-- Main Card -->
            <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-3xl overflow-hidden border border-gray-100 dark:border-gray-700">

                <!-- Header -->
                @php
                    $colorClasses = match($notificationData['color'] ?? 'violet') {
                        'blue' => 'from-blue-500 to-blue-600',
                        'purple' => 'from-purple-500 to-purple-600',
                        'green' => 'from-emerald-500 to-emerald-600',
                        'amber' => 'from-amber-500 to-amber-600',
                        'indigo' => 'from-indigo-500 to-indigo-600',
                        'gray' => 'from-gray-500 to-gray-600',
                        'cyan' => 'from-cyan-500 to-cyan-600',
                        default => 'from-violet-500 to-violet-600',
                    };
                @endphp

                <div class="relative bg-gradient-to-r {{ $colorClasses }} px-8 py-8">
                    <div class="absolute inset-0 bg-black/10"></div>
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-32 translate-x-32"></div>

                    <div class="relative flex items-start justify-between">
                        <div class="flex items-start space-x-5">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center border border-white/30 shadow-lg">
                                    @include('notifications.partials.icon', ['type' => $notificationData['type'], 'category' => $notificationData['category'] ?? 'general'])
                                </div>
                            </div>

                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-white/20 text-white capitalize">
                                        {{ $notificationData['category'] ?? $notificationData['type'] }}
                                    </span>
                                </div>
                                <h1 class="text-2xl md:text-3xl font-bold text-white mb-3 leading-tight">
                                    {{ $notificationData['title'] }}
                                </h1>
                                <div class="flex flex-wrap items-center gap-4 text-white/90 text-sm">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span>{{ $notificationData['created_at']->format('M j, Y \a\t g:i A') }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>{{ $notificationData['created_at']->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div class="flex-shrink-0">
                            @if($notificationData['read_at'])
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-white text-green-700 shadow-lg">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Read
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-white text-blue-700 shadow-lg animate-pulse">
                                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                                    New
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-8">
                    <!-- Message Section -->
                    <div class="mb-8">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                            Message
                        </h2>
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700/50 dark:to-gray-800/50 rounded-2xl p-6 border border-gray-200 dark:border-gray-600">
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed text-base whitespace-pre-line">
                                {{ $notificationData['message'] }}
                            </p>
                        </div>
                    </div>

                    <!-- Type-specific Details -->
                    @include('notifications.partials.details', ['notificationData' => $notificationData])

                    <!-- Action Buttons -->
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        @include('notifications.partials.actions', ['notificationData' => $notificationData])
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
