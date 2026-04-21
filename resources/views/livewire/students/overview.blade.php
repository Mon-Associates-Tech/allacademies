<div class="p-6 space-y-6">
    @if(!$student)
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
            <p class="text-yellow-800 dark:text-yellow-200">Student profile not found. Please contact your administrator.</p>
        </div>
    @else
        <!-- Header with Gradient -->
        <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-white/20 backdrop-blur-sm rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">My Dashboard</h1>
                        <p class="text-indigo-100 text-sm">Track your academic progress</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-center bg-white/10 backdrop-blur-sm rounded-lg px-4 py-2">
                        <p class="text-2xl font-bold">{{ $averageAssignmentScore }}%</p>
                        <p class="text-xs text-indigo-100">Avg Score</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Range Selector -->
        <div class="flex justify-end">
            <h2 class="sr-only">Student Dashboard</h2>
            <div class="inline-flex rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 p-1 text-sm" role="tablist" aria-label="Select time range">
                <button wire:click="$set('range','all')" type="button" class="px-3 py-1.5 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ $range==='all' ? 'bg-indigo-600 text-white' : 'text-gray-700 dark:text-gray-300' }}">All</button>
                <button wire:click="$set('range','7d')" type="button" class="px-3 py-1.5 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ $range==='7d' ? 'bg-indigo-600 text-white' : 'text-gray-700 dark:text-gray-300' }}">7d</button>
                <button wire:click="$set('range','30d')" type="button" class="px-3 py-1.5 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ $range==='30d' ? 'bg-indigo-600 text-white' : 'text-gray-700 dark:text-gray-300' }}">30d</button>
                <button wire:click="$set('range','90d')" type="button" class="px-3 py-1.5 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ $range==='90d' ? 'bg-indigo-600 text-white' : 'text-gray-700 dark:text-gray-300' }}">90d</button>
                <button wire:click="$set('range','term')" type="button" class="px-3 py-1.5 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ $range==='term' ? 'bg-indigo-600 text-white' : 'text-gray-700 dark:text-gray-300' }}">Term</button>
            </div>
        </div>

        @if(!empty($studentSnapshot))
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Assignments Snapshot</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $studentSnapshot['assignments']['completion_rate'] ?? 0 }}%</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ $studentSnapshot['assignments']['completed'] ?? 0 }} completed, {{ $studentSnapshot['assignments']['upcoming'] ?? 0 }} due soon
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Self Assessments</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $studentSnapshot['quizzes']['average_score'] ?? 0 }}%</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ $studentSnapshot['quizzes']['total'] ?? 0 }} attempts, best {{ $studentSnapshot['quizzes']['best_score'] ?? 0 }}%
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Reading Progress</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $studentSnapshot['reading']['books_in_progress'] ?? 0 }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ $studentSnapshot['reading']['books_completed'] ?? 0 }} completed books, {{ $studentSnapshot['reading']['total_pages_read'] ?? 0 }} pages read
                    </p>
                </div>
            </div>
        @endif

        <!-- Stats Cards Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Total Assignments -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $totalAssignments }}</p>
                    </div>
                </div>
            </div>

            <!-- Completed -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Completed</p>
                        <p class="text-xl font-bold text-green-600 dark:text-green-400">{{ $completedAssignments }}</p>
                    </div>
                </div>
            </div>

            <!-- Ongoing -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900/50 rounded-lg">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Ongoing</p>
                        <p class="text-xl font-bold text-yellow-600 dark:text-yellow-400">{{ $ongoingAssignments }}</p>
                    </div>
                </div>
            </div>

            <!-- Overdue -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-red-100 dark:bg-red-900/50 rounded-lg">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Overdue</p>
                        <p class="text-xl font-bold text-red-600 dark:text-red-400">{{ $overdueAssignments }}</p>
                    </div>
                </div>
            </div>

            <!-- Completion Rate Gauge -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow col-span-2 lg:col-span-1">
                <div class="flex items-center justify-center h-full">
                    <livewire:charts.gauge-chart :value="$gaugeValue" :min="$gaugeMin" :max="$gaugeMax" :thresholds="$gaugeThresholds" center-label="Completion" height-class="h-28" />
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Submission Pipeline</p>
                <div class="mt-2 flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Submitted</span>
                    <span class="text-lg font-semibold text-gray-900 dark:text-white">{{ $submittedAssignments }}</span>
                </div>
                <div class="mt-1 flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Graded</span>
                    <span class="text-lg font-semibold text-green-600 dark:text-green-400">{{ $gradedAssignments }}</span>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Assigned Scope</p>
                <div class="mt-2 flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Assigned</span>
                    <span class="text-lg font-semibold text-gray-900 dark:text-white">{{ $totalAssignments }}</span>
                </div>
                <div class="mt-1 flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Active Now</span>
                    <span class="text-lg font-semibold text-indigo-600 dark:text-indigo-400">{{ $studentSnapshot['assignments']['total_available_now'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <!-- Quick Actions Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h3>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ now()->format('l, F j, Y') }}</span>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <!-- View Assignments -->
                <a href="{{ route('students.assignments') }}" class="relative p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-blue-300 dark:hover:border-blue-600 hover:shadow-md transition-all duration-200 group bg-white dark:bg-gray-800 text-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg mx-auto w-fit mb-2 group-hover:bg-blue-200 dark:group-hover:bg-blue-800/50 transition-colors">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <h4 class="text-xs font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">Assignments</h4>
                    @if($upcomingAssignments > 0)
                        <div class="absolute -top-2 -right-2 bg-orange-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                            {{ $upcomingAssignments > 9 ? '9+' : $upcomingAssignments }}
                        </div>
                    @endif
                </a>

                <!-- Take Quiz -->
                <a href="{{ route('students.assessments') }}" class="relative p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-purple-300 dark:hover:border-purple-600 hover:shadow-md transition-all duration-200 group bg-white dark:bg-gray-800 text-center">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900/50 rounded-lg mx-auto w-fit mb-2 group-hover:bg-purple-200 dark:group-hover:bg-purple-800/50 transition-colors">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h4 class="text-xs font-medium text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400">Take Quiz</h4>
                </a>

                <!-- View Courses -->
                <a href="{{ route('students.courses') }}" class="relative p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-green-300 dark:hover:border-green-600 hover:shadow-md transition-all duration-200 group bg-white dark:bg-gray-800 text-center">
                    <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg mx-auto w-fit mb-2 group-hover:bg-green-200 dark:group-hover:bg-green-800/50 transition-colors">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h4 class="text-xs font-medium text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400">Courses</h4>
                </a>

                <!-- View Schedule -->
                <a href="{{ route('students.schedules') }}" class="relative p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-indigo-300 dark:hover:border-indigo-600 hover:shadow-md transition-all duration-200 group bg-white dark:bg-gray-800 text-center">
                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg mx-auto w-fit mb-2 group-hover:bg-indigo-200 dark:group-hover:bg-indigo-800/50 transition-colors">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h4 class="text-xs font-medium text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400">Schedule</h4>
                </a>

                <!-- Messages -->
                <a href="{{ route('students.messages.index') }}" class="relative p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-pink-300 dark:hover:border-pink-600 hover:shadow-md transition-all duration-200 group bg-white dark:bg-gray-800 text-center">
                    <div class="p-2 bg-pink-100 dark:bg-pink-900/50 rounded-lg mx-auto w-fit mb-2 group-hover:bg-pink-200 dark:group-hover:bg-pink-800/50 transition-colors">
                        <svg class="w-5 h-5 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h4 class="text-xs font-medium text-gray-900 dark:text-white group-hover:text-pink-600 dark:group-hover:text-pink-400">Messages</h4>
                </a>

                <!-- Virtual Classroom -->
                <a href="{{ route('students.classroom.sessions') }}" class="relative p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-cyan-300 dark:hover:border-cyan-600 hover:shadow-md transition-all duration-200 group bg-white dark:bg-gray-800 text-center">
                    <div class="p-2 bg-cyan-100 dark:bg-cyan-900/50 rounded-lg mx-auto w-fit mb-2 group-hover:bg-cyan-200 dark:group-hover:bg-cyan-800/50 transition-colors">
                        <svg class="w-5 h-5 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h4 class="text-xs font-medium text-gray-900 dark:text-white group-hover:text-cyan-600 dark:group-hover:text-cyan-400">Virtual Class</h4>
                </a>
            </div>
        </div>

        <!-- Charts Section: Bar + Pie in a coherent grid -->
        <div class="grid grid-cols-12 gap-6 mb-6">
            <div class="col-span-12 lg:col-span-8 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">Performance by Subject</h3>
                    <span class="text-xs text-gray-500">Range: {{ strtoupper($range) }}</span>
                </div>
                <livewire:charts.bar-chart :labels="$barLabels" :datasets="$barDatasets" :options="$barOptions" height-class="h-72" />
            </div>
            <div class="col-span-12 lg:col-span-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">Assignments Status</h3>
                    <span class="text-xs text-gray-500">Range: {{ strtoupper($range) }}</span>
                </div>
                <livewire:charts.pie-chart :labels="$pieLabels" :values="$pieValues" :options="$pieOptions" height-class="h-72" />
            </div>
        </div>

        <!-- Activity & Upcoming Due -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Assignments -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center gap-2">
                        <div class="p-2 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg">
                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Assignments</h3>
                    </div>
                    <a href="{{ route('students.assignments') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">View All →</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentAssignments as $assignment)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-900/70 transition-colors">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 dark:text-white truncate">{{ $assignment['title'] }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $assignment['subject'] }}</p>
                            </div>
                            <div class="text-right ml-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $assignment['percentage'] >= 80 ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' :
                                       ($assignment['percentage'] >= 60 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300' : 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300') }}">
                                    {{ $assignment['percentage'] }}%
                                </span>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $assignment['submitted_at']->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No completed assignments yet</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Upcoming Due Assignments -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center gap-2">
                        <div class="p-2 bg-orange-100 dark:bg-orange-900/50 rounded-lg">
                            <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Upcoming Assignments</h3>
                    </div>
                    <a href="{{ route('students.assignments') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">View All →</a>
                </div>
                <div class="space-y-3">
                    @forelse($upcomingDueAssignments as $assignment)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-900/70 transition-colors">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 dark:text-white truncate">{{ $assignment['title'] }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $assignment['subject'] }}</p>
                            </div>
                            <div class="text-right ml-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $assignment['days_until_due'] <= 1 ? 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300' :
                                       ($assignment['days_until_due'] <= 3 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300') }}">
                                    {{ abs($assignment['days_until_due']) }} {{ abs($assignment['days_until_due']) == 1 ? 'day' : 'days' }}
                                </span>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $assignment['due_date']->format('M d, Y') }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No upcoming assignments</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Subject Performance list (optional small details under charts) -->
        @if(count($subjectPerformance) > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Subject Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($subjectPerformance as $subject)
                        <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-900/40">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $subject['subject'] }}</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $subject['average_score'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="h-2 rounded-full {{ $subject['average_score'] >= 80 ? 'bg-green-500' : ($subject['average_score'] >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $subject['average_score'] }}%"></div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $subject['assignments_count'] }} assignments</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Book Quiz Analytics Section -->
        @if($totalQuizAttempts > 0)
            <!-- Quiz Stats Cards -->
            <div class="bg-gradient-to-r from-purple-600 via-violet-600 to-indigo-600 rounded-2xl p-6 text-white shadow-lg mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-white/20 backdrop-blur-sm rounded-xl">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold">Book Quiz Performance</h2>
                            <p class="text-purple-100 text-sm">Self-assessment quizzes from books & uploads</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="text-center bg-white/10 backdrop-blur-sm rounded-lg px-4 py-2">
                            <p class="text-2xl font-bold">{{ $totalQuizAttempts }}</p>
                            <p class="text-xs text-purple-100">Quizzes</p>
                        </div>
                        <div class="text-center bg-white/10 backdrop-blur-sm rounded-lg px-4 py-2">
                            <p class="text-2xl font-bold">{{ $averageSelfAssessmentScore }}%</p>
                            <p class="text-xs text-purple-100">Avg Score</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quiz Charts Section -->
            <div class="grid grid-cols-12 gap-6 mb-6">
                <div class="col-span-12 lg:col-span-8 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">Quiz Scores by Source</h3>
                        <span class="text-xs text-gray-500">Range: {{ strtoupper($range) }}</span>
                    </div>
                    <livewire:charts.bar-chart :labels="$quizBarLabels" :datasets="$quizBarDatasets" :options="$quizBarOptions" height-class="h-72" />
                </div>
                <div class="col-span-12 lg:col-span-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">By Difficulty</h3>
                        <span class="text-xs text-gray-500">Range: {{ strtoupper($range) }}</span>
                    </div>
                    <livewire:charts.pie-chart :labels="$quizPieLabels" :values="$quizPieValues" :options="$quizPieOptions" height-class="h-72" />
                </div>
            </div>

            <!-- Quiz Stats by Type and Difficulty -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- By Difficulty -->
                @if(count($quizzesByDifficulty) > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Performance by Difficulty</h3>
                        <div class="space-y-3">
                            @foreach($quizzesByDifficulty as $diff)
                                <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-900/40">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $diff['difficulty'] }}</span>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $diff['count'] }} quizzes</span>
                                            <span class="text-sm font-semibold {{ $diff['average_score'] >= 80 ? 'text-green-600 dark:text-green-400' : ($diff['average_score'] >= 60 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">{{ $diff['average_score'] }}%</span>
                                        </div>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="h-2 rounded-full {{ $diff['average_score'] >= 80 ? 'bg-green-500' : ($diff['average_score'] >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $diff['average_score'] }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- By Question Type -->
                @if(count($quizzesByType) > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Performance by Question Type</h3>
                        <div class="space-y-3">
                            @foreach($quizzesByType as $type)
                                <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-900/40">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $type['type'] }}</span>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $type['count'] }} quizzes</span>
                                            <span class="text-sm font-semibold {{ $type['average_score'] >= 80 ? 'text-green-600 dark:text-green-400' : ($type['average_score'] >= 60 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">{{ $type['average_score'] }}%</span>
                                        </div>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="h-2 rounded-full {{ $type['average_score'] >= 80 ? 'bg-purple-500' : ($type['average_score'] >= 60 ? 'bg-violet-500' : 'bg-indigo-500') }}" style="width: {{ $type['average_score'] }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Recent Self-Assessments -->
        @if(count($recentSelfAssessments) > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center gap-2">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900/50 rounded-lg">
                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Book Quizzes</h3>
                    </div>
                    <a href="{{ route('students.assessments') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">View All →</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($recentSelfAssessments as $assessment)
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-900/70 transition-colors">
                            <div class="flex items-start justify-between mb-2">
                                <p class="font-medium text-gray-900 dark:text-white truncate flex-1">{{ $assessment['book_title'] }}</p>
                                @if(isset($assessment['type']) && $assessment['type'] === 'uploaded')
                                    <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-violet-100 text-violet-800 dark:bg-violet-900/50 dark:text-violet-300">
                                        Upload
                                    </span>
                                @endif
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Score:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $assessment['score'] >= 80 ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' :
                                       ($assessment['score'] >= 60 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300' : 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300') }}">
                                    {{ $assessment['score'] }}%
                                </span>
                            </div>
                            @if(isset($assessment['difficulty']))
                                <div class="flex justify-between items-center mt-1">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Difficulty:</span>
                                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ ucfirst($assessment['difficulty']) }}</span>
                                </div>
                            @endif
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $assessment['completed_at']->diffForHumans() }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</div>
