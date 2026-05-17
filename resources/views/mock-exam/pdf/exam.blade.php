<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $mockExam->title }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10.5pt;
            color: #0f172a;
            line-height: 1.55;
        }

        /* ── Top banner ── */
        .top-banner {
            background: #0f172a;
            padding: 12px 18px 10px;
            margin-bottom: 0;
        }
        .banner-title {
            color: #ffffff;
            font-size: 15pt;
            font-weight: bold;
            letter-spacing: 0.08em;
            font-family: 'DejaVu Serif', Georgia, serif;
            text-transform: uppercase;
        }
        .banner-sub {
            color: #94a3b8;
            font-size: 8pt;
            margin-top: 2px;
            letter-spacing: 0.04em;
        }
        .violet-stripe {
            height: 3px;
            background: #7c3aed;
            margin-bottom: 14px;
        }

        /* ── Candidate box ── */
        .candidate-box {
            border: 1.5px solid #334155;
            margin-bottom: 14px;
        }
        .candidate-box table {
            width: 100%;
            border-collapse: collapse;
        }
        .candidate-box td {
            padding: 7px 10px;
            font-size: 9.5pt;
        }
        .candidate-box .field-label {
            color: #475569;
            font-weight: bold;
            width: 160px;
            white-space: nowrap;
        }
        .field-line {
            border-bottom: 1.5px solid #334155;
            display: inline-block;
            width: 100%;
        }
        .candidate-box td + td {
            border-left: 1px solid #e2e8f0;
        }
        .candidate-box tr + tr td {
            border-top: 1px solid #e2e8f0;
        }

        /* ── Global instructions ── */
        .global-instructions {
            border-left: 3px solid #7c3aed;
            background: #faf5ff;
            padding: 8px 12px;
            margin-bottom: 14px;
            font-size: 9.5pt;
        }
        .global-instructions .heading {
            font-weight: bold;
            color: #5b21b6;
            text-transform: uppercase;
            font-size: 8.5pt;
            letter-spacing: 0.08em;
            margin-bottom: 4px;
        }

        /* ── Subject exam header ── */
        .subject-block {
            margin-bottom: 20px;
        }
        .subject-header {
            background: #0f172a;
            color: #ffffff;
            padding: 9px 14px;
            font-size: 11pt;
            font-weight: bold;
            font-family: 'DejaVu Serif', Georgia, serif;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 0;
        }
        .subject-header .subject-meta {
            font-size: 8.5pt;
            color: #94a3b8;
            font-weight: normal;
            font-family: 'DejaVu Sans', Arial, sans-serif;
            letter-spacing: 0;
            text-transform: none;
            margin-top: 2px;
        }
        .subject-instructions {
            background: #fffbeb;
            border-left: 3px solid #f59e0b;
            padding: 7px 12px;
            font-size: 9.5pt;
            margin-bottom: 10px;
            border-top: 1px solid #fde68a;
        }

        /* ── Section header ── */
        .section-block {
            margin-bottom: 16px;
        }
        .section-header-row {
            display: table;
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 4px;
            margin-bottom: 4px;
        }
        .section-header-left {
            display: table-cell;
            font-weight: bold;
            font-size: 10.5pt;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .section-header-right {
            display: table-cell;
            text-align: right;
            font-size: 8.5pt;
            color: #64748b;
            vertical-align: bottom;
        }
        .section-instructions {
            font-style: italic;
            color: #374151;
            font-size: 9pt;
            margin-bottom: 10px;
        }

        /* ── Questions ── */
        .question-block {
            margin-bottom: 14px;
        }
        .question-row {
            display: table;
            width: 100%;
        }
        .question-num-cell {
            display: table-cell;
            width: 26px;
            font-weight: bold;
            color: #4c1d95;
            font-size: 10.5pt;
            vertical-align: top;
            padding-top: 0;
        }
        .question-text-cell {
            display: table-cell;
            vertical-align: top;
        }
        .question-text {
            font-size: 10.5pt;
            color: #0f172a;
        }
        .marks-badge {
            display: inline;
            font-size: 8pt;
            color: #64748b;
            font-style: italic;
        }

        /* ── MCQ Options ── */
        .mcq-options {
            margin-top: 6px;
            margin-left: 0;
        }
        .mcq-options table {
            width: 100%;
            border-collapse: collapse;
        }
        .mcq-options td {
            padding: 2px 8px 2px 0;
            font-size: 10pt;
            width: 50%;
            vertical-align: top;
        }
        .option-key {
            font-weight: bold;
            color: #1e293b;
            margin-right: 4px;
        }

        /* ── True/False (formatted like MCQ) ── */
        .tf-options {
            margin-top: 6px;
        }
        .tf-options table {
            border-collapse: collapse;
        }
        .tf-options td {
            padding: 2px 20px 2px 0;
            font-size: 10pt;
        }

        /* ── Essay (writing lines) ── */
        .essay-lines {
            margin-top: 8px;
        }
        .writing-line {
            border-bottom: 1px solid #94a3b8;
            height: 20px;
            margin-bottom: 2px;
        }

        /* ── Page break ── */
        .page-break { page-break-before: always; }

        /* ── Footer ── */
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

        /* ── Divider ── */
        hr.section-divider {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 14px 0;
        }
    </style>
</head>
<body>

{{-- ── Header banner ── --}}
<div class="top-banner">
    <div class="banner-title">{{ $mockExam->title }}</div>
    <div class="banner-sub">
        Generated: {{ now()->format('d F Y') }}
        @if($mockExam->starts_at) &nbsp;·&nbsp; Exam Date: {{ $mockExam->starts_at->format('d F Y') }} @endif
    </div>
</div>
<div class="violet-stripe"></div>

{{-- ── Candidate details ── --}}
<div class="candidate-box">
    <table>
        <tr>
            <td class="field-label">Candidate Name:</td>
            <td style="width:42%;"><span class="field-line">&nbsp;</span></td>
            <td class="field-label" style="width:130px;">Index / ID No.:</td>
            <td style="width:22%;"><span class="field-line">&nbsp;</span></td>
        </tr>
        <tr>
            <td class="field-label">Centre / School:</td>
            <td colspan="3"><span class="field-line">&nbsp;</span></td>
        </tr>
    </table>
</div>

{{-- ── Global instructions ── --}}
@if($mockExam->instructions)
    <div class="global-instructions">
        <div class="heading">General Instructions</div>
        {{ $mockExam->instructions }}
    </div>
@endif

{{-- ── Subject exams ── --}}
@foreach($mockExam->subjectExams as $seIdx => $se)
    @if($seIdx > 0) <div class="page-break"></div> @endif

    <div class="subject-block">
        <div class="subject-header">
            Subject {{ $loop->iteration }}: {{ $se->getDisplayTitle() }}
            @if($se->duration_in_minutes)
                <div class="subject-meta">Time Allowed: {{ $se->duration_in_minutes }} minutes</div>
            @endif
        </div>

        @if($se->instructions)
            <div class="subject-instructions">
                {{ $se->instructions }}
            </div>
        @endif

        @foreach($se->sections as $sIdx => $section)
            <div class="section-block">

                {{-- Section title bar --}}
                <div class="section-header-row">
                    <div class="section-header-left">
                        Section {{ $sIdx + 1 }}: {{ $section->title }}
                    </div>
                    <div class="section-header-right">
                        {{ $section->questions->count() }} question(s) &nbsp;·&nbsp;
                        {{ number_format($section->getTotalMarks(), 1) }} marks
                    </div>
                </div>

                @if($section->instructions)
                    <div class="section-instructions">{{ $section->instructions }}</div>
                @endif

                {{-- Questions --}}
                @foreach($section->questions as $qIdx => $question)
                    <div class="question-block">
                        <div class="question-row">
                            <div class="question-num-cell">{{ $qIdx + 1 }}.</div>
                            <div class="question-text-cell">

                                <span class="question-text">
                                    {{ strip_tags($question->question_text) }}
                                </span>
                                <span class="marks-badge"> [{{ $question->marks }}mk]</span>

                                {{-- ── MCQ options ── --}}
                                @if($question->isMultipleChoice() && !empty($question->options))
                                    <div class="mcq-options">
                                        @php $opts = $question->getOptionsForDisplay(); $pairs = array_chunk(array_keys($opts), 2, true); @endphp
                                        <table>
                                            @foreach($pairs as $pair)
                                                <tr>
                                                    @foreach($pair as $letter)
                                                        <td>
                                                            <span class="option-key">({{ $letter }})</span>{{ $opts[$letter] }}
                                                        </td>
                                                    @endforeach
                                                    @if(count($pair) < 2)<td></td>@endif
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>

                                {{-- ── True / False – formatted as A/B options ── --}}
                                @elseif($question->isTrueFalse())
                                    <div class="tf-options">
                                        <table>
                                            <tr>
                                                <td><span class="option-key">(A)</span> True</td>
                                                <td><span class="option-key">(B)</span> False</td>
                                            </tr>
                                        </table>
                                    </div>

                                {{-- ── Essay – writing lines only, no box ── --}}
                                @elseif($question->isEssay())
                                    <div class="essay-lines">
                                        @php $lines = max(6, (int) round($question->marks / 3)); @endphp
                                        @for($l = 0; $l < $lines; $l++)
                                            <div class="writing-line"></div>
                                        @endfor
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                @endforeach

            </div>

            @if(!$loop->last)<hr class="section-divider">@endif
        @endforeach
    </div>
@endforeach

{{-- ── Fixed footer ── --}}
<div class="page-footer">
    {{ $mockExam->title }} &nbsp;·&nbsp; {{ now()->format('Y') }} &nbsp;·&nbsp; Page <span class="pagenum"></span>
</div>

</body>
</html>