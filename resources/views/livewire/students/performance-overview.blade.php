<div class="space-y-6" x-data="{
    showCharts: true,
    activeMetric: 'overview',
    showTooltip: false,
    tooltipContent: ''
}">
    <!-- Enhanced Header Section -->
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-white/20 backdrop-blur-sm rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold">Performance Analytics</h1>
                    <p class="text-indigo-100 mt-1">Deep insights into your academic progress and achievements</p>
                </div>
            </div>

            <!-- Quick Performance Indicator -->
            <div class="hidden lg:flex items-center space-x-6">
                <div class="text-center bg-white/10 backdrop-blur-sm rounded-lg p-4">
                    <div class="text-3xl font-bold text-yellow-300">{{ $overallStats['overall_grade'] ?? 'N/A' }}</div>
                    <div class="text-sm text-indigo-200">Current Grade</div>
                </div>
                @php
                    $percentage = $overallStats['average_percentage'] ?? 0;
                    $color = $percentage >= 80 ? 'text-green-300' : ($percentage >= 60 ? 'text-yellow-300' : 'text-red-300');
                @endphp
                <div class="text-center bg-white/10 backdrop-blur-sm rounded-lg p-4">
                    <div class="text-3xl font-bold {{ $color }}">{{ number_format($percentage, 1) }}%</div>
                    <div class="text-sm text-indigo-200">Overall Score</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Filters with Search and Export -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 flex-1">
                    <div>
                        <label for="period" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Time Period
                        </label>
                        <select wire:model="selectedPeriod" id="period"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="all">All Time</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="quarter">This Quarter</option>
                            <option value="year">This Year</option>
                        </select>
                    </div>
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Subject Filter
                        </label>
                        <select wire:model="selectedSubject" id="subject"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">All Subjects</option>
                            @foreach($subjects as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center space-x-3">
                    <button @click="showCharts = !showCharts"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span x-text="showCharts ? 'Hide Charts' : 'Show Charts'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Stats Dashboard -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Assessments -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Assessments</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $overallStats['total_assessments'] ?? 0 }}</p>
                    </div>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    +{{ $overallStats['completed_assessments'] ?? 0 }} completed
                </div>
            </div>
        </div>

        <!-- Average Score -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Average Score</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($overallStats['average_percentage'] ?? 0, 1) }}%</p>
                    </div>
                </div>
                @php
                    $trend = ($overallStats['average_percentage'] ?? 0) >= 75 ? 'up' : 'down';
                    $trendColor = $trend === 'up' ? 'text-green-500' : 'text-red-500';
                @endphp
                <div class="flex items-center {{ $trendColor }}">
                    <svg class="w-4 h-4 {{ $trend === 'down' ? 'rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Overall Grade -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-full">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Overall Grade</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $overallStats['overall_grade'] ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completion Rate -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Completion Rate</p>
                        @php
                            $total = $overallStats['total_assessments'] ?? 0;
                            $completed = $overallStats['completed_assessments'] ?? 0;
                            $rate = $total > 0 ? ($completed / $total) * 100 : 0;
                        @endphp
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($rate, 1) }}%</p>
                    </div>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $completed }}/{{ $total }}
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div x-show="showCharts" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Performance by Subject Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Performance by Subject</h3>
                <canvas id="performanceChart" style="max-width: 100%; max-height: 400px;"></canvas>
            </div>
        </div>

        <!-- Trend Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Performance Trend</h3>
                <canvas id="trendChart" style="max-width: 100%; max-height: 400px;"></canvas>
            </div>
        </div>
    </div>


    <!-- Enhanced Subject Performance Table -->
    @if(!empty($performanceData))
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Subject Performance Analysis</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Detailed breakdown of your performance across all subjects</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Subject
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Assessments
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Average Score
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Percentage
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Grade
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Progress
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Trend
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($performanceData as $index => $subject)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full mr-3" style="background-color: {{ ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4'][$index % 6] }}"></div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $subject['subject'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ $subject['total_assessments'] }}
                                    </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ number_format($subject['average_score'], 1) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-1 bg-gray-200 dark:bg-gray-600 rounded-full h-2 mr-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min($subject['percentage'], 100) }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-900 dark:text-white">{{ number_format($subject['percentage'], 1) }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $gradeColors = [
                                        'A+' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                        'A' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                        'B+' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                        'B' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                        'C+' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                        'C' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                        'D' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                                        'F' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                    ];
                                    $gradeClass = $gradeColors[$subject['grade']] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $gradeClass }}">
                                        {{ $subject['grade'] }}
                                    </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-16 bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full transition-all duration-500"
                                         style="width: {{ min($subject['percentage'], 100) }}%"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($subject['trend'] === 'up')
                                    <div class="flex items-center text-green-600 dark:text-green-400">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-xs font-medium">Improving</span>
                                    </div>
                                @elseif($subject['trend'] === 'down')
                                    <div class="flex items-center text-red-600 dark:text-red-400">
                                        <svg class="w-4 h-4 mr-1 rotate-180" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-xs font-medium">Declining</span>
                                    </div>
                                @else
                                    <div class="flex items-center text-gray-600 dark:text-gray-400">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-xs font-medium">Stable</span>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No performance data</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Start taking assessments to see your performance analytics here.</p>
                <div class="mt-6">
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Take Assessment
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Performance Insights Panel -->
    @if(!empty($performanceData))
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-xl p-6 border border-blue-200 dark:border-gray-600">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                AI Performance Insights
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                    $topSubject = collect($performanceData)->sortByDesc('percentage')->first();
                    $improvementSubject = collect($performanceData)->sortBy('percentage')->first();
                    $totalAssessments = $overallStats['total_assessments'] ?? 0;
                @endphp

                @if($topSubject)
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                        <div class="flex items-center text-green-600 dark:text-green-400 mb-2">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">Strongest Subject</span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            You're excelling in <strong>{{ $topSubject['subject'] }}</strong> with {{ number_format($topSubject['percentage'], 1) }}% average score!
                        </p>
                    </div>
                @endif

                @if($improvementSubject && $improvementSubject['percentage'] < 70)
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                        <div class="flex items-center text-orange-600 dark:text-orange-400 mb-2">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">Focus Area</span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Consider spending more time on <strong>{{ $improvementSubject['subject'] }}</strong> to improve your {{ number_format($improvementSubject['percentage'], 1) }}% score.
                        </p>
                    </div>
                @endif

                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center text-blue-600 dark:text-blue-400 mb-2">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">Activity Level</span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        You've completed <strong>{{ $totalAssessments }}</strong> assessments.
                        {{ $totalAssessments >= 10 ? 'Great consistency!' : 'Try to maintain regular practice.' }}
                    </p>
                </div>
            </div>
        </div>
    @endif

        <!-- Make sure Chart.js loads first -->
{{--        <script src="{{ asset('js/ChartDataHelper.js') }}"></script>--}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Debug: Check if Chart.js is loaded
                console.log('Chart.js loaded:', typeof Chart !== 'undefined');
                console.log('ChartDataHelper loaded:', typeof ChartDataHelper !== 'undefined');

                // Wait for Chart.js to be fully available
                function waitForChart() {
                    return new Promise((resolve) => {
                        if (typeof Chart !== 'undefined' && Chart.registry) {
                            resolve();
                        } else {
                            setTimeout(() => waitForChart().then(resolve), 100);
                        }
                    });
                }

                waitForChart().then(() => {
                    let performanceChart = null;
                    let trendChart = null;

                    function initializeCharts() {
                        const performanceData = @json($performanceData);
                        const trendData = @json($trendData);

                        // Destroy existing charts
                        if (performanceChart) {
                            ChartDataHelper.destroyChart(performanceChart);
                        }
                        if (trendChart) {
                            ChartDataHelper.destroyChart(trendChart);
                        }

                        // Performance Chart using ChartDataHelper
                        const performanceCtx = document.getElementById('performanceChart');
                        if (performanceCtx && performanceData.length > 0) {
                            const chartConfig = ChartDataHelper.generateBarChartData(
                                performanceData.map(item => item.subject),
                                [{
                                    label: 'Average Score (%)',
                                    data: performanceData.map(item => item.percentage),
                                    backgroundColor: 'rgba(59, 130, 246, 0.6)',
                                    borderColor: 'rgba(59, 130, 246, 1)'
                                }],
                                {
                                    scales: {
                                        y: {
                                            max: 100
                                        }
                                    }
                                }
                            );

                            performanceChart = ChartDataHelper.createAnimatedChart(performanceCtx, chartConfig);
                        }

                        // Trend Chart
                        const trendCtx = document.getElementById('trendChart');
                        if (trendCtx && trendData.length > 0) {
                            const trendConfig = ChartDataHelper.generateLineChartData(
                                trendData.map(item => item.period),
                                [{
                                    label: 'Average Score (%)',
                                    data: trendData.map(item => item.score),
                                    borderColor: 'rgba(34, 197, 94, 1)',
                                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                    fill: true,
                                    tension: 0.4
                                }],
                                {
                                    scales: {
                                        y: {
                                            max: 100
                                        }
                                    }
                                }
                            );

                            trendChart = ChartDataHelper.createAnimatedChart(trendCtx, trendConfig);
                        }
                    }

                    // Initialize charts
                    initializeCharts();

                    // Event listeners for data changes
                    Livewire.on('periodChanged', () => setTimeout(initializeCharts, 100));
                    Livewire.on('subjectChanged', () => setTimeout(initializeCharts, 100));
                });
            });
        </script>

</div>
