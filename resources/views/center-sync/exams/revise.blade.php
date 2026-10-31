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
                    Revise — {{ $exam->title }}
                </h1>
                <p class="text-slate-400 mt-2 text-sm">After publishing, only grading changes are allowed. Changes will create a new revision.</p>
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

        {{-- ── REVISE FORM ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5"
                     style="background: linear-gradient(180deg, #b45309, #d97706); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                    style="letter-spacing: 0.1em;">Grading Corrections</h2>
            </div>

            <form method="POST" action="{{ route('center-sync.exams.revise', $exam) }}">
                @csrf
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Question</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Answer Key</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Marks</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Excluded</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Award on Exclude</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                            @foreach($exam->sections as $section)
                                @foreach($section->questions as $q)
                                    <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors">
                                        <td class="px-6 py-3.5 max-w-md">
                                            @php
                                                $questionText = is_object($q->question) ? ($q->question->down ?? '') : ($q->question ?? '');
                                                $plainQuestionText = is_string($questionText) ? strip_tags($questionText) : '';
                                            @endphp
                                            <div class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ Str::limit($plainQuestionText, 80) }}</div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">Section: {{ $section->title ?? '' }}</div>
                                        </td>
                                        <td class="px-6 py-3.5">
                                            <input type="hidden" name="answer_keys[{{ $loop->parent->index * 100 + $loop->index }}][question_id]" value="{{ $q->id }}">
                                            <input type="text" name="answer_keys[{{ $loop->parent->index * 100 + $loop->index }}][correct_answer]" value="{{ $q->correct_answer ?? '' }}" maxlength="1"
                                                class="w-16 px-3 py-2 text-sm font-mono uppercase border border-slate-300 dark:border-slate-700 rounded-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all">
                                        </td>
                                        <td class="px-6 py-3.5">
                                            <input type="hidden" name="marks[{{ $loop->parent->index * 100 + $loop->index }}][question_id]" value="{{ $q->id }}">
                                            <input type="number" name="marks[{{ $loop->parent->index * 100 + $loop->index }}][marks]" value="{{ $q->marks ?? '1.00' }}" step="0.01" min="0"
                                                class="w-20 px-3 py-2 text-sm border border-slate-300 dark:border-slate-700 rounded-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all">
                                        </td>
                                        <td class="px-6 py-3.5 text-center">
                                            <input type="hidden" name="excluded_questions[{{ $loop->parent->index * 100 + $loop->index }}][question_id]" value="{{ $q->id }}">
                                            <input type="hidden" name="excluded_questions[{{ $loop->parent->index * 100 + $loop->index }}][award_marks]" value="0">
                                            <input type="checkbox" name="excluded_questions[{{ $loop->parent->index * 100 + $loop->index }}][excluded]" value="1"
                                                {{ $q->excluded_from_grading ? 'checked' : '' }}
                                                class="rounded-sm border-slate-300 dark:border-slate-600 text-amber-600 focus:ring-amber-500">
                                        </td>
                                        <td class="px-6 py-3.5 text-center">
                                            <input type="checkbox" name="excluded_questions[{{ $loop->parent->index * 100 + $loop->index }}][award_marks]" value="1"
                                                {{ $q->award_marks_on_exclusion ? 'checked' : '' }}
                                                class="rounded-sm border-slate-300 dark:border-slate-600 text-amber-600 focus:ring-amber-500">
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
                        Create Revision
                    </button>
                </div>
            </form>
        </div>

    </div>{{-- /container --}}
</x-layouts.app>
