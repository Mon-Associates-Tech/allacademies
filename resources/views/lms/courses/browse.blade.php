<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 -mt-8 -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-7xl mx-auto">
            {{-- Breadcrumb --}}
            <nav class="flex mb-6">
                <ol class="inline-flex items-center gap-2 text-sm">
                    <li><a href="{{ route('lms.courses.index') }}" class="text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors">Courses</a></li>
                    <li><svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg></li>
                    <li><span class="text-gray-900 dark:text-white font-medium">Browse All</span></li>
                </ol>
            </nav>

            {{-- Page Header --}}
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Browse All Courses</h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">Find the perfect course to match your learning goals</p>
            </div>

            {{-- Search & Filters --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
                <form method="GET" action="{{ route('lms.courses.browse') }}" class="space-y-4">
                    {{-- Search Row --}}
                    <div class="flex flex-col md:flex-row gap-4">
                        {{-- Main Search --}}
                        <div class="flex-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   class="w-full pl-12 pr-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                   placeholder="Search courses, topics, skills...">
                        </div>

                        {{-- Category Input --}}
                        <div class="md:w-64 relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                            </div>
                            <input type="text" name="category" value="{{ request('category') }}"
                                   class="w-full pl-12 pr-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                   placeholder="Category...">
                        </div>
                    </div>

                    {{-- Filter Row --}}
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        {{-- Difficulty Pills --}}
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Difficulty:</span>
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('lms.courses.browse', array_merge(request()->except('difficulty'), ['difficulty' => ''])) }}"
                                   class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ !request('difficulty') ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600' }}">
                                    All Levels
                                </a>
                                @foreach($difficulties as $level)
                                    <a href="{{ route('lms.courses.browse', array_merge(request()->except('difficulty'), ['difficulty' => $level])) }}"
                                       class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors
                                           {{ request('difficulty') === $level
                                               ? ($level === 'beginner' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300'
                                                   : ($level === 'intermediate' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300'
                                                       : 'bg-rose-100 text-rose-700 dark:bg-rose-900 dark:text-rose-300'))
                                               : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600' }}">
                                        {{ ucfirst($level) }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex gap-3">
                            @if(request()->hasAny(['search', 'difficulty', 'category']))
                                <a href="{{ route('lms.courses.browse') }}"
                                   class="px-4 py-1.5 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-400 text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Clear
                                </a>
                            @endif
                            <button type="submit" class="px-4 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Results Header --}}
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Showing <span class="font-semibold text-gray-900 dark:text-white">{{ $courses->firstItem() ?? 0 }}</span> - <span class="font-semibold text-gray-900 dark:text-white">{{ $courses->lastItem() ?? 0 }}</span> of <span class="font-semibold text-gray-900 dark:text-white">{{ $courses->total() }}</span> courses
                </p>
                <a href="{{ route('lms.courses.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors flex items-center gap-1">
                    View Recommended
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>

            {{-- Course Grid --}}
            @if($courses->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($courses as $course)
                        <a href="{{ route('lms.courses.show', $course->slug) }}" class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-600 transition-all flex flex-col">
                            {{-- Thumbnail --}}
                            <div class="relative aspect-video bg-gray-100 dark:bg-gray-700">
                                @if($course->thumbnail)
                                    <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                @endif

                                {{-- Badges --}}
                                <div class="absolute top-3 left-3">
                                    <span class="px-2 py-1 rounded text-xs font-medium
                                        @if($course->difficulty_level === 'beginner') bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300
                                        @elseif($course->difficulty_level === 'intermediate') bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300
                                        @else bg-rose-100 text-rose-700 dark:bg-rose-900 dark:text-rose-300
                                        @endif">
                                        {{ ucfirst($course->difficulty_level) }}
                                    </span>
                                </div>

                                <div class="absolute top-3 right-3">
                                    @if($course->is_free)
                                        <span class="px-2 py-1 rounded text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300">
                                            Free
                                        </span>
                                    @else
                                        <span class="px-2 py-1 rounded text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300">
                                            ₦{{ number_format($course->price) }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="p-4 flex-1 flex flex-col">
                                <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors line-clamp-2 mb-2">
                                    {{ $course->title }}
                                </h3>

                                @if($course->description)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-3 flex-1">
                                        {{ $course->description }}
                                    </p>
                                @endif

                                {{-- Instructor --}}
                                <div class="flex items-center gap-2 mb-3">
                                    @if($course->creator->avatar)
                                        <img class="h-6 w-6 rounded-full" src="{{ asset('storage/' . $course->creator->avatar) }}" alt="{{ $course->creator->name }}">
                                    @else
                                        <div class="h-6 w-6 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                            <span class="text-xs font-medium text-indigo-600 dark:text-indigo-400">{{ substr($course->creator->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $course->creator->name }}</span>
                                </div>

                                {{-- School Info --}}
                                @if($course->school)
                                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-500 mb-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        {{ $course->school->name }}
                                    </div>
                                @endif

                                {{-- Stats --}}
                                <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-500 pt-3 border-t border-gray-100 dark:border-gray-700">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        {{ $course->enrollments_count }} enrolled
                                    </span>
                                    @if($course->audience === 'school_only')
                                        <span class="flex items-center gap-1 text-amber-600 dark:text-amber-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                            School Only
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $courses->links() }}
                </div>
            @else
                {{-- Empty State --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No courses found</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Try adjusting your search or filter criteria</p>
                    <a href="{{ route('lms.courses.browse') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        Clear Filters
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
