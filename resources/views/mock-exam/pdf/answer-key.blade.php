<x-print>
    {{-- ── SCREEN ONLY: Header & Actions ── --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6 print:hidden">
        @php
            $backUrl = isset($fromSubscription) && $fromSubscription
                ? route('mock-exams.subscription-preview', $mockExam)
                : route('mock-exams.pdf.preview', $mockExam);
        @endphp
        <x-mock-header
            :title="$mockExam->title . ' – Answer Key'"
            subtitle="Confidential Marking Scheme for Instructors"
            :back-url="$backUrl">
            <x-slot:actions>
                <button onclick="window.print()"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all hover:shadow-lg"
                        style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #6d28d9);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print Answer Key
                </button>
            </x-slot:actions>
        </x-mock-header>

        {{-- Screen Confidential Warning --}}
        <div class="bg-amber-50 border border-amber-200 rounded-sm p-4 flex items-center gap-3 text-amber-800 text-sm">
            <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span><strong>Confidential:</strong> This marking scheme is for instructor use only. Do not distribute to students.</span>
        </div>
    </div>

    {{-- ── DOCUMENT CONTENT (Screen & Print Optimized) ── --}}
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        <div
            class="bg-white shadow-sm border border-slate-200 rounded-sm p-8 sm:p-12 print:shadow-none print:border-none print:p-0 print:max-w-none print:bg-transparent">

            {{-- Print-Only Header --}}
            <div class="hidden print:block border-b-2 border-slate-900 pb-4 mb-8">
                <h1 class="text-2xl print:text-4xl font-bold text-slate-900 font-serif uppercase tracking-wider">{{ $mockExam->title }}</h1>
                <div class="flex justify-between items-center mt-2 text-sm print:text-base text-slate-600 uppercase tracking-wide">
                    <span>Answer Key / Marking Scheme</span>
                    <span>Generated: {{ now()->format('d M Y') }}</span>
                </div>
                <div
                    class="mt-4 text-xs print:text-sm font-bold text-red-700 uppercase tracking-widest text-center border border-red-600 py-1">
                    ⚠ Confidential – Instructor Use Only – Do Not Distribute ⚠
                </div>
            </div>

            {{-- Screen-Only Title --}}
            <div class="print:hidden mb-8 border-b border-slate-200 pb-6">
                <h1 class="text-3xl font-bold text-slate-900 font-serif tracking-tight">{{ $mockExam->title }}</h1>
                <p class="text-slate-500 mt-2 uppercase tracking-widest text-sm font-medium">Answer Key / Marking
                    Scheme</p>
            </div>

            {{-- Subject Exams --}}
            <div class="space-y-12 print:space-y-0">
                @foreach($mockExam->subjectExams as $seIdx => $se)
                    <div class="{{ $seIdx > 0 ? 'print:break-before-page' : '' }} space-y-8 print:space-y-6">

                        {{-- Subject Header --}}
                        <div
                            class="border-l-4 border-slate-900 bg-slate-900 text-white p-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 print:bg-transparent print:text-black print:border-l-4 print:border-slate-700 print:p-0 print:border-b-3 print:pb-3 print:mb-4">
                            <h2 class="text-xl print:text-3xl font-bold font-serif uppercase tracking-wide">{{ $se->getDisplayTitle() }}</h2>
                            <span class="text-sm print:text-base font-medium text-slate-300 print:text-slate-600">
                                Total: {{ number_format($se->sections->sum(fn($s) => $s->getTotalMarks()), 1) }} marks
                            </span>
                        </div>

                        {{-- Sections --}}
                        @foreach($se->sections as $sIdx => $section)
                            @php
                                $allQs   = $section->questions;
                                $objQs   = $allQs->filter(fn($q) => $q->isMultipleChoice() || $q->isTrueFalse())->values();
                                $essayQs = $allQs->filter(fn($q) => $q->isEssay())->values();
                            @endphp

                            <div class="space-y-6 print:space-y-6">
                                {{-- Section Header --}}
                                <div class="flex justify-between items-end border-b-2 border-slate-300 pb-2">
                                    <h3 class="text-lg print:text-2xl font-bold text-slate-800 uppercase tracking-wide">{{ $section->title }}</h3>
                                    <span class="text-sm print:text-base text-slate-500 font-medium">
                                        {{ number_format($section->getTotalMarks(), 1) }} marks
                                    </span>
                                </div>

                                {{-- Objective answers --}}
                                @if($objQs->isNotEmpty())
                                    <div class="columns-1 sm:columns-2 lg:columns-3 print:columns-2 gap-x-8">
                                        @foreach($objQs as $idx => $q)
                                            @php
                                                $qNum = $idx + 1;

                                                if ($q->isMultipleChoice()) {
                                                    $answer = strtoupper($q->correct_answer ?? '—');
                                                    $ansClass = 'text-slate-900 font-bold';
                                                } else {
                                                    $raw = strtolower(trim((string)($q->correct_answer ?? '')));
                                                    $isTrue = in_array($raw, ['true','1','yes','t'], true);

                                                    if ($raw === '') {
                                                        $answer = '—';
                                                        $ansClass = 'text-slate-400';
                                                    } elseif ($isTrue) {
                                                        $answer = 'True';
                                                        $ansClass = 'text-emerald-700 font-semibold';
                                                    } else {
                                                        $answer = 'False';
                                                        $ansClass = 'text-rose-700 font-semibold italic';
                                                    }
                                                }
                                            @endphp

                                            <div
                                                class="mb-2 break-inside-avoid flex items-baseline gap-2 border-b border-slate-100 pb-1 print:border-none print:pb-2 print:mb-3">
                <span class="w-8 shrink-0 font-mono text-sm print:text-lg text-slate-500">
                    {{ $qNum }}.
                </span>

                                                <span class="flex-1 font-serif text-sm print:text-xl {{ $ansClass }}">
                    {{ $answer }}
                </span>

                                                <span class="text-xs print:text-base text-slate-400 shrink-0">
                    ({{ $q->marks }} mk)
                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Essay Answers --}}
                                @if($essayQs->isNotEmpty())
                                    <div class="space-y-6 print:space-y-6">
                                        @foreach($essayQs as $eIdx => $question)
                                            <div
                                                class="border border-slate-200 border-l-4 border-l-slate-600 rounded-sm p-5 bg-slate-50 print:bg-transparent print:border-slate-400 print:p-4 print:break-inside-avoid print:mb-6">
                                                <div class="flex justify-between items-start gap-4 mb-3 print:mb-4">
                                                    <div class="flex items-start gap-2">
                                                        <span class="font-bold text-slate-900 shrink-0 text-sm print:text-lg">Q{{ $objQs->count() + $eIdx + 1 }}.</span>
                                                        <p class="text-slate-800 text-sm print:text-lg leading-relaxed">
                                                            <x-ui.latex :content="$question->question_text"/>
                                                        </p>
                                                    </div>
                                                    <span
                                                        class="text-xs print:text-sm font-bold text-slate-500 bg-slate-200 px-2 py-1 rounded shrink-0 print:bg-transparent print:border print:border-slate-400">
                                                        [{{ $question->marks }} marks]
                                                    </span>
                                                </div>

                                                <div class="mt-4 print:mt-6">
                                                    <h4 class="text-xs print:text-sm font-bold text-slate-500 uppercase tracking-wider mb-1 border-b border-slate-200 pb-1">
                                                        Model Answer</h4>
                                                    @if($question->answer_explanation)
                                                        <p class="text-slate-700 text-sm print:text-lg leading-relaxed">
                                                            <x-ui.latex :content="$question->answer_explanation"/>
                                                        </p>
                                                    @else
                                                        <p class="text-slate-400 text-sm print:text-lg italic">No model answer
                                                            stored.</p>
                                                    @endif
                                                </div>

                                                @if(!empty($question->answer_keywords))
                                                    <div class="mt-3 print:mt-4">
                                                        <h4 class="text-xs print:text-sm font-bold text-slate-500 uppercase tracking-wider mb-1">
                                                            Key Terms / Keywords</h4>
                                                        <p class="text-slate-600 text-xs print:text-base italic">
                                                            {{ implode('  ·  ', $question->answer_keywords) }}
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            @if(!$loop->last)
                                <hr class="border-slate-200 print:hidden">
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── PRINT ONLY: Fixed Footer ── --}}
    <div class="print-footer hidden print:flex">
        <span>{{ $mockExam->title }}</span>
        <span class="font-bold uppercase tracking-wider">Marking Scheme — Confidential</span>
        <span>Page <span class="pagenum"></span> · {{ now()->format('d M Y') }}</span>
    </div>

    <style>
        @media print {
            @page {
                margin: 20mm 15mm;
            }

            html,
            body {
                height: auto !important;
                overflow: visible !important;
                background: #fff !important;
                font-size: 14pt !important;
            }

            /*
                Force common app-shell containers to expand during print.
                This fixes layouts that use h-screen / overflow-hidden / overflow-y-auto.
            */
            main,
            #app,
            [id^="app"],
            .h-screen,
            .min-h-screen,
            .max-h-screen,
            .overflow-hidden,
            .overflow-y-auto,
            .overflow-auto,
            .flex-1,
            [class*="h-["],
            [class*="min-h-["],
            [class*="max-h-["] {
                height: auto !important;
                max-height: none !important;
                min-height: 0 !important;
                overflow: visible !important;
            }

            /*
                If the layout uses a flex shell, make the main content behave like
                a normal printable document block.
            */
            main {
                display: block !important;
                width: 100% !important;
            }

            /* Hide app chrome if not already hidden */
            nav,
            aside,
            .sidebar,
            .no-print {
                display: none !important;
            }

            /* Make sure column layouts are not clipped */
            [class*="columns-"] {
                height: auto !important;
                overflow: visible !important;
            }

            /* ── GLOBAL: Scale up ALL text inside the answer key document ── */
            .bg-white.shadow-sm {
                font-size: 14pt !important;
            }

            /* ── PRINT HEADER ── */
            .print-block h1 {
                font-size: 24pt !important;
            }
            .print-block div span {
                font-size: 12pt !important;
            }

            /* ── SUBJECT HEADERS ── */
            /* Target: the border-l-4 bg-slate-900 subject header div */
            .border-l-4.bg-slate-900 {
                background: transparent !important;
                color: #000 !important;
                border-left: none !important;
                border-bottom: 3px solid #1e293b !important;
                padding: 0 0 8px 0 !important;
                margin-bottom: 16px !important;
            }
            .border-l-4.bg-slate-900 h2 {
                font-size: 20pt !important;
                font-weight: 700 !important;
            }
            .border-l-4.bg-slate-900 span {
                font-size: 12pt !important;
            }

            /* ── SECTION HEADERS ── */
            /* Target: flex justify-between items-end border-b-2 (section header) */
            .flex.justify-between.items-end.border-b-2 {
                border-bottom: 2px solid #475569 !important;
                padding-bottom: 6px !important;
                margin-bottom: 12px !important;
            }
            .flex.justify-between.items-end.border-b-2 h3 {
                font-size: 16pt !important;
                font-weight: 700 !important;
            }
            .flex.justify-between.items-end.border-b-2 span {
                font-size: 12pt !important;
            }

            /* ── OBJECTIVE ANSWER ROWS ── */
            /* Target: each answer row (mb-2 break-inside-avoid flex items-baseline) */
            .mb-2.break-inside-avoid.flex {
                padding: 8px 0 !important;
                margin-bottom: 8px !important;
                border-bottom: 1px solid #cbd5e1 !important;
                font-size: 14pt !important;
            }
            /* Question number inside answer row */
            .mb-2.break-inside-avoid.flex span.w-8 {
                font-size: 14pt !important;
                font-weight: 700 !important;
            }
            /* Answer text inside answer row */
            .mb-2.break-inside-avoid.flex .flex-1 {
                font-size: 16pt !important;
                font-weight: 700 !important;
            }
            /* Marks text inside answer row */
            .mb-2.break-inside-avoid.flex span.shrink-0 {
                font-size: 12pt !important;
            }

            /* ── ESSAY BLOCKS ── */
            /* Target: essay question cards (border border-slate-200 border-l-4) */
            .border.border-slate-200.border-l-4 {
                padding: 16px !important;
                margin-bottom: 20px !important;
                border: 1px solid #94a3b8 !important;
                border-left: 4px solid #475569 !important;
                background: transparent !important;
                page-break-inside: avoid !important;
            }
            /* Essay question number */
            .border.border-slate-200.border-l-4 .font-bold.text-slate-900.shrink-0 {
                font-size: 14pt !important;
            }
            /* Essay question text */
            .border.border-slate-200.border-l-4 .text-slate-800 {
                font-size: 14pt !important;
                line-height: 1.6 !important;
            }
            /* Essay marks badge */
            .border.border-slate-200.border-l-4 .font-bold.text-slate-500.bg-slate-200 {
                font-size: 12pt !important;
                background: transparent !important;
                border: 1px solid #94a3b8 !important;
            }
            /* Model Answer heading */
            .border.border-slate-200.border-l-4 h4 {
                font-size: 12pt !important;
            }
            /* Model Answer text */
            .border.border-slate-200.border-l-4 .text-slate-700,
            .border.border-slate-200.border-l-4 .text-slate-400 {
                font-size: 14pt !important;
                line-height: 1.6 !important;
            }
            /* Keywords */
            .border.border-slate-200.border-l-4 .text-slate-600.italic {
                font-size: 12pt !important;
            }

            /* ── FOOTER ── */
            .print-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                border-top: 1px solid #cbd5e1;
                padding: 8px 24px;
                font-size: 10pt;
                color: #64748b;
                display: flex;
                justify-content: space-between;
                background: #fff;
            }
        }
    </style>
</x-print>
