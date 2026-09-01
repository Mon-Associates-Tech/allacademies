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

        /* ─── Front Page ─── */
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
        .fp-content {
            margin: 25px 0;
            text-align: left;
            font-size: {{ $fontSize ?? 11 }}pt;
            line-height: 1.7;
        }
        .fp-divider {
            width: 100%;
            height: 2px;
            background: #aaa;
            margin: 30px 0;
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
        .fp-fill {
            display: inline-block;
            width: 100%;
            border-bottom: 1px solid #444;
            height: 14px;
        }
    </style>
</head>
<body>
    {{-- Front Page if subject exam has template with front page config --}}
    @if($subjectExam->template && !empty($subjectExam->template->front_page_config['blocks']))
        <div class="front-page">
            @foreach($subjectExam->template->front_page_config['blocks'] ?? [] as $block)
                @switch($block['type'])
                    @case('heading')
                        <div class="fp-title" style="
                            font-size: {{ ($fontSize ?? 11) + ($block['level'] == 'h1' ? 8 : ($block['level'] == 'h2' ? 5 : 2)) }}pt;
                            @if($block['level'] == 'h1') text-transform: uppercase; letter-spacing: 0.05em; @endif
                        ">
                            {{ $block['content'] ?? '' }}
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
                        @php
                            $fpFieldLabels = [
                                'candidate_name' => 'Full Name',
                                'index_number'   => 'Index Number',
                                'date'           => 'Date',
                                'duration'       => 'Duration',
                                'subject'        => 'Subject',
                                'grade'          => 'Grade / Class',
                                'signature'      => 'Invigilator Signature',
                                'score'          => 'Total Score',
                            ];
                            $fpFieldValues = [
                                'date' => $subjectExam->mockExam->starts_at
                                    ? $subjectExam->mockExam->starts_at->format('d M Y')
                                    : now()->format('d M Y'),
                                'duration' => $subjectExam->duration_in_minutes
                                    ? ($subjectExam->duration_in_minutes >= 60
                                        ? floor($subjectExam->duration_in_minutes / 60) . 'hr' . ($subjectExam->duration_in_minutes % 60 > 0 ? ' ' . ($subjectExam->duration_in_minutes % 60) . 'min' : '')
                                        : $subjectExam->duration_in_minutes . ' mins')
                                    : null,
                                'subject' => $subjectExam->academicSubject?->name,
                            ];
                            $fpActiveFields = $block['fields'] ?? [];
                        @endphp
                        <table class="fp-info-table">
                            <thead>
                                <tr>
                                    @foreach($fpActiveFields as $fieldKey)
                                        <th>{{ $fpFieldLabels[$fieldKey] ?? $fieldKey }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @foreach($fpActiveFields as $fieldKey)
                                        <td>
                                            @if(!empty($fpFieldValues[$fieldKey]))
                                                {{ $fpFieldValues[$fieldKey] }}
                                            @else
                                                <span class="fp-fill">&nbsp;</span>
                                            @endif
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

    <div class="exam-container">
        {{-- Header --}}
        <div class="header-section">
            <div class="school-name">{{ config('company.name', 'All Academies') }}</div>
            <div class="exam-main-title">{{ $subjectExam->mockExam->title }}</div>
            <div class="subject-title">{{ $subjectExam->getDisplayTitle() }}</div>
        </div>

        {{-- Info Grid --}}
        <div class="info-grid">
            @if($subjectExam->academicGroup)
            <div class="info-item">
                <span class="ig-lbl">Group</span>
                <span class="ig-val">{{ $subjectExam->academicGroup->name }}</span>
            </div>
            @endif

            @if($subjectExam->academicLevel)
            <div class="info-item">
                <span class="ig-lbl">Level</span>
                <span class="ig-val">{{ $subjectExam->academicLevel->name }}</span>
            </div>
            @endif

            <div class="info-item">
                <span class="ig-lbl">Subject</span>
                <span class="ig-val">{{ $subjectExam->academicSubject?->name }}</span>
            </div>

            @if($subjectExam->duration_in_minutes)
            <div class="info-item">
                <span class="ig-lbl">Duration</span>
                <span class="ig-val">{{ $subjectExam->duration_in_minutes }} minutes</span>
            </div>
            @endif

            <div class="info-item">
                <span class="ig-lbl">Total Marks</span>
                <span class="ig-val">{{ number_format($subjectExam->getTotalMarks(), 1) }}</span>
            </div>
        </div>

        {{-- Instructions --}}
        @if($subjectExam->instructions || $subjectExam->mockExam->instructions)
        <div class="inst-wrap">
            <div class="inst-heading">Instructions</div>
            <div class="inst-body">
                @if($subjectExam->instructions)
                    {{ $subjectExam->instructions }}
                @else
                    {{ $subjectExam->mockExam->instructions }}
                @endif
            </div>
        </div>
        @endif

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
                        <x-markdown-renderer :content="$question->question_text" inline="true" />
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
                            <x-markdown-renderer :content="$option" inline="true" />
                        </span>
                    </div>
                    @php $optionIndex++; @endphp
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endforeach
    </div>
</body>
</html>
