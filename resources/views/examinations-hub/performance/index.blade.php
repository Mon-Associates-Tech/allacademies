<x-layouts.app>
    <x-examinations-hub.navigation active="performance" />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Participant Performance</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">View examination performance and metrics for all participants</p>
        </div>

        <!-- Performance Overview Chart -->
        <div class="grid lg:grid-cols-3 gap-6 mb-6">
            <!-- Performance Distribution -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Performance Distribution</h2>
                <div class="h-64">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>

            <!-- Average Grade Distribution -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Average Grade Distribution</h2>
                <div class="h-64">
                    <canvas id="gradeChart"></canvas>
                </div>
            </div>

            <!-- Top Performers -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top 10 Performers</h2>
                <div class="h-64">
                    <canvas id="topPerformersChart"></canvas>
                </div>
            </div>
        </div>

        <div id="participants-table" class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <form method="GET" class="flex gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}"
                            placeholder="Name or Email..."
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                        >
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Search
                        </button>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                <a href="?sort_by=name&sort_order={{ $sortBy === 'name' && $sortOrder === 'asc' ? 'desc' : 'asc' }}{{ request('search') ? '&search=' . request('search') : '' }}#participants-table" 
                                   class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-100">
                                    Participant
                                    @if($sortBy === 'name')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($sortOrder === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            @endif
                                        </svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Email</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                <a href="?sort_by=submissions&sort_order={{ $sortBy === 'submissions' && $sortOrder === 'asc' ? 'desc' : 'asc' }}{{ request('search') ? '&search=' . request('search') : '' }}#participants-table" 
                                   class="inline-flex items-center justify-center gap-1 hover:text-gray-700 dark:hover:text-gray-100">
                                    Submissions
                                    @if($sortBy === 'submissions')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($sortOrder === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            @endif
                                        </svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                <a href="?sort_by=performance&sort_order={{ $sortBy === 'performance' && $sortOrder === 'asc' ? 'desc' : 'asc' }}{{ request('search') ? '&search=' . request('search') : '' }}#participants-table" 
                                   class="inline-flex items-center justify-center gap-1 hover:text-gray-700 dark:hover:text-gray-100">
                                    Avg Score
                                    @if($sortBy === 'performance')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($sortOrder === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            @endif
                                        </svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($participants as $participant)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                                <span class="text-indigo-600 dark:text-indigo-300 font-medium text-sm">
                                                    {{ strtoupper(substr($participant->participant_name, 0, 2)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $participant->participant_name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $participant->participant_email ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        @if($participant->participant_type === 'configured') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                        @endif">
                                        {{ ucfirst($participant->participant_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900 dark:text-white">
                                    {{ $participant->submission_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                        @if($participant->avg_percentage >= 80) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($participant->avg_percentage >= 60) bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        @elseif($participant->avg_percentage >= 50) bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @endif">
                                        {{ round($participant->avg_percentage, 1) }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <a href="{{ route('examinations-hub.performance.show', [$participant->participant_type, $participant->participant_id ?? 0]) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">
                                        View Performance
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    No participants found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($participants->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $participants->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Performance Distribution Chart
            const performanceData = @json($performanceDistribution);
            
            const ctx = document.getElementById('performanceChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(performanceData),
                    datasets: [{
                        data: Object.values(performanceData),
                        backgroundColor: [
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(251, 191, 36, 0.8)',
                            'rgba(239, 68, 68, 0.8)'
                        ],
                        borderColor: [
                            'rgb(34, 197, 94)',
                            'rgb(59, 130, 246)',
                            'rgb(251, 191, 36)',
                            'rgb(239, 68, 68)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: { size: 12 }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 14 },
                            bodyFont: { size: 13 }
                        }
                    }
                }
            });

            // Average Grade Distribution Chart
            const gradeData = @json($gradeDistribution);
            const gradeCtx = document.getElementById('gradeChart').getContext('2d');
            new Chart(gradeCtx, {
                type: 'bar',
                data: {
                    labels: ['A+', 'A', 'B', 'C', 'D', 'F'],
                    datasets: [{
                        label: 'Participants',
                        data: ['A+', 'A', 'B', 'C', 'D', 'F'].map(grade => gradeData[grade] || 0),
                        backgroundColor: [
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(99, 102, 241, 0.8)',
                            'rgba(251, 191, 36, 0.8)',
                            'rgba(249, 115, 22, 0.8)',
                            'rgba(239, 68, 68, 0.8)'
                        ],
                        borderColor: [
                            'rgb(34, 197, 94)',
                            'rgb(59, 130, 246)',
                            'rgb(99, 102, 241)',
                            'rgb(251, 191, 36)',
                            'rgb(249, 115, 22)',
                            'rgb(239, 68, 68)'
                        ],
                        borderWidth: 2,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12
                        }
                    }
                }
            });

            // Top Performers Chart
            const topPerformers = @json($topPerformers);
            const topPerformersCtx = document.getElementById('topPerformersChart').getContext('2d');
            new Chart(topPerformersCtx, {
                type: 'bar',
                data: {
                    labels: topPerformers.map(p => p.participant_name.length > 15 ? p.participant_name.substring(0, 15) + '...' : p.participant_name),
                    datasets: [{
                        label: 'Average Score',
                        data: topPerformers.map(p => p.avg_percentage),
                        backgroundColor: 'rgba(99, 102, 241, 0.8)',
                        borderColor: 'rgb(99, 102, 241)',
                        borderWidth: 2,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) { return value + '%'; }
                            }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    return 'Average Score: ' + context.parsed.x + '%';
                                }
                            }
                        }
                    }
                }
            });
        });

        // Smooth scroll to table if sort parameters are present
        if (window.location.hash === '#participants-table') {
            setTimeout(function() {
                document.getElementById('participants-table').scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
            }, 100);
        }
    </script>
    @endpush
</x-layouts.app>
