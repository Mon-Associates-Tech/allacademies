<div class="px-4 lg:px-0 w-full mx-auto">

    <div class="sm:flex sm:justify-between sm:items-center mb-6">
        <div class="mb-4 sm:mb-0">
            <div class="flex items-center space-x-3">
                <svg class="w-9 h-9 text-violet-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2zm-2 1H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6z"/>
                </svg>
                <h1 class="text-3xl md:text-4xl font-extrabold bg-gradient-to-r from-slate-700 via-gray-600 to-slate-800 bg-clip-text text-transparent">Your Notifications</h1>
            </div>
            <p class="mt-1 text-gray-600 dark:text-gray-400">Stay organized with your latest updates and assignments.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">

            @if(count($pendingAssignments) > 0)
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden">
                    <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60 bg-red-50 dark:bg-red-950/20">
                        <h2 class="font-bold text-red-700 dark:text-red-300 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2c-5.523 0-10 4.477-10 10s4.477 10 10 10 10-4.477 10-10-4.477-10-10-10zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8zm-.5-13h1v6h-1zm0 8h1v1h-1z"/></svg>
                            Urgent: Pending Assignments
                        </h2>
                    </header>
                    <div class="p-5">
                        <div class="space-y-4">
                            @foreach($pendingAssignments as $assignment)
                                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                    <div class="flex-1 mb-3 sm:mb-0">
                                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-lg">{{ $assignment['title'] }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $assignment['subject'] }} • {{ $assignment['type'] }}</p>
                                        <p class="text-xs text-red-600 dark:text-red-400 mt-1 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8V4l8 8-8 8v-4H4V8z"/></svg>
                                            Due {{ \Carbon\Carbon::parse($assignment['ends_at'])->diffForHumans() }}
                                        </p>
                                    </div>
                                    <button wire:click="startAssignment({{ $assignment['id'] }})"
                                            class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md shadow-sm transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Start Now
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if(count($upcomingAssignments) > 0)
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden">
                    <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                        <h2 class="font-bold text-gray-800 dark:text-gray-100 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2c-5.523 0-10 4.477-10 10s4.477 10 10 10 10-4.477 10-10-4.477-10-10-10zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8zm.5-13h-1v6h1zM11 16h1v1h-1z"/></svg>
                            Upcoming Assignments
                        </h2>
                    </header>
                    <div class="p-5">
                        <div class="space-y-4">
                            @foreach($upcomingAssignments as $assignment)
                                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                    <div class="flex-1 mb-2 sm:mb-0">
                                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-lg">{{ $assignment['title'] }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $assignment['subject'] }} • {{ $assignment['type'] }}</p>
                                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-1 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8V4l8 8-8 8v-4H4V8z"/></svg>
                                            Starts {{ \Carbon\Carbon::parse($assignment['starts_at'])->diffForHumans() }}
                                        </p>
                                    </div>
                                    <div class="text-right text-sm text-gray-500 dark:text-gray-400">
                                        <span>{{ $assignment['duration'] }} min</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-5 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Completed</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $this->getCompletedAssignmentsCount() }}</p>
                    </div>
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.172l-3.536-3.536 1.414-1.414L9 13.344 19.071 3.273l1.414 1.414L9 16.172z"/></svg>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-5 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ count($pendingAssignments) }}</p>
                    </div>
                    <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2c-5.523 0-10 4.477-10 10s4.477 10 10 10 10-4.477 10-10-4.477-10-10-10zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8zm-.5-13h1v6h-1zm0 8h1v1h-1z"/></svg>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-5 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Upcoming</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ count($upcomingAssignments) }}</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2c-5.523 0-10 4.477-10 10s4.477 10 10 10 10-4.477 10-10-4.477-10-10-10zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8zm.5-13h-1v6h1zM11 16h1v1h-1z"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">

            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden">
                <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
                    <h2 class="font-bold text-gray-800 dark:text-gray-100">Recent Notifications</h2>
                    <a href="{{ route('student.notifications.index') }}" class="text-sm text-violet-600 hover:text-violet-700 dark:text-violet-400 font-medium">
                        View all &rarr;
                    </a>
                </header>
                <div class="p-3">
                    @if(count($recentNotifications) > 0)
                        <div class="space-y-3">
                            @foreach($recentNotifications as $notification)
                                <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150 group">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 rounded-full {{ $notification['type'] === 'assignment' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : 'bg-violet-100 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400' }} flex items-center justify-center">
                                            @if($notification['type'] === 'assignment')
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                                            @else
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 leading-tight">
                                            {{ Str::limit($notification['title'], 60) }}
                                        </p>
                                        @if($notification['type'] === 'assignment' && isset($notification['subject']))
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $notification['subject'] }}</p>
                                        @endif
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
                                        </p>
                                        <div class="mt-2 flex space-x-3 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                                            <a href="{{ route('notifications.show', ['type' => $notification['type'], 'id' => $notification['id']]) }}"
                                               class="text-xs text-violet-600 hover:text-violet-700 dark:text-violet-400 font-medium">
                                                View Details
                                            </a>
                                            @if($notification['type'] === 'assignment' && isset($notification['assignment_id']))
                                                <button wire:click="startAssignment({{ $notification['assignment_id'] }})"
                                                        class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 font-medium">
                                                    Start Assignment
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-14 h-14 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-7 h-7 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">You're all caught up! No new notifications.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden">
                <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                    <h2 class="font-bold text-gray-800 dark:text-gray-100">Quick Actions</h2>
                </header>
                <div class="p-5 space-y-3">
                    <a href="{{ route('student.notifications.index') }}"
                       class="flex items-center p-3 text-sm text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150 group">
                        <svg class="w-5 h-5 mr-3 text-violet-500 group-hover:text-violet-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>
                        View All Notifications
                    </a>
                    <a href="#"
                       class="flex items-center p-3 text-sm text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150 group">
                        <svg class="w-5 h-5 mr-3 text-green-500 group-hover:text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                        View Grades
                    </a>
                    <a href="{{ route('profile.show') }}"
                       class="flex items-center p-3 text-sm text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150 group">
                        <svg class="w-5 h-5 mr-3 text-gray-500 group-hover:text-gray-600" fill="currentColor" viewBox="0 0 24 24"><path d="M19.43 12.98c.04-.32.07-.64.07-.98s-.03-.66-.07-.98l2.11-1.65c.19-.15.24-.42.12-.64l-2-3.46c-.12-.22-.39-.3-.61-.22l-2.49 1c-.52-.4-1.09-.72-1.71-.97l-.37-2.65C14.05 2.18 13.73 2 13.31 2h-2.63c-.42 0-.74.18-.82.32l-.37 2.65c-.62.25-1.19.57-1.71.97l-2.49-1c-.22-.08-.49 0-.61.22l-2 3.46c-.12.22-.07.49.12.64l2.11 1.65c-.04.32-.07.64-.07.98s.03.66.07.98l-2.11 1.65c-.19.15-.24.42-.12.64l2 3.46c.12.22.39.3.61.22l2.49-1c.52.4 1.09.72 1.71.97l.37 2.65c.08.14.4.32.82.32h2.63c.42 0 .74-.18.82-.32l.37-2.65c.62-.25 1.19-.57 1.71-.97l2.49 1c.22.08.49 0 .61-.22l2-3.46c.12-.22.07-.49-.12-.64l-2.11-1.65zM12 15.5c-1.93 0-3.5-1.57-3.5-3.5s1.57-3.5 3.5-3.5 3.5 1.57 3.5 3.5-1.57 3.5-3.5 3.5z"/></svg>
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
        // This would typically come from the backend, e.g., via an API call or a Livewire component property.
        // For demonstration, let's return a static number. In a real application, you'd fetch this dynamically.
        return 23; // Placeholder value
    };
</script>
@endscript
