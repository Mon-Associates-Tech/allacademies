<div>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Page header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">My Teaching Subjects</h1>
                <p class="text-gray-600 dark:text-gray-400">Manage and view all subjects assigned to {{ $teacherName }}</p>
            </div>

            <!-- Quick Actions -->
            <div class="flex items-center space-x-3">
                <button wire:click="toggleViewMode"
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                    @if($viewMode === 'grid')
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        List View
                    @else
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                        Grid View
                    @endif
                </button>
            </div>
        </div>

        <!-- Teacher Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Academic Groups Card -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Academic Groups</h3>
                        <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $academicGroups->count() }}</p>
                    </div>
                    <div class="p-3 bg-indigo-100 dark:bg-indigo-900 rounded-full">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap gap-1">
                    @foreach($academicGroups->take(3) as $group)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                            {{ $group->name }}
                        </span>
                    @endforeach
                    @if($academicGroups->count() > 3)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                            +{{ $academicGroups->count() - 3 }} more
                        </span>
                    @endif
                </div>
            </div>

            <!-- Academic Levels Card -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Academic Levels</h3>
                        <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $academicLevels->count() }}</p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap gap-1">
                    @foreach($academicLevels->take(3) as $level)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            {{ $level->name }}
                        </span>
                    @endforeach
                    @if($academicLevels->count() > 3)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                            +{{ $academicLevels->count() - 3 }} more
                        </span>
                    @endif
                </div>
            </div>

            <!-- Total Subjects Card -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Total Subjects</h3>
                        <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $subjects->total() }}</p>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active Content Card -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Active Content</h3>
                        <p class="text-3xl font-bold text-orange-600 dark:text-orange-400">
                            {{ $subjects->sum(function($subject) { return $subject->quizzes->count() + $subject->examinations->count() + $subject->lessons->count(); }) }}
                        </p>
                    </div>
                    <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-full">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and filters -->
        <div class="mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Search Bar -->
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input wire:model.live="searchTerm" type="text"
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-violet-500 focus:border-violet-500"
                               placeholder="Search subjects, codes, descriptions, levels, or groups...">
                    </div>
                </div>

                <!-- Filters -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <!-- Group Filter -->
                    <div class="min-w-[150px]">
                        <select wire:model.live="filterByGroup"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                            <option value="">All Groups</option>
                            @foreach($availableGroups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Level Filter -->
                    <div class="min-w-[150px]">
                        <select wire:model.live="filterByLevel"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                            <option value="">All Levels</option>
                            @foreach($availableLevels as $level)
                                <option value="{{ $level->id }}">{{ $level->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort Options -->
                    <div class="flex items-center space-x-2">
                        <button wire:click="sortBy('name')"
                                class="px-3 py-2 text-sm font-medium {{ $sortBy === 'name' ? 'bg-violet-100 text-violet-700 dark:bg-violet-900 dark:text-violet-300' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300' }} border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                            Name
                            @if($sortBy === 'name')
                                <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                </svg>
                            @endif
                        </button>
                        <button wire:click="sortBy('group')"
                                class="px-3 py-2 text-sm font-medium {{ $sortBy === 'group' ? 'bg-violet-100 text-violet-700 dark:bg-violet-900 dark:text-violet-300' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300' }} border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                            Group
                            @if($sortBy === 'group')
                                <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                </svg>
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        @if($subjects->count() > 0)
            @if($viewMode === 'grid')
                <!-- Grid View -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($subjects as $subject)
                        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden hover:shadow-md transition-shadow duration-200">
                            <div class="p-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ $subject->academicLevel->academicGroup->name }}
                                            </span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                {{ $subject->academicLevel->name }}
                                            </span>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                            {{ $subject->name }}
                                        </h3>
                                        @if($subject->code)
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                                Code: {{ $subject->code }}
                                            </p>
                                        @endif
                                        @if($subject->description)
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4 line-clamp-2">
                                                {{ Str::limit($subject->description, 100) }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-purple-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- Stats -->
                                <div class="grid grid-cols-3 gap-4 mb-4">
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                            {{ $subject->lessons->count() }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Lessons</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                            {{ $subject->quizzes->count() }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Quizzes</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                                            {{ $subject->examinations->count() }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Exams</div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex space-x-2">
                                    <button wire:click="showSubjectDetails({{ $subject->id }})"
                                            class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- List View -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Subject
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Level & Group
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Content
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($subjects as $subject)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 bg-gradient-to-br from-violet-500 to-purple-600 rounded-lg flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $subject->name }}
                                                </div>
                                                @if($subject->code)
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $subject->code }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col space-y-1">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                    {{ $subject->academicLevel->academicGroup->name }}
                                                </span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    {{ $subject->academicLevel->name }}
                                                </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex space-x-4 text-sm">
                                            <div class="flex items-center text-blue-600 dark:text-blue-400">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                </svg>
                                                {{ $subject->lessons->count() }} lessons
                                            </div>
                                            <div class="flex items-center text-green-600 dark:text-green-400">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $subject->quizzes->count() }} quizzes
                                            </div>
                                            <div class="flex items-center text-orange-600 dark:text-orange-400">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                                </svg>
                                                {{ $subject->examinations->count() }} exams
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button wire:click="showSubjectDetails({{ $subject->id }})"
                                                class="text-violet-600 hover:text-violet-900 dark:text-violet-400 dark:hover:text-violet-300">
                                            View Details
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Pagination -->
            <div class="mt-6">
                {{ $subjects->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="mx-auto h-12 w-12 text-gray-400">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="h-full w-full">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No subjects found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    @if($searchTerm || $filterByLevel || $filterByGroup)
                        Try adjusting your search or filters.
                    @else
                        No subjects have been assigned to you yet.
                    @endif
                </p>
                @if($searchTerm || $filterByLevel || $filterByGroup)
                    <div class="mt-6">
                        <button wire:click="$set('searchTerm', ''); $set('filterByLevel', ''); $set('filterByGroup', '')"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-violet-700 bg-violet-100 hover:bg-violet-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500">
                            Clear filters
                        </button>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Subject Details Modal -->
    @if($showSubjectModal && $selectedSubject)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="w-full">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                                        {{ $selectedSubject->name }}
                                    </h3>
                                    <button wire:click="closeSubjectModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Basic Info -->
                                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">Basic Information</h4>
                                        <div class="space-y-2">
                                            @if($selectedSubject->code)
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Code:</span>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $selectedSubject->code }}</span>
                                                </div>
                                            @endif
                                            <div class="flex justify-between">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">Group:</span>
                                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $selectedSubject->academicLevel->academicGroup->name }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">Level:</span>
                                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $selectedSubject->academicLevel->name }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Content Stats -->
                                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">Content Statistics</h4>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="text-center">
                                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $selectedSubject->lessons->count() }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">Lessons</div>
                                            </div>
                                            <div class="text-center">
                                                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $selectedSubject->quizzes->count() }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">Quizzes</div>
                                            </div>
                                            <div class="text-center">
                                                <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $selectedSubject->examinations->count() }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">Exams</div>
                                            </div>
                                            <div class="text-center">
                                                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $selectedSubject->academicTopics->count() }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">Topics</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if($selectedSubject->description)
                                    <div class="mt-6">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">Description</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $selectedSubject->description }}</p>
                                    </div>
                                @endif

                                <!-- Recent Activity -->
                                <div class="mt-6">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">Quick Actions</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <a href="#" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                            View Lessons
                                        </a>
                                        <a href="#" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Manage Quizzes
                                        </a>
                                        <a href="#" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                            </svg>
                                            View Exams
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
