<x-layouts.general-exam title="My Exam Results" pageName="Results Dashboard">
    <div class="py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">
            @if(($participant ?? null) && ($needsEmail ?? false))
                <div class="max-w-md mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 border border-gray-200 dark:border-gray-700">
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Confirm your email</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Enter the email you used for this assignment to access your full results dashboard.</p>
                    <form method="GET" action="{{ route('general-exams.results.dashboard', ['token' => $token]) }}">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                        <input type="email" name="email" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white mb-4">
                        <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">Continue</button>
                    </form>
                </div>
            @elseif(!($participant ?? null))
                <div class="text-center py-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Results Not Found</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">The result link is invalid or expired.</p>
                    <a href="{{ route('general-exams.join') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition-colors">
                        Join an Assignment
                    </a>
                </div>
            @else
                <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Results Dashboard</h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $participant->name }} ({{ $participant->email }})</p>
                    </div>
                    <form method="GET" action="{{ route('general-exams.results.dashboard', ['token' => $token]) }}" class="flex flex-col sm:flex-row gap-2">
                        <select name="subject" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white">
                            <option value="">All subjects</option>
                            @foreach($subjectOptions as $subject)
                                <option value="{{ $subject['id'] }}" @selected($selectedSubject === $subject['id'])>{{ $subject['name'] }}</option>
                            @endforeach
                        </select>
                        <select name="assigner" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white">
                            <option value="">All assigners</option>
                            @foreach($assignerOptions as $assigner)
                                <option value="{{ $assigner['id'] }}" @selected($selectedAssigner === $assigner['id'])>{{ $assigner['name'] }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">Filter</button>
                    </form>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Submissions</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['total_submissions'] ?? 0 }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Released</div>
                        <div class="text-2xl font-bold text-green-600">{{ $summary['results_released'] ?? 0 }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Average %</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($summary['average_percentage'] ?? 0, 1) }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Best %</div>
                        <div class="text-2xl font-bold text-indigo-600">{{ number_format($summary['best_percentage'] ?? 0, 1) }}</div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid lg:grid-cols-2 gap-6 mb-6">
                    <!-- Performance Trend Chart -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Performance Trend (Last 10 Exams)</h2>
                        <div class="h-64">
                            <canvas id="performanceTrendChart"></canvas>
                        </div>
                    </div>

                    <!-- Grade Distribution Chart -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Grade Distribution</h2>
                        <div class="h-64">
                            <canvas id="gradeDistributionChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    @if($submissions->isEmpty())
                        <div class="p-8 text-center text-gray-500 dark:text-gray-400">No submissions found for the selected filters.</div>
                    @else
                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($submissions as $submission)
                                @php
                                    $subjects = optional($submission->assignment?->subscription)->subjects ?? collect();
                                @endphp
                                <div class="p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                                    <div>
                                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $submission->assignment->title ?? 'Assignment' }}</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $submission->assignment?->user?->name ?? 'Unknown assigner' }}
                                            • {{ $submission->submitted_at?->format('M d, Y H:i') ?? 'N/A' }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            @if($subjects->isEmpty())
                                                No subject assigned
                                            @else
                                                {{ $subjects->pluck('name')->join(', ') }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div class="text-right">
                                            <div class="text-xl font-bold {{ ($submission->percentage ?? 0) >= 70 ? 'text-green-600' : (($submission->percentage ?? 0) >= 50 ? 'text-amber-600' : 'text-red-600') }}">
                                                {{ number_format((float) ($submission->percentage ?? 0), 1) }}%
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ number_format((float) ($submission->score ?? 0), 1) }} / {{ number_format((float) ($submission->total_marks ?? 0), 1) }}
                                            </div>
                                        </div>
                                        @if($submission->canViewResults())
                                            <a href="{{ route('general-exams.results.submission', ['token' => $token, 'submission' => $submission->id]) }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                                                View Result
                                            </a>
                                        @else
                                            <span class="px-3 py-1 text-xs rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">Pending release</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @if(($participant ?? null) && !($needsEmail ?? false))
        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Performance Trend Chart
                const trendData = @json($performanceTrend ?? []);
                const trendCtx = document.getElementById('performanceTrendChart')?.getContext('2d');
                
                if (trendCtx && Object.keys(trendData).length > 0) {
                    new Chart(trendCtx, {
                        type: 'line',
                        data: {
                            labels: Object.keys(trendData),
                            datasets: [{
                                label: 'Score %',
                                data: Object.values(trendData),
                                borderColor: 'rgb(99, 102, 241)',
                                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: 'rgb(99, 102, 241)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                pointHoverRadius: 7
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
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
                                            return 'Score: ' + context.parsed.y + '%';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Grade Distribution Chart
                const gradeData = @json($gradeDistribution ?? []);
                const gradeCtx = document.getElementById('gradeDistributionChart')?.getContext('2d');
                
                if (gradeCtx) {
                    new Chart(gradeCtx, {
                        type: 'bar',
                        data: {
                            labels: ['A+', 'A', 'B', 'C', 'D', 'F'],
                            datasets: [{
                                label: 'Count',
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
                }
            });
        </script>
        @endpush
    @endif
</x-layouts.general-exam>
