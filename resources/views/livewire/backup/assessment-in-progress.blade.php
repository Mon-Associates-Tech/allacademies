<div class="max-w-4xl mx-auto space-y-6">
    <!-- Progress Header -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Assessment in Progress</h1>
                <p class="text-indigo-100 mt-1">Question {{ $currentQuestionIndex + 1 }} of {{ count($questions) }}</p>
            </div>

            <!-- Timer -->
            @if($timeRemaining)
                <div class="text-center bg-white/10 backdrop-blur-sm rounded-xl p-4">
                    <div class="text-2xl font-bold" id="timer">{{ gmdate('H:i:s', $timeRemaining) }}</div>
                    <div class="text-sm text-indigo-200">Time Remaining</div>
                </div>
            @endif
        </div>

        <!-- Progress Bar -->
        <div class="mt-4">
            <div class="bg-white/20 rounded-full h-3">
                <div class="bg-white rounded-full h-3 transition-all duration-300"
                     style="width: {{ (($currentQuestionIndex + 1) / count($questions)) * 100 }}%"></div>
            </div>
            <div class="flex justify-between text-sm text-indigo-200 mt-1">
                <span>Progress</span>
                <span>{{ round((($currentQuestionIndex + 1) / count($questions)) * 100) }}%</span>
            </div>
        </div>
    </div>

    @if(isset($questions[$currentQuestionIndex]))
        @php $currentQuestion = $questions[$currentQuestionIndex]; @endphp


            <!-- Question Card -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-8 space-y-6">
                <!-- Question Header -->
                <div class="flex items-start space-x-4">
                    <div
                        class="flex-shrink-0 w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center">
                        <span
                            class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{{ $currentQuestionIndex + 1 }}</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-2">
                            @if($currentQuestion['type'] === 'multiple_choice_question')
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    🔘 Multiple Choice
                                </span>
                            @elseif($currentQuestion['type'] === 'true_or_false_question')
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    ✅ True/False
                                </span>
                            @elseif($currentQuestion['type'] === 'essay_question')
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                    📝 Essay
                                </span>
                            @endif

                            @if($currentQuestion['model']->difficulty_level ?? false)
                                @php
                                    $difficultyColors = [
                                        'easy' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                        'medium' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                        'hard' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                    ];
                                @endphp
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $difficultyColors[$currentQuestion['model']->difficulty_level] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($currentQuestion['model']->difficulty_level) }}
                                </span>
                            @endif
                        </div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 leading-relaxed">
                            {!!  $currentQuestion['model']->question->down !!}
                        </h2>
                    </div>
                </div>

                <!-- Question Content -->
                <div class="ml-16 space-y-4">
                    @if($currentQuestion['type'] === 'multiple_choice_question')
                        <div class="space-y-3">
                            @php
                                $options = [];
                                $question = $currentQuestion['model'];

                                // Collect all non-empty options
                                if (!empty($question->option_a)) $options['A'] = $question->option_a;
                                if (!empty($question->option_b)) $options['B'] = $question->option_b;
                                if (!empty($question->option_c)) $options['C'] = $question->option_c;
                                if (!empty($question->option_d)) $options['D'] = $question->option_d;
                                if (!empty($question->option_e)) $options['E'] = $question->option_e;
                            @endphp

                            @foreach($options as $letter => $option)
                                <label
                                    class="flex items-start space-x-3 p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors group">
                                    <input type="radio"
                                           name="question_{{ $currentQuestionIndex }}"
                                           value="{{ $letter }}"
                                           wire:model="responses.{{ $currentQuestionIndex }}.answer"
                                           class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600">
                                    <div class="flex-1">
                    <span
                        class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-gray-100">
                        {{ $letter }}.
                    </span>
                                        <span
                                            class="ml-2 text-gray-900 dark:text-gray-100 group-hover:text-gray-900 dark:group-hover:text-gray-100">
                        {!! $option->down !!}
                    </span>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                    @elseif($currentQuestion['type'] === 'true_or_false_question')
                        <div class="space-y-3">
                            <label
                                class="flex items-center space-x-3 p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors group">
                                <input type="radio"
                                       name="question_{{ $currentQuestionIndex }}"
                                       value="true"
                                       wire:model="responses.{{ $currentQuestionIndex }}.answer"
                                       class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 dark:border-gray-600">
                                <span class="text-lg font-medium text-green-600 dark:text-green-400">✅ True</span>
                            </label>
                            <label
                                class="flex items-center space-x-3 p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors group">
                                <input type="radio"
                                       name="question_{{ $currentQuestionIndex }}"
                                       value="false"
                                       wire:model="responses.{{ $currentQuestionIndex }}.answer"
                                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 dark:border-gray-600">
                                <span class="text-lg font-medium text-red-600 dark:text-red-400">❌ False</span>
                            </label>
                        </div>
                    @elseif($currentQuestion['type'] === 'essay_question')
                        <div>
                            <textarea wire:model="responses.{{ $currentQuestionIndex }}.answer"
                                      rows="8"
                                      class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                      placeholder="Enter your answer here..."></textarea>
                            <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                <span wire:ignore>
                                    <span id="wordCount">0</span> words
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Navigation -->
            <div class="bg-gray-50 dark:bg-gray-700 px-8 py-4 flex items-center justify-between">
                <button wire:click="previousQuestion"
                        {{ $currentQuestionIndex === 0 ? 'disabled' : '' }}
                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Previous
                </button>

                <div class="flex items-center space-x-2">
                    @for($i = 0; $i < count($questions); $i++)
                        <button wire:click="jumpToQuestion({{ $i }})"
                                class="w-8 h-8 rounded-full text-xs font-medium transition-colors
                                {{ $i === $currentQuestionIndex ? 'bg-indigo-600 text-white' :
                                   (isset($responses[$i]) && $responses[$i]['is_answered'] ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-600 dark:bg-gray-600 dark:text-gray-400') }}
                                hover:bg-indigo-500 hover:text-white">
                            {{ $i + 1 }}
                        </button>
                    @endfor
                </div>

                @if($currentQuestionIndex === count($questions) - 1)
                    <button wire:click="completeAssessment"
                            onclick="return confirm('Are you sure you want to submit your assessment?')"
                            class="inline-flex items-center px-6 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Submit Assessment
                    </button>
                @else
                    <button wire:click="nextQuestion"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">
                        Next
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                @endif
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            // Timer functionality
            @if($timeRemaining)
            let timeRemaining = {{ $timeRemaining }};
            const timer = setInterval(() => {
                timeRemaining--;
                document.getElementById('timer').textContent = new Date(timeRemaining * 1000).toISOString().substr(11, 8);

                if (timeRemaining <= 0) {
                    clearInterval(timer);
                    @this.
                    call('submitAssessment');
                }
            }, 1000);
            @endif

            // Word count for essay questions
            document.addEventListener('livewire:updated', () => {
                const textareas = document.querySelectorAll('textarea');
                textareas.forEach(textarea => {
                    const updateWordCount = () => {
                        const words = textarea.value.trim().split(/\s+/).filter(word => word.length > 0);
                        const wordCountElement = document.getElementById('wordCount');
                        if (wordCountElement) {
                            wordCountElement.textContent = words.length;
                        }
                    };

                    textarea.addEventListener('input', updateWordCount);
                    updateWordCount(); // Initial count
                });
            });
        </script>
    @endpush
</div>
