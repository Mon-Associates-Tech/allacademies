<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $exam->title }} – {{ $section->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-slate-50 dark:bg-slate-950" x-data="examSection()">

{{-- ── Top bar ──────────────────────────────────────────────────────────────── --}}
<div class="sticky top-0 z-30 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700 shadow-sm">
    <div class="max-w-3xl mx-auto px-4 py-3 flex items-center justify-between gap-4">
        <div class="flex-1 min-w-0">
            <p class="text-xs text-slate-400 dark:text-slate-500">{{ $exam->title }}</p>
            <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 truncate">
                Section {{ $sectionIndex + 1 }} of {{ $totalSections }}: {{ $section->title }}
            </p>
        </div>

        {{-- Progress bar --}}
        <div class="hidden sm:flex items-center gap-2 flex-1 max-w-[200px]">
            <div class="flex-1 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                <div class="h-full bg-violet-500 rounded-full transition-all"
                     :style="{ width: progressPercent + '%' }"></div>
            </div>
            <span class="text-xs text-slate-400" x-text="answeredCount + '/' + totalQuestions"></span>
        </div>

        {{-- Save indicator --}}
        <div class="flex items-center gap-2 shrink-0">
            <span x-show="saving" class="text-xs text-amber-500 flex items-center gap-1">
                <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                Saving…
            </span>
            <span x-show="!saving && lastSaved" class="text-xs text-emerald-500" x-text="'Saved ' + lastSaved"></span>
        </div>
    </div>
</div>

{{-- ── Main content ─────────────────────────────────────────────────────────── --}}
<div class="max-w-3xl mx-auto px-4 py-8">

    {{-- Section instructions --}}
    @if($section->instructions)
        <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-[2px] text-sm text-amber-800 dark:text-amber-300">
            <p class="font-semibold text-xs uppercase tracking-wider mb-1">Section Instructions</p>
            {{ $section->instructions }}
        </div>
    @endif

    {{-- Questions --}}
    @foreach($questions as $question)
        @php
            $storedResponse = $responses[$question->id]['response'] ?? null;
        @endphp

        <div class="mb-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-[2px] shadow-sm overflow-hidden"
             :class="answered['{{ $question->id }}'] ? 'border-l-4 border-l-emerald-400' : 'border-l-4 border-l-slate-200 dark:border-l-slate-600'">

            {{-- Question header --}}
            <div class="px-5 py-4 flex items-start justify-between gap-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-start gap-3 flex-1">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-400 text-xs font-bold shrink-0 mt-0.5">
                        {{ $loop->iteration }}
                    </span>
                    <div class="flex-1">
                        <p class="text-sm text-slate-800 dark:text-slate-200 leading-relaxed">
                            {!! nl2br(e($question->question_text)) !!}
                        </p>
                    </div>
                </div>
                <div class="shrink-0 text-right">
                    <span class="text-xs text-slate-400">{{ $question->marks }} mk{{ $question->marks != 1 ? 's' : '' }}</span>
                    <p class="text-xs text-slate-300 dark:text-slate-600">{{ $question->getTypeLabel() }}</p>
                </div>
            </div>

            {{-- Answer area --}}
            <div class="px-5 py-4">

                {{-- ── Multiple Choice ──────────────────────────────────────── --}}
                @if($question->isMultipleChoice())
                    <div class="space-y-2">
                        @foreach($question->getOptionsForDisplay() as $letter => $text)
                            <label class="flex items-start gap-3 cursor-pointer p-3 rounded-[2px] border transition-all"
                                   :class="responses['{{ $question->id }}'] === '{{ $letter }}'
                                       ? 'border-violet-400 bg-violet-50 dark:bg-violet-900/20 dark:border-violet-600'
                                       : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600'">
                                <input type="radio"
                                       name="q_{{ $question->id }}"
                                       value="{{ $letter }}"
                                       x-model="responses['{{ $question->id }}']"
                                       @change="saveResponse({{ $question->id }}, $event.target.value)"
                                       {{ $storedResponse === $letter ? 'checked' : '' }}
                                       class="accent-violet-600 mt-0.5 shrink-0">
                                <span class="text-sm text-slate-700 dark:text-slate-300">
                                    <span class="font-mono font-bold text-slate-400 mr-1">{{ $letter }}.</span>
                                    {{ $text }}
                                </span>
                            </label>
                        @endforeach
                    </div>

                {{-- ── True / False ─────────────────────────────────────────── --}}
                @elseif($question->isTrueFalse())
                    <div class="flex gap-3">
                        @foreach(['true' => 'True', 'false' => 'False'] as $val => $label)
                            <label class="flex-1 flex items-center justify-center gap-2 cursor-pointer p-3 rounded-[2px] border transition-all"
                                   :class="responses['{{ $question->id }}'] === '{{ $val }}'
                                       ? 'border-violet-400 bg-violet-50 dark:bg-violet-900/20 dark:border-violet-600'
                                       : 'border-slate-200 dark:border-slate-700 hover:border-slate-300'">
                                <input type="radio"
                                       name="q_{{ $question->id }}"
                                       value="{{ $val }}"
                                       x-model="responses['{{ $question->id }}']"
                                       @change="saveResponse({{ $question->id }}, $event.target.value)"
                                       {{ $storedResponse === $val ? 'checked' : '' }}
                                       class="accent-violet-600">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>

                {{-- ── Essay ────────────────────────────────────────────────── --}}
                @elseif($question->isEssay())
                    <div class="space-y-2">
                        <textarea
                            name="q_{{ $question->id }}"
                            rows="6"
                            x-model="responses['{{ $question->id }}']"
                            @blur="saveResponse({{ $question->id }}, $event.target.value)"
                            @keydown.ctrl.s.prevent="saveResponse({{ $question->id }}, responses['{{ $question->id }}'])"
                            placeholder="Write your answer here… (Ctrl+S to save)"
                            class="w-full px-4 py-3 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white resize-y transition-all"
                        >{{ $storedResponse }}</textarea>
                        <p class="text-xs text-slate-400">
                            <span x-text="(responses['{{ $question->id }}'] || '').length"></span> characters ·
                            Auto-saves on blur or Ctrl+S
                        </p>
                    </div>
                @endif

                {{-- Answered indicator --}}
                <div class="mt-2 flex items-center gap-1" x-show="answered['{{ $question->id }}']">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="text-xs text-emerald-500">Saved</span>
                </div>
            </div>
        </div>
    @endforeach

    {{-- ── Navigation ──────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between mt-8 pt-6 border-t border-slate-200 dark:border-slate-700">

        {{-- Previous --}}
        @if($sectionIndex > 0)
            <a href="{{ route('mock-exams.take.section', [$exam, $sectionIndex - 1]) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-[2px] transition-all">
                ← Previous Section
            </a>
        @else
            <div></div>
        @endif

        {{-- Next or Submit --}}
        @if($sectionIndex < $totalSections - 1)
            <a href="{{ route('mock-exams.take.section', [$exam, $sectionIndex + 1]) }}"
               class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-violet-600 to-violet-500 hover:from-violet-700 hover:to-violet-600 text-white font-semibold rounded-[2px] text-sm shadow-[0_2px_10px_rgba(124,58,237,0.3)] transition-all">
                Next Section →
            </a>
        @else
            {{-- Final section – show submit --}}
            <button type="button"
                    @click="confirmSubmit = true"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600 text-white font-semibold rounded-[2px] text-sm shadow-[0_2px_10px_rgba(5,150,105,0.3)] transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Submit Exam
            </button>
        @endif
    </div>

    {{-- Section progress dots --}}
    @if($totalSections > 1)
        <div class="flex items-center justify-center gap-2 mt-6">
            @for($i = 0; $i < $totalSections; $i++)
                <a href="{{ route('mock-exams.take.section', [$exam, $i]) }}"
                   class="w-2.5 h-2.5 rounded-full transition-all {{ $i === $sectionIndex ? 'bg-violet-500 scale-125' : 'bg-slate-300 dark:bg-slate-600 hover:bg-slate-400' }}"></a>
            @endfor
        </div>
    @endif
</div>

{{-- ── Submit confirmation modal ───────────────────────────────────────────── --}}
<div x-show="confirmSubmit"
     x-transition
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-[2px] shadow-xl max-w-md w-full p-6">
        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Submit Examination?</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
            You have answered <strong x-text="answeredCount"></strong> of <strong>{{ $questions->count() }}</strong> questions in this section.
            Once submitted, you cannot make changes.
        </p>
        <div class="flex gap-3">
            <form method="POST" action="{{ route('mock-exams.take.submit', $exam) }}" class="flex-1">
                @csrf
                <button type="submit"
                        class="w-full px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-500 text-white font-semibold rounded-[2px] text-sm transition-all hover:from-emerald-700 hover:to-emerald-600">
                    Yes, Submit Now
                </button>
            </form>
            <button type="button" @click="confirmSubmit = false"
                    class="flex-1 px-4 py-2.5 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-medium rounded-[2px] text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                Continue Reviewing
            </button>
        </div>
    </div>
</div>

<script>
function examSection() {
    // Seed current responses from the server-side data
    const serverResponses = @json(collect($responses)->mapWithKeys(fn($r, $id) => [(string)$id => $r['response'] ?? null]));

    return {
        confirmSubmit: false,
        saving: false,
        lastSaved: null,
        responses: { ...serverResponses },

        get answered() {
            const result = {};
            Object.keys(this.responses).forEach(id => {
                result[id] = this.responses[id] !== null && this.responses[id] !== '';
            });
            return result;
        },

        get answeredCount() {
            return Object.values(this.responses).filter(v => v !== null && v !== '').length;
        },

        get totalQuestions() {
            return {{ $questions->count() }};
        },

        get progressPercent() {
            return this.totalQuestions > 0
                ? Math.round((this.answeredCount / this.totalQuestions) * 100)
                : 0;
        },

        saveResponse(questionId, value) {
            if (value === null || value === undefined) return;
            this.responses[String(questionId)] = value;
            this.saving = true;

            fetch('{{ route('mock-exams.take.response', $exam) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    question_id:   questionId,
                    response:      String(value),
                    section_index: {{ $sectionIndex }},
                }),
            })
            .then(r => r.json())
            .then(() => {
                this.lastSaved = new Date().toLocaleTimeString();
            })
            .catch(err => console.error('Save failed:', err))
            .finally(() => { this.saving = false; });
        },
    };
}
</script>

</body>
</html>
