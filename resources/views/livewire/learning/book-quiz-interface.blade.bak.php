<div x-data="{
    currentStep: 'setup', // setup, quiz, results
    currentQuestion: 0,
    answers: {},
    timeRemaining: null,
    quizTimer: null,
    showExplanations: false,

    startQuiz() {
        this.currentStep = 'quiz';
        this.currentQuestion = 0;
        this.answers = {};
        if (this.quizData?.estimated_duration) {
            const minutes = parseInt(this.quizData.estimated_duration.split('-')[1] || '20');
            this.timeRemaining = minutes * 60; // Convert to seconds
            this.startTimer();
        }
    },

    startTimer() {
        this.quizTimer = setInterval(() => {
            this.timeRemaining--;
            if (this.timeRemaining <= 0) {
                this.submitQuiz();
            }
        }, 1000);
    },

    stopTimer() {
        if (this.quizTimer) {
            clearInterval(this.quizTimer);
        }
    },

    formatTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins}:${secs.toString().padStart(2, '0')}`;
    },

    nextQuestion() {
        if (this.currentQuestion < this.quizData.questions.length - 1) {
            this.currentQuestion++;
        }
    },

    prevQuestion() {
        if (this.currentQuestion > 0) {
            this.currentQuestion--;
        }
    },

    submitQuiz() {
        this.stopTimer();
        const timeTaken = this.quizData?.estimated_duration ?
            (parseInt(this.quizData.estimated_duration.split('-')[1] || '20') * 60) - this.timeRemaining :
            null;

        $wire.submitQuizAnswers(this.answers, timeTaken).then(() => {
            this.currentStep = 'results';
        });
    },

    resetQuiz() {
        this.currentStep = 'setup';
        this.currentQuestion = 0;
        this.answers = {};
        this.timeRemaining = null;
        this.stopTimer();
        $wire.resetQuiz();
    }
}"
     class="max-w-4xl mx-auto p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">

    {{-- Quiz Setup Phase --}}
    <div x-show="currentStep === 'setup'" x-transition>
        <div class="mb-8">
            <div class="flex items-center space-x-4 mb-6">
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                    <svg class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Quiz Me!</h1>
                    <p class="text-gray-600 dark:text-gray-400">Test your knowledge with AI-generated questions</p>
                </div>
            </div>
        </div>

        {{-- Book Selection --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Select Book
                    </label>
                    <select wire:model.live="selectedBookId"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">Choose a book...</option>
                        @foreach($availableBooks as $book)
                            <option value="{{ $book->id }}">{{ $book->title }} by {{ $book->author }}</option>
                        @endforeach
                    </select>
                </div>

                @if($selectedBook)
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-start space-x-4">
                            @if($selectedBook->cover_image_url)
                                <img src="{{ $selectedBook->cover_image_url }}" alt="{{ $selectedBook->title }}"
                                     class="w-20 h-28 object-cover rounded shadow-md">
                            @else
                                <div class="w-20 h-28 bg-gray-300 dark:bg-gray-600 rounded flex items-center justify-center">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 dark:text-white">{{ $selectedBook->title }}</h3>
                                <p class="text-gray-600 dark:text-gray-400">by {{ $selectedBook->author }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">{{ $selectedBook->genre }} • {{ $selectedBook->total_pages }} pages</p>
                                @if($selectedBook->themes)
                                    <div class="flex flex-wrap gap-1 mt-2">
                                        @foreach(array_slice($selectedBook->themes, 0, 3) as $theme)
                                            <span class="px-2 py-1 text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full">{{ $theme }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Chapter Selection --}}
                    @if($bookChapters->isNotEmpty())
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

                    {{-- Page Range --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Start Page (Optional)
                            </label>
                            <input type="number" wire:model.live="pageStart" min="1" max="{{ $selectedBook->total_pages }}"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                   placeholder="1">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                End Page (Optional)
                            </label>
                            <input type="number" wire:model.live="pageEnd" min="1" max="{{ $selectedBook->total_pages }}"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                   placeholder="{{ $selectedBook->total_pages }}">
                        </div>
                    </div>
                @endif
            </div>

            {{-- Quiz Configuration --}}
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Question Type
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                            <input type="radio" wire:model.live="questionType" value="multiple_choice" class="text-blue-600">
                            <div class="ml-3">
                                <div class="font-medium text-gray-900 dark:text-white">Multiple Choice</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">A, B, C, D options</div>
                            </div>
                        </label>
                        <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                            <input type="radio" wire:model.live="questionType" value="true_false" class="text-blue-600">
                            <div class="ml-3">
                                <div class="font-medium text-gray-900 dark:text-white">True/False</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Simple T/F questions</div>
                            </div>
                        </label>
                        <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                            <input type="radio" wire:model.live="questionType" value="essay" class="text-blue-600">
                            <div class="ml-3">
                                <div class="font-medium text-gray-900 dark:text-white">Essay</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Written responses</div>
                            </div>
                        </label>
                        <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                            <input type="radio" wire:model.live="questionType" value="mixed" class="text-blue-600">
                            <div class="ml-3">
                                <div class="font-medium text-gray-900 dark:text-white">Mixed</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Variety of types</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
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

                {{-- Advanced Options --}}
                <div x-data="{ showAdvanced: false }">
                    <button @click="showAdvanced = !showAdvanced"
                            class="flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                        <svg class="h-4 w-4 mr-1 transform transition-transform" :class="{ 'rotate-180': showAdvanced }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <input type="checkbox" wire:model.live="includeQuotes" id="includeQuotes" class="rounded border-gray-300 dark:border-gray-600 text-blue-600">
                            <label for="includeQuotes" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Include questions with book quotes</label>
                        </div>
                    </div>
                </div>

                {{-- Generate Quiz Button --}}
                <div class="pt-4">
                    <button wire:click="generateQuiz"
                            wire:loading.attr="disabled"
                            :disabled="!$wire.selectedBookId || $wire.isGenerating"
                            class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-semibold py-4 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center">
                        <span wire:loading.remove wire:target="generateQuiz">
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
                </div>
            </div>
        </div>

        {{-- Error Messages --}}
        @if($errors->any())
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
    <div x-show="currentStep === 'quiz'" x-transition>
        @if($quizData)
            {{-- Quiz Header --}}
            <div class="flex items-center justify-between mb-8 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $quizData['quiz_session']['book_title'] }}</h2>
                    <p class="text-gray-600 dark:text-gray-400">by {{ $quizData['quiz_session']['author'] }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-500">{{ $quizData['quiz_session']['context'] }}</p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" x-text="formatTime(timeRemaining)" x-show="timeRemaining"></div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Time Remaining</div>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div class="mb-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Progress</span>
                    <span class="text-sm text-gray-600 dark:text-gray-400" x-text="`Question ${currentQuestion + 1} of ${$wire.quizData.questions.length}`"></span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                         :style="`width: ${((currentQuestion + 1) / $wire.quizData.questions.length) * 100}%`"></div>
                </div>
            </div>

            {{-- Question Display --}}
            <div class="mb-8">
                <template x-for="(question, index) in $wire.quizData.questions" :key="index">
                    <div x-show="currentQuestion === index" x-transition>
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-6 mb-6">
                            <div class="flex items-start justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="`Question ${index + 1}`"></h3>
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-full" x-text="question.type.replace('_', ' ').toUpperCase()"></span>
                                    <span class="px-2 py-1 text-xs bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-full" x-text="`${question.points} pts`"></span>
                                </div>
                            </div>

                            <div class="mb-6">
                                <p class="text-gray-800 dark:text-gray-200 text-lg leading-relaxed" x-text="question.question"></p>
                            </div>

                            {{-- Multiple Choice Options --}}
                            <template x-if="question.type === 'multiple_choice'">
                                <div class="space-y-3">
                                    <template x-for="option in question.options" :key="option.id">
                                        <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                            <input type="radio" :name="`question_${index}`" :value="option.id"
                                                   x-model="answers[index]"
                                                   class="text-blue-600 focus:ring-blue-500">
                                            <span class="ml-3 text-gray-900 dark:text-white" x-text="option.text"></span>
                                        </label>
                                    </template>
                                </div>
                            </template>

                            {{-- True/False Options --}}
                            <template x-if="question.type === 'true_false'">
                                <div class="grid grid-cols-2 gap-4">
                                    <label class="flex items-center justify-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                        <input type="radio" :name="`question_${index}`" value="true"
                                               x-model="answers[index]"
                                               class="text-blue-600 focus:ring-blue-500">
                                        <span class="ml-3 text-gray-900 dark:text-white font-semibold">True</span>
                                    </label>
                                    <label class="flex items-center justify-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                        <input type="radio" :name="`question_${index}`" value="false"
                                               x-model="answers[index]"
                                               class="text-blue-600 focus:ring-blue-500">
                                        <span class="ml-3 text-gray-900 dark:text-white font-semibold">False</span>
                                    </label>
                                </div>
                            </template>

                            {{-- Essay Question --}}
                            <template x-if="question.type === 'essay'">
                                <div>
                                    <textarea x-model="answers[index]"
                                              :placeholder="`Write your answer here (150-300 words recommended)...`"
                                              rows="8"
                                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none">
                                    </textarea>
                                    <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                        <span x-text="(answers[index] || '').split(' ').filter(word => word.length > 0).length"></span> words
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Navigation Buttons --}}
                        <div class="flex items-center justify-between">
                            <button @click="prevQuestion()"
                                    x-show="currentQuestion > 0"
                                    class="flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Previous
                            </button>

                            <div class="text-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    Question <span x-text="currentQuestion + 1"></span> of <span x-text="$wire.quizData.questions.length"></span>
                                </span>
                            </div>

                            <template x-if="currentQuestion < $wire.quizData.questions.length - 1">
                                <button @click="nextQuestion()"
                                        class="flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                    Next
                                    <svg class="h-4 w-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </template>

                            <template x-if="currentQuestion === $wire.quizData.questions.length - 1">
                                <button @click="submitQuiz()"
                                        class="flex items-center px-8 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors font-semibold">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Submit Quiz
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        @endif
    </div>

    {{-- Quiz Results Phase --}}
    <div x-show="currentStep === 'results'" x-transition>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
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
                    <p class="text-xl text-gray-600 dark:text-gray-400">{{ $quizResults['results']['performance_level'] }}</p>
                </div>

                {{-- Score Summary --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="text-center p-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $quizResults['results']['percentage'] }}%</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Final Score</div>
                    </div>
                    <div class="text-center p-6 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $quizResults['results']['correct_answers'] }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Correct Answers</div>
                    </div>
                    <div class="text-center p-6 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $quizResults['results']['grade'] }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Letter Grade</div>
                    </div>
                    <div class="text-center p-6 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="text-3xl font-bold text-gray-600 dark:text-gray-400">
                            @if($quizResults['results']['time_taken'])
                                {{ gmdate('i:s', $quizResults['results']['time_taken']) }}
                            @else
                                --
                            @endif
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Time Taken</div>
                    </div>
                </div>

                {{-- Detailed Feedback --}}
                @if($quizResults['detailed_feedback'])
                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detailed Feedback</h3>
                        <div class="prose dark:prose-invert max-w-none">
                            <p class="text-gray-700 dark:text-gray-300">{{ $quizResults['detailed_feedback']['overall_performance'] }}</p>

                            @if(!empty($quizResults['detailed_feedback']['strengths']))
                                <h4 class="text-green-700 dark:text-green-300 font-semibold mt-4">Strengths:</h4>
                                <ul class="text-green-600 dark:text-green-400">
                                    @foreach($quizResults['detailed_feedback']['strengths'] as $strength)
                                        <li>{{ $strength }}</li>
                                    @endforeach
                                </ul>
                            @endif

                            @if(!empty($quizResults['detailed_feedback']['areas_for_improvement']))
                                <h4 class="text-orange-700 dark:text-orange-300 font-semibold mt-4">Areas for Improvement:</h4>
                                <ul class="text-orange-600 dark:text-orange-400">
                                    @foreach($quizResults['detailed_feedback']['areas_for_improvement'] as $area)
                                        <li>{{ $area }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Action Buttons --}}
                <div class="flex flex-wrap gap-4 justify-center">
                    <button @click="showExplanations = !showExplanations"
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <span x-text="showExplanations ? 'Hide' : 'Show'"></span> Explanations
                    </button>

                    <button @click="resetQuiz()"
                            class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                        Take Another Quiz
                    </button>

                    <button wire:click="exportResults"
                            class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        Export Results
                    </button>
                </div>

                {{-- Question Explanations --}}
                <div x-show="showExplanations" x-transition>
                    @if($quizResults['question_breakdown'])
                        <div class="space-y-6">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Question Review</h3>

                            @foreach($quizResults['question_breakdown'] as $index => $question)
                                <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-6 border-l-4 {{ $question['is_correct'] ? 'border-green-500' : 'border-red-500' }}">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="font-semibold text-gray-900 dark:text-white">Question {{ $question['question_number'] }}</h4>
                                        <div class="flex items-center space-x-2">
                                            @if($question['is_correct'])
                                                <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full text-sm">✓ Correct</span>
                                            @else
                                                <span class="px-2 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-full text-sm">✗ Incorrect</span>
                                            @endif
                                        </div>
                                    </div>

                                    <p class="text-gray-800 dark:text-gray-200 mb-3">{{ $question['question_text'] }}</p>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="font-medium text-gray-600 dark:text-gray-400">Your answer:</span>
                                            <span class="ml-2 {{ $question['is_correct'] ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                {{ $question['user_answer'] }}
                                            </span>
                                        </div>

                                        @if(!$question['is_correct'])
                                            <div>
                                                <span class="font-medium text-gray-600 dark:text-gray-400">Correct answer:</span>
                                                <span class="ml-2 text-green-600 dark:text-green-400">{{ $question['correct_answer'] }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    @if($question['feedback'])
                                        <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-600 rounded-lg">
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $question['feedback'] }}</p>
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
    <div wire:loading.flex wire:target="generateQuiz" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-8 max-w-md mx-4">
            <div class="text-center">
                <svg class="animate-spin h-12 w-12 text-blue-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor{{-- resources/views/livewire/book-quiz-interface.blade.php --}}
<div x-data="{
                            currentStep: 'setup', // setup, quiz, results
                    currentQuestion: 0,
                    answers: {},
                    timeRemaining: null,
                    quizTimer: null,
                    showExplanations: false,

                    startQuiz() {
                    this.currentStep = 'quiz';
                    this.currentQuestion = 0;
                    this.answers = {};
                    if (this.quizData?.estimated_duration) {
                    const minutes = parseInt(this.quizData.estimated_duration.split('-')[1] || '20');
                    this.timeRemaining = minutes * 60; // Convert to seconds
                    this.startTimer();
                    }
                    },

                    startTimer() {
                    this.quizTimer = setInterval(() => {
                    this.timeRemaining--;
                    if (this.timeRemaining <= 0) {
                    this.submitQuiz();
                    }
                    }, 1000);
                    },

                    stopTimer() {
                    if (this.quizTimer) {
                    clearInterval(this.quizTimer);
                    }
                    },

                    formatTime(seconds) {
                    const mins = Math.floor(seconds / 60);
                    const secs = seconds % 60;
                    return `${mins}:${secs.toString().padStart(2, '0')}`;
                    },

                    nextQuestion() {
                    if (this.currentQuestion < this.quizData.questions.length - 1) {
                    this.currentQuestion++;
                    }
                    },

                    prevQuestion() {
                    if (this.currentQuestion > 0) {
                    this.currentQuestion--;
                    }
                    },

                    submitQuiz() {
                    this.stopTimer();
                    const timeTaken = this.quizData?.estimated_duration ?
                    (parseInt(this.quizData.estimated_duration.split('-')[1] || '20') * 60) - this.timeRemaining :
                    null;

                    $wire.submitQuizAnswers(this.answers, timeTaken).then(() => {
                    this.currentStep = 'results';
                    });
                    },

                    resetQuiz() {
                    this.currentStep = 'setup';
                    this.currentQuestion = 0;
                    this.answers = {};
                    this.timeRemaining = null;
                    this.stopTimer();
                    $wire.resetQuiz();
                    }
                    }"
                    class="max-w-4xl mx-auto p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">

                    {{-- Quiz Setup Phase --}}
                    <div x-show="currentStep === 'setup'" x-transition>
                        <div class="mb-8">
                            <div class="flex items-center space-x-4 mb-6">
                                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                                    <svg class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Quiz Me!</h1>
                                    <p class="text-gray-600 dark:text-gray-400">Test your knowledge with AI-generated questions</p>
                                </div>
                            </div>
                        </div>

                        {{-- Book Selection --}}
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Select Book
                                    </label>
                                    <select wire:model.live="selectedBookId"
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        <option value="">Choose a book...</option>
                                        @foreach($availableBooks as $book)
                                            <option value="{{ $book->id }}">{{ $book->title }} by {{ $book->author }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                @if($selectedBook)
                                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-start space-x-4">
                                            @if($selectedBook->cover_image_url)
                                                <img src="{{ $selectedBook->cover_image_url }}" alt="{{ $selectedBook->title }}"
                                                     class="w-20 h-28 object-cover rounded shadow-md">
                                            @else
                                                <div class="w-20 h-28 bg-gray-300 dark:bg-gray-600 rounded flex items-center justify-center">
                                                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                            <div class="flex-1">
                                                <h3 class="font-semibold text-gray-900 dark:text-white">{{ $selectedBook->title }}</h3>
                                                <p class="text-gray-600 dark:text-gray-400">by {{ $selectedBook->author }}</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">{{ $selectedBook->genre }} • {{ $selectedBook->total_pages }} pages</p>
                                                @if($selectedBook->themes)
                                                    <div class="flex flex-wrap gap-1 mt-2">
                                                        @foreach(array_slice($selectedBook->themes, 0, 3) as $theme)
                                                            <span class="px-2 py-1 text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full">{{ $theme }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Chapter Selection --}}
                                    @if($bookChapters->isNotEmpty())
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

                                    {{-- Page Range --}}
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Start Page (Optional)
                                            </label>
                                            <input type="number" wire:model.live="pageStart" min="1" max="{{ $selectedBook->total_pages }}"
                                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                   placeholder="1">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                End Page (Optional)
                                            </label>
                                            <input type="number" wire:model.live="pageEnd" min="1" max="{{ $selectedBook->total_pages }}"
                                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                   placeholder="{{ $selectedBook->total_pages }}">
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Quiz Configuration --}}
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Question Type
                                    </label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <input type="radio" wire:model.live="questionType" value="multiple_choice" class="text-blue-600">
                                            <div class="ml-3">
                                                <div class="font-medium text-gray-900 dark:text-white">Multiple Choice</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">A, B, C, D options</div>
                                            </div>
                                        </label>
                                        <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50
<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-gray-600 dark:text-gray-400 text-lg font-medium">Generating your personalized quiz...</p>
                <p class="text-gray-500 dark:text-gray-500 text-sm mt-2">This may take 30-60 seconds</p>
            </div>
        </div>
    </div>
</div>


<style>
    .quiz-progress-bar {
        transition: width 0.5s ease-in-out;
    }

    .question-fade-enter {
        opacity: 0;
        transform: translateX(50px);
    }

    .question-fade-enter-active {
        opacity: 1;
        transform: translateX(0);
        transition: opacity 0.3s, transform 0.3s;
    }

    .quiz-option:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .achievement-badge {
        animation: bounceIn 0.6s ease-out;
    }

    @keyframes bounceIn {
        0% {
            opacity: 0;
            transform: scale(0.3);
        }
        50% {
            opacity: 1;
            transform: scale(1.05);
        }
        70% {
            transform: scale(0.9);
        }
        100% {
            opacity: 1;
            transform: scale(1);
        }
    }

    .results-summary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .timer-warning {
        animation: pulse 1s infinite;
    }

    @keyframes pulse {
        0% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
        100% {
            opacity: 1;
        }
    }
</style>

{{-- JavaScript for enhanced functionality --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('quizEnhancements', () => ({
            startTime: null,

            init() {
                this.startTime = Date.now();

                // Auto-save answers periodically
                setInterval(() => {
                    this.saveAnswersToSession();
                }, 30000); // Every 30 seconds

                // Warn before leaving page
                window.addEventListener('beforeunload', (e) => {
                    if (this.currentStep === 'quiz' && Object.keys(this.answers).length > 0) {
                        e.preventDefault();
                        e.returnValue = '';
                    }
                });
            },

            saveAnswersToSession() {
                if (typeof(Storage) !== "undefined" && this.currentStep === 'quiz') {
                    sessionStorage.setItem('quizAnswers', JSON.stringify(this.answers));
                    sessionStorage.setItem('quizProgress', this.currentQuestion);
                }
            },

            loadAnswersFromSession() {
                if (typeof(Storage) !== "undefined") {
                    const savedAnswers = sessionStorage.getItem('quizAnswers');
                    const savedProgress = sessionStorage.getItem('quizProgress');

                    if (savedAnswers) {
                        this.answers = JSON.parse(savedAnswers);
                    }

                    if (savedProgress) {
                        this.currentQuestion = parseInt(savedProgress);
                    }
                }
            },

            clearSessionData() {
                if (typeof(Storage) !== "undefined") {
                    sessionStorage.removeItem('quizAnswers');
                    sessionStorage.removeItem('quizProgress');
                }
            },

            getElapsedTime() {
                if (!this.startTime) return 0;
                return Math.floor((Date.now() - this.startTime) / 1000);
            },

            isAnswerSelected(questionIndex) {
                return this.answers.hasOwnProperty(questionIndex);
            },

            getCompletionPercentage() {
                const totalQuestions = this.quizData?.questions?.length || 0;
                const answeredQuestions = Object.keys(this.answers).length;
                return totalQuestions > 0 ? (answeredQuestions / totalQuestions) * 100 : 0;
            }
        }));
    });

    // Event listeners for Livewire events
    document.addEventListener('DOMContentLoaded', function() {
        window.addEventListener('quiz-generated', event => {
            // Scroll to quiz section
            document.getElementById('quiz-section')?.scrollIntoView({ behavior: 'smooth' });
        });

        window.addEventListener('quiz-reset', event => {
            // Clear any saved data
            if (typeof(Storage) !== "undefined") {
                sessionStorage.removeItem('quizAnswers');
                sessionStorage.removeItem('quizProgress');
            }
        });

        window.addEventListener('download-results', event => {
            // Handle results download
            const data = event.detail;
            const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = `quiz-results-${data.book}-${data.quiz_date}.json`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        });
    });
</script>                        <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
    <input type="radio" wire:model.live="questionType" value="true_false" class="text-blue-600">
    <div class="ml-3">
        <div class="font-medium text-gray-900 dark:text-white">True/False</div>
        <div class="text-sm text-gray-500 dark:text-gray-400">Simple T/F questions</div>
    </div>
</label>
<label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
    <input type="radio" wire:model.live="questionType" value="essay" class="text-blue-600">
    <div class="ml-3">
        <div class="font-medium text-gray-900 dark:text-white">Essay</div>
        <div class="text-sm text-gray-500 dark:text-gray-400">Written responses</div>
    </div>
</label>
<label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
    <input type="radio" wire:model.live="questionType" value="mixed" class="text-blue-600">
    <div class="ml-3">
        <div class="font-medium text-gray-900 dark:text-white">Mixed</div>
        <div class="text-sm text-gray-500 dark:text-gray-400">Variety of types</div>
    </div>
</label>
</div>
</div>

<div class="grid grid-cols-2 gap-4">
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

{{-- Advanced Options --}}
<div x-data="{ showAdvanced: false }">
    <button @click="showAdvanced = !showAdvanced"
            class="flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
        <svg class="h-4 w-4 mr-1 transform transition-transform" :class="{ 'rotate-180': showAdvanced }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <input type="checkbox" wire:model.live="includeQuotes" id="includeQuotes" class="rounded border-gray-300 dark:border-gray-600 text-blue-600">
            <label for="includeQuotes" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Include questions with book quotes</label>
        </div>
    </div>
</div>

{{-- Generate Quiz Button --}}
<div class="pt-4">
    <button wire:click="generateQuiz"
            wire:loading.attr="disabled"
            :disabled="!$wire.selectedBookId || $wire.isGenerating"
            class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-semibold py-4 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center">
                        <span wire:loading.remove wire:target="generateQuiz">
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
</div>
</div>
</div>

{{-- Error Messages --}}
@if($errors->any())
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
    <div x-show="currentStep === 'quiz'" x-transition>
        @if($quizData)
            {{-- Quiz Header --}}
            <div class="flex items-center justify-between mb-8 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $quizData['quiz_session']['book_title'] }}</h2>
                    <p class="text-gray-600 dark:text-gray-400">by {{ $quizData['quiz_session']['author'] }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-500">{{ $quizData['quiz_session']['context'] }}</p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" x-text="formatTime(timeRemaining)" x-show="timeRemaining"></div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Time Remaining</div>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div class="mb-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Progress</span>
                    <span class="text-sm text-gray-600 dark:text-gray-400" x-text="`Question ${currentQuestion + 1} of ${$wire.quizData.questions.length}`"></span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                         :style="`width: ${((currentQuestion + 1) / $wire.quizData.questions.length) * 100}%`"></div>
                </div>
            </div>

            {{-- Question Display --}}
            <div class="mb-8">
                <template x-for="(question, index) in $wire.quizData.questions" :key="index">
                    <div x-show="currentQuestion === index" x-transition>
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-6 mb-6">
                            <div class="flex items-start justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="`Question ${index + 1}`"></h3>
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-full" x-text="question.type.replace('_', ' ').toUpperCase()"></span>
                                    <span class="px-2 py-1 text-xs bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-full" x-text="`${question.points} pts`"></span>
                                </div>
                            </div>

                            <div class="mb-6">
                                <p class="text-gray-800 dark:text-gray-200 text-lg leading-relaxed" x-text="question.question"></p>
                            </div>

                            {{-- Multiple Choice Options --}}
                            <template x-if="question.type === 'multiple_choice'">
                                <div class="space-y-3">
                                    <template x-for="option in question.options" :key="option.id">
                                        <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                            <input type="radio" :name="`question_${index}`" :value="option.id"
                                                   x-model="answers[index]"
                                                   class="text-blue-600 focus:ring-blue-500">
                                            <span class="ml-3 text-gray-900 dark:text-white" x-text="option.text"></span>
                                        </label>
                                    </template>
                                </div>
                            </template>

                            {{-- True/False Options --}}
                            <template x-if="question.type === 'true_false'">
                                <div class="grid grid-cols-2 gap-4">
                                    <label class="flex items-center justify-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                        <input type="radio" :name="`question_${index}`" value="true"
                                               x-model="answers[index]"
                                               class="text-blue-600 focus:ring-blue-500">
                                        <span class="ml-3 text-gray-900 dark:text-white font-semibold">True</span>
                                    </label>
                                    <label class="flex items-center justify-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                        <input type="radio" :name="`question_${index}`" value="false"
                                               x-model="answers[index]"
                                               class="text-blue-600 focus:ring-blue-500">
                                        <span class="ml-3 text-gray-900 dark:text-white font-semibold">False</span>
                                    </label>
                                </div>
                            </template>

                            {{-- Essay Question --}}
                            <template x-if="question.type === 'essay'">
                                <div>
                                    <textarea x-model="answers[index]"
                                              :placeholder="`Write your answer here (150-300 words recommended)...`"
                                              rows="8"
                                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none">
                                    </textarea>
                                    <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                        <span x-text="(answers[index] || '').split(' ').filter(word => word.length > 0).length"></span> words
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Navigation Buttons --}}
                        <div class="flex items-center justify-between">
                            <button @click="prevQuestion()"
                                    x-show="currentQuestion > 0"
                                    class="flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Previous
                            </button>

                            <div class="text-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    Question <span x-text="currentQuestion + 1"></span> of <span x-text="$wire.quizData.questions.length"></span>
                                </span>
                            </div>

                            <template x-if="currentQuestion < $wire.quizData.questions.length - 1">
                                <button @click="nextQuestion()"
                                        class="flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                    Next
                                    <svg class="h-4 w-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </template>

                            <template x-if="currentQuestion === $wire.quizData.questions.length - 1">
                                <button @click="submitQuiz()"
                                        class="flex items-center px-8 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors font-semibold">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Submit Quiz
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        @endif
    </div>

    {{-- Quiz Results Phase --}}
    <div x-show="currentStep === 'results'" x-transition>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
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
                    <p class="text-xl text-gray-600 dark:text-gray-400">{{ $quizResults['results']['performance_level'] }}</p>
                </div>

                {{-- Score Summary --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="text-center p-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $quizResults['results']['percentage'] }}%</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Final Score</div>
                    </div>
                    <div class="text-center p-6 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $quizResults['results']['correct_answers'] }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Correct Answers</div>
                    </div>
                    <div class="text-center p-6 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $quizResults['results']['grade'] }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Letter Grade</div>
                    </div>
                    <div class="text-center p-6 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="text-3xl font-bold text-gray-600 dark:text-gray-400">
                            @if($quizResults['results']['time_taken'])
                                {{ gmdate('i:s', $quizResults['results']['time_taken']) }}
                            @else
                                --
                            @endif
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Time Taken</div>
                    </div>
                </div>

                {{-- Detailed Feedback --}}
                @if($quizResults['detailed_feedback'])
                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detailed Feedback</h3>
                        <div class="prose dark:prose-invert max-w-none">
                            <p class="text-gray-700 dark:text-gray-300">{{ $quizResults['detailed_feedback']['overall_performance'] }}</p>

                            @if(!empty($quizResults['detailed_feedback']['strengths']))
                                <h4 class="text-green-700 dark:text-green-300 font-semibold mt-4">Strengths:</h4>
                                <ul class="text-green-600 dark:text-green-400">
                                    @foreach($quizResults['detailed_feedback']['strengths'] as $strength)
                                        <li>{{ $strength }}</li>
                                    @endforeach
                                </ul>
                            @endif

                            @if(!empty($quizResults['detailed_feedback']['areas_for_improvement']))
                                <h4 class="text-orange-700 dark:text-orange-300 font-semibold mt-4">Areas for Improvement:</h4>
                                <ul class="text-orange-600 dark:text-orange-400">
                                    @foreach($quizResults['detailed_feedback']['areas_for_improvement'] as $area)
                                        <li>{{ $area }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Action Buttons --}}
                <div class="flex flex-wrap gap-4 justify-center">
                    <button @click="showExplanations = !showExplanations"
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <span x-text="showExplanations ? 'Hide' : 'Show'"></span> Explanations
                    </button>

                    <button @click="resetQuiz()"
                            class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                        Take Another Quiz
                    </button>

                    <button wire:click="exportResults"
                            class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        Export Results
                    </button>
                </div>

                {{-- Question Explanations --}}
                <div x-show="showExplanations" x-transition>
                    @if($quizResults['question_breakdown'])
                        <div class="space-y-6">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Question Review</h3>

                            @foreach($quizResults['question_breakdown'] as $index => $question)
                                <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-6 border-l-4 {{ $question['is_correct'] ? 'border-green-500' : 'border-red-500' }}">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="font-semibold text-gray-900 dark:text-white">Question {{ $question['question_number'] }}</h4>
                                        <div class="flex items-center space-x-2">
                                            @if($question['is_correct'])
                                                <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full text-sm">✓ Correct</span>
                                            @else
                                                <span class="px-2 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-full text-sm">✗ Incorrect</span>
                                            @endif
                                        </div>
                                    </div>

                                    <p class="text-gray-800 dark:text-gray-200 mb-3">{{ $question['question_text'] }}</p>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="font-medium text-gray-600 dark:text-gray-400">Your answer:</span>
                                            <span class="ml-2 {{ $question['is_correct'] ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                {{ $question['user_answer'] }}
                                            </span>
                                        </div>

                                        @if(!$question['is_correct'])
                                            <div>
                                                <span class="font-medium text-gray-600 dark:text-gray-400">Correct answer:</span>
                                                <span class="ml-2 text-green-600 dark:text-green-400">{{ $question['correct_answer'] }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    @if($question['feedback'])
                                        <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-600 rounded-lg">
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $question['feedback'] }}</p>
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
    <div wire:loading.flex wire:target="generateQuiz" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-8 max-w-md mx-4">
            <div class="text-center">
                <svg class="animate-spin h-12 w-12 text-blue-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor{{-- resources/views/livewire/book-quiz-interface.blade.php --}}
<div x-data="{
                            currentStep: 'setup', // setup, quiz, results
                    currentQuestion: 0,
                    answers: {},
                    timeRemaining: null,
                    quizTimer: null,
                    showExplanations: false,

                    startQuiz() {
                    this.currentStep = 'quiz';
                    this.currentQuestion = 0;
                    this.answers = {};
                    if (this.quizData?.estimated_duration) {
                    const minutes = parseInt(this.quizData.estimated_duration.split('-')[1] || '20');
                    this.timeRemaining = minutes * 60; // Convert to seconds
                    this.startTimer();
                    }
                    },

                    startTimer() {
                    this.quizTimer = setInterval(() => {
                    this.timeRemaining--;
                    if (this.timeRemaining <= 0) {
                    this.submitQuiz();
                    }
                    }, 1000);
                    },

                    stopTimer() {
                    if (this.quizTimer) {
                    clearInterval(this.quizTimer);
                    }
                    },

                    formatTime(seconds) {
                    const mins = Math.floor(seconds / 60);
                    const secs = seconds % 60;
                    return `${mins}:${secs.toString().padStart(2, '0')}`;
                    },

                    nextQuestion() {
                    if (this.currentQuestion < this.quizData.questions.length - 1) {
                    this.currentQuestion++;
                    }
                    },

                    prevQuestion() {
                    if (this.currentQuestion > 0) {
                    this.currentQuestion--;
                    }
                    },

                    submitQuiz() {
                    this.stopTimer();
                    const timeTaken = this.quizData?.estimated_duration ?
                    (parseInt(this.quizData.estimated_duration.split('-')[1] || '20') * 60) - this.timeRemaining :
                    null;

                    $wire.submitQuizAnswers(this.answers, timeTaken).then(() => {
                    this.currentStep = 'results';
                    });
                    },

                    resetQuiz() {
                    this.currentStep = 'setup';
                    this.currentQuestion = 0;
                    this.answers = {};
                    this.timeRemaining = null;
                    this.stopTimer();
                    $wire.resetQuiz();
                    }
                    }"
                    class="max-w-4xl mx-auto p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">

                    {{-- Quiz Setup Phase --}}
                    <div x-show="currentStep === 'setup'" x-transition>
                        <div class="mb-8">
                            <div class="flex items-center space-x-4 mb-6">
                                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                                    <svg class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Quiz Me!</h1>
                                    <p class="text-gray-600 dark:text-gray-400">Test your knowledge with AI-generated questions</p>
                                </div>
                            </div>
                        </div>

                        {{-- Book Selection --}}
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Select Book
                                    </label>
                                    <select wire:model.live="selectedBookId"
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        <option value="">Choose a book...</option>
                                        @foreach($availableBooks as $book)
                                            <option value="{{ $book->id }}">{{ $book->title }} by {{ $book->author }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                @if($selectedBook)
                                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-start space-x-4">
                                            @if($selectedBook->cover_image_url)
                                                <img src="{{ $selectedBook->cover_image_url }}" alt="{{ $selectedBook->title }}"
                                                     class="w-20 h-28 object-cover rounded shadow-md">
                                            @else
                                                <div class="w-20 h-28 bg-gray-300 dark:bg-gray-600 rounded flex items-center justify-center">
                                                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                            <div class="flex-1">
                                                <h3 class="font-semibold text-gray-900 dark:text-white">{{ $selectedBook->title }}</h3>
                                                <p class="text-gray-600 dark:text-gray-400">by {{ $selectedBook->author }}</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">{{ $selectedBook->genre }} • {{ $selectedBook->total_pages }} pages</p>
                                                @if($selectedBook->themes)
                                                    <div class="flex flex-wrap gap-1 mt-2">
                                                        @foreach(array_slice($selectedBook->themes, 0, 3) as $theme)
                                                            <span class="px-2 py-1 text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full">{{ $theme }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Chapter Selection --}}
                                    @if($bookChapters->isNotEmpty())
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

                                    {{-- Page Range --}}
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Start Page (Optional)
                                            </label>
                                            <input type="number" wire:model.live="pageStart" min="1" max="{{ $selectedBook->total_pages }}"
                                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                   placeholder="1">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                End Page (Optional)
                                            </label>
                                            <input type="number" wire:model.live="pageEnd" min="1" max="{{ $selectedBook->total_pages }}"
                                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                   placeholder="{{ $selectedBook->total_pages }}">
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Quiz Configuration --}}
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Question Type
                                    </label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <input type="radio" wire:model.live="questionType" value="multiple_choice" class="text-blue-600">
                                            <div class="ml-3">
                                                <div class="font-medium text-gray-900 dark:text-white">Multiple Choice</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">A, B, C, D options</div>
                                            </div>
                                        </label>
                                        <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50{{-- Continuation of the Question Explanations section --}}
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1 0 01.293.707l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Question-by-Question Review
                            </h3>
                            @foreach($quizResults['question_breakdown'] as $index => $detail)
                                <div class="bg-white dark:bg-gray-700 rounded-xl p-6 border border-gray-200 dark:border-gray-600 question-review-card">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex items-center justify-center w-10 h-10 rounded-full font-bold text-white"
                                                 :class="{'bg-green-500': {{ $detail['is_correct'] ? 'true' : 'false' }}, 'bg-red-500': {{ $detail['is_correct'] ? 'false' : 'true' }}}">
                                                {{ $detail['question_number'] }}
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900 dark:text-white">
                                                    {{ ucfirst(str_replace('_', ' ', $detail['question_type'])) }} Question
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $detail['points_earned'] }}/{{ $detail['points_possible'] }} points
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            @if($detail['is_correct'])
                                                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Correct
                                                </div>
                                            @else
                                                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    Incorrect
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Question Text --}}
                                    <div class="mb-6">
                                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Question:</h4>
                                        <div class="prose dark:prose-invert max-w-none">
                                            <p class="text-gray-700 dark:text-gray-300">{{ $detail['question_text'] }}</p>
                                        </div>
                                    </div>

                                    {{-- Answer Section --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                        <div>
                                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">Your Answer:</h5>
                                            <div class="p-4 rounded-lg border-2"
                                                 :class="{'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-900/20': {{ $detail['is_correct'] ? 'true' : 'false' }}, 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20': {{ $detail['is_correct'] ? 'false' : 'true' }}}">
                                                <span class="text-gray-800 dark:text-gray-200 font-medium">
                                                    {{ $detail['user_answer'] ?? 'No answer provided' }}
                                                </span>
                                            </div>
                                        </div>
                                        @if($detail['question_type'] !== 'essay')
                                            <div>
                                                <h5 class="font-medium text-gray-900 dark:text-white mb-2">Correct Answer:</h5>
                                                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border-2 border-blue-200 dark:border-blue-800">
                                                    <span class="text-blue-800 dark:text-blue-200 font-medium">
                                                        {{ $detail['correct_answer'] }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Explanation --}}
                                    @if($detail['feedback'])
                                        <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                                            <h5 class="font-medium text-gray-900 dark:text-white mb-2 flex items-center">
                                                <svg class="h-5 w-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Explanation:
                                            </h5>
                                            <div class="prose dark:prose-invert max-w-none">
                                                <p class="text-gray-700 dark:text-gray-300">{{ $detail['feedback'] }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        @endif
    </div>

    {{-- Loading States --}}
    <div wire:loading.flex wire:target="generateQuiz" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-8 max-w-md mx-4 text-center">
            <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Generating Your Quiz</h3>
            <p class="text-gray-600 dark:text-gray-400">Our AI is creating personalized questions based on your book selection...</p>
            <div class="mt-4 text-sm text-gray-500 dark:text-gray-500">This may take 10-30 seconds</div>
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

{{-- JavaScript Event Listeners --}}
<script>
    document.addEventListener('alpine:init', () => {
        // Listen for quiz events
        Livewire.on('quiz-generated', () => {
            // Scroll to quiz section when generated
            document.getElementById('quiz-section')?.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        });

        Livewire.on('quiz-reset', () => {
            // Reset any client-side state
            console.log('Quiz reset');
        });

        Livewire.on('download-results', (data) => {
            // Handle results download
            const blob = new Blob([JSON.stringify(data, null, 2)], {
                type: 'application/json'
            });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `quiz-results-${new Date().toISOString().split('T')[0]}.json`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        });

        // Keyboard shortcuts for quiz navigation
        document.addEventListener('keydown', (e) => {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;

            const quizComponent = document.querySelector('[x-data]').__x.$data;
            if (quizComponent.currentStep === 'quiz') {
                switch(e.key) {
                    case 'ArrowRight':
                        e.preventDefault();
                        quizComponent.nextQuestion();
                        break;
                    case 'ArrowLeft':
                        e.preventDefault();
                        quizComponent.prevQuestion();
                        break;
                    case 'Enter':
                        if (e.ctrlKey) {
                            e.preventDefault();
                            quizComponent.submitQuiz();
                        }
                        break;
                }
            }
        });

        // Auto-save quiz progress (optional)
        window.addEventListener('beforeunload', (e) => {
            const quizComponent = document.querySelector('[x-data]').__x.$data;
            if (quizComponent.currentStep === 'quiz' && Object.keys(quizComponent.answers).length > 0) {
                localStorage.setItem('quiz_progress', JSON.stringify({
                    currentQuestion: quizComponent.currentQuestion,
                    answers: quizComponent.answers,
                    timestamp: Date.now()
                }));
            }
        });

        // Restore quiz progress on page load (optional)
        const savedProgress = localStorage.getItem('quiz_progress');
        if (savedProgress) {
            try {
                const progress = JSON.parse(savedProgress);
                // Only restore if less than 1 hour old
                if (Date.now() - progress.timestamp < 3600000) {
                    const quizComponent = document.querySelector('[x-data]').__x.$data;
                    if (confirm('Would you like to restore your previous quiz progress?')) {
                        quizComponent.currentQuestion = progress.currentQuestion;
                        quizComponent.answers = progress.answers;
                        quizComponent.currentStep = 'quiz';
                    }
                }
            } catch (e) {
                localStorage.removeItem('quiz_progress');
            }
        }
    });
</script>
