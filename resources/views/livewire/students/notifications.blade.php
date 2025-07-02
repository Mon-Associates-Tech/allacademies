<div class="px-4 lg:px-0 w-full mx-auto">

    <!-- Welcome Section -->
    <div class="sm:flex sm:justify-between sm:items-center mb-2">
        <div class="mb-0 sm:mb-0">
            <div class="flex items-center space-x-3">
                <svg class="w-8 h-8 text-violet-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2zm-2 1H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6z"/>
                </svg>
                <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-slate-700 via-gray-600 to-slate-800 bg-clip-text text-transparent">Notifications</h1>
            </div>

        </div>
    </div>

    <!-- Dashboard Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Main Content Area -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Pending Assignments -->
            @if(count($pendingAssignments) > 0)
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl">
                    <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                        <h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center">
                            <span class="text-red-500 mr-2"></span>
                            Urgent: Pending Assignments
                        </h2>
                    </header>
                    <div class="p-5">
                        <div class="space-y-3">
                            @foreach($pendingAssignments as $assignment)
                                <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-900 dark:text-gray-100">{{ $assignment['title'] }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $assignment['subject'] }} • {{ $assignment['type'] }}</p>
                                        <p class="text-xs text-red-600 dark:text-red-400 mt-1">
                                            ⏰ Due {{ \Carbon\Carbon::parse($assignment['ends_at'])->diffForHumans() }}
                                        </p>
                                    </div>
                                    <button wire:click="startAssignment({{ $assignment['id'] }})"
                                            class="btn bg-red-500 hover:bg-red-600 text-white text-sm">
                                        Start Now
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Upcoming Assignments -->
            @if(count($upcomingAssignments) > 0)
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl">
                    <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                        <h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center">
                            <span class="text-blue-500 mr-2"></span>
                            Upcoming Assignments
                        </h2>
                    </header>
                    <div class="p-5">
                        <div class="space-y-3">
                            @foreach($upcomingAssignments as $assignment)
                                <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-900 dark:text-gray-100">{{ $assignment['title'] }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $assignment['subject'] }} • {{ $assignment['type'] }}</p>
                                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                            Starts {{ \Carbon\Carbon::parse($assignment['starts_at'])->diffForHumans() }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $assignment['duration'] }} min</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                                <span class="text-green-600 dark:text-green-400 text-lg">✓</span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Completed</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $this->getCompletedAssignmentsCount() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center">
                                <span class="text-yellow-600 dark:text-yellow-400 text-lg">⏳</span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ count($pendingAssignments) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 dark:text-blue-400 text-lg"></span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Upcoming</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ count($upcomingAssignments) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">

            <!-- Recent Notifications -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl">
                <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-800 dark:text-gray-100">Recent Notifications</h2>
                    <a href="{{ route('student.notifications.index') }}" class="text-sm text-violet-600 hover:text-violet-700 dark:text-violet-400">
                        View all
                    </a>
                </header>
                <div class="p-3">
                    @if(count($recentNotifications) > 0)
                        <div class="space-y-3">
                            @foreach($recentNotifications as $notification)
                                <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 rounded-full {{ $notification['type'] === 'assignment' ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-violet-100 dark:bg-violet-900/30' }} flex items-center justify-center">
                                            {{ $notification['type'] === 'assignment' ? '' : '' }}
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ Str::limit($notification['title'], 40) }}
                                        </p>
                                        @if($notification['type'] === 'assignment' && isset($notification['subject']))
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $notification['subject'] }}</p>
                                        @endif
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
                                        </p>
                                        <div class="mt-2 flex space-x-2">
                                            <a href="{{ route('notifications.show', ['type' => $notification['type'], 'id' => $notification['id']]) }}"
                                               class="text-xs text-violet-600 hover:text-violet-700 dark:text-violet-400">
                                                View
                                            </a>
                                            @if($notification['type'] === 'assignment' && isset($notification['assignment_id']))
                                                <button wire:click="startAssignment({{ $notification['assignment_id'] }})"
                                                        class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400">
                                                    Start
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6">
                            <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-2">
                                <span class="text-gray-400 text-lg"></span>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">No new notifications</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl">
                <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                    <h2 class="font-semibold text-gray-800 dark:text-gray-100">Quick Actions</h2>
                </header>
                <div class="p-5 space-y-3">
                    <a href="{{ route('student.notifications.index') }}"
                       class="flex items-center p-3 text-sm text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                        <span class="mr-3"></span>
                        View All Notifications
                    </a>
                    <a href="#"
                       class="flex items-center p-3 text-sm text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                        <span class="mr-3"></span>
                        View Grades
                    </a>
                    <a href="{{ route('profile.show') }}"
                       class="flex items-center p-3 text-sm text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                        <span class="mr-3">⚙️</span>
                        Profile Settings
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

@script
<script>
    // Add a method to get completed assignments count
    $wire.getCompletedAssignmentsCount = function() {
        // This would typically come from the backend
        return 0; // Placeholder
    };
</script>
@endscript
