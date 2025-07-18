<div class="min-h-screen bg-gray-50">
    @if($currentStep === 'setup')
        <!-- Setup Phase -->
        <div class="max-w-4xl mx-auto p-6">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Self Assessment</h1>
                    <p class="text-gray-600">Create your personalized assessment to test your knowledge</p>
                </div>

                <form wire:submit.prevent="startAssessment" class="space-y-6">
                    <!-- Subject Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
                            <select wire:model.live="selectedSubject"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select a subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedSubject') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Topic (Optional)</label>
                            <select wire:model.live="selectedTopic"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                {{ count($topics) === 0 ? 'disabled' : '' }}>
                                <option value="">All topics</option>
                                @foreach($topics as $topic)
                                    <option value="{{ $topic['id'] }}">{{ $topic['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Subtopic Selection -->
                    @if(count($subtopics) > 0)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subtopic (Optional)</label>
                            <select wire:model.live="selectedSubtopic"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All subtopics</option>
                                @foreach($subtopics as $subtopic)
                                    <option value="{{ $subtopic['id'] }}">{{ $subtopic['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Question Types -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-4">Question Types *</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="flex items-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50">
                                <input wire:model="questionTypes.multiple_choice_question"
                                       type="checkbox"
                                       class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <div class="ml-3">
                                    <label class="text-sm font-medium text-gray-700">Multiple Choice</label>
                                    <p class="text-xs text-gray-500">Choose from multiple options</p>
                                </div>
                            </div>
                            <div class="flex items-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50">
                                <input wire:model="questionTypes.true_or_false_question"
                                       type="checkbox"
                                       class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <div class="ml-3">
                                    <label class="text-sm font-medium text-gray-700">True/False</label>
                                    <p class="text-xs text-gray-500">Binary choice questions</p>
                                </div>
                            </div>
                            <div class="flex items-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50">
                                <input wire:model="questionTypes.essay_question"
                                       type="checkbox"
                                       class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <div class="ml-3">
                                    <label class="text-sm font-medium text-gray-700">Essay</label>
                                    <p class="text-xs text-gray-500">Written responses</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Assessment Settings -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Number of Questions *</label>
                            <input wire:model="questionCount"
                                   type="number"
                                   min="1"
                                   max="50"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('questionCount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Time Limit (minutes) *</label>
                            <input wire:model="timeLimitMinutes"
                                   type="number"
                                   min="5"
                                   max="180"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('timeLimitMinutes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Difficulty Level</label>
                            <select wire:model="difficulty"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="all">All Levels</option>
                                <option value="easy">Easy</option>
                                <option value="medium">Medium</option>
                                <option value="hard">Hard</option>
                            </select>
                        </div>
                    </div>

                    <!-- Start Button -->
                    <div class="flex justify-center pt-6">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-8 rounded-lg transition-colors duration-200 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M19 10a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Start Assessment
                        </button>
                    </div>
                </form>

            </div>
        </div>

    @elseif($currentStep === 'assessment')
        <!-- Assessment Phase -->
        <div class="max-w-6xl mx-auto p-6">
            <!-- Header with Timer -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Assessment in Progress</h1>
                        <p class="text-gray-600">Question {{ $currentQuestionIndex + 1 }} of {{ count($questions) }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-blue-600" x-data="{ time: @entangle('timeRemaining') }" x-init="
                            setInterval(() => {
                                if (time > 0 && @entangle('isTimerActive').live) {
                                    time--;
                                    @this.set('timeRemaining', time);
                                }
                                if (time === 0) {
                                    @this.call('submitAssessment');
                                }
                            }, 1000)
                        ">
                            <span x-text="Math.floor(time / 60) + ':' + (time % 60 < 10 ? '0' : '') + (time % 60)"></span>
                        </div>
                        <p class="text-sm text-gray-500">Time Remaining</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Question Navigation -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Questions</h3>
                        <div class="grid grid-cols-5 lg:grid-cols-4 gap-2">
                            @foreach($questions as $index => $question)
                                <button wire:click="goToQuestion({{ $index }})"
                                        class="w-10 h-10 rounded-full text-sm font-medium transition-colors duration-200
                                               {{ $index === $currentQuestionIndex ? 'bg-blue-600 text-white' : '' }}
                                               {{ $responses[$index]['answered'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}
                                               {{ $index === $currentQuestionIndex && $responses[$index]['answered'] ? 'bg-green-600 text-white' : '' }}
                                               hover:bg-blue-500 hover:text-white">
                                    {{ $index + 1 }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Question Content -->
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-xl shadow-lg p-8">
                        @if(isset($questions[$currentQuestionIndex]))
                            @php
                                $question = $questions[$currentQuestionIndex];
                                $questionType = $question['type'];
                            @endphp

                            <div class="mb-6">
                                <div class="flex justify-between items-start mb-4">
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                        {{ ucfirst(str_replace('_', ' ', $questionType)) }}
                                    </span>
{{--                                    <span class="text-sm text-gray-500">{{ $question['points'] }} points</span>--}}
                                </div>
                                <h2 class="text-xl font-semibold text-gray-900 mb-6">
                                    {!! $question['question'] !!}
                                </h2>
                            </div>

                            <!-- Answer Options -->
                            <div class="mb-8">
                                @if($questionType === 'multiple_choice_question')
                                    <div class="space-y-3">
                                        @foreach($question['options'] as $option => $text)
                                            <label class="flex items-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                                                <input wire:click="answerQuestion({{ $currentQuestionIndex }}, '{{ $option }}')"
                                                       type="radio"
                                                       name="question_{{ $currentQuestionIndex }}"
                                                       value="{{ $option }}"
                                                       @if($responses[$currentQuestionIndex]['answer'] === $option) checked @endif
                                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                                <div class="ml-3">
                                                    <span class="font-medium">{{ $option }}.</span> {!! $text !!}
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>

                                @elseif($questionType === 'true_or_false_question')
                                    <div class="space-y-3">
                                        <label class="flex items-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input wire:click="answerQuestion({{ $currentQuestionIndex }}, 'true')"
                                                   type="radio"
                                                   name="question_{{ $currentQuestionIndex }}"
                                                   value="true"
                                                   @if($responses[$currentQuestionIndex]['answer'] === 'true') checked @endif
                                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                            <span class="ml-3 font-medium">True</span>
                                        </label>
                                        <label class="flex items-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input wire:click="answerQuestion({{ $currentQuestionIndex }}, 'false')"
                                                   type="radio"
                                                   name="question_{{ $currentQuestionIndex }}"
                                                   value="false"
                                                   @if($responses[$currentQuestionIndex]['answer'] === 'false') checked @endif
                                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                            <span class="ml-3 font-medium">False</span>
                                        </label>
                                    </div>

                                @elseif($questionType === 'essay_question')
                                    <div>
                                        <textarea wire:model="responses.{{ $currentQuestionIndex }}.answer"
                                                  wire:change="answerQuestion({{ $currentQuestionIndex }}, $event.target.value)"
                                                  rows="8"
                                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                  placeholder="Type your answer here..."></textarea>
                                    </div>
                                @endif
                            </div>

                            <!-- Navigation Buttons -->
                            <div class="flex justify-between">
                                <button wire:click="previousQuestion"
                                        @if($currentQuestionIndex === 0) disabled @endif
                                        class="bg-gray-600 hover:bg-gray-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200">
                                    Previous
                                </button>

                                <div class="flex space-x-3">
                                    @if($currentQuestionIndex === count($questions) - 1)
                                        <button wire:click="submitAssessment"
                                                class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200">
                                            Submit Assessment
                                        </button>
                                    @else
                                        <button wire:click="nextQuestion"
                                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200">
                                            Next
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    @elseif($currentStep === 'results')
        <!-- Results Phase -->
        <div class="max-w-6xl mx-auto p-6">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Assessment Complete!</h1>
                    <p class="text-gray-600">Here are your results</p>
                </div>

                <!-- Overall Score -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-blue-50 rounded-lg p-6 text-center">
                        <div class="text-3xl font-bold text-blue-600">{{ $results['percentage'] }}%</div>
                        <div class="text-sm text-gray-600">Overall Score</div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-6 text-center">
                        <div class="text-3xl font-bold text-green-600">{!! $results['correct_answers'] !!}</div>
                        <div class="text-sm text-gray-600">Correct Answers</div>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-6 text-center">
                        <div class="text-3xl font-bold text-yellow-600">{!! $results['answered_questions'] !!}</div>
                        <div class="text-sm text-gray-600">Questions Answered</div>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-6 text-center">
                        <div class="text-3xl font-bold text-purple-600">{{ $results['time_taken'] }}</div>
                        <div class="text-sm text-gray-600">Minutes Taken</div>
                    </div>
                </div>

                <!-- Question Type Performance -->
                @if(!empty($detailedResults))
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Performance by Question Type</h2>
                        <div class="grid grid-cols-1 md:grid-cols-{{ count($detailedResults) }} gap-6">
                            @foreach($detailedResults as $result)
                                @dd($result)
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <h3 class="font-semibold text-gray-900 mb-2">{{ $result['type'] }}</h3>
                                    <div class="text-2xl font-bold text-gray-900 mb-1">{{ $result['percentage'] }}%</div>
                                    <div class="text-sm text-gray-600 mb-2">{{ $result['correct'] }}/{{ $result['total'] }} correct</div>
                                    <div class="text-lg font-semibold text-{{ $result['grade'] === 'A' ? 'green' : ($result['grade'] === 'F' ? 'red' : 'yellow') }}-600">
                                        Grade: {{ $result['grade'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Detailed Question Review -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Question Review</h2>
                    <div class="space-y-4">
                        @foreach($results['questions'] as $index => $question)
                            <div class="border border-gray-200 rounded-lg p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex items-center">
                                        <span class="text-lg font-semibold text-gray-900 mr-2">{{ $index + 1 }}.</span>
                                        <span class="bg-{{ $question['is_correct'] ? 'green' : 'red' }}-100 text-{{ $question['is_correct'] ? 'green' : 'red' }}-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                            {{ $question['is_correct'] ? 'Correct' : 'Incorrect' }}
                                        </span>
                                    </div>
                                    <span class="text-sm text-gray-500">{{ $question['points_earned'] }}/{{ $question['points_possible'] }} points</span>
                                </div>

                                <div class="mb-4">
                                    <h3 class="font-medium text-gray-900 mb-2">{!! $question['question'] !!}</h3>
                                </div>

                                @if($question['type'] !== 'essay_question')
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <span class="text-sm font-medium text-gray-700">Your Answer:</span>
                                            <div class="text-sm text-gray-900">{{ $question['student_answer'] ?? 'No answer' }}</div>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-700">Correct Answer:</span>
                                            <div class="text-sm text-green-600">{{ $question['correct_answer'] }}</div>
                                        </div>
                                    </div>
                                @else
                                    <div>
                                        <span class="text-sm font-medium text-gray-700">Your Answer:</span>
                                        <div class="text-sm text-gray-900 bg-gray-50 rounded p-3 mt-1">
                                            {{ $question['student_answer'] ?? 'No answer provided' }}
                                        </div>
                                        @if(!$question['is_graded'])
                                            <p class="text-sm text-orange-600 mt-2">
                                                <i class="fas fa-clock mr-1"></i>
                                                This essay question is pending teacher review.
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-center space-x-4">
                    <button wire:click="resetAssessment"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200">
                        Take Another Assessment
                    </button>
                    <a href="{{ route('dashboard') }}"
                       class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200">
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed z-50 top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-lg" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
</div>
