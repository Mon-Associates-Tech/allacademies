<div class="teachers-dashboard rounded-lg  shadow-sm overflow-hidden">
    <!-- Dashboard Header -->
    <div class="bg-white dark:bg-gray-900 shadow-sm border-b border-gray-200 dark:border-gray-700 px-4 sm:px-6 py-4">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Teacher Dashboard</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Welcome back, {{ auth()->user()->name }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">{{ now()->format('l, F j, Y') }}</p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <select wire:model.live="selectedPeriod" class="rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm">
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                    <option value="year">This Year</option>
                </select>
                <a href="{{ route('teachers.assignments.create') }}"
                   class="flex items-center justify-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create Assignment
                </a>
                <button wire:click="refreshDashboard"
                        class="flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Dashboard Navigation Tabs -->
    <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-10">
        <nav class="px-4 sm:px-6">
            <div class="flex overflow-x-auto scrollbar-hide space-x-4 sm:space-x-8">
                <button
                    wire:click="setActiveTab('dashboard')"
                    class="flex-shrink-0 py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'dashboard' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600' }}"
                >
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    Overview
                </button>
                <button
                    wire:click="setActiveTab('students')"
                    class="flex-shrink-0 py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'students' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600' }}"
                >
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Students ({{ $totalStudents }})
                </button>
                <button
                    wire:click="setActiveTab('assignments')"
                    class="flex-shrink-0 py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'assignments' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600' }}"
                >
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    Assignments ({{ $totalAssignments }})
                </button>
                <button
                    wire:click="setActiveTab('subjects')"
                    class="flex-shrink-0 py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'subjects' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600' }}"
                >
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Subjects ({{ $totalSubjects }})
                </button>
                <button
                    wire:click="setActiveTab('levels')"
                    class="flex-shrink-0 py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'levels' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600' }}"
                >
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Academic Levels
                </button>
            </div>
        </nav>
    </div>

    <!-- Dashboard Content -->
    <div class="p-4 sm:p-6 bg-gray-50 dark:bg-gray-800 min-h-screen">
        <!-- Overview Tab -->
        @if($activeTab === 'dashboard')
            @php
                $submissionStats = $this->submissionStats();
                $attendanceStats = $this->attendanceStats();
                $messageStats = $this->messageStats();
                $virtualStats = $this->virtualClassroomStats();
            @endphp

            <!-- Primary Stats Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Students</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalStudents }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Assignments</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalAssignments }}</p>
                            <p class="text-xs text-green-600">{{ $submissionStats['pending_review'] }} pending review</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Subjects</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalSubjects }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-lg">
                            <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Due Soon</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ count($upcomingAssignments) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secondary Stats Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Attendance Rate</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $attendanceStats['attendance_rate'] }}%</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-cyan-100 dark:bg-cyan-900 rounded-lg">
                            <svg class="w-6 h-6 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Virtual Sessions</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $virtualStats['total_sessions'] }}</p>
                            <p class="text-xs text-cyan-600">{{ $virtualStats['upcoming_sessions'] }} upcoming</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-pink-100 dark:bg-pink-900 rounded-lg">
                            <svg class="w-6 h-6 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Messages</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $messageStats['sent_messages'] }}</p>
                            @if($messageStats['unread_messages'] > 0)
                                <p class="text-xs text-pink-600">{{ $messageStats['unread_messages'] }} unread</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-emerald-100 dark:bg-emerald-900 rounded-lg">
                            <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Avg Score</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $submissionStats['average_score'] }}%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Student Distribution Chart -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Student Distribution by Level</h3>
                        <a href="{{ route('teachers.students.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View All</a>
                    </div>
                    @php $studentDistribution = $this->studentDistributionChartData(); @endphp
                    @if(count($studentDistribution['data']) > 0)
                        <div class="h-64" x-data="studentDistributionChart()" x-init="initChart()">
                            <canvas x-ref="distributionCanvas" class="w-full h-full"></canvas>
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                            @foreach($studentDistribution['labels'] as $index => $label)
                                <div class="flex items-center">
                                    <span class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $studentDistribution['colors'][$index] }}"></span>
                                    <span class="text-gray-600 dark:text-gray-400">{{ $label }}: {{ number_format($studentDistribution['data'][$index]) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="h-64 flex items-center justify-center">
                            <p class="text-gray-500 dark:text-gray-400">No student data available</p>
                        </div>
                    @endif
                </div>

                <!-- Assignment Trends Chart -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Assignment Activity</h3>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <span class="w-3 h-3 bg-blue-500 rounded-full mr-1"></span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Created</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-3 h-3 bg-green-500 rounded-full mr-1"></span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Submissions</span>
                            </div>
                        </div>
                    </div>
                    @php $assignmentTrend = $this->assignmentTrendChartData(); @endphp
                    <div class="h-64" x-data="assignmentTrendChart()" x-init="initChart()">
                        <canvas x-ref="trendCanvas" class="w-full h-full"></canvas>
                    </div>
                    <div class="mt-4 flex justify-between text-sm text-gray-500 dark:text-gray-400">
                        <span>Total Created: {{ array_sum($assignmentTrend['created']) }}</span>
                        <span>Total Submissions: {{ array_sum($assignmentTrend['completed']) }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            @if($showQuickActions)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ count($this->quickActionItems()) }} actions available</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach($this->quickActionItems() as $action)
                            <a href="{{ route($action['route']) }}" class="relative p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-blue-300 dark:hover:border-blue-600 hover:shadow-md transition-all duration-200 group bg-white dark:bg-gray-800">
                                <div class="flex items-start space-x-3">
                                    <div class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg group-hover:bg-blue-100 dark:group-hover:bg-blue-900/50 transition-colors">
                                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($action['icon'] === 'clipboard-list')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                            @elseif($action['icon'] === 'check-circle')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            @elseif($action['icon'] === 'academic-cap')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                                            @elseif($action['icon'] === 'mail')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            @elseif($action['icon'] === 'video-camera')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            @elseif($action['icon'] === 'user-group')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            @elseif($action['icon'] === 'book-open')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            @elseif($action['icon'] === 'calendar')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            @endif
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">{{ $action['title'] }}</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $action['description'] }}</p>
                                    </div>
                                </div>
                                @if(isset($action['badge']) && $action['badge'] > 0)
                                    <div class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center animate-pulse">
                                        {{ $action['badge'] > 99 ? '99+' : $action['badge'] }}
                                    </div>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Recent and Upcoming Assignments -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
                <!-- Recent Assignments -->
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Assignments</h3>
                    </div>
                    <div class="p-6">
                        @if(count($recentAssignments) > 0)
                            <div class="space-y-4">
                                @foreach($recentAssignments as $assignment)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-gray-900 dark:text-white truncate">{{ $assignment['title'] ?? 'Untitled Assignment' }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $assignment['academic_subject']['name'] ?? 'Unknown Subject' }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-500">
                                                {{ \Carbon\Carbon::parse($assignment['created_at'])->diffForHumans() }}
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0 ml-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                {{ $assignment['status'] ?? 'Active' }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p class="mt-2 text-gray-500 dark:text-gray-400">No recent assignments</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Upcoming Assignments -->
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Upcoming Assignments</h3>
                    </div>
                    <div class="p-6">
                        @if(count($upcomingAssignments) > 0)
                            <div class="space-y-4">
                                @foreach($upcomingAssignments as $assignment)
                                    <div class="flex items-center justify-between p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-gray-900 dark:text-white truncate">{{ $assignment['title'] ?? 'Untitled Assignment' }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $assignment['academic_subject']['name'] ?? 'Unknown Subject' }}
                                            </p>
                                            <p class="text-xs text-orange-600 dark:text-orange-400 font-medium">
                                                Due: {{ \Carbon\Carbon::parse($assignment['ends_at'])->diffForHumans() }}
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0 ml-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200">
                                                Due Soon
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="mt-2 text-gray-500 dark:text-gray-400">No upcoming assignments</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Students Tab -->
        @if($activeTab === 'students')
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">My Students</h3>
                </div>
                <div class="p-6">
                    @if(count($myStudents) > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($myStudents as $student)
                                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 hover:shadow-md transition-shadow">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-gray-900 dark:text-white truncate">
                                                {{ $student['user']['name'] ?? 'Unknown Student' }}
                                            </p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 truncate">
                                                {{ $student['academic_level']['name'] ?? 'No Level' }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-500 truncate">
                                                {{ $student['academic_level']['academic_group']['name'] ?? 'No Group' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <p class="mt-2 text-gray-500 dark:text-gray-400">No students assigned yet</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Assignments Tab -->
        @if($activeTab === 'assignments')
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">All Assignments</h3>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create Assignment
                    </button>
                </div>
                <div class="p-6">
                    @if(count($recentAssignments) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Assignment</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Subject</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($recentAssignments as $assignment)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $assignment['title'] ?? 'Untitled Assignment' }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ Str::limit($assignment['description'] ?? '', 50) }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $assignment['academic_subject']['name'] ?? 'Unknown Subject' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ \Carbon\Carbon::parse($assignment['created_at'])->format('M j, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                {{ $assignment['status'] ?? 'Active' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('teachers.assignments.show', ['assignment' => $assignment['id']]) }}"
                                                   class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded text-indigo-700 dark:text-indigo-400 bg-indigo-100 dark:bg-indigo-900 hover:bg-indigo-200 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    View
                                                </a>
                                                <button class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-200 mr-3">Edit</button>
                                                <button class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-200">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p class="mt-2 text-gray-500 dark:text-gray-400">No assignments created yet</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Subjects Tab -->
        @if($activeTab === 'subjects')
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">My Subjects</h3>
                </div>
                <div class="p-6">
                    @if(count($mySubjects) > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($mySubjects as $subject)
                                <div class="p-6 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 hover:shadow-md transition-shadow">
                                    <div class="flex items-center mb-4">
                                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <h4 class="text-lg font-medium text-gray-900 dark:text-white">{{ $subject['name'] }}</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $subject['code'] ?? 'No Code' }}</p>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            <span class="font-medium">Level:</span>
                                            {{ $subject['academic_level']['name'] ?? 'Not assigned' }}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            <span class="font-medium">Group:</span>
                                            {{ $subject['academic_level']['academic_group']['name'] ?? 'Not assigned' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <p class="mt-2 text-gray-500 dark:text-gray-400">No subjects assigned yet</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Academic Levels Tab -->
        @if($activeTab === 'levels')
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Academic Levels</h3>
                </div>
                <div class="p-6">
                    @if(count($myAcademicLevels) > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($myAcademicLevels as $level)
                                <div class="p-6 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 hover:shadow-md transition-shadow">
                                    <div class="flex items-center mb-4">
                                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <h4 class="text-lg font-medium text-gray-900 dark:text-white">{{ $level['name'] }}</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $level['academic_group']['name'] ?? 'No Group' }}</p>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            <span class="font-medium">Students:</span>
                                            {{ $level['accessible_students_count'] ?? 0 }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <p class="mt-2 text-gray-500 dark:text-gray-400">No academic levels assigned yet</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <script>
        function studentDistributionChart() {
            return {
                initChart() {
                    const canvas = this.$refs.distributionCanvas;
                    if (!canvas) return;
                    const ctx = canvas.getContext('2d');
                    const labels = @json($this->studentDistributionChartData()['labels']);
                    const data = @json($this->studentDistributionChartData()['data']);
                    const colors = @json($this->studentDistributionChartData()['colors']);

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
                    const radius = Math.min(width, height) / 2 - 20;
                    const innerRadius = radius * 0.6;

                    const total = data.reduce((sum, val) => sum + val, 0);
                    if (total === 0) return;

                    let startAngle = -Math.PI / 2;

                    data.forEach((value, index) => {
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
                    ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#1F2937';
                    ctx.font = 'bold 24px sans-serif';
                    ctx.textAlign = 'center';
                    ctx.fillText(this.formatNumber(total), centerX, centerY);
                    ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280';
                    ctx.font = '12px sans-serif';
                    ctx.fillText('Total Students', centerX, centerY + 18);
                },
                formatNumber(num) {
                    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
                    if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
                    return num.toString();
                }
            };
        }

        function assignmentTrendChart() {
            return {
                initChart() {
                    const canvas = this.$refs.trendCanvas;
                    if (!canvas) return;
                    const ctx = canvas.getContext('2d');
                    const labels = @json($this->assignmentTrendChartData()['labels']);
                    const created = @json($this->assignmentTrendChartData()['created']);
                    const completed = @json($this->assignmentTrendChartData()['completed']);

                    this.drawBarChart(ctx, canvas, labels, created, completed);
                },
                drawBarChart(ctx, canvas, labels, created, completed) {
                    const rect = canvas.getBoundingClientRect();
                    canvas.width = rect.width * window.devicePixelRatio;
                    canvas.height = rect.height * window.devicePixelRatio;
                    ctx.scale(window.devicePixelRatio, window.devicePixelRatio);

                    const width = rect.width;
                    const height = rect.height;
                    const padding = { top: 20, right: 20, bottom: 40, left: 40 };
                    const chartWidth = width - padding.left - padding.right;
                    const chartHeight = height - padding.top - padding.bottom;

                    const maxValue = Math.max(...created, ...completed, 1);
                    const barGroupWidth = chartWidth / labels.length;
                    const barWidth = barGroupWidth * 0.35;
                    const barGap = barGroupWidth * 0.1;

                    // Clear canvas
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

                        // Y-axis labels
                        ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280';
                        ctx.font = '10px sans-serif';
                        ctx.textAlign = 'right';
                        const value = Math.round(maxValue - (maxValue / 5) * i);
                        ctx.fillText(value.toString(), padding.left - 5, y + 4);
                    }

                    // Draw bars
                    labels.forEach((label, index) => {
                        const groupX = padding.left + index * barGroupWidth + barGap;

                        // Created bar (blue)
                        const createdHeight = (created[index] / maxValue) * chartHeight;
                        const createdY = padding.top + chartHeight - createdHeight;
                        ctx.fillStyle = '#3B82F6';
                        ctx.beginPath();
                        ctx.roundRect(groupX, createdY, barWidth, createdHeight, 3);
                        ctx.fill();

                        // Completed bar (green)
                        const completedHeight = (completed[index] / maxValue) * chartHeight;
                        const completedY = padding.top + chartHeight - completedHeight;
                        ctx.fillStyle = '#10B981';
                        ctx.beginPath();
                        ctx.roundRect(groupX + barWidth + 2, completedY, barWidth, completedHeight, 3);
                        ctx.fill();

                        // X-axis labels
                        const showEvery = Math.ceil(labels.length / 7);
                        if (index % showEvery === 0) {
                            ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280';
                            ctx.font = '10px sans-serif';
                            ctx.textAlign = 'center';
                            ctx.fillText(label, groupX + barWidth, height - padding.bottom + 15);
                        }
                    });
                }
            };
        }
    </script>

    <style>
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
    </style>
</div>


