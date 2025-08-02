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
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
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
</div>
