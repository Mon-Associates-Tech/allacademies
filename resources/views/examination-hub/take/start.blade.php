<div class="min-h-screen bg-slate-50 dark:bg-slate-950 font-serif">

    {{-- STATE 1 · SECTION INFO OVERLAY --}}
    @if($showSectionInfo)
        <div class="flex items-center justify-center min-h-screen px-4 py-8 bg-[radial-gradient(ellipse_at_60%_40%,rgba(15,23,42,0.06)_0%,transparent_70%)] bg-slate-50">
            <div class="bg-white dark:bg-slate-900 w-full max-w-lg overflow-hidden rounded-[2px] shadow-[0_0_0_1px_rgba(0,0,0,0.06),0_20px_60px_-10px_rgba(0,0,0,0.15),0_4px_16px_rgba(0,0,0,0.06)]">

                {{-- Accent bar --}}
                <div class="h-1 w-full bg-gradient-to-r from-amber-800 via-amber-600 to-amber-400"></div>

                {{-- Header --}}
                <div class="px-8 pt-7 pb-5 border-b border-slate-200 dark:border-slate-800 flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-medium tracking-widest uppercase text-amber-600 dark:text-amber-500 mb-1 font-sans">Examination Section</p>
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white leading-snug tracking-tight">
                            {{ $this->section->title }}
                        </h2>
                    </div>
                    <button wire:click="toggleSectionInfo"
                            class="mt-1 flex-shrink-0 w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors rounded-[2px] border border-slate-200 dark:border-slate-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="px-8 py-7 space-y-6 font-sans">
                    @if($this->section->description)
                        <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            {{ $this->section->description }}
                        </p>
                    @endif

                    {{-- Stats grid --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="px-4 py-3 bg-slate-50 dark:bg-slate-800/60 rounded-[2px] border border-slate-200 dark:border-slate-800">
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1 text-[10px]">Questions</p>
                            <p class="text-xl font-bold text-slate-900 dark:text-white tracking-tighter">{{ $this->questions->count() }}</p>
                        </div>
                        <div class="px-4 py-3 bg-slate-50 dark:bg-slate-800/60 rounded-[2px] border border-slate-200 dark:border-slate-800">
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1 text-[10px]">Section</p>
                            <p class="text-xl font-bold text-slate-900 dark:text-white tracking-tighter">
                                {{ $sectionIndex + 1 }} <span class="text-sm font-normal text-slate-500">of {{ $this->exam->sections->count() }}</span>
                            </p>
                        </div>
                        @if($this->section->time_limit_minutes)
                            <div class="px-4 py-3 bg-amber-50 dark:bg-amber-900/20 rounded-[2px] border border-amber-200 dark:border-amber-800">
                                <p class="text-xs text-amber-700 dark:text-amber-500 uppercase tracking-widest mb-1 text-[10px]">Time Limit</p>
                                <p class="text-xl font-bold text-amber-800 dark:text-amber-400 tracking-tighter">{{ $this->section->time_limit_minutes }} <span class="text-sm font-normal">min</span></p>
                            </div>
                        @endif
                        <div class="px-4 py-3 bg-slate-50 dark:bg-slate-800/60 rounded-[2px] border border-slate-200 dark:border-slate-800">
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1 text-[10px]">Type</p>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white mt-1">{{ str_replace('_', ' ', ucfirst($this->section->question_type)) }}</p>
                        </div>
                    </div>

                    @if($this->section->instructions)
                        <div class="border-l-4 border-amber-500 pl-4">
                            <h4 class="text-xs font-semibold text-amber-700 dark:text-amber-500 uppercase tracking-widest mb-2 text-[10px]">Instructions</h4>
                            <div class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">{{ $this->section->instructions }}</div>
                        </div>
                    @endif

                    <div class="pt-2 flex justify-end">
                        <x-ui.button
                            variant="secondary"
                            size="md"
                            wire:click="startSection"
                        >
                            Begin Section
                        </x-ui.button>
                    </div>
                </div>
            </div>
        </div>

    @else

        {{-- STATE 2 · EMPTY SECTION --}}
        @if($this->questions->count() === 0)
            <div class="max-w-3xl mx-auto px-4 py-12 font-sans">
                <div class="bg-white dark:bg-slate-900 overflow-hidden rounded-[2px] shadow-[0_0_0_1px_rgba(0,0,0,0.06),0_8px_32px_rgba(0,0,0,0.08)]">

                    <div class="h-1 bg-gradient-to-r from-amber-800 via-amber-600 to-amber-400"></div>

                    {{-- Exam title strip --}}
                    <div class="px-8 py-6 border-b border-slate-200 dark:border-slate-800 bg-gradient-to-r from-slate-900 to-slate-800">
                        <p class="text-xs tracking-widest text-amber-400 uppercase mb-1">Online Examination</p>
                        <h1 class="text-2xl font-bold text-white tracking-tight font-serif">{{ $this->exam->title }}</h1>
                        <p class="text-slate-400 text-sm mt-1">Section {{ $sectionIndex + 1 }} — {{ $this->section->title }}</p>
                    </div>

                    <div class="p-8 space-y-6">
                        @if($this->section->description)
                            <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{{ $this->section->description }}</p>
                        @endif

                        @if($this->section->instructions)
                            <div class="p-4 bg-blue-50 dark:bg-blue-950/30 border-l-4 border-blue-400">
                                <h3 class="text-xs font-bold text-blue-800 dark:text-blue-300 uppercase tracking-wider mb-2">Section Instructions</h3>
                                <div class="text-sm text-blue-700 dark:text-blue-300 whitespace-pre-line leading-relaxed">{{ $this->section->instructions }}</div>
                            </div>
                        @endif

                        <div class="grid md:grid-cols-3 gap-3">
                            <div class="p-4 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-[2px]">
                                <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Question Type</p>
                                <p class="font-semibold text-slate-900 dark:text-white">{{ str_replace('_', ' ', ucfirst($this->section->question_type)) }}</p>
                            </div>
                            @if($this->section->time_limit_minutes)
                                <div class="p-4 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-[2px]">
                                    <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Time Limit</p>
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $this->section->time_limit_minutes }} minutes</p>
                                </div>
                            @endif
                            <div class="p-4 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-[2px]">
                                <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Expected</p>
                                <p class="font-semibold text-slate-900 dark:text-white">{{ $this->section->question_count }} Questions</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 p-5 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 rounded-[2px]">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900 flex items-center justify-center">
                                <svg class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-amber-900 dark:text-amber-200 mb-1">No Questions Available</h3>
                                <p class="text-sm text-amber-700 dark:text-amber-400 leading-relaxed">This section does not have any questions yet. The exam administrator may still be preparing the questions. Please check back later or contact support.</p>
                            </div>
                        </div>

                        @if($this->exam->instructions)
                            <div class="p-4 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-[2px]">
                                <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">General Exam Instructions</h3>
                                <div class="text-sm text-slate-600 dark:text-slate-400 whitespace-pre-line leading-relaxed">{{ $this->exam->instructions }}</div>
                            </div>
                        @endif

                        <div class="flex justify-between items-center pt-4 border-t border-slate-200 dark:border-slate-800">
                            <x-ui.button
                                variant="ghost"
                                size="sm"
                                href="{{ route('examination-hub.take.start', $this->exam) }}"
                                icon="arrow-left"
                            >
                                Back to Overview
                            </x-ui.button>

                            @if($sectionIndex < $this->exam->sections->count() - 1)
                                <x-ui.button
                                    variant="secondary"
                                    size="sm"
                                    href="{{ route('examination-hub.take.section', [$this->exam, $sectionIndex + 1]) }}"
                                >
                                    Skip to Next Section
                                </x-ui.button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        @else

            {{-- STATE 3 · ACTIVE EXAMINATION VIEW --}}
            <div class="flex flex-col h-screen font-sans">

                {{-- TOP HEADER BAR --}}
                <div class="flex-shrink-0 bg-slate-900 dark:bg-slate-950 border-b border-slate-800 shadow-[0_1px_0_rgba(255,255,255,0.04)]">
                    <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between gap-4">

                        {{-- Exam / Section title --}}
                        <div class="min-w-0 flex-1">
                            <h2 class="text-base font-bold text-white truncate tracking-tight font-serif">
                                {{ $this->exam->title }}
                            </h2>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-xs text-slate-400">{{ $this->section->title }}</span>
                                <span class="text-slate-700">·</span>
                                <span class="text-xs text-amber-400 font-medium">
                            Q{{ $currentQuestionIndex + 1 }} / {{ $this->questions->count() }}
                        </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 flex-shrink-0">
                            {{-- Progress pill --}}
                            <div class="hidden sm:flex flex-col items-end gap-1">
                                <span class="text-xs text-slate-500">{{ $this->getAnsweredCount() }} answered</span>
                                <div class="w-28 h-1.5 bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500 bg-gradient-to-r from-amber-600 to-amber-400"
                                         style="width: {{ $this->questions->count() > 0 ? ($this->getAnsweredCount() / $this->questions->count()) * 100 : 0 }}%"></div>
                                </div>
                            </div>

                            {{-- Timer --}}
                            @if($timeRemaining !== null)
                                <div class="flex items-center gap-2 px-3 py-1.5 rounded bg-red-900/20 border border-red-800/30">
                                    <svg class="h-4 w-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="font-mono text-sm font-bold text-red-400">{{ $timeRemaining }}<span class="text-red-500 text-xs font-normal ml-0.5">min</span></span>
                                </div>
                            @endif

                            {{-- Section info toggle --}}
                            <x-ui.button
                                variant="ghost"
                                size="xs"
                                wire:click="toggleSectionInfo"
                                icon="information-circle"
                            >
                                Section Info
                            </x-ui.button>
                        </div>
                    </div>
                </div>

                {{-- SCROLLABLE CONTENT AREA --}}
                <div class="flex-1 overflow-y-auto bg-gradient-to-b from-slate-100 to-slate-50 dark:bg-gradient-to-b dark:from-slate-900 dark:to-slate-950 scrollbar-gutter-stable">
                    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">

                        @php
                            $question = $this->questions[$currentQuestionIndex];
                        @endphp

                        {{-- QUESTION CARD --}}
                        <x-ui.card variant="default" shadow="true" class="mb-5">
                            {{-- Question header strip --}}
                            <div class="px-6 py-3 border-b border-slate-200 dark:border-slate-800 bg-gradient-to-r from-slate-50 to-white dark:from-slate-900 dark:to-slate-800 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-7 h-7 flex items-center justify-center text-xs font-bold text-white bg-gradient-to-br from-amber-700 to-amber-500 rounded-[2px]">
                                        {{ $currentQuestionIndex + 1 }}
                                    </div>
                                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                                Question {{ $currentQuestionIndex + 1 }} of {{ $this->questions->count() }}
                            </span>
                                </div>
                                <div class="flex items-center gap-2">
                            <span class="text-xs px-2 py-1 text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-[2px]">
                                {{ str_replace('_', ' ', ucfirst($question->type)) }}
                            </span>
                                    <span class="text-xs px-2 py-1 text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-[2px]">
                                {{ $question->marks }} {{ $question->marks === 1 ? 'mark' : 'marks' }}
                            </span>
                                </div>
                            </div>

                            {{-- Question body --}}
                            <div class="px-6 pt-6 pb-5">
                                {{-- Question text --}}
                                <div class="text-slate-800 dark:text-slate-200 mb-7 leading-relaxed text-[1.05rem] font-serif"
                                     wire:key="question-text-{{ $question->id }}">
                                    <x-form.markdown-with-math :content="$question->getFormattedQuestion()" class="prose dark:prose-invert max-w-none" />
                                </div>

                                {{-- ── MULTIPLE CHOICE ── --}}
                                @if($question->isMultipleChoice())
                                    <div class="space-y-2" wire:key="options-{{ $question->id }}">
                                        @foreach($question->getOptionsForDisplay() as $key => $optionText)
                                            @php
                                                $isSelected = isset($responses[$question->id]) && $responses[$question->id] === $key;
                                            @endphp
                                            <label
                                                wire:key="opt-{{ $question->id }}-{{ $key }}"
                                                class="flex items-start gap-3 p-3.5 cursor-pointer transition-all duration-150 group rounded-[2px] border {{ $isSelected ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800' }}"
                                            >
                                                <input
                                                    type="radio"
                                                    name="question_{{ $question->id }}"
                                                    value="{{ $key }}"
                                                    wire:model.live="responses.{{ $question->id }}"
                                                    class="h-4 w-4 text-amber-600 border-slate-300 focus:ring-amber-500 flex-shrink-0 mt-1"
                                                >
                                                <div class="flex-1 flex items-start gap-3">
                                            <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-bold flex-shrink-0 mt-0.5 transition-colors rounded-[2px] {{ $isSelected ? 'bg-amber-500 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                                                {{ $key }}
                                            </span>
                                                    <div class="flex-1 text-sm text-slate-700 dark:text-slate-300 leading-relaxed pt-0.5">
                                                        <x-form.markdown-with-math :content="$optionText" class="text-slate-800 dark:text-slate-200" />
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>

                                    {{-- ── TRUE / FALSE ── --}}
                                @elseif($question->isTrueFalse())
                                    <div class="grid grid-cols-2 gap-3">
                                        @foreach(['True', 'False'] as $tfValue)
                                            @php
                                                $isSelected = isset($responses[$question->id]) && $responses[$question->id] === $tfValue;
                                            @endphp
                                            <label
                                                class="flex items-center justify-center gap-3 py-5 cursor-pointer transition-all duration-150 rounded-[2px] {{ $isSelected ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800' }}"
                                            >
                                                <input
                                                    type="radio"
                                                    name="question_{{ $question->id }}"
                                                    value="{{ $tfValue }}"
                                                    wire:model.live="responses.{{ $question->id }}"
                                                    class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-slate-300 flex-shrink-0"
                                                >
                                                <span class="text-base font-semibold text-slate-700 dark:text-slate-200">{{ $tfValue }}</span>
                                            </label>
                                        @endforeach
                                    </div>

                                    {{-- ── OPEN / ESSAY ── --}}
                                @else
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Your Answer</label>
                                        <textarea
                                            wire:model.live.debounce.500ms="responses.{{ $question->id }}"
                                            rows="10"
                                            class="w-full px-4 py-3 text-sm text-slate-800 dark:text-white bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:focus:border-amber-500 transition-all resize-none leading-relaxed rounded-[2px] font-serif"
                                            placeholder="Type your answer here…"
                                        ></textarea>
                                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1.5">Responses are saved automatically as you type.</p>
                                    </div>
                                @endif
                            </div>
                        </x-ui.card>

                        {{-- QUESTION NAVIGATOR --}}
                        <x-ui.card variant="default" shadow="true">
                            <div class="px-5 py-3 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                                <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-widest">Question Navigator</h4>
                                <span class="text-xs font-medium text-amber-700 dark:text-amber-400 px-2 py-0.5 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-[2px]">
                            {{ $this->getAnsweredCount() }} / {{ $this->questions->count() }} answered
                        </span>
                            </div>

                            <div class="p-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($this->questions as $index => $q)
                                        <button
                                            wire:key="nav-btn-{{ $q->id }}"
                                            wire:click="goToQuestion({{ $index }})"
                                            title="Question {{ $index + 1 }}"
                                            class="w-8 h-8 text-xs font-semibold transition-all duration-150 flex items-center justify-center relative rounded-[2px]
                                        @if($currentQuestionIndex === $index)
                                            bg-gradient-to-br from-amber-700 to-amber-500 text-white shadow-[0_2px_8px_rgba(180,83,9,0.35)] scale-110
                                        @elseif(!empty($responses[$q->id]))
                                            bg-emerald-600 text-white
                                        @else
                                            bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300
                                        @endif">
                                            {{ $index + 1 }}
                                        </button>
                                    @endforeach
                                </div>

                                <div class="flex items-center gap-5 mt-4 pt-3 border-t border-slate-200 dark:border-slate-800">
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-4 h-4 bg-gradient-to-br from-amber-700 to-amber-500 rounded-[2px]"></div>
                                        <span class="text-xs text-slate-500 dark:text-slate-400">Current</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-4 h-4 bg-emerald-600 rounded-[2px]"></div>
                                        <span class="text-xs text-slate-500 dark:text-slate-400">Answered</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-4 h-4 bg-slate-200 dark:bg-slate-700 rounded-[2px]"></div>
                                        <span class="text-xs text-slate-500 dark:text-slate-400">Unanswered</span>
                                    </div>
                                </div>
                            </div>
                        </x-ui.card>

                    </div>{{-- /max-w-3xl --}}
                </div>{{-- /overflow-y-auto --}}

                {{-- BOTTOM NAVIGATION BAR --}}
                <div class="flex-shrink-0 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 shadow-[0_-2px_12px_rgba(0,0,0,0.06)]">
                    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between gap-3">

                        {{-- Previous --}}
                        <x-ui.button
                            variant="ghost"
                            size="md"
                            wire:click="previousQuestion"
                            :disabled="$currentQuestionIndex === 0"
                        >
                            <x-heroicon-o-arrow-left class="w-4 h-4"/>
                            Previous
                        </x-ui.button>

                        {{-- Centre action --}}
                        <div class="flex items-center gap-2">
                            @if($sectionIndex < $this->exam->sections->count() - 1)
                                <x-ui.button
                                    variant="secondary"
                                    size="md"
                                    href="{{ route('examination-hub.take.section', [$this->exam, $sectionIndex + 1]) }}"
                                >
                                    Next Section
                                    <x-heroicon-o-arrow-right class="w-4 h-4"/>
                                </x-ui.button>
                            @else
                                <form method="POST" action="{{ route('examination-hub.take.submit', $this->exam) }}">
                                    @csrf
                                    <x-ui.button
                                        variant="success"
                                        size="md"
                                        type="submit"
                                    >
                                        <x-heroicon-o-check-circle class="w-4 h-4"/>
                                        Submit Examination
                                    </x-ui.button>
                                </form>
                            @endif
                        </div>

                        {{-- Next --}}
                        <x-ui.button
                            variant="warning"
                            size="md"
                            wire:click="nextQuestion"
                            :disabled="$currentQuestionIndex === $this->questions->count() - 1"
                        >
                            Next
                            <x-heroicon-o-arrow-right class="w-4 h-4"/>
                        </x-ui.button>

                    </div>
                </div>

            </div>{{-- /flex flex-col h-screen --}}
        @endif
    @endif

</div>
