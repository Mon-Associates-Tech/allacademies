<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $mockExam->title }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        @page {
            size: A4;
            margin: 18mm 15mm 24mm 15mm;
        }

        body {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: {{ $fontSize ?? 10.5 }}pt;
            color: #111111;
            line-height: 1.55;
            background: #ffffff;
        }

        /* ══════════════════════════════════════════════
           FIXED – repeated on every page
        ══════════════════════════════════════════════ */

        /* Outer page border */
        .pg-border {
            position: fixed;
            top: 5mm; left: 5mm; right: 5mm; bottom: 5mm;
            border: 1.5px solid #0c1f3f;
        }
        /* Inner gold accent frame */
        .pg-border-gold {
            position: fixed;
            top: 7.5mm; left: 7.5mm; right: 7.5mm; bottom: 7.5mm;
            border: 0.75px solid #c9a22a;
        }

        /* Footer ─ sits in the bottom margin */
        .pg-footer {
            position: fixed;
            bottom: -20mm;
            left: 0; right: 0;
            border-top: 1px solid #0c1f3f;
            padding-top: 2px;
        }
        .pg-footer table { width: 100%; border-collapse: collapse; }
        .pg-footer td {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 7pt;
            color: #555;
        }
        .ft-right  { text-align: right; }
        .ft-center { text-align: center; color: #888; font-style: italic; }


        /* ══════════════════════════════════════════════
           EXAM HEADER
        ══════════════════════════════════════════════ */

        /* 1 ─ Heavy navy rule + gold line */
        .hdr-rule-navy { height: 5px;   background: #0c1f3f; }
        .hdr-rule-gold { height: 2.5px; background: #c9a22a; }

        /* 2 ─ Title block */
        .hdr-title {
            text-align: center;
            padding: 9px 18px 8px;
            border-left: 1px solid #d0d0d0;
            border-right: 1px solid #d0d0d0;
        }
        .hdr-exam-title {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: {{ ($fontSize ?? 10.5) + 6 }}pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #0c1f3f;
            line-height: 1.25;
        }
        .hdr-exam-date {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: {{ ($fontSize ?? 10.5) }}pt;
            color: #555;
            margin-top: 3px;
            letter-spacing: 0.03em;
        }

        /* 3 ─ Info grid (date / subjects / duration / marks) */
        .hdr-info {
            width: 100%;
            border-collapse: collapse;
            border-top: 2px solid #0c1f3f;
            border-bottom: 2px solid #0c1f3f;
            background: #f5f0df;
        }
        .hdr-info td {
            padding: 5px 10px;
            font-family: 'DejaVu Sans', Arial, sans-serif;
            text-align: center;
            border-right: 1px solid #c8b870;
        }
        .hdr-info td:last-child { border-right: none; }
        .hi-lbl {
            display: block;
            font-size: 6.5pt;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #999;
            margin-bottom: 3px;
        }
        .hi-val {
            font-size: {{ ($fontSize ?? 10.5) }}pt;
            font-weight: bold;
            color: #0c1f3f;
        }


        /* ══════════════════════════════════════════════
           CANDIDATE INFORMATION
        ══════════════════════════════════════════════ */

        .cand-wrap {
            border: 1.5px solid #0c1f3f;
            margin-top: 9px;
            margin-bottom: 9px;
        }
        .cand-bar {
            background: #0c1f3f;
            color: #c9a22a;
            padding: 3px 10px;
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: {{ ($fontSize ?? 10.5) - 2.5 }}pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }
        .cand-tbl { width: 100%; border-collapse: collapse; }
        .cand-tbl td {
            padding: 6px 10px 5px;
            font-family: 'DejaVu Sans', Arial, sans-serif;
            vertical-align: bottom;
        }
        .cand-tbl td + td { border-left: 1px solid #ccc; }
        .cand-tbl tr + tr td { border-top: 1px solid #ccc; }
        .cf-lbl {
            display: block;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #888;
            margin-bottom: 5px;
            white-space: nowrap;
        }
        .cf-line {
            display: block;
            border-bottom: 1.5px solid #0c1f3f;
            height: 18px;
        }


        /* ══════════════════════════════════════════════
           INSTRUCTIONS
        ══════════════════════════════════════════════ */

        .inst-wrap {
            border: 1px solid #c9a22a;
            border-left: 5px solid #c9a22a;
            background: #fffdf0;
            padding: 6px 10px;
            margin-bottom: 10px;
        }
        .inst-heading {
            display: block;
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: {{ ($fontSize ?? 10.5) - 2 }}pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.09em;
            color: #0c1f3f;
            margin-bottom: 4px;
            padding-bottom: 3px;
            border-bottom: 0.5px solid #ddd5a0;
        }
        .inst-body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: {{ ($fontSize ?? 10.5) - 1.5 }}pt;
            color: #333;
            line-height: 1.55;
        }


        /* ══════════════════════════════════════════════
           SUBJECT BLOCK
        ══════════════════════════════════════════════ */

        .subj-wrap { margin-top: 6px; margin-bottom: 4px; }

        .subj-bar { width: 100%; border-collapse: collapse; }
        .subj-bar-name {
            background: #0c1f3f;
            color: #ffffff;
            padding: 6px 12px;
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: {{ ($fontSize ?? 10.5) + 1 }}pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .subj-bar-meta {
            background: #0c1f3f;
            color: #c9a22a;
            padding: 6px 12px;
            text-align: right;
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: {{ ($fontSize ?? 10.5) - 1 }}pt;
            white-space: nowrap;
        }
        .subj-gold-rule { height: 2.5px; background: #c9a22a; margin-bottom: 8px; }

        .subj-inst {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: {{ ($fontSize ?? 10.5) - 1.5 }}pt;
            font-style: italic;
            color: #444;
            border-left: 3px solid #c9a22a;
            background: #fffdf0;
            padding: 4px 9px;
            margin-bottom: 8px;
        }


        /* ══════════════════════════════════════════════
           SECTION HEADER
        ══════════════════════════════════════════════ */

        .sec-wrap { margin-bottom: 8px; }

        .sec-bar { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        .sec-bar-name {
            background: #1a3a6b;
            color: #ffffff;
            padding: 5px 10px;
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: {{ ($fontSize ?? 10.5) }}pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-left: 4px solid #c9a22a;
        }
        .sec-bar-meta {
            background: #1a3a6b;
            color: #c9a22a;
            padding: 5px 12px;
            text-align: right;
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: {{ ($fontSize ?? 10.5) - 1.5 }}pt;
            white-space: nowrap;
        }

        .sec-inst {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: {{ ($fontSize ?? 10.5) - 1.5 }}pt;
            font-style: italic;
            color: #555;
            padding: 2px 5px;
            margin-bottom: 5px;
        }


        /* ══════════════════════════════════════════════
           QUESTIONS  — 3-column table: num | body | marks
           (no floats — fully dompdf-safe)
        ══════════════════════════════════════════════ */

        .q-item { margin-bottom: 7px; page-break-inside: avoid; }

        .q-row { width: 100%; border-collapse: collapse; }

        /* column 1: question number */
        .q-num {
            width: 24px;
            vertical-align: top;
            padding-top: 1px;
            padding-right: 4px;
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-weight: bold;
            font-size: {{ ($fontSize ?? 10.5) }}pt;
            color: #0c1f3f;
            white-space: nowrap;
        }

        /* column 2: question text + options / lines */
        .q-body { vertical-align: top; }

        .q-text {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: {{ ($fontSize ?? 10.5) }}pt;
            color: #111;
            line-height: 1.55;
        }

        /* column 3: marks badge */
        .q-marks-col {
            width: 40px;
            vertical-align: top;
            text-align: right;
            padding-left: 5px;
            padding-top: 1px;
            white-space: nowrap;
        }
        .q-marks-badge {
            display: inline-block;
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: {{ ($fontSize ?? 10.5) - 2 }}pt;
            color: #555;
            border: 0.75px solid #bbb;
            background: #f8f8f6;
            padding: 1px 4px;
            font-style: italic;
        }


        /* ══════════════════════════════════════════════
           MCQ OPTIONS  — 2-column table, indented
        ══════════════════════════════════════════════ */

        .opts { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .opts td {
            width: 50%;
            padding: 2px 6px 2px 2px;
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: {{ ($fontSize ?? 10.5) - 0.5 }}pt;
            color: #1a1a1a;
            vertical-align: top;
        }
        .opt-key {
            font-weight: bold;
            color: #0c1f3f;
            display: inline-block;
            width: 18px;
        }


        /* ══════════════════════════════════════════════
           ESSAY ANSWER LINES
        ══════════════════════════════════════════════ */

        .elines { margin-top: 6px; }
        .eline {
            border-bottom: 0.75px solid #999;
            height: 20px;
            margin-bottom: 1px;
        }


        /* ══════════════════════════════════════════════
           UTILITIES
        ══════════════════════════════════════════════ */

        .sec-sep { border: none; border-top: 1px dashed #ccc; margin: 11px 0; }
        .pg-break { page-break-before: always; }
    </style>
</head>
<body>

{{-- ══ Decorative page frame (fixed → every page) ══ --}}
<div class="pg-border"></div>
<div class="pg-border-gold"></div>

{{-- ══ Footer ══ --}}
<div class="pg-footer">
    <table>
        <tr>
            <td style="width:38%;">{{ $mockExam->title }}</td>
            <td class="ft-center" style="width:24%;">— Turn Over —</td>
            <td class="ft-right" style="width:38%;">Page <span class="pagenum"></span></td>
        </tr>
    </table>
</div>

{{-- ══════════════════════════════════════════════
     EXAM HEADER
══════════════════════════════════════════════ --}}
<div class="hdr-rule-navy"></div>
<div class="hdr-rule-gold"></div>

<div class="hdr-title">
    <div class="hdr-exam-title">{{ $mockExam->title }}</div>
    @if($mockExam->starts_at)
        <div class="hdr-exam-date">{{ $mockExam->starts_at->format('l, d F Y') }}</div>
    @endif
</div>

@php
    $totalMarks    = $mockExam->subjectExams->sum(fn($se) => $se->sections->sum(fn($s) => $s->getTotalMarks()));
    $totalDuration = $mockExam->subjectExams->sum('duration_in_minutes');
    $subjectCount  = $mockExam->subjectExams->count();
@endphp

<table class="hdr-info">
    <tr>
        <td>
            <span class="hi-lbl">Exam Date</span>
            <span class="hi-val">
                @if($mockExam->starts_at)
                    {{ $mockExam->starts_at->format('d M Y') }}
                @else
                    {{ now()->format('d M Y') }}
                @endif
            </span>
        </td>
        @if($subjectCount > 0)
            <td>
                <span class="hi-lbl">{{ $subjectCount === 1 ? 'Subject' : 'Papers' }}</span>
                <span class="hi-val">
                    @if($subjectCount === 1)
                        {{ Str::limit($mockExam->subjectExams->first()->getDisplayTitle(), 22) }}
                    @else
                        {{ $subjectCount }} Papers
                    @endif
                </span>
            </td>
        @endif
        @if($totalDuration > 0)
            <td>
                <span class="hi-lbl">Total Duration</span>
                <span class="hi-val">
                    @if($totalDuration >= 60)
                        {{ floor($totalDuration / 60) }}hr {{ $totalDuration % 60 > 0 ? ($totalDuration % 60).'min' : '' }}
                    @else
                        {{ $totalDuration }} mins
                    @endif
                </span>
            </td>
        @endif
        @if($totalMarks > 0)
            <td>
                <span class="hi-lbl">Total Marks</span>
                <span class="hi-val">{{ number_format($totalMarks, 0) }}</span>
            </td>
        @endif
        <td>
            <span class="hi-lbl">Instructions</span>
            <span class="hi-val">Read carefully</span>
        </td>
    </tr>
</table>


{{-- ══════════════════════════════════════════════
     CANDIDATE INFORMATION
══════════════════════════════════════════════ --}}
<div class="cand-wrap">
    <div class="cand-bar">Candidate Information</div>
    <table class="cand-tbl">
        <tr>
            <td style="width:44%;">
                <span class="cf-lbl">Full Name</span>
                <span class="cf-line"></span>
            </td>
            <td style="width:26%;">
                <span class="cf-lbl">Index No.</span>
                <span class="cf-line"></span>
            </td>
            <td style="width:30%;">
                <span class="cf-lbl">Class / Form</span>
                <span class="cf-line"></span>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span class="cf-lbl">School / Institution</span>
                <span class="cf-line"></span>
            </td>
            <td>
                <span class="cf-lbl">Signature</span>
                <span class="cf-line"></span>
            </td>
        </tr>
    </table>
</div>


{{-- ══════════════════════════════════════════════
     GLOBAL INSTRUCTIONS
══════════════════════════════════════════════ --}}
@if($mockExam->instructions)
    <div class="inst-wrap">
        <span class="inst-heading">General Instructions to Candidates</span>
        <div class="inst-body">{{ $mockExam->instructions }}</div>
    </div>
@endif


{{-- ══════════════════════════════════════════════
     SUBJECT EXAMS
══════════════════════════════════════════════ --}}
@foreach($mockExam->subjectExams as $seIdx => $se)
    @if($seIdx > 0) <div class="pg-break"></div> @endif

    <div class="subj-wrap">

        {{-- Subject bar --}}
        <table class="subj-bar">
            <tr>
                <td class="subj-bar-name">{{ $se->getDisplayTitle() }}</td>
                @if($se->duration_in_minutes)
                    <td class="subj-bar-meta">
                        Time Allowed:&nbsp;
                        @if($se->duration_in_minutes >= 60)
                            {{ floor($se->duration_in_minutes / 60) }} hour{{ floor($se->duration_in_minutes / 60) > 1 ? 's' : '' }}
                            {{ $se->duration_in_minutes % 60 > 0 ? ($se->duration_in_minutes % 60).' mins' : '' }}
                        @else
                            {{ $se->duration_in_minutes }} minutes
                        @endif
                    </td>
                @endif
            </tr>
        </table>
        <div class="subj-gold-rule"></div>

        @if($se->instructions)
            <div class="subj-inst">{{ $se->instructions }}</div>
        @endif


        {{-- Sections --}}
        @foreach($se->sections as $sIdx => $section)
            @if($sIdx > 0) <hr class="sec-sep"> @endif

            <div class="sec-wrap">

                {{-- Section header --}}
                <table class="sec-bar">
                    <tr>
                        <td class="sec-bar-name">
                            Section&nbsp;{{ $sIdx + 1 }}:&nbsp;{{ $section->title }}
                        </td>
                        <td class="sec-bar-meta">
                            {{ $section->questions->count() }}&nbsp;{{ Str::plural('Question', $section->questions->count()) }}
                            &nbsp;·&nbsp;
                            {{ number_format($section->getTotalMarks(), 0) }}&nbsp;Marks
                        </td>
                    </tr>
                </table>

                @if($section->instructions)
                    <div class="sec-inst">{{ $section->instructions }}</div>
                @endif


                {{-- Questions --}}
                @foreach($section->questions as $qIdx => $question)
                    <div class="q-item">
                        <table class="q-row">
                            <tr>
                                {{-- Col 1: number --}}
                                <td class="q-num">{{ $qIdx + 1 }}.</td>

                                {{-- Col 2: question body --}}
                                <td class="q-body">
                                    <span class="q-text">{{ strip_tags($question->question_text) }}</span>

                                    {{-- ── MCQ ── --}}
                                    @if($question->isMultipleChoice() && !empty($question->options))
                                        @php $opts = $question->getOptionsForDisplay(); @endphp
                                        <table class="opts">
                                            @foreach(array_chunk(array_keys($opts), 2, true) as $pair)
                                                <tr>
                                                    @foreach($pair as $letter)
                                                        <td>
                                                            <span class="opt-key">{{ $letter }}.</span>
                                                            {{ $opts[$letter] }}
                                                        </td>
                                                    @endforeach
                                                    @if(count($pair) < 2)
                                                        <td></td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </table>

                                    {{-- ── True / False ── --}}
                                    @elseif($question->isTrueFalse())
                                        <table class="opts">
                                            <tr>
                                                <td><span class="opt-key">A.</span> True</td>
                                                <td><span class="opt-key">B.</span> False</td>
                                            </tr>
                                        </table>

                                    {{-- ── Essay ── --}}
                                    @elseif($question->isEssay())
                                        <div class="elines">
                                            @php $numLines = max(4, (int) round($question->marks * 1.2)); @endphp
                                            @for($l = 0; $l < $numLines; $l++)
                                                <div class="eline"></div>
                                            @endfor
                                        </div>
                                    @endif
                                </td>

                                {{-- Col 3: marks badge --}}
                                <td class="q-marks-col">
                                    <span class="q-marks-badge">[{{ $question->marks }}mk]</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                @endforeach

            </div>{{-- /.sec-wrap --}}
        @endforeach

    </div>{{-- /.subj-wrap --}}
@endforeach

</body>
</html>