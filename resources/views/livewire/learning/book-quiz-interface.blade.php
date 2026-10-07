<x-exam.page>
    <x-exam.header
        eyebrow="QUIZ HALL · AI-POWERED LEARNING"
        title="Quiz Generator"
        sub="Select a book or upload content, and the messengers will draft a personalised quiz for you."
        :lines="['QUIZ', 'HALL']"
    />

    {{-- Tabs / back control --}}
    @if($activeTab === 'results')
        <div class="mb-8">
            <x-exam.link-button wire:click="backToHistory">← BACK TO HISTORY</x-exam.link-button>
        </div>
    @else
        <x-exam.tabs
            :tabs="['new' => 'New Quiz', 'history' => 'Quiz History']"
            :active="$activeTab"
            property="activeTab"
        />
    @endif

    {{-- ==================== NEW QUIZ TAB ==================== --}}
    @if($activeTab === 'new')

        {{-- Messenger warnings --}}
        @if(!$canGenerateQuiz)
            <div class="mb-6 space-y-4">
                <x-exam.callout>
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                        <div class="flex-1">
                            <p class="font-display text-base font-bold text-ink">Get started with AI Messengers</p>
                            <p class="mt-1 text-sm text-ink-soft">You don't have an active messenger subscription. Activate a package to start generating quizzes with AI assistance.</p>
                        </div>
                        <a href="{{ route('token-subscriptions.create') }}"
                           class="inline-flex items-center justify-center whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi">
                            Get Messengers
                        </a>
                    </div>
                </x-exam.callout>

                @if($tokenWarningMessage === 'depleted')
                    <x-exam.callout tone="danger">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                            <div class="flex-1">
                                <p class="font-display text-base font-bold text-ink">Messengers exhausted</p>
                                <p class="mt-1 text-sm text-ink-soft">Your messenger balance is fully depleted. Top up now to continue generating quizzes and resume your learning journey.</p>
                            </div>
                            <a href="{{ route('token-subscriptions.create') }}"
                               class="inline-flex items-center justify-center whitespace-nowrap rounded bg-accent px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-white transition hover:-translate-y-px hover:opacity-90">
                                Buy More Messengers
                            </a>
                        </div>
                    </x-exam.callout>
                @elseif($tokenWarningMessage === 'expired')
                    <x-exam.callout tone="warn">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                            <div class="flex-1">
                                <p class="font-display text-base font-bold text-ink">Subscription expired</p>
                                <p class="mt-1 text-sm text-ink-soft">Your messenger subscription has expired. Purchase a new package to continue generating quizzes with AI assistance.</p>
                            </div>
                            <a href="{{ route('token-subscriptions.create') }}"
                               class="inline-flex items-center justify-center whitespace-nowrap rounded bg-warn px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-white transition hover:-translate-y-px hover:opacity-90">
                                Renew Subscription
                            </a>
                        </div>
                    </x-exam.callout>
                @elseif($tokenWarningMessage === 'insufficient')
                    <x-exam.callout tone="warn">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                            <div class="flex-1">
                                <p class="font-display text-base font-bold text-ink">Insufficient messengers</p>
                                <p class="mt-1 text-sm text-ink-soft">You don't have enough messengers to generate a quiz. Top up now to keep learning without interruption.</p>
                            </div>
                            <a href="{{ route('token-subscriptions.create') }}"
                               class="inline-flex items-center justify-center whitespace-nowrap rounded bg-warn px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-white transition hover:-translate-y-px hover:opacity-90">
                                Top Up Messengers
                            </a>
                        </div>
                    </x-exam.callout>
                @endif
            </div>
        @endif

        <div class="{{ !$canGenerateQuiz ? 'pointer-events-none opacity-50' : '' }}">

            {{-- ========== Quiz setup phase ========== --}}
            <div x-data="{ showAdvanced: false }" x-show="!$wire.quizData && !$wire.quizResults" x-transition>
                <x-exam.card :padded="false">
                    <x-slot:header>
                        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">QUIZ SETUP · ANSWER SHEET PREPARATION</p>
                        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted"><span class="font-bold text-accent">*</span> REQUIRED</p>
                    </x-slot:header>

                    <div class="space-y-6 p-5">

                        {{-- Content source tabs --}}
                        <div class="flex flex-wrap gap-1 border-b border-line">
                            @foreach(['book' => 'Select Book', 'upload' => 'Upload Content'] as $src => $srcLabel)
                                <button type="button" wire:click="$set('contentSourceTab', '{{ $src }}')"
                                        class="relative -mb-px rounded-t border px-4 py-2 font-mono text-[11px] font-semibold uppercase tracking-[0.12em] transition
                                            {{ $contentSourceTab === $src ? 'border-line border-b-surface bg-surface text-ink' : 'border-transparent text-muted hover:text-ink' }}">
                                    @if($contentSourceTab === $src)<span class="absolute inset-x-0 top-0 h-0.5 bg-accent"></span>@endif
                                    {{ $srcLabel }}
                                </button>
                            @endforeach
                        </div>

                        {{-- BOOK SOURCE --}}
                        @if($contentSourceTab === 'book')
                            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                                <x-exam.field bare label="Select Book">
                                    <x-exam.select wire:model="selectedBookId" placeholder="Choose a book…">
                                        @foreach($availableBooks as $book)
                                            <option value="{{ $book->id }}">{{ $book->title }} by {{ $book->author_name }}</option>
                                        @endforeach
                                    </x-exam.select>
                                </x-exam.field>

                                <x-exam.field bare label="Subject" required>
                                    <livewire:common.searchable-multi-select
                                        :items="$availableSubjects"
                                        :selected="$selectedSubjectId ? [$selectedSubjectId] : []"
                                        placeholder="Search and select a subject..."
                                        :multiple="false"
                                        name="selectedSubjectId"
                                        value-key="id"
                                        label-key="display_name"
                                        wire:key="subject-select-book"
                                    />
                                    <p class="mt-1.5 text-xs text-muted">Select the subject this quiz is related to.</p>
                                </x-exam.field>
                            </div>

                            @if($selectedBook)
                                <div class="rounded-md border border-line bg-surface-2 p-4">
                                    <p class="font-display text-base font-bold text-ink">{{ $selectedBook->title }}</p>
                                    <div class="mt-2 grid grid-cols-1 gap-2 font-mono text-[11px] tracking-wide text-muted sm:grid-cols-3">
                                        <p><span class="text-ink-soft">AUTHOR:</span> {{ $selectedBook->author_name }}</p>
                                        <p><span class="text-ink-soft">GENRE:</span> {{ $selectedBook->genre ?? 'N/A' }}</p>
                                        <p><span class="text-ink-soft">PAGES:</span> {{ $selectedBook->pages ?? 'N/A' }}</p>
                                    </div>
                                </div>

                                @if(!empty($bookChapters))
                                    <x-exam.field bare label="Chapter (optional)">
                                        <x-exam.select wire:model="selectedChapterId" placeholder="All chapters">
                                            @foreach($bookChapters as $chapter)
                                                <option value="{{ $chapter->id }}">Chapter {{ $chapter->chapter_number }}: {{ $chapter->title }}</option>
                                            @endforeach
                                        </x-exam.select>
                                    </x-exam.field>
                                @endif

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <x-exam.field bare label="Start Page (optional)">
                                        <x-exam.input type="number" min="1" wire:model="pageStart" placeholder="e.g., 1" />
                                    </x-exam.field>
                                    <x-exam.field bare label="End Page (optional)">
                                        <x-exam.input type="number" min="1" wire:model="pageEnd" placeholder="e.g., 50" />
                                    </x-exam.field>
                                </div>
                            @endif

                        {{-- UPLOAD SOURCE --}}
                        @else
                            <div class="space-y-4">
                                <x-exam.field bare label="Subject" required>
                                    <livewire:common.searchable-multi-select
                                        :items="$availableSubjects"
                                        :selected="$selectedSubjectId ? [$selectedSubjectId] : []"
                                        placeholder="Search and select a subject..."
                                        :multiple="false"
                                        name="selectedSubjectId"
                                        value-key="id"
                                        label-key="display_name"
                                        wire:key="subject-select-upload"
                                    />
                                    <p class="mt-1.5 text-xs text-muted">Select the subject this quiz is related to.</p>
                                </x-exam.field>

                                <x-exam.field bare label="Upload Content File">
                                    <div class="flex justify-center rounded-md border-[1.5px] border-dashed border-line-strong px-6 pb-6 pt-5 transition hover:border-primary hover:bg-surface-2">
                                        <div class="space-y-1 text-center">
                                            <div class="flex items-center justify-center text-sm text-ink-soft">
                                                <label class="relative cursor-pointer font-semibold text-primary transition hover:text-primary-hi">
                                                    <span>Upload a file</span>
                                                    <input type="file" class="sr-only" wire:model="uploadedFile" accept=".pdf,.doc,.docx,.txt">
                                                </label>
                                                <p class="pl-1">or drag and drop</p>
                                            </div>
                                            <p class="font-mono text-[10px] tracking-wider text-muted">PDF, DOC, DOCX, TXT · UP TO 10MB</p>
                                        </div>
                                    </div>
                                </x-exam.field>

                                @if($fileName)
                                    <x-exam.callout>
                                        <div class="flex items-start gap-3">
                                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-primary" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                            <div>
                                                <p class="text-sm font-semibold text-ink">File uploaded successfully</p>
                                                <p class="mt-0.5 text-sm text-ink-soft">{{ $fileName }}</p>
                                                @if($fileContent)
                                                    <p class="mt-1 font-mono text-[10px] tracking-wider text-muted">CONTENT EXTRACTED · {{ strlen($fileContent) }} CHARACTERS</p>
                                                @endif
                                            </div>
                                        </div>
                                    </x-exam.callout>
                                @endif
                            </div>
                        @endif

                        {{-- Quiz settings --}}
                        <div class="border-t border-dashed border-line pt-5">
                            <p class="mb-4 font-mono text-[10.5px] tracking-[0.14em] text-muted">QUIZ SETTINGS</p>

                            <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                                <x-exam.field bare label="Question Type">
                                    <x-exam.select wire:model.live="questionType">
                                        <option value="multiple_choice">Multiple Choice</option>
                                        <option value="true_false">True/False</option>
                                        <option value="essay">Essay</option>
                                        <option value="mixed">Mixed</option>
                                    </x-exam.select>
                                </x-exam.field>

                                <x-exam.field bare label="Number of Questions">
                                    <div class="flex gap-2"
                                         x-data="{
                                             isCustom: @js($this->questionCount === 'custom'),
                                             updateCustomState(value) { this.isCustom = value === 'custom'; }
                                         }"
                                         x-init="$watch('$wire.questionCount', value => updateCustomState(value))">
                                        <x-exam.select wire:model.live="questionCount" x-on:change="updateCustomState($event.target.value)">
                                            <option value="5">5 questions</option>
                                            <option value="10" selected>10 questions</option>
                                            <option value="15">15 questions</option>
                                            <option value="20">20 questions</option>
                                            <option value="custom">Custom</option>
                                        </x-exam.select>
                                        <div class="w-1/2" x-show="isCustom" x-transition>
                                            <x-exam.input type="number" min="1" max="50" wire:model.live.debounce.500ms="customQuestionCount" placeholder="1–50" />
                                        </div>
                                    </div>
                                    <p class="mt-1.5 text-xs text-muted">Presets, or choose "Custom" for your own value (1–50).</p>
                                </x-exam.field>

                                <x-exam.field bare label="Difficulty">
                                    <x-exam.select wire:model.live="difficulty">
                                        <option value="easy">Easy</option>
                                        <option value="medium" selected>Medium</option>
                                        <option value="hard">Hard</option>
                                    </x-exam.select>
                                </x-exam.field>
                            </div>
                        </div>

                        {{-- Advanced options --}}
                        <div class="border-t border-dashed border-line pt-4">
                            <button type="button" @click="showAdvanced = !showAdvanced"
                                    class="flex items-center gap-1.5 font-mono text-[11px] font-semibold uppercase tracking-[0.12em] text-primary transition hover:text-primary-hi">
                                <svg class="h-3.5 w-3.5 transition-transform" :class="{ 'rotate-180': showAdvanced }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                Advanced Options
                            </button>
                            <div x-show="showAdvanced" x-transition class="mt-4">
                                <x-exam.field bare label="Focus Topics (optional)">
                                    <x-exam.input wire:model.live="focusTopics" placeholder="e.g., character development, themes, plot" />
                                    <p class="mt-1.5 text-xs text-muted">Separate topics with commas.</p>
                                </x-exam.field>
                            </div>
                        </div>

                        {{-- Generate --}}
                        <div class="border-t border-line pt-5">
                            <button wire:click="generateQuiz"
                                    :disabled="$wire.isGenerating || !{{ $canGenerateQuiz ? 'true' : 'false' }}"
                                    class="inline-flex w-full items-center justify-center rounded bg-primary px-6 py-3 text-sm font-semibold tracking-wide text-on-primary shadow-[0_1px_0_rgba(20,24,34,0.2),0_8px_18px_-10px_rgba(20,24,34,0.5)] transition hover:-translate-y-px hover:bg-primary-hi disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0 motion-reduce:transition-none">
                                <span wire:loading.remove wire:target="generateQuiz">{{ $canGenerateQuiz ? 'Generate Quiz' : 'Insufficient Messengers' }}</span>
                                <span wire:loading wire:target="generateQuiz">
                                    <span class="mr-2 inline-block h-3.5 w-3.5 animate-spin rounded-full border-2 border-current border-t-transparent align-[-2px]"></span>Generating Quiz…
                                </span>
                            </button>
                            <p class="mt-2 text-center font-mono text-[10px] tracking-wider text-muted">THIS MAY TAKE 30–60 SECONDS</p>
                        </div>
                    </div>
                </x-exam.card>

                {{-- Validation errors --}}
                @if(isset($errors) && count($errors) > 0)
                    <x-exam.callout tone="danger" class="mt-6">
                        <div class="flex gap-3">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                            <div>
                                <p class="text-sm font-semibold text-ink">Please fix the following errors:</p>
                                <ul class="mt-2 list-inside list-disc text-sm text-ink-soft">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </x-exam.callout>
                @endif
            </div>

            {{-- ========== Quiz taking phase ========== --}}
            @if($quizData && !$quizResults)
                <div x-data="{
                        currentQuestion: 0,
                        answers: {},
                        elapsedSeconds: 0,
                        quizTimer: null,
                        showExplanations: false,
                        quizData: @js($quizData),
                        init() {
                            const savedAnswers = sessionStorage.getItem('quizAnswers');
                            if (savedAnswers) { this.answers = JSON.parse(savedAnswers); }
                            const savedTime = sessionStorage.getItem('quizElapsedTime');
                            if (savedTime) { this.elapsedSeconds = parseInt(savedTime, 10); }
                            this.startTimer();
                        },
                        startTimer() {
                            if (this.quizTimer) { clearInterval(this.quizTimer); }
                            this.quizTimer = setInterval(() => {
                                this.elapsedSeconds++;
                                sessionStorage.setItem('quizElapsedTime', this.elapsedSeconds.toString());
                            }, 1000);
                        },
                        stopTimer() {
                            if (this.quizTimer) { clearInterval(this.quizTimer); this.quizTimer = null; }
                        },
                        formatTime(seconds) {
                            const hrs = Math.floor(seconds / 3600);
                            const mins = Math.floor((seconds % 3600) / 60);
                            const secs = seconds % 60;
                            if (hrs > 0) { return `${hrs.toString().padStart(2,'0')}:${mins.toString().padStart(2,'0')}:${secs.toString().padStart(2,'0')}`; }
                            return `${mins.toString().padStart(2,'0')}:${secs.toString().padStart(2,'0')}`;
                        },
                        selectAnswer(questionIndex, option) { this.answers[questionIndex] = option; this.saveAnswers(); },
                        isAnswerSelected(questionIndex, option) { return this.answers[questionIndex] === option; },
                        saveAnswers() { sessionStorage.setItem('quizAnswers', JSON.stringify(this.answers)); },
                        nextQuestion() { if (this.currentQuestion < (this.quizData.questions.length - 1)) { this.currentQuestion++; } },
                        prevQuestion() { if (this.currentQuestion > 0) { this.currentQuestion--; } },
                        getProgressPercentage() {
                            const total = this.quizData.questions.length || 1;
                            return ((this.currentQuestion + 1) / total) * 100;
                        },
                        submitQuiz() {
                            this.stopTimer();
                            $wire.submitQuizAnswers(this.answers, this.elapsedSeconds);
                            sessionStorage.removeItem('quizAnswers');
                            sessionStorage.removeItem('quizElapsedTime');
                        }
                    }"
                     x-transition id="quiz-section">

                    {{-- Quiz header --}}
                    <div class="mb-6 flex flex-col justify-between gap-4 rounded-md border border-line bg-surface p-4 shadow-[0_1px_2px_rgba(20,24,34,0.04)] md:flex-row md:items-center">
                        <div class="min-w-0">
                            <h2 class="font-display text-xl font-bold text-ink">{{ $selectedBook?->title ?? $fileName ?? 'Quiz' }}</h2>
                            <p class="mt-0.5 font-mono text-[10.5px] tracking-wider text-muted">BY {{ strtoupper($selectedBook?->author_name ?? 'User Content') }}</p>
                        </div>
                        <div class="flex items-center gap-6">
                            <div class="text-center">
                                <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">Time Elapsed</p>
                                <p class="timer-display mt-0.5 flex items-center justify-center font-mono text-lg font-bold text-accent">
                                    <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span x-text="formatTime(elapsedSeconds)">00:00</span>
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">Question</p>
                                <p class="mt-0.5 font-display text-lg font-bold text-primary"><span x-text="currentQuestion + 1"></span> / {{ count($quizData['questions']) }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Progress --}}
                    <div class="mb-8">
                        <div class="mb-1.5 flex items-center justify-between font-mono text-[10.5px] tracking-wider text-muted">
                            <span>PROGRESS</span>
                            <span x-text="`${Math.round(getProgressPercentage())}%`"></span>
                        </div>
                        <div class="h-1.5 w-full overflow-hidden rounded-full bg-line">
                            <div class="h-full rounded-full bg-primary transition-all duration-300" :style="`width: ${getProgressPercentage()}%`"></div>
                        </div>
                    </div>

                    {{-- Current question --}}
                    <div class="mb-8 rounded-md border border-line bg-surface p-6 shadow-[0_1px_2px_rgba(20,24,34,0.04),0_18px_40px_-30px_rgba(20,24,34,0.25)]">
                        <div class="mb-6">
                            <span class="inline-block rounded-[3px] bg-accent/10 px-2 py-0.5 font-mono text-[10.5px] tracking-wider text-accent">QUESTION <span x-text="currentQuestion + 1"></span></span>
                            <h3 class="mt-3 text-lg font-semibold leading-relaxed text-ink"
                                x-html="window.renderMarkdownWithMath(quizData.questions[currentQuestion].question || '')"></h3>
                        </div>

                        <div class="space-y-3">
                            <template x-if="quizData.questions[currentQuestion].type === 'multiple_choice'">
                                <div class="space-y-3">
                                    <template x-for="(option, index) in quizData.questions[currentQuestion].options" :key="index">
                                        <label @click="selectAnswer(currentQuestion, option)"
                                               class="flex cursor-pointer items-center rounded-md border p-4 transition-all"
                                               :class="isAnswerSelected(currentQuestion, option)
                                                   ? 'border-primary bg-primary/5 ring-2 ring-primary/20'
                                                   : 'border-line-strong hover:border-ink-soft hover:bg-surface-2'">
                                            <input type="radio" :checked="isAnswerSelected(currentQuestion, option)" class="h-4 w-4 accent-[rgb(var(--c-primary))]">
                                            <span class="mx-2.5 font-mono text-sm font-semibold text-ink" x-text="String.fromCharCode(65 + index) + '.'"></span>
                                            <span class="text-ink" x-html="window.renderMarkdownWithMath(option || '')"></span>
                                        </label>
                                    </template>
                                </div>
                            </template>

                            <template x-if="quizData.questions[currentQuestion].type === 'true_false'">
                                <div class="space-y-3">
                                    <label @click="selectAnswer(currentQuestion, 'True')"
                                           class="flex cursor-pointer items-center rounded-md border p-4 transition-all"
                                           :class="isAnswerSelected(currentQuestion, 'True')
                                               ? 'border-primary bg-primary/5 ring-2 ring-primary/20'
                                               : 'border-line-strong hover:border-ink-soft hover:bg-surface-2'">
                                        <input type="radio" :checked="isAnswerSelected(currentQuestion, 'True')" class="h-4 w-4 accent-[rgb(var(--c-primary))]">
                                        <span class="mx-2.5 font-mono text-sm font-semibold text-ink">A.</span>
                                        <span class="font-medium text-ink">True</span>
                                    </label>
                                    <label @click="selectAnswer(currentQuestion, 'False')"
                                           class="flex cursor-pointer items-center rounded-md border p-4 transition-all"
                                           :class="isAnswerSelected(currentQuestion, 'False')
                                               ? 'border-primary bg-primary/5 ring-2 ring-primary/20'
                                               : 'border-line-strong hover:border-ink-soft hover:bg-surface-2'">
                                        <input type="radio" :checked="isAnswerSelected(currentQuestion, 'False')" class="h-4 w-4 accent-[rgb(var(--c-primary))]">
                                        <span class="mx-2.5 font-mono text-sm font-semibold text-ink">B.</span>
                                        <span class="font-medium text-ink">False</span>
                                    </label>
                                </div>
                            </template>

                            <template x-if="quizData.questions[currentQuestion].type === 'essay'">
                                <div>
                                    <textarea x-model="answers[currentQuestion]" rows="6"
                                              placeholder="Type your detailed answer here…"
                                              class="w-full rounded border border-line-strong bg-surface px-3 py-2.5 text-sm text-ink placeholder:text-muted transition hover:border-ink-soft focus:border-primary focus:outline-none focus:ring-[3px] focus:ring-primary/20"></textarea>
                                    <p class="mt-2 font-mono text-[10.5px] tracking-wider text-muted">
                                        WORD COUNT: <span x-text="(answers[currentQuestion] || '').split(/\s+/).filter(word => word.length > 0).length">0</span>
                                    </p>
                                </div>
                            </template>
                        </div>

                        {{-- Navigation --}}
                        <div class="mt-8 flex justify-between">
                            <button @click="prevQuestion()" :disabled="currentQuestion === 0"
                                    class="inline-flex items-center gap-1.5 rounded border border-line-strong px-5 py-2.5 text-sm font-semibold text-ink-soft transition hover:bg-surface-2 hover:text-ink disabled:cursor-not-allowed disabled:opacity-50">
                                ← Previous
                            </button>
                            <template x-if="currentQuestion < quizData.questions.length - 1">
                                <button @click="nextQuestion()"
                                        class="inline-flex items-center gap-1.5 rounded bg-primary px-5 py-2.5 text-sm font-semibold text-on-primary transition hover:bg-primary-hi">
                                    Next →
                                </button>
                            </template>
                            <template x-if="currentQuestion === quizData.questions.length - 1">
                                <button @click="submitQuiz()"
                                        class="inline-flex items-center gap-1.5 rounded bg-ok px-5 py-2.5 text-sm font-semibold text-white transition hover:opacity-90">
                                    ✓ Submit Quiz
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ========== Quiz results phase ========== --}}
            <div x-data="{ showExplanations: false }" x-show="$wire.quizResults" x-transition>
                @if($quizResults)
                    <div class="space-y-8">
                        {{-- Results hero --}}
                        <div class="text-center">
                            <div class="mb-4 flex justify-center">
                                @if($quizResults['results']['percentage'] >= 90)
                                    <div class="grid h-20 w-20 place-items-center rounded-full bg-ok/10 ring-1 ring-inset ring-ok/30">
                                        <svg class="h-10 w-10 text-ok" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                @elseif($quizResults['results']['percentage'] >= 70)
                                    <div class="grid h-20 w-20 place-items-center rounded-full bg-warn/10 ring-1 ring-inset ring-warn/30">
                                        <svg class="h-10 w-10 text-warn" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                @else
                                    <div class="grid h-20 w-20 place-items-center rounded-full bg-accent/10 ring-1 ring-inset ring-accent/30">
                                        <svg class="h-10 w-10 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                @endif
                            </div>
                            <h2 class="font-display text-3xl font-bold text-ink">Quiz Complete!</h2>
                            <p class="mt-1 text-lg text-ink-soft">{{ $quizResults['detailed_feedback']['overall_performance'] }}</p>
                            <div class="mx-auto mt-6 max-w-2xl rounded-md border border-primary/25 bg-primary/5 p-6">
                                <p class="font-display text-5xl font-bold text-primary">{{ $quizResults['results']['percentage'] }}%</p>
                                <p class="mt-2 font-mono text-[11px] tracking-wider text-muted">
                                    YOU ANSWERED {{ $quizResults['results']['correct_answers'] }} OUT OF {{ $quizResults['results']['total_questions'] }} QUESTIONS CORRECTLY
                                </p>
                            </div>
                        </div>

                        {{-- Score summary --}}
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <x-exam.stat label="Correct Answers" :value="$quizResults['results']['correct_answers']" tone="primary" />
                            <x-exam.stat label="Incorrect Answers" :value="$quizResults['results']['total_questions'] - $quizResults['results']['correct_answers']" tone="danger" />
                            <x-exam.stat label="Points" :value="$quizResults['results']['points_earned'] . ' / ' . $quizResults['results']['points_possible']" tone="ok" />
                        </div>

                        {{-- Strengths & improvements --}}
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            @if(!empty($quizResults['detailed_feedback']['strengths']))
                                <x-exam.card :padded="false">
                                    <x-slot:header>
                                        <p class="flex items-center gap-2 font-mono text-[10.5px] tracking-[0.14em] text-muted"><span class="h-2 w-2 bg-ok"></span>YOUR STRENGTHS</p>
                                    </x-slot:header>
                                    <ul class="space-y-2.5 p-5">
                                        @foreach($quizResults['detailed_feedback']['strengths'] as $strength)
                                            <li class="flex items-start gap-2.5 text-sm text-ink-soft">
                                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-ok" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                <span>{{ $strength }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </x-exam.card>
                            @endif

                            @if(!empty($quizResults['detailed_feedback']['areas_for_improvement']))
                                <x-exam.card :padded="false">
                                    <x-slot:header>
                                        <p class="flex items-center gap-2 font-mono text-[10.5px] tracking-[0.14em] text-muted"><span class="h-2 w-2 bg-warn"></span>AREAS FOR IMPROVEMENT</p>
                                    </x-slot:header>
                                    <ul class="space-y-2.5 p-5">
                                        @foreach($quizResults['detailed_feedback']['areas_for_improvement'] as $improvement)
                                            <li class="flex items-start gap-2.5 text-sm text-ink-soft">
                                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-warn" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                                <span>{{ $improvement }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </x-exam.card>
                            @endif
                        </div>

                        {{-- Study suggestions --}}
                        @if(!empty($quizResults['detailed_feedback']['study_suggestions']))
                            <x-exam.card :padded="false">
                                <x-slot:header>
                                    <p class="flex items-center gap-2 font-mono text-[10.5px] tracking-[0.14em] text-muted"><span class="h-2 w-2 bg-primary"></span>STUDY SUGGESTIONS</p>
                                </x-slot:header>
                                <ul class="space-y-2.5 p-5">
                                    @foreach($quizResults['detailed_feedback']['study_suggestions'] as $suggestion)
                                        <li class="flex items-start gap-2.5 text-sm text-ink-soft">
                                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            <span>{{ $suggestion }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </x-exam.card>
                        @endif

                        {{-- Actions --}}
                        <div class="flex justify-center pt-2">
                            <x-exam.button type="button" variant="ok" @click="$wire.resetQuiz(); showExplanations = false">Take Another Quiz</x-exam.button>
                        </div>

                        {{-- Question review --}}
                        @if($quizResults['question_breakdown'])
                            <div class="space-y-6">
                                <h3 class="text-center font-display text-2xl font-bold text-ink">Question Review</h3>
                                @foreach($quizResults['question_breakdown'] as $index => $question)
                                    <div class="rounded-md border border-line border-l-4 bg-surface p-6 shadow-[0_1px_2px_rgba(20,24,34,0.04)] {{ $question['is_correct'] ? 'border-l-ok' : 'border-l-accent' }}">
                                        <div class="mb-3 flex items-center justify-between">
                                            <h4 class="font-mono text-[11px] font-semibold uppercase tracking-[0.14em] text-muted">Question {{ $question['question_number'] }}</h4>
                                            <x-exam.badge :tone="$question['is_correct'] ? 'ok' : 'danger'">{{ $question['is_correct'] ? 'Correct' : 'Incorrect' }}</x-exam.badge>
                                        </div>
                                        <div class="mb-4 font-medium text-ink">
                                            <x-form.markdown-with-math content="{{ $question['question_text'] }}"/>
                                        </div>
                                        <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                                            <div class="rounded-md bg-surface-2 p-3">
                                                <p class="mb-1 font-mono text-[10px] uppercase tracking-[0.14em] text-muted">Your Answer</p>
                                                <p class="text-sm text-ink">{{ $question['user_answer'] ?? 'No answer provided' }}</p>
                                            </div>
                                            <div class="rounded-md bg-surface-2 p-3">
                                                <p class="mb-1 font-mono text-[10px] uppercase tracking-[0.14em] text-muted">Correct Answer</p>
                                                <p class="text-sm text-ink">{{ $question['correct_answer'] }}</p>
                                            </div>
                                        </div>
                                        @if($question['feedback'])
                                            <div class="border-t border-dashed border-line pt-4">
                                                <p class="mb-2 flex items-center gap-1.5 font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                                                    <svg class="h-3.5 w-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    Explanation
                                                </p>
                                                <div class="prose prose-sm max-w-none text-ink-soft dark:prose-invert">
                                                    <p>{{ $question['feedback'] }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ==================== HISTORY TAB ==================== --}}
    @if($activeTab === 'history')
        <div class="animate-rise">
            @if($previousQuizzes->isEmpty())
                <x-exam.empty title="No quizzes taken yet" description="Get started by taking your first quiz.">
                    <x-exam.button type="button" class="mt-5" wire:click="$set('activeTab', 'new')">+ Start New Quiz</x-exam.button>
                </x-exam.empty>
            @else
                <x-exam.card :padded="false">
                    <ul class="divide-y divide-line">
                        @foreach($previousQuizzes as $quiz)
                            @php($pct = $quiz->results['percentage'] ?? 0)
                            <li class="flex flex-col gap-4 px-5 py-4 transition hover:bg-surface-2/60 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex min-w-0 items-center gap-4">
                                    @if($quiz->book && $quiz->book->cover_image)
                                        <img class="h-16 w-12 rounded-[3px] object-cover shadow-sm ring-1 ring-line" src="{{ $quiz->book->cover_image }}" alt="{{ $quiz->book->title ?? 'Book' }}">
                                    @else
                                        <div class="grid h-16 w-12 place-items-center rounded-[3px] border border-dashed border-line-strong bg-surface-2">
                                            <svg class="h-5 w-5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <h3 class="truncate font-display text-base font-bold text-ink">{{ $quiz->book->title ?? 'Unknown Book' }}</h3>
                                        <p class="mt-0.5 font-mono text-[10.5px] tracking-wider text-muted">
                                            {{ $quiz->completed_at->format('M j, Y') }} · {{ $quiz->results['total_questions'] ?? 0 }} QUESTIONS
                                        </p>
                                        <div class="mt-1.5 flex items-center gap-2">
                                            <x-exam.badge :tone="$pct >= 80 ? 'ok' : ($pct >= 60 ? 'warn' : 'danger')">{{ $pct }}%</x-exam.badge>
                                            <span class="font-mono text-[10.5px] text-muted">{{ $quiz->results['correct_answers'] ?? 0 }}/{{ $quiz->results['total_questions'] ?? 0 }} CORRECT</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="shrink-0 sm:text-right">
                                    <x-exam.link-button wire:click="viewResults({{ $quiz->id }})">View Results</x-exam.link-button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </x-exam.card>
            @endif
        </div>
    @endif

    {{-- ==================== RESULTS TAB (from history) ==================== --}}
    @if($activeTab === 'results' && $quizResults)
        <div x-data="{ showExplanations: false }" x-transition class="animate-rise">
            <div class="mx-auto max-w-3xl">
                {{-- Header --}}
                <div class="mb-7 text-center">
                    <div class="mb-3 flex justify-center">
                        @if($quizResults['results']['percentage'] >= 90)
                            <div class="grid h-16 w-16 place-items-center rounded-full bg-ok/10 ring-1 ring-inset ring-ok/30">
                                <svg class="h-8 w-8 text-ok" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        @elseif($quizResults['results']['percentage'] >= 70)
                            <div class="grid h-16 w-16 place-items-center rounded-full bg-warn/10 ring-1 ring-inset ring-warn/30">
                                <svg class="h-8 w-8 text-warn" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        @else
                            <div class="grid h-16 w-16 place-items-center rounded-full bg-accent/10 ring-1 ring-inset ring-accent/30">
                                <svg class="h-8 w-8 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        @endif
                    </div>
                    <h2 class="font-display text-2xl font-bold text-ink">Quiz Results</h2>
                    <p class="mt-1 text-ink-soft">{{ $quizResults['detailed_feedback']['overall_performance'] }}</p>
                    <div class="mt-4">
                        <p class="font-display text-4xl font-bold text-primary">{{ $quizResults['results']['percentage'] }}%</p>
                        <p class="mt-1 font-mono text-[11px] tracking-wider text-muted">{{ $quizResults['results']['correct_answers'] }} / {{ $quizResults['results']['total_questions'] }} CORRECT</p>
                    </div>
                </div>

                {{-- Key metrics --}}
                <div class="mb-7 grid grid-cols-3 gap-4">
                    <x-exam.stat label="Correct" :value="$quizResults['results']['correct_answers']" tone="primary" />
                    <x-exam.stat label="Incorrect" :value="$quizResults['results']['total_questions'] - $quizResults['results']['correct_answers']" tone="danger" />
                    <x-exam.stat label="Points" :value="$quizResults['results']['points_earned'] . '/' . $quizResults['results']['points_possible']" tone="ok" />
                </div>

                {{-- Feedback --}}
                <div class="space-y-5">
                    @if(!empty($quizResults['detailed_feedback']['strengths']))
                        <div class="rounded-md border border-line border-l-4 border-l-ok bg-surface p-4">
                            <h3 class="mb-3 flex items-center gap-2 font-mono text-[11px] font-semibold uppercase tracking-[0.14em] text-ok">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Your Strengths
                            </h3>
                            <ul class="space-y-2">
                                @foreach($quizResults['detailed_feedback']['strengths'] as $strength)
                                    <li class="flex items-start gap-2 text-sm text-ink-soft">
                                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-ok" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>{{ $strength }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(!empty($quizResults['detailed_feedback']['areas_for_improvement']))
                        <div class="rounded-md border border-line border-l-4 border-l-warn bg-surface p-4">
                            <h3 class="mb-3 flex items-center gap-2 font-mono text-[11px] font-semibold uppercase tracking-[0.14em] text-warn">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                Areas for Improvement
                            </h3>
                            <ul class="space-y-2">
                                @foreach($quizResults['detailed_feedback']['areas_for_improvement'] as $improvement)
                                    <li class="flex items-start gap-2 text-sm text-ink-soft">
                                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-warn" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                        <span>{{ $improvement }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(!empty($quizResults['detailed_feedback']['study_suggestions']))
                        <div class="rounded-md border border-line border-l-4 border-l-primary bg-surface p-4">
                            <h3 class="mb-3 flex items-center gap-2 font-mono text-[11px] font-semibold uppercase tracking-[0.14em] text-primary">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                Study Suggestions
                            </h3>
                            <ul class="space-y-2">
                                @foreach($quizResults['detailed_feedback']['study_suggestions'] as $suggestion)
                                    <li class="flex items-start gap-2 text-sm text-ink-soft">
                                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        <span>{{ $suggestion }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                {{-- Back --}}
                <div class="mt-7 flex justify-center border-t border-line pt-5">
                    <x-exam.button type="button" variant="ghost" wire:click="backToHistory">← Back to History</x-exam.button>
                </div>

                {{-- Question review --}}
                @if($quizResults['question_breakdown'])
                    <div class="mt-7 border-t border-line pt-6">
                        <h3 class="mb-4 font-display text-lg font-bold text-ink">Question Review</h3>
                        <div class="space-y-4">
                            @foreach($quizResults['question_breakdown'] as $index => $question)
                                <div class="rounded-md border border-line bg-surface p-4 shadow-[0_1px_2px_rgba(20,24,34,0.04)]">
                                    <div class="mb-3 flex items-center justify-between">
                                        <h4 class="font-mono text-[11px] font-semibold uppercase tracking-[0.14em] text-muted">Question {{ $question['question_number'] }}</h4>
                                        <x-exam.badge :tone="$question['is_correct'] ? 'ok' : 'danger'">{{ $question['is_correct'] ? 'Correct' : 'Incorrect' }}</x-exam.badge>
                                    </div>
                                    <p class="mb-4 text-sm text-ink">{{ $question['question_text'] }}</p>
                                    <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                                        <div class="rounded-md bg-surface-2 p-3">
                                            <p class="mb-1 font-mono text-[10px] uppercase tracking-[0.14em] text-muted">Your Answer</p>
                                            <p class="text-sm text-ink">{{ $question['user_answer'] ?? 'No answer provided' }}</p>
                                        </div>
                                        <div class="rounded-md bg-surface-2 p-3">
                                            <p class="mb-1 font-mono text-[10px] uppercase tracking-[0.14em] text-muted">Correct Answer</p>
                                            <p class="text-sm text-ink">{{ $question['correct_answer'] }}</p>
                                        </div>
                                    </div>
                                    @if($question['feedback'])
                                        <div class="border-t border-dashed border-line pt-4">
                                            <p class="mb-2 flex items-center gap-1.5 font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                                                <svg class="h-3.5 w-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Explanation
                                            </p>
                                            <div class="text-sm text-ink-soft">
                                                <x-form.markdown-with-math content="{{ $question['feedback'] }}"/>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ==================== Loading overlays ==================== --}}
    <div wire:loading.flex wire:target="generateQuiz" class="fixed inset-0 z-50 items-center justify-center bg-black/60 p-4">
        <div class="w-full max-w-md rounded-md border border-line bg-surface p-8 text-center shadow-2xl">
            <div class="mx-auto mb-4 h-14 w-14 animate-spin rounded-full border-2 border-line border-t-primary"></div>
            <h3 class="font-display text-lg font-bold text-ink">Generating Your Quiz</h3>
            <p class="mt-2 text-sm text-ink-soft">Hang in there! We are creating personalised questions based on your book selection…</p>
        </div>
    </div>

    <div wire:loading.flex wire:target="submitQuizAnswers" class="fixed inset-0 z-50 items-center justify-center bg-black/60 p-4">
        <div class="w-full max-w-md rounded-md border border-line bg-surface p-8 text-center shadow-2xl">
            <div class="mx-auto mb-4 h-14 w-14 animate-spin rounded-full border-2 border-line border-t-ok"></div>
            <h3 class="font-display text-lg font-bold text-ink">Grading Your Quiz</h3>
            <p class="mt-2 text-sm text-ink-soft">Analysing your answers and generating detailed feedback…</p>
        </div>
    </div>


<style>
    .quiz-progress-bar {
        background: rgb(var(--c-primary));
        box-shadow: 0 2px 4px rgb(var(--c-primary) / 0.3);
    }
    .timer-display { font-family: 'JetBrains Mono', ui-monospace, monospace; letter-spacing: 1px; }
    .question-review-card { transition: all 0.3s ease; }
    .question-review-card:hover { transform: translateY(-4px); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); }
    @media (prefers-reduced-motion: reduce) {
        .question-review-card, .question-review-card:hover { transition: none; transform: none; }
    }
</style>

<script>
    document.addEventListener('livewire:init', function () {
        Livewire.on('update-url', (params) => {
            const url = new URL(window.location);
            if (params[0].quiz) { url.searchParams.set('quiz', params[0].quiz); }
            else { url.searchParams.delete('quiz'); }
            window.history.pushState({}, '', url);
        });

        Livewire.on('download-results', (data) => {
            let content = `Quiz Results Report\n==================\n\n`;
            content += `Book: ${data[0].book}\n`;
            content += `Author: ${data[0].author}\n`;
            content += `Date: ${data[0].quiz_date}\n\n`;
            if (data[0].results) {
                content += `Overall Score: ${data[0].results.percentage}%\n`;
                content += `Correct Answers: ${data[0].results.correct_answers}/${data[0].results.total_questions}\n`;
                content += `Points: ${data[0].results.points_earned}/${data[0].results.points_possible}\n\n`;
            }
            if (data[0].performance) {
                content += `Performance Feedback:\n${data[0].performance.overall_performance}\n\n`;
                if (data[0].performance.strengths && data[0].performance.strengths.length > 0) {
                    content += `Strengths:\n`;
                    data[0].performance.strengths.forEach(s => { content += `- ${s}\n`; });
                    content += `\n`;
                }
                if (data[0].performance.areas_for_improvement && data[0].performance.areas_for_improvement.length > 0) {
                    content += `Areas for Improvement:\n`;
                    data[0].performance.areas_for_improvement.forEach(a => { content += `- ${a}\n`; });
                    content += `\n`;
                }
                if (data[0].performance.study_suggestions && data[0].performance.study_suggestions.length > 0) {
                    content += `Study Suggestions:\n`;
                    data[0].performance.study_suggestions.forEach(s => { content += `- ${s}\n`; });
                }
            }
            const blob = new Blob([content], {type: 'text/plain'});
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `quiz-results-${data[0].book.replace(/\s+/g, '-').toLowerCase()}-${new Date().toISOString().slice(0, 10)}.txt`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        });

        Livewire.on('quiz-generated', (event) => {
            setTimeout(() => {
                const quizSection = document.getElementById('quiz-section');
                if (quizSection) { quizSection.scrollIntoView({behavior: 'smooth', block: 'start'}); }
            }, 300);
        });
    });
</script>
</x-exam.page>