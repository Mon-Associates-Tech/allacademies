<div class="space-y-6" x-data="{
    showCharts: true,
    activeMetric: 'overview',
    showTooltip: false,
    tooltipContent: '',
    activeInsight: null
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
                    <h1 class="text-3xl font-bold">Assignment Performance</h1>
                    <p class="text-indigo-100 mt-1">Track your academic progress and achievements</p>
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

    <!-- Filters Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div>
            <!-- Performance Overview Component -->
            <div class="space-y-6">
                <!-- Filters -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Period Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Time Period
                            </label>
                            <select wire:model.live="selectedPeriod" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                <option value="all">All Time</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                                <option value="quarter">This Quarter</option>
                                <option value="year">This Year</option>
                            </select>
                        </div>

                        <!-- Subject Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Subject
                            </label>
                            <select wire:model.live="selectedSubject" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                <option value="">All Subjects</option>
                                @foreach($subjects as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Overall Stats Cards -->
                @if(!empty($overallStats))
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Average Score</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ $overallStats['average_percentage'] }}%
                                    </p>
                                    <p class="text-sm text-gray-500">{{ $overallStats['overall_grade'] }}</p>
                                </div>
                                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Completed</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ $overallStats['completed_assignments'] }}
                                    </p>
                                    <p class="text-sm text-gray-500">of {{ $overallStats['total_assignments'] }}</p>
                                </div>
                                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Completion Rate</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ $overallStats['completion_rate'] }}%
                                    </p>
                                    <p class="text-sm text-gray-500">{{ $overallStats['total_subjects'] }} subjects</p>
                                </div>
                                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Study Streak</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ $overallStats['study_streak'] }}
                                    </p>
                                    <p class="text-sm text-gray-500">days</p>
                                </div>
                                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Performance Trend Chart -->
                @if(!empty($trendData))
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Performance Trend</h3>
                        <div class="h-64">
                            <canvas id="performanceTrendChart"></canvas>
                        </div>
                    </div>
                @endif

                <!-- Subject Performance Table -->
                @if(!empty($performanceData))
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Performance by Subject</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Subject</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Assignments</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Average</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Grade</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Trend</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($performanceData as $data)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $data['subject'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $data['total_assignments'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $data['percentage'] }}%</span>
                                                <div class="ml-2 w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min($data['percentage'], 100) }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($data['grade'] === 'A' || $data['grade'] === 'A+') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($data['grade'] === 'B' || $data['grade'] === 'B+') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @elseif($data['grade'] === 'C') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @endif">
                                    {{ $data['grade'] }}
                                </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($data['trend'] === 'up')
                                                <span class="text-green-600 dark:text-green-400 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                        </svg>
                                        +{{ $data['improvement'] }}%
                                    </span>
                                            @elseif($data['trend'] === 'down')
                                                <span class="text-red-600 dark:text-red-400 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                                        </svg>
                                        {{ $data['improvement'] }}%
                                    </span>
                                            @else
                                                <span class="text-gray-600 dark:text-gray-400">Stable</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Insights -->
                @if(!empty($insights))
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($insights as $insight)
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4
                @if($insight['color'] === 'green') border-green-500
                @elseif($insight['color'] === 'blue') border-blue-500
                @elseif($insight['color'] === 'yellow') border-yellow-500
                @elseif($insight['color'] === 'orange') border-orange-500
                @endif">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $insight['title'] }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ $insight['message'] }}</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $insight['action'] }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        initializeCharts();
                    });

                    document.addEventListener('livewire:navigated', function() {
                        initializeCharts();
                    });

                    function initializeCharts() {
                        const trendCanvas = document.getElementById('performanceTrendChart');
                        if (!trendCanvas) return;

                        const trendData = @json($trendData ?? []);

                        if (trendData.length === 0) {
                            trendCanvas.parentElement.innerHTML = '<p class="text-center text-gray-500 dark:text-gray-400">No data available</p>';
                            return;
                        }

                        const ctx = trendCanvas.getContext('2d');

                        // Destroy existing chart if it exists
                        if (window.performanceTrendChart instanceof Chart) {
                            window.performanceTrendChart.destroy();
                        }

                        window.performanceTrendChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: trendData.map(d => d.week),
                                datasets: [{
                                    label: 'Average Score',
                                    data: trendData.map(d => d.percentage),
                                    borderColor: 'rgb(59, 130, 246)',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    tension: 0.4,
                                    fill: true
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                return 'Score: ' + context.parsed.y.toFixed(1) + '%';
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        max: 100,
                                        ticks: {
                                            callback: function(value) {
                                                return value + '%';
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }

                    // Reinitialize on Livewire updates
                    Livewire.hook('morph.updated', () => {
                        setTimeout(initializeCharts, 100);
                    });
                </script>
        </div>
    </div>

    <!-- Overall Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Assignments -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Assignments</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $overallStats['total_assignments'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ $overallStats['available_assignments'] ?? 0 }} available now
                    </p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Average Score -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Average Score</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($overallStats['average_percentage'] ?? 0, 1) }}%</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Grade: {{ $overallStats['overall_grade'] ?? 'N/A' }}</p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Completion Rate -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Completion Rate</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($overallStats['completion_rate'] ?? 0, 1) }}%</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $overallStats['completed_assignments'] ?? 0 }} completed</p>
                </div>
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-full">
                    <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Assignments -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Pending</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $overallStats['pending_assignments'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $overallStats['submitted_assignments'] ?? 0 }} submitted</p>
                </div>
                <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-full">
                    <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Assignments Alert -->
    @if(!empty($pendingAssignments) && count($pendingAssignments) > 0)
        <div class="bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-400 p-4 rounded-r-lg shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-orange-800 dark:text-orange-200">
                        ⚠️ You have {{ count($pendingAssignments) }} pending assignment(s)
                    </h3>
                    <div class="mt-2 text-sm text-orange-700 dark:text-orange-300">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($pendingAssignments as $pending)
                                <li class="py-1">
                                    <strong>{{ $pending['assignment']->title }}</strong>
                                    <span class="text-xs">
                                        - Due in {{ $pending['time_remaining'] }} hours
                                    </span>
                                    @if($pending['status'] === 'in_progress')
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                            In Progress
                                        </span>
                                    @else
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                            Not Started
                                        </span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('students.assignments') }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-orange-700 bg-orange-100 hover:bg-orange-200 dark:bg-orange-900/50 dark:text-orange-200 dark:hover:bg-orange-900 transition-colors">
                            View All Assignments
                            <svg class="ml-1 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Upcoming Assignments -->
    @if(!empty($upcomingAssignments) && count($upcomingAssignments) > 0)
        <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 p-4 rounded-r-lg shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                        📅 Upcoming Assignments ({{ count($upcomingAssignments) }})
                    </h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <ul class="space-y-1">
                            @foreach($upcomingAssignments as $upcoming)
                                <li class="py-1">
                                    <strong>{{ $upcoming->title }}</strong>
                                    <span class="text-xs">
                                        - Starts {{ $upcoming->starts_at->diffForHumans() }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Charts Section -->
    <div x-show="showCharts" x-transition class="grid grid-cols-1 lg:grid-cols-2 gap-6">
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

    <!-- Subject Performance Table -->
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Assignments</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Average Score</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Percentage</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Grade</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Progress</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Trend</th>
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
                                        {{ $subject['total_assignments'] }}
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
                                        'A-' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                        'B+' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                        'B' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                        'B-' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                        'C+' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                        'C' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                        'C-' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                        'D+' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
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
                                        <span class="text-xs font-medium">+{{ number_format($subject['improvement'], 1) }}%</span>
                                    </div>
                                @elseif($subject['trend'] === 'down')
                                    <div class="flex items-center text-red-600 dark:text-red-400">
                                        <svg class="w-4 h-4 mr-1 rotate-180" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-xs font-medium">{{ number_format($subject['improvement'], 1) }}%</span>
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
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Start taking assignments to see your performance analytics here.</p>
                <div class="mt-6">
                    <a href="{{ route('students.assignments') }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        View Assignments
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- AI Performance Insights -->
    @if(!empty($insights))
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-xl p-6 border border-blue-200 dark:border-gray-600">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                AI Performance Insights
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($insights as $insight)
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border-l-4 border-{{ $insight['color'] }}-500"
                         @mouseenter="activeInsight = {{ $loop->index }}"
                         @mouseleave="activeInsight = null">
                        <div class="flex items-start">
                            @php
                                $iconColors = [
                                    'green' => 'text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900',
                                    'orange' => 'text-orange-600 dark:text-orange-400 bg-orange-100 dark:bg-orange-900',
                                    'blue' => 'text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900',
                                    'yellow' => 'text-yellow-600 dark:text-yellow-400 bg-yellow-100 dark:bg-yellow-900',
                                ];
                                $iconClass = $iconColors[$insight['color']] ?? 'text-gray-600 bg-gray-100';
                            @endphp
                            <div class="flex-shrink-0 {{ $iconClass }} rounded-full p-2">
                                @if($insight['type'] === 'strength')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                @elseif($insight['type'] === 'improvement')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="ml-3 flex-1">
                                <h4 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $insight['title'] }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ $insight['message'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 italic">💡 {{ $insight['action'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Chart.js Initialization -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                    if (performanceChart) performanceChart.destroy();
                    if (trendChart) trendChart.destroy();

                    // Performance by Subject Chart
                    if (performanceData && performanceData.length > 0) {
                        const perfCtx = document.getElementById('performanceChart');
                        if (perfCtx) {
                            performanceChart = new Chart(perfCtx, {
                                type: 'bar',
                                data: {
                                    labels: performanceData.map(d => d.subject),
                                    datasets: [{
                                        label: 'Average Score (%)',
                                        data: performanceData.map(d => d.percentage),
                                        backgroundColor: [
                                            'rgba(59, 130, 246, 0.8)',
                                            'rgba(16, 185, 129, 0.8)',
                                            'rgba(245, 158, 11, 0.8)',
                                            'rgba(239, 68, 68, 0.8)',
                                            'rgba(139, 92, 246, 0.8)',
                                            'rgba(6, 182, 212, 0.8)'
                                        ],
                                        borderColor: [
                                            'rgb(59, 130, 246)',
                                            'rgb(16, 185, 129)',
                                            'rgb(245, 158, 11)',
                                            'rgb(239, 68, 68)',
                                            'rgb(139, 92, 246)',
                                            'rgb(6, 182, 212)'
                                        ],
                                        borderWidth: 2,
                                        borderRadius: 5,
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    return 'Score: ' + context.parsed.y.toFixed(1) + '%';
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            max: 100,
                                            ticks: {
                                                callback: function(value) {
                                                    return value + '%';
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    }

                    // Trend Chart
                    if (trendData && trendData.length > 0) {
                        const trendCtx = document.getElementById('trendChart');
                        if (trendCtx) {
                            trendChart = new Chart(trendCtx, {
                                type: 'line',
                                data: {
                                    labels: trendData.map(d => d.week),
                                    datasets: [{
                                        label: 'Performance Trend',
                                        data: trendData.map(d => d.percentage),
                                        borderColor: 'rgb(59, 130, 246)',
                                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                        tension: 0.4,
                                        fill: true,
                                        pointRadius: 4,
                                        pointBackgroundColor: 'rgb(59, 130, 246)',
                                        pointBorderColor: '#fff',
                                        pointBorderWidth: 2,
                                        pointHoverRadius: 6,
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    return 'Score: ' + context.parsed.y.toFixed(1) + '%';
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            max: 100,
                                            ticks: {
                                                callback: function(value) {
                                                    return value + '%';
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    }
                }

                // Initialize charts
                initializeCharts();

                // Reinitialize on Livewire updates
                Livewire.on('periodChanged', () => initializeCharts());
                Livewire.on('subjectChanged', () => initializeCharts());
            });
        });
    </script>
</div>
