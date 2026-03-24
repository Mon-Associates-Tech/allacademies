<x-layouts.app>
    <div class="min-h-screen bg-gray-50 rounded-lg dark:bg-gray-900 -mt-8 px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-7xl mx-auto">
            {{-- Page Header --}}
            <div class="mb-8 mt-4">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Explore Courses</h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">Discover courses to enhance your skills and knowledge</p>
            </div>

            {{-- Search & Filters --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
                <form method="GET" action="{{ route('lms.courses.index') }}" class="space-y-4">
                    {{-- Search Bar --}}
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="w-full pl-12 pr-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                               placeholder="Search for courses, topics, or instructors...">
                    </div>

                    {{-- Filter Row --}}
                    <div class="flex flex-wrap items-center gap-4">
                        {{-- Difficulty Filter --}}
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Level:</span>
                            <div class="flex gap-2">
                                <a href="{{ route('lms.courses.index', array_merge(request()->except('difficulty'), ['difficulty' => ''])) }}"
                                   class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ !request('difficulty') ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600' }}">
                                    All
                                </a>
                                <a href="{{ route('lms.courses.index', array_merge(request()->except('difficulty'), ['difficulty' => 'beginner'])) }}"
                                   class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ request('difficulty') === 'beginner' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600' }}">
                                    Beginner
                                </a>
                                <a href="{{ route('lms.courses.index', array_merge(request()->except('difficulty'), ['difficulty' => 'intermediate'])) }}"
                                   class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ request('difficulty') === 'intermediate' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600' }}">
                                    Intermediate
                                </a>
                                <a href="{{ route('lms.courses.index', array_merge(request()->except('difficulty'), ['difficulty' => 'advanced'])) }}"
                                   class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ request('difficulty') === 'advanced' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900 dark:text-rose-300' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600' }}">
                                    Advanced
                                </a>
                            </div>
                        </div>

                        {{-- Price Filter --}}
                        <div class="flex items-center gap-2 ml-auto">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Price:</span>
                            <div class="flex gap-2">
                                <a href="{{ route('lms.courses.index', array_merge(request()->except('price'), ['price' => ''])) }}"
                                   class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ !request('price') ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600' }}">
                                    All
                                </a>
                                <a href="{{ route('lms.courses.index', array_merge(request()->except('price'), ['price' => 'free'])) }}"
                                   class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ request('price') === 'free' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600' }}">
                                    Free
                                </a>
                                <a href="{{ route('lms.courses.index', array_merge(request()->except('price'), ['price' => 'paid'])) }}"
                                   class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ request('price') === 'paid' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600' }}">
                                    Premium
                                </a>
                            </div>
                        </div>

                        {{-- Search Button --}}
                        <button type="submit" class="px-4 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Search
                        </button>
                    </div>
                </form>
            </div>

            {{-- Results Count --}}
            @if($courses->count() > 0)
                <div class="flex items-center justify-between mb-6">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Showing <span class="font-semibold text-gray-900 dark:text-white">{{ $courses->count() }}</span> of <span class="font-semibold text-gray-900 dark:text-white">{{ $courses->total() }}</span> courses
                    </p>
                </div>

                {{-- Course Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                    @foreach($courses as $course)
                        <a href="{{ route('lms.courses.show', $course->slug) }}" class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-600 transition-all">
                            {{-- Thumbnail --}}
                            <div class="relative aspect-[16/10] bg-gray-100 dark:bg-gray-700">
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
                                <div class="absolute top-3 left-3 flex gap-2">
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
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors line-clamp-2 mb-2">
                                    {{ $course->title }}
                                </h3>

                                @if($course->description)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-3">
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                        {{ $course->chapters->count() }} chapters
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        {{ $course->enrollments_count }} enrolled
                                    </span>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No courses found</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Try adjusting your search or filter criteria</p>
                    <a href="{{ route('lms.courses.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        Clear Filters
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
