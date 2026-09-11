<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $subjectExam->getDisplayTitle() }} - {{ $subjectExam->mockExam->title }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ public_path('vendor/katex/katex.min.css') }}">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        @page {
            size: A4;
            margin: 15mm;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: {{ $fontSize ?? 11 }}pt;
            color: #1f2937;
            line-height: 1.6;
            background: #fff;
            padding: 0;
        }

        .exam-container {
            max-width: 60rem;
            margin: 0 auto;
            padding: 0 1rem;
            background: white;
        }

        @media print {
            .exam-container {
                max-width: 100%;
                margin: 0;
                padding: 0;
            }
            body { padding: 15mm; }
        }

        /* Header Section */
        .header-section {
            text-align: center;
            padding: 1rem 0 0.75rem;
            border-bottom: 2px solid #3b82f6;
            margin-bottom: 1.25rem;
        }
        .school-name {
            font-size: {{ ($fontSize ?? 11) + 1 }}pt;
            font-weight: 600;
            color: #1f2937;
            letter-spacing: 0.025em;
            margin-bottom: 0.25rem;
        }
        .exam-main-title {
            font-size: {{ ($fontSize ?? 11) + 3 }}pt;
            font-weight: 700;
            color: #1f2937;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.3rem;
        }
        .subject-title {
            font-size: {{ ($fontSize ?? 11) + 1 }}pt;
            font-weight: 600;
            color: #3b82f6;
            margin-top: 0.5rem;
        }

        /* Info Grid */
        .info-grid {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.25rem;
            padding: 0.75rem;
            background-color: #f9fafb;
            border-radius: 0.375rem;
            border: 1px solid #e5e7eb;
        }
        .info-item {
            flex: 1;
        }
        .ig-lbl {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #9ca3af;
            display: block;
            margin-bottom: 0.1rem;
        }
        .ig-val {
            font-size: {{ ($fontSize ?? 11) }}pt;
            font-weight: 600;
            color: #1f2937;
        }

        /* Instructions */
        .inst-wrap {
            background-color: #eff6ff;
            border-left: 3px solid #3b82f6;
            padding: 0.75rem 1rem;
            margin-bottom: 1.25rem;
            border-radius: 0 0.25rem 0.25rem 0;
        }
        .inst-heading {
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #1d4ed8;
            margin-bottom: 0.25rem;
        }
        .inst-body {
            font-size: {{ ($fontSize ?? 11) - 0.5 }}pt;
            color: #374151;
            white-space: pre-wrap;
        }

        /* Section Styles */
        .section-wrap {
            margin-top: 1.5rem;
            page-break-inside: avoid;
        }
        .section-header {
            padding: 0.5rem 0;
            border-bottom: 1.5px solid #3b82f6;
            margin-bottom: 0.75rem;
        }
        .section-title {
            font-size: {{ ($fontSize ?? 11) + 1 }}pt;
            font-weight: 600;
            color: #1f2937;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }
        .section-meta {
            font-size: {{ ($fontSize ?? 11) - 1 }}pt;
            color: #6b7280;
            margin-top: 0.25rem;
        }
        .section-instructions {
            font-size: {{ ($fontSize ?? 11) - 0.5 }}pt;
            color: #4b5563;
            font-style: italic;
            margin-bottom: 0.75rem;
            padding: 0.5rem;
            background-color: #fef3c7;
            border-left: 2px solid #f59e0b;
        }

        /* Question Styles */
        .question-block {
            margin-bottom: 1rem;
            page-break-inside: avoid;
        }
        .question-header {
            display: flex;
            align-items: baseline;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .question-number {
            font-weight: 600;
            color: black;
            min-width: 1.5rem;
            flex-shrink: 0;
        }
        .question-text {
            flex: 1;
            font-size: {{ $fontSize ?? 11 }}pt;
            color: #1f2937;
            line-height: 1.6;
        }
        .question-marks {
            font-size: {{ ($fontSize ?? 11) - 1 }}pt;
            color: #6b7280;
            font-weight: 600;
            white-space: nowrap;
            flex-shrink: 0;
            margin-left: auto;
        }

        /* Options for MCQ */
        .options-list {
            margin-left: 2rem;
            margin-top: 0.5rem;
        }
        .option-item {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 0.35rem;
            align-items: baseline;
            page-break-inside: avoid;
        }
        .option-label {
            font-weight: 600;
            color: black;
            min-width: 1.5rem;
            flex-shrink: 0;
        }
        .option-text {
            flex: 1;
            font-size: {{ ($fontSize ?? 11) - 0.5 }}pt;
            color: #374151;
            line-height: 1.5;
        }

        /* Page break helpers */
        .page-break {
            page-break-before: always;
        }

        .prose-inline .katex-display { display: inline-block; margin: 0; }

        /* ─── Front Page (Cover) ─── */
        .front-page {
            page-break-after: always;
            font-family: 'Georgia', 'Times New Roman', serif;
            color: #1a1a1a;
        }
        .front-page-frame {
            display: table;
            width: 100%;
            height: 267mm;
            border: 1px solid #cbd5e1;
            padding: 14mm 16mm;
        }
        .front-page-content {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }
        .fp-block {
            margin-bottom: 10mm;
        }
        .fp-block:last-child {
            margin-bottom: 0;
        }

        /* Headings */
        .fp-heading-h1 {
            font-size: {{ ($fontSize ?? 11) + 9 }}pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #111827;
            line-height: 1.35;
            padding-bottom: 6mm;
            border-bottom: 2px solid #1f2937;
        }
        .fp-heading-h2 {
            font-size: {{ ($fontSize ?? 11) + 5 }}pt;
            font-weight: 600;
            color: #1f2937;
            letter-spacing: 0.02em;
            line-height: 1.4;
        }
        .fp-heading-h3 {
            font-size: {{ ($fontSize ?? 11) + 1 }}pt;
            font-weight: 600;
            color: #4b5563;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            line-height: 1.4;
        }

        /* Rich text */
        .fp-richtext {
            max-width: 130mm;
            margin: 0 auto;
            text-align: left;
            font-size: {{ $fontSize ?? 11 }}pt;
            line-height: 1.7;
            color: #374151;
        }

        /* Image */
        .fp-image {
            display: inline-block;
            border: 1px solid #e5e7eb;
            padding: 2mm;
            background: #fff;
            max-width: 100%;
        }

        /* Divider */
        .fp-divider {
            width: 40mm;
            height: 0;
            border-top: 1px solid #9ca3af;
            margin: 0 auto;
        }

        /* Declaration panel (info table) */
        .fp-declaration {
            max-width: 145mm;
            margin: 0 auto;
            border-top: 1px solid #d1d5db;
            border-bottom: 1px solid #d1d5db;
            padding: 6mm 2mm;
        }
        .fp-decl-row {
            display: table;
            width: 100%;
            table-layout: fixed;
            margin-bottom: 5mm;
        }
        .fp-decl-row:last-child {
            margin-bottom: 0;
        }
        .fp-decl-field {
            display: table-cell;
            padding: 0 4mm;
            text-align: left;
            vertical-align: bottom;
        }
        .fp-decl-label {
            display: block;
            font-size: 7.5pt;
            font-family: 'Inter', sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #6b7280;
            margin-bottom: 2mm;
        }
        .fp-decl-value {
            display: block;
            font-size: {{ $fontSize ?? 11 }}pt;
            font-weight: 600;
            color: #111827;
            border-bottom: 1px solid #9ca3af;
            min-height: 5mm;
            padding-bottom: 1mm;
        }
    </style>
</head>
<body>
    {{-- Front Page if subject exam has template with front page config --}}
      @if($subjectExam->template && !empty($subjectExam->template->front_page_config['content']))
        <div class="front-page">
            <div class="front-page-frame">
                <div class="front-page-content">
                    @if(!empty($subjectExam->template->front_page_config['content']))
                        <div class="fp-block fp-richtext">
                            {!! $subjectExam->template->front_page_config['content'] !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif


    <div class="exam-container">

        {{-- Sections and Questions --}}
        @foreach($subjectExam->sections as $sectionIndex => $section)
        <div class="section-wrap {{ $sectionIndex > 0 ? 'page-break' : '' }}">
            <div class="section-header">
                <div class="section-title">{{ $section->title }}</div>
                <div class="section-meta">
                    {{ $section->questions->count() }} questions •
                    {{ number_format($section->getTotalMarks(), 1) }} marks
                    @if($section->question_type !== 'mixed')
                        • {{ ucwords(str_replace('_', ' ', $section->question_type)) }}
                    @endif
                </div>
            </div>

            @if($section->instructions)
            <div class="section-instructions">
                {{ $section->instructions }}
            </div>
            @endif

            @foreach($section->questions as $qIndex => $question)
            <div class="question-block">
                <div class="question-header">
                    <span class="question-number">{{ $loop->iteration }}</span>
                    <span class="question-text">
{{--                        @dd($question)--}}
                        <x-ui.latex :display="true" :content="$question->question_text" inline="true" />
                    </span>
                    <span class="question-marks">[{{ $question->marks }} mark{{ $question->marks != 1 ? 's' : '' }}]</span>
                </div>

                @if(in_array($question->source_type, ['multiple_choice', 'true_false']) && is_array($question->options) && !empty($question->options))
                <div class="options-list">
                    @php $optionIndex = 0; @endphp
                    @foreach($question->options as $option)

                    <div class="option-item">
                        <span class="option-label">{{ chr(65 + (int)$optionIndex) }}.</span>
                        <span class="option-text">
                            <x-ui.latex :content="$option" inline="true" />
                        </span>
                    </div>
                    @php $optionIndex++; @endphp
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach

            @include('mock-exam.pdf.partials.section-attachment', ['section' => $section])
        </div>
        @endforeach
    </div>
</body>
</html>