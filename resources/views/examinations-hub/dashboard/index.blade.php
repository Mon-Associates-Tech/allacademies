<x-layouts.app>
    <x-examinations-hub.navigation active="dashboard" />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Examinations Hub</h1>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4"><div class="text-2xl font-bold">{{ $summary['total_exams'] }}</div><div class="text-sm text-gray-500">Exams</div></div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4"><div class="text-2xl font-bold">{{ $summary['total_submissions'] }}</div><div class="text-sm text-gray-500">Submissions</div></div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4"><div class="text-2xl font-bold">{{ $summary['avg_score'] }}%</div><div class="text-sm text-gray-500">Average Score</div></div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4"><div class="text-2xl font-bold">{{ $summary['auto_gradable'] }}</div><div class="text-sm text-gray-500">Auto-Gradable</div></div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4"><div class="text-2xl font-bold">{{ $summary['manual_review'] }}</div><div class="text-sm text-gray-500">Manual Review</div></div>
        </div>

        <!-- Charts Section -->
        <div class="grid lg:grid-cols-2 gap-6 mb-6">
            <!-- Submission Trend Chart -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Submission Trend (Last 30 Days)</h2>
                <div class="h-64">
                    <canvas id="submissionTrendChart"></canvas>
                </div>
            </div>

            <!-- Exam Status Distribution -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Exam Status Distribution</h2>
                <div class="h-64">
                    <canvas id="examStatusChart"></canvas>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-gray-500">Title</th>
                        <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-gray-500">Code</th>
                        <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-gray-500">Sections</th>
                        <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-gray-500">Questions</th>
                        <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-gray-500">Submissions</th>
                        <th class="px-4 py-3 text-right text-xs uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($exams as $exam)
                        <tr>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $exam->title }}</td>
                            <td class="px-4 py-3">{{ $exam->access_code }}</td>
                            <td class="px-4 py-3">{{ $exam->sections_count }}</td>
                            <td class="px-4 py-3">{{ $exam->questions_count }}</td>
                            <td class="px-4 py-3">{{ $exam->submissions_count }}</td>
                            <td class="px-4 py-3 text-right">
                                <a class="text-indigo-600 hover:underline" href="{{ route('examinations-hub.exams.show', $exam) }}">Open</a>
                                @if(!$exam->starts_at || now()->lt($exam->starts_at))
                                    <span class="text-gray-300 px-1">|</span>
                                    <a class="text-emerald-600 hover:underline" href="{{ route('examinations-hub.exams.edit', $exam) }}">Edit</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No examinations yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4">{{ $exams->links() }}</div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Submission Trend Chart
            const submissionTrend = @json($summary['submission_trend']);
            const trendCtx = document.getElementById('submissionTrendChart').getContext('2d');
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: Object.keys(submissionTrend),
                    datasets: [{
                        label: 'Submissions',
                        data: Object.values(submissionTrend),
                        borderColor: 'rgb(99, 102, 241)',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgb(99, 102, 241)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        },
                        x: {
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45
                            }
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

            // Exam Status Distribution Chart
            const statusData = @json($summary['exam_status_distribution']);
            const statusCtx = document.getElementById('examStatusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(statusData),
                    datasets: [{
                        data: Object.values(statusData),
                        backgroundColor: [
                            'rgba(251, 191, 36, 0.8)',
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(156, 163, 175, 0.8)'
                        ],
                        borderColor: [
                            'rgb(251, 191, 36)',
                            'rgb(34, 197, 94)',
                            'rgb(156, 163, 175)'
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
                            padding: 12
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-layouts.app>
