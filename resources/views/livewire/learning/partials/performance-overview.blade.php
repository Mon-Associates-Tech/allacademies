<div class="performance-overview">
    {{-- Grade Distribution --}}
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Grade Distribution</h3>
        <div class="grid grid-cols-5 gap-4">
            @foreach($this->performanceData['grade_distribution'] as $grade => $count)
                <div class="text-center p-4 rounded-lg border-2 {{ $count > 0 ? 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-gray-50' }}">
                    <div class="text-3xl font-bold {{ $count > 0 ? 'text-blue-600' : 'text-gray-400' }}">
                        {{ $grade }}
                    </div>
                    <div class="text-sm text-gray-600 mt-1">
                        {{ $count }} {{ Str::plural('quiz', $count) }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Strengths and Weaknesses --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Strengths --}}
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Strengths
            </h3>
            @if(count($this->strengthsAndWeaknesses['strengths']) > 0)
                <div class="space-y-3">
                    @foreach($this->strengthsAndWeaknesses['strengths'] as $strength)
                        <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-gray-900">{{ $strength['type'] }}</span>
                                <span class="text-sm font-semibold text-green-600">{{ number_format($strength['accuracy'], 1) }}%</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ $strength['correct_answers'] }}/{{ $strength['total_questions'] }} correct
                            </p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">Complete more quizzes to identify your strengths</p>
            @endif
        </div>

        {{-- Weaknesses --}}
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Areas for Improvement
            </h3>
            @if(count($this->strengthsAndWeaknesses['weaknesses']) > 0)
                <div class="space-y-3">
                    @foreach($this->strengthsAndWeaknesses['weaknesses'] as $weakness)
                        <div class="p-4 bg-orange-50 border border-orange-200 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-gray-900">{{ $weakness['type'] }}</span>
                                <span class="text-sm font-semibold text-orange-600">{{ number_format($weakness['accuracy'], 1) }}%</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ $weakness['correct_answers'] }}/{{ $weakness['total_questions'] }} correct
                            </p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">Great job! No significant weaknesses detected</p>
            @endif
        </div>
    </div>

    {{-- Recent Quizzes --}}
    <div>
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Quizzes</h3>
        @if($this->recentQuizzes->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Difficulty</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Questions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($this->recentQuizzes as $quiz)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ Str::limit($quiz['book_title'], 40) }}</div>
                                <div class="text-xs text-gray-500">{{ $quiz['author'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $quiz['difficulty'] === 'Easy' ? 'bg-green-100 text-green-800' : ($quiz['difficulty'] === 'Hard' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ $quiz['difficulty'] }}
                                    </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $quiz['question_type'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                        <span class="text-sm font-semibold {{ $quiz['score'] >= 80 ? 'text-green-600' : ($quiz['score'] >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ number_format($quiz['score'], 1) }}%
                                        </span>
                                    <span class="ml-2 text-xs text-gray-500">({{ $quiz['grade'] }})</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $quiz['correct'] }}/{{ $quiz['questions'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $quiz['completed_at']->format('M d, Y') }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 bg-gray-50 rounded-lg">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="mt-2 text-sm text-gray-500">No quizzes found with the current filters</p>
            </div>
        @endif
    </div>
</div>
