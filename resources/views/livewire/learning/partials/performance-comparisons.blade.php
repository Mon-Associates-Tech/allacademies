<div class="performance-comparisons space-y-6">
    {{-- Question Type Comparison with Livewire Chart --}}
    <div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Question Type Performance Comparison</h3>
        @if(count($this->performanceByQuestionType) > 1)
            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4">
                @if(!empty($typePieLabels))
                    <livewire:charts.bar-chart
                        :labels="$typePieLabels"
                        :datasets="[['label' => 'Avg Score %', 'data' => collect($this->performanceByQuestionType)->pluck('average_score')->toArray(), 'backgroundColor' => ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']]]"
                        :options="['plugins' => ['legend' => ['display' => false]], 'scales' => ['y' => ['beginAtZero' => true, 'max' => 100]]]"
                        height-class="h-64"
                    />
                @else
                    <div class="flex items-center justify-center h-64 text-gray-500 dark:text-gray-400">
                        <p>No question type data available</p>
                    </div>
                @endif
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-400 text-sm">Complete more quizzes with different question types to see comparisons</p>
        @endif
    </div>

    {{-- Difficulty Comparison with Livewire Chart --}}
    <div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Difficulty Level Performance</h3>
        @if(count($this->performanceByDifficulty) > 0)
            {{-- Difficulty Bar Chart --}}
            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 mb-4">
                @if(!empty($difficultyPieLabels))
                    <livewire:charts.bar-chart
                        :labels="$difficultyPieLabels"
                        :datasets="[
                            ['label' => 'Avg Score %', 'data' => collect($this->performanceByDifficulty)->pluck('average_score')->toArray(), 'backgroundColor' => '#3b82f6'],
                            ['label' => 'Pass Rate %', 'data' => collect($this->performanceByDifficulty)->pluck('pass_rate')->toArray(), 'backgroundColor' => '#10b981']
                        ]"
                        :options="['plugins' => ['legend' => ['display' => true, 'position' => 'bottom']], 'scales' => ['y' => ['beginAtZero' => true, 'max' => 100]]]"
                        height-class="h-64"
                    />
                @endif
            </div>

            {{-- Difficulty Progress Bars --}}
            <div class="space-y-4">
                @foreach($this->performanceByDifficulty as $index => $diff)
                    <div class="p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $diff['difficulty'] }}</span>
                            <span class="text-lg font-semibold {{ $diff['average_score'] >= 80 ? 'text-green-600 dark:text-green-400' : ($diff['average_score'] >= 60 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">
                                {{ number_format($diff['average_score'], 1) }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                            <div class="h-3 rounded-full transition-all {{ $diff['average_score'] >= 80 ? 'bg-green-600' : ($diff['average_score'] >= 60 ? 'bg-yellow-600' : 'bg-red-600') }}"
                                 style="width: {{ $diff['average_score'] }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-2">
                            <span>{{ $diff['quiz_count'] }} quizzes</span>
                            <span>Pass rate: {{ number_format($diff['pass_rate'], 1) }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-400 text-sm">No difficulty comparison data available</p>
        @endif
    </div>

    {{-- Performance Summary --}}
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
        <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Performance Insights</h4>
        <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
            @if($this->performanceData['improvement_trend']['trend'] === 'improving')
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Your performance is <strong>improving</strong> over time ({{ $this->performanceData['improvement_trend']['change'] > 0 ? '+' : '' }}{{ number_format($this->performanceData['improvement_trend']['change'], 1) }}%)</span>
                </li>
            @endif

            @if($this->performanceData['average_score'] >= 80)
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Excellent average score of <strong>{{ number_format($this->performanceData['average_score'], 1) }}%</strong> - keep up the great work!</span>
                </li>
            @endif

            @if(count($this->strengthsAndWeaknesses['strengths']) > 0)
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span>You excel at <strong>{{ $this->strengthsAndWeaknesses['strengths'][0]['type'] }}</strong> questions</span>
                </li>
            @endif

            @if(count($this->strengthsAndWeaknesses['weaknesses']) > 0)
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>Focus on improving <strong>{{ $this->strengthsAndWeaknesses['weaknesses'][0]['type'] }}</strong> questions</span>
                </li>
            @endif

            @if($this->performanceData['total_quizzes'] >= 10)
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                    <span>You've completed <strong>{{ $this->performanceData['total_quizzes'] }}</strong> quizzes - great dedication!</span>
                </li>
            @endif

            @if($this->performanceData['total_quizzes'] == 0)
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Start taking quizzes to see your performance insights!</span>
                </li>
            @endif
        </ul>
    </div>
</div>
