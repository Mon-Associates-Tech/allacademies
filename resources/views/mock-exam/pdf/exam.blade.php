<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $mockExam->title }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        @page {
            size: A4;
            margin: 22mm 20mm 26mm 20mm;
        }

        body {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-size: {{ $fontSize ?? 11 }}pt;
            color: #111;
            line-height: 1.65;
            background: #fff;
        }

        /* ─── Fixed Footer ─── */
        .pg-footer {
            position: fixed;
            bottom: -22mm;
            left: 0; right: 0;
            border-top: 1px solid #bbb;
            padding-top: 3px;
        }
        .pg-footer table { width: 100%; border-collapse: collapse; }
        .pg-footer td {
            font-size: 7.5pt;
            font-family: Arial, Helvetica, sans-serif;
            color: #777;
            padding: 0 2px;
        }
        .ft-right { text-align: right; }

        /* ─── Document Header ─── */
        .doc-header {
            text-align: center;
            margin-bottom: 18px;
        }
        .rule-heavy { height: 3px; background: #111; }
        .rule-light { height: 1px; background: #111; margin: 3px 0; }
        .header-body { padding: 10px 0 8px; }

        .school-name {
            font-size: {{ ($fontSize ?? 11) + 2 }}pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #111;
            margin-bottom: 2px;
        }
        .school-tagline {
            font-size: {{ ($fontSize ?? 11) - 1.5 }}pt;
            font-style: italic;
            color: #666;
            font-family: Arial, Helvetica, sans-serif;
            margin-bottom: 9px;
        }
        .exam-main-title {
            font-size: {{ ($fontSize ?? 11) + 5 }}pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #111;
            margin-bottom: 3px;
        }
        .exam-sub-date {
            font-size: {{ ($fontSize ?? 11) - 1 }}pt;
            color: #666;
            font-family: Arial, Helvetica, sans-serif;
        }

        /* ─── Info Table ─── */
        .info-tbl {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #444;
            margin-bottom: 18px;
        }
        .info-tbl td {
            padding: 5px 10px;
            text-align: center;
            border: 1px solid #bbb;
            vertical-align: middle;
        }
        .ig-lbl {
            display: block;
            font-size: 7pt;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #999;
            font-family: Arial, Helvetica, sans-serif;
            margin-bottom: 1px;
        }
        .ig-val {
            font-size: {{ $fontSize ?? 11 }}pt;
            font-weight: bold;
            color: #111;
        }

        /* ─── Candidate Information (no box, just ruled fields) ─── */
        .cand-section { margin-bottom: 18px; }
        .section-label {
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #111;
            font-family: Arial, Helvetica, sans-serif;
            margin-bottom: 4px;
        }
        .section-rule { border: none; border-top: 1.5px solid #333; margin-bottom: 12px; }

        .field-row {
            display: table;
            width: 100%;
            margin-bottom: 14px;
        }
        .field-row:last-child { margin-bottom: 0; }
        .field-cell {
            display: table-cell;
            padding-right: 24px;
            vertical-align: bottom;
        }
        .field-cell:last-child { padding-right: 0; }
        .field-lbl {
            display: block;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #777;
            font-family: Arial, Helvetica, sans-serif;
            margin-bottom: 13px;
        }
        .field-line { border-bottom: 1px solid #444; }

        /* ─── Instructions (no box, just ruled separator) ─── */
        .inst-section { margin-bottom: 20px; }
        .inst-body {
            font-size: {{ ($fontSize ?? 11) - 1 }}pt;
            color: #333;
            line-height: 1.65;
        }

        /* ─── Subject Header ─── */
        .subj-wrap { margin-top: 20px; margin-bottom: 4px; }
        .subj-rule-top { border: none; border-top: 2.5px solid #111; margin-bottom: 5px; }
        .subj-rule-btm { border: none; border-top: 1px solid #555; margin-bottom: 10px; }
        .subj-hdr-tbl { width: 100%; border-collapse: collapse; }
        .subj-hdr-name {
            font-size: {{ ($fontSize ?? 11) + 2 }}pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #111;
        }
        .subj-hdr-meta {
            text-align: right;
            font-size: {{ ($fontSize ?? 11) - 1 }}pt;
            color: #666;
            font-family: Arial, Helvetica, sans-serif;
            white-space: nowrap;
        }
        .subj-inst {
            font-size: {{ ($fontSize ?? 11) - 1 }}pt;
            font-style: italic;
            color: #555;
            margin-bottom: 12px;
        }

        /* ─── Section Header ─── */
        .sec-wrap { margin-top: 18px; margin-bottom: 2px; }
        .sec-hdr-tbl { width: 100%; border-collapse: collapse; }
        .sec-hdr-rule { border: none; border-top: 1.5px solid #555; margin-bottom: 10px; }
        .sec-hdr-name {
            font-size: {{ ($fontSize ?? 11) + 0.5 }}pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #111;
        }
        .sec-hdr-meta {
            text-align: right;
            font-size: {{ ($fontSize ?? 11) - 1.5 }}pt;
            color: #888;
            font-family: Arial, Helvetica, sans-serif;
            white-space: nowrap;
        }
        .sec-inst {
            font-size: {{ ($fontSize ?? 11) - 1.5 }}pt;
            font-style: italic;
            color: #555;
            margin-bottom: 10px;
        }

        /* ─── Questions (no borders, pure flow) ─── */
        .q-item {
            margin-bottom: 14px;
            page-break-inside: avoid;
        }
        .q-header {
            display: table;
            width: 100%;
            table-layout: fixed;
            margin-bottom: 4px;
        }
        .q-num {
            display: table-cell;
            font-weight: bold;
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
            width: 26px;
            vertical-align: top;
            padding-top: 1px;
        }
        .q-text {
            display: table-cell;
            font-size: {{ $fontSize ?? 11 }}pt;
            color: #111;
            vertical-align: top;
        }
        .q-marks {
            display: table-cell;
            font-size: {{ ($fontSize ?? 11) - 2 }}pt;
            color: #888;
            font-family: Arial, Helvetica, sans-serif;
            vertical-align: top;
            padding-top: 2px;
            padding-left: 8px;
            text-align: right;
            white-space: nowrap;
            width: 44px;
        }

        /* ─── Options (indented, no borders) ─── */
        .opts-wrap { padding-left: 26px; margin-top: 4px; }
        .opt-row {
            display: table;
            width: 100%;
            margin-bottom: 2px;
            page-break-inside: avoid;
        }
        .opt-lbl {
            display: table-cell;
            width: 22px;
            font-weight: bold;
            font-family: Arial, Helvetica, sans-serif;
            font-size: {{ ($fontSize ?? 11) - 0.5 }}pt;
            color: #333;
            vertical-align: top;
        }
        .opt-txt {
            display: table-cell;
            font-size: {{ ($fontSize ?? 11) - 0.5 }}pt;
            color: #222;
            vertical-align: top;
        }

        /* ─── Essay Lines ─── */
        .elines { margin-top: 8px; padding-left: 0; }
        .eline { border-bottom: 1px solid #ccc; height: 22px; }

        /* ─── Page Break ─── */
        .pg-break { page-break-before: always; }
        
        /* ─── Front Page Styles ─── */
        .front-page {
            page-break-after: always;
            text-align: center;
            padding: 40mm 25mm 30mm 25mm;
            font-family: 'Georgia', 'Times New Roman', serif;
        }
        .fp-title {
            font-size: {{ ($fontSize ?? 11) + 8 }}pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #111;
            margin-bottom: 25px;
            line-height: 1.4;
        }
        .fp-subtitle {
            font-size: {{ ($fontSize ?? 11) + 2 }}pt;
            font-style: italic;
            color: #666;
            margin-bottom: 35px;
            line-height: 1.5;
        }
        .fp-divider {
            width: 100%;
            height: 1px;
            background: #ccc;
            margin: 30px 0;
            position: relative;
        }
        .fp-divider::before {
            content: "";
            position: absolute;
            top: -0.5px;
            left: 0;
            right: 0;
            height: 2px;
            background: #aaa;
        }
        .fp-content {
            margin: 25px 0;
            text-align: left;
            font-size: {{ $fontSize ?? 11 }}pt;
            line-height: 1.7;
        }
        .fp-image {
            max-width: 100%;
            margin: 20px auto;
            display: block;
        }
        .fp-info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            text-align: left;
        }
        .fp-info-table th {
            font-weight: bold;
            padding: 8px 12px;
            background: #f0f0f0;
            border: 1px solid #ddd;
        }
        .fp-info-table td {
            padding: 8px 12px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>

{{-- Front Page if any subject exam has template with front page config --}}
@php
    $hasFrontPage = false;
    $frontPageConfig = null;
    
    foreach($mockExam->subjectExams as $se) {
        if($se->template && !empty($se->template->front_page_config['blocks'])) {
            $hasFrontPage = true;
            $frontPageConfig = $se->template->front_page_config;
            break;
        }
    }
@endphp

@if($hasFrontPage)
    <div class="front-page">
        @foreach($frontPageConfig['blocks'] ?? [] as $block)
            @switch($block['type'])
                @case('heading')
                    <div class="fp-title" style="
                        font-size: {{ ($fontSize ?? 11) + ($block['level'] == 'h1' ? 8 : ($block['level'] == 'h2' ? 5 : 2)) }}pt;
                        @if($block['level'] == 'h1') text-transform: uppercase; letter-spacing: 0.05em; @endif
                    ">
                        {!! $block['content'] !!}
                    </div>
                    @break
                @case('richtext')
                    <div class="fp-content">
                        {!! $block['content'] !!}
                    </div>
                    @break
                @case('image')
                    @if($block['source_type'] == 'url')
                        <img src="{{ $block['src'] }}" class="fp-image" style="width: {{ $block['width'] ?? 100 }}px;" alt="{{ $block['alt'] ?? '' }}">
                    @elseif($block['source_type'] == 'upload')
                        <img src="{{ Storage::disk('public')->url($block['src']) }}" class="fp-image" style="width: {{ $block['width'] ?? 100 }}px;" alt="{{ $block['alt'] ?? '' }}">
                    @endif
                    @break
                @case('divider')
                    <div class="fp-divider"></div>
                    @break
                @case('info_table')
                    <table class="fp-info-table">
                        <thead>
                            <tr>
                                @foreach($block['fields'] as $field)
                                    <th>{{ $field }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @foreach($block['fields'] as $field)
                                    <td>
                                        @switch($field)
                                            @case('School Name')
                                                {{ $mockExam->team?->name ?? 'N/A' }}
                                                @break
                                            @case('Exam Title')
                                                {{ $mockExam->title }}
                                                @break
                                            @case('Date')
                                                {{ $mockExam->starts_at ? $mockExam->starts_at->format('d M Y') : now()->format('d M Y') }}
                                                @break
                                            @case('Time')
                                                {{ $mockExam->starts_at ? $mockExam->starts_at->format('h:i A') : 'N/A' }}
                                                @break
                                            @case('Duration')
                                                @php
                                                    $totalDuration = $mockExam->subjectExams->sum('duration_in_minutes');
                                                    echo $totalDuration > 0 ? 
                                                        ($totalDuration >= 60 ? 
                                                            floor($totalDuration / 60) . 'hr' . ($totalDuration % 60 > 0 ? ' ' . ($totalDuration % 60) . 'min' : '') : 
                                                            $totalDuration . ' mins') 
                                                        : 'N/A';
                                                @endphp
                                                @break
                                            @case('Subject Count')
                                                {{ $mockExam->subjectExams->count() }}
                                                @break
                                            @case('Total Marks')
                                                @php
                                                    $totalMarks = $mockExam->subjectExams->sum(fn($se) => $se->sections->sum(fn($s) => $s->getTotalMarks()));
                                                    echo $totalMarks > 0 ? number_format($totalMarks, 0) : 'N/A';
                                                @endphp
                                                @break
                                            @default
                                                N/A
                                        @endswitch
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                    @break
            @endswitch
        @endforeach
    </div>
@endif

{{-- Fixed Footer --}}
<div class="pg-footer">
    <table>
        <tr>
            <td style="width:50%;">{{ $mockExam->title }}</td>
            <td class="ft-right" style="width:50%;">Page <span class="pagenum"></span></td>
        </tr>
    </table>
</div>

{{-- Document Header --}}
@php
    $schoolName = null;
    try { $schoolName = $mockExam->team?->name; } catch (\Throwable $e) {}
@endphp

<div class="doc-header">
    <div class="rule-heavy"></div>
    <div class="rule-light"></div>
    <div class="header-body">
        @if($schoolName)
            <div class="school-name">{{ $schoolName }}</div>
            <div class="school-tagline">Mock Examination Series</div>
        @endif
        <div class="exam-main-title">{{ $mockExam->title }}</div>
        @if($mockExam->starts_at)
            <div class="exam-sub-date">{{ $mockExam->starts_at->format('l, d F Y') }}</div>
        @endif
    </div>
    <div class="rule-light"></div>
    <div class="rule-heavy"></div>
</div>

{{-- Info Table --}}
@php
    $totalMarks    = $mockExam->subjectExams->sum(fn($se) => $se->sections->sum(fn($s) => $s->getTotalMarks()));
    $totalDuration = $mockExam->subjectExams->sum('duration_in_minutes');
    $subjectCount  = $mockExam->subjectExams->count();
@endphp

<table class="info-tbl">
    <tr>
        <td>
            <span class="ig-lbl">Exam Date</span>
            <span class="ig-val">{{ $mockExam->starts_at ? $mockExam->starts_at->format('d M Y') : now()->format('d M Y') }}</span>
        </td>
        <td>
            <span class="ig-lbl">{{ $subjectCount === 1 ? 'Subject' : 'Papers' }}</span>
            <span class="ig-val">
                @if($subjectCount === 1)
                    {{ Str::limit($mockExam->subjectExams->first()->getDisplayTitle(), 26) }}
                @else
                    {{ $subjectCount }} Papers
                @endif
            </span>
        </td>
        @if($totalDuration > 0)
            <td>
                <span class="ig-lbl">Duration</span>
                <span class="ig-val">
                @if($totalDuration >= 60)
                        {{ floor($totalDuration / 60) }}hr{{ floor($totalDuration / 60) > 1 ? 's' : '' }}{{ $totalDuration % 60 > 0 ? ' '.($totalDuration % 60).'min' : '' }}
                    @else
                        {{ $totalDuration }} mins
                    @endif
            </span>
            </td>
        @endif
        @if($totalMarks > 0)
            <td>
                <span class="ig-lbl">Total Marks</span>
                <span class="ig-val">{{ number_format($totalMarks, 0) }}</span>
            </td>
        @endif
    </tr>
</table>

{{-- Candidate Information --}}
<div class="cand-section">
    <div class="section-label">Candidate Information</div>
    <hr class="section-rule">
    <div class="field-row">
        <div class="field-cell" style="width:100%;">
            <span class="field-lbl">Full Name</span>
            <div class="field-line"></div>
        </div>
    </div>
    <div class="field-row" style="margin-top:10px;">
        <div class="field-cell" style="width:50%;">
            <span class="field-lbl">Index No.</span>
            <div class="field-line"></div>
        </div>
        <div class="field-cell" style="width:50%;">
            <span class="field-lbl">Class / Form</span>
            <div class="field-line"></div>
        </div>
    </div>
    <div class="field-row" style="margin-top:10px;">
        <div class="field-cell" style="width:65%;">
            <span class="field-lbl">Signature</span>
            <div class="field-line"></div>
        </div>
        <div class="field-cell" style="width:35%;">
            <span class="field-lbl">Date</span>
            <div class="field-line"></div>
        </div>
    </div>
</div>

{{-- General Instructions --}}
@if($mockExam->instructions)
    <div class="inst-section">
        <div class="section-label">General Instructions to Candidates</div>
        <hr class="section-rule">
        <div class="inst-body">{!! $mockExam->instructions !!}</div>
    </div>
@endif

{{-- Subject Exams --}}
@foreach($mockExam->subjectExams as $seIdx => $se)
    @if($seIdx > 0)<div class="pg-break"></div>@endif

    <div class="subj-wrap">
        <div class="subj-rule-top"></div>
        <table class="subj-hdr-tbl">
            <tr>
                <td class="subj-hdr-name">{{ $se->getDisplayTitle() }}</td>
                @if($se->duration_in_minutes)
                    <td class="subj-hdr-meta">
                        Time Allowed:
                        @if($se->duration_in_minutes >= 60)
                            {{ floor($se->duration_in_minutes / 60) }}hr{{ floor($se->duration_in_minutes / 60) > 1 ? 's' : '' }}{{ $se->duration_in_minutes % 60 > 0 ? ' '.($se->duration_in_minutes % 60).'min' : '' }}
                        @else
                            {{ $se->duration_in_minutes }} minutes
                        @endif
                    </td>
                @endif
            </tr>
        </table>
        <div class="subj-rule-btm"></div>

        @if($se->instructions)
            <div class="subj-inst">{!! $se->instructions !!}</div>
        @endif

        @foreach($se->sections as $sIdx => $section)
            <div class="sec-wrap">
                <table class="sec-hdr-tbl">
                    <tr>
                        <td class="sec-hdr-name">Section {{ $sIdx + 1 }}: {{ $section->title }}</td>
                        <td class="sec-hdr-meta">
                            {{ $section->questions->count() }} {{ Str::plural('Question', $section->questions->count()) }}
                            &nbsp;·&nbsp;
                            {{ number_format($section->getTotalMarks(), 0) }} Marks
                        </td>
                    </tr>
                </table>
                <div class="sec-hdr-rule"></div>

                @if($section->instructions)
                    <div class="sec-inst">{!! $section->instructions !!}</div>
                @endif

                @foreach($section->questions as $qIdx => $question)
                    <div class="q-item">
                        <div class="q-header">
                            <span class="q-num">{{ $qIdx + 1 }}.</span>
                            <span class="q-text">
                                <x-form.markdown-with-math :content="$question->question_text" inline="true" />
                            </span>
                            <span class="q-marks">[{{ $question->marks }} mk]</span>
                        </div>

                        @if($question->isMultipleChoice() && !empty($question->options))
                            @php $opts = $question->getOptionsForDisplay(); @endphp
                            <div class="opts-wrap">
                                @php $optIdx = 0; @endphp
                                @foreach($opts as $letter => $text)
                                    <div class="opt-row">
                                        <span class="opt-lbl">{{ chr(65 + $optIdx) }}.</span>
                                        <span class="opt-txt"><x-form.markdown-with-math :content="$text" inline="true" /></span>
                                    </div>
                                    @php $optIdx++; @endphp
                                @endforeach
                            </div>

                        @elseif($question->isTrueFalse())
                            <div class="opts-wrap">
                                @foreach([['A','True'],['B','False']] as [$lbl,$val])
                                    <div class="opt-row">
                                        <span class="opt-lbl">{{ $lbl }}.</span>
                                        <span class="opt-txt">{{ $val }}</span>
                                    </div>
                                @endforeach
                            </div>

                        @elseif($question->isEssay())
                            <div class="elines">
                                @php $numLines = max(4, (int) round($question->marks * 1.2)); @endphp
                                @for($l = 0; $l < $numLines; $l++)
                                    <div class="eline"></div>
                                @endfor
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
@endforeach

</body>
</html>