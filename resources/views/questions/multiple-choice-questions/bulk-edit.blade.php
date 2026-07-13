<x-layouts.app title="Bulk Edit Questions" :has-action="false" :show-title-area="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicTopic->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            $academicTopic->academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            $academicTopic->academicSubject->name => route('academic-subjects.show', ['academic_subject' => $academicTopic->academicSubject, 'academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            $academicTopic->name => route('academic-topics.show', ['academic_topic' => $academicTopic, 'academic_subject' => $academicTopic->academicSubject, 'academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            'Multiple Choice Questions' => route('multiple-choice-questions.index', ['academic_topic' => $academicTopic, 'academic_subject' => $academicTopic->academicSubject, 'academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            'Bulk Edit' => null,
        ]"/>
    </x-slot>

    @php
        $difficultyColors = [
            'easy'        => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
            'medium'      => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300',
            'hard'        => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
            'difficult'   => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
            'unspecified' => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
        ];

        $formAction = route('multiple-choice-questions.bulk-update', [
            'academic_topic'   => $academicTopic,
            'academic_subject' => $academicSubject,
            'academic_level'   => $academicLevel,
            'academic_group'   => $academicGroup,
        ]);

        $backUrl = route('multiple-choice-questions.index', [
            'academic_topic'   => $academicTopic,
            'academic_subject' => $academicSubject,
            'academic_level'   => $academicLevel,
            'academic_group'   => $academicGroup,
        ]);
    @endphp

    <div class="min-h-screen bg-gray-50 dark:bg-gray-950 pb-28">
        <div class="container mx-auto px-4 py-6 max-w-4xl">

            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Bulk Edit Questions</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $academicTopic->name }}
                    @if($search)
                        &mdash; {{ $questions->count() }} result{{ $questions->count() === 1 ? '' : 's' }} for &ldquo;{{ $search }}&rdquo;
                    @else
                        &mdash; {{ $questions->count() }} multiple choice question{{ $questions->count() === 1 ? '' : 's' }}
                    @endif
                </p>
            </div>

            {{-- Search --}}
            <form method="GET" action="" class="mb-5">
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 0 5 11a6 6 0 0 0 12 0z"/>
                    </svg>
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Search questions…"
                           class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 pl-9 pr-10 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @if($search)
                    <a href="?" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                    @endif
                </div>
            </form>

            @if($questions->isEmpty())
            <div class="rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 p-16 text-center">
                <h3 class="text-base font-semibold text-gray-700 dark:text-gray-300">
                    {{ $search ? 'No questions match your search.' : 'No questions found.' }}
                </h3>
                @if($search)
                <a href="?" class="mt-4 inline-flex items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Clear search
                </a>
                @else
                <a href="{{ $backUrl }}" class="mt-4 inline-flex items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    &larr; Back
                </a>
                @endif
            </div>
            @else
            <form method="POST" action="{{ $formAction }}" id="bulk-edit-form" class="space-y-5">
                @csrf

                @foreach($questions as $index => $q)
                @php
                    $diffLevel  = $q->difficulty_level ?? 'medium';
                    $diffColor  = $difficultyColors[$diffLevel] ?? $difficultyColors['medium'];
                    $answer     = strtoupper($q->answer ?? '');
                @endphp

                <div class="relative rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">
                    <div class="absolute inset-y-0 left-0 w-1 bg-indigo-500"></div>

                    <div class="pl-5 pr-6 py-5">

                        {{-- Card header --}}
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 text-xs font-bold text-gray-500 dark:text-gray-400">
                                    {{ $index + 1 }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                    <span class="h-1.5 w-1.5 rounded-full bg-indigo-500"></span>
                                    Multiple Choice
                                </span>
                            </div>
                            <div class="flex items-center gap-2 text-xs">
                                <span class="rounded-full px-2.5 py-0.5 font-medium {{ $diffColor }}">
                                    {{ ucfirst($diffLevel) }}
                                </span>
                                <span class="rounded-full px-2.5 py-0.5 font-medium bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                    {{ $q->score ?? 1 }} pt{{ ($q->score ?? 1) == 1 ? '' : 's' }}
                                </span>
                            </div>
                        </div>

                        <input type="hidden" name="questions[{{ $index }}][id]" value="{{ $q->id }}">

                        {{-- Question text --}}
                        <div class="mb-5">
                            <x-form.rich-editor
                                name="questions[{{ $index }}][question]"
                                :value="old('questions.'.$index.'.question.down', $q->question->down ?? '')"
                                label="Question"
                                required
                            />
                        </div>

                        {{-- MCQ options --}}
                        <div class="space-y-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Options — select the correct answer</p>
                            @foreach(['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D', 'e' => 'E'] as $opt => $label)
                            @php
                                $isCorrect = strtoupper(old('questions.'.$index.'.answer', $answer)) === $label;
                                $optValue  = old('questions.'.$index.'.option_'.$opt, $q->{'option_'.$opt}->down ?? '');
                            @endphp
                            <div class="flex items-start gap-3 rounded-xl border transition-colors
                                {{ $isCorrect
                                    ? 'border-indigo-400 bg-indigo-50 dark:border-indigo-600 dark:bg-indigo-900/30'
                                    : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50' }}
                                px-4 py-3 option-row" data-index="{{ $index }}" data-letter="{{ $label }}">

                                <label class="flex h-5 w-5 mt-1 shrink-0 cursor-pointer items-center justify-center">
                                    <input type="radio"
                                           name="questions[{{ $index }}][answer]"
                                           value="{{ $label }}"
                                           class="answer-radio h-4 w-4 cursor-pointer text-indigo-600 border-gray-300 dark:border-gray-600 focus:ring-indigo-500"
                                           {{ $isCorrect ? 'checked' : '' }}>
                                </label>

                                <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-bold
                                    {{ $isCorrect ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }} option-badge">
                                    {{ $label }}
                                </span>

                                <textarea name="questions[{{ $index }}][option_{{ $opt }}]"
                                          rows="1"
                                          placeholder="{{ empty($optValue) ? 'Leave blank if not used' : '' }}"
                                          class="flex-1 resize-none bg-transparent text-sm text-gray-900 dark:text-white placeholder-gray-400 border-0 focus:ring-0 p-0 leading-relaxed"
                                >{{ $optValue }}</textarea>
                            </div>
                            @endforeach
                        </div>

                        {{-- Difficulty + Score --}}
                        <div class="mt-5 flex flex-wrap items-center gap-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                            <div class="flex items-center gap-2">
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">Difficulty</label>
                                <select name="questions[{{ $index }}][difficulty_level]"
                                        class="rounded-lg border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs text-gray-700 dark:text-gray-300 py-1.5 pr-7 focus:ring-indigo-500 focus:border-indigo-500">
                                    @foreach(['easy' => 'Easy', 'medium' => 'Medium', 'hard' => 'Hard', 'difficult' => 'Difficult', 'unspecified' => 'Unspecified'] as $val => $lbl)
                                    <option value="{{ $val }}" {{ old('questions.'.$index.'.difficulty_level', $diffLevel) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">Score</label>
                                <input type="number"
                                       name="questions[{{ $index }}][score]"
                                       min="0.5"
                                       step="0.5"
                                       value="{{ old('questions.'.$index.'.score', $q->score ?? 1) }}"
                                       class="w-20 rounded-lg border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs text-gray-700 dark:text-gray-300 py-1.5 text-center focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>

                    </div>
                </div>
                @endforeach

                <div class="h-4"></div>
            </form>
            @endif

        </div>
    </div>

    @if($questions->isNotEmpty())
    <div class="fixed bottom-0 inset-x-0 z-40 border-t border-gray-200 dark:border-gray-700 bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm">
        <div class="container mx-auto px-4 max-w-4xl">
            <div class="flex items-center justify-between gap-4 py-3">
                <a href="{{ $backUrl }}"
                   class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back
                </a>
                <div class="flex items-center gap-3">
                    <p class="hidden sm:block text-sm text-gray-500 dark:text-gray-400">
                        {{ $questions->count() }} question{{ $questions->count() === 1 ? '' : 's' }}
                    </p>
                    <button type="submit"
                            form="bulk-edit-form"
                            id="save-button"
                            class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span id="save-label">Save All Changes</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.answer-radio').forEach(function (radio) {
                radio.addEventListener('change', function () {
                    const qIndex = this.closest('.option-row').dataset.index;

                    document.querySelectorAll(`.option-row[data-index="${qIndex}"]`).forEach(function (row) {
                        row.classList.remove('border-indigo-400', 'bg-indigo-50', 'dark:border-indigo-600', 'dark:bg-indigo-900/30');
                        row.classList.add('border-gray-200', 'dark:border-gray-700', 'bg-gray-50', 'dark:bg-gray-800/50');
                        const badge = row.querySelector('.option-badge');
                        if (badge) {
                            badge.classList.remove('bg-indigo-600', 'text-white');
                            badge.classList.add('bg-gray-200', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-400');
                        }
                    });

                    const selectedRow = this.closest('.option-row');
                    selectedRow.classList.remove('border-gray-200', 'dark:border-gray-700', 'bg-gray-50', 'dark:bg-gray-800/50');
                    selectedRow.classList.add('border-indigo-400', 'bg-indigo-50', 'dark:border-indigo-600', 'dark:bg-indigo-900/30');
                    const badge = selectedRow.querySelector('.option-badge');
                    if (badge) {
                        badge.classList.remove('bg-gray-200', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-400');
                        badge.classList.add('bg-indigo-600', 'text-white');
                    }
                });
            });

            const form  = document.getElementById('bulk-edit-form');
            const btn   = document.getElementById('save-button');
            const label = document.getElementById('save-label');
            let submitted = false;

            form?.addEventListener('submit', function (e) {
                if (submitted) { e.preventDefault(); return; }
                submitted = true;
                btn.disabled = true;
                label.textContent = 'Saving…';
            });
        });
    </script>
</x-layouts.app>
