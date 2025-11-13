<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section with Cover -->
    <div class="relative bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Cover Image -->
        <div class="relative h-64 md:h-80 lg:h-96">
            @if($user->cover_image_url)
                <img src="{{ $user->cover_image_url }}"
                     alt="Cover"
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-black/20 to-transparent"></div>
            @else
                <div class="absolute inset-0 bg-gradient-to-br from-violet-600 via-purple-600 to-indigo-700"></div>
                <div class="absolute inset-0 opacity-10"
                     style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
                </div>
            @endif
        </div>

        <!-- Profile Info Overlay -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative -mt-32 pb-8">
                <div class="flex flex-col md:flex-row md:items-end md:space-x-6">
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        @if($user->profile_avatar_url)
                            <img class="w-40 h-40 md:w-48 md:h-48 rounded-2xl border-4 border-white dark:border-gray-800 shadow-2xl object-cover"
                                 src="{{ $user->profile_avatar_url }}"
                                 alt="{{ $user->name }}">
                        @else
                            <div class="w-40 h-40 md:w-48 md:h-48 rounded-2xl border-4 border-white dark:border-gray-800 shadow-2xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center">
                                <span class="text-white font-bold text-6xl">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- User Info -->
                    <div class="flex-1 mt-6 md:mt-0 md:mb-4">
                        <div class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-sm rounded-2xl p-6 shadow-xl border border-gray-200 dark:border-gray-700">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex-1">
                                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">
                                        {{ $user->name }}
                                    </h1>
                                    <div class="mt-2 flex items-center gap-3">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-violet-100 dark:bg-violet-900/30 text-violet-800 dark:text-violet-300">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                            Teacher
                                        </span>
                                        <span class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 8h6m-6 4h6m2-8V7a2 2 0 00-2-2H8a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2z"/>
                                            </svg>
                                            Member since {{ $user->created_at->format('M Y') }}
                                        </span>
                                    </div>
                                    @if($user->bio)
                                        <p class="mt-4 text-base text-gray-600 dark:text-gray-300 max-w-3xl">
                                            {{ $user->bio }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Stats -->
                            <div class="mt-6 grid grid-cols-3 gap-4">
                                <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl border border-blue-200 dark:border-blue-800">
                                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $totalStudents }}</div>
                                    <div class="text-sm text-blue-700 dark:text-blue-300 mt-1 font-medium">Students</div>
                                </div>
                                <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl border border-purple-200 dark:border-purple-800">
                                    <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $totalAssignments }}</div>
                                    <div class="text-sm text-purple-700 dark:text-purple-300 mt-1 font-medium">Assignments</div>
                                </div>
                                <div class="text-center p-4 bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20 rounded-xl border border-indigo-200 dark:border-indigo-800">
                                    <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $totalSubjects }}</div>
                                    <div class="text-sm text-indigo-700 dark:text-indigo-300 mt-1 font-medium">Subjects</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Column -->
            <div class="lg:col-span-2 space-y-8">
                <!-- About Section -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-violet-50 to-purple-50 dark:from-violet-900/10 dark:to-purple-900/10">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                            <svg class="w-6 h-6 mr-2 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            About {{ explode(' ', $user->name)[0] }}
                        </h2>
                    </div>
                    <div class="p-6">
                        @if($user->bio)
                            <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                                {{ $user->bio }}
                            </p>
                        @else
                            <p class="text-gray-500 dark:text-gray-400 italic">
                                This teacher hasn't added a bio yet.
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Recent Assignments -->
                @if($recentAssignments && $recentAssignments->count() > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/10 dark:to-indigo-900/10">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                                <svg class="w-6 h-6 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Recent Assignments
                            </h2>
                        </div>
                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($recentAssignments as $assignment)
                                <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                {{ $assignment->title }}
                                            </h3>
                                            @if($assignment->subject)
                                                <span class="inline-flex items-center mt-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300">
                                                    {{ $assignment->subject->name }}
                                                </span>
                                            @endif
                                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                                Created {{ $assignment->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Contact Info -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Contact Information
                    </h3>

                    <div class="space-y-4">
                        <div class="flex items-center text-sm">
                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-gray-600 dark:text-gray-300">{{ $user->email }}</span>
                        </div>

                        @if($user->phone)
                            <div class="flex items-center text-sm">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <span class="text-gray-600 dark:text-gray-300">{{ $user->phone }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Teaching Stats -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Teaching Overview
                    </h3>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <span class="text-sm font-medium text-blue-900 dark:text-blue-300">Total Students</span>
                            <span class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $totalStudents }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                            <span class="text-sm font-medium text-purple-900 dark:text-purple-300">Assignments Created</span>
                            <span class="text-lg font-bold text-purple-600 dark:text-purple-400">{{ $totalAssignments }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg">
                            <span class="text-sm font-medium text-indigo-900 dark:text-indigo-300">Subjects Teaching</span>
                            <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{{ $totalSubjects }}</span>
                        </div>
                    </div>
                </div>

                <!-- Member Since -->
                <div class="bg-gradient-to-br from-violet-50 to-purple-50 dark:from-violet-900/20 dark:to-purple-900/20 rounded-2xl shadow-sm border border-violet-200 dark:border-violet-800 p-6">
                    <div class="text-center">
                        <svg class="w-12 h-12 mx-auto text-violet-600 dark:text-violet-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Member Since</h3>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->created_at->format('F Y') }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                            {{ $user->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
