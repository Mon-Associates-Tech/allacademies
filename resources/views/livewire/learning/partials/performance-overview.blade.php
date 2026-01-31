<div class="performance-overview">
    {{-- Grade Distribution Chart - Full Row --}}
    <div class="mb-8">
        <div class="col-span-12">
            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Grade Distribution</h3>
                @if(!empty($gradeBarLabels))
                    <livewire:charts.bar-chart :labels="$gradeBarLabels" :datasets="$gradeBarDatasets" :options="$gradeBarOptions" height-class="h-64" />
                @else
                    <div class="flex items-center justify-center h-64 text-gray-500 dark:text-gray-400">
                        <p>No grade data available yet</p>
                    </div>
                @endif
            </div>
            {{-- Grade Cards --}}
            @php
                $allGrades = \App\Support\GradingSystemResolver::getAllGrades($this->targetUser);
                $gradeInterpretations = collect($allGrades)->keyBy('grade')->map(fn($g) => $g['interpretation'])->toArray();
            @endphp
            <div class="grid grid-cols-3 md:grid-cols-5 lg:grid-cols-9 gap-3 mt-4">
                @foreach($this->performanceData['grade_distribution'] as $grade => $count)
                    <div class="text-center p-3 rounded-lg border-2 {{ $count > 0 ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/30 dark:border-blue-600' : 'border-gray-200 bg-gray-50 dark:bg-gray-800 dark:border-gray-700' }}">
                        <div class="text-2xl font-bold {{ $count > 0 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}">
                            {{ $grade }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate" title="{{ $gradeInterpretations[$grade] ?? '' }}">
                            {{ $gradeInterpretations[$grade] ?? '' }}
                        </div>
                        <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">
                            {{ $count }} {{ Str::plural('quiz', $count) }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Completion Rate Gauge - Full Row --}}
    <div class="mb-8">
        <div class="col-span-12">
            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Completion Rate</h3>
                @if(!empty($completionGaugeThresholds))
                    <div class="flex items-center justify-center">
                        <livewire:charts.gauge-chart
                            :value="$completionGaugeValue"
                            :min="$completionGaugeMin"
                            :max="$completionGaugeMax"
                            :thresholds="$completionGaugeThresholds"
                            center-label="Complete"
                            height-class="h-48"
                        />
                    </div>
                    <p class="text-center text-sm text-gray-600 dark:text-gray-400 mt-2">
                        {{ number_format($completionGaugeValue, 1) }}% of started quizzes completed
                    </p>
                @else
                    <div class="flex items-center justify-center h-48 text-gray-500 dark:text-gray-400">
                        <p>No completion data available yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Strengths and Weaknesses --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Strengths --}}
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Strengths
            </h3>
            @if(count($this->strengthsAndWeaknesses['strengths']) > 0)
                <div class="space-y-3">
                    @foreach($this->strengthsAndWeaknesses['strengths'] as $strength)
                        <div class="p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-gray-900 dark:text-white">{{ $strength['type'] }}</span>
                                <span class="text-sm font-semibold text-green-600 dark:text-green-400">{{ number_format($strength['accuracy'], 1) }}%</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ $strength['correct_answers'] }}/{{ $strength['total_questions'] }} correct
                            </p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-sm">Complete more quizzes to identify your strengths</p>
            @endif
        </div>

        {{-- Weaknesses --}}
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 text-orange-600 dark:text-orange-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Areas for Improvement
            </h3>
            @if(count($this->strengthsAndWeaknesses['weaknesses']) > 0)
                <div class="space-y-3">
                    @foreach($this->strengthsAndWeaknesses['weaknesses'] as $weakness)
                        <div class="p-4 bg-orange-50 dark:bg-orange-900/30 border border-orange-200 dark:border-orange-800 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-gray-900 dark:text-white">{{ $weakness['type'] }}</span>
                                <span class="text-sm font-semibold text-orange-600 dark:text-orange-400">{{ number_format($weakness['accuracy'], 1) }}%</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ $weakness['correct_answers'] }}/{{ $weakness['total_questions'] }} correct
                            </p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-sm">Great job! No significant weaknesses detected</p>
            @endif
        </div>
    </div>

    {{-- Recent Quizzes --}}
    <div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Quizzes</h3>
        @if($this->recentQuizzes->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Book</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Difficulty</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Score</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Questions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($this->recentQuizzes as $quiz)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ Str::limit($quiz['book_title'], 40) }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $quiz['author'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $quiz['difficulty'] === 'Easy' ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' : ($quiz['difficulty'] === 'Hard' ? 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300') }}">
                                        {{ $quiz['difficulty'] }}
                                    </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $quiz['question_type'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $quizGradeInfo = $this->getGrade($quiz['score']);
                                @endphp
                                <div class="flex flex-col">
                                    <div class="flex items-center">
                                        <span class="text-sm font-semibold {{ $quizGradeInfo['is_passing'] ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ number_format($quiz['score'], 1) }}%
                                        </span>
                                        <span class="ml-2 px-2 py-0.5 text-xs font-medium rounded {{ $quizGradeInfo['is_passing'] ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300' }}">
                                            {{ $quizGradeInfo['grade'] }}
                                        </span>
                                    </div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $quizGradeInfo['interpretation'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $quiz['correct'] }}/{{ $quiz['questions'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $quiz['completed_at']->format('M d, Y') }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No quizzes found with the current filters</p>
            </div>
        @endif
    </div>
</div>
