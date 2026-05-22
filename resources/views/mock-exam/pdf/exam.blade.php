<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $mockExam->title }}</title>
    <style>
        /* Basic Reset */
        * { box-sizing: border-box; margin: 0; padding: 0; }

        /* Page Setup */
        @page {
            size: A4;
            margin: 15mm; /* Standard page margins */
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif; /* Modern, readable font stack */
            font-size: {{ $fontSize ?? 11 }}pt;
            color: #1f2937; /* Dark gray for good contrast */
            line-height: 1.6;
            background: #fff;
            /* Ensure content respects page margins and has internal padding */
            padding: 0; /* Rely on @page margins */
        }

        /* Main Container - Mimicking the example's centered, constrained layout */
        .exam-container {
            max-width: 60rem; /* Matches the example's max-w-[60rem] */
            margin: 0 auto; /* Center the content */
            padding: 0 1rem; /* Small horizontal padding for breathing room */
            background: white; /* Ensure background is white for print */
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); /* Optional subtle shadow for screen */
        }

        /* Print-specific adjustments */
        @media print {
            .exam-container {
                max-width: 100%; /* Expand to full width on print */
                margin: 0;
                padding: 0;
                box-shadow: none; /* Remove shadow for print */
            }
            /* Hide elements not meant for print */
            .print\:hidden { display: none !important; }
            /* Apply padding for readability during print preview */
            body { padding: 15mm; }
        }

        /* Header Section */
        .header-section {
            text-align: center;
            padding: 1rem 0 0.75rem; /* Reduced top padding */
            border-bottom: 2px solid #3b82f6; /* Primary blue accent */
            margin-bottom: 1.25rem;
        }
        .school-name {
            font-size: {{ ($fontSize ?? 11) + 1 }}pt; /* Slightly larger */
            font-weight: 600; /* Semi-bold */
            color: #1f2937;
            letter-spacing: 0.025em;
            margin-bottom: 0.25rem;
        }
        .school-tagline {
            font-size: {{ ($fontSize ?? 11) - 2 }}pt;
            color: #6b7280; /* Muted gray */
            font-style: italic;
        }

        /* Exam Title Block */
        .exam-title-block {
            text-align: center;
            margin-bottom: 1.25rem;
        }
        .exam-main-title {
            font-size: {{ ($fontSize ?? 11) + 4 }}pt; /* Significantly larger for impact */
            font-weight: 700; /* Bold */
            color: #1f2937;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.3rem;
        }
        .exam-sub-date {
            font-size: {{ ($fontSize ?? 11) - 1 }}pt;
            color: #6b7280;
        }

        /* Info Grid - Using Flexbox */
        .info-grid {
            display: flex;
            gap: 0.5rem; /* Reduced gap */
            margin-bottom: 1.25rem;
        }
        .info-grid-item {
            flex: 1;
            padding: 0.5rem; /* Reduced padding */
            text-align: center;
            border: 1px solid #d1d5db; /* Light gray border */
            border-radius: 0.375rem; /* Rounded corners */
            background-color: #f9fafb; /* Very light gray background */
        }
        .ig-lbl {
            font-size: 0.75rem; /* Smaller label */
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #9ca3af; /* Muted label color */
            display: block;
            margin-bottom: 0.1rem;
        }
        .ig-val {
            font-size: {{ ($fontSize ?? 11) }}pt; /* Standard value size */
            font-weight: 600; /* Slightly bolder */
            color: #1f2937;
        }

        /* Candidate Information Box */
        .cand-wrap {
            border: 1.5px solid #d1d5db; /* Neutral gray border */
            border-radius: 0.375rem; /* Rounded corners */
            margin-bottom: 1.25rem;
            overflow: hidden;
        }
        .cand-header {
            background-color: #3b82f6; /* Primary blue header */
            color: white;
            padding: 0.5rem 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.875rem; /* Smaller header text */
        }
        .cand-content {
            padding: 1rem;
        }
        .cand-field {
            margin-bottom: 0.75rem; /* Reduced spacing */
        }
        .cand-field:last-child { margin-bottom: 0; }
        .cf-lbl {
            display: block;
            font-size: 0.75rem; /* Smaller label */
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }
        .cf-line {
            display: block;
            height: 0.75rem; /* Reduced height */
            border-bottom: 1px solid #9ca3af; /* Solid underline */
            width: 100%;
        }

        /* Instructions Block */
        .inst-wrap {
            background-color: #eff6ff; /* Light blue background */
            border-left: 3px solid #3b82f6; /* Primary blue left border */
            padding: 0.75rem 1rem; /* Reduced padding */
            margin-bottom: 1.25rem;
            border-radius: 0 0.25rem 0.25rem 0; /* Rounded corners matching border */
        }
        .inst-heading {
            font-size: 0.875rem; /* Smaller heading */
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #1d4ed8; /* Darker blue for contrast */
            margin-bottom: 0.25rem;
        }
        .inst-body {
            font-size: {{ ($fontSize ?? 11) - 0.5 }}pt; /* Slightly smaller than base */
            color: #374151; /* Darker gray for body text */
        }

        /* Subject Wrap */
        .subj-wrap {
            margin-top: 1.25rem;
            margin-bottom: 1rem;
        }
        .subj-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1.5px solid #3b82f6; /* Primary blue border */
            margin-bottom: 0.5rem;
        }
        .subj-hdr-name {
            font-size: {{ ($fontSize ?? 11) + 1 }}pt;
            font-weight: 600;
            color: #1f2937;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }
        .subj-hdr-meta {
            font-size: {{ ($fontSize ?? 11) - 1 }}pt;
            color: #6b7280;
        }

        .subj-inst {
            font-size: {{ ($fontSize ?? 11) - 1.5 }}pt;
            font-style: italic;
            color: #6b7280;
            border-left: 2px solid #3b82f6;
            padding-left: 0.5rem;
            margin-bottom: 0.75rem;
        }

        /* Section Wrap */
        .sec-wrap {
            margin-top: 1.5rem;
            margin-bottom: 1rem;
        }
        .sec-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #d1d5db; /* Neutral gray border */
            margin-bottom: 0.5rem;
        }
        .sec-hdr-name {
            font-size: {{ ($fontSize ?? 11) + 0.5 }}pt;
            font-weight: 600;
            color: #1f2937;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }
        .sec-hdr-meta {
            font-size: {{ ($fontSize ?? 11) - 1.5 }}pt;
            color: #6b7280;
        }
        .sec-inst {
            font-size: {{ ($fontSize ?? 11) - 1.5 }}pt;
            font-style: italic;
            color: #6b7280;
            margin-bottom: 0.75rem;
        }

        /* Question Item */
        .q-item {
            margin-bottom: 1rem; /* Adjusted spacing */
            page-break-inside: avoid;
        }
        .q-header {
            display: flex;
            align-items: flex-start; /* Align top */
            margin-bottom: 0.3rem; /* Reduced space */
        }
        .q-num {
            font-weight: 600;
            color: #1f2937;
            margin-right: 0.5rem; /* Space between number and text */
            min-width: 1.5rem; /* Consistent number column width */
        }
        .q-text {
            flex: 1; /* Take remaining space */
            font-size: {{ ($fontSize ?? 11) }}pt;
            color: #1f2937;
        }
        .q-marks {
            font-size: {{ ($fontSize ?? 11) - 2 }}pt;
            color: #6b7280;
            font-style: italic;
            margin-left: 0.5rem; /* Space before marks */
            white-space: nowrap;
        }

        /* Options List - Vertical (Default) */
        .opts-list {
            list-style-type: upper-alpha;
            padding-left: 1.25rem; /* Reduced indentation */
            margin-top: 0.3rem; /* Reduced space */
        }
        .opts-list li {
            margin-bottom: 0.2rem; /* Reduced space between options */
            font-size: {{ ($fontSize ?? 11) - 0.5 }}pt;
            color: #1f2937;
        }
        .opts-list li:last-child { margin-bottom: 0; }

        /* Options Grid - Horizontal Layout (Toggleable) - Matches example logic */
        .opts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); /* Flexible grid, minimum 200px wide */
            gap: 0.3rem; /* Reduced gap */
            list-style-type: upper-alpha;
            padding-left: 1.25rem; /* Maintain indentation */
            margin-top: 0.3rem; /* Reduced space */
        }
        .opts-grid li {
            margin-bottom: 0; /* Remove default list item margin */
            font-size: {{ ($fontSize ?? 11) - 0.5 }}pt;
            color: #1f2937;
            display: flex;
            align-items: flex-start; /* Align option content to top */
        }
        .opts-grid li::marker { /* Style the alpha marker */
            font-weight: 600;
            color: #1f2937;
        }

        /* Essay Lines */
        .elines {
            margin-top: 0.5rem; /* Reduced space */
        }
        .eline {
            height: 0.75rem; /* Reduced height */
            border-bottom: 1px dashed #d1d5db; /* Dashed line for essay */
            margin-bottom: 0.1rem; /* Reduced space */
        }

        /* Page Break */
        .pg-break { page-break-before: always; }

        /* Footer (Basic styling, actual content placement depends on renderer) */
        .pg-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 0.75rem;
            color: #6b7280;
            padding: 0.25rem 0;
            border-top: 1px solid #e5e7eb; /* Light top border */
        }

    </style>
</head>
<body>

{{-- Fixed Footer (Note: Implementation varies by PDF renderer) --}}
<div class="pg-footer print:hidden">
    <!-- Example footer content: -->
    <span>{{ $mockExam->title }}</span> |
    <span>Page <span class="pagenum"></span></span> |
    <span class="ft-center">— Turn Over —</span>
</div>

{{-- Main Container --}}
<div class="exam-container">

    {{-- Header Section --}}
    @php
        $schoolName = null;
        try { $schoolName = $mockExam->team?->name; } catch (\Throwable $e) {}
    @endphp
    @if($schoolName)
        <div class="header-section">
            <div class="school-name">{{ $schoolName }}</div>
            <div class="school-tagline">Mock Examination Series</div>
        </div>
    @endif

    {{-- Exam Title Block --}}
    <div class="exam-title-block">
        <div class="exam-main-title">{{ $mockExam->title }}</div>
        @if($mockExam->starts_at)
            <div class="exam-sub-date">{{ $mockExam->starts_at->format('l, d F Y') }}</div>
        @endif
    </div>

    {{-- Info Grid --}}
    @php
        $totalMarks    = $mockExam->subjectExams->sum(fn($se) => $se->sections->sum(fn($s) => $s->getTotalMarks()));
        $totalDuration = $mockExam->subjectExams->sum('duration_in_minutes');
        $subjectCount  = $mockExam->subjectExams->count();
    @endphp

    <div class="info-grid">
        <div class="info-grid-item">
            <span class="ig-lbl">Exam Date</span>
            <span class="ig-val">
                {{ $mockExam->starts_at ? $mockExam->starts_at->format('d M Y') : now()->format('d M Y') }}
            </span>
        </div>
        <div class="info-grid-item">
            <span class="ig-lbl">{{ $subjectCount === 1 ? 'Subject' : 'Papers' }}</span>
            <span class="ig-val">
                @if($subjectCount === 1)
                    {{ Str::limit($mockExam->subjectExams->first()->getDisplayTitle(), 26) }}
                @else
                    {{ $subjectCount }} Papers
                @endif
            </span>
        </div>
        @if($totalDuration > 0)
            <div class="info-grid-item">
                <span class="ig-lbl">Duration</span>
                <span class="ig-val">
                    @if($totalDuration >= 60)
                        {{ floor($totalDuration / 60) }}hr{{ floor($totalDuration / 60) > 1 ? 's' : '' }}{{ $totalDuration % 60 > 0 ? ' '.($totalDuration % 60).'min' : '' }}
                    @else
                        {{ $totalDuration }} mins
                    @endif
                </span>
            </div>
        @endif
        @if($totalMarks > 0)
            <div class="info-grid-item">
                <span class="ig-lbl">Total Marks</span>
                <span class="ig-val">{{ number_format($totalMarks, 0) }}</span>
            </div>
        @endif
    </div>

    {{-- Candidate Information --}}
    <div class="cand-wrap">
        <div class="cand-header">Candidate Information</div>
        <div class="cand-content">
            <div class="cand-field">
                <span class="cf-lbl">Full Name</span>
                <span class="cf-line"></span>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <div class="cand-field" style="flex: 1;">
                    <span class="cf-lbl">Index No.</span>
                    <span class="cf-line"></span>
                </div>
                <div class="cand-field" style="flex: 1;">
                    <span class="cf-lbl">Class / Form</span>
                    <span class="cf-line"></span>
                </div>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <div class="cand-field" style="flex: 2;">
                    <span class="cf-lbl">Signature</span>
                    <span class="cf-line"></span>
                </div>
                <div class="cand-field" style="flex: 1;">
                    <span class="cf-lbl">Date</span>
                    <span class="cf-line"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- General Instructions --}}
    @if($mockExam->instructions)
        <div class="inst-wrap">
            <span class="inst-heading">General Instructions to Candidates</span>
            <div class="inst-body">{!! $mockExam->instructions !!}</div> <!-- Use !! for potential HTML formatting -->
        </div>
    @endif

    {{-- Subject Exams Loop --}}
    @foreach($mockExam->subjectExams as $seIdx => $se)
        @if($seIdx > 0) <div class="pg-break"></div> @endif

        <div class="subj-wrap">

            <div class="subj-header">
                <div class="subj-hdr-name">{{ $se->getDisplayTitle() }}</div>
                @if($se->duration_in_minutes)
                    <div class="subj-hdr-meta">
                        Time Allowed:
                        @if($se->duration_in_minutes >= 60)
                            {{ floor($se->duration_in_minutes / 60) }}hr{{ floor($se->duration_in_minutes / 60) > 1 ? 's' : '' }}{{ $se->duration_in_minutes % 60 > 0 ? ' '.($se->duration_in_minutes % 60).'min' : '' }}
                        @else
                            {{ $se->duration_in_minutes }} minutes
                        @endif
                    </div>
                @endif
            </div>

            @if($se->instructions)
                <div class="subj-inst">{!! $se->instructions !!}</div> <!-- Use !! for potential HTML formatting -->
            @endif

            {{-- Sections Loop --}}
            @foreach($se->sections as $sIdx => $section)
                <div class="sec-wrap">
                    <div class="sec-header">
                        <div class="sec-hdr-name">Section {{ $sIdx + 1 }}: {{ $section->title }}</div>
                        <div class="sec-hdr-meta">
                            {{ $section->questions->count() }} {{ Str::plural('Question', $section->questions->count()) }}
                            &nbsp;·&nbsp;
                            {{ number_format($section->getTotalMarks(), 0) }} Marks
                        </div>
                    </div>

                    @if($section->instructions)
                        <div class="sec-inst">{!! $section->instructions !!}</div> <!-- Use !! for potential HTML formatting -->
                    @endif

                    {{-- Questions Loop --}}
                    @foreach($section->questions as $qIdx => $question)
                        <div class="q-item">
                            <div class="q-header">
                                <span class="q-num">{{ $qIdx + 1 }}.</span>
                                <span class="q-text">{!! strip_tags($question->question_text) !!}</span> <!-- Use !! and strip_tags -->
                                <span class="q-marks">[{{ $question->marks }} mk]</span>
                            </div>

                            {{-- MCQ --}}
                            @if($question->isMultipleChoice() && !empty($question->options))
                                @php $opts = $question->getOptionsForDisplay(); @endphp
                                <ol class="{{ ($format ?? 'lenticular') === 'elliptical' ? 'opts-grid' : 'opts-list' }}"> <!-- Toggle based on $format -->
                                    @foreach($opts as $letter => $text)
                                        <li>{!! $text !!}</li> <!-- Use !! for potential HTML in options -->
                                    @endforeach
                                </ol>

                            {{-- True / False --}}
                            @elseif($question->isTrueFalse())
                                 <ol class="{{ ($format ?? 'lenticular') === 'elliptical' ? 'opts-grid' : 'opts-list' }}"> <!-- Toggle based on $format -->
                                     <li>True</li>
                                     <li>False</li>
                                 </ol>

                            {{-- Essay --}}
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

                </div> {{-- /.sec-wrap --}}
            @endforeach

        </div> {{-- /.subj-wrap --}}
    @endforeach

</div> <!-- /.exam-container -->

</body>
</html>