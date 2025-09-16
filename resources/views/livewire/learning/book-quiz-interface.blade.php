<section>
<div>
    {{-- Quiz Setup Phase --}}
    <div x-data="{ showAdvanced: false }" x-show="!$wire.quizData && !$wire.quizResults" x-transition>
        <div class="mb-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Book Quiz Generator</h1>
                <p class="text-gray-600 dark:text-gray-400">Test your knowledge with AI-generated questions</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {{-- Book Selection --}}
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Select Book
                            </label>
                            <select wire:model.live="selectedBookId"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">Choose a book...</option>
                                @foreach($availableBooks as $book)
                                    <option value="{{ $book->id }}">{{ $book->title }} by {{ $book->author->name ?? $book->author->user?->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if($selectedBook)
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-start space-x-4">
                                    @if($selectedBook->cover_image_url)
                                        <img src="{{ $selectedBook->cover_image_url }}"
                                             alt="{{ $selectedBook->title }}"
                                             class="w-20 h-28 object-cover rounded shadow-md">
                                    @else
                                        <div class="w-20 h-28 bg-gray-300 dark:bg-gray-600 rounded flex items-center justify-center">
                                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <h3 class="font-bold text-gray-900 dark:text-white">{{ $selectedBook->title }}</h3>
                                        <p class="text-gray-600 dark:text-gray-400 text-sm">by {{ $selectedBook->author->name ?? $selectedBook->author->user?->nam }}</p>
                                        @if($selectedBook->genre)
                                            <span class="inline-block px-2 py-1 text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded mt-1">
                                                {{ $selectedBook->genre }}
                                            </span>
                                        @endif
                                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-2">
                                            {{ Str::limit($selectedBook->description, 100) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Quiz Settings --}}
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Question Type
                                </label>
                                <select wire:model.live="questionType"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="multiple_choice">Multiple Choice</option>
                                    <option value="true_false">True/False</option>
                                    <option value="essay">Essay</option>
                                    <option value="mixed">Mixed</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Number of Questions
                                </label>
                                <select wire:model.live="questionCount"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="5">5 questions</option>
                                    <option value="10" selected>10 questions</option>
                                    <option value="15">15 questions</option>
                                    <option value="20">20 questions</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Difficulty
                                </label>
                                <select wire:model.live="difficulty"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="easy">Easy</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="hard">Hard</option>
                                </select>
                            </div>
                        </div>

                        @if($selectedBook && $bookChapters->isNotEmpty())
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Chapter (Optional)
                                </label>
                                <select wire:model.live="selectedChapterId"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="">Entire book</option>
                                    @foreach($bookChapters as $chapter)
                                        <option value="{{ $chapter->id }}">Chapter {{ $chapter->chapter_number }}: {{ $chapter->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- Advanced Options Toggle --}}
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <button @click="showAdvanced = !showAdvanced"
                                    class="flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                                <svg class="h-4 w-4 mr-1 transform transition-transform"
                                     :class="{ 'rotate-180': showAdvanced }"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                                Advanced Options
                            </button>

                            <div x-show="showAdvanced" x-transition class="mt-4 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Focus Topics (Optional)
                                    </label>
                                    <input type="text" wire:model.live="focusTopics"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                           placeholder="e.g., character development, themes, plot">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Separate topics with commas</p>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" wire:model.live="includeQuotes"
                                           id="includeQuotes"
                                           class="rounded border-gray-300 dark:border-gray-600 text-blue-600">
                                    <label for="includeQuotes" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        Include questions with book quotes
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Generate Quiz Button --}}
                <div class="pt-6">
                    <button wire:click="generateQuiz"
                            wire:loading.attr="disabled"
                            :disabled="!$wire.selectedBookId || $wire.isGenerating"
                            class="w-full bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 disabled:from-gray-400 disabled:to-gray-500 text-white font-semibold py-4 px-6 rounded-lg transition-all duration-200 flex items-center justify-center shadow-lg hover:shadow-xl">
                        <span wire:loading.remove wire:target="generateQuiz" class="flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Generate Quiz
                        </span>
                        <span wire:loading wire:target="generateQuiz" class="flex items-center">
                            <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Generating Quiz...
                        </span>
                    </button>
                    <p class="text-gray-500 dark:text-gray-500 text-sm mt-2 text-center">This may take 30-60 seconds</p>
                </div>
            </div>
        </div>

        {{-- Error Messages --}}
        @if(isset($errors) && count($errors) > 0)
            <div class="mt-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                <div class="flex">
                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Please fix the following errors:</h3>
                        <ul class="mt-2 text-sm text-red-700 dark:text-red-300 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </div>

{{-- Quiz Taking Phase --}}
<div x-data="{
    currentQuestion: 0,
    answers: {},
    timeRemaining: null,
    quizTimer: null,
    showExplanations: false,

    init() {
        // Restore saved answers if any
        const savedAnswers = sessionStorage.getItem('quizAnswers');
        if (savedAnswers) {
            this.answers = JSON.parse(savedAnswers);
        }
    },

    selectAnswer(questionIndex, option) {
        this.answers[questionIndex] = option;
        this.saveAnswers();
    },

    isAnswerSelected(questionIndex, option) {
        return this.answers[questionIndex] === option;
    },

    saveAnswers() {
        sessionStorage.setItem('quizAnswers', JSON.stringify(this.answers));
    },

    nextQuestion() {
        if (this.currentQuestion < ($wire.quizData?.questions?.length - 1)) {
            this.currentQuestion++;
        }
    },

    prevQuestion() {
        if (this.currentQuestion > 0) {
            this.currentQuestion--;
        }
    },

    getProgressPercentage() {
        const total = $wire.quizData?.questions?.length || 1;
        return ((this.currentQuestion + 1) / total) * 100;
    },

    submitQuiz() {
        $wire.submitQuizAnswers(this.answers);
        sessionStorage.removeItem('quizAnswers');
    }
}"
x-show="$wire.quizData && !$wire.quizResults"
x-transition
id="quiz-section">


        @if($quizData)
            {{-- Quiz Header --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/30 dark:to-indigo-900/30 rounded-lg">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $quizData['quiz_session']['book_title'] }}</h2>
                    <p class="text-gray-600 dark:text-gray-400">by {{ $quizData['quiz_session']['author'] }}</p>
                </div>
                <div class="mt-4 md:mt-0 text-right">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Question</div>
                    <div class="text-lg font-bold text-blue-600 dark:text-blue-400">
                        <span x-text="currentQuestion + 1"></span> of {{ count($quizData['questions']) }}
                    </div>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div class="mb-8">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Progress</span>
                    <span class="text-sm text-gray-600 dark:text-gray-400"
                          x-text="`${Math.round(getProgressPercentage())}%`"></span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2.5 rounded-full transition-all duration-300"
                         :style="`width: ${getProgressPercentage()}%`"></div>
                </div>
            </div>

{{-- Current Question --}}
<template x-if="$wire.quizData && $wire.quizData.questions && currentQuestion < $wire.quizData.questions.length">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8 transition-all duration-300">
        <div class="mb-6">
            <span class="inline-block px-3 py-1 text-xs font-semibold bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full mb-3">
                Question <span x-text="currentQuestion + 1"></span>
            </span>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white"
                x-html="window.renderMarkdownWithMath($wire.quizData.questions[currentQuestion].question || '')">
            </h3>
        </div>

{{-- Question Options --}}
<div class="space-y-3">
    <template x-if="$wire.quizData.questions[currentQuestion].type === 'multiple_choice'">
        <div class="space-y-3">
            <template x-for="(option, index) in $wire.quizData.questions[currentQuestion].options" :key="index">
                <label @click="selectAnswer(currentQuestion, option)"
                       class="flex items-center p-4 border rounded-lg cursor-pointer transition-all"
                       :class="isAnswerSelected(currentQuestion, option) ?
                               'border-blue-500 bg-blue-50 dark:bg-blue-900/30 ring-2 ring-blue-500/30' :
                               'border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'">
                    <input type="radio"
                           :checked="isAnswerSelected(currentQuestion, option)"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                    <span class="font-medium mr-2 ml-2 text-gray-900 dark:text-white"
                          x-text="String.fromCharCode(65 + index) + '.'"></span>
                    <span class="text-gray-900 dark:text-white"
                          x-html="window.renderMarkdownWithMath(option || '')"></span>
                </label>
            </template>
        </div>
    </template>

    <template x-if="$wire.quizData.questions[currentQuestion].type === 'true_false'">
        <div class="space-y-3">
            <label @click="selectAnswer(currentQuestion, 'True')"
                   class="flex items-center justify-start p-4 border rounded-lg cursor-pointer transition-all"
                   :class="isAnswerSelected(currentQuestion, 'True') ?
                           'border-blue-500 bg-blue-50 dark:bg-blue-900/30 ring-2 ring-blue-500/30' :
                           'border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'">
                <input type="radio"
                       :checked="isAnswerSelected(currentQuestion, 'True')"
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 mr-2">
                <span class="font-medium mx-2 text-gray-900 dark:text-white">A.</span>
                <span class="text-gray-900 dark:text-white font-medium">True</span>
            </label>
            <label @click="selectAnswer(currentQuestion, 'False')"
                   class="flex items-center justify-start p-4 border rounded-lg cursor-pointer transition-all"
                   :class="isAnswerSelected(currentQuestion, 'False') ?
                           'border-blue-500 bg-blue-50 dark:bg-blue-900/30 ring-2 ring-blue-500/30' :
                           'border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'">
                <input type="radio"
                       :checked="isAnswerSelected(currentQuestion, 'False')"
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 mr-2">
                <span class="font-medium mx-2 text-gray-900 dark:text-white">B.</span>
                <span class="text-gray-900 dark:text-white font-medium">False</span>
            </label>
        </div>
    </template>

    <template x-if="$wire.quizData.questions[currentQuestion].type === 'essay'">
        <div>
            <textarea
                x-model="answers[currentQuestion]"
                placeholder="Type your detailed answer here..."
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                rows="6"></textarea>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                Please provide a detailed response. Word count:
                <span x-text="(answers[currentQuestion] || '').split(/\s+/).filter(word => word.length > 0).length">0</span> words
            </p>
        </div>
    </template>
</div>


        {{-- Navigation Buttons --}}
        <div class="flex justify-between mt-8">
            <button @click="prevQuestion()"
                    :disabled="currentQuestion === 0"
                    class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <svg class="h-4 w-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Previous
            </button>

            <template x-if="currentQuestion < $wire.quizData.questions.length - 1">
                <button @click="nextQuestion()"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center">
                    Next
                    <svg class="h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </template>

            <template x-if="currentQuestion === $wire.quizData.questions.length - 1">
                <button @click="submitQuiz()"
                        class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center font-semibold">
                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Submit Quiz
                </button>
            </template>
        </div>
    </div>
</template>

        @endif
    </div>

    {{-- Quiz Results Phase --}}
    <div x-show="$wire.quizResults" x-transition>
        @if($quizResults)
            <div class="space-y-8">
                {{-- Results Header --}}
                <div class="text-center">
                    <div class="mb-4">
                        @if($quizResults['results']['percentage'] >= 90)
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 dark:bg-green-900 rounded-full mb-4">
                                <svg class="h-10 w-10 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        @elseif($quizResults['results']['percentage'] >= 70)
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-yellow-100 dark:bg-yellow-900 rounded-full mb-4">
                                <svg class="h-10 w-10 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        @else
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-red-100 dark:bg-red-900 rounded-full mb-4">
                                <svg class="h-10 w-10 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Quiz Complete!</h2>
                    <p class="text-xl text-gray-600 dark:text-gray-400">{{ $quizResults['detailed_feedback']['overall_performance'] }}</p>

                    <div class="mt-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/30 dark:to-indigo-900/30 rounded-xl p-6 max-w-2xl mx-auto">
                        <div class="text-5xl font-bold text-blue-600 dark:text-blue-400 mb-2">{{ $quizResults['results']['percentage'] }}%</div>
                        <div class="text-gray-600 dark:text-gray-400">
                            You answered {{ $quizResults['results']['correct_answers'] }} out of {{ $quizResults['results']['total_questions'] }} questions correctly
                        </div>
                    </div>
                </div>

                {{-- Score Summary --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 text-center">
                        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">{{ $quizResults['results']['correct_answers'] }}</div>
                        <div class="text-gray-600 dark:text-gray-400">Correct Answers</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 text-center">
                        <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400 mb-2">{{ $quizResults['results']['total_questions'] - $quizResults['results']['correct_answers'] }}</div>
                        <div class="text-gray-600 dark:text-gray-400">Incorrect Answers</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 text-center">
                        <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-2">{{ $quizResults['results']['points_earned'] }}/{{ $quizResults['results']['points_possible'] }}</div>
                        <div class="text-gray-600 dark:text-gray-400">Points</div>
                    </div>
                </div>

                {{-- Strengths and Areas for Improvement --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @if(!empty($quizResults['detailed_feedback']['strengths']))
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                            <h3 class="text-xl font-bold text-green-600 dark:text-green-400 mb-4 flex items-center">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Your Strengths
                            </h3>
                            <ul class="space-y-3">
                                @foreach($quizResults['detailed_feedback']['strengths'] as $strength)
                                    <li class="flex items-start">
                                        <svg class="h-5 w-5 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="text-gray-700 dark:text-gray-300">{{ $strength }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(!empty($quizResults['detailed_feedback']['areas_for_improvement']))
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                            <h3 class="text-xl font-bold text-yellow-600 dark:text-yellow-400 mb-4 flex items-center">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                Areas for Improvement
                            </h3>
                            <ul class="space-y-3">
                                @foreach($quizResults['detailed_feedback']['areas_for_improvement'] as $improvement)
                                    <li class="flex items-start">
                                        <svg class="h-5 w-5 text-yellow-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                        <span class="text-gray-700 dark:text-gray-300">{{ $improvement }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                {{-- Study Suggestions --}}
                @if(!empty($quizResults['detailed_feedback']['study_suggestions']))
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                            <svg class="h-5 w-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            Study Suggestions
                        </h3>
                        <ul class="space-y-2">
                            @foreach($quizResults['detailed_feedback']['study_suggestions'] as $suggestion)
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-blue-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                    <span class="text-gray-700 dark:text-gray-300">{{ $suggestion }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row justify-center gap-4 pt-4">
                    <button @click="showExplanations = !showExplanations"
                            class="px-6 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-white rounded-lg transition-colors flex items-center justify-center">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span x-text="showExplanations ? 'Hide Explanations' : 'Show Explanations'"></span>
                    </button>

                    <button wire:click="exportResults"
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center justify-center">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export Results
                    </button>

                    <button @click="$wire.resetQuiz(); showExplanations = false"
                            class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center justify-center">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Take Another Quiz
                    </button>
                </div>

                {{-- Question Explanations --}}
                <div x-show="showExplanations" x-transition>
                    @if($quizResults['question_breakdown'])
                        <div class="space-y-6 mt-8">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white text-center">Question Review</h3>

                            @foreach($quizResults['question_breakdown'] as $index => $question)
                                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border-l-4 {{ $question['is_correct'] ? 'border-green-500' : 'border-red-500' }}">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="font-semibold text-gray-900 dark:text-white">Question {{ $question['question_number'] }}</h4>
                                        <div class="flex items-center space-x-2">
                                            @if($question['is_correct'])
                                                <span class="px-2 py-1 text-xs bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full">
                                                    Correct
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-full">
                                                    Incorrect
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <p class="text-gray-800 dark:text-gray-200 font-medium">{{ $question['question_text'] }}</p>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <h5 class="font-medium text-gray-700 dark:text-gray-300 mb-1">Your Answer:</h5>
                                            <p class="text-gray-900 dark:text-white p-2 bg-gray-100 dark:bg-gray-700 rounded">
                                                {{ $question['user_answer'] ?? 'No answer provided' }}
                                            </p>
                                        </div>
                                        <div>
                                            <h5 class="font-medium text-gray-700 dark:text-gray-300 mb-1">Correct Answer:</h5>
                                            <p class="text-gray-900 dark:text-white p-2 bg-gray-100 dark:bg-gray-700 rounded">
                                                {{ $question['correct_answer'] }}
                                            </p>
                                        </div>
                                    </div>

                                    @if($question['feedback'])
                                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                            <h5 class="font-medium text-gray-900 dark:text-white mb-2 flex items-center">
                                                <svg class="h-5 w-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Explanation:
                                            </h5>
                                            <div class="prose dark:prose-invert max-w-none">
                                                <p class="text-gray-700 dark:text-gray-300">{{ $question['feedback'] }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Loading States --}}
    <div wire:loading.flex wire:target="generateQuiz" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-8 max-w-md mx-4 text-center">
            <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Generating Your Quiz</h3>
            <p class="text-gray-600 dark:text-gray-400">Our AI is creating personalized questions based on your book selection...</p>
        </div>
    </div>

    <div wire:loading.flex wire:target="submitQuizAnswers" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-8 max-w-md mx-4 text-center">
            <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-green-600 mx-auto mb-4"></div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Grading Your Quiz</h3>
            <p class="text-gray-600 dark:text-gray-400">Analyzing your answers and generating detailed feedback...</p>
        </div>
    </div>
</div>

{{-- Custom Styles --}}
<style>
    .quiz-option:hover {
        transform: translateY(-2px);
        transition: all 0.2s ease;
    }

    .quiz-progress-bar {
        background: linear-gradient(45deg, #3B82F6, #1D4ED8);
        box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
    }

    .timer-display {
        font-family: 'Courier New', monospace;
        letter-spacing: 1px;
    }

    .achievement-badge {
        animation: bounce 1s infinite;
    }

    @keyframes bounce {
        0%, 100% {
            transform: translateY(-25%);
            animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
        }
        50% {
            transform: translateY(0);
            animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
        }
    }

    .question-review-card {
        transition: all 0.3s ease;
    }

    .question-review-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    /* Dark mode enhancements */
    @media (prefers-color-scheme: dark) {
        .question-review-card:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }
    }

    /* Responsive design improvements */
    @media (max-width: 768px) {
        .achievement-badge {
            animation: none;
        }

        .question-review-card:hover {
            transform: none;
        }
    }

    /* Print styles for quiz results */
    @media print {
        .quiz-option, .achievement-badge {
            animation: none !important;
        }

        .bg-gradient-to-r, .bg-gradient-to-br {
            background: #f8f9fa !important;
            color: #333 !important;
        }
    }
</style>
</section>
