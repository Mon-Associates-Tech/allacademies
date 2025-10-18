<div class="performance-trends" x-data="{ chartInitialized: false }">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance Over Time</h3>

    @php
        $timeSeriesData = $this->timeSeriesData;
        Log::info('Rendering trends view', [
            'has_data' => !empty($timeSeriesData),
            'count' => count($timeSeriesData),
            'data' => $timeSeriesData
        ]);
    @endphp

    @if(!empty($timeSeriesData) && count($timeSeriesData) > 0)
        <div class="mb-6">
            <canvas id="performanceChart" class="w-full" style="height: 400px;"></canvas>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($timeSeriesData as $period)
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="text-sm font-medium text-gray-600">{{ $period['period'] }}</div>
                    <div class="mt-2">
                        <span class="text-2xl font-bold text-gray-900">{{ number_format($period['average_score'], 1) }}%</span>
                        <span class="ml-2 text-sm text-gray-500">avg</span>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        {{ $period['quiz_count'] }} {{ Str::plural('quiz', $period['quiz_count']) }},
                        {{ $period['total_questions'] }} questions
                    </div>
                </div>
            @endforeach
        </div>

        <script>
            (function() {
                console.log('Trends script loaded');

                function initPerformanceChart() {
                    console.log('Initializing performance chart...');

                    const canvas = document.getElementById('performanceChart');
                    if (!canvas) {
                        console.error('Performance chart canvas not found');
                        return;
                    }
                    console.log('Canvas found:', canvas);

                    // Check if Chart.js is available
                    if (typeof Chart === 'undefined') {
                        console.error('Chart.js not loaded');
                        return;
                    }
                    console.log('Chart.js is available');

                    // Check if ChartDataHelper is available
                    if (typeof ChartDataHelper === 'undefined') {
                        console.error('ChartDataHelper not loaded, using Chart.js directly');

                        // Fallback to direct Chart.js implementation
                        const labels = @json(array_column($timeSeriesData, 'period'));
                        const scoreData = @json(array_column($timeSeriesData, 'average_score'));
                        const quizCounts = @json(array_column($timeSeriesData, 'quiz_count'));

                        console.log('Chart data:', { labels, scoreData, quizCounts });

                        // Destroy existing chart if it exists
                        if (window.performanceChartInstance) {
                            window.performanceChartInstance.destroy();
                        }

                        window.performanceChartInstance = new Chart(canvas, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Average Score (%)',
                                    data: scoreData,
                                    borderColor: 'rgb(59, 130, 246)',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    tension: 0.3,
                                    fill: true
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
                                            callback: function(value) {
                                                return value + '%';
                                            }
                                        }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                const index = context.dataIndex;
                                                const score = context.parsed.y.toFixed(1);
                                                const count = quizCounts[index];
                                                return [
                                                    `Score: ${score}%`,
                                                    `Quizzes: ${count}`
                                                ];
                                            }
                                        }
                                    }
                                }
                            }
                        });

                        console.log('Chart created successfully (direct)');
                        return;
                    }

                    // Destroy existing chart if it exists
                    if (window.performanceChartInstance) {
                        ChartDataHelper.destroyChart(window.performanceChartInstance);
                    }

                    // Prepare data
                    const labels = @json(array_column($timeSeriesData, 'period'));
                    const scoreData = @json(array_column($timeSeriesData, 'average_score'));
                    const quizCounts = @json(array_column($timeSeriesData, 'quiz_count'));

                    console.log('Chart data:', { labels, scoreData, quizCounts });

                    // Use ChartDataHelper to create line chart
                    const chartConfig = ChartDataHelper.generateLineChartData(
                        labels,
                        [
                            {
                                label: 'Average Score (%)',
                                data: scoreData,
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.3,
                                fill: true
                            }
                        ],
                        {
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
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const index = context.dataIndex;
                                            const score = context.parsed.y.toFixed(1);
                                            const count = quizCounts[index];
                                            return [
                                                `Score: ${score}%`,
                                                `Quizzes: ${count}`
                                            ];
                                        }
                                    }
                                }
                            }
                        }
                    );

                    // Create animated chart
                    try {
                        window.performanceChartInstance = ChartDataHelper.createAnimatedChart(canvas, chartConfig);
                        console.log('Performance chart created successfully');
                    } catch (error) {
                        console.error('Error creating chart:', error);
                    }
                }

                // Initialize on DOM ready
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initPerformanceChart);
                } else {
                    initPerformanceChart();
                }

                // Re-initialize on Livewire navigation
                document.addEventListener('livewire:navigated', initPerformanceChart);

                // Clean up on Livewire navigation
                document.addEventListener('livewire:navigating', function() {
                    if (window.performanceChartInstance) {
                        if (typeof ChartDataHelper !== 'undefined') {
                            ChartDataHelper.destroyChart(window.performanceChartInstance);
                        } else if (window.performanceChartInstance.destroy) {
                            window.performanceChartInstance.destroy();
                        }
                        window.performanceChartInstance = null;
                    }
                });
            })();
        </script>
    @else
        <div class="text-center py-12 bg-gray-50 rounded-lg">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <p class="mt-2 text-sm text-gray-500">No trend data available</p>
            <p class="mt-1 text-xs text-gray-400">Complete more quizzes to see performance trends</p>

            @if(config('app.debug'))
                <div class="mt-4 text-left inline-block p-4 bg-yellow-50 border border-yellow-200 rounded">
                    <p class="text-xs font-mono text-gray-600">
                        Debug Info:<br>
                        User ID: {{ $this->userId }}<br>
                        Selected Period: {{ $this->selectedPeriod }}<br>
                        Selected Book: {{ $this->selectedBookId ?? 'All' }}<br>
                        Time Series Data Count: {{ count($timeSeriesData) }}<br>
                        Check Laravel logs for more details
                    </p>
                </div>
            @endif
        </div>
    @endif
</div>
