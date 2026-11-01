<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
         style="font-family: 'system-ui', -apple-system, sans-serif;">

        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden"
             style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #065f46, #059669, #10b981);"></div>
            <div class="px-7 py-6">
                <a href="{{ route('center-sync.exams.show', $exam) }}"
                   class="inline-flex items-center gap-1 text-sm text-slate-400 hover:text-slate-200 transition-colors mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                    </svg>
                    Back to Exam
                </a>
                <h1 class="text-2xl font-bold text-white leading-snug"
                    style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                    Republish Exam
                </h1>
                <p class="text-slate-400 mt-2 text-sm">Edit questions, answer keys, marks, and exclusions. Changes will create a new revision that centers will pull automatically.</p>
            </div>
        </div>

        {{-- ── FLASH MESSAGES ── --}}
        @if(session('success'))
            <div class="bg-white dark:bg-slate-900 overflow-hidden rounded-sm border border-emerald-500/20 shadow-[0_1px_6px_rgba(5,150,105,0.08)]">
                <div class="px-5 py-4 flex items-center gap-2 bg-gradient-to-br from-emerald-50/[0.3] to-green-50/[0.1]">
                    <div class="w-1 h-5 bg-gradient-to-b from-emerald-600 to-emerald-400 rounded-sm"></div>
                    <p class="text-sm text-emerald-800 dark:text-emerald-200 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-white dark:bg-slate-900 overflow-hidden rounded-sm border border-red-500/20 shadow-[0_1px_6px_rgba(220,38,38,0.08)]">
                <div class="px-5 py-4 flex items-center gap-2 bg-gradient-to-br from-red-50/[0.3] to-orange-50/[0.1]">
                    <div class="w-1 h-5 bg-gradient-to-b from-red-600 to-red-400 rounded-sm"></div>
                    <p class="text-sm text-red-800 dark:text-red-200 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        {{-- ── CORRECTIONS FORM ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5"
                     style="background: linear-gradient(180deg, #b45309, #d97706); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                    style="letter-spacing: 0.1em;">Question Corrections</h2>
            </div>

            <form method="POST" action="{{ route('center-sync.corrections.update', $exam) }}" id="correctionsForm">
                @csrf
                @method('PATCH')

                {{-- Summary bar --}}
                <div class="px-5 py-3 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between">
                    <span class="text-xs text-slate-500 dark:text-slate-400">
                        {{ $exam->sections->sum(fn($s) => $s->questions->count()) }} questions across {{ $exam->sections->count() }} sections
                    </span>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="expandAll()" class="text-xs font-semibold text-amber-700 dark:text-amber-400 hover:text-amber-900 dark:hover:text-amber-300 transition-colors">
                            Expand All
                        </button>
                        <button type="button" onclick="collapseAll()" class="text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                            Collapse All
                        </button>
                    </div>
                </div>

                {{-- Questions by section --}}
                @foreach($exam->sections as $section)
                    <div class="border-b border-slate-100 dark:border-slate-800">
                        <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800/30 flex items-center gap-2">
                            <div class="w-1 h-4 bg-gradient-to-b from-violet-600 to-violet-400 rounded-sm"></div>
                            <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider" style="letter-spacing: 0.08em;">
                                {{ $section->title ?? 'Section ' . ($loop->index + 1) }}
                            </h3>
                            <span class="text-xs text-slate-400 dark:text-slate-500">({{ $section->questions->count() }} questions)</span>
                        </div>

                        @foreach($section->questions as $q)
                            @php
                                $questionIndex = $loop->parent->index * 100 + $loop->index;
                                $questionText = is_object($q->question) ? ($q->question->down ?? '') : ($q->question ?? '');
                                $plainQuestionText = strip_tags($questionText);
                                $options = $q->options ?? [];
                                // Normalize options to keyed array, handling Mark objects, arrays, and JSON strings
                                $optionsDisplay = [];
                                if (!empty($options)) {
                                    foreach ($options as $opt) {
                                        if (is_array($opt) && isset($opt['key'])) {
                                            $optValue = $opt['value'] ?? '';
                                            // Handle Mark object (has up/down properties)
                                            if (is_object($optValue)) {
                                                $optValue = $optValue->down ?? $optValue->up ?? '';
                                            }
                                            // Handle Mark array format ['up' => ..., 'down' => ...]
                                            if (is_array($optValue)) {
                                                $optValue = $optValue['down'] ?? $optValue['up'] ?? '';
                                            }
                                            // Handle JSON-encoded Mark string '{"up":"...","down":"..."}'
                                            if (is_string($optValue)) {
                                                $decoded = json_decode($optValue, true);
                                                if (is_array($decoded)) {
                                                    $optValue = $decoded['down'] ?? $decoded['up'] ?? $optValue;
                                                }
                                            }
                                            // Now it should be a string
                                            if (is_string($optValue)) {
                                                $optionsDisplay[$opt['key']] = strip_tags($optValue);
                                            } else {
                                                $optionsDisplay[$opt['key']] = '';
                                            }
                                        }
                                    }
                                }
                            @endphp

                            <div class="question-row border-b border-slate-50 dark:border-slate-800/50 last:border-b-0">
                                {{-- Question header (collapsible) --}}
                                <div class="px-5 py-3 flex items-center gap-4 cursor-pointer hover:bg-amber-50/30 dark:hover:bg-slate-800/30 transition-colors"
                                     onclick="toggleQuestion('q{{ $questionIndex }}')">
                                    <button type="button" class="expand-btn text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors flex-shrink-0">
                                        <svg class="w-4 h-4 transform transition-transform" id="expandIcon-q{{ $questionIndex }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs font-mono font-bold text-slate-400 dark:text-slate-500 flex-shrink-0">Q{{ $loop->parent->index * 100 + $loop->index + 1 }}</span>
                                            <span class="text-sm font-semibold text-slate-800 dark:text-slate-200 truncate">{{ Str::limit($plainQuestionText, 100) }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 flex-shrink-0">
                                        {{-- Correct answer badge --}}
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-mono font-bold rounded-sm bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                            Answer: {{ $q->correct_answer ?? '?' }}
                                        </span>
                                        {{-- Marks badge --}}
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-mono font-bold rounded-sm bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                                            {{ $q->marks ?? '1.00' }} marks
                                        </span>
                                        {{-- Excluded badge --}}
                                        @if($q->excluded_from_grading)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-bold rounded-sm bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300">
                                                Excluded
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Expanded edit area --}}
                                <div id="q{{ $questionIndex }}" class="hidden px-5 py-4 bg-slate-50/50 dark:bg-slate-800/20 border-t border-slate-100 dark:border-slate-800/50">
                                    <input type="hidden" name="questions[{{ $questionIndex }}][id]" value="{{ $q->id }}">

                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                        {{-- Left column: Question text & options --}}
                                        <div class="space-y-4">
                                            {{-- Question text (editable) --}}
                                            <div>
                                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5" style="letter-spacing: 0.08em;">
                                                    Question Text (editable)
                                                </label>
                                                <textarea
                                                    name="questions[{{ $questionIndex }}][question]"
                                                    rows="4"
                                                    class="w-full px-3 py-2.5 text-sm text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all resize-y"
                                                    style="border-radius: 2px;">{{ $plainQuestionText }}</textarea>
                                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Changes will create a new revision that centers will pull automatically.</p>
                                            </div>

                                            {{-- Options (editable for MCQ) --}}
                                            @if($q->isMultipleChoice() && !empty($optionsDisplay))
                                                <div>
                                                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5" style="letter-spacing: 0.08em;">
                                                        Options (editable)
                                                    </label>
                                                    <div class="space-y-2">
                                                        @foreach($optionsDisplay as $key => $value)
                                                            <div class="flex items-center gap-2">
                                                                <span class="w-6 h-6 flex items-center justify-center text-xs font-mono font-bold rounded-sm
                                                                    {{ $key === strtoupper($q->correct_answer ?? '') ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400' }}">
                                                                    {{ $key }}
                                                                </span>
                                                                <input type="hidden" name="questions[{{ $questionIndex }}][options][{{ $loop->index }}][key]" value="{{ $key }}">
                                                                <input type="text"
                                                                       name="questions[{{ $questionIndex }}][options][{{ $loop->index }}][value]"
                                                                       value="{{ $value }}"
                                                                       class="flex-1 px-3 py-1.5 text-sm border border-slate-300 dark:border-slate-700 rounded-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                                                       style="border-radius: 2px;">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Right column: Editable fields --}}
                                        <div class="space-y-4">
                                            {{-- Correct Answer --}}
                                            <div>
                                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5" style="letter-spacing: 0.08em;">
                                                    Correct Answer
                                                </label>
                                                @if($q->isMultipleChoice())
                                                    <select name="questions[{{ $questionIndex }}][correct_answer]"
                                                            class="w-24 px-3 py-2 text-sm font-mono uppercase border border-slate-300 dark:border-slate-700 rounded-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                                            style="border-radius: 2px;">
                                                        @foreach(array_keys($optionsDisplay) as $optKey)
                                                            <option value="{{ $optKey }}" {{ $q->correct_answer == $optKey ? 'selected' : '' }}>
                                                                {{ $optKey }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @elseif($q->isTrueFalse())
                                                    <select name="questions[{{ $questionIndex }}][correct_answer]"
                                                            class="w-24 px-3 py-2 text-sm font-mono uppercase border border-slate-300 dark:border-slate-700 rounded-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                                            style="border-radius: 2px;">
                                                        <option value="true" {{ strtoupper($q->correct_answer ?? '') === 'TRUE' ? 'selected' : '' }}>True</option>
                                                        <option value="false" {{ strtoupper($q->correct_answer ?? '') === 'FALSE' ? 'selected' : '' }}>False</option>
                                                    </select>
                                                @else
                                                    <input type="text"
                                                           name="questions[{{ $questionIndex }}][correct_answer]"
                                                           value="{{ $q->correct_answer ?? '' }}"
                                                           class="w-32 px-3 py-2 text-sm border border-slate-300 dark:border-slate-700 rounded-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                                           style="border-radius: 2px;">
                                                @endif
                                            </div>

                                            {{-- Marks --}}
                                            <div>
                                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5" style="letter-spacing: 0.08em;">
                                                    Marks
                                                </label>
                                                <input type="number"
                                                       name="questions[{{ $questionIndex }}][marks]"
                                                       value="{{ $q->marks ?? '1.00' }}"
                                                       step="0.01"
                                                       min="0"
                                                       class="w-24 px-3 py-2 text-sm border border-slate-300 dark:border-slate-700 rounded-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                                       style="border-radius: 2px;">
                                            </div>

                                            {{-- Exclusion settings --}}
                                            <div class="space-y-2">
                                                <label class="flex items-center gap-3 cursor-pointer">
                                                    <input type="hidden" name="questions[{{ $questionIndex }}][excluded_from_grading]" value="0">
                                                    <input type="checkbox"
                                                           name="questions[{{ $questionIndex }}][excluded_from_grading]"
                                                           value="1"
                                                           {{ $q->excluded_from_grading ? 'checked' : '' }}
                                                           class="w-4 h-4 rounded-sm border-slate-300 dark:border-slate-600 text-amber-600 focus:ring-amber-500">
                                                    <span class="text-sm text-slate-700 dark:text-slate-300">Exclude from grading</span>
                                                </label>
                                                <label class="flex items-center gap-3 cursor-pointer">
                                                    <input type="hidden" name="questions[{{ $questionIndex }}][award_marks_on_exclusion]" value="0">
                                                    <input type="checkbox"
                                                           name="questions[{{ $questionIndex }}][award_marks_on_exclusion]"
                                                           value="1"
                                                           {{ $q->award_marks_on_exclusion ? 'checked' : '' }}
                                                           class="w-4 h-4 rounded-sm border-slate-300 dark:border-slate-600 text-amber-600 focus:ring-amber-500">
                                                    <span class="text-sm text-slate-700 dark:text-slate-300">Award marks if excluded</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach

                {{-- Form actions --}}
                <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-800/50">
                    <a href="{{ route('center-sync.exams.show', $exam) }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200 transition-all border border-slate-200/50 rounded-sm bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800/50 dark:to-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white transition-all rounded-sm bg-gradient-to-br from-violet-600 to-violet-400 shadow-[0_2px_10px_rgba(124,58,237,0.3)] hover:from-violet-700 hover:to-violet-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Apply Corrections & Create Revision
                    </button>
                </div>
            </form>
        </div>

    </div>{{-- /container --}}

    {{-- Expand/Collapse JS --}}
    <script>
        function toggleQuestion(id) {
            const el = document.getElementById(id);
            const icon = document.getElementById('expandIcon-' + id);
            if (el.classList.contains('hidden')) {
                el.classList.remove('hidden');
                icon.style.transform = 'rotate(90deg)';
            } else {
                el.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }
        function expandAll() {
            document.querySelectorAll('[id^="q"]').forEach(el => {
                el.classList.remove('hidden');
                const iconId = 'expandIcon-' + el.id;
                const icon = document.getElementById(iconId);
                if (icon) icon.style.transform = 'rotate(90deg)';
            });
        }
        function collapseAll() {
            document.querySelectorAll('[id^="q"]').forEach(el => {
                el.classList.add('hidden');
                const iconId = 'expandIcon-' + el.id;
                const icon = document.getElementById(iconId);
                if (icon) icon.style.transform = 'rotate(0deg)';
            });
        }
    </script>
</x-layouts.app>
