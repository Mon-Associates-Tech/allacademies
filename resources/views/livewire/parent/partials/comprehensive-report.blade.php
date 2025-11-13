<!-- Comprehensive Report Overview -->
<div class="space-y-8">
    <!-- Executive Summary -->
    <div class="bg-gradient-to-r from-violet-50 to-purple-50 dark:from-violet-900/20 dark:to-purple-900/20 rounded-lg p-6">
        <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">Executive Summary</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Performance Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <h5 class="font-semibold text-gray-900 dark:text-gray-100">Academic Performance</h5>
                </div>
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($data['performance']['summary']['average_score'], 1) }}%</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ $data['performance']['summary']['total_assignments'] }} assignments completed
                </p>
            </div>

            <!-- Attendance Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h5 class="font-semibold text-gray-900 dark:text-gray-100">Attendance</h5>
                </div>
                <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ number_format($data['attendance']['summary']['attendance_rate'], 1) }}%</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ $data['attendance']['summary']['present_days'] }}/{{ $data['attendance']['summary']['total_days'] }} days present
                </p>
            </div>

            <!-- Progress Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <h5 class="font-semibold text-gray-900 dark:text-gray-100">Overall Progress</h5>
                </div>
                @php
                    $improvingCount = $data['progress']['progress_data']->where('trend', 'improving')->count();
                    $totalSubjects = $data['progress']['progress_data']->count();
                @endphp
                <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $improvingCount }}/{{ $totalSubjects }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">subjects improving</p>
            </div>
        </div>
    </div>

    <!-- Detailed Performance Section -->
    <div class="border-t border-gray-200 dark:border-gray-700 pt-8">
        <div class="flex items-center mb-6">
            <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg mr-3">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100">Academic Performance Details</h4>
        </div>
        @include('livewire.parent.partials.performance-report', ['data' => $data['performance']])
    </div>

    <!-- Detailed Attendance Section -->
    <div class="border-t border-gray-200 dark:border-gray-700 pt-8">
        <div class="flex items-center mb-6">
            <div class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg mr-3">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100">Attendance Details</h4>
        </div>
        @include('livewire.parent.partials.attendance-report', ['data' => $data['attendance']])
    </div>

    <!-- Detailed Progress Section -->
    <div class="border-t border-gray-200 dark:border-gray-700 pt-8">
        <div class="flex items-center mb-6">
            <div class="p-2 bg-purple-100 dark:bg-purple-900/20 rounded-lg mr-3">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100">Progress Tracking</h4>
        </div>
        @include('livewire.parent.partials.progress-report', ['data' => $data['progress']])
    </div>

    <!-- Recommendations -->
    <div class="border-t border-gray-200 dark:border-gray-700 pt-8">
        <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6">Recommendations & Action Items</h4>
        <div class="space-y-4">
            <!-- Performance Recommendations -->
            @if($data['performance']['summary']['average_score'] < 60)
                <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-r-lg">
                    <div class="flex">
                        <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <h5 class="font-semibold text-red-800 dark:text-red-200">Academic Support Recommended</h5>
                            <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                                Average score is below 60%. Consider arranging extra tutoring or study sessions to improve performance.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Attendance Recommendations -->
            @if($data['attendance']['summary']['attendance_rate'] < 80)
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 p-4 rounded-r-lg">
                    <div class="flex">
                        <svg class="w-6 h-6 text-yellow-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <h5 class="font-semibold text-yellow-800 dark:text-yellow-200">Attendance Improvement Needed</h5>
                            <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                                Attendance rate is at {{ number_format($data['attendance']['summary']['attendance_rate'], 1) }}%.
                                Regular attendance is crucial for academic success. Please ensure consistent school attendance.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Progress Recommendations -->
            @php
                $decliningCount = $data['progress']['progress_data']->where('trend', 'declining')->count();
            @endphp
            @if($decliningCount > 0)
                <div class="bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-500 p-4 rounded-r-lg">
                    <div class="flex">
                        <svg class="w-6 h-6 text-orange-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                        </svg>
                        <div>
                            <h5 class="font-semibold text-orange-800 dark:text-orange-200">Monitor Subject Performance</h5>
                            <p class="text-sm text-orange-700 dark:text-orange-300 mt-1">
                                Performance is declining in {{ $decliningCount }} subject(s).
                                Consider focused study sessions in these areas.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Positive Feedback -->
            @if($data['performance']['summary']['average_score'] >= 75 && $data['attendance']['summary']['attendance_rate'] >= 90)
                <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 rounded-r-lg">
                    <div class="flex">
                        <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h5 class="font-semibold text-green-800 dark:text-green-200">Excellent Progress!</h5>
                            <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                                Your ward is performing excellently with strong academic performance and excellent attendance.
                                Keep up the great work and continue to encourage these positive habits!
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
