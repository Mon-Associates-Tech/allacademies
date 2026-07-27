<div>
    <style>
        @media print {
            html, body { overflow: visible !important; height: auto !important; }
            .relative.flex.flex-col.flex-1 { overflow: visible !important; height: auto !important; }
            .no-print { display: none !important; }
            * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>

    {{-- ─── Page Header ──────────────────────────────────────────────────── --}}
    <div class="bg-gradient-to-br from-indigo-800 via-indigo-900 to-indigo-900 shadow-xl border-b-2 border-blue-500 mb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-xl font-semibold text-white flex items-center gap-2">
                        {{-- Edit icon --}}
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Direct Exam Question Editing
                    </h1>
                    <p class="mt-1 text-sm text-gray-400">
                        Edit exam questions directly without affecting the question bank. Changes affect only this specific exam.
                    </p>
                </div>

                @if($saveMessage)
                    <div @class([
                        'flex-shrink-0 flex items-start gap-2 text-sm rounded px-3 py-2 max-w-sm',
                        'bg-green-900/40 border border-green-700 text-green-300' => $saveSuccess,
                        'bg-red-900/40 border border-red-700 text-red-300' => !$saveSuccess,
                    ])>
                        @if($saveSuccess)
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        @else
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @endif
                        <span>{{ $saveMessage }}</span>
                    </div>
                @endif
            </div>

            {{-- ── Subject / Exam selectors ──────────────────────────────────── --}}
            <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Subject --}}
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Subject</label>
                    <select wire:model.live="subjectId"
                            class="w-full bg-gray-700 border border-gray-600 text-gray-200 text-sm rounded px-3 py-2
                                   focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500
                                   appearance-none">
                        <option value="">— Select subject —</option>
                        @foreach($subjects as $levelName => $levelSubjects)
                            <optgroup label="{{ $levelName }}">
                                @foreach($levelSubjects as $subject)
                                    <option value="{{ $subject['id'] }}">{{ $subject['name'] }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                {{-- Exam (cascades from subject) --}}
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Exam</label>
                    <select wire:model.live="examId"
                            @disabled(!$subjectId)
                            class="w-full bg-gray-700 border border-gray-600 text-gray-200 text-sm rounded px-3 py-2
                                   focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500
                                   appearance-none disabled:opacity-40 disabled:cursor-not-allowed">
                        <option value="">— Select exam —</option>
                        @foreach($this->exams as $exam)
                            <option value="{{ $exam['id'] }}">{{ $exam['title'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Main content ─────────────────────────────────────────────────── --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Skeleton while Livewire is loading --}}
        <div wire:loading.block wire:target="subjectId,examId"
             class="space-y-4">
            @foreach(range(1,3) as $_)
                <div class="bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 p-5 animate-pulse">
                    <div class="h-4 bg-gray-200 dark:bg-gray-600 rounded w-3/4 mb-4"></div>
                    <div class="space-y-2">
                        <div class="h-3 bg-gray-100 dark:bg-gray-700 rounded w-full"></div>
                        <div class="h-3 bg-gray-100 dark:bg-gray-700 rounded w-5/6"></div>
                        <div class="h-3 bg-gray-100 dark:bg-gray-700 rounded w-4/6"></div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Content (hidden while loading) --}}
        <div wire:loading.remove wire:target="subjectId,examId">

            @if(!$subjectId)
                {{-- ── Empty state: no subject selected ─────────────────────── --}}
                <div class="flex flex-col items-center justify-center py-24 text-center">
                    <div class="w-14 h-14 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-medium text-gray-700 dark:text-gray-300">Select a subject</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Choose a subject above to load the exams for that subject.
                    </p>
                </div>

            @elseif(!$examId)
                {{-- ── Empty state: subject selected but no exam selected ─── --}}
                @if(empty($this->exams))
                    <div class="flex flex-col items-center justify-center py-24 text-center">
                        <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                            <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-medium text-gray-700 dark:text-gray-300">No exams found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            This subject has no exams with questions yet.
                        </p>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-24 text-center">
                        <div class="w-14 h-14 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mb-4">
                            <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-medium text-gray-700 dark:text-gray-300">Select an exam</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Choose an exam from the dropdown above to edit its questions.
                        </p>
                    </div>
                @endif

            @elseif(empty($this->questionData))
                {{-- ── Empty state: exam has no questions ─────────────────── --}}
                <div class="flex flex-col items-center justify-center py-24 text-center">
                    <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-medium text-gray-700 dark:text-gray-300">No questions found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        This exam has no questions yet.
                    </p>
                </div>

            @else
                {{-- ── Alpine editor (reinitialises when exam changes via wire:key) ── --}}
                <div
                    wire:key="direct-exam-question-editor-{{ $examId }}"
                    x-data="directExamQuestionEditor(@js($this->questionData), $wire)"
                    x-init="init()"
                    @direct-exam-question-edited.window="onSaved()"
                >

                    {{-- ── Sticky action bar ────────────────────────────────── --}}
                    <div class="sticky top-0 z-20 bg-white/90 dark:bg-gray-900/90 backdrop-blur border-b border-gray-200 dark:border-gray-700 -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 py-3 mb-5 flex items-center justify-between gap-3">

                        {{-- Change counter --}}
                        <div class="flex items-center gap-2 text-sm">
                            <span class="text-gray-500 dark:text-gray-400">
                                {{ count($this->questionData) }} question(s) loaded
                                <template x-if="filteredQuestions.length < questions.length">
                                    <span x-text="'(' + filteredQuestions.length + ' shown)'"></span>
                                </template>
                            </span>
                            <template x-if="changedCount > 0">
                                <span class="inline-flex items-center gap-1 bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400 rounded-full px-2.5 py-0.5 text-xs font-medium">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/>
                                    </svg>
                                    <span x-text="changedCount + ' unsaved change(s)'"></span>
                                </span>
                            </template>
                            <template x-if="changedCount === 0">
                                <span class="inline-flex items-center gap-1 text-gray-400 dark:text-gray-500 text-xs">
                                    No unsaved changes
                                </span>
                            </template>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex items-center gap-2">
                            <button
                                onclick="window.print()"
                                type="button"
                                class="no-print text-sm px-3 py-1.5 rounded border border-gray-300 dark:border-gray-600
                                       text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700
                                       transition-colors flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Export / Print
                            </button>
                            <button
                                @click="resetAll()"
                                x-show="changedCount > 0"
                                type="button"
                                class="text-sm px-3 py-1.5 rounded border border-gray-300 dark:border-gray-600
                                       text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700
                                       transition-colors">
                                Reset All
                            </button>

                            <button
                                @click="save()"
                                :disabled="changedCount === 0 || saving"
                                type="button"
                                class="flex items-center gap-2 text-sm px-4 py-1.5 rounded
                                       bg-blue-600 hover:bg-blue-700 text-white font-medium
                                       disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                                <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <svg x-show="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                <span x-text="saving ? 'Saving…' : 'Save Changes'"></span>
                            </button>
                        </div>
                    </div>

                    {{-- ── Info note ────────────────────────────────────────── --}}
                    <div class="mb-5 flex items-start gap-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded p-3 text-sm text-blue-700 dark:text-blue-300">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>
                            Saving will update the exam questions directly (not the question bank),
                            and automatically queue regrading for affected submissions.
                            <strong>Finalised submissions are not re-graded automatically.</strong>
                        </span>
                    </div>

                    {{-- ── Search & Sort bar ───────────────────────────────── --}}
                    <div class="mb-5 flex flex-col sm:flex-row gap-2">
                        <div class="relative flex-1">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                            </svg>
                            <input
                                type="text"
                                x-model.debounce.300ms="search"
                                placeholder="Search questions or options…"
                                class="w-full pl-9 pr-3 py-2 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded
                                       text-gray-800 dark:text-gray-200 placeholder-gray-400
                                       focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>
                        <div class="flex items-center gap-2">
                            <select x-model="sortBy"
                                    class="text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded px-3 py-2
                                           text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="index">Order: Default</option>
                                <option value="id">Order: ID</option>
                                <option value="answer">Order: Answer</option>
                                <option value="modified">Order: Modified first</option>
                            </select>
                            <button @click="sortDir = sortDir === 'asc' ? 'desc' : 'asc'"
                                    type="button"
                                    :title="sortDir === 'asc' ? 'Ascending' : 'Descending'"
                                    class="p-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                                           text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <svg class="w-4 h-4 transition-transform" :class="sortDir === 'desc' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9M3 12h5m8 0l4-4m0 0l4 4m-4-4v12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- ── Question cards ───────────────────────────────────── --}}
                    <div class="space-y-4 pb-16">
                        <template x-for="(q, index) in filteredQuestions" :key="q.id">
                            <div :class="isChanged(q.id)
                                    ? 'bg-white dark:bg-gray-800 border border-amber-400 dark:border-amber-600 shadow-md rounded'
                                    : 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded'"
                                 class="transition-colors duration-200">

                                {{-- Card header --}}
                                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 dark:bg-gray-700 text-xs font-semibold text-gray-600 dark:text-gray-300"
                                              x-text="index + 1"></span>
                                        <span class="text-xs text-gray-400 dark:text-gray-500" x-text="'Q#' + q.id"></span>
                                        <span class="text-xs text-gray-500 dark:text-gray-500" x-text="'(' + q.type.replace('_', ' ') + ')'"></span>

                                        {{-- Changed badge --}}
                                        <template x-if="isChanged(q.id)">
                                            <span class="inline-flex items-center gap-1 bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400 rounded px-2 py-0.5 text-xs font-medium">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zm-2.207 2.207L3 13.172V16h2.828l8.38-8.379-2.83-2.828z"/>
                                                </svg>
                                                Modified
                                            </span>
                                        </template>
                                        
                                        {{-- Edited badge --}}
                                        <template x-if="q.is_edited">
                                            <span class="inline-flex items-center gap-1 bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400 rounded px-2 py-0.5 text-xs font-medium">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                                                </svg>
                                                Edited
                                            </span>
                                        </template>
                                    </div>

                                    {{-- Per-question reset --}}
                                    <button
                                        x-show="isChanged(q.id)"
                                        @click="reset(q.id)"
                                        type="button"
                                        class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                                        ↩ Reset
                                    </button>
                                </div>

                                <div class="p-4 space-y-4">
                                    {{-- Question text (editable) --}}
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Question</label>
                                        <textarea
                                            x-model="edits[q.id].question"
                                            rows="3"
                                            class="w-full text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded px-3 py-2
                                                   text-gray-800 dark:text-gray-200 placeholder-gray-400
                                                   focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                            :placeholder="'Question ' + (index + 1)"
                                        ></textarea>
                                    </div>

                                    {{-- Options grid for MCQ/T&F questions --}}
                                    <template x-if="q.type === 'multiple_choice' || q.type === 'true_false'">
                                        <div class="space-y-2">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Options</label>
                                            
                                            <div class="grid grid-cols-1 gap-2">
                                                <template x-for="letter in optionLetters(q.option_count)" :key="letter">
                                                    <div class="flex items-center gap-2">
                                                        {{-- Letter badge --}}
                                                        <span :class="edits[q.id].answer === letter
                                                                ? 'bg-blue-600 text-white'
                                                                : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300'"
                                                              class="flex-shrink-0 inline-flex items-center justify-center w-7 h-7 text-xs font-bold rounded transition-colors"
                                                              x-text="letter">
                                                        </span>

                                                        {{-- Option text input --}}
                                                        <input
                                                            type="text"
                                                            x-model="edits[q.id]['option_' + letter.toLowerCase()]"
                                                            class="flex-1 text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded px-3 py-1.5
                                                                   text-gray-800 dark:text-gray-200 placeholder-gray-400
                                                                   focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                                            :placeholder="'Option ' + letter"
                                                        />

                                                        {{-- Mark as correct radio --}}
                                                        <button
                                                            @click="edits[q.id].answer = letter"
                                                            :title="'Mark ' + letter + ' as correct answer'"
                                                            type="button"
                                                            :class="edits[q.id].answer === letter
                                                                ? 'bg-green-100 dark:bg-green-900/40 border-green-500 text-green-600 dark:text-green-400'
                                                                : 'bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-400 hover:border-green-400'"
                                                            class="flex-shrink-0 w-8 h-8 rounded border-2 flex items-center justify-center transition-colors">
                                                            <svg x-show="edits[q.id].answer === letter"
                                                                 class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>

                                    {{-- For non-MCQ questions, show answer field if applicable --}}
                                    <template x-if="q.type !== 'multiple_choice' && q.type !== 'true_false'">
                                        <div class="space-y-2">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expected Answer</label>
                                            <input
                                                type="text"
                                                x-model="edits[q.id].answer"
                                                class="w-full text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded px-3 py-1.5
                                                       text-gray-800 dark:text-gray-200 placeholder-gray-400
                                                       focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                                placeholder="Enter expected answer"
                                            />
                                        </div>
                                    </template>

                                    {{-- Correct answer summary --}}
                                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 pt-1">
                                        <template x-if="q.type === 'multiple_choice' || q.type === 'true_false'">
                                            <div class="flex items-center gap-2">
                                                <span>Correct option:</span>
                                                <span class="font-semibold text-gray-700 dark:text-gray-200"
                                                      x-text="'Option ' + edits[q.id].answer"></span>
                                                <template x-if="edits[q.id].answer !== q.answer">
                                                    <span class="text-amber-600 dark:text-amber-400">
                                                        (was: Option <span x-text="q.answer"></span>)
                                                    </span>
                                                </template>
                                            </div>
                                        </template>
                                        <template x-if="q.type !== 'multiple_choice' && q.type !== 'true_false'">
                                            <div class="flex items-center gap-2">
                                                <span>Correct answer:</span>
                                                <span class="font-semibold text-gray-700 dark:text-gray-200"
                                                      x-text="edits[q.id].answer"></span>
                                                <template x-if="edits[q.id].answer !== q.answer">
                                                    <span class="text-amber-600 dark:text-amber-400">
                                                        (was: <span x-text="q.answer"></span>)
                                                    </span>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                    
                                    {{-- Marks info --}}
                                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                        <span>Marks:</span>
                                        <span class="font-semibold text-gray-700 dark:text-gray-200" x-text="q.marks"></span>
                                        <span class="ml-2">Difficulty:</span>
                                        <span class="font-semibold text-gray-700 dark:text-gray-200" x-text="q.difficulty"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                </div>{{-- /x-data --}}

            @endif
        </div>{{-- /wire:loading.remove --}}
    </div>
</div>

{{-- Alpine component — outside Livewire's DOM so it registers once and survives re-renders --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('directExamQuestionEditor', (initialQuestions, wire) => ({
            questions: initialQuestions,
            originals: {},
            edits: {},
            saving: false,
            search: '',
            sortBy: 'index',
            sortDir: 'asc',

            get filteredQuestions() {
                let list = this.questions.map((q, i) => ({ ...q, _index: i }));

                if (this.search.trim()) {
                    const term = this.search.trim().toLowerCase();
                    list = list.filter(q => {
                        const edit = this.edits[q.id] || {};
                        return [
                            q.question,
                            edit.question,
                            edit.option_a, edit.option_b, edit.option_c,
                            edit.option_d, edit.option_e,
                        ].some(v => v && String(v).toLowerCase().includes(term));
                    });
                }

                list.sort((a, b) => {
                    let av, bv;
                    if (this.sortBy === 'id') {
                        av = a.id; bv = b.id;
                    } else if (this.sortBy === 'answer') {
                        av = (this.edits[a.id]?.answer || a.answer);
                        bv = (this.edits[b.id]?.answer || b.answer);
                    } else if (this.sortBy === 'modified') {
                        av = this.isChanged(a.id) ? 0 : 1;
                        bv = this.isChanged(b.id) ? 0 : 1;
                    } else {
                        av = a._index; bv = b._index;
                    }

                    if (av < bv) return this.sortDir === 'asc' ? -1 : 1;
                    if (av > bv) return this.sortDir === 'asc' ? 1 : -1;
                    return 0;
                });

                return list;
            },

            // Available for markdown rendering if needed in the future
            renderMd(text) {
                if (!text || !window.renderMarkdownWithMath) return text || '';
                return window.renderMarkdownWithMath(text);
            },

            init() {
                this.questions.forEach(q => {
                    const snapshot = this.extractFields(q);
                    this.originals[q.id] = { ...snapshot };
                    this.edits[q.id] = { ...snapshot };
                });
            },

            // Called after Livewire confirms a successful save
            onSaved() {
                // Promote edits → originals so changed badges clear
                Object.keys(this.edits).forEach(id => {
                    this.originals[id] = { ...this.edits[id] };
                });
                this.saving = false;
            },

            extractFields(q) {
                return {
                    question: q.question ?? '',
                    answer: q.answer ?? 'A',
                    option_a: q.option_a ?? '',
                    option_b: q.option_b ?? '',
                    option_c: q.option_c ?? '',
                    option_d: q.option_d ?? '',
                    option_e: q.option_e ?? '',
                };
            },

            /**
             * Returns letter sequence ['A','B',...] up to option_count.
             * Always shows at least 4 options; caps at 5.
             */
            optionLetters(count) {
                const n = Math.min(5, Math.max(4, count || 4));
                return 'ABCDE'.slice(0, n).split('');
            },

            isChanged(questionId) {
                const orig = this.originals[questionId];
                const edit = this.edits[questionId];
                if (!orig || !edit) return false;

                return orig.question !== edit.question ||
                       orig.answer !== edit.answer ||
                       orig.option_a !== edit.option_a ||
                       orig.option_b !== edit.option_b ||
                       orig.option_c !== edit.option_c ||
                       orig.option_d !== edit.option_d ||
                       orig.option_e !== edit.option_e;
            },

            get changedCount() {
                return this.questions.filter(q => this.isChanged(q.id)).length;
            },

            /**
             * Build a diff: only include questions that changed,
             * and within each question only include the changed fields.
             * This is what gets sent to the server.
             */
            buildDiff() {
                const diff = {};
                this.questions.forEach(q => {
                    if (!this.isChanged(q.id)) return;

                    const orig = this.originals[q.id];
                    const edit = this.edits[q.id];
                    const entry = {};

                    if (orig.question !== edit.question) entry.question = edit.question;
                    if (orig.answer !== edit.answer) entry.answer = edit.answer;
                    if (orig.option_a !== edit.option_a) entry.option_a = edit.option_a;
                    if (orig.option_b !== edit.option_b) entry.option_b = edit.option_b;
                    if (orig.option_c !== edit.option_c) entry.option_c = edit.option_c;
                    if (orig.option_d !== edit.option_d) entry.option_d = edit.option_d;
                    if (orig.option_e !== edit.option_e) entry.option_e = edit.option_e;

                    diff[q.id] = entry;
                });
                return diff;
            },

            reset(questionId) {
                this.edits[questionId] = { ...this.originals[questionId] };
            },

            resetAll() {
                this.questions.forEach(q => {
                    this.edits[q.id] = { ...this.originals[q.id] };
                });
            },

            async save() {
                const diff = this.buildDiff();
                if (Object.keys(diff).length === 0) return;

                this.saving = true;
                try {
                    await wire.applyChanges(diff);
                    // Fallback: ensure state is reset even if the event dispatch fails
                    this.onSaved();
                } catch (e) {
                    this.saving = false;
                    console.error('Direct exam question save failed', e);
                }
            },
        }));
    });
</script>