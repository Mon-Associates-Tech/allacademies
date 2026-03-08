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
        <!-- Overview Statistics Section (shown in overview mode) -->
        @if($viewMode === 'overview')
            <!-- Key Stats Cards -->
            <div class="px-4 sm:px-6 py-6">
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white">
                        <p class="text-blue-100 text-xs font-medium">Total Students</p>
                        <p class="text-2xl font-bold">{{ $overviewStats['total_students'] }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-4 text-white">
                        <p class="text-emerald-100 text-xs font-medium">Avg Performance</p>
                        <p class="text-2xl font-bold">{{ $overviewStats['avg_performance'] }}%</p>
                    </div>
                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white">
                        <p class="text-green-100 text-xs font-medium">Excellent (≥80%)</p>
                        <p class="text-2xl font-bold">{{ $overviewStats['excellent_count'] }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-4 text-white">
                        <p class="text-yellow-100 text-xs font-medium">Good (60-79%)</p>
                        <p class="text-2xl font-bold">{{ $overviewStats['good_count'] }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-4 text-white">
                        <p class="text-red-100 text-xs font-medium">Needs Help (&lt;60%)</p>
                        <p class="text-2xl font-bold">{{ $overviewStats['needs_improvement_count'] }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white">
                        <p class="text-purple-100 text-xs font-medium">Submission Rate</p>
                        <p class="text-2xl font-bold">{{ $overviewStats['submission_rate'] }}%</p>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Performance Distribution Chart -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Performance Distribution</h3>
                        <div class="h-64" x-data="performanceDistributionChart()" x-init="initChart()">
                            <canvas x-ref="distCanvas" class="w-full h-full"></canvas>
                        </div>
                    </div>

                    <!-- Students by Level Chart -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Students by Level</h3>
                        <div class="h-64" x-data="studentsByLevelChart()" x-init="initChart()">
                            <canvas x-ref="levelCanvas" class="w-full h-full"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Top Performers & Needing Attention -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    @if($overviewStats['top_performers']->count() > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            Top Performers
                        </h3>
                        <div class="space-y-3">
                            @foreach($overviewStats['top_performers'] as $student)
                            <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg cursor-pointer hover:bg-green-100 dark:hover:bg-green-900/30" wire:click="showStudentDetails({{ $student->id }})">
                                <div class="flex items-center space-x-3">
                                    <x-avatar name="{{ $student->user->name }}" class="w-8 h-8" text-size="text-xs" avatar="{{ $student->user->avatar ?? '' }}" />
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->user->name }}</span>
                                </div>
                                <span class="text-sm font-bold text-green-600 dark:text-green-400">{{ number_format($student->performance_avg, 1) }}%</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($overviewStats['needing_attention']->count() > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            Needs Attention
                        </h3>
                        <div class="space-y-3">
                            @foreach($overviewStats['needing_attention'] as $student)
                            <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 rounded-lg cursor-pointer hover:bg-red-100 dark:hover:bg-red-900/30" wire:click="showStudentDetails({{ $student->id }})">
                                <div class="flex items-center space-x-3">
                                    <x-avatar name="{{ $student->user->name }}" class="w-8 h-8" text-size="text-xs" avatar="{{ $student->user->avatar ?? '' }}" />
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->user->name }}</span>
                                </div>
                                <span class="text-sm font-bold text-red-600 dark:text-red-400">{{ number_format($student->performance_avg, 1) }}%</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">All Students</h3>
            </div>
        @endif

        @if($viewMode === 'subject-analysis')
            <!-- Subject Analysis View -->
            <div class="px-4 sm:px-6 py-6 space-y-6">
                @if(count($enhancedSubjectAnalysis) > 0)
                    <!-- Summary Stats Cards -->
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @php
                            $totalAssignments = collect($enhancedSubjectAnalysis)->sum('total_assignments');
                            $totalSubmissions = collect($enhancedSubjectAnalysis)->sum('total_submissions');
                            $totalGraded = collect($enhancedSubjectAnalysis)->sum('graded_submissions');
                            $overallAvg = collect($enhancedSubjectAnalysis)->avg('average_score');
                            $overallCompletion = collect($enhancedSubjectAnalysis)->avg('completion_rate');
                            $totalTopPerformers = collect($enhancedSubjectAnalysis)->sum('top_performers_count');
                        @endphp
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white">
                            <p class="text-blue-100 text-xs font-medium">Total Subjects</p>
                            <p class="text-2xl font-bold">{{ count($enhancedSubjectAnalysis) }}</p>
                        </div>
                        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white">
                            <p class="text-purple-100 text-xs font-medium">Total Assignments</p>
                            <p class="text-2xl font-bold">{{ $totalAssignments }}</p>
                        </div>
                        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-4 text-white">
                            <p class="text-emerald-100 text-xs font-medium">Avg Performance</p>
                            <p class="text-2xl font-bold">{{ number_format($overallAvg, 1) }}%</p>
                        </div>
                        <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-xl p-4 text-white">
                            <p class="text-cyan-100 text-xs font-medium">Completion Rate</p>
                            <p class="text-2xl font-bold">{{ number_format($overallCompletion, 1) }}%</p>
                        </div>
                        <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl p-4 text-white">
                            <p class="text-amber-100 text-xs font-medium">Graded</p>
                            <p class="text-2xl font-bold">{{ $totalGraded }}</p>
                        </div>
                        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white">
                            <p class="text-green-100 text-xs font-medium">Top Performers</p>
                            <p class="text-2xl font-bold">{{ $totalTopPerformers }}</p>
                        </div>
                    </div>

                    <!-- Charts Row 1: Performance & Grade Distribution -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Subject Performance Bar Chart -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Subject Performance Comparison</h3>
                                <div class="flex items-center space-x-4 text-xs">
                                    <div class="flex items-center">
                                        <span class="w-3 h-3 bg-blue-500 rounded-full mr-1"></span>
                                        <span class="text-gray-500 dark:text-gray-400">Avg Score</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-3 h-3 bg-emerald-500 rounded-full mr-1"></span>
                                        <span class="text-gray-500 dark:text-gray-400">Completion</span>
                                    </div>
                                </div>
                            </div>
                            <div class="h-72" x-data="subjectPerformanceChart()" x-init="initChart()">
                                <canvas x-ref="perfCanvas" class="w-full h-full"></canvas>
                            </div>
                        </div>

                        <!-- Grade Distribution Stacked Bar Chart -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Grade Distribution by Subject</h3>
                                <div class="flex items-center space-x-2 text-xs flex-wrap">
                                    <span class="flex items-center"><span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>A</span>
                                    <span class="flex items-center"><span class="w-2 h-2 bg-blue-500 rounded-full mr-1"></span>B</span>
                                    <span class="flex items-center"><span class="w-2 h-2 bg-yellow-500 rounded-full mr-1"></span>C</span>
                                    <span class="flex items-center"><span class="w-2 h-2 bg-orange-500 rounded-full mr-1"></span>D</span>
                                    <span class="flex items-center"><span class="w-2 h-2 bg-red-500 rounded-full mr-1"></span>F</span>
                                </div>
                            </div>
                            <div class="h-72" x-data="gradeDistributionChart()" x-init="initChart()">
                                <canvas x-ref="gradeCanvas" class="w-full h-full"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row 2: Submission Status & Weekly Trend -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Submission Status Pie Chart -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Submission Status Overview</h3>
                            </div>
                            <div class="h-64" x-data="submissionStatusChart()" x-init="initChart()">
                                <canvas x-ref="statusCanvas" class="w-full h-full"></canvas>
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                                @foreach($submissionStatusChart['labels'] as $index => $label)
                                    <div class="flex items-center">
                                        <span class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $submissionStatusChart['colors'][$index] }}"></span>
                                        <span class="text-gray-600 dark:text-gray-400">{{ $label }}: {{ number_format($submissionStatusChart['data'][$index]) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Weekly Performance Trend Line Chart -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Weekly Performance Trend</h3>
                                <div class="flex items-center space-x-4 text-xs">
                                    <div class="flex items-center">
                                        <span class="w-3 h-3 bg-purple-500 rounded-full mr-1"></span>
                                        <span class="text-gray-500 dark:text-gray-400">Avg Score</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-3 h-3 bg-cyan-500 rounded-full mr-1"></span>
                                        <span class="text-gray-500 dark:text-gray-400">Submissions</span>
                                    </div>
                                </div>
                            </div>
                            <div class="h-64" x-data="weeklyTrendChart()" x-init="initChart()">
                                <canvas x-ref="trendCanvas" class="w-full h-full"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Subject Rankings Table -->
                    @if(count($subjectRankings) > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Subject Performance Rankings</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Rank</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Subject</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Avg Score</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Completion</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Submissions</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Trend</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($subjectRankings as $ranking)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold
                                                {{ $ranking['rank'] === 1 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                                                   ($ranking['rank'] === 2 ? 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200' :
                                                   ($ranking['rank'] === 3 ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' :
                                                   'bg-blue-50 text-blue-800 dark:bg-blue-900 dark:text-blue-200')) }}">
                                                {{ $ranking['rank'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $ranking['subject'] }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $ranking['code'] }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <span class="text-sm font-semibold {{ $ranking['avg_score'] >= 80 ? 'text-green-600 dark:text-green-400' : ($ranking['avg_score'] >= 60 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">
                                                    {{ number_format($ranking['avg_score'], 1) }}%
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 w-24">
                                                <div class="h-2 rounded-full bg-blue-500" style="width: {{ min($ranking['completion_rate'], 100) }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($ranking['completion_rate'], 1) }}%</span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $ranking['total_submissions'] }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if($ranking['trend'] === 'up')
                                                <span class="inline-flex items-center text-green-600 dark:text-green-400">
                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                                                    Improving
                                                </span>
                                            @elseif($ranking['trend'] === 'down')
                                                <span class="inline-flex items-center text-red-600 dark:text-red-400">
                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                    Declining
                                                </span>
                                            @else
                                                <span class="inline-flex items-center text-gray-500 dark:text-gray-400">
                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>
                                                    Stable
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Enhanced Subject Cards -->
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Detailed Subject Analysis</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($enhancedSubjectAnalysis as $analysis)
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-shadow">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ $analysis['subject']->name }}
                                        </h4>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $analysis['subject']->code }}</span>
                                    </div>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        {{ $analysis['average_score'] >= 80 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                                           ($analysis['average_score'] >= 60 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                                           'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200') }}">
                                        {{ number_format($analysis['average_score'], 1) }}%
                                    </span>
                                </div>

                                <!-- Stats Grid -->
                                <div class="grid grid-cols-2 gap-3 mb-4">
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Assignments</p>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $analysis['total_assignments'] }}</p>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Submissions</p>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $analysis['total_submissions'] }}</p>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Graded</p>
                                        <p class="text-lg font-bold text-green-600 dark:text-green-400">{{ $analysis['graded_submissions'] }}</p>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Completion</p>
                                        <p class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ number_format($analysis['completion_rate'], 1) }}%</p>
                                    </div>
                                </div>

                                <!-- Grade Distribution Mini Bar -->
                                <div class="mb-4">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Grade Distribution</p>
                                    <div class="flex h-4 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700">
                                        @php
                                            $total = array_sum($analysis['grade_distribution']);
                                            $total = $total > 0 ? $total : 1;
                                        @endphp
                                        <div class="bg-green-500" style="width: {{ ($analysis['grade_distribution']['A'] / $total) * 100 }}%" title="A: {{ $analysis['grade_distribution']['A'] }}"></div>
                                        <div class="bg-blue-500" style="width: {{ ($analysis['grade_distribution']['B'] / $total) * 100 }}%" title="B: {{ $analysis['grade_distribution']['B'] }}"></div>
                                        <div class="bg-yellow-500" style="width: {{ ($analysis['grade_distribution']['C'] / $total) * 100 }}%" title="C: {{ $analysis['grade_distribution']['C'] }}"></div>
                                        <div class="bg-orange-500" style="width: {{ ($analysis['grade_distribution']['D'] / $total) * 100 }}%" title="D: {{ $analysis['grade_distribution']['D'] }}"></div>
                                        <div class="bg-red-500" style="width: {{ ($analysis['grade_distribution']['F'] / $total) * 100 }}%" title="F: {{ $analysis['grade_distribution']['F'] }}"></div>
                                    </div>
                                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        <span>A: {{ $analysis['grade_distribution']['A'] }}</span>
                                        <span>B: {{ $analysis['grade_distribution']['B'] }}</span>
                                        <span>C: {{ $analysis['grade_distribution']['C'] }}</span>
                                        <span>D: {{ $analysis['grade_distribution']['D'] }}</span>
                                        <span>F: {{ $analysis['grade_distribution']['F'] }}</span>
                                    </div>
                                </div>

                                <!-- Student Performance Indicators -->
                                <div class="flex justify-between items-center pt-3 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center text-sm">
                                        <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <span class="text-gray-600 dark:text-gray-400">{{ $analysis['top_performers_count'] }} top</span>
                                    </div>
                                    <div class="flex items-center text-sm">
                                        <svg class="w-4 h-4 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                        <span class="text-gray-600 dark:text-gray-400">{{ $analysis['struggling_count'] }} need help</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Subject Data Available</h3>
                        <p class="text-gray-500 dark:text-gray-400">No subjects or performance data found for analysis.</p>
                    </div>
                @endif
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
                <div class="px-6 py-4 sm:p-8 space-y-6 max-h-[70vh] overflow-y-auto">
                    @if($selectedStudentStats)
                    <!-- Key Stats Row -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white">
                            <p class="text-blue-100 text-xs font-medium">Average Score</p>
                            <p class="text-2xl font-bold">{{ $selectedStudentStats['avg_score'] }}%</p>
                        </div>
                        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white">
                            <p class="text-green-100 text-xs font-medium">Completion Rate</p>
                            <p class="text-2xl font-bold">{{ $selectedStudentStats['completion_rate'] }}%</p>
                        </div>
                        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white">
                            <p class="text-purple-100 text-xs font-medium">Overall Grade</p>
                            <p class="text-2xl font-bold">{{ $selectedStudentStats['overall_grade'] }}</p>
                        </div>
                        <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-xl p-4 text-white">
                            <p class="text-cyan-100 text-xs font-medium">Attendance</p>
                            <p class="text-2xl font-bold">{{ $selectedStudentStats['attendance']['attendance_rate'] }}%</p>
                        </div>
                    </div>

                    <!-- Comparison with Class -->
                    @if($studentComparison)
                    <div class="bg-gradient-to-r {{ $studentComparison['difference'] >= 0 ? 'from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-green-200 dark:border-green-800' : 'from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 border-red-200 dark:border-red-800' }} rounded-xl p-4 border">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Class Comparison</p>
                                <p class="text-lg font-bold {{ $studentComparison['difference'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $studentComparison['comparison_text'] }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Percentile Rank</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $studentComparison['percentile'] }}th</p>
                            </div>
                        </div>
                    </div>
                    @endif

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

                        <!-- Attendance Summary -->
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 rounded-xl p-6 shadow-sm">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                Attendance Summary
                            </h4>
                            <dl class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <dt class="text-sm font-medium text-gray-600 dark:text-gray-300">Present</dt>
                                    <dd class="text-sm font-semibold text-green-600 dark:text-green-400">{{ $selectedStudentStats['attendance']['present'] }}</dd>
                                </div>
                                <div class="flex justify-between items-center">
                                    <dt class="text-sm font-medium text-gray-600 dark:text-gray-300">Late</dt>
                                    <dd class="text-sm font-semibold text-yellow-600 dark:text-yellow-400">{{ $selectedStudentStats['attendance']['late'] }}</dd>
                                </div>
                                <div class="flex justify-between items-center">
                                    <dt class="text-sm font-medium text-gray-600 dark:text-gray-300">Absent</dt>
                                    <dd class="text-sm font-semibold text-red-600 dark:text-red-400">{{ $selectedStudentStats['attendance']['absent'] }}</dd>
                                </div>
                                <div class="flex justify-between items-center">
                                    <dt class="text-sm font-medium text-gray-600 dark:text-gray-300">Excused</dt>
                                    <dd class="text-sm font-semibold text-gray-600 dark:text-gray-400">{{ $selectedStudentStats['attendance']['excused'] }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Subject Performance -->
                    @if(count($selectedStudentStats['subject_performance']) > 0)
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Performance by Subject
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($selectedStudentStats['subject_performance'] as $subjectPerf)
                            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $subjectPerf['subject'] }}</span>
                                    <span class="text-sm font-bold {{ $subjectPerf['avg_score'] >= 80 ? 'text-green-600' : ($subjectPerf['avg_score'] >= 60 ? 'text-yellow-600' : 'text-red-600') }}">{{ $subjectPerf['avg_score'] }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $subjectPerf['avg_score'] >= 80 ? 'bg-green-500' : ($subjectPerf['avg_score'] >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ min($subjectPerf['avg_score'], 100) }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $subjectPerf['graded'] }} graded / {{ $subjectPerf['total_submissions'] }} submitted</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Grade Distribution -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Grade Distribution</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($selectedStudentStats['grade_distribution'] as $grade => $count)
                                <div class="px-3 py-2 rounded-lg {{ $grade == 'A+' || $grade == 'A' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : ($grade == 'B+' || $grade == 'B' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' : ($grade == 'C' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300')) }}">
                                    <span class="font-bold">{{ $grade }}</span>: {{ $count }}
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Strengths & Weaknesses -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Strengths & Areas to Improve</h4>
                            @if(count($selectedStudentStats['strengths']) > 0)
                            <div class="mb-3">
                                <p class="text-xs font-medium text-green-600 dark:text-green-400 mb-1">Strengths</p>
                                @foreach($selectedStudentStats['strengths'] as $strength)
                                <span class="inline-block px-2 py-1 text-xs bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 rounded mr-1 mb-1">{{ $strength['subject'] }} ({{ $strength['avg_score'] }}%)</span>
                                @endforeach
                            </div>
                            @endif
                            @if(count($selectedStudentStats['weaknesses']) > 0)
                            <div>
                                <p class="text-xs font-medium text-red-600 dark:text-red-400 mb-1">Needs Improvement</p>
                                @foreach($selectedStudentStats['weaknesses'] as $weakness)
                                <span class="inline-block px-2 py-1 text-xs bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 rounded mr-1 mb-1">{{ $weakness['subject'] }} ({{ $weakness['avg_score'] }}%)</span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Assignments -->
                    @if(count($selectedStudentStats['recent_assignments']) > 0)
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Assignments</h4>
                        <div class="space-y-2">
                            @foreach($selectedStudentStats['recent_assignments'] as $assignment)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $assignment['title'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $assignment['subject'] }}</p>
                                </div>
                                <div class="text-right">
                                    @if($assignment['status'] === 'graded')
                                    <span class="text-sm font-bold {{ $assignment['percentage'] >= 80 ? 'text-green-600' : ($assignment['percentage'] >= 60 ? 'text-yellow-600' : 'text-red-600') }}">{{ $assignment['percentage'] }}%</span>
                                    @else
                                    <span class="text-xs px-2 py-1 rounded-full {{ $assignment['status'] === 'submitted' ? 'bg-blue-100 text-blue-800' : ($assignment['status'] === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">{{ ucfirst(str_replace('_', ' ', $assignment['status'])) }}</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    @else
                    <!-- Fallback to basic info if stats not loaded -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 rounded-xl p-6 shadow-sm">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Student Information</h4>
                            <dl class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <dt class="text-sm font-medium text-gray-600 dark:text-gray-300">Email</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white">{{ $selectedStudent->user->email }}</dd>
                                </div>
                                <div class="flex justify-between items-center">
                                    <dt class="text-sm font-medium text-gray-600 dark:text-gray-300">Academic Level</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white">{{ $selectedStudent->academicLevel->name ?? 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                    @endif

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

<!-- Chart Scripts -->
<script>
    function performanceDistributionChart() {
        return {
            initChart() {
                const canvas = this.$refs.distCanvas;
                if (!canvas) return;
                const ctx = canvas.getContext('2d');
                const labels = @json($performanceDistribution['labels']);
                const data = @json($performanceDistribution['data']);
                const colors = @json($performanceDistribution['colors']);

                this.drawBarChart(ctx, canvas, labels, data, colors);
            },
            drawBarChart(ctx, canvas, labels, data, colors) {
                const rect = canvas.getBoundingClientRect();
                canvas.width = rect.width * window.devicePixelRatio;
                canvas.height = rect.height * window.devicePixelRatio;
                ctx.scale(window.devicePixelRatio, window.devicePixelRatio);

                const width = rect.width;
                const height = rect.height;
                const padding = { top: 20, right: 20, bottom: 60, left: 50 };
                const chartWidth = width - padding.left - padding.right;
                const chartHeight = height - padding.top - padding.bottom;

                const maxValue = Math.max(...data, 1);
                const barWidth = (chartWidth / data.length) * 0.7;
                const barGap = (chartWidth / data.length) * 0.3;

                ctx.clearRect(0, 0, width, height);

                // Draw grid lines
                ctx.strokeStyle = document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB';
                ctx.lineWidth = 1;
                for (let i = 0; i <= 5; i++) {
                    const y = padding.top + (chartHeight / 5) * i;
                    ctx.beginPath();
                    ctx.moveTo(padding.left, y);
                    ctx.lineTo(width - padding.right, y);
                    ctx.stroke();

                    ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280';
                    ctx.font = '11px sans-serif';
                    ctx.textAlign = 'right';
                    const value = Math.round(maxValue - (maxValue / 5) * i);
                    ctx.fillText(value.toString(), padding.left - 8, y + 4);
                }

                // Draw bars
                data.forEach((value, index) => {
                    const barHeight = (value / maxValue) * chartHeight;
                    const x = padding.left + (index * (barWidth + barGap)) + barGap / 2;
                    const y = padding.top + chartHeight - barHeight;

                    ctx.fillStyle = colors[index] || '#3B82F6';
                    ctx.beginPath();
                    ctx.roundRect(x, y, barWidth, barHeight, 4);
                    ctx.fill();

                    // X-axis labels
                    ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280';
                    ctx.font = '10px sans-serif';
                    ctx.textAlign = 'center';
                    ctx.save();
                    ctx.translate(x + barWidth / 2, height - padding.bottom + 15);
                    ctx.rotate(-Math.PI / 6);
                    ctx.fillText(labels[index], 0, 0);
                    ctx.restore();
                });
            }
        };
    }

    function studentsByLevelChart() {
        return {
            initChart() {
                const canvas = this.$refs.levelCanvas;
                if (!canvas) return;
                const ctx = canvas.getContext('2d');
                const labels = @json($studentsByLevel['labels']);
                const data = @json($studentsByLevel['data']);
                const colors = @json($studentsByLevel['colors']);

                this.drawDoughnutChart(ctx, canvas, labels, data, colors);
            },
            drawDoughnutChart(ctx, canvas, labels, data, colors) {
                const rect = canvas.getBoundingClientRect();
                canvas.width = rect.width * window.devicePixelRatio;
                canvas.height = rect.height * window.devicePixelRatio;
                ctx.scale(window.devicePixelRatio, window.devicePixelRatio);

                const width = rect.width;
                const height = rect.height;
                const centerX = width / 2;
                const centerY = height / 2;
                const radius = Math.min(width, height) / 2 - 40;
                const innerRadius = radius * 0.6;

                const total = data.reduce((sum, val) => sum + val, 0);
                if (total === 0) {
                    ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280';
                    ctx.font = '14px sans-serif';
                    ctx.textAlign = 'center';
                    ctx.fillText('No data available', centerX, centerY);
                    return;
                }

                let startAngle = -Math.PI / 2;

                data.forEach((value, index) => {
                    const sliceAngle = (value / total) * 2 * Math.PI;
                    const endAngle = startAngle + sliceAngle;

                    ctx.beginPath();
                    ctx.arc(centerX, centerY, radius, startAngle, endAngle);
                    ctx.arc(centerX, centerY, innerRadius, endAngle, startAngle, true);
                    ctx.closePath();
                    ctx.fillStyle = colors[index] || '#3B82F6';
                    ctx.fill();

                    startAngle = endAngle;
                });

                // Center text
                ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#F9FAFB' : '#111827';
                ctx.font = 'bold 24px sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText(total.toString(), centerX, centerY - 5);
                ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280';
                ctx.font = '12px sans-serif';
                ctx.fillText('Students', centerX, centerY + 15);

                // Legend
                const legendY = height - 25;
                const legendItemWidth = width / labels.length;
                labels.forEach((label, index) => {
                    const x = legendItemWidth * index + legendItemWidth / 2;
                    ctx.fillStyle = colors[index] || '#3B82F6';
                    ctx.fillRect(x - 30, legendY, 10, 10);
                    ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280';
                    ctx.font = '10px sans-serif';
                    ctx.textAlign = 'left';
                    const displayLabel = label.length > 8 ? label.substring(0, 8) + '...' : label;
                    ctx.fillText(displayLabel + ' (' + data[index] + ')', x - 16, legendY + 9);
                });
            }
        };
    }

    // Subject Analysis Charts
    function subjectPerformanceChart() {
        return {
            initChart() {
                const canvas = this.$refs.perfCanvas;
                if (!canvas) return;
                const ctx = canvas.getContext('2d');
                const chartData = @json($subjectPerformanceChart ?? []);

                if (!chartData.labels || chartData.labels.length === 0) {
                    this.drawNoData(ctx, canvas);
                    return;
                }

                this.drawGroupedBarChart(ctx, canvas, chartData);
            },
            drawNoData(ctx, canvas) {
                const rect = canvas.getBoundingClientRect();
                canvas.width = rect.width * window.devicePixelRatio;
                canvas.height = rect.height * window.devicePixelRatio;
                ctx.scale(window.devicePixelRatio, window.devicePixelRatio);
                ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280';
                ctx.font = '14px sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('No data available', rect.width / 2, rect.height / 2);
            },
            drawGroupedBarChart(ctx, canvas, chartData) {
                const rect = canvas.getBoundingClientRect();
                canvas.width = rect.width * window.devicePixelRatio;
                canvas.height = rect.height * window.devicePixelRatio;
                ctx.scale(window.devicePixelRatio, window.devicePixelRatio);

                const width = rect.width;
                const height = rect.height;
                const padding = { top: 20, right: 20, bottom: 60, left: 50 };
                const chartWidth = width - padding.left - padding.right;
                const chartHeight = height - padding.top - padding.bottom;

                const labels = chartData.labels;
                const avgScores = chartData.avgScores;
                const completionRates = chartData.completionRates;

                const maxValue = 100;
                const groupWidth = chartWidth / labels.length;
                const barWidth = groupWidth * 0.35;
                const barGap = groupWidth * 0.1;

                ctx.clearRect(0, 0, width, height);

                // Draw grid lines
                ctx.strokeStyle = document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB';
                ctx.lineWidth = 1;
                for (let i = 0; i <= 5; i++) {
                    const y = padding.top + (chartHeight / 5) * i;
                    ctx.beginPath();
                    ctx.moveTo(padding.left, y);
                    ctx.lineTo(width - padding.right, y);
                    ctx.stroke();

                    ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280';
                    ctx.font = '11px sans-serif';
                    ctx.textAlign = 'right';
                    const value = Math.round(maxValue - (maxValue / 5) * i);
                    ctx.fillText(value + '%', padding.left - 8, y + 4);
                }

                // Draw bars
                labels.forEach((label, index) => {
                    const groupX = padding.left + (index * groupWidth) + barGap;

                    // Avg Score bar (blue)
                    const avgHeight = (avgScores[index] / maxValue) * chartHeight;
                    const avgY = padding.top + chartHeight - avgHeight;
                    ctx.fillStyle = '#3B82F6';
                    ctx.beginPath();
                    ctx.roundRect(groupX, avgY, barWidth, avgHeight, 3);
                    ctx.fill();

                    // Completion Rate bar (emerald)
                    const compHeight = (completionRates[index] / maxValue) * chartHeight;
                    const compY = padding.top + chartHeight - compHeight;
                    ctx.fillStyle = '#10B981';
                    ctx.beginPath();
                    ctx.roundRect(groupX + barWidth + 4, compY, barWidth, compHeight, 3);
                    ctx.fill();

                    // X-axis labels
                    ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280';
                    ctx.font = '10px sans-serif';
                    ctx.textAlign = 'center';
                    ctx.save();
                    ctx.translate(groupX + barWidth, height - padding.bottom + 15);
                    ctx.rotate(-Math.PI / 6);
                    const displayLabel = label.length > 12 ? label.substring(0, 12) + '...' : label;
                    ctx.fillText(displayLabel, 0, 0);
                    ctx.restore();
                });
            }
        };
    }

    function gradeDistributionChart() {
        return {
            initChart() {
                const canvas = this.$refs.gradeCanvas;
                if (!canvas) return;
                const ctx = canvas.getContext('2d');
                const chartData = @json($subjectGradeDistribution ?? []);

                if (!chartData.labels || chartData.labels.length === 0) {
                    this.drawNoData(ctx, canvas);
                    return;
                }

                this.drawStackedBarChart(ctx, canvas, chartData);
            },
            drawNoData(ctx, canvas) {
                const rect = canvas.getBoundingClientRect();
                canvas.width = rect.width * window.devicePixelRatio;
                canvas.height = rect.height * window.devicePixelRatio;
                ctx.scale(window.devicePixelRatio, window.devicePixelRatio);
                ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280';
                ctx.font = '14px sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('No data available', rect.width / 2, rect.height / 2);
            },
            drawStackedBarChart(ctx, canvas, chartData) {
                const rect = canvas.getBoundingClientRect();
                canvas.width = rect.width * window.devicePixelRatio;
                canvas.height = rect.height * window.devicePixelRatio;
                ctx.scale(window.devicePixelRatio, window.devicePixelRatio);

                const width = rect.width;
                const height = rect.height;
                const padding = { top: 20, right: 20, bottom: 60, left: 50 };
                const chartWidth = width - padding.left - padding.right;
                const chartHeight = height - padding.top - padding.bottom;

                const labels = chartData.labels;
                const datasets = chartData.datasets;

                // Calculate max stack height
                let maxStack = 0;
                labels.forEach((_, index) => {
                    let stackTotal = 0;
                    datasets.forEach(ds => {
                        stackTotal += ds.data[index] || 0;
                    });
                    maxStack = Math.max(maxStack, stackTotal);
                });
                maxStack = maxStack || 1;

                const barWidth = (chartWidth / labels.length) * 0.7;
                const barGap = (chartWidth / labels.length) * 0.3;

                ctx.clearRect(0, 0, width, height);

                // Draw grid lines
                ctx.strokeStyle = document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB';
                ctx.lineWidth = 1;
                for (let i = 0; i <= 5; i++) {
                    const y = padding.top + (chartHeight / 5) * i;
                    ctx.beginPath();
                    ctx.moveTo(padding.left, y);
                    ctx.lineTo(width - padding.right, y);
                    ctx.stroke();

                    ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280';
                    ctx.font = '11px sans-serif';
                    ctx.textAlign = 'right';
                    const value = Math.round(maxStack - (maxStack / 5) * i);
                    ctx.fillText(value.toString(), padding.left - 8, y + 4);
                }

                // Draw stacked bars
                labels.forEach((label, labelIndex) => {
                    const x = padding.left + (labelIndex * (barWidth + barGap)) + barGap / 2;
                    let currentY = padding.top + chartHeight;

                    datasets.forEach(ds => {
                        const value = ds.data[labelIndex] || 0;
                        const barHeight = (value / maxStack) * chartHeight;

                        if (barHeight > 0) {
                            currentY -= barHeight;
                            ctx.fillStyle = ds.color;
                            ctx.fillRect(x, currentY, barWidth, barHeight);
                        }
                    });

                    // X-axis labels
                    ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280';
                    ctx.font = '10px sans-serif';
                    ctx.textAlign = 'center';
                    ctx.save();
                    ctx.translate(x + barWidth / 2, height - padding.bottom + 15);
                    ctx.rotate(-Math.PI / 6);
                    const displayLabel = label.length > 12 ? label.substring(0, 12) + '...' : label;
                    ctx.fillText(displayLabel, 0, 0);
                    ctx.restore();
                });
            }
        };
    }

    function submissionStatusChart() {
        return {
            initChart() {
                const canvas = this.$refs.statusCanvas;
                if (!canvas) return;
                const ctx = canvas.getContext('2d');
                const chartData = @json($submissionStatusChart ?? []);

                if (!chartData.labels || chartData.data.reduce((a, b) => a + b, 0) === 0) {
                    this.drawNoData(ctx, canvas);
                    return;
                }

                this.drawDoughnutChart(ctx, canvas, chartData);
            },
            drawNoData(ctx, canvas) {
                const rect = canvas.getBoundingClientRect();
                canvas.width = rect.width * window.devicePixelRatio;
                canvas.height = rect.height * window.devicePixelRatio;
                ctx.scale(window.devicePixelRatio, window.devicePixelRatio);
                ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280';
                ctx.font = '14px sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('No data available', rect.width / 2, rect.height / 2);
            },
            drawDoughnutChart(ctx, canvas, chartData) {
                const rect = canvas.getBoundingClientRect();
                canvas.width = rect.width * window.devicePixelRatio;
                canvas.height = rect.height * window.devicePixelRatio;
                ctx.scale(window.devicePixelRatio, window.devicePixelRatio);

                const width = rect.width;
                const height = rect.height;
                const centerX = width / 2;
                const centerY = height / 2;
                const radius = Math.min(width, height) / 2 - 20;
                const innerRadius = radius * 0.55;

                const labels = chartData.labels;
                const data = chartData.data;
                const colors = chartData.colors;
                const total = data.reduce((sum, val) => sum + val, 0);

                ctx.clearRect(0, 0, width, height);

                let startAngle = -Math.PI / 2;

                data.forEach((value, index) => {
                    if (value === 0) return;
                    const sliceAngle = (value / total) * 2 * Math.PI;
                    const endAngle = startAngle + sliceAngle;

                    ctx.beginPath();
                    ctx.arc(centerX, centerY, radius, startAngle, endAngle);
                    ctx.arc(centerX, centerY, innerRadius, endAngle, startAngle, true);
                    ctx.closePath();
                    ctx.fillStyle = colors[index];
                    ctx.fill();

                    startAngle = endAngle;
                });

                // Center text
                ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#F9FAFB' : '#111827';
                ctx.font = 'bold 28px sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText(total.toString(), centerX, centerY - 5);
                ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280';
                ctx.font = '12px sans-serif';
                ctx.fillText('Total', centerX, centerY + 15);
            }
        };
    }

    function weeklyTrendChart() {
        return {
            initChart() {
                const canvas = this.$refs.trendCanvas;
                if (!canvas) return;
                const ctx = canvas.getContext('2d');
                const chartData = @json($weeklyTrendChart ?? []);

                if (!chartData.labels || chartData.labels.length === 0) {
                    this.drawNoData(ctx, canvas);
                    return;
                }

                this.drawLineChart(ctx, canvas, chartData);
            },
            drawNoData(ctx, canvas) {
                const rect = canvas.getBoundingClientRect();
                canvas.width = rect.width * window.devicePixelRatio;
                canvas.height = rect.height * window.devicePixelRatio;
                ctx.scale(window.devicePixelRatio, window.devicePixelRatio);
                ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280';
                ctx.font = '14px sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('No data available', rect.width / 2, rect.height / 2);
            },
            drawLineChart(ctx, canvas, chartData) {
                const rect = canvas.getBoundingClientRect();
                canvas.width = rect.width * window.devicePixelRatio;
                canvas.height = rect.height * window.devicePixelRatio;
                ctx.scale(window.devicePixelRatio, window.devicePixelRatio);

                const width = rect.width;
                const height = rect.height;
                const padding = { top: 20, right: 50, bottom: 40, left: 50 };
                const chartWidth = width - padding.left - padding.right;
                const chartHeight = height - padding.top - padding.bottom;

                const labels = chartData.labels;
                const avgScores = chartData.avgScores;
                const submissionCounts = chartData.submissionCounts;

                const maxScore = 100;
                const maxSubmissions = Math.max(...submissionCounts, 1);

                ctx.clearRect(0, 0, width, height);

                // Draw grid lines
                ctx.strokeStyle = document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB';
                ctx.lineWidth = 1;
                for (let i = 0; i <= 5; i++) {
                    const y = padding.top + (chartHeight / 5) * i;
                    ctx.beginPath();
                    ctx.moveTo(padding.left, y);
                    ctx.lineTo(width - padding.right, y);
                    ctx.stroke();

                    // Left Y-axis (Score)
                    ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280';
                    ctx.font = '11px sans-serif';
                    ctx.textAlign = 'right';
                    const scoreValue = Math.round(maxScore - (maxScore / 5) * i);
                    ctx.fillText(scoreValue + '%', padding.left - 8, y + 4);

                    // Right Y-axis (Submissions)
                    ctx.textAlign = 'left';
                    const subValue = Math.round(maxSubmissions - (maxSubmissions / 5) * i);
                    ctx.fillText(subValue.toString(), width - padding.right + 8, y + 4);
                }

                const pointSpacing = chartWidth / (labels.length - 1 || 1);

                // Draw submission count bars (background)
                ctx.fillStyle = 'rgba(6, 182, 212, 0.3)';
                submissionCounts.forEach((count, index) => {
                    const x = padding.left + (index * pointSpacing) - 10;
                    const barHeight = (count / maxSubmissions) * chartHeight;
                    const y = padding.top + chartHeight - barHeight;
                    ctx.fillRect(x, y, 20, barHeight);
                });

                // Draw avg score line
                ctx.strokeStyle = '#8B5CF6';
                ctx.lineWidth = 3;
                ctx.beginPath();
                let firstPoint = true;

                avgScores.forEach((score, index) => {
                    if (score === null) return;
                    const x = padding.left + (index * pointSpacing);
                    const y = padding.top + chartHeight - (score / maxScore) * chartHeight;

                    if (firstPoint) {
                        ctx.moveTo(x, y);
                        firstPoint = false;
                    } else {
                        ctx.lineTo(x, y);
                    }
                });
                ctx.stroke();

                // Draw points
                avgScores.forEach((score, index) => {
                    if (score === null) return;
                    const x = padding.left + (index * pointSpacing);
                    const y = padding.top + chartHeight - (score / maxScore) * chartHeight;

                    ctx.beginPath();
                    ctx.arc(x, y, 5, 0, Math.PI * 2);
                    ctx.fillStyle = '#8B5CF6';
                    ctx.fill();
                    ctx.strokeStyle = '#fff';
                    ctx.lineWidth = 2;
                    ctx.stroke();
                });

                // X-axis labels
                ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280';
                ctx.font = '10px sans-serif';
                ctx.textAlign = 'center';
                labels.forEach((label, index) => {
                    const x = padding.left + (index * pointSpacing);
                    ctx.fillText(label, x, height - padding.bottom + 20);
                });
            }
        };
    }
</script>
</div>
