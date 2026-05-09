<x-layouts.exam>
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        @if($needsEmail)
            <!-- Email Verification Form -->
            <div class="max-w-md mx-auto">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
                    <div class="text-center mb-6">
                        <div class="mx-auto h-16 w-16 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mb-4">
                            <svg class="h-8 w-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Access Your Results</h2>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">Enter your email to view your exam results</p>
                    </div>

                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                            <p class="text-sm text-red-600 dark:text-red-400">{{ session('error') }}</p>
                        </div>
                    @endif

                    <form method="GET" action="{{ route('examinations-hub.results.index') }}">
                        <div class="mb-6">
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                            <input type="email" name="email" id="email" required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                                placeholder="your.email@example.com">
                        </div>

                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200">
                            View My Results
                        </button>
                    </form>
                </div>
            </div>
        @else
            <!-- Results Dashboard -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Exam Results</h1>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">{{ $email }}</p>
                    </div>
                    <a href="{{ route('examinations-hub.results.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                        Change Email
                    </a>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Submissions</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $summary['total_submissions'] }}</p>
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
                            <p class="text-sm text-gray-600 dark:text-gray-400">Results Released</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $summary['results_released'] }}</p>
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
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($summary['average_percentage'], 1) }}%</p>
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
                            <p class="text-sm text-gray-600 dark:text-gray-400">Best Score</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($summary['best_percentage'], 1) }}%</p>
                        </div>
                        <div class="h-12 w-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            @if(false && $submissions->isNotEmpty() && isset($performanceTrend) && isset($gradeDistribution))
                <!-- Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Performance Trend</h3>
                        <canvas id="performanceTrendChart" height="250"></canvas>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Grade Distribution</h3>
                        <canvas id="gradeDistributionChart" height="250"></canvas>
                    </div>
                </div>
            @endif

            <!-- Submissions List -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">All Submissions</h2>
                </div>

                @if($submissions->isEmpty())
                    <div class="p-8 text-center">
                        <p class="text-gray-500 dark:text-gray-400">No exam submissions found.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Exam</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Submitted</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Score</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Grade</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($submissions as $submission)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $submission->assignment->title }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">by {{ $submission->assignment->user->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $submission->submitted_at?->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                            {{ $submission->score }}/{{ $submission->total_marks }}
                                            <span class="text-gray-500 dark:text-gray-400">({{ number_format($submission->percentage, 1) }}%)</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                @if($submission->grade === 'A+' || $submission->grade === 'A') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                @elseif($submission->grade === 'B') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                @elseif($submission->grade === 'C') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                @endif">
                                                {{ $submission->grade }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($submission->canViewResults())
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    Released
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                    Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm">
                                            @if($submission->canViewResults())
                                                <a href="{{ route('examinations-hub.results.show', ['submission' => $submission, 'email' => $email]) }}" 
                                                   class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                                    View Details
                                                </a>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-600">Not Available</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>

@if(false && !$needsEmail && $submissions->isNotEmpty() && isset($performanceTrend) && isset($gradeDistribution))
    <script>
        (function() {
            if (window.chartsInitialized) return;
            window.chartsInitialized = true;

            document.addEventListener('DOMContentLoaded', function() {
                const isDark = document.documentElement.classList.contains('dark');
                const textColor = isDark ? '#9CA3AF' : '#6B7280';
                const gridColor = isDark ? '#374151' : '#E5E7EB';

                // Performance Trend Chart
                const trendCanvas = document.getElementById('performanceTrendChart');
                if (trendCanvas && !trendCanvas.chart) {
                    const trendCtx = trendCanvas.getContext('2d');
                    trendCanvas.chart = new Chart(trendCtx, {
                        type: 'line',
                        data: {
                            labels: {!! json_encode($performanceTrend->keys()) !!},
                            datasets: [{
                                label: 'Score (%)',
                                data: {!! json_encode($performanceTrend->values()) !!},
                                borderColor: '#6366F1',
                                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                tension: 0.4,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: (context) => `Score: ${context.parsed.y}%`
                                    }
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

                // Grade Distribution Chart
                const gradeCanvas = document.getElementById('gradeDistributionChart');
                if (gradeCanvas && !gradeCanvas.chart) {
                    const gradeCtx = gradeCanvas.getContext('2d');
                    const gradeData = {!! json_encode($gradeDistribution) !!};
                    const grades = ['A+', 'A', 'B', 'C', 'D', 'F'];
                    const gradeCounts = grades.map(g => gradeData[g] || 0);

                    gradeCanvas.chart = new Chart(gradeCtx, {
                        type: 'bar',
                        data: {
                            labels: grades,
                            datasets: [{
                                label: 'Count',
                                data: gradeCounts,
                                backgroundColor: [
                                    '#10B981',
                                    '#34D399',
                                    '#3B82F6',
                                    '#FBBF24',
                                    '#F59E0B',
                                    '#EF4444'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: { 
                                        color: textColor,
                                        stepSize: 1
                                    },
                                    grid: { color: gridColor }
                                },
                                x: {
                                    ticks: { color: textColor },
                                    grid: { display: false }
                                }
                            }
                        }
                    });
                }
            });
        })();
    </script>
@endif
</div>
</x-layouts.exam>
