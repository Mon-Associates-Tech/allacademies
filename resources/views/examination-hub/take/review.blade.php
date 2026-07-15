<x-layouts.exam>
    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header --}}
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Review Your Answers</h1>
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                            Review all your answers before final submission. You can go back to modify any response.
                        </p>
                    </div>
                    <a href="{{ route('examination-hub.take.section', [$exam, 0]) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Exam
                    </a>
                </div>
            </div>

            {{-- Summary Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Questions</p>
                            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $totalQuestions }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/30">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider">Answered</p>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $answeredQuestions }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 flex items-center justify-center rounded-lg {{ $unansweredQuestions > 0 ? 'bg-red-100 dark:bg-red-900/30' : 'bg-gray-100 dark:bg-gray-800' }}">
                            <svg class="w-6 h-6 {{ $unansweredQuestions > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider">Unanswered</p>
                            <p class="text-2xl font-bold {{ $unansweredQuestions > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400' }}">{{ $unansweredQuestions }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($unansweredQuestions > 0)
            {{-- Warning Banner --}}
            <div class="mb-6 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">You have {{ $unansweredQuestions }} unanswered question(s)</p>
                        <p class="text-xs text-amber-700 dark:text-amber-400 mt-1">
                            Unanswered questions will receive zero marks. Please review and answer all questions before submitting.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Questions Review by Section --}}
            <div class="space-y-6">
                @foreach($reviewData as $section)
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg overflow-hidden">
                    {{-- Section Header --}}
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
                            Section {{ $section['index'] + 1 }}: {{ $section['title'] }}
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            {{ count($section['questions']) }} questions
                        </p>
                    </div>

                    {{-- Questions List --}}
                    <div class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach($section['questions'] as $question)
                        <div class="px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            <div class="flex items-start gap-4">
                                {{-- Question Number & Status --}}
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 flex items-center justify-center rounded-full {{ $question['is_answered'] ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30' }}">
                                        <span class="text-sm font-bold {{ $question['is_answered'] ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $loop->parent->iteration }}.{{ $loop->iteration }}
                                        </span>
                                    </div>
                                    @if($question['is_flagged'])
                                    <div class="mt-1 text-center">
                                        <svg class="w-4 h-4 text-amber-500 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V9a1 1 0 011-1h3a1 1 0 001-1V6z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    @endif
                                </div>

                                {{-- Question Content --}}
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-900 dark:text-white mb-2">
                                        {{ Str::limit($question['question_text'], 150) }}
                                    </p>
                                    
                                    @if($question['is_answered'])
                                        <div class="mt-2 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                            <p class="text-xs font-semibold text-green-700 dark:text-green-400 mb-1">Your Answer:</p>
                                            <p class="text-sm text-slate-700 dark:text-slate-300">
                                                {{ Str::limit($question['response'], 200) }}
                                            </p>
                                        </div>
                                    @else
                                        <div class="mt-2 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                            <p class="text-xs font-semibold text-red-700 dark:text-red-400">
                                                ⚠️ Not answered - will receive 0 marks
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                {{-- Action Button --}}
                                <div class="flex-shrink-0">
                                    <a href="{{ route('examination-hub.take.section', [$exam, $section['index']]) }}#question-{{ $question['id'] }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Final Submission Actions --}}
            <div class="mt-8 sticky bottom-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-900 dark:text-white">Ready to submit?</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            Once submitted, you cannot modify your answers.
                        </p>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <a href="{{ route('examination-hub.take.section', [$exam, 0]) }}"
                           class="px-6 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                            Go Back
                        </a>
                        
                        <form action="{{ route('examination-hub.take.submit', $exam) }}" method="POST" onsubmit="return confirmSubmission();">
                            @csrf
                            <button type="submit"
                                    class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 rounded-lg shadow-md hover:shadow-lg transition-all transform hover:scale-[1.02]">
                                Submit Exam
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Reuse the confirmSubmission function from section view
        function confirmSubmission() {
            const totalQuestions = {{ $totalQuestions }};
            const answeredQuestions = {{ $answeredQuestions }};
            
            if (answeredQuestions === 0) {
                return confirm('⚠️ WARNING: You haven\'t answered any questions!\n\nAre you sure you want to submit an empty exam? This cannot be undone.');
            }
            
            if (answeredQuestions < totalQuestions * 0.5) {
                return confirm(`⚠️ You've only answered ${answeredQuestions} of ${totalQuestions} questions.\n\nAre you sure you want to submit?`);
            }
            
            return confirm('Are you sure you want to submit your exam? This action cannot be undone.');
        }
    </script>
</x-layouts.exam>
