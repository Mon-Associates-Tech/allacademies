<div class="performance-detailed space-y-6">
    {{-- Performance by Book with Chart --}}
    <div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Performance by Book</h3>
        @if(count($this->performanceByBook) > 0)
            {{-- Book Performance Bar Chart --}}
            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 mb-6">
                @if(!empty($bookBarLabels))
                    <livewire:charts.bar-chart :labels="$bookBarLabels" :datasets="$bookBarDatasets" :options="$bookBarOptions" height-class="h-72" />
                @endif
            </div>

            {{-- Book Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($this->performanceByBook as $bookPerf)
                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-blue-500 dark:hover:border-blue-400 transition bg-white dark:bg-gray-800">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900 dark:text-white">{{ Str::limit($bookPerf['book_title'], 50) }}</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $bookPerf['author'] }}</p>
                            </div>
                            <span class="ml-2 text-lg font-bold {{ $bookPerf['average_score'] >= 80 ? 'text-green-600 dark:text-green-400' : ($bookPerf['average_score'] >= 60 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">
                                {{ number_format($bookPerf['average_score'], 1) }}%
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-sm text-gray-600 dark:text-gray-400">
                            <span>{{ $bookPerf['quiz_count'] }} {{ Str::plural('quiz', $bookPerf['quiz_count']) }}</span>
                            <span>Best: {{ number_format($bookPerf['best_score'], 1) }}%</span>
                        </div>
                        @if($bookPerf['improvement'] != 0)
                            <div class="mt-2 text-xs {{ $bookPerf['improvement'] > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $bookPerf['improvement'] > 0 ? '▲' : '▼' }} {{ abs($bookPerf['improvement']) }}% from first attempt
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-400 text-sm">No book data available</p>
        @endif
    </div>

    {{-- Performance by Difficulty and Question Type with Charts --}}
    <div class="grid grid-cols-12 gap-6">
        {{-- Difficulty Distribution Pie Chart --}}
        <div class="col-span-12 lg:col-span-6">
            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quizzes by Difficulty</h3>
                @if(!empty($difficultyPieLabels))
                    <livewire:charts.pie-chart :labels="$difficultyPieLabels" :values="$difficultyPieValues" :options="$difficultyPieOptions" height-class="h-64" />
                @else
                    <div class="flex items-center justify-center h-64 text-gray-500 dark:text-gray-400">
                        <p>No difficulty data available</p>
                    </div>
                @endif
            </div>
            {{-- Difficulty Cards --}}
            @if(count($this->performanceByDifficulty) > 0)
                <div class="grid grid-cols-3 gap-4 mt-4">
                    @foreach($this->performanceByDifficulty as $diffPerf)
                        <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800">
                            <div class="text-center">
                                <h4 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $diffPerf['difficulty'] }}</h4>
                                <div class="text-2xl font-bold {{ $diffPerf['average_score'] >= 80 ? 'text-green-600 dark:text-green-400' : ($diffPerf['average_score'] >= 60 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">
                                    {{ number_format($diffPerf['average_score'], 1) }}%
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $diffPerf['quiz_count'] }} {{ Str::plural('quiz', $diffPerf['quiz_count']) }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Question Type Distribution Pie Chart --}}
        <div class="col-span-12 lg:col-span-6">
            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quizzes by Question Type</h3>
                @if(!empty($typePieLabels))
                    <livewire:charts.pie-chart :labels="$typePieLabels" :values="$typePieValues" :options="$typePieOptions" height-class="h-64" />
                @else
                    <div class="flex items-center justify-center h-64 text-gray-500 dark:text-gray-400">
                        <p>No question type data available</p>
                    </div>
                @endif
            </div>
            {{-- Question Type Cards --}}
            @if(count($this->performanceByQuestionType) > 0)
                <div class="space-y-3 mt-4">
                    @foreach($this->performanceByQuestionType as $typePerf)
                        <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-medium text-gray-900 dark:text-white">{{ $typePerf['type'] }}</span>
                                <span class="text-lg font-bold {{ $typePerf['average_score'] >= 80 ? 'text-green-600 dark:text-green-400' : ($typePerf['average_score'] >= 60 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">
                                    {{ number_format($typePerf['average_score'], 1) }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                <div class="h-2.5 rounded-full {{ $typePerf['average_score'] >= 80 ? 'bg-green-600' : ($typePerf['average_score'] >= 60 ? 'bg-yellow-600' : 'bg-red-600') }}"
                                     style="width: {{ $typePerf['average_score'] }}%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-2">
                                <span>{{ $typePerf['quiz_count'] }} {{ Str::plural('quiz', $typePerf['quiz_count']) }}</span>
                                @if($typePerf['average_time'] > 0)
                                    <span>Avg time: {{ gmdate('i:s', $typePerf['average_time']) }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
