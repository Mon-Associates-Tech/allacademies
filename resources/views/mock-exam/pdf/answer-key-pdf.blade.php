<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ isset($subjectExam) && $subjectExam ? $subjectExam->getDisplayTitle() : $mockExam->title }} - Answer Key</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 11pt;
            color: #1f2937;
            line-height: 1.6;
            background: #fff;
            padding: 0;
        }

        .answer-key-container {
            max-width: 100%;
            margin: 0;
            padding: 0;
            background: white;
        }

        /* Print Header */
        .print-header {
            border-bottom: 2px solid #111827;
            padding-bottom: 12px;
            margin-bottom: 24px;
        }

        .print-header h1 {
            font-size: 24pt;
            font-weight: 700;
            color: #111827;
            font-family: Georgia, 'Times New Roman', serif;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .print-header-sub {
            display: table;
            width: 100%;
            margin-top: 6px;
            font-size: 11pt;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .print-header-sub td {
            vertical-align: top;
        }

        .confidential-badge {
            margin-top: 10px;
            font-size: 10pt;
            font-weight: 700;
            color: #b91c1c;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            text-align: center;
            border: 1px solid #dc2626;
            padding: 4px;
        }

        /* Subject Header */
        .subject-header {
            border-left: 4px solid #1e293b;
            border-bottom: 3px solid #1e293b;
            padding: 0 0 8px 0;
            margin-bottom: 16px;
        }

        .subject-header-inner {
            display: table;
            width: 100%;
        }

        .subject-header-inner td {
            vertical-align: middle;
        }

        .subject-title {
            font-size: 20pt;
            font-weight: 700;
            font-family: Georgia, 'Times New Roman', serif;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #111827;
        }

        .subject-marks {
            font-size: 12pt;
            font-weight: 600;
            color: #6b7280;
            text-align: right;
        }

        /* Section Header */
        .section-header {
            border-bottom: 2px solid #94a3b8;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }

        .section-header-inner {
            display: table;
            width: 100%;
        }

        .section-header-inner td {
            vertical-align: bottom;
        }

        .section-title {
            font-size: 16pt;
            font-weight: 700;
            color: #1f2937;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .section-marks {
            font-size: 12pt;
            color: #6b7280;
            font-weight: 600;
            text-align: right;
        }

        /* Objective Answers Grid */
        .obj-answers-grid {
            display: table;
            width: 100%;
        }

        .obj-answers-grid-inner {
            display: table-row;
        }

        .obj-answers-grid-cell {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 0 12px 0 0;
        }

        .obj-answer-row {
            padding: 6px 0;
            margin-bottom: 6px;
            border-bottom: 1px solid #cbd5e1;
            font-size: 13pt;
        }

        .obj-answer-row-inner {
            display: table;
            width: 100%;
        }

        .obj-answer-row-inner td {
            vertical-align: baseline;
        }

        .obj-q-num {
            width: 32px;
            font-family: monospace;
            font-size: 13pt;
            color: #6b7280;
        }

        .obj-answer-text {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 16pt;
            font-weight: 700;
            color: #111827;
        }

        .obj-answer-text.true-ans {
            color: #047857;
        }

        .obj-answer-text.false-ans {
            color: #be123c;
            font-style: italic;
        }

        .obj-marks {
            font-size: 11pt;
            color: #9ca3af;
            text-align: right;
            white-space: nowrap;
        }

        /* Essay Blocks */
        .essay-block {
            padding: 12px 0;
            margin-bottom: 16px;
            page-break-inside: avoid;
        }

        .essay-q-header {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .essay-q-header td {
            vertical-align: top;
        }

        .essay-q-left {
            display: table;
        }

        .essay-q-left-inner {
            display: table-row;
        }

        .essay-q-left-inner td {
            display: table-cell;
            vertical-align: top;
            padding-right: 6px;
        }

        .essay-q-num {
            font-weight: 700;
            color: #111827;
            font-size: 13pt;
        }

        .essay-q-text {
            color: #1f2937;
            font-size: 13pt;
            line-height: 1.6;
        }

        .essay-marks-badge {
            font-size: 11pt;
            font-weight: 700;
            color: #6b7280;
            border: 1px solid #94a3b8;
            padding: 2px 6px;
            text-align: right;
            white-space: nowrap;
        }

        .essay-model-answer {
            margin-top: 12px;
        }

        .essay-model-heading {
            font-size: 11pt;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 4px;
        }

        .essay-model-text {
            color: #374151;
            font-size: 13pt;
            line-height: 1.6;
        }

        .essay-keywords {
            margin-top: 10px;
        }

        .essay-keywords-heading {
            font-size: 11pt;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }

        .essay-keywords-text {
            color: #4b5563;
            font-size: 12pt;
            font-style: italic;
        }

        /* Page breaks */
        .page-break {
            page-break-before: always;
        }

        /* Footer */
        .print-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            border-top: 1px solid #cbd5e1;
            padding: 6px 20px;
            font-size: 9pt;
            color: #64748b;
            display: table;
            width: 100%;
            background: #fff;
        }

        .print-footer td {
            vertical-align: middle;
        }

        .print-footer td:last-child {
            text-align: right;
        }

        /* KaTeX inline styles for PDF */
        .katex { font: normal 1.1em KaTeX_Main, 'Times New Roman', serif; }
        .katex-display { display: block; margin: 1em 0; text-align: center; }
        .katex-error { color: #cc0000; }

        /* Ensure prose content renders correctly */
        .prose-content p,
        .prose-content div {
            margin: 0;
            padding: 0;
        }

        .prose-content p + p {
            margin-top: 4pt;
        }

        /* Images */
        img {
            max-width: 100%;
            height: auto;
            page-break-inside: avoid;
        }
    </style>
</head>

<body>

<div class="answer-key-container">

    {{-- Print Header --}}
    <div class="print-header">
        @if(isset($subjectExam) && $subjectExam)
            <h1>{{ $subjectExam->getDisplayTitle() }}</h1>
            <div class="print-header-sub">
                <td>{{ $mockExam->title }} — Answer Key</td>
                <td style="text-align: right;">Generated: {{ now()->format('d M Y') }}</td>
            </div>
        @else
            <h1>{{ $mockExam->title }}</h1>
            <div class="print-header-sub">
                <td>Answer Key / Marking Scheme</td>
                <td style="text-align: right;">Generated: {{ now()->format('d M Y') }}</td>
            </div>
        @endif
        <div class="confidential-badge">
            ⚠ Confidential – Instructor Use Only – Do Not Distribute ⚠
        </div>
    </div>

    {{-- Subject Exams --}}
    @php
        $subjectExamsToRender = isset($subjectExam) && $subjectExam
            ? collect([$subjectExam])
            : $mockExam->subjectExams;
    @endphp

    @foreach($subjectExamsToRender as $seIdx => $se)
        <div class="{{ $seIdx > 0 ? 'page-break' : '' }}">

            {{-- Subject Header --}}
            <div class="subject-header">
                <div class="subject-header-inner">
                    <td>
                        <div class="subject-title">{{ $se->getDisplayTitle() }}</div>
                    </td>
                    <td style="width: 120px;">
                        <div class="subject-marks">
                            Total: {{ number_format($se->sections->sum(fn($s) => $s->getTotalMarks()), 1) }} marks
                        </div>
                    </td>
                </div>
            </div>

            {{-- Sections --}}
            @foreach($se->sections as $sIdx => $section)
                @php
                    $allQs   = $section->questions;
                    $objQs   = $allQs->filter(fn($q) => $q->isMultipleChoice() || $q->isTrueFalse())->values();
                    $essayQs = $allQs->filter(fn($q) => $q->isEssay())->values();
                @endphp

                <div class="section-header">
                    <div class="section-header-inner">
                        <td>
                            <div class="section-title">{{ $section->title }}</div>
                        </td>
                        <td style="width: 100px;">
                            <div class="section-marks">
                                {{ number_format($section->getTotalMarks(), 1) }} marks
                            </div>
                        </td>
                    </div>
                </div>

                {{-- Objective answers --}}
                @if($objQs->isNotEmpty())
                    <div class="obj-answers-grid">
                        <div class="obj-answers-grid-inner">
                            <div class="obj-answers-grid-cell">
                                @foreach($objQs->slice(0, ceil($objQs->count() / 2)) as $idx => $q)
                                    @php
                                        $qNum = floor($idx);
                                        if ($q->isMultipleChoice()) {
                                            $answer = strtoupper($q->correct_answer ?? '—');
                                            $ansClass = 'obj-answer-text';
                                        } else {
                                            $raw = strtolower(trim((string)($q->correct_answer ?? '')));
                                            $isTrue = in_array($raw, ['true','1','yes','t'], true);

                                            if ($raw === '') {
                                                $answer = '—';
                                                $ansClass = 'obj-answer-text';
                                            } elseif ($isTrue) {
                                                $answer = 'True';
                                                $ansClass = 'obj-answer-text true-ans';
                                            } else {
                                                $answer = 'False';
                                                $ansClass = 'obj-answer-text false-ans';
                                            }
                                        }
                                    @endphp

                                    <div class="obj-answer-row">
                                        <div class="obj-answer-row-inner">
                                            <td class="obj-q-num">{{ $qNum + 1 }}.</td>
                                            <td class="{{ $ansClass }}">
                                                {{ $answer }}
                                            </td>
                                            <td class="obj-marks">
                                                ({{ $q->marks }} mk)
                                            </td>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="obj-answers-grid-cell">
                                @foreach($objQs->slice(ceil($objQs->count() / 2)) as $idx => $q)
                                    @php
                                        $qNum = floor($idx) + ceil($objQs->count() / 2);
                                        if ($q->isMultipleChoice()) {
                                            $answer = strtoupper($q->correct_answer ?? '—');
                                            $ansClass = 'obj-answer-text';
                                        } else {
                                            $raw = strtolower(trim((string)($q->correct_answer ?? '')));
                                            $isTrue = in_array($raw, ['true','1','yes','t'], true);

                                            if ($raw === '') {
                                                $answer = '—';
                                                $ansClass = 'obj-answer-text';
                                            } elseif ($isTrue) {
                                                $answer = 'True';
                                                $ansClass = 'obj-answer-text true-ans';
                                            } else {
                                                $answer = 'False';
                                                $ansClass = 'obj-answer-text false-ans';
                                            }
                                        }
                                    @endphp

                                    <div class="obj-answer-row">
                                        <div class="obj-answer-row-inner">
                                            <td class="obj-q-num">{{ $qNum + 1 }}.</td>
                                            <td class="{{ $ansClass }}">
                                                {{ $answer }}
                                            </td>
                                            <td class="obj-marks">
                                                ({{ $q->marks }} mk)
                                            </td>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Essay Answers --}}
                @if($essayQs->isNotEmpty())
                    @foreach($essayQs as $eIdx => $question)
                        <div class="essay-block">
                            <div class="essay-q-header">
                                <td>
                                    <div class="essay-q-left">
                                        <div class="essay-q-left-inner">
                                            <td class="essay-q-num">Q{{ $objQs->count() + $eIdx + 1 }}.</td>
                                            <td>
                                                <div class="essay-q-text prose-content">
                                                    {!! app(\App\Support\MarkdownMathService::class)->cachedRender($question->question_text) !!}
                                                </div>
                                            </td>
                                        </div>
                                    </div>
                                </td>
                                <td style="width: 80px;">
                                    <div class="essay-marks-badge">
                                        [{{ $question->marks }} marks]
                                    </div>
                                </td>
                            </div>

                            <div class="essay-model-answer">
                                <div class="essay-model-heading">Model Answer</div>
                                @if($question->answer_explanation)
                                    <div class="essay-model-text prose-content">
                                        {!! app(\App\Support\MarkdownMathService::class)->cachedRender($question->answer_explanation) !!}
                                    </div>
                                @else
                                    <div class="essay-model-text" style="color: #9ca3af; font-style: italic;">
                                        No model answer stored.
                                    </div>
                                @endif
                            </div>

                            @if(!empty($question->answer_keywords))
                                <div class="essay-keywords">
                                    <div class="essay-keywords-heading">Key Terms / Keywords</div>
                                    <div class="essay-keywords-text">
                                        {{ implode('  ·  ', $question->answer_keywords) }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endif

                @if(!$loop->last)
                    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 16px 0;">
                @endif
            @endforeach
        </div>
    @endforeach
</div>

{{-- Footer --}}
<div class="print-footer">
    <td>{{ $mockExam->title }}</td>
    <td style="font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Marking Scheme — Confidential</td>
    <td>Page <span class="pagenum"></span> · {{ now()->format('d M Y') }}</td>
</div>

</body>
</html>
