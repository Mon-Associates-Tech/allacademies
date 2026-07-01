<x-layouts.app title="Preview Questions" action-link-text="" :action_link="''">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Dashboard' => route('dashboard'),
            'Academic Groups' => route('academic-groups.index'),
            $academicTopic?->academicSubject?->academicLevel?->academicGroup?->name ?? ($academicSubject?->academicLevel?->academicGroup?->name ?? '') => $academicTopic?->academicSubject?->academicLevel?->academicGroup ? route('academic-groups.show', ['academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]) : ($academicSubject?->academicLevel?->academicGroup ? route('academic-groups.show', ['academic_group' => $academicSubject->academicLevel->academicGroup]) : ''),
            $academicTopic?->academicSubject?->academicLevel?->name ?? ($academicSubject?->academicLevel?->name ?? '') => $academicTopic?->academicSubject?->academicLevel ? route('academic-levels.show', ['academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]) : ($academicSubject?->academicLevel ? route('academic-levels.show', ['academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) : ''),
            $academicTopic?->academicSubject?->name ?? ($academicSubject?->name ?? '') => $academicTopic?->academicSubject ? route('academic-subjects.show', ['academic_subject' => $academicTopic->academicSubject, 'academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]) : ($academicSubject ? route('academic-subjects.show', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) : ''),
            $academicTopic?->name ?? 'Import Questions' => $academicTopic ? route('academic-topics.show', ['academic_topic' => $academicTopic, 'academic_subject' => $academicTopic->academicSubject, 'academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]) : null,
            'Preview Questions' => null,
        ]" />
    </x-slot>

    @php
        $formAction = (isset($importDriver) && $importDriver === 'ai_document' && isset($batch))
            ? route('questions.import.confirm', ['batch' => $batch])
            : ($academicTopic ? route('questions.import', [
                'academic_topic' => $academicTopic,
                'academic_subject' => $academic_subject,
                'academic_level' => $academic_level,
                'academic_group' => $academic_group,
            ]) : route('questions.subject.import', [
                'academic_subject' => $academic_subject,
                'academic_level' => $academic_level,
                'academic_group' => $academic_group,
            ]));

        $backUrl = $academicTopic
            ? route('questions.import.form', ['academic_topic' => $academicTopic, 'academic_subject' => $academic_subject, 'academic_level' => $academic_level, 'academic_group' => $academic_group])
            : route('questions.subject.import.form', ['academic_subject' => $academic_subject, 'academic_level' => $academic_level, 'academic_group' => $academic_group]);

        $typeColors = [
            'multiple_choice' => ['border' => 'border-indigo-500',    'bg' => 'bg-indigo-50 dark:bg-indigo-900/20',  'badge' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',  'dot' => 'bg-indigo-500'],
            'mcq'             => ['border' => 'border-indigo-500',    'bg' => 'bg-indigo-50 dark:bg-indigo-900/20',  'badge' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',  'dot' => 'bg-indigo-500'],
            'true_false'      => ['border' => 'border-emerald-500',   'bg' => 'bg-emerald-50 dark:bg-emerald-900/20','badge' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200','dot' => 'bg-emerald-500'],
            'essay'           => ['border' => 'border-amber-500',     'bg' => 'bg-amber-50 dark:bg-amber-900/20',   'badge' => 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200',      'dot' => 'bg-amber-500'],
        ];

        $difficultyColors = [
            'easy'   => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
            'medium' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300',
            'hard'   => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
        ];

        $questions   = $previewData['preview'] ?? [];
        $errors      = $previewData['errors'] ?? [];
        $mcqCount    = collect($questions)->filter(fn($q) => in_array(strtolower($q['type'] ?? ''), ['multiple_choice','mcq','multiple choice']))->count();
        $tfCount     = collect($questions)->filter(fn($q) => in_array(strtolower($q['type'] ?? ''), ['true_false','true/false','tf']))->count();
        $essayCount  = collect($questions)->filter(fn($q) => strtolower($q['type'] ?? '') === 'essay')->count();
    @endphp

    <div class="min-h-screen bg-gray-50 dark:bg-gray-950 pb-28">
        <div class="container mx-auto px-4 py-6 max-w-4xl">

            {{-- ── Page header ── --}}
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Review & Import
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $academicTopic?->name ?? $academicSubject?->name }}
                    @if($academicSubtopic) &rsaquo; {{ $academicSubtopic->name }} @endif
                    &mdash; check each question, edit if needed, then confirm.
                </p>
            </div>

            {{-- ── Summary pills ── --}}
            @if(count($questions) > 0)
            <div class="mb-5 flex flex-wrap gap-3">
                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-medium bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                    {{ count($questions) }} total
                </span>
                @if($mcqCount > 0)
                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300">
                    <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                    {{ $mcqCount }} multiple choice
                </span>
                @endif
                @if($tfCount > 0)
                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    {{ $tfCount }} true / false
                </span>
                @endif
                @if($essayCount > 0)
                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300">
                    <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                    {{ $essayCount }} essay
                </span>
                @endif
            </div>
            @endif

            {{-- ── Extraction warnings (AI confidence flags) ── --}}
            @if(!empty($errors))
            <div class="mb-6 rounded-xl border border-amber-200 dark:border-amber-700/50 bg-amber-50 dark:bg-amber-900/20 p-4">
                <div class="flex gap-3">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-amber-800 dark:text-amber-200">
                            {{ count($errors) }} item{{ count($errors) === 1 ? '' : 's' }} need{{ count($errors) === 1 ? 's' : '' }} your attention
                        </p>
                        <ul class="mt-1.5 space-y-0.5">
                            @foreach($errors as $error)
                            <li class="text-sm text-amber-700 dark:text-amber-300">
                                &middot; {{ is_array($error) ? ($error['message'] ?? '') : $error }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            {{-- ── Main form ── --}}
            @if(count($questions) > 0)
            <form method="POST" action="{{ $formAction }}" id="import-form" class="space-y-5">
                @csrf

                @foreach($questions as $index => $item)
                @php
                    $typeKey   = strtolower($item['type'] ?? 'multiple_choice');
                    $typeKey   = str_replace(['/', ' '], '_', $typeKey);
                    $colors    = $typeColors[$typeKey] ?? $typeColors['essay'];
                    $isMcq     = in_array($typeKey, ['multiple_choice', 'mcq']);
                    $isTf      = in_array($typeKey, ['true_false', 'true_false', 'tf']);
                    $isEssay   = $typeKey === 'essay';
                    $diffLevel = $item['difficulty_level'] ?? 'medium';
                    $diffColor = $difficultyColors[$diffLevel] ?? $difficultyColors['medium'];
                @endphp

                <div class="relative rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">

                    {{-- Colored left accent --}}
                    <div class="absolute inset-y-0 left-0 w-1 {{ $colors['dot'] }}"></div>

                    <div class="pl-5 pr-6 py-5">

                        {{-- Card header --}}
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 text-xs font-bold text-gray-500 dark:text-gray-400">
                                    {{ $item['row_number'] ?? $index + 1 }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $colors['badge'] }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $colors['dot'] }}"></span>
                                    {{ ucfirst(str_replace('_', ' ', $item['type'] ?? '')) }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2 text-xs">
                                <span class="rounded-full px-2.5 py-0.5 font-medium {{ $diffColor }}">
                                    {{ ucfirst($diffLevel) }}
                                </span>
                                <span class="rounded-full px-2.5 py-0.5 font-medium bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                    {{ $item['score'] ?? 1 }} pt{{ ($item['score'] ?? 1) == 1 ? '' : 's' }}
                                </span>
                            </div>
                        </div>

                        {{-- Hidden housekeeping fields --}}
                        <input type="hidden" name="questions[{{ $index }}][row_number]"        value="{{ $item['row_number'] ?? $index + 1 }}">
                        <input type="hidden" name="questions[{{ $index }}][type]"              value="{{ $item['type'] ?? 'multiple_choice' }}">
                        <input type="hidden" name="questions[{{ $index }}][academic_topic_id]" value="{{ $item['academic_topic_id'] ?? '' }}">
                        <input type="hidden" name="questions[{{ $index }}][academic_topic_name]" value="{{ $item['academic_topic_name'] ?? '' }}">

                        {{-- Question text --}}
                        <div class="mb-5">
                            <x-form.rich-editor
                                name="questions[{{ $index }}][question]"
                                :value="old('questions.'.$index.'.question.down', $item['question'])"
                                label="Question"
                                required
                            />
                        </div>

                        {{-- ── MCQ options + correct-answer selector ── --}}
                        @if($isMcq)
                        <div class="space-y-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Options — select the correct answer</p>
                            @foreach(['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D', 'e' => 'E'] as $opt => $label)
                            @php
                                $isCorrect = strtoupper(old('questions.'.$index.'.answer', $item['answer'] ?? '')) === $label;
                                $optValue  = old('questions.'.$index.'.option_'.$opt, $item['option_'.$opt] ?? '');
                            @endphp
                            <div class="flex items-start gap-3 rounded-xl border transition-colors
                                {{ $isCorrect
                                    ? 'border-indigo-400 bg-indigo-50 dark:border-indigo-600 dark:bg-indigo-900/30'
                                    : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50' }}
                                px-4 py-3 option-row" data-index="{{ $index }}" data-letter="{{ $label }}">

                                {{-- Radio to mark as correct --}}
                                <label class="flex h-5 w-5 mt-1 shrink-0 cursor-pointer items-center justify-center">
                                    <input type="radio"
                                           name="questions[{{ $index }}][answer]"
                                           value="{{ $label }}"
                                           class="answer-radio h-4 w-4 cursor-pointer text-indigo-600 border-gray-300 dark:border-gray-600 focus:ring-indigo-500"
                                           {{ $isCorrect ? 'checked' : '' }}>
                                </label>

                                {{-- Letter badge --}}
                                <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-bold
                                    {{ $isCorrect ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }} option-badge">
                                    {{ $label }}
                                </span>

                                {{-- Option text --}}
                                <textarea name="questions[{{ $index }}][option_{{ $opt }}]"
                                          rows="1"
                                          placeholder="{{ empty($optValue) ? 'Leave blank if not used' : '' }}"
                                          class="flex-1 resize-none bg-transparent text-sm text-gray-900 dark:text-white placeholder-gray-400 border-0 focus:ring-0 p-0 leading-relaxed"
                                >{{ $optValue }}</textarea>
                            </div>
                            @endforeach
                        </div>

                        {{-- ── True / False ── --}}
                        @elseif($isTf)
                        <div class="space-y-2">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Correct answer</p>
                            <div class="inline-flex rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden text-sm font-medium">
                                @foreach(['true' => 'True', 'false' => 'False'] as $val => $lbl)
                                @php
                                    $currentAnswer = old('questions.'.$index.'.answer', $item['answer']);
                                    $selected = ($currentAnswer === true && $val === 'true')
                                             || ($currentAnswer === false && $val === 'false')
                                             || ($currentAnswer === $val);
                                @endphp
                                <label class="flex cursor-pointer items-center gap-2 px-5 py-2.5 transition-colors
                                    {{ $selected
                                        ? 'bg-emerald-600 text-white'
                                        : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                    <input type="radio"
                                           name="questions[{{ $index }}][answer]"
                                           value="{{ $val }}"
                                           class="sr-only"
                                           {{ $selected ? 'checked' : '' }}>
                                    {{ $lbl }}
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- ── Essay ── --}}
                        @elseif($isEssay)
                        <div class="space-y-2">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Model answer / marking points</p>
                            <textarea name="questions[{{ $index }}][answer]"
                                      rows="4"
                                      class="block w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-3 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-amber-500 focus:border-transparent resize-y"
                            >{{ old('questions.'.$index.'.answer', $item['answer'] ?? '') }}</textarea>
                        </div>
                        @endif

                        {{-- Difficulty + Score (editable) --}}
                        <div class="mt-5 flex flex-wrap items-center gap-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                            <div class="flex items-center gap-2">
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">Difficulty</label>
                                <select name="questions[{{ $index }}][difficulty_level]"
                                        class="rounded-lg border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs text-gray-700 dark:text-gray-300 py-1.5 pr-7 focus:ring-indigo-500 focus:border-indigo-500">
                                    @foreach(['easy' => 'Easy', 'medium' => 'Medium', 'hard' => 'Hard'] as $val => $lbl)
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
                                       value="{{ old('questions.'.$index.'.score', $item['score'] ?? 1) }}"
                                       class="w-20 rounded-lg border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs text-gray-700 dark:text-gray-300 py-1.5 text-center focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>

                    </div>
                </div>
                @endforeach

                {{-- Spacer so last card isn't hidden by sticky bar --}}
                <div class="h-4"></div>
            </form>

            @else
            {{-- Empty state --}}
            <div class="rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 p-16 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="mt-4 text-base font-semibold text-gray-700 dark:text-gray-300">No questions extracted</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">The file had no recognisable question content. Try a different file or use the Excel template.</p>
                <a href="{{ $backUrl }}"
                   class="mt-6 inline-flex items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    &larr; Back to import
                </a>
            </div>
            @endif

        </div>
    </div>

    {{-- ── Sticky bottom action bar ── --}}
    @if(count($questions) > 0)
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
                        {{ count($questions) }} question{{ count($questions) === 1 ? '' : 's' }} ready to import
                    </p>
                    <button type="submit"
                            form="import-form"
                            id="import-button"
                            class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <span id="import-label">Confirm &amp; Import</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ── MCQ option row highlight when radio changes ────────────────
            document.querySelectorAll('.answer-radio').forEach(function (radio) {
                radio.addEventListener('change', function () {
                    const qIndex = this.closest('.option-row').dataset.index;

                    // Reset all rows for this question
                    document.querySelectorAll(`.option-row[data-index="${qIndex}"]`).forEach(function (row) {
                        row.classList.remove(
                            'border-indigo-400', 'bg-indigo-50',
                            'dark:border-indigo-600', 'dark:bg-indigo-900/30'
                        );
                        row.classList.add(
                            'border-gray-200', 'dark:border-gray-700',
                            'bg-gray-50', 'dark:bg-gray-800/50'
                        );
                        const badge = row.querySelector('.option-badge');
                        if (badge) {
                            badge.classList.remove('bg-indigo-600', 'text-white');
                            badge.classList.add('bg-gray-200', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-400');
                        }
                    });

                    // Highlight selected row
                    const selectedRow = this.closest('.option-row');
                    selectedRow.classList.remove(
                        'border-gray-200', 'dark:border-gray-700',
                        'bg-gray-50', 'dark:bg-gray-800/50'
                    );
                    selectedRow.classList.add(
                        'border-indigo-400', 'bg-indigo-50',
                        'dark:border-indigo-600', 'dark:bg-indigo-900/30'
                    );
                    const badge = selectedRow.querySelector('.option-badge');
                    if (badge) {
                        badge.classList.remove('bg-gray-200', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-400');
                        badge.classList.add('bg-indigo-600', 'text-white');
                    }
                });
            });

            // ── True/False toggle styling ──────────────────────────────────
            document.querySelectorAll('input[type="radio"][name*="[answer]"]').forEach(function (radio) {
                if (radio.classList.contains('answer-radio')) return; // already handled above
                radio.addEventListener('change', function () {
                    const groupName = this.name;
                    document.querySelectorAll(`input[name="${CSS.escape(groupName)}"]`).forEach(function (r) {
                        const label = r.closest('label');
                        if (!label) return;
                        label.classList.remove('bg-emerald-600', 'text-white');
                        label.classList.add('bg-white', 'dark:bg-gray-800', 'text-gray-700', 'dark:text-gray-300');
                    });
                    const activeLabel = this.closest('label');
                    if (activeLabel) {
                        activeLabel.classList.remove('bg-white', 'dark:bg-gray-800', 'text-gray-700', 'dark:text-gray-300');
                        activeLabel.classList.add('bg-emerald-600', 'text-white');
                    }
                });
            });

            // ── Prevent double-submit ──────────────────────────────────────
            const form   = document.getElementById('import-form');
            const btn    = document.getElementById('import-button');
            const label  = document.getElementById('import-label');
            let submitted = false;

            form?.addEventListener('submit', function (e) {
                if (submitted) { e.preventDefault(); return; }
                submitted = true;
                btn.disabled = true;
                label.textContent = 'Importing…';
            });

        });
    </script>
</x-layouts.app>