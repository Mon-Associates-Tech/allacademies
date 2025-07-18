<div class="max-w-4xl mx-auto space-y-8">
    <!-- Results Header -->
    <div class="bg-gradient-to-r from-green-500 via-emerald-600 to-teal-600 rounded-xl p-8 text-white shadow-lg relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>

        <div class="relative text-center">
            <div class="mb-4">
                @if($result['percentage_score'] >= 90)
                    <div class="text-6xl mb-2">🏆</div>
                    <h1 class="text-3xl font-bold">Excellent Work!</h1>
                @elseif($result['percentage_score'] >= 80)
                    <div class="text-6xl mb-2">🎉</div>
                    <h1 class="text-3xl font-bold">Great Job!</h1>
                @elseif($result['percentage_score'] >= 70)
                    <div class="text-6xl mb-2">👍</div>
                    <h1 class="text-3xl font-bold">Good Work!</h1>
                @elseif($result['percentage_score'] >= 60)
                    <div class="text-6xl mb-2">📚</div>
                    <h1 class="text-3xl font-bold">Keep Practicing!</h1>
                @else
                    <div class="text-6xl mb-2">💪</div>
                    <h1 class="text-3xl font-bold">Room for Improvement!</h1>
                @endif
            </div>

            <div class="flex items-center justify-center space-x-8">
                <div class="text-center">
                    <div class="text-4xl font-bold">{{ number_format($result['percentage_score'], 1) }}%</div>
                    <div class="text-emerald-100">Your Score</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold">{{ $result['correct_count'] }}/{{ $result['total_questions'] }}</div>
                    <div class="text-emerald-100">Correct Answers</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold">{{ $result['answered_count'] }}/{{ $result['total_questions'] }}</div>
                    <div class="text-emerald-100">Answered</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Score Breakdown -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Overall Performance -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Performance</h3>
            </div>

            <!-- Score Circle -->
            <div class="flex items-center justify-center mb-4">
                <div class="relative w-24 h-24">
                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="45" stroke="#e5e7eb" stroke-width="8" fill="none" class="dark:stroke-gray-600"/>
                        <circle cx="50" cy="50" r="45" stroke="{{ $result['percentage_score'] >= 80 ? '#10b981' : ($result['percentage_score'] >= 60 ? '#f59e0b' : '#ef4444') }}"
                                stroke-width="8" fill="none" stroke-linecap="round"
                                stroke-dasharray="{{ 2 * pi() * 45 }}"
                                stroke-dashoffset="{{ 2 * pi() * 45 * (1 - $result['percentage_score'] / 100) }}"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-xl font-bold {{ $result['percentage_score'] >= 80 ? 'text-green-600' : ($result['percentage_score'] >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ number_format($result['percentage_score'], 1) }}%
                        </span>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    @if($result['percentage_score'] >= 90)
                        Outstanding performance!
                    @elseif($result['percentage_score'] >= 80)
                        Excellent work!
                    @elseif($result['percentage_score'] >= 70)
                        Good job!
                    @elseif($result['percentage_score'] >= 60)
                        Fair performance.
                    @else
                        Needs improvement.
                    @endif
                </p>
            </div>
        </div>

        <!-- Question Types Breakdown -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Question Types</h3>
            </div>

            <div class="space-y-3">
                @if(isset($result['byType']['multiple_choice']) && $result['byType']['multiple_choice']['total_count'] > 0)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600 dark:text-gray-400">Multiple Choice</span>
                            <span class="font-medium">{{ $result['byType']['multiple_choice']['correct_count'] }}/{{ $result['byType']['multiple_choice']['total_count'] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $result['byType']['multiple_choice']['total_count'] > 0 ? ($result['byType']['multiple_choice']['correct_count'] / $result['byType']['multiple_choice']['total_count']) * 100 : 0 }}%"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            Score: {{ $result['byType']['multiple_choice']['score'] }}/{{ $result['byType']['multiple_choice']['max_score'] }}
                        </div>
                    </div>
                @endif

                @if(isset($result['byType']['true_false']) && $result['byType']['true_false']['total_count'] > 0)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600 dark:text-gray-400">True/False</span>
                            <span class="font-medium">{{ $result['byType']['true_false']['correct_count'] }}/{{ $result['byType']['true_false']['total_count'] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ $result['byType']['true_false']['total_count'] > 0 ? ($result['byType']['true_false']['correct_count'] / $result['byType']['true_false']['total_count']) * 100 : 0 }}%"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            Score: {{ $result['byType']['true_false']['score'] }}/{{ $result['byType']['true_false']['max_score'] }}
                        </div>
                    </div>
                @endif

                @if(isset($result['byType']['essay']) && $result['byType']['essay']['total_count'] > 0)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600 dark:text-gray-400">Essay</span>
                            <span class="font-medium">{{ $result['byType']['essay']['correct_count'] }}/{{ $result['byType']['essay']['total_count'] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $result['byType']['essay']['total_count'] > 0 ? ($result['byType']['essay']['correct_count'] / $result['byType']['essay']['total_count']) * 100 : 0 }}%"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            Score: {{ $result['byType']['essay']['score'] }}/{{ $result['byType']['essay']['max_score'] }}
                        </div>
                    </div>
                @endif

                @if((!isset($result['byType']['multiple_choice']) || $result['byType']['multiple_choice']['total_count'] == 0) &&
                    (!isset($result['byType']['true_false']) || $result['byType']['true_false']['total_count'] == 0) &&
                    (!isset($result['byType']['essay']) || $result['byType']['essay']['total_count'] == 0))
                    <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                        <p>No questions answered yet</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Score Summary -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Score Summary</h3>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Total Score:</span>
                    <span class="font-medium">{{ $result['total_score'] }}/{{ $result['max_score'] }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Percentage:</span>
                    <span class="font-medium">{{ number_format($result['percentage_score'], 1) }}%</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Questions Answered:</span>
                    <span class="font-medium">{{ $result['answered_count'] }}/{{ $result['total_questions'] }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Correct Answers:</span>
                    <span class="font-medium">{{ $result['correct_count'] }}/{{ $result['total_questions'] }}</span>
                </div>

                @if($result['answered_count'] < $result['total_questions'])
                    <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                        <p class="text-sm text-yellow-800 dark:text-yellow-200">
                            <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $result['total_questions'] - $result['answered_count'] }} question(s) not answered
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-center space-x-4">
        <button wire:click="retakeAssessment"
                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Retake Assessment
        </button>

        <button wire:click="startNewAssessment"
                class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            New Assessment
        </button>
    </div>
</div>
