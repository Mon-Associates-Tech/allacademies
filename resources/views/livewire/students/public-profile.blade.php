<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Similar structure to teacher profile but adapted for students -->
    <div class="relative bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Cover Image -->
        <div class="relative h-64 md:h-80">
            @if($user->cover_image_url)
                <img src="{{ $user->cover_image_url }}" alt="Cover" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-black/20 to-transparent"></div>
            @else
                <div class="absolute inset-0 bg-gradient-to-br from-blue-600 via-cyan-600 to-teal-700"></div>
            @endif
        </div>

        <!-- Profile Info -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative -mt-24 pb-8">
                <div class="flex flex-col md:flex-row md:items-end md:space-x-6">
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        @if($user->profile_avatar_url)
                            <img class="w-32 h-32 md:w-40 md:h-40 rounded-2xl border-4 border-white dark:border-gray-800 shadow-2xl object-cover"
                                 src="{{ $user->profile_avatar_url }}" alt="{{ $user->name }}">
                        @else
                            <div class="w-32 h-32 md:w-40 md:h-40 rounded-2xl border-4 border-white dark:border-gray-800 shadow-2xl bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center">
                                <span class="text-white font-bold text-5xl">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- User Info -->
                    <div class="flex-1 mt-6 md:mt-0 md:mb-4">
                        <div class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-sm rounded-2xl p-6 shadow-xl border border-gray-200 dark:border-gray-700">
                            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h1>
                            <div class="mt-2 flex items-center gap-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    Student
                                </span>
                            </div>
                            @if($user->bio)
                                <p class="mt-4 text-base text-gray-600 dark:text-gray-300">{{ $user->bio }}</p>
                            @endif

                            <!-- Stats -->
                            <div class="mt-6 grid grid-cols-2 gap-4">
                                <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl border border-blue-200 dark:border-blue-800">
                                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $totalAssessments }}</div>
                                    <div class="text-sm text-blue-700 dark:text-blue-300 mt-1 font-medium">Assessments</div>
                                </div>
                                <div class="text-center p-4 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl border border-green-200 dark:border-green-800">
                                    <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $averageScore }}%</div>
                                    <div class="text-sm text-green-700 dark:text-green-300 mt-1 font-medium">Average Score</div>
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
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">About {{ explode(' ', $user->name)[0] }}</h2>
                    @if($user->bio)
                        <p class="text-gray-600 dark:text-gray-300">{{ $user->bio }}</p>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 italic">This student hasn't added a bio yet.</p>
                    @endif
                </div>
            </div>

            <div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Member Since</h3>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->created_at->format('F Y') }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ $user->created_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
