<div class="space-y-6">
    <!-- Account Alerts Section -->
    @if(count($accountAlerts) > 0)
        <div class="space-y-3">
            @foreach($accountAlerts as $alert)
                <div class="border-l-4 rounded-lg p-4 shadow-sm
                @if($alert['type'] === 'error') bg-red-50 border-red-400 dark:bg-red-900/20 dark:border-red-500
                @elseif($alert['type'] === 'warning') bg-amber-50 border-amber-400 dark:bg-amber-900/20 dark:border-amber-500
                @else bg-blue-50 border-blue-400 dark:bg-blue-900/20 dark:border-blue-500 @endif">

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">{{ $alert['icon'] }}</span>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-lg font-semibold
                            @if($alert['type'] === 'error') text-red-800 dark:text-red-200
                            @elseif($alert['type'] === 'warning') text-amber-800 dark:text-amber-200
                            @else text-blue-800 dark:text-blue-200 @endif">
                                {{ $alert['title'] }}
                            </h3>
                            <p class="text-sm mt-1
                            @if($alert['type'] === 'error') text-red-700 dark:text-red-300
                            @elseif($alert['type'] === 'warning') text-amber-700 dark:text-amber-300
                            @else text-blue-700 dark:text-blue-300 @endif">
                                {{ $alert['message'] }}
                            </p>
                        </div>
                        @if(isset($alert['action']))
                            <div class="ml-4">
                                <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white focus:outline-none focus:ring-2 focus:ring-offset-2
                                @if($alert['type'] === 'error') bg-red-600 hover:bg-red-700 focus:ring-red-500
                                @elseif($alert['type'] === 'warning') bg-amber-600 hover:bg-amber-700 focus:ring-amber-500
                                @else bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 @endif">
                                    {{ $alert['action'] }}
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Account Completeness Section -->
    @if($accountCompleteness['percentage'] < 100)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Account Setup Progress
                    </h3>
                    <div class="flex items-center space-x-2">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                        {{ $accountCompleteness['completed_items'] }} of {{ $accountCompleteness['total_items'] }} completed
                    </span>
                        <div class="flex items-center">
                            <div class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{{ $accountCompleteness['percentage'] }}%</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-3 mb-6">
                    <div class="bg-indigo-600 h-3 rounded-full transition-all duration-300"
                         style="width: {{ $accountCompleteness['percentage'] }}%"></div>
                </div>

                <!-- Checklist -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($accountCompleteness['checklist'] as $key => $item)
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                @if($item['completed'])
                                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1">
                            <span class="text-sm font-medium {{ $item['completed'] ? 'text-gray-900 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400' }}">
                                {{ $item['label'] }}
                            </span>
                            </div>
                            <div class="text-xs text-gray-400">{{ $item['weight'] }}%</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Academic Status Overview -->
    @if($academicStatus)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                    </svg>
                    Academic Information
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Academic Group -->
                    <div class="space-y-2">
                        <h4 class="font-medium text-gray-900 dark:text-gray-100 flex items-center">
                            <span class="text-lg mr-2">👥</span>
                            Academic Group
                        </h4>
                        @if($academicStatus['academic_group'])
                            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3">
                                <p class="font-semibold text-green-800 dark:text-green-200">
                                    {{ $academicStatus['academic_group']['name'] }}
                                </p>
                                <p class="text-sm text-green-700 dark:text-green-300">
                                    {{ $academicStatus['academic_group']['teachers_count'] }} teacher(s) assigned
                                </p>
                            </div>
                        @else
                            <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-3">
                                <p class="text-red-800 dark:text-red-200 font-medium">Not Assigned</p>
                                <p class="text-sm text-red-700 dark:text-red-300">Contact your administrator</p>
                            </div>
                        @endif
                    </div>

                    <!-- Academic Level -->
                    <div class="space-y-2">
                        <h4 class="font-medium text-gray-900 dark:text-gray-100 flex items-center">
                            <span class="text-lg mr-2">📚</span>
                            Academic Level
                        </h4>
                        @if($academicStatus['academic_level'])
                            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3">
                                <p class="font-semibold text-green-800 dark:text-green-200">
                                    {{ $academicStatus['academic_level']['name'] }}
                                </p>
                                <p class="text-sm text-green-700 dark:text-green-300">
                                    {{ $academicStatus['academic_level']['subjects_count'] }} subject(s) available
                                </p>
                            </div>
                        @else
                            <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-3">
                                <p class="text-red-800 dark:text-red-200 font-medium">Not Assigned</p>
                                <p class="text-sm text-red-700 dark:text-red-300">Contact your administrator</p>
                            </div>
                        @endif
                    </div>

                    <!-- School -->
                    <div class="space-y-2">
                        <h4 class="font-medium text-gray-900 dark:text-gray-100 flex items-center">
                            <span class="text-lg mr-2">🏫</span>
                            School
                        </h4>
                        @if($academicStatus['school'])
                            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3">
                                <p class="font-semibold text-green-800 dark:text-green-200">
                                    {{ $academicStatus['school']['name'] }}
                                </p>
                            </div>
                        @else
                            <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-3">
                                <p class="text-red-800 dark:text-red-200 font-medium">Not Assigned</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Subjects Summary -->
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                        <span class="text-lg mr-2">📖</span>
                        Subjects Summary
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                {{ $academicStatus['subjects_summary']['total_accessible'] }}
                            </div>
                            <div class="text-sm text-blue-700 dark:text-blue-300">Total Accessible</div>
                        </div>
                        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                {{ $academicStatus['subjects_summary']['from_level'] }}
                            </div>
                            <div class="text-sm text-green-700 dark:text-green-300">From Level</div>
                        </div>
                        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-3">
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                {{ $academicStatus['subjects_summary']['individually_assigned'] }}
                            </div>
                            <div class="text-sm text-purple-700 dark:text-purple-300">Individual</div>
                        </div>
                        <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-3">
                            <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                                {{ $academicStatus['subjects_summary']['removed_from_level'] }}
                            </div>
                            <div class="text-sm text-orange-700 dark:text-orange-300">Removed</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Enhanced Welcome Section -->
    <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-700 rounded-xl p-4 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <h2 class="text-3xl font-bold mb-2">{{ $greeting }}, {{ auth()->user()->name }}! 👋</h2>
                <p class="text-blue-100 text-sm tracking-tight mb-4">Ready to continue your learning journey? Let's make today count!</p>

                <!-- Achievements Section -->
                @if(count($achievements) > 0)
                    <div class="flex flex-wrap gap-2 mt-4">
                        @foreach($achievements as $achievement)
                            <div class="bg-white/20 backdrop-blur-sm rounded-full px-3 py-1 text-sm">
                                {{ $achievement['message'] }}
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Study Streak -->
            <div class="hidden lg:block text-center bg-white/10 backdrop-blur-sm rounded-xl p-6">
                <div class="text-4xl font-bold text-yellow-300">{{ $quickStats['study_streak'] }}</div>
                <div class="text-sm text-blue-200">Day Study Streak 🔥</div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Dashboard -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Overall Score with Trend -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow cursor-pointer"
             onclick="document.querySelector('[x-data]').__x.$data.activeTab = 'performance'">
            <div class="flex items-center justify-between">
                <div class="flex items-center my-auto">
                    <div class="flex-shrink-0 bg-indigo-100 dark:bg-indigo-900 rounded-full p-3">
                        <svg class="w-6 h-6 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold">Overall Score</h3>
                        <div class="flex items-center space-x-2">
                            <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $overallScore }}%</p>
                            @if($performanceTrend['trend'] !== 'stable')
                                <div class="flex items-center {{ $performanceTrend['trend'] === 'up' ? 'text-green-500' : 'text-red-500' }}">
                                    <svg class="w-4 h-4 {{ $performanceTrend['trend'] === 'down' ? 'rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-sm font-medium">{{ abs($performanceTrend['difference']) }}%</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Books -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow cursor-pointer"
             onclick="document.querySelector('[x-data]').__x.$data.activeTab = 'books'">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold">My Books</h3>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $bookCount }}</p>
                    <p class="text-xs text-gray-500">Available resources</p>
                </div>
            </div>
        </div>

        <!-- Today's Activity -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-nowrap">Today's Activity</h3>
                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $quickStats['today_assessments'] }}</p>
                    <p class="text-xs text-gray-500">Assessments completed</p>
                </div>
            </div>
        </div>

        <!-- Upcoming Activities -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow cursor-pointer"
             onclick="document.querySelector('[x-data]').__x.$data.activeTab = 'schedule'">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 dark:bg-purple-900 rounded-full p-3">
                    <svg class="w-6 h-6 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5.5 2A3.5 3.5 0 002 5.5v2.879a2.5 2.5 0 00.732 1.767l6.5 6.5a2.5 2.5 0 003.536 0l2.878-2.878a2.5 2.5 0 000-3.536l-6.5-6.5A2.5 2.5 0 007.38 2.732 3.5 3.5 0 005.5 2zM6 5.5a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold">Upcoming</h3>
                    <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $upcomingActivitiesCount }}</p>
                    <p class="text-xs text-gray-500">Activities pending</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Subject Progress Section -->
    @if(count($subjectProgress) > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                    </svg>
                    Subject Progress
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($subjectProgress as $subject)
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ $subject['name'] }}</h4>
                                <span class="text-2xl font-bold text-{{ $subject['color'] }}-600">{{ $subject['average_score'] }}%</span>
                            </div>

                            <!-- Progress Bar -->
                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2 mb-3">
                                <div class="bg-{{ $subject['color'] }}-600 h-2 rounded-full transition-all duration-300"
                                     style="width: {{ $subject['progress_percentage'] }}%"></div>
                            </div>

                            <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400">
                                <span>{{ $subject['total_assessments'] }} assessments</span>
                                <span>{{ $subject['recent_activity'] }} this week</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Assessments -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold">Recent Assessments</h3>
                    <button onclick="document.querySelector('[x-data]').__x.$data.activeTab = 'schedule'"
                            class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        View All →
                    </button>
                </div>
            </div>
            <div class="p-6">
                @if(count($recentAssessments) > 0)
                    <div class="space-y-4">
                        @foreach($recentAssessments as $assessment)
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $assessment->title ?? "Assessment #{$assessment->id}" }}
                                    </h4>
                                    <div class="flex items-center space-x-4 mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        @if($assessment->subject)
                                            <span>📚 {{ $assessment->subject->name }}</span>
                                        @endif
                                        @if($assessment->topic)
                                            <span>📖 {{ $assessment->topic->name }}</span>
                                        @endif
                                        <span>📅 {{ $assessment->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($assessment->score !== null)
                                        <div class="text-lg font-semibold text-indigo-600 dark:text-indigo-400">
                                            {{ $assessment->score }}%
                                        </div>
                                    @endif
                                    <span class="inline-block px-2 py-1 text-xs rounded-full
                                        @if($assessment->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($assessment->status === 'in_progress') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $assessment->status)) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No assessments yet</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by taking your first assessment!</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Upcoming Activities -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold">Upcoming Activities</h3>
            </div>
            <div class="p-6">
                @if(count($upcomingActivities) > 0)
                    <div class="space-y-4">
                        @foreach($upcomingActivities as $activity)
                            <div class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex-shrink-0 mt-1">
                                    @php
                                        $activityIcons = [
                                            'exam' => '📝',
                                            'quiz' => '❓',
                                            'assessment' => '📊',
                                            'group_meeting' => '👥',
                                            'book_reading' => '📚'
                                        ];
                                    @endphp
                                    <span class="text-lg">{{ $activityIcons[$activity->activity_type] ?? '📌' }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ $activity->title }}</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        @if($activity->subject)
                                            {{ $activity->subject->name }}
                                        @endif
                                        @if($activity->due_date)
                                            • Due {{ $activity->due_date->diffForHumans() }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No upcoming activities</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Your schedule is clear for now!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions Panel -->
    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-gray-800 dark:to-gray-700 rounded-xl p-6 border border-indigo-200 dark:border-gray-600">
        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <button onclick="document.querySelector('[x-data]').__x.$data.activeTab = 'self-assessment'"
                    class="flex items-center space-x-3 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow border border-gray-200 dark:border-gray-600">
                <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 rounded-full p-2">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="text-left">
                    <div class="font-medium text-gray-900 dark:text-gray-100">Start Assessment</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Begin a new practice session</div>
                </div>
            </button>

            <button onclick="document.querySelector('[x-data]').__x.$data.activeTab = 'books'"
                    class="flex items-center space-x-3 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow border border-gray-200 dark:border-gray-600">
                <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-full p-2">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                    </svg>
                </div>
                <div class="text-left">
                    <div class="font-medium text-gray-900 dark:text-gray-100">Browse Books</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Explore learning materials</div>
                </div>
            </button>

            <button onclick="document.querySelector('[x-data]').__x.$data.activeTab = 'performance'"
                    class="flex items-center space-x-3 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow border border-gray-200 dark:border-gray-600">
                <div class="flex-shrink-0 bg-purple-100 dark:bg-purple-900 rounded-full p-2">
                    <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
                <div class="text-left">
                    <div class="font-medium text-gray-900 dark:text-gray-100">View Performance</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Track your progress</div>
                </div>
            </button>
        </div>
    </div>
</div>
