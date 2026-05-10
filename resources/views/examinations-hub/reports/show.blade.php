<x-layouts.app>
    <x-examinations-hub.navigation active="reports" />
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Performance Report</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Period: {{ $data['period']['start'] }} to {{ $data['period']['end'] }}
                </p>
            </div>
            <div class="flex space-x-3">
                <button onclick="window.print()" 
                        class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print
                </button>
                <a href="{{ route('examinations-hub.reports.index') }}" 
                   class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Generate New Report
                </a>
            </div>
        </div>

        <!-- Token Usage Info -->
        @if($usage)
            <div class="mb-6 bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">AI Tokens Used:</span>
                    <span class="font-semibold text-gray-900 dark:text-white">
                        {{ number_format($usage['total_tokens'] ?? 0) }} tokens
                        (Prompt: {{ number_format($usage['prompt_tokens'] ?? 0) }}, 
                        Completion: {{ number_format($usage['completion_tokens'] ?? 0) }})
                    </span>
                </div>
            </div>
        @endif

        <!-- Quick Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Exams</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $data['overview']['total_exams'] }}</p>
                    </div>
                    <div class="h-12 w-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Submissions</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $data['overview']['total_submissions'] }}</p>
                    </div>
                    <div class="h-12 w-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Average Score</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($data['overview']['average_score'], 1) }}%</p>
                    </div>
                    <div class="h-12 w-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Completion Rate</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($data['overview']['completion_rate'], 1) }}%</p>
                    </div>
                    <div class="h-12 w-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Generated Report -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center">
                <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">AI-Generated Analysis</h2>
            </div>

            <div class="p-6 prose prose-indigo dark:prose-invert max-w-none">
                {!! \Illuminate\Support\Str::markdown($report) !!}
            </div>
        </div>

        <!-- Data Visualizations -->
        <div class="mt-8 space-y-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Data Visualizations</h2>
            
            <!-- Grade Distribution Chart -->
            @if(!empty($data['grade_distribution']))
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Grade Distribution</h3>
                <canvas id="gradeDistributionChart" height="80"></canvas>
            </div>
            @endif

            <!-- Daily Submissions Chart -->
            @if(!empty($data['daily_submissions']))
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Daily Submission Trends</h3>
                <canvas id="dailySubmissionsChart" height="80"></canvas>
            </div>
            @endif

            <!-- Exam Performance Chart -->
            @if(!empty($data['exam_performance']))
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Exam Performance Overview</h3>
                <canvas id="examPerformanceChart" height="80"></canvas>
            </div>
            @endif
        </div>

    </div>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? '#e5e7eb' : '#374151';
    const gridColor = isDark ? '#374151' : '#e5e7eb';

    // Grade Distribution Chart
    @if(!empty($data['grade_distribution']))
    const gradeCtx = document.getElementById('gradeDistributionChart');
    if (gradeCtx) {
        new Chart(gradeCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($data['grade_distribution'])) !!},
                datasets: [{
                    label: 'Number of Students',
                    data: {!! json_encode(array_values($data['grade_distribution'])) !!},
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(234, 179, 8, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ],
                    borderColor: [
                        'rgb(34, 197, 94)',
                        'rgb(59, 130, 246)',
                        'rgb(234, 179, 8)',
                        'rgb(249, 115, 22)',
                        'rgb(239, 68, 68)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    title: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: textColor, stepSize: 1 },
                        grid: { color: gridColor }
                    },
                    x: {
                        ticks: { color: textColor },
                        grid: { color: gridColor }
                    }
                }
            }
        });
    }
    @endif

    // Daily Submissions Chart
    @if(!empty($data['daily_submissions']))
    const dailyCtx = document.getElementById('dailySubmissionsChart');
    if (dailyCtx) {
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_keys($data['daily_submissions'])) !!},
                datasets: [{
                    label: 'Submissions',
                    data: {!! json_encode(array_values($data['daily_submissions'])) !!},
                    borderColor: 'rgb(99, 102, 241)',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: textColor, stepSize: 1 },
                        grid: { color: gridColor }
                    },
                    x: {
                        ticks: { color: textColor },
                        grid: { color: gridColor }
                    }
                }
            }
        });
    }
    @endif

    // Exam Performance Chart
    @if(!empty($data['exam_performance']))
    const examCtx = document.getElementById('examPerformanceChart');
    if (examCtx) {
        const examData = {!! json_encode($data['exam_performance']) !!};
        new Chart(examCtx, {
            type: 'bar',
            data: {
                labels: examData.map(e => e.title.length > 20 ? e.title.substring(0, 20) + '...' : e.title),
                datasets: [
                    {
                        label: 'Average Score (%)',
                        data: examData.map(e => e.average_score),
                        backgroundColor: 'rgba(139, 92, 246, 0.8)',
                        borderColor: 'rgb(139, 92, 246)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Pass Rate (%)',
                        data: examData.map(e => e.pass_rate),
                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                        borderColor: 'rgb(34, 197, 94)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        labels: { color: textColor }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: { color: textColor },
                        grid: { color: gridColor }
                    },
                    x: {
                        ticks: { color: textColor },
                        grid: { color: gridColor }
                    }
                }
            }
        });
    }
    @endif
});
</script>
</x-layouts.app>
