<div class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-800">
    @if(!$isFinished)
        {{-- Header: Quiz Title and Timer --}}
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $quizData['title'] }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Section {{ $currentSectionIndex + 1 }} of {{ count($quizData['sections']) }}:
                    <span class="font-medium">{{ $quizData['sections'][$this->currentSectionIndex]['title'] }}</span>
                </p>
            </div>
            @if($timeLeft !== null || $totalTimeLeft !== null)
                <div class="flex flex-col items-end gap-2">
                    @if($totalTimeLeft !== null)
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold uppercase text-gray-500 dark:text-gray-400">Total Time</span>
                            <div class="text-sm font-mono font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-md border border-indigo-200" wire:poll.1s="tick">
                                {{ floor($totalTimeLeft / 60) }}:{{ str_pad($totalTimeLeft % 60, 2, '0', STR_PAD_LEFT) }}
                            </div>
                        </div>
                    @endif
                    @if($timeLeft !== null)
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold uppercase text-red-500">Section Time</span>
                            <div class="text-xl font-mono font-bold text-red-600 bg-red-50 px-4 py-2 rounded-lg border border-red-200" @if($totalTimeLeft === null) wire:poll.1s="tick" @endif>
                                {{ floor($timeLeft / 60) }}:{{ str_pad($timeLeft % 60, 2, '0', STR_PAD_LEFT) }}
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Section Instructions --}}
        @if(!empty($quizData['sections'][$currentSectionIndex]['instructions']) && $currentQuestionIndex === 0)
            <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700 dark:bg-blue-900 dark:text-blue-200">
                <h3 class="font-bold mb-1">Section Instructions:</h3>
                <p>{{ $quizData['sections'][$currentSectionIndex]['instructions'] }}</p>
            </div>
        @endif

        {{-- Question Display --}}
        @php
            $currentQuestion = $quizData['sections'][$currentSectionIndex]['questions'][$currentQuestionIndex];
            $questionNumber = $currentQuestionIndex + 1;
            // Cumulative question number could be calculated if needed
        @endphp

        <div class="mb-8" wire:key="q-{{ $currentSectionIndex }}-{{ $currentQuestionIndex }}">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                    Question {{ $questionNumber }}:
                </h3>
                <span class="bg-gray-100 text-gray-600 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-400">
                    {{ $currentQuestion['points'] }} Marks
                </span>
            </div>

            <p class="text-xl mb-6 text-gray-900 dark:text-white">{{ $currentQuestion['text'] }}</p>

            {{-- Multiple Choice (A-E) --}}
            @if($currentQuestion['type'] === 'multiple_choice')
                <div class="space-y-3">
                    @foreach($currentQuestion['options'] as $key => $option)
                        <label class="flex items-center p-4 border rounded-lg cursor-pointer transition-colors hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700 {{ ($responses[$currentSectionIndex][$currentQuestionIndex] === $key) ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30' : '' }}">
                            <input type="radio"
                                   wire:model="responses.{{ $currentSectionIndex }}.{{ $currentQuestionIndex }}"
                                   value="{{ $key }}"
                                   class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                            <span class="ml-3 text-gray-700 dark:text-gray-300">
                                <span class="font-bold mr-1">{{ $key }}.</span> {{ $option }}
                            </span>
                        </label>
                    @endforeach
                </div>

            {{-- True/False --}}
            @elseif($currentQuestion['type'] === 'true_false')
                <div class="flex gap-4">
                    @foreach(['True', 'False'] as $option)
                        <label class="flex-1 flex items-center p-4 border rounded-lg cursor-pointer transition-colors hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700 {{ ($responses[$currentSectionIndex][$currentQuestionIndex] === $option) ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30' : '' }}">
                            <input type="radio"
                                   wire:model="responses.{{ $currentSectionIndex }}.{{ $currentQuestionIndex }}"
                                   value="{{ $option }}"
                                   class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                            <span class="ml-3 text-gray-700 dark:text-gray-300 font-medium">{{ $option }}</span>
                        </label>
                    @endforeach
                </div>

            {{-- Essay / Short Answer --}}
            @elseif(in_array($currentQuestion['type'], ['essay', 'short_answer']))
                <textarea
                    wire:model.blur="responses.{{ $currentSectionIndex }}.{{ $currentQuestionIndex }}"
                    rows="6"
                    placeholder="Type your answer here..."
                    class="w-full p-4 border rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-white"
                ></textarea>
            @endif
        </div>

        {{-- Navigation Controls --}}
        <div class="flex justify-between items-center border-t pt-6">
            <button wire:click="previousQuestion"
                    @if($currentSectionIndex === 0 && $currentQuestionIndex === 0) disabled @endif
                    class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 disabled:opacity-50 transition-colors">
                Previous
            </button>

            <div class="flex gap-2">
                @if($currentSectionIndex === count($quizData['sections']) - 1 && $currentQuestionIndex === count($quizData['sections'][$currentSectionIndex]['questions']) - 1)
                    <button wire:click="finish"
                            class="px-8 py-2 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 shadow-lg transition-all">
                        Finish & Submit
                    </button>
                @else
                    <button wire:click="nextQuestion"
                            class="px-8 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 shadow-lg transition-all">
                        Next
                    </button>
                @endif
            </div>
        </div>

    @else
        {{-- Results Display --}}
        <div class="space-y-8">
            <div class="text-center p-8 bg-indigo-50 dark:bg-indigo-900/20 rounded-2xl border border-indigo-100 dark:border-indigo-800">
                <h2 class="text-3xl font-extrabold text-indigo-900 dark:text-indigo-100 mb-2">Quiz Completed!</h2>
                <div class="text-6xl font-black text-indigo-600 dark:text-indigo-400 mb-4">
                    {{ $results['total_earned'] }} / {{ $results['total_possible'] }}
                </div>
                <div class="inline-block px-4 py-2 bg-white dark:bg-gray-800 rounded-full text-lg font-bold text-gray-700 dark:text-gray-300 border border-indigo-200 dark:border-indigo-700">
                    Score: {{ $results['percentage'] }}%
                </div>
            </div>

            <div class="space-y-6">
                @foreach($results['sections'] as $sIndex => $sectionResult)
                    <div class="border rounded-xl overflow-hidden dark:border-gray-700">
                        <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex justify-between items-center border-b dark:border-gray-600">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $sectionResult['title'] }}</h3>
                            <span class="px-3 py-1 bg-white dark:bg-gray-800 rounded-lg font-bold text-gray-700 dark:text-gray-300">
                                {{ $sectionResult['earned_marks'] }} / {{ $sectionResult['possible_marks'] }}
                            </span>
                        </div>

                        <div class="p-6 space-y-6">
                            @foreach($sectionResult['questions'] as $qIndex => $qResult)
                                <div class="pb-6 border-b last:border-0 dark:border-gray-700">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-bold text-gray-800 dark:text-gray-200">Question {{ $loop->iteration }}: {{ $qResult['question'] }}</h4>
                                        <div class="flex flex-col items-end">
                                            <span class="text-sm font-bold {{ $qResult['earned_marks'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $qResult['earned_marks'] }} / {{ $qResult['possible_marks'] }} Marks
                                            </span>
                                            @if($qResult['status'] === 'pending')
                                                <span class="text-[10px] uppercase font-bold text-yellow-600 px-1.5 py-0.5 bg-yellow-50 rounded mt-1">Pending Review</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 text-sm">
                                        <div class="p-3 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                            <span class="block text-xs font-bold text-gray-500 uppercase mb-1">Your Answer</span>
                                            <span class="text-gray-900 dark:text-white">{{ $qResult['response'] ?? '(No answer)' }}</span>
                                        </div>
                                        @if(isset($qResult['correct_answer']))
                                        <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                            <span class="block text-xs font-bold text-green-600 uppercase mb-1">Correct Answer</span>
                                            <span class="text-green-900 dark:text-green-100 font-medium">{{ $qResult['correct_answer'] }}</span>
                                        </div>
                                        @endif
                                    </div>

                                    @if($qResult['feedback'])
                                        <div class="mt-3 p-3 text-sm italic text-gray-600 dark:text-gray-400 border-l-2 border-gray-300 dark:border-gray-600">
                                            Feedback: {{ $qResult['feedback'] }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-center pt-6">
                <button onclick="window.location.reload()" class="px-8 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-lg">
                    Back to Quizzes
                </button>
            </div>
        </div>
    @endif
</div>
