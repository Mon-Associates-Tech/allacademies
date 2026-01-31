<div class="performance-trends">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Performance Over Time</h3>

    @php
        $timeSeriesData = $this->timeSeriesData;
    @endphp

    @if(!empty($timeSeriesData) && count($timeSeriesData) > 0)
        {{-- Trend Line Chart using Livewire Component --}}
        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 mb-6">
            @if(!empty($trendLineLabels))
                <livewire:charts.line-chart :labels="$trendLineLabels" :datasets="$trendLineDatasets" :options="$trendLineOptions" height-class="h-80" />
            @else
                <div class="flex items-center justify-center h-80 text-gray-500 dark:text-gray-400">
                    <p>Loading chart data...</p>
                </div>
            @endif
        </div>

        {{-- Period Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($timeSeriesData as $period)
                <div class="p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:shadow-md transition-shadow">
                    <div class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $period['period'] }}</div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-bold {{ $period['average_score'] >= 80 ? 'text-green-600 dark:text-green-400' : ($period['average_score'] >= 60 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">
                            {{ number_format($period['average_score'], 1) }}%
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">avg</span>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ $period['quiz_count'] }} {{ Str::plural('quiz', $period['quiz_count']) }},
                        {{ $period['total_questions'] }} questions
                    </div>
                    {{-- Mini progress bar --}}
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mt-2">
                        <div class="h-1.5 rounded-full {{ $period['average_score'] >= 80 ? 'bg-green-600' : ($period['average_score'] >= 60 ? 'bg-yellow-600' : 'bg-red-600') }}"
                             style="width: {{ $period['average_score'] }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Trend Summary --}}
        @if($this->performanceData['improvement_trend']['trend'] !== 'neutral')
            <div class="mt-6 p-4 rounded-lg {{ $this->performanceData['improvement_trend']['trend'] === 'improving' ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800' }}">
                <div class="flex items-center gap-3">
                    @if($this->performanceData['improvement_trend']['trend'] === 'improving')
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        <div>
                            <p class="font-medium text-green-800 dark:text-green-300">Performance Improving</p>
                            <p class="text-sm text-green-600 dark:text-green-400">
                                Your scores have improved by {{ number_format(abs($this->performanceData['improvement_trend']['change']), 1) }}% over this period
                            </p>
                        </div>
                    @else
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                        </svg>
                        <div>
                            <p class="font-medium text-red-800 dark:text-red-300">Performance Declining</p>
                            <p class="text-sm text-red-600 dark:text-red-400">
                                Your scores have decreased by {{ number_format(abs($this->performanceData['improvement_trend']['change']), 1) }}% over this period
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    @else
        <div class="text-center py-12 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No trend data available</p>
            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Complete more quizzes to see performance trends</p>
        </div>
    @endif
</div>
