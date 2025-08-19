<div class="student-performances-index rounded-lg shadow-sm">
    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-900 shadow-sm border-b border-gray-200 dark:border-gray-700 px-4 sm:px-6 py-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Student Performance</h1>
                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                    Monitor and analyze your students' academic performance
                </p>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                <button wire:click="resetFilters"
                        class="flex items-center justify-center bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Reset Filters
                </button>
            </div>
        </div>
    </div>

    <!-- View Mode Tabs -->
    <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
        <nav class="px-4 sm:px-6">
            <div class="flex overflow-x-auto scrollbar-hide space-x-4 sm:space-x-8">
                <button wire:click="setViewMode('overview')"
                        class="flex-shrink-0 py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors
                        {{ $viewMode === 'overview' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Overview
                </button>
                <button wire:click="setViewMode('detailed')"
                        class="flex-shrink-0 py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors
                        {{ $viewMode === 'detailed' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m0 0h2a2 2 0 002-2V7a2 2 0 00-2-2h-2m0 0V3a2 2 0 00-2-2h-2a2 2 0 00-2 2v2M7 7h10"></path>
                    </svg>
                    Detailed
                </button>
                <button wire:click="setViewMode('subject-analysis')"
                        class="flex-shrink-0 py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors
                        {{ $viewMode === 'subject-analysis' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Subject Analysis
                </button>
            </div>
        </nav>
    </div>

    <!-- Filters Section -->
    <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 px-4 sm:px-6 py-4">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-4">
            <!-- Search -->
            <div class="xl:col-span-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search students..."
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm">
                </div>
            </div>

            <!-- Subject Filter -->
            <div>
                <select wire:model.live="selectedSubject"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Level Filter -->
            <div>
                <select wire:model.live="selectedLevel"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm">
                    <option value="">All Levels</option>
                    @foreach($levels as $level)
                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Performance Filter -->
            <div>
                <select wire:model.live="performanceFilter"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm">
                    <option value="all">All Performance</option>
                    <option value="excellent">Excellent (≥80%)</option>
                    <option value="good">Good (60-79%)</option>
                    <option value="needs_improvement">Needs Improvement (&lt;60%)</option>
                </select>
            </div>

            <!-- Time Range -->
            <div>
                <select wire:model.live="timeRange"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm">
                    <option value="7">Last 7 Days</option>
                    <option value="30">Last 30 Days</option>
                    <option value="90">Last 90 Days</option>
                    <option value="all">All Time</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <div class="bg-white dark:bg-gray-900 min-h-screen">
        @if($viewMode === 'subject-analysis')
            <!-- Subject Analysis View -->
            <div class="px-4 sm:px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($subjectAnalysis as $analysis)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $analysis['subject']->name }}
                                </h3>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $analysis['subject']->code }}
                                </span>
                            </div>

                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">Total Assignments</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $analysis['total_assignments'] }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">Total Submissions</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $analysis['total_submissions'] }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">Average Score</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($analysis['average_score'], 1) }}%</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">Completion Rate</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($analysis['completion_rate'], 1) }}%</span>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="mt-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Performance</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($analysis['average_score'], 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full transition-all duration-300"
                                         style="width: {{ min($analysis['average_score'], 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Subject Data Available</h3>
                            <p class="text-gray-500 dark:text-gray-400">No subjects or performance data found for analysis.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @else
            <!-- Students Performance View -->
            <div class="px-4 sm:px-6 py-6">
                @if($viewMode === 'overview')
                    <!-- Overview Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-4 mb-6">
                        @forelse($students as $student)
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow cursor-pointer"
                                 wire:click="showStudentDetails({{ $student->id }})">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center space-x-3">
                                    <x-avatar name="{{$student->user->name}}" class="w-8 h-8" text-size="text-xs" avatar="{{$student->user->avatar}}" />

                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                {{ $student->user->name }}
                                            </p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $student->performance_avg >= 80 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                                           ($student->performance_avg >= 60 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                                           'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200') }}">
                                        {{ $student->performance_grade }}
                                    </span>
                                </div>

                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Average Score</span>
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($student->performance_avg, 1) }}%</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Submissions</span>
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $student->total_submissions }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Completed</span>
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $student->completed_assignments }}</span>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div class="mt-3">
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="h-2 rounded-full transition-all duration-300
                                            {{ $student->performance_avg >= 80 ? 'bg-green-500' :
                                               ($student->performance_avg >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                             style="width: {{ min($student->performance_avg, 100) }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-12">
                                <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Students Found</h3>
                                <p class="text-gray-500 dark:text-gray-400">No students match your current filters.</p>
                            </div>
                        @endforelse
                    </div>
                @else
                    <!-- Detailed Table View -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <button wire:click="sortBy('name')" class="flex items-center space-x-1 hover:text-gray-700 dark:hover:text-gray-200">
                                            <span>Student</span>
                                            @if($sortBy === 'name')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                                </svg>
                                            @endif
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Academic Level
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <button wire:click="sortBy('performance_avg')" class="flex items-center space-x-1 hover:text-gray-700 dark:hover:text-gray-200">
                                            <span>Performance</span>
                                            @if($sortBy === 'performance_avg')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                                </svg>
                                            @endif
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <button wire:click="sortBy('total_submissions')" class="flex items-center space-x-1 hover:text-gray-700 dark:hover:text-gray-200">
                                            <span>Submissions</span>
                                            @if($sortBy === 'total_submissions')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                                </svg>
                                            @endif
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <button wire:click="sortBy('last_submission')" class="flex items-center space-x-1 hover:text-gray-700 dark:hover:text-gray-200">
                                            <span>Last Activity</span>
                                            @if($sortBy === 'last_submission')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                                </svg>
                                            @endif
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($students as $student)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                 <x-avatar name="{{$student->user->name}}" class="w-8 h-8" text-size="text-xs" avatar="{{$student->user->avatar}}" />
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $student->user->name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $student->user->email }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-white">
                                                {{ $student->academicLevel->name ?? 'N/A' }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $student->academicLevel->academicGroup->name ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-1 mr-3">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ number_format($student->performance_avg, 1) }}%
                                                    </div>
                                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-1">
                                                        <div class="h-2 rounded-full transition-all duration-300
                                                                {{ $student->performance_avg >= 80 ? 'bg-green-500' :
                                                                   ($student->performance_avg >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                                             style="width: {{ min($student->performance_avg, 100) }}%"></div>
                                                    </div>
                                                </div>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                        {{ $student->performance_avg >= 80 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                                                           ($student->performance_avg >= 60 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                                                           'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200') }}">
                                                        {{ $student->performance_grade }}
                                                    </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-white">
                                                {{ $student->completed_assignments }} / {{ $student->total_submissions }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $student->graded_assignments }} graded
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $student->last_submission ? \Carbon\Carbon::parse($student->last_submission)->diffForHumans() : 'No activity' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button wire:click="showStudentDetails({{ $student->id }})"
                                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                View Details
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Students Found</h3>
                                            <p class="text-gray-500 dark:text-gray-400">No students match your current filters.</p>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Pagination -->
                @if($total > $perPage)
                    <div class="flex items-center justify-between bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6 mt-4 rounded-lg">
                        <div class="flex-1 flex justify-between sm:hidden">
                            @if($hasPrevPage)
                                <button wire:click="previousPage"
                                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    Previous
                                </button>
                            @endif
                            @if($hasNextPage)
                                <button wire:click="nextPage"
                                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    Next
                                </button>
                            @endif
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    Showing
                                    <span class="font-medium">{{ (($currentPage - 1) * $perPage) + 1 }}</span>
                                    to
                                    <span class="font-medium">{{ min($currentPage * $perPage, $total) }}</span>
                                    of
                                    <span class="font-medium">{{ $total }}</span>
                                    results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    @if($hasPrevPage)
                                        <button wire:click="previousPage"
                                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <span class="sr-only">Previous</span>
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    @endif
                                    @if($hasNextPage)
                                        <button wire:click="nextPage"
                                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <span class="sr-only">Next</span>
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    @endif
                                </nav>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>

<!-- Student Detail Modal -->
@if($showStudentModal && $selectedStudent)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm transition-opacity duration-300 ease-out"
         aria-labelledby="modal-title" role="dialog" aria-modal="true" wire:transition.opacity>
        <div class="flex items-center justify-center min-h-screen px-4 py-8 sm:p-0">
            <!-- Overlay -->
            <div class="fixed inset-0 transition-opacity" wire:click="closeStudentModal" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Content -->
            <div class="relative inline-block w-full max-w-4xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl transform transition-all duration-300 ease-out sm:my-8 sm:align-middle"
                 x-data="{ open: true }"
                 x-show="open"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">

                <!-- Modal Header -->
                <div class="px-6 pt-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            {{ $selectedStudent->user->name }} - Performance Details
                        </h3>
                        <button wire:click="closeStudentModal"
                                class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-full"
                                aria-label="Close modal">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="px-6 py-4 sm:p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Student Info -->
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 rounded-xl p-6 shadow-sm">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2"></path>
                                </svg>
                                Student Information
                            </h4>
                            <dl class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <dt class="text-sm font-medium text-gray-600 dark:text-gray-300">Email</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white">{{ $selectedStudent->user->email }}</dd>
                                </div>
                                <div class="flex justify-between items-center">
                                    <dt class="text-sm font-medium text-gray-600 dark:text-gray-300">Academic Level</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white">{{ $selectedStudent->academicLevel->name ?? 'N/A' }}</dd>
                                </div>
                                <div class="flex justify-between items-center">
                                    <dt class="text-sm font-medium text-gray-600 dark:text-gray-300">Academic Group</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white">{{ $selectedStudent->academicLevel->academicGroup->name ?? 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Performance Summary -->
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 rounded-xl p-6 shadow-sm">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2V9a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path>
                                </svg>
                                Performance Summary
                            </h4>
                            <dl class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <dt class="text-sm font-medium text-gray-600 dark:text-gray-300">Overall Grade</dt>
                                    <dd class="text-sm font-semibold text-gray-900 dark:text-white">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                              :class="$selectedStudent->performance_grade ? '{{ $selectedStudent->performance_grade == "A" ? "bg-green-100 text-green-800" : ($selectedStudent->performance_grade == "B" ? "bg-blue-100 text-blue-800" : "bg-red-100 text-red-800") }}' : 'bg-gray-100 text-gray-800'">
                                            {{ $selectedStudent->performance_grade ?? 'N/A' }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="flex justify-between items-center">
                                    <dt class="text-sm font-medium text-gray-600 dark:text-gray-300">Average Score</dt>
                                    <dd class="text-sm font-semibold text-gray-900 dark:text-white">
                                        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2.5">
                                            <div class="bg-blue-500 h-2.5 rounded-full" style="width: {{ $selectedStudent->performance_avg ?? 0 }}%"></div>
                                        </div>
                                        <span class="mt-1 block text-xs">{{ number_format($selectedStudent->performance_avg ?? 0, 1) }}%</span>
                                    </dd>
                                </div>
                                <div class="flex justify-between items-center">
                                    <dt class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Submissions</dt>
                                    <dd class="text-sm font-semibold text-gray-900 dark:text-white">{{ $selectedStudent->total_submissions ?? 0 }}</dd>
                                </div>
                                <div class="flex justify-between items-center">
                                    <dt class="text-sm font-medium text-gray-600 dark:text-gray-300">Completed</dt>
                                    <dd class="text-sm font-semibold text-gray-900 dark:text-white">{{ $selectedStudent->completed_assignments ?? 0 }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Enrolled Subjects -->
                    <div class="mt-6">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.747 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            Enrolled Subjects
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @forelse($selectedStudent->academicSubjects as $subject)
                                <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow duration-200">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $subject->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $subject->code }}</div>
                                </div>
                            @empty
                                <div class="col-span-full text-center py-6 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No subjects assigned</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 sm:flex sm:flex-row-reverse sm:gap-4">
                    <button wire:click="closeStudentModal"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors duration-200"
                            aria-label="Close modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
</div>
