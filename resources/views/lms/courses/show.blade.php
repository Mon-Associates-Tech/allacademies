<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 -mt-8 -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-7xl mx-auto">
            {{-- Breadcrumb --}}
            <nav class="flex mb-6 pt-6">
                <ol class="inline-flex items-center gap-2 text-sm">
                    <li><a href="{{ route('lms.courses.index') }}" class="text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors">Courses</a></li>
                    <li><svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg></li>
                    <li><span class="text-gray-900 dark:text-white font-medium">{{ Str::limit($course->title, 40) }}</span></li>
                </ol>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Main Content --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Hero Card --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        {{-- Thumbnail --}}
                        <div class="relative h-48 sm:h-56 md:h-64 bg-gray-100 dark:bg-gray-700">
                            @if($course->thumbnail)
                                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                            @endif

                            {{-- Badges --}}
                            <div class="absolute top-4 left-4 flex gap-2">
                                <span class="px-3 py-1 rounded-lg text-xs font-medium
                                    @if($course->difficulty_level === 'beginner') bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300
                                    @elseif($course->difficulty_level === 'intermediate') bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300
                                    @else bg-rose-100 text-rose-700 dark:bg-rose-900 dark:text-rose-300
                                    @endif">
                                    {{ ucfirst($course->difficulty_level) }}
                                </span>
                                @if($course->is_free)
                                    <span class="px-3 py-1 rounded-lg text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300">
                                        Free
                                    </span>
                                @endif
                                @if($course->audience === 'school_only')
                                    <span class="px-3 py-1 rounded-lg text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300">
                                        School Only
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Course Info --}}
                        <div class="p-6">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ $course->title }}</h1>

                            @if($course->description)
                                <p class="text-gray-600 dark:text-gray-400 mb-6">{{ $course->description }}</p>
                            @endif

                            {{-- Instructor --}}
                            <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100 dark:border-gray-700">
                                @if($course->creator->avatar)
                                    <img class="h-12 w-12 rounded-full" src="{{ asset('storage/' . $course->creator->avatar) }}" alt="{{ $course->creator->name }}">
                                @else
                                    <div class="h-12 w-12 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                        <span class="text-lg font-medium text-indigo-600 dark:text-indigo-400">{{ substr($course->creator->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Instructor</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $course->creator->name }}</p>
                                </div>
                            </div>

                            {{-- Stats --}}
                            <div class="grid grid-cols-4 gap-4">
                                <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $course->chapters->count() }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Chapters</p>
                                </div>
                                <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $course->getTotalContentsCount() }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Lessons</p>
                                </div>
                                <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $course->enrollments->count() }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Students</p>
                                </div>
                                <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $course->estimated_duration_minutes ? floor($course->estimated_duration_minutes / 60).'h' : 'Self' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $course->estimated_duration_minutes ? 'Duration' : 'Paced' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @include('lms.courses.partials.show-content')
                </div>

                {{-- Sidebar --}}
                @include('lms.courses.partials.show-sidebar')
            </div>
        </div>
    </div>
</x-layouts.app>
