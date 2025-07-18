<div class="">

    @php
        $formatted = $this->getFormattedQuestion($currentQuestionIndex);
    @endphp
    @if($step === 'configuration')
        <!-- Configuration Phase -->
        <div class="max-w-6xl mx-auto p-6">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-4xl font-bold mb-2">Self Assessment</h1>
                            <p class="text-blue-100 text-lg">Configure your personalized assessment</p>
                        </div>
                        <div class="flex space-x-4">
                            <button wire:click="toggleSettings" class="bg-white/20 hover:bg-white/30 p-3 rounded-full transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </button>
                            <button wire:click="toggleHelp" class="bg-white/20 hover:bg-white/30 p-3 rounded-full transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Configuration Form -->
                <div class="p-8">
                    <div class="space-y-8">
                        <!-- Subject and Topic Selection -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Subject *</label>
                                <select wire:model.live="selectedSubject"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                    <option value="">Select a subject</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedSubject') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Topic (Optional)</label>
                                <select wire:model.live="selectedTopic"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                        {{ empty($topics) ? 'disabled' : '' }}>
                                    <option value="">All topics</option>
                                    @foreach($topics as $topic)
                                        <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Subtopic (Optional)</label>
                                <select wire:model.live="selectedSubtopic"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                        {{ empty($subtopics) ? 'disabled' : '' }}>
                                    <option value="">All subtopics</option>
                                    @foreach($subtopics as $subtopic)
                                        <option value="{{ $subtopic->id }}">{{ $subtopic->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Question Types -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-4">Question Types *</label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="relative">
                                    <input wire:model.live="questionTypes.multiple_choice_question"
                                           type="checkbox"
                                           class="sr-only peer"
                                           id="mcq">
                                    <label for="mcq" class="flex items-center p-6 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                                        <div class="flex items-center">
                                            <div class="w-5 h-5 border-2 border-gray-300 rounded mr-3 peer-checked:border-blue-500 peer-checked:bg-blue-500 relative">
                                                <svg class="w-3 h-3 text-white absolute top-0.5 left-0.5 hidden peer-checked:block" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-800">Multiple Choice</div>
                                                <div class="text-sm text-gray-600">Choose from multiple options</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <div class="relative">
                                    <input wire:model.live="questionTypes.true_or_false_question"
                                           type="checkbox"
                                           class="sr-only peer"
                                           id="tf">
                                    <label for="tf" class="flex items-center p-6 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                                        <div class="flex items-center">
                                            <div class="w-5 h-5 border-2 border-gray-300 rounded mr-3 peer-checked:border-blue-500 peer-checked:bg-blue-500 relative">
                                                <svg class="w-3 h-3 text-white absolute top-0.5 left-0.5 hidden peer-checked:block" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-800">True/False</div>
                                                <div class="text-sm text-gray-600">Binary choice questions</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <div class="relative">
                                    <input wire:model.live="questionTypes.essay_question"
                                           type="checkbox"
                                           class="sr-only peer"
                                           id="essay">
                                    <label for="essay" class="flex items-center p-6 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                                        <div class="flex items-center">
                                            <div class="w-5 h-5 border-2 border-gray-300 rounded mr-3 peer-checked:border-blue-500 peer-checked:bg-blue-500 relative">
                                                <svg class="w-3 h-3 text-white absolute top-0.5 left-0.5 hidden peer-checked:block" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-800">Essay</div>
                                                <div class="text-sm text-gray-600">Written responses</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            @if(isset($customErrors['questionTypes']))
                                <span class="text-red-500 text-sm mt-2">{{ $customErrors['questionTypes'] }}</span>
                            @endif
                        </div>

                        <!-- Assessment Settings -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Number of Questions *</label>
                                <input wire:model="questionCount"
                                       type="number"
                                       min="1"
                                       max="50"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                @error('questionCount') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Time Limit (minutes) *</label>
                                <input wire:model="timeLimitMinutes"
                                       type="number"
                                       min="5"
                                       max="180"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                @error('timeLimitMinutes') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Difficulty Level</label>
                                <select wire:model="difficulty"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                    <option value="all">All Levels</option>
                                    <option value="easy">Easy</option>
                                    <option value="medium">Medium</option>
                                    <option value="hard">Hard</option>
                                </select>
                            </div>
                        </div>

                        <!-- Additional Options -->
                        <div class="flex items-center space-x-6">
                            <label class="flex items-center">
                                <input wire:model="saveProgress"
                                       type="checkbox"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Save progress automatically</span>
                            </label>
                        </div>

                        <!-- Error Messages -->
                        @if(isset($customErrors['questions']))
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <p class="text-red-800">{{ $customErrors['questions'] }}</p>
                            </div>
                        @endif

                        <!-- Submit Button -->
                        <div class="flex justify-center">
                            <button wire:click="startAssessment"
                                    class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-4 rounded-xl hover:from-blue-700 hover:to-indigo-700 font-semibold text-lg shadow-lg transition-all transform hover:scale-105">
                                Start Assessment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @elseif($step === 'assessment')
        <!-- Assessment Phase -->
        <div class="max-w-7xl mx-auto p-4">
            <!-- Header with Timer and Progress -->
            <div class="bg-white rounded-t-2xl shadow-lg p-6 border-b">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">{{ $this->getSubjectName() }}</h1>
                        <p class="text-gray-600">Question {{ $currentQuestionIndex + 1 }} of {{ count($questions) }}</p>
                    </div>

                    <div class="flex items-center space-x-6">
                        <!-- Timer -->
                        <div class="flex items-center space-x-2 bg-red-50 px-4 py-2 rounded-lg">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-red-600 font-semibold" id="timer">{{ $timeLimitMinutes }}:00</span>
                        </div>

                        <!-- Progress -->
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Progress:</span>
                            <div class="w-32 bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                     style="width: {{ $progressPercentage }}%"></div>
                            </div>
                            <span class="text-sm font-semibold">{{ round($progressPercentage) }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-lg">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 p-6">
                    <!-- Question Navigation -->
                    <div class="lg:col-span-1">
                        <div class="sticky top-6">
                            <h3 class="text-lg font-semibold mb-4">Questions</h3>
                            <div class="grid grid-cols-5 lg:grid-cols-4 gap-2">
                                @foreach($questions as $index => $question)
                                    <button wire:click="navigateToQuestion({{ $index }})"
                                            class="w-10 h-10 rounded-full text-sm font-semibold transition-all
                                                {{ $index === $currentQuestionIndex ? 'bg-blue-600 text-white' :
                                                   ($answeredQuestions[$index] ? 'bg-green-100 text-green-800 border-2 border-green-300' :
                                                    'bg-gray-100 text-gray-600 hover:bg-gray-200') }}">
                                        {{ $index + 1 }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Question Content -->
                    <div class="lg:col-span-3">
                        @if(!empty($questions) && isset($questions[$currentQuestionIndex]))
                            @php $currentQuestion = $questions[$currentQuestionIndex] @endphp


                            <div class="question-content">
                                <h3>{{ $formatted['display_question'] }}</h3>

                                @if(!empty($formatted['display_options']))
                                    <div class="options">
                                        @foreach($formatted['display_options'] as $key => $option)
                                            <div class="option">
                                                <input type="radio" name="question_{{ $currentQuestionIndex }}"
                                                       value="{{ $key }}"
                                                       wire:model="responses.{{ $currentQuestionIndex }}.selected_option">
                                                <label>{{ $option }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <!-- Answer Options -->
                            <div class="space-y-4">
                                @if($currentQuestion['type'] === 'multiple_choice_question')
                                    @foreach($formatted['display_options'] as $key => $option)
                                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-all
                                                {{ $responses[$currentQuestionIndex] === $key ? 'border-blue-500 bg-blue-50' : '' }}">
                                            <input wire:click="answerQuestion('{{ $key }}')"
                                                   type="radio"
                                                   name="question_{{ $currentQuestionIndex }}"
                                                   value="{{ $key }}"
                                                   class="mr-4 text-blue-600 focus:ring-blue-500">
                                            <span class="font-medium text-gray-800 mr-2">{{ strtoupper($key) }}.</span>
                                            <span class="text-gray-700">{!! $option !!}</span>
                                        </label>
                                    @endforeach

                                @elseif($currentQuestion['type'] === 'true_or_false_question')
                                    @foreach(['true' => 'True', 'false' => 'False'] as $value => $label)
                                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-all
                                                {{ $responses[$currentQuestionIndex] === $value ? 'border-blue-500 bg-blue-50' : '' }}">
                                            <input wire:click="answerQuestion('{{ $value }}')"
                                                   type="radio"
                                                   name="question_{{ $currentQuestionIndex }}"
                                                   value="{{ $value }}"
                                                   class="mr-4 text-blue-600 focus:ring-blue-500">
                                            <span class="text-gray-700 font-medium">{{ $label }}</span>
                                        </label>
                                    @endforeach

                                @elseif($currentQuestion['type'] === 'essay_question')
                                    <div>
                                        <textarea wire:model.lazy="responses.{{ $currentQuestionIndex }}"
                                                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all min-h-[200px]"
                                                  placeholder="Write your answer here..."></textarea>
                                    </div>
                                @endif
                            </div>

                            <!-- Navigation Buttons -->
                            <div class="flex justify-between mt-8">
                                <button wire:click="previousQuestion"
                                        class="flex items-center px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all
                                               {{ $currentQuestionIndex === 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        {{ $currentQuestionIndex === 0 ? 'disabled' : '' }}>
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                    Previous
                                </button>

                                <div class="flex space-x-4">
                                    @if($currentQuestionIndex === count($questions) - 1)
                                        <button wire:click="confirmSubmit"
                                                class="flex items-center px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Submit Assessment
                                        </button>
                                    @else
                                        <button wire:click="nextQuestion"
                                                class="flex items-center px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all">
                                            Next
                                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    @elseif($step === 'results')
        <!-- Results Phase -->
        <div class="max-w-6xl mx-auto p-6">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 text-white p-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h1 class="text-4xl font-bold mb-2">Assessment Complete!</h1>
                    <p class="text-green-100 text-lg">Great job! Here are your results</p>
                </div>

                <!-- Results Summary -->
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                        <div class="bg-blue-50 rounded-xl p-6 text-center">
                            <div class="text-3xl font-bold text-blue-600 mb-2">{{ $results['percentage'] }}%</div>
                            <div class="text-blue-800 font-medium">Overall Score</div>
                        </div>
                        <div class="bg-green-50 rounded-xl p-6 text-center">
                            <div class="text-3xl font-bold text-green-600 mb-2">{{ $results['correct_answers'] }}</div>
                            <div class="text-green-800 font-medium">Correct Answers</div>
                        </div>
                        <div class="bg-yellow-50 rounded-xl p-6 text-center">
                            <div class="text-3xl font-bold text-yellow-600 mb-2">{{ $results['total_questions'] }}</div>
                            <div class="text-yellow-800 font-medium">Total Questions</div>
                        </div>
                        <div class="bg-purple-50 rounded-xl p-6 text-center">
                            <div class="text-3xl font-bold text-purple-600 mb-2">{{ $results['grade'] }}</div>
                            <div class="text-purple-800 font-medium">Grade</div>
                        </div>
                    </div>

                    <!-- Performance by Question Type -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Performance by Question Type</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            @foreach($results['type_results'] as $type => $typeResult)
                                <div class="bg-gray-50 rounded-xl p-6">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                                        {{ ucfirst(str_replace('_', ' ', $type)) }}
                                    </h3>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Correct:</span>
                                            <span class="font-semibold">{{ $typeResult['correct'] }}/{{ $typeResult['total'] }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Score:</span>
                                            <span class="font-semibold">{{ $typeResult['score'] }}/{{ $typeResult['max_score'] }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Percentage:</span>
                                            <span class="font-semibold">{{ $typeResult['max_score'] > 0 ? round(($typeResult['score'] / $typeResult['max_score']) * 100) : 0 }}%</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-4 justify-center">
                        <button wire:click="toggleReview"
                                class="flex items-center px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Review Answers
                        </button>

                        <button wire:click="retakeAssessment"
                                class="flex items-center px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Retake Assessment
                        </button>

                        <button wire:click="exportResults('pdf')"
                                class="flex items-center px-6 py-3 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export Results
                        </button>

                        <button wire:click="shareResults"
                                class="flex items-center px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                            </svg>
                            Share Results
                        </button>

                        <button wire:click="toggleFeedback"
                                class="flex items-center px-6 py-3 bg-yellow-600 text-white rounded-xl hover:bg-yellow-700 transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                            </svg>
                            Feedback
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modals and Overlays -->

    <!-- Confirm Submit Modal -->
    @if($showConfirmSubmit)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-8 max-w-md mx-4">
                <div class="text-center">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Submit Assessment?</h3>
                    <p class="text-gray-600 mb-6">Are you sure you want to submit your assessment? This action cannot be undone.</p>
                    <div class="flex space-x-4 justify-center">
                        <button wire:click="cancelSubmit"
                                class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all">
                            Cancel
                        </button>
                        <button wire:click="submitAssessment"
                                class="px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all">
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Review Modal -->
    @if($showReview && $step === 'results')
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[80vh] overflow-y-auto">
                <div class="p-6 border-b">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-gray-800">Review Your Answers</h2>
                        <button wire:click="toggleReview" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    @if($results && isset($questions))
                        @foreach($questions as $index => $question)
                            <div class="mb-8 p-6 border rounded-xl">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold">Question {{ $index + 1 }}</h3>
                                    <div class="flex items-center space-x-2">
                                        @if($question['is_correct'] === true)
                                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">Correct</span>
                                        @elseif($question['is_correct'] === false)
                                            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm">Incorrect</span>
                                        @else
                                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">Pending Review</span>
                                        @endif
                                        <span class="text-sm text-gray-600">{{ $question['score_earned'] }}/{{ $question['max_score'] }} points</span>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <p class="text-gray-800 font-medium">{!! $question['question_data']['question'] !!}</p>
                                </div>

                                @if($question['type'] !== 'essay_question')
                                    <div class="space-y-2">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm text-gray-600">Your Answer:</span>
                                            <span class="font-medium {{ $question['is_correct'] ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $question['student_answer'] ?? 'No answer' }}
                                            </span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm text-gray-600">Correct Answer:</span>
                                            <span class="font-medium text-green-600">{{ $question['correct_answer'] }}</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="space-y-2">
                                        <div class="text-sm text-gray-600">Your Answer:</div>
                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            {{ $question['student_answer'] ?? 'No answer provided' }}
                                        </div>
                                        <div class="text-sm text-gray-600">Sample Answer:</div>
                                        <div class="bg-green-50 p-4 rounded-lg">
                                            {!! $question['correct_answer'] !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Settings Modal -->
    @if($showSettings && $step === 'configuration')
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl max-w-2xl w-full">
                <div class="p-6 border-b">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-gray-800">Assessment Settings</h2>
                        <button wire:click="toggleSettings" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-gray-800">Auto-save Progress</h3>
                                <p class="text-sm text-gray-600">Automatically save your progress as you answer questions</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input wire:model="saveProgress" type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Help Modal -->
    @if($showHelp)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[80vh] overflow-y-auto">
                <div class="p-6 border-b">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-gray-800">Assessment Help</h2>
                        <button wire:click="toggleHelp" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-6">
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-2">How to Take the Assessment</h3>
                            <ul class="space-y-2 text-sm text-gray-600">
                                <li>• Configure your assessment by selecting subject, question types, and settings</li>
                                <li>• Navigate through questions using the numbered buttons or Previous/Next buttons</li>
                                <li>• Click on answer options to select your response</li>
                                <li>• Your progress is automatically saved if enabled</li>
                                <li>• Review your answers before submitting</li>
                            </ul>
                        </div>

                        <div>
                            <h3 class="font-semibold text-gray-800 mb-2">Question Types</h3>
                            <div class="space-y-2 text-sm text-gray-600">
                                <div><strong>Multiple Choice:</strong> Select the best answer from the options provided</div>
                                <div><strong>True/False:</strong> Choose whether the statement is true or false</div>
                                <div><strong>Essay:</strong> Provide a written response to the question</div>
                            </div>
                        </div>

                        <div>
                            <h3 class="font-semibold text-gray-800 mb-2">Tips for Success</h3>
                            <ul class="space-y-2 text-sm text-gray-600">
                                <li>• Read each question carefully before selecting an answer</li>
                                <li>• Use the navigation buttons to review previous questions</li>
                                <li>• Keep an eye on the timer in the top right corner</li>
                                <li>• Don't spend too much time on one question</li>
                                <li>• Review your answers before submitting</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Feedback Modal -->
    @if($showFeedback)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full">
                <div class="p-6 border-b">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-gray-800">Feedback</h2>
                        <button wire:click="toggleFeedback" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <form wire:submit.prevent="submitFeedback">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                How was your assessment experience?
                            </label>
                            <textarea wire:model="feedbackText"
                                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all min-h-[120px]"
                                      placeholder="Share your thoughts about the assessment..."></textarea>
                            @error('feedbackText') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex justify-end space-x-4">
                            <button type="button" wire:click="toggleFeedback"
                                    class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all">
                                Submit Feedback
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Loading Overlay -->
    @if($isSubmitting)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-8 text-center">
                <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-600 mx-auto mb-4"></div>
                <p class="text-gray-600 font-medium">Submitting your assessment...</p>
            </div>
        </div>
    @endif

    <!-- Timer Script -->
    <script>
        document.addEventListener('livewire:load', function () {
            let timerInterval;

            Livewire.on('startTimer', (timeLimit) => {
                let timeRemaining = timeLimit;
                const timerElement = document.getElementById('timer');

                timerInterval = setInterval(() => {
                    if (timeRemaining <= 0) {
                        clearInterval(timerInterval);
                        Livewire.emit('timeUp');
                        return;
                    }

                    const minutes = Math.floor(timeRemaining / 60);
                    const seconds = timeRemaining % 60;
                    timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                    // Change color when time is running low
                    if (timeRemaining <= 300) { // 5 minutes
                        timerElement.parentElement.classList.add('bg-red-100');
                        timerElement.classList.add('text-red-600');
                    }

                    timeRemaining--;
                }, 1000);
            });

            // Auto-start timer when assessment begins
            @if($step === 'assessment' && $timeLimitMinutes)
                Livewire.emit('startTimer', {{ $timeLimitMinutes * 60 }});
            @endif
        });
    </script>
</div>
