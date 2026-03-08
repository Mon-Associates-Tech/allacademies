{{-- resources/views/livewire/subscribers/analytics.blade.php --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Learning Analytics</h1>
        <select wire:model.live="timeframe" class="px-3 py-2 border rounded-md">
            <option value="week">This Week</option>
            <option value="month">This Month</option>
            <option value="quarter">This Quarter</option>
            <option value="year">This Year</option>
        </select>
    </div>

    <!-- Performance Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Assessments Taken</h3>
            <p class="text-3xl font-bold text-blue-600">{{ $performanceMetrics['assessments_taken'] }}</p>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                {{ $performanceMetrics['improvement_rate'] > 0 ? '+' : '' }}{{ $performanceMetrics['improvement_rate'] }}% from last period
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Average Score</h3>
            <p class="text-3xl font-bold text-green-600">{{ $performanceMetrics['average_score'] }}%</p>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $performanceMetrics['average_score'] }}%"></div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Study Consistency</h3>
            <p class="text-3xl font-bold text-purple-600">{{ $performanceMetrics['study_consistency'] }}%</p>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Days with study activity
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Books Subscribed</h3>
            <p class="text-3xl font-bold text-yellow-600">{{ $performanceMetrics['books_subscribed'] }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Reading Progress</h3>
            <p class="text-3xl font-bold text-indigo-600">{{ $performanceMetrics['reading_progress'] }}%</p>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ min(100, $performanceMetrics['reading_progress']) }}%"></div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Time Spent Learning</h3>
            <p class="text-3xl font-bold text-red-600">{{ $analyticsData['time_spent_learning'] }}m</p>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                This {{ $timeframe }}
            </p>
        </div>
    </div>

    <!-- Goals Progress -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Learning Goals</h2>
        <div class="space-y-6">
            @foreach($goals as $goalKey => $goal)
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="font-medium text-gray-900 dark:text-white">
                            @switch($goalKey)
                                @case('weekly_assessment_goal')
                                    Weekly Assessment Goal
                                    @break
                                @case('reading_goal')
                                    Monthly Reading Goal
                                    @break
                                @case('score_improvement_goal')
                                    Score Improvement Goal
                                    @break
                            @endswitch
                        </h3>
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $goal['current'] }} / {{ $goal['target'] }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-violet-600 h-3 rounded-full transition-all duration-300"
                             style="width: {{ $goal['progress'] }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Learning Insights -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Strongest Subjects -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Strongest Subjects</h2>
            @forelse($learningInsights['strongest_subjects'] as $subject)
                <div class="flex justify-between items-center py-2">
                    <span class="text-gray-900 dark:text-white">{{ $subject['subject'] }}</span>
                    <span class="text-green-600 font-semibold">{{ $subject['average_score'] }}%</span>
                </div>
            @empty
                <p class="text-gray-600 dark:text-gray-400">No data available yet.</p>
            @endforelse
        </div>

        <!-- Areas for Improvement -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Areas for Improvement</h2>
            @forelse($learningInsights['areas_for_improvement'] as $subject)
                <div class="flex justify-between items-center py-2">
                    <span class="text-gray-900 dark:text-white">{{ $subject['subject'] }}</span>
                    <span class="text-red-600 font-semibold">{{ $subject['average_score'] }}%</span>
                </div>
            @empty
                <p class="text-gray-600 dark:text-gray-400">Great job! No weak areas identified.</p>
            @endforelse
        </div>
    </div>

    <!-- Study Patterns -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Study Patterns</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <h3 class="font-medium text-gray-900 dark:text-white">Peak Study Time</h3>
                <p class="text-2xl font-bold text-violet-600">{{ $learningInsights['study_patterns']['peak_study_hour'] }}</p>
            </div>
            <div class="text-center">
                <h3 class="font-medium text-gray-900 dark:text-white">Most Active Day</h3>
                <p class="text-2xl font-bold text-violet-600">{{ $learningInsights['study_patterns']['most_active_day'] }}</p>
            </div>
            <div class="text-center">
                <h3 class="font-medium text-gray-900 dark:text-white">Avg Session Length</h3>
                <p class="text-2xl font-bold text-violet-600">{{ $learningInsights['study_patterns']['average_session_length'] }}m</p>
            </div>
        </div>
    </div>

    <!-- Recommended Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Recommended Actions</h2>
        <ul class="space-y-2">
            @foreach($learningInsights['recommended_actions'] as $action)
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-violet-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-gray-700 dark:text-gray-300">{{ $action }}</span>
                </li>
            @endforeach
        </ul>
    </div>

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
                    <span class="text-xs text-gray-500">Timeframe: {{ ucfirst($timeframe) }}</span>
                </div>
                @if(count($quizBarLabels) > 0)
                    <livewire:charts.bar-chart :labels="$quizBarLabels" :datasets="$quizBarDatasets" :options="$quizBarOptions" height-class="h-72" />
                @else
                    <div class="flex items-center justify-center h-72 text-gray-500 dark:text-gray-400">
                        No quiz data available for this period
                    </div>
                @endif
            </div>
            <div class="col-span-12 lg:col-span-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">By Difficulty</h3>
                    <span class="text-xs text-gray-500">Timeframe: {{ ucfirst($timeframe) }}</span>
                </div>
                @if(count($quizPieLabels) > 0)
                    <livewire:charts.pie-chart :labels="$quizPieLabels" :values="$quizPieValues" :options="$quizPieOptions" height-class="h-72" />
                @else
                    <div class="flex items-center justify-center h-72 text-gray-500 dark:text-gray-400">
                        No quiz data available
                    </div>
                @endif
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
                    <a href="{{ route('guests.quizzes') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">View All →</a>
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
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ \Carbon\Carbon::parse($assessment['completed_at'])->diffForHumans() }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @else
        <!-- No Quiz Data Message -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="text-center py-8">
                <div class="p-3 bg-purple-100 dark:bg-purple-900/50 rounded-full inline-block mb-4">
                    <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Quiz Data Yet</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">Start taking book quizzes to see your performance analytics here.</p>
                <a href="{{ route('guests.quizzes') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Take a Quiz
                </a>
            </div>
        </div>
    @endif
</div>
