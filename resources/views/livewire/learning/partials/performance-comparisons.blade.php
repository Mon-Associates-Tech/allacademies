<div class="performance-comparisons space-y-6">
    {{-- Question Type Comparison --}}
    <div>
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Question Type Performance Comparison</h3>
        @if(count($this->performanceByQuestionType) > 1)
            <div class="relative pt-8">
                <canvas id="typeComparisonChart" style="height: 300px;"></canvas>
            </div>

            @push('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const canvas = document.getElementById('typeComparisonChart');
                        if (!canvas) return;

                        // Prepare data
                        const labels = @json(collect($this->performanceByQuestionType)->pluck('type'));
                        const scoreData = @json(collect($this->performanceByQuestionType)->pluck('average_score'));

                        // Use ChartDataHelper to create bar chart
                        const chartConfig = ChartDataHelper.generateBarChartData(
                            labels,
                            [
                                {
                                    label: 'Average Score (%)',
                                    data: scoreData,
                                    backgroundColor: [
                                        'rgba(59, 130, 246, 0.8)',
                                        'rgba(16, 185, 129, 0.8)',
                                        'rgba(245, 158, 11, 0.8)',
                                        'rgba(239, 68, 68, 0.8)'
                                    ],
                                    borderColor: [
                                        'rgb(59, 130, 246)',
                                        'rgb(16, 185, 129)',
                                        'rgb(245, 158, 11)',
                                        'rgb(239, 68, 68)'
                                    ],
                                    borderWidth: 2
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
                                                return 'Score: ' + context.parsed.y.toFixed(1) + '%';
                                            }
                                        }
                                    }
                                }
                            }
                        );

                        // Create animated chart
                        const chart = ChartDataHelper.createAnimatedChart(canvas, chartConfig);

                        // Clean up on page navigation
                        document.addEventListener('livewire:navigate', function() {
                            ChartDataHelper.destroyChart(chart);
                        });
                    });
                </script>
            @endpush
        @else
            <p class="text-gray-500 text-sm">Complete more quizzes with different question types to see comparisons</p>
        @endif
    </div>

    {{-- Difficulty Comparison --}}
    <div>
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Difficulty Level Performance</h3>
        @if(count($this->performanceByDifficulty) > 0)
            <div class="space-y-4">
                @foreach($this->performanceByDifficulty as $index => $diff)
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $diff['difficulty'] }}</span>
                            <span class="text-sm font-semibold {{ $diff['average_score'] >= 80 ? 'text-green-600' : ($diff['average_score'] >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ number_format($diff['average_score'], 1) }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="h-3 rounded-full transition-all {{ $diff['average_score'] >= 80 ? 'bg-green-600' : ($diff['average_score'] >= 60 ? 'bg-yellow-600' : 'bg-red-600') }}"
                                 style="width: {{ $diff['average_score'] }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>{{ $diff['quiz_count'] }} quizzes</span>
                            <span>Pass rate: {{ number_format($diff['pass_rate'], 1) }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Optional: Difficulty Comparison Chart --}}
            @if(count($this->performanceByDifficulty) > 1)
                <div class="mt-6">
                    <canvas id="difficultyComparisonChart" style="height: 250px;"></canvas>
                </div>

                @push('scripts')
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const canvas = document.getElementById('difficultyComparisonChart');
                            if (!canvas) return;

                            // Prepare data
                            const labels = @json(collect($this->performanceByDifficulty)->pluck('difficulty'));
                            const scoreData = @json(collect($this->performanceByDifficulty)->pluck('average_score'));
                            const passRates = @json(collect($this->performanceByDifficulty)->pluck('pass_rate'));

                            // Use ChartDataHelper to create bar chart with multiple datasets
                            const chartConfig = ChartDataHelper.generateBarChartData(
                                labels,
                                [
                                    {
                                        label: 'Average Score (%)',
                                        data: scoreData,
                                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                                        borderColor: 'rgb(59, 130, 246)',
                                        borderWidth: 2
                                    },
                                    {
                                        label: 'Pass Rate (%)',
                                        data: passRates,
                                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                                        borderColor: 'rgb(16, 185, 129)',
                                        borderWidth: 2
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
                                            display: true,
                                            position: 'top'
                                        }
                                    }
                                }
                            );

                            // Create animated chart
                            const chart = ChartDataHelper.createAnimatedChart(canvas, chartConfig);

                            // Clean up on page navigation
                            document.addEventListener('livewire:navigate', function() {
                                ChartDataHelper.destroyChart(chart);
                            });
                        });
                    </script>
                @endpush
            @endif
        @else
            <p class="text-gray-500 text-sm">No difficulty comparison data available</p>
        @endif
    </div>

    {{-- Performance Summary --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h4 class="font-semibold text-gray-900 mb-3">Performance Insights</h4>
        <ul class="space-y-2 text-sm text-gray-700">
            @if($this->performanceData['improvement_trend']['trend'] === 'improving')
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-green-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Your performance is <strong>improving</strong> over time ({{ $this->performanceData['improvement_trend']['change'] > 0 ? '+' : '' }}{{ number_format($this->performanceData['improvement_trend']['change'], 1) }}%)</span>
                </li>
            @endif

            @if($this->performanceData['average_score'] >= 80)
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-green-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Excellent average score of <strong>{{ number_format($this->performanceData['average_score'], 1) }}%</strong> - keep up the great work!</span>
                </li>
            @endif

            @if(count($this->strengthsAndWeaknesses['strengths']) > 0)
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span>You excel at <strong>{{ $this->strengthsAndWeaknesses['strengths'][0]['type'] }}</strong> questions</span>
                </li>
            @endif

            @if(count($this->strengthsAndWeaknesses['weaknesses']) > 0)
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-orange-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>Focus on improving <strong>{{ $this->strengthsAndWeaknesses['weaknesses'][0]['type'] }}</strong> questions</span>
                </li>
            @endif

            @if($this->performanceData['total_quizzes'] >= 10)
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-purple-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                    <span>You've completed <strong>{{ $this->performanceData['total_quizzes'] }}</strong> quizzes - great dedication!</span>
                </li>
            @endif
        </ul>
    </div>
</div>
