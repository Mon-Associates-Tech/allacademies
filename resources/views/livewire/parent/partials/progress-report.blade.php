<!-- Progress Overview -->
<div class="mb-8">
    <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-6">Academic Progress Overview</h4>

    @if($data['progress_data']->isNotEmpty())
        <div class="space-y-6">
            @foreach($data['progress_data'] as $subjectProgress)
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <!-- Subject Header -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-violet-100 dark:bg-violet-900/20 rounded-lg">
                                <svg class="w-6 h-6 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <div>
                                <h5 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $subjectProgress['subject']->name }}
                                </h5>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $subjectProgress['progress_points']->count() }} assessments tracked
                                </p>
                            </div>
                        </div>

                        <!-- Trend Indicator -->
                        <div class="flex items-center space-x-2">
                            @if($subjectProgress['trend'] === 'improving')
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                                <span class="text-sm font-medium text-green-600">Improving</span>
                            @elseif($subjectProgress['trend'] === 'declining')
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                                </svg>
                                <span class="text-sm font-medium text-red-600">Declining</span>
                            @else
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                                <span class="text-sm font-medium text-yellow-600">Stable</span>
                            @endif
                        </div>
                    </div>

                    <!-- Improvement Badge -->
                    @if(abs($subjectProgress['improvement']) > 0)
                        <div class="mb-4 p-3 rounded-lg {{ $subjectProgress['improvement'] > 0 ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 {{ $subjectProgress['improvement'] > 0 ? 'text-green-600' : 'text-red-600' }} mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 {{ $subjectProgress['improvement'] > 0 ? '7h8m0 0v8m0-8l-8 8-4-4-6 6' : '17h8m0 0V9m0 8l-8-8-4 4-6-6' }}"/>
                                </svg>
                                <span class="text-sm font-medium {{ $subjectProgress['improvement'] > 0 ? 'text-green-800 dark:text-green-200' : 'text-red-800 dark:text-red-200' }}">
                                    {{ $subjectProgress['improvement'] > 0 ? '+' : '' }}{{ number_format($subjectProgress['improvement'], 1) }}%
                                    {{ $subjectProgress['improvement'] > 0 ? 'improvement' : 'decline' }} from first to latest assessment
                                </span>
                            </div>
                        </div>
                    @endif

                    <!-- Progress Timeline -->
                    <div class="space-y-3">
                        @foreach($subjectProgress['progress_points'] as $index => $point)
                            <div class="flex items-center">
                                <!-- Timeline Line -->
                                <div class="flex flex-col items-center mr-4">
                                    <div class="w-3 h-3 rounded-full {{ $point['score'] >= 75 ? 'bg-green-500' : ($point['score'] >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"></div>
                                    @if(!$loop->last)
                                        <div class="w-0.5 h-12 bg-gray-300 dark:bg-gray-600"></div>
                                    @endif
                                </div>

                                <!-- Progress Item -->
                                <div class="flex-1 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <h6 class="font-medium text-gray-900 dark:text-gray-100">{{ $point['title'] }}</h6>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ \Carbon\Carbon::parse($point['date'])->format('M d, Y') }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-lg font-bold {{ $point['score'] >= 75 ? 'text-green-600' : ($point['score'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                                {{ number_format($point['score'], 1) }}%
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Progress Bar -->
                                    <div class="mt-2 w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                        <div class="h-2 rounded-full {{ $point['score'] >= 75 ? 'bg-green-500' : ($point['score'] >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                             style="width: {{ $point['score'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <h4 class="mt-4 text-lg font-semibold text-gray-900 dark:text-gray-100">No Progress Data Available</h4>
            <p class="mt-2 text-gray-600 dark:text-gray-400">There are no assessments in the selected period.</p>
        </div>
    @endif
</div>
