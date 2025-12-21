<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Schedule & Activities</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Track all your academic activities, assessments, and progress
                    </p>
                </div>

                <!-- View Mode Toggle -->
                <div class="flex space-x-2">
                    <button wire:click="setViewMode('calendar')"
                            class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ $viewMode === 'calendar' ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                        <!-- fas fa-calendar -->
                        <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/>
                        </svg>
                        Calendar
                    </button>
                    <button wire:click="setViewMode('list')"
                            class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ $viewMode === 'list' ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                        <!-- fas fa-list -->
                        <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/>
                        </svg>
                        List
                    </button>
                    <button wire:click="setViewMode('week')"
                            class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ $viewMode === 'week' ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                        <!-- fas fa-calendar-week -->
                        <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/>
                            <path d="M7 12h2v2H7v-2zm4 0h2v2h-2v-2zm4 0h2v2h-2v-2z"/>
                        </svg>
                        Week
                    </button>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="px-6 py-4">
            <div class="flex flex-wrap items-center gap-3">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Filter by:</label>

                <button wire:click="setFilterType('all')"
                        class="px-3 py-1.5 text-xs font-medium rounded-full transition-all duration-200 {{ $filterType === 'all' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                    All Activities
                </button>

                <button wire:click="setFilterType('assessments')"
                        class="px-3 py-1.5 text-xs font-medium rounded-full transition-all duration-200 {{ $filterType === 'assessments' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                    <!-- fas fa-clipboard-check -->
                    <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                    </svg>
                    Assessments
                </button>

                <button wire:click="setFilterType('assignments')"
                        class="px-3 py-1.5 text-xs font-medium rounded-full transition-all duration-200 {{ $filterType === 'assignments' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                    <!-- fas fa-tasks -->
                    <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14 10H2v2h12v-2zm0-4H2v2h12V6zm4 8v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM2 16h8v-2H2v2z"/>
                    </svg>
                    Assignments
                </button>

                <button wire:click="setFilterType('reading')"
                        class="px-3 py-1.5 text-xs font-medium rounded-full transition-all duration-200 {{ $filterType === 'reading' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                    <!-- fas fa-book-open -->
                    <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/>
                    </svg>
                    Reading
                </button>

                <button wire:click="setFilterType('lessons')"
                        class="px-3 py-1.5 text-xs font-medium rounded-full transition-all duration-200 {{ $filterType === 'lessons' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                    <!-- fas fa-chalkboard-teacher -->
                    <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M21 11c0-1.1-.9-2-2-2h-1V5c0-1.1-.9-2-2-2H8c-1.1 0-2 .9-2 2v4H5c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-8zm-8 3h-2v2c0 .55-.45 1-1 1s-1-.45-1-1v-2H7c-.55 0-1-.45-1-1s.45-1 1-1h2V9c0-.55.45-1 1-1s1 .45 1 1v2h2c.55 0 1 .45 1 1s-.45 1-1 1z"/>
                    </svg>
                    Lessons
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    @php
        $weeklyStats = $this->getWeeklyStats();
        $monthlyStats = $this->getMonthlyStats();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
                    <!-- fas fa-chart-line -->
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3.5 18.49l6-6.01 4 4L22 6.92l-1.41-1.41-7.09 7.97-4-4L2 16.99z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">This Week</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $weeklyStats['assessments_completed'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-500">Assessments completed</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-xl">
                    <!-- fas fa-percentage -->
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.25 19H5.75c-.97 0-1.75-.78-1.75-1.75V6.75c0-.97.78-1.75 1.75-1.75h12.5c.97 0 1.75.78 1.75 1.75v10.5c0 .97-.78 1.75-1.75 1.75zM5.75 7v10.25h12.5V7H5.75z"/>
                        <path d="M12 9.5c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm0 5c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Average Score</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($weeklyStats['average_score'] ?? 0, 1) }}%</p>
                    <p class="text-xs text-gray-500 dark:text-gray-500">This week</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-xl">
                    <!-- fas fa-book -->
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Reading Progress</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($weeklyStats['books_progress'] ?? 0, 1) }}%</p>
                    <p class="text-xs text-gray-500 dark:text-gray-500">Average this week</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-xl">
                    <!-- fas fa-calendar-check -->
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zm-4.5-7c0 .55-.45 1-1 1h-5c-.55 0-1-.45-1-1s.45-1 1-1h5c.55 0 1 .45 1 1z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">This Month</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $monthlyStats['total_activities'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-500">Total activities</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        @if($viewMode === 'calendar')
            <!-- Calendar View -->
            <div class="p-6">
                <!-- Calendar Header with Navigation -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $monthName }}</h2>
                        <button wire:click="goToToday"
                                class="px-3 py-1.5 text-sm font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors duration-200">
                            Today
                        </button>
                    </div>
                    <div class="flex space-x-2">
                        <button wire:click="changeMonth('prev')"
                                class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button wire:click="changeMonth('next')"
                                class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <!-- Calendar Grid -->
                <div class="grid grid-cols-7 gap-px bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden">
                    <!-- Week Day Headers -->
                    @foreach($weekDays as $day)
                        <div class="bg-gray-50 dark:bg-gray-800 p-3 text-center">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $day }}</span>
                        </div>
                    @endforeach

                    <!-- Calendar Days -->
                    @foreach($calendarData as $week)
                        @foreach($week as $day)
                            <div class="bg-white dark:bg-gray-900 min-h-[120px] p-2 {{ !$day['isCurrentMonth'] ? 'opacity-50' : '' }}">
                                <div class="flex justify-between items-start mb-2">
                                    <button wire:click="selectDate('{{ $day['date']->format('Y-m-d') }}')"
                                            class="text-sm font-medium {{ $day['isToday'] ? 'bg-blue-600 text-white' : ($day['isCurrentMonth'] ? 'text-gray-900 dark:text-white hover:bg-blue-100 dark:hover:bg-blue-900' : 'text-gray-400') }} w-7 h-7 rounded-full flex items-center justify-center transition-colors duration-200">
                                        {{ $day['date']->day }}
                                    </button>
                                    @if($day['activityCount'] > 0)
                                        <span class="bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 text-xs px-2 py-1 rounded-full">
                                            {{ $day['activityCount'] }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Activity indicators -->
                                <div class="space-y-1">
                                    @foreach($day['activities']->take(3) as $activity)
                                        <div class="text-xs p-1 rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 truncate cursor-pointer hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors duration-200"
                                             wire:click="showActivityDetails({{ $activity->id }}, 'assessment')">
                                            {{ Str::limit($activity->title, 20) }}
                                        </div>
                                    @endforeach
                                    @if($day['activityCount'] > 3)
                                        <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                            +{{ $day['activityCount'] - 3 }} more
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>

        @elseif($viewMode === 'list')
            <!-- List View -->
            <div class="p-6">
                <!-- List Header with Navigation -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Activities for {{ $selectedDate->format('F j, Y') }}
                        </h2>
                        <button wire:click="goToToday"
                                class="px-3 py-1.5 text-sm font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors duration-200">
                            Today
                        </button>
                    </div>
                    <div class="flex space-x-2">
                        <button wire:click="changeDay('prev')"
                                class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button wire:click="changeDay('next')"
                                class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>

                @if($activities->isEmpty())
                    <div class="text-center py-12">
                        <div class="w-24 h-24 mx-auto mb-4">
                            <!-- fas fa-calendar-day -->
                            <svg class="w-24 h-24 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No activities found</h3>
                        <p class="text-gray-500 dark:text-gray-400">There are no activities for the selected date and filter.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($activities as $activity)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 hover:shadow-md transition-shadow duration-200 cursor-pointer"
                                 wire:click="showActivityDetails({{ $activity['id'] }}, '{{ $activity['type'] }}')">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-4">
                                        <div class="p-2 rounded-lg bg-{{ $activity['color'] }}-100 text-{{ $activity['color'] }}-600 dark:bg-{{ $activity['color'] }}-900/30 dark:text-{{ $activity['color'] }}-400">
                                            @if($activity['icon'] === 'fas fa-clipboard-check')
                                                <!-- fas fa-clipboard-check -->
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                                </svg>
                                            @elseif($activity['icon'] === 'fas fa-tasks')
                                                <!-- fas fa-tasks -->
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M14 10H2v2h12v-2zm0-4H2v2h12V6zm4 8v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM2 16h8v-2H2v2z"/>
                                                </svg>
                                            @elseif($activity['icon'] === 'fas fa-book-open')
                                                <!-- fas fa-book-open -->
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/>
                                                </svg>
                                            @else
                                                <!-- Default icon -->
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $activity['title'] }}</h3>
                                            @if(isset($activity['subject']))
                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $activity['subject'] }}</p>
                                            @endif
                                            @if(isset($activity['topic']))
                                                <p class="text-xs text-gray-500 dark:text-gray-500">{{ $activity['topic'] }}</p>
                                            @endif
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                {{ $activity['date']->format('M j, Y \a\t g:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @if($activity['type'] === 'assessment' && isset($activity['percentage']))
                                            <div class="text-lg font-bold text-gray-900 dark:text-white">
                                                {{ number_format($activity['percentage'], 1) }}%
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $activity['score'] }}/{{ $activity['max_score'] }}
                                            </div>
                                        @elseif($activity['type'] === 'reading' && isset($activity['progress']))
                                            <div class="text-lg font-bold text-gray-900 dark:text-white">
                                                {{ number_format($activity['progress'], 1) }}%
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $activity['pages_read'] ?? 0 }}/{{ $activity['total_pages'] ?? 0 }} pages
                                            </div>
                                        @endif
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $activity['color'] }}-100 text-{{ $activity['color'] }}-800 dark:bg-{{ $activity['color'] }}-900 dark:text-{{ $activity['color'] }}-200 mt-2">
                                            {{ ucfirst(str_replace('_', ' ', $activity['status'])) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $activities->links() }}
                    </div>
                @endif
            </div>

        @elseif($viewMode === 'week')
            <!-- Week View -->
            <div class="p-6">
                <!-- Week Header with Navigation -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Week of {{ $selectedDate->copy()->startOfWeek()->format('M j') }} - {{ $selectedDate->copy()->endOfWeek()->format('M j, Y') }}
                        </h2>
                        <button wire:click="goToToday"
                                class="px-3 py-1.5 text-sm font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors duration-200">
                            This Week
                        </button>
                    </div>
                    <div class="flex space-x-2">
                        <button wire:click="changeWeek('prev')"
                                class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button wire:click="changeWeek('next')"
                                class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-7 gap-4">
                    @php
                        $weekStart = $selectedDate->copy()->startOfWeek();
                    @endphp
                    @for($i = 0; $i < 7; $i++)
                        @php
                            $currentDay = $weekStart->copy()->addDays($i);
                            $dayActivities = $activities->filter(function($activity) use ($currentDay) {
                                return $activity['date']->isSameDay($currentDay);
                            });
                        @endphp
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 min-h-[200px]">
                            <div class="text-center mb-3">
                                <div class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                    {{ $currentDay->format('D') }}
                                </div>
                                <div class="text-lg font-bold text-gray-900 dark:text-white {{ $currentDay->isToday() ? 'bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mx-auto' : '' }}">
                                    {{ $currentDay->day }}
                                </div>
                            </div>
                            <div class="space-y-2">
                                @foreach($dayActivities->take(5) as $activity)
                                    <div class="text-xs p-2 rounded bg-{{ $activity['color'] }}-100 text-{{ $activity['color'] }}-800 dark:bg-{{ $activity['color'] }}-900 dark:text-{{ $activity['color'] }}-200 cursor-pointer hover:bg-{{ $activity['color'] }}-200 dark:hover:bg-{{ $activity['color'] }}-800 transition-colors duration-200"
                                         wire:click="showActivityDetails({{ $activity['id'] }}, '{{ $activity['type'] }}')">
                                        <div class="font-medium truncate">{{ Str::limit($activity['title'], 15) }}</div>
                                        @if(isset($activity['percentage']))
                                            <div class="text-xs opacity-75">{{ number_format($activity['percentage'], 0) }}%</div>
                                        @endif
                                    </div>
                                @endforeach
                                @if($dayActivities->count() > 5)
                                    <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                        +{{ $dayActivities->count() - 5 }} more
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        @endif
    </div>

    <!-- Activity Detail Modal -->
    @if($showActivityModal && $selectedActivity)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeActivityModal"></div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-900 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-900 px-6 pt-6 pb-4">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="p-3 rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                    @if($selectedActivity instanceof \App\Models\Assessment)
                                        <!-- fas fa-clipboard-check -->
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                        </svg>
                                    @elseif($selectedActivity instanceof \App\Models\BookReadingProgress)
                                        <!-- fas fa-book-open -->
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/>
                                        </svg>
                                    @else
                                        <!-- fas fa-tasks -->
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M14 10H2v2h12v-2zm0-4H2v2h12V6zm4 8v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM2 16h8v-2H2v2z"/>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        @if($selectedActivity instanceof \App\Models\Assessment)
                                            {{ $selectedActivity->title }}
                                        @elseif($selectedActivity instanceof \App\Models\BookReadingProgress)
                                            Reading: {{ $selectedActivity->book->title }}
                                        @endif
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        @if($selectedActivity instanceof \App\Models\Assessment)
                                            {{ $selectedActivity->subject?->name }}
                                        @elseif($selectedActivity instanceof \App\Models\BookReadingProgress)
                                            {{ $selectedActivity->book->subject?->name }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <button wire:click="closeActivityModal"
                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <!-- fas fa-times -->
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800 px-6 py-4 space-y-4">
                        @if($selectedActivity instanceof \App\Models\Assessment)
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Score</label>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                                        {{ $selectedActivity->score ?? 'N/A' }}/{{ $selectedActivity->max_score ?? 'N/A' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Percentage</label>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                                        {{ $selectedActivity->percentage_score ? number_format($selectedActivity->percentage_score, 1) . '%' : 'N/A' }}
                                    </p>
                                </div>
                            </div>

                            @if($selectedActivity->start_time && $selectedActivity->end_time)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Duration</label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $selectedActivity->start_time->format('M j, Y g:i A') }} -
                                        {{ $selectedActivity->end_time->format('g:i A') }}
                                    </p>
                                </div>
                            @endif

                        @elseif($selectedActivity instanceof \App\Models\BookReadingProgress)
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Progress</label>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                                        {{ number_format($selectedActivity->progress_percentage ?? 0, 1) }}%
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pages</label>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                                        {{ $selectedActivity->current_page ?? 0 }}/{{ $selectedActivity->book->total_pages ?? 0 }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ ucfirst(str_replace('_', ' ', $selectedActivity->status ?? 'Unknown')) }}
                            </span>
                        </div>

                        @if($selectedActivity instanceof \App\Models\Assessment && $selectedActivity->topic)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Topic</label>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $selectedActivity->topic->name }}</p>
                                @if($selectedActivity->subtopic)
                                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $selectedActivity->subtopic->name }}</p>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800 px-6 py-3">
                        <button wire:click="closeActivityModal"
                                class="w-full px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors duration-200">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
    <style>
        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }
        .scrollbar-thumb-gray-300::-webkit-scrollbar-thumb {
            background-color: rgb(209 213 219);
            border-radius: 3px;
        }
        .scrollbar-track-transparent::-webkit-scrollbar-track {
            background-color: transparent;
        }
    </style>
@endpush