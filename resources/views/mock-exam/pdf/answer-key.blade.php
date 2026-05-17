<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $mockExam->title }} – Answer Key</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            color: #0f172a;
            line-height: 1.55;
        }

        /* ── Top banner ── */
        .top-banner {
            background: #0f172a;
            padding: 12px 18px 10px;
        }
        .banner-title {
            color: #ffffff;
            font-size: 15pt;
            font-weight: bold;
            letter-spacing: 0.06em;
            font-family: 'DejaVu Serif', Georgia, serif;
            text-transform: uppercase;
        }
        .banner-scheme {
            color: #a78bfa;
            font-size: 9pt;
            font-weight: bold;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin-top: 3px;
        }
        .confidential-bar {
            background: #dc2626;
            padding: 5px 18px;
            color: white;
            font-size: 8.5pt;
            font-weight: bold;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 16px;
        }

        /* ── Subject header ── */
        .subject-block { margin-bottom: 22px; }
        .subject-header {
            background: #1e293b;
            color: #ffffff;
            padding: 8px 14px;
            font-size: 11pt;
            font-weight: bold;
            font-family: 'DejaVu Serif', Georgia, serif;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-left: 4px solid #7c3aed;
            margin-bottom: 12px;
        }

        /* ── Section header ── */
        .section-block { margin-bottom: 18px; }
        .section-title {
            display: table;
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .section-title-left {
            display: table-cell;
            font-size: 10.5pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .section-title-right {
            display: table-cell;
            text-align: right;
            font-size: 8.5pt;
            color: #64748b;
            vertical-align: bottom;
        }

        /* ── MCQ / TF answer grid ── */
        .answer-grid {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 6px;
        }
        .answer-grid th {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
            text-align: center;
            font-size: 8.5pt;
            font-weight: bold;
            color: #475569;
            letter-spacing: 0.04em;
            white-space: nowrap;
        }
        .answer-grid td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: center;
            vertical-align: middle;
        }
        .answer-val-mcq {
            font-size: 13pt;
            font-weight: bold;
            color: #059669;
        }
        .answer-val-tf-true {
            font-size: 10pt;
            font-weight: bold;
            color: #059669;
        }
        .answer-val-tf-false {
            font-size: 10pt;
            font-weight: bold;
            color: #dc2626;
        }
        .marks-cell {
            font-size: 7.5pt;
            color: #94a3b8;
        }

        /* ── Essay answers ── */
        .essay-block {
            border: 1px solid #e2e8f0;
            border-left: 4px solid #7c3aed;
            padding: 10px 14px;
            margin-bottom: 10px;
        }
        .essay-q-header {
            display: table;
            width: 100%;
            margin-bottom: 6px;
        }
        .essay-q-num {
            display: table-cell;
            font-weight: bold;
            color: #4c1d95;
            font-size: 10.5pt;
            width: 28px;
            vertical-align: top;
        }
        .essay-q-text {
            display: table-cell;
            font-size: 10pt;
            color: #334155;
            vertical-align: top;
        }
        .essay-marks {
            display: table-cell;
            text-align: right;
            font-size: 8.5pt;
            color: #64748b;
            white-space: nowrap;
            vertical-align: top;
        }
        .model-answer-label {
            font-size: 8pt;
            font-weight: bold;
            color: #7c3aed;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 3px;
            margin-top: 6px;
        }
        .model-answer-text {
            font-size: 9.5pt;
            color: #0f172a;
            line-height: 1.6;
        }
        .keywords-label {
            font-size: 7.5pt;
            font-weight: bold;
            color: #0891b2;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-top: 6px;
            margin-bottom: 2px;
        }
        .keywords-text {
            font-size: 9pt;
            color: #0891b2;
            font-style: italic;
        }

        /* ── Page break / footer ── */
        .page-break { page-break-before: always; }
        .page-footer {
            position: fixed;
            bottom: 10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7.5pt;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }
        hr.divider {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 14px 0;
        }
    </style>
</head>
<body>

{{-- ── Banner ── --}}
<div class="top-banner">
    <div class="banner-title">{{ $mockExam->title }}</div>
    <div class="banner-scheme">Answer Key / Marking Scheme</div>
</div>
<div class="confidential-bar">⚠ Confidential &nbsp;–&nbsp; Instructor Use Only &nbsp;–&nbsp; Do Not Distribute</div>

{{-- ── Subject exams ── --}}
@foreach($mockExam->subjectExams as $seIdx => $se)
    @if($seIdx > 0) <div class="page-break"></div> @endif

    <div class="subject-block">
        <div class="subject-header">
            {{ $se->getDisplayTitle() }}
        </div>

        @foreach($se->sections as $section)
            @php
                $allQuestions = $section->questions;
                $objQuestions = $allQuestions->filter(fn($q) => $q->isMultipleChoice() || $q->isTrueFalse())->values();
                $essayQuestions = $allQuestions->filter(fn($q) => $q->isEssay())->values();
            @endphp

            <div class="section-block">
                <div class="section-title">
                    <div class="section-title-left">{{ $section->title }}</div>
                    <div class="section-title-right">Total: {{ number_format($section->getTotalMarks(), 1) }} marks</div>
                </div>

                {{-- ── Objective questions (MCQ + True/False) in a table grid ── --}}
                @if($objQuestions->isNotEmpty())
                    @php
                        $chunkSize = 10; // answers per row
                        $chunks = $objQuestions->chunk($chunkSize);
                        $startNum = 1;
                    @endphp

                    @foreach($chunks as $chunk)
                        <table class="answer-grid" style="margin-bottom: 12px;">
                            <thead>
                                <tr>
                                    @foreach($chunk as $idx => $q)
                                        <th>Q{{ $startNum + $idx }}</th>
                                    @endforeach
                                    {{-- pad empty columns if chunk is shorter than chunkSize --}}
                                    @for($p = $chunk->count(); $p < $chunkSize; $p++)
                                        <th style="background:transparent; border-color:transparent;"></th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Answer row --}}
                                <tr>
                                    @foreach($chunk as $q)
                                        <td>
                                            @if($q->isMultipleChoice())
                                                <span class="answer-val-mcq">{{ strtoupper($q->correct_answer ?? '—') }}</span>
                                            @else
                                                {{-- True/False: normalise stored value to readable label --}}
                                                @php
                                                    $raw = strtolower(trim((string) ($q->correct_answer ?? '')));
                                                    $isTrueAnswer = in_array($raw, ['true', '1', 'yes', 't'], true);
                                                @endphp
                                                @if($raw === '')
                                                    <span style="color:#94a3b8;">—</span>
                                                @elseif($isTrueAnswer)
                                                    <span class="answer-val-tf-true">True</span>
                                                @else
                                                    <span class="answer-val-tf-false">False</span>
                                                @endif
                                            @endif
                                        </td>
                                    @endforeach
                                    @for($p = $chunk->count(); $p < $chunkSize; $p++)
                                        <td style="border-color:transparent;"></td>
                                    @endfor
                                </tr>
                                {{-- Marks row --}}
                                <tr>
                                    @foreach($chunk as $q)
                                        <td class="marks-cell">{{ $q->marks }}mk</td>
                                    @endforeach
                                    @for($p = $chunk->count(); $p < $chunkSize; $p++)
                                        <td style="border-color:transparent;"></td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                        @php $startNum += $chunk->count(); @endphp
                    @endforeach
                @endif

                {{-- ── Essay questions ── --}}
                @foreach($essayQuestions as $eIdx => $question)
                    <div class="essay-block">
                        <div class="essay-q-header">
                            <div class="essay-q-num">Q{{ $objQuestions->count() + $eIdx + 1 }}.</div>
                            <div class="essay-q-text">{{ strip_tags($question->question_text) }}</div>
                            <div class="essay-marks">{{ $question->marks }} marks</div>
                        </div>

                        @if($question->answer_explanation)
                            <div class="model-answer-label">Model Answer</div>
                            <div class="model-answer-text">{{ strip_tags($question->answer_explanation) }}</div>
                        @else
                            <div class="model-answer-label">Model Answer</div>
                            <div class="model-answer-text" style="color:#94a3b8; font-style:italic;">No model answer stored.</div>
                        @endif

                        @if(!empty($question->answer_keywords))
                            <div class="keywords-label">Key Terms / Keywords</div>
                            <div class="keywords-text">{{ implode(' &nbsp;·&nbsp; ', $question->answer_keywords) }}</div>
                        @endif
                    </div>
                @endforeach

            </div>

            @if(!$loop->last)<hr class="divider">@endif
        @endforeach
    </div>
@endforeach

<div class="page-footer">
    Answer Key: {{ $mockExam->title }} &nbsp;·&nbsp; Generated {{ now()->format('d M Y H:i') }} &nbsp;·&nbsp; CONFIDENTIAL
</div>

</body>
</html>