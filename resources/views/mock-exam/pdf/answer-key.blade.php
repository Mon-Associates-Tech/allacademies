<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $mockExam->title }} – Answer Key</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        @page {
            size: A4;
            margin: 20mm 14mm 24mm 14mm;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            color: #0d1b38;
            line-height: 1.55;
            background: #ffffff;
        }

        /* ─── Decorative Page Frame ─── */
        .frame-outer {
            position: fixed;
            top: 4mm; left: 4mm; right: 4mm; bottom: 4mm;
            border: 2px solid #b91c1c;
        }
        .frame-inner {
            position: fixed;
            top: 6.5mm; left: 6.5mm; right: 6.5mm; bottom: 6.5mm;
            border: 0.5px solid rgba(185,28,28,0.4);
        }

        /* ─── Fixed Footer ─── */
        .doc-footer {
            position: fixed;
            bottom: -20mm;
            left: 0; right: 0;
            border-top: 1.5px solid #0d1b38;
            padding: 2px 0 0;
        }
        .doc-footer table { width: 100%; border-collapse: collapse; }
        .doc-footer td {
            font-size: 7pt;
            color: #444;
            padding: 0 2px;
        }
        .footer-center {
            text-align: center;
            font-weight: bold;
            color: #b91c1c;
            text-transform: uppercase;
            letter-spacing: 0.07em;
        }
        .footer-right { text-align: right; }

        /* ─── TOP BANNER ─── */
        .top-banner {
            background: #0d1b38;
            padding: 10px 18px 9px;
        }
        .banner-title {
            color: #ffffff;
            font-size: 15pt;
            font-weight: bold;
            letter-spacing: 0.07em;
            font-family: 'DejaVu Serif', Georgia, serif;
            text-transform: uppercase;
        }
        .banner-scheme {
            color: #c9a227;
            font-size: 9pt;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin-top: 3px;
        }

        .gold-stripe  { height: 3.5px; background: #c9a227; }
        .red-stripe   { height: 3px;   background: #b91c1c; }

        .confidential-bar {
            background: #b91c1c;
            padding: 5px 18px;
            text-align: center;
            color: #ffffff;
            font-size: 8.5pt;
            font-weight: bold;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            margin-bottom: 14px;
        }

        /* ─── SUBJECT BLOCK ─── */
        .subject-block { margin-bottom: 20px; }

        .subject-header-tbl { width: 100%; border-collapse: collapse; }
        .subj-title-td {
            background: #1a3057;
            color: #ffffff;
            padding: 7px 14px;
            font-size: 11pt;
            font-weight: bold;
            font-family: 'DejaVu Serif', Georgia, serif;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-left: 5px solid #c9a227;
        }
        .subj-marks-td {
            background: #1a3057;
            color: #c9a227;
            padding: 7px 14px;
            font-size: 8.5pt;
            text-align: right;
            white-space: nowrap;
        }

        /* ─── SECTION BLOCK ─── */
        .section-block { margin-bottom: 16px; }

        .section-title-tbl {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #0d1b38;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .section-title-tbl td { padding-bottom: 4px; }
        .sec-name {
            font-size: 10.5pt;
            font-weight: bold;
            color: #0d1b38;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .sec-total {
            text-align: right;
            font-size: 8.5pt;
            color: #64748b;
            white-space: nowrap;
        }

        /* ─── MCQ / TF ANSWER GRID ─── */
        .answer-grid {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 10px;
        }
        .answer-grid th {
            background: #f1f5f9;
            border: 1px solid #c8d4e0;
            padding: 4px 6px;
            text-align: center;
            font-size: 8pt;
            font-weight: bold;
            color: #374151;
            letter-spacing: 0.04em;
            white-space: nowrap;
        }
        .answer-grid td {
            border: 1px solid #c8d4e0;
            padding: 5px 6px;
            text-align: center;
            vertical-align: middle;
        }
        .ans-mcq {
            font-size: 13pt;
            font-weight: bold;
            color: #047857;
            font-family: 'DejaVu Serif', Georgia, serif;
        }
        .ans-true  { font-size: 9.5pt; font-weight: bold; color: #047857; }
        .ans-false { font-size: 9.5pt; font-weight: bold; color: #b91c1c; }
        .marks-cell {
            font-size: 7pt;
            color: #9ca3af;
            font-style: italic;
        }
        .ans-empty { color: #d1d5db; }

        /* ─── ESSAY ANSWERS ─── */
        .essay-item {
            border: 1px solid #e2e8f0;
            border-left: 5px solid #1a3057;
            padding: 9px 12px;
            margin-bottom: 10px;
            background: #fafcff;
        }
        .essay-header-tbl { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        .essay-qnum {
            font-weight: bold;
            color: #1a3057;
            font-size: 10.5pt;
            width: 30px;
            vertical-align: top;
        }
        .essay-qtext {
            font-size: 9.5pt;
            color: #374151;
            vertical-align: top;
        }
        .essay-marks {
            text-align: right;
            font-size: 8pt;
            color: #6b7280;
            white-space: nowrap;
            vertical-align: top;
            padding-left: 8px;
        }

        .model-lbl {
            font-size: 7.5pt;
            font-weight: bold;
            color: #1a3057;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin: 6px 0 2px;
            border-bottom: 0.5px solid #c8d4e0;
            padding-bottom: 2px;
        }
        .model-text {
            font-size: 9.5pt;
            color: #111827;
            line-height: 1.6;
        }
        .model-empty {
            font-size: 9pt;
            color: #9ca3af;
            font-style: italic;
        }

        .kw-lbl {
            font-size: 7.5pt;
            font-weight: bold;
            color: #0e7490;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            margin: 6px 0 2px;
        }
        .kw-text {
            font-size: 9pt;
            color: #0e7490;
            font-style: italic;
        }

        /* ─── UTILITIES ─── */
        .page-break { page-break-before: always; }
        .section-sep { border: none; border-top: 1px solid #e2e8f0; margin: 14px 0; }
    </style>
</head>
<body>

{{-- ── Decorative page frame ── --}}
<div class="frame-outer"></div>
<div class="frame-inner"></div>

{{-- ── Fixed Footer ── --}}
<div class="doc-footer">
    <table>
        <tr>
            <td style="width:33%;">{{ $mockExam->title }}</td>
            <td class="footer-center" style="width:34%;">⚠ CONFIDENTIAL – DO NOT DISTRIBUTE</td>
            <td class="footer-right" style="width:33%;">Page <span class="pagenum"></span> &nbsp;·&nbsp; {{ now()->format('d M Y') }}</td>
        </tr>
    </table>
</div>

{{-- ── TOP BANNER ── --}}
<div class="top-banner">
    <div class="banner-title">{{ $mockExam->title }}</div>
    <div class="banner-scheme">Answer Key / Marking Scheme</div>
</div>
<div class="gold-stripe"></div>
<div class="red-stripe"></div>
<div class="confidential-bar">
    ⚠ &nbsp; Confidential &nbsp;–&nbsp; Instructor Use Only &nbsp;–&nbsp; Do Not Distribute &nbsp; ⚠
</div>

{{-- ── SUBJECT EXAMS ── --}}
@foreach($mockExam->subjectExams as $seIdx => $se)
    @if($seIdx > 0) <div class="page-break"></div> @endif

    <div class="subject-block">
        <table class="subject-header-tbl">
            <tr>
                <td class="subj-title-td">{{ $se->getDisplayTitle() }}</td>
                <td class="subj-marks-td">Total: {{ number_format($se->sections->sum(fn($s) => $s->getTotalMarks()), 1) }} marks</td>
            </tr>
        </table>

        @foreach($se->sections as $section)
            @php
                $allQs    = $section->questions;
                $objQs    = $allQs->filter(fn($q) => $q->isMultipleChoice() || $q->isTrueFalse())->values();
                $essayQs  = $allQs->filter(fn($q) => $q->isEssay())->values();
                $chunkSz  = 10;
            @endphp

            <div class="section-block">
                <table class="section-title-tbl">
                    <tr>
                        <td class="sec-name">{{ $section->title }}</td>
                        <td class="sec-total">Total: {{ number_format($section->getTotalMarks(), 1) }} marks</td>
                    </tr>
                </table>

                {{-- ── Objective answer grid ── --}}
                @if($objQs->isNotEmpty())
                    @php $startNum = 1; @endphp
                    @foreach($objQs->chunk($chunkSz) as $chunk)
                        <table class="answer-grid">
                            <thead>
                                <tr>
                                    @foreach($chunk as $idx => $q)
                                        <th>Q{{ $startNum + $idx }}</th>
                                    @endforeach
                                    @for($p = $chunk->count(); $p < $chunkSz; $p++)
                                        <th style="background:transparent;border-color:transparent;"></th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Answer row --}}
                                <tr>
                                    @foreach($chunk as $q)
                                        <td>
                                            @if($q->isMultipleChoice())
                                                <span class="ans-mcq">{{ strtoupper($q->correct_answer ?? '—') }}</span>
                                            @else
                                                @php
                                                    $raw      = strtolower(trim((string)($q->correct_answer ?? '')));
                                                    $isTrue   = in_array($raw, ['true','1','yes','t'], true);
                                                @endphp
                                                @if($raw === '')
                                                    <span class="ans-empty">—</span>
                                                @elseif($isTrue)
                                                    <span class="ans-true">True</span>
                                                @else
                                                    <span class="ans-false">False</span>
                                                @endif
                                            @endif
                                        </td>
                                    @endforeach
                                    @for($p = $chunk->count(); $p < $chunkSz; $p++)
                                        <td style="border-color:transparent;"></td>
                                    @endfor
                                </tr>
                                {{-- Marks row --}}
                                <tr>
                                    @foreach($chunk as $q)
                                        <td class="marks-cell">{{ $q->marks }}mk</td>
                                    @endforeach
                                    @for($p = $chunk->count(); $p < $chunkSz; $p++)
                                        <td style="border-color:transparent;"></td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                        @php $startNum += $chunk->count(); @endphp
                    @endforeach
                @endif

                {{-- ── Essay answers ── --}}
                @foreach($essayQs as $eIdx => $question)
                    <div class="essay-item">
                        <table class="essay-header-tbl">
                            <tr>
                                <td class="essay-qnum">Q{{ $objQs->count() + $eIdx + 1 }}.</td>
                                <td class="essay-qtext">{{ strip_tags($question->question_text) }}</td>
                                <td class="essay-marks">[{{ $question->marks }} marks]</td>
                            </tr>
                        </table>

                        <div class="model-lbl">Model Answer</div>
                        @if($question->answer_explanation)
                            <div class="model-text">{{ strip_tags($question->answer_explanation) }}</div>
                        @else
                            <div class="model-empty">No model answer stored.</div>
                        @endif

                        @if(!empty($question->answer_keywords))
                            <div class="kw-lbl">Key Terms / Keywords</div>
                            <div class="kw-text">{{ implode(' &nbsp;·&nbsp; ', $question->answer_keywords) }}</div>
                        @endif
                    </div>
                @endforeach
            </div>

            @if(!$loop->last) <hr class="section-sep"> @endif
        @endforeach
    </div>
@endforeach

</body>
</html>