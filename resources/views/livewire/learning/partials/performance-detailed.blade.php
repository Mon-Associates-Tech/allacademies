<div class="performance-detailed space-y-6">
    {{-- Performance by Book --}}
    <div>
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance by Book</h3>
        @if(count($this->performanceByBook) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($this->performanceByBook as $bookPerf)
                    <div class="p-4 border border-gray-200 rounded-lg hover:border-blue-500 transition">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">{{ Str::limit($bookPerf['book_title'], 50) }}</h4>
                                <p class="text-sm text-gray-500">{{ $bookPerf['author'] }}</p>
                            </div>
                            <span class="ml-2 text-lg font-bold {{ $bookPerf['average_score'] >= 80 ? 'text-green-600' : ($bookPerf['average_score'] >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ number_format($bookPerf['average_score'], 1) }}%
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-sm text-gray-600">
                            <span>{{ $bookPerf['quiz_count'] }} {{ Str::plural('quiz', $bookPerf['quiz_count']) }}</span>
                            <span>Best: {{ number_format($bookPerf['best_score'], 1) }}%</span>
                        </div>
                        @if($bookPerf['improvement'] != 0)
                            <div class="mt-2 text-xs {{ $bookPerf['improvement'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $bookPerf['improvement'] > 0 ? '▲' : '▼' }} {{ abs($bookPerf['improvement']) }}% from first attempt
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-sm">No book data available</p>
        @endif
    </div>

    {{-- Performance by Difficulty --}}
    <div>
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance by Difficulty</h3>
        @if(count($this->performanceByDifficulty) > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($this->performanceByDifficulty as $diffPerf)
                    <div class="p-6 border border-gray-200 rounded-lg">
                        <div class="text-center">
                            <h4 class="font-semibold text-gray-900 mb-2">{{ $diffPerf['difficulty'] }}</h4>
                            <div class="text-3xl font-bold {{ $diffPerf['average_score'] >= 80 ? 'text-green-600' : ($diffPerf['average_score'] >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ number_format($diffPerf['average_score'], 1) }}%
                            </div>
                            <p class="text-sm text-gray-500 mt-2">
                                {{ $diffPerf['quiz_count'] }} {{ Str::plural('quiz', $diffPerf['quiz_count']) }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                Pass rate: {{ number_format($diffPerf['pass_rate'], 1) }}%
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-sm">No difficulty data available</p>
        @endif
    </div>

    {{-- Performance by Question Type --}}
    <div>
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance by Question Type</h3>
        @if(count($this->performanceByQuestionType) > 0)
            <div class="space-y-3">
                @foreach($this->performanceByQuestionType as $typePerf)
                    <div class="p-4 border border-gray-200 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-gray-900">{{ $typePerf['type'] }}</span>
                            <span class="text-lg font-bold {{ $typePerf['average_score'] >= 80 ? 'text-green-600' : ($typePerf['average_score'] >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ number_format($typePerf['average_score'], 1) }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="h-2.5 rounded-full {{ $typePerf['average_score'] >= 80 ? 'bg-green-600' : ($typePerf['average_score'] >= 60 ? 'bg-yellow-600' : 'bg-red-600') }}"
                                 style="width: {{ $typePerf['average_score'] }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-2">
                            <span>{{ $typePerf['quiz_count'] }} {{ Str::plural('quiz', $typePerf['quiz_count']) }}</span>
                            @if($typePerf['average_time'] > 0)
                                <span>Avg time: {{ gmdate('i:s', $typePerf['average_time']) }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-sm">No question type data available</p>
        @endif
    </div>
</div>
