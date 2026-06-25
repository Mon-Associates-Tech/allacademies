<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $mockExam->title }} – Answer Key</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        @page {
            size: A4;
            margin: 20mm 18mm 24mm 18mm;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            color: #111;
            line-height: 1.6;
            background: #fff;
            padding: 0 20px;
        }

        /* ─── Fixed Footer ─── */
        .doc-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            border-top: 1px solid #999;
            padding: 4px 20px 6px;
        }
        .doc-footer table { width: 100%; border-collapse: collapse; }
        .doc-footer td {
            font-size: 7.5pt;
            color: #666;
            padding: 0 2px;
        }
        .footer-center {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .footer-right { text-align: right; }

        /* ─── Top Banner ─── */
        .top-banner, .banner-rule, .confidential-bar {
            margin-left: -20px;
            margin-right: -20px;
        }

        .top-banner {
            background: #111;
            padding: 12px 20px 10px;
        }
        .banner-title {
            color: #fff;
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 0.05em;
            font-family: 'DejaVu Serif', Georgia, serif;
            text-transform: uppercase;
        }
        .banner-sub {
            color: #ccc;
            font-size: 8.5pt;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-top: 4px;
        }
        .banner-rule {
            height: 2px;
            background: #555;
            margin-bottom: 0;
        }

        .confidential-bar {
            background: #333;
            padding: 6px 20px;
            text-align: center;
            color: #e5e5e5;
            font-size: 7.5pt;
            font-weight: bold;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        /* ─── Subject Block ─── */
        .subject-block { margin-bottom: 30px; }

        .subject-header-tbl {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #333;
        }
        .subj-title-td {
            background: #1a1a1a;
            color: #fff;
            padding: 8px 14px;
            font-size: 11pt;
            font-weight: bold;
            font-family: 'DejaVu Serif', Georgia, serif;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-left: 4px solid #888;
        }
        .subj-marks-td {
            background: #1a1a1a;
            color: #bbb;
            padding: 8px 14px;
            font-size: 8.5pt;
            text-align: right;
            white-space: nowrap;
        }

        /* ─── Section Block ─── */
        .section-block { margin-bottom: 24px; }

        .section-title-tbl {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 1.5px solid #333;
            margin-bottom: 12px;
        }
        .section-title-tbl td { padding-bottom: 4px; }
        .sec-name {
            font-size: 10pt;
            font-weight: bold;
            color: #111;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .sec-total {
            text-align: right;
            font-size: 8pt;
            color: #777;
            white-space: nowrap;
        }

        /* ─── Vertical Answer List (No Borders, Spacing Only) ─── */
        .obj-answers {
            margin-bottom: 24px;
        }

        .obj-item {
            display: flex;
            align-items: baseline;
            margin-bottom: 6px; /* Visual separation via spacing only */
            font-size: 9.5pt;
        }
        .obj-item:last-child {
            margin-bottom: 0;
        }

        .obj-q {
            width: 40px;
            font-weight: bold;
            color: #333;
            flex-shrink: 0;
        }
        .obj-val {
            flex: 1;
            font-weight: bold;
            font-family: 'DejaVu Serif', Georgia, serif;
        }
        .obj-mark {
            text-align: right;
            color: #888;
            font-style: italic;
            font-size: 7.5pt;
            flex-shrink: 0;
            padding-left: 10px;
            white-space: nowrap;
        }

        /* Answer Type Styles */
        .ans-mcq   { font-size: 10pt; color: #111; }
        .ans-true  { font-size: 9pt;  color: #111; }
        .ans-false { font-size: 9pt;  color: #444; font-style: italic; }
        .ans-empty { color: #bbb; }

        /* ─── Essay Answers ─── */
        .essay-item {
            border: 1px solid #ccc;
            border-left: 4px solid #444;
            padding: 12px 16px;
            margin-bottom: 16px;
            background: #fafafa;
        }
        .essay-header-tbl { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .essay-qnum {
            font-weight: bold;
            color: #111;
            font-size: 10pt;
            width: 30px;
            vertical-align: top;
        }
        .essay-qtext {
            font-size: 9.5pt;
            color: #333;
            vertical-align: top;
        }
        .essay-marks {
            text-align: right;
            font-size: 8pt;
            color: #666;
            white-space: nowrap;
            vertical-align: top;
            padding-left: 8px;
        }

        .model-lbl {
            font-size: 7pt;
            font-weight: bold;
            color: #444;
            text-transform: uppercase;
            letter-spacing: 0.09em;
            margin: 8px 0 3px;
            border-bottom: 0.5px solid #ccc;
            padding-bottom: 2px;
        }
        .model-text {
            font-size: 9.5pt;
            color: #111;
            line-height: 1.6;
        }
        .model-empty {
            font-size: 9pt;
            color: #aaa;
            font-style: italic;
        }

        .kw-lbl {
            font-size: 7pt;
            font-weight: bold;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin: 8px 0 3px;
        }
        .kw-text {
            font-size: 9pt;
            color: #444;
            font-style: italic;
        }

        /* ─── Utilities ─── */
        .page-break { page-break-before: always; }
        .section-sep {
            border: none;
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }
    </style>
</head>
<body>

{{-- Fixed Footer --}}
<div class="doc-footer">
    <table>
        <tr>
            <td style="width:33%;">{{ $mockExam->title }}</td>
            <td class="footer-center" style="width:34%;">Marking Scheme — Confidential</td>
            <td class="footer-right" style="width:33%;">Page <span class="pagenum"></span> &nbsp;·&nbsp; {{ now()->format('d M Y') }}</td>
        </tr>
    </table>
</div>

{{-- Top Banner --}}
<div class="top-banner">
    <div class="banner-title">{{ $mockExam->title }}</div>
    <div class="banner-sub">Answer Key / Marking Scheme</div>
</div>
<div class="banner-rule"></div>
<div class="confidential-bar">
    ⚠ &nbsp; Confidential &nbsp;–&nbsp; Instructor Use Only &nbsp;–&nbsp; Do Not Distribute &nbsp; ⚠
</div>

{{-- Subject Exams --}}
@foreach($mockExam->subjectExams as $seIdx => $se)
    @if($seIdx > 0)<div class="page-break"></div>@endif

    <div class="subject-block">
        <table class="subject-header-tbl">
            <tr>
                <td class="subj-title-td">{{ $se->getDisplayTitle() }}</td>
                <td class="subj-marks-td">Total: {{ number_format($se->sections->sum(fn($s) => $s->getTotalMarks()), 1) }} marks</td>
            </tr>
        </table>

        @foreach($se->sections as $section)
            @php
                $allQs   = $section->questions;
                $objQs   = $allQs->filter(fn($q) => $q->isMultipleChoice() || $q->isTrueFalse())->values();
                $essayQs = $allQs->filter(fn($q) => $q->isEssay())->values();
            @endphp

            <div class="section-block">
                <table class="section-title-tbl">
                    <tr>
                        <td class="sec-name">{{ $section->title }}</td>
                        <td class="sec-total">Total: {{ number_format($section->getTotalMarks(), 1) }} marks</td>
                    </tr>
                </table>

                {{-- Objective answers (Vertical List, No Borders) --}}
                @if($objQs->isNotEmpty())
                    <div class="obj-answers">
                        @foreach($objQs as $idx => $q)
                            @php
                                $qNum = $idx + 1;
                                if ($q->isMultipleChoice()) {
                                    $answer = strtoupper($q->correct_answer ?? '—');
                                    $ansClass = 'ans-mcq';
                                } else {
                                    $raw = strtolower(trim((string)($q->correct_answer ?? '')));
                                    $isTrue = in_array($raw, ['true','1','yes','t'], true);
                                    if ($raw === '') {
                                        $answer = '—';
                                        $ansClass = 'ans-empty';
                                    } elseif ($isTrue) {
                                        $answer = 'True';
                                        $ansClass = 'ans-true';
                                    } else {
                                        $answer = 'False';
                                        $ansClass = 'ans-false';
                                    }
                                }
                            @endphp
                            <div class="obj-item">
                                <span class="obj-q">{{ $qNum }}.</span>
                                <span class="obj-val {{ $ansClass }}">{{ $answer }}</span>
                                <span class="obj-mark">({{ $q->marks }} mk)</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Essay answers --}}
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

            @if(!$loop->last)<hr class="section-sep">@endif
        @endforeach
    </div>
@endforeach

</body>
</html>
