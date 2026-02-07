<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{-- Breadcrumb --}}
            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="{{ route('my-learning.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            My Learning
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-700 dark:text-gray-300">Completed</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- Page Header --}}
            <div class="mb-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Completed Courses</h1>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Congratulations on your achievements! View your certificates below.</p>
                    </div>
                    <div class="mt-4 lg:mt-0">
                        <a href="{{ route('my-learning.certificates') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                            View All Certificates
                        </a>
                    </div>
                </div>
            </div>

            {{-- Navigation Tabs --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-8">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="flex -mb-px" aria-label="Tabs">
                        <a href="{{ route('my-learning.index') }}" class="flex-1 py-4 px-6 text-center border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 font-medium text-sm transition-colors">
                            All Courses
                        </a>
                        <a href="{{ route('my-learning.in-progress') }}" class="flex-1 py-4 px-6 text-center border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 font-medium text-sm transition-colors">
                            In Progress
                        </a>
                        <a href="{{ route('my-learning.completed') }}" class="flex-1 py-4 px-6 text-center border-b-2 border-emerald-500 text-emerald-600 dark:text-emerald-400 font-medium text-sm">
                            Completed
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-400">{{ $enrollments->total() }}</span>
                        </a>
                        <a href="{{ route('my-learning.certificates') }}" class="flex-1 py-4 px-6 text-center border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 font-medium text-sm transition-colors">
                            Certificates
                        </a>
                    </nav>
                </div>
            </div>

            {{-- Courses Grid --}}
            @if($enrollments->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($enrollments as $enrollment)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow duration-200">
                            {{-- Course Thumbnail --}}
                            <div class="relative aspect-video bg-gray-100 dark:bg-gray-700">
                                @if($enrollment->course->thumbnail)
                                    <img src="{{ asset('storage/' . $enrollment->course->thumbnail) }}" alt="{{ $enrollment->course->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20">
                                        <svg class="w-12 h-12 text-emerald-300 dark:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                @endif

                                {{-- Completed Badge --}}
                                <div class="absolute top-3 right-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/80 dark:text-emerald-300">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Completed
                                    </span>
                                </div>

                                {{-- Grade Badge --}}
                                @if($enrollment->final_grade !== null)
                                    <div class="absolute bottom-3 left-3">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold
                                            @if($enrollment->final_grade >= 90) bg-emerald-100 text-emerald-800 dark:bg-emerald-900/80 dark:text-emerald-300
                                            @elseif($enrollment->final_grade >= 70) bg-blue-100 text-blue-800 dark:bg-blue-900/80 dark:text-blue-300
                                            @elseif($enrollment->final_grade >= 50) bg-amber-100 text-amber-800 dark:bg-amber-900/80 dark:text-amber-300
                                            @else bg-red-100 text-red-800 dark:bg-red-900/80 dark:text-red-300
                                            @endif">
                                            Grade: {{ number_format($enrollment->final_grade, 0) }}%
                                        </span>
                                    </div>
                                @endif
                            </div>

                            {{-- Course Content --}}
                            <div class="p-5">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2">
                                    {{ $enrollment->course->title }}
                                </h3>

                                {{-- Instructor --}}
                                @if($enrollment->course->creator)
                                    <div class="flex items-center mb-4">
                                        @if($enrollment->course->creator->avatar)
                                            <img class="h-6 w-6 rounded-full" src="{{ asset('storage/' . $enrollment->course->creator->avatar) }}" alt="{{ $enrollment->course->creator->name }}">
                                        @else
                                            <div class="h-6 w-6 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                                <span class="text-xs font-medium text-gray-600 dark:text-gray-300">{{ substr($enrollment->course->creator->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">{{ $enrollment->course->creator->name }}</span>
                                    </div>
                                @endif

                                {{-- Completion Info --}}
                                <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 mb-4">
                                    <svg class="w-4 h-4 mr-1.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Completed {{ $enrollment->completed_at ? $enrollment->completed_at->format('M d, Y') : '' }}
                                </div>

                                {{-- Action Buttons --}}
                                <div class="flex gap-2">
                                    <a href="{{ route('lms.courses.certificate', $enrollment->course) }}" class="flex-1 inline-flex justify-center items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                        </svg>
                                        Certificate
                                    </a>
                                    <a href="{{ route('lms.courses.learn', $enrollment->course->slug) }}" class="inline-flex justify-center items-center px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium rounded-lg transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        Review
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($enrollments->hasPages())
                    <div class="mt-8">
                        {{ $enrollments->links() }}
                    </div>
                @endif
            @else
                {{-- Empty State --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                    <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No completed courses yet</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 max-w-sm mx-auto">Complete a course to earn your certificate. Keep learning and you'll see your achievements here.</p>
                    <div class="flex flex-col sm:flex-row justify-center gap-3">
                        <a href="{{ route('my-learning.in-progress') }}" class="inline-flex items-center justify-center px-5 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium rounded-lg transition-colors duration-200">
                            View In Progress
                        </a>
                        <a href="{{ route('lms.courses.browse') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Browse Courses
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
