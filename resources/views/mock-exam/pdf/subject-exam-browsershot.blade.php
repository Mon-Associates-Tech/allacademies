<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subjectExam->getDisplayTitle() }} - {{ $subjectExam->mockExam->title }}</title>
    
    {{-- Vite assets (CSS & JS/Alpine) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- KaTeX CSS (Use asset() for correct URL resolution) --}}
    <link rel="stylesheet" href="{{ asset('vendor/katex/katex.min.css') }}">
    
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        @page { size: A4; margin: 15mm; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: {{ $fontSize ?? 11 }}pt;
            color: #000;
            line-height: 1.4;
            background: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        /* Front Page Styles */
        .front-page { page-break-after: always; }
        .fp-header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 12px; }
        .fp-school { font-size: {{ ($fontSize ?? 11) + 8 }}pt; font-weight: bold; text-transform: uppercase; letter-spacing: 3px; margin-bottom: 8px; }
        .fp-title { font-size: {{ ($fontSize ?? 11) + 4 }}pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 6px; }
        .fp-subtitle { font-size: {{ ($fontSize ?? 11) + 1 }}pt; font-style: italic; color: #333; }
        .fp-info-table { width: 100%; border-collapse: collapse; border: 1.5px solid #000; margin-bottom: 20px; }
        .fp-info-table td { padding: 8px 12px; border: 1px solid #000; vertical-align: top; }
        .fp-info-label { font-size: {{ ($fontSize ?? 11) - 2 }}pt; text-transform: uppercase; letter-spacing: 1px; color: #555; font-weight: bold; display: block; margin-bottom: 4px; }
        .fp-info-value { font-size: {{ $fontSize ?? 11 }}pt; font-weight: bold; }
        
        /* Exam Content Styles */
        .exam-content { margin-top: 20px; }
        .header-section { text-align: center; padding: 1rem 0 0.75rem; border-bottom: 2px solid #3b82f6; margin-bottom: 1.25rem; }
        .school-name { font-size: {{ ($fontSize ?? 11) + 1 }}pt; font-weight: 600; color: #1f2937; margin-bottom: 0.25rem; }
        .exam-main-title { font-size: {{ ($fontSize ?? 11) + 3 }}pt; font-weight: 700; color: #1f2937; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.3rem; }
        .subject-title { font-size: {{ ($fontSize ?? 11) + 1 }}pt; font-weight: 600; color: #3b82f6; margin-top: 0.5rem; }
        
        .info-grid { display: flex; justify-content: space-between; gap: 1rem; margin-bottom: 1.25rem; padding: 0.75rem; background-color: #f9fafb; border-radius: 0.375rem; border: 1px solid #e5e7eb; }
        .info-item { flex: 1; }
        .ig-lbl { font-size: 0.75rem; text-transform: uppercase; color: #9ca3af; display: block; margin-bottom: 0.1rem; }
        .ig-val { font-size: {{ $fontSize ?? 11 }}pt; font-weight: 600; color: #1f2937; }
        
        .inst-wrap { background-color: #eff6ff; border-left: 3px solid #3b82f6; padding: 0.75rem 1rem; margin-bottom: 1.25rem; border-radius: 0 0.25rem 0.25rem 0; }
        .inst-heading { font-size: 0.875rem; font-weight: 600; text-transform: uppercase; color: #1d4ed8; margin-bottom: 0.25rem; }
        .inst-body { font-size: {{ ($fontSize ?? 11) - 0.5 }}pt; color: #374151; white-space: pre-wrap; }
        
        .section-wrap { margin-top: 1.5rem; page-break-inside: avoid; }
        .section-header { padding: 0.5rem 0; border-bottom: 1.5px solid #3b82f6; margin-bottom: 0.75rem; }
        .section-title { font-size: {{ ($fontSize ?? 11) + 1 }}pt; font-weight: 600; color: #1f2937; text-transform: uppercase; }
        .section-meta { font-size: {{ ($fontSize ?? 11) - 1 }}pt; color: #6b7280; margin-top: 0.25rem; }
        
        .question-block { margin-bottom: 1rem; page-break-inside: avoid; }
        .question-header { display: flex; align-items: baseline; gap: 0.5rem; margin-bottom: 0.5rem; }
        .question-number { font-weight: 600; min-width: 1.5rem; flex-shrink: 0; }
        .question-text { flex: 1; font-size: {{ $fontSize ?? 11 }}pt; color: #1f2937; line-height: 1.6; }
        .question-marks { font-size: {{ ($fontSize ?? 11) - 1 }}pt; color: #6b7280; font-weight: 600; white-space: nowrap; margin-left: auto; }
        
        .options-list { margin-left: 2rem; margin-top: 0.5rem; }
        .option-item { display: flex; gap: 0.5rem; margin-bottom: 0.35rem; align-items: baseline; }
        .option-label { font-weight: 600; min-width: 1.5rem; flex-shrink: 0; }
        .option-text { flex: 1; font-size: {{ ($fontSize ?? 11) - 0.5 }}pt; color: #374151; }
        
        .page-break { page-break-before: always; }
        .prose-inline .katex-display { display: inline-block; margin: 0; }
    </style>
</head>
<body>
    @php
        $companyName = config('company.name', 'All Academies');
        $template = $subjectExam->template;
    @endphp

    {{-- Front Page --}}
    <div class="front-page">
        <div class="fp-header">
            <div class="fp-school">{{ $companyName }}</div>
            @if($template)
                <div class="fp-title">{{ $template->name }}</div>
                @if($template->description) <div class="fp-subtitle">{{ $template->description }}</div> @endif
            @endif
        </div>

        <table class="fp-info-table">
            <tr>
                @if($subjectExam->academicGroup) <td><span class="fp-info-label">Group</span><span class="fp-info-value">{{ $subjectExam->academicGroup->name }}</span></td> @endif
                @if($subjectExam->academicLevel) <td><span class="fp-info-label">Level</span><span class="fp-info-value">{{ $subjectExam->academicLevel->name }}</span></td> @endif
                @if($subjectExam->academicSubject) <td><span class="fp-info-label">Subject</span><span class="fp-info-value">{{ $subjectExam->academicSubject->name }}</span></td> @endif
                @if($subjectExam->duration_in_minutes) <td><span class="fp-info-label">Duration</span><span class="fp-info-value">{{ $subjectExam->duration_in_minutes }} min</span></td> @endif
                <td><span class="fp-info-label">Total Marks</span><span class="fp-info-value">{{ number_format($subjectExam->getTotalMarks(), 1) }}</span></td>
            </tr>
        </table>

        @if($template && !empty($template->front_page_config['content']))
            <div style="text-align: left; line-height: 1.6; margin-bottom: 12px;">
                {!! $template->front_page_config['content'] !!}
            </div>
        @endif
    </div>

    {{-- Exam Content --}}
    <div class="exam-content">
        <div class="header-section">
            <div class="school-name">{{ config('company.name', 'All Academies') }}</div>
            <div class="exam-main-title">{{ $subjectExam->mockExam->title }}</div>
            <div class="subject-title">{{ $subjectExam->getDisplayTitle() }}</div>
        </div>

        <div class="info-grid">
            @if($subjectExam->academicGroup) <div class="info-item"><span class="ig-lbl">Group</span><span class="ig-val">{{ $subjectExam->academicGroup->name }}</span></div> @endif
            @if($subjectExam->academicLevel) <div class="info-item"><span class="ig-lbl">Level</span><span class="ig-val">{{ $subjectExam->academicLevel->name }}</span></div> @endif
            <div class="info-item"><span class="ig-lbl">Subject</span><span class="ig-val">{{ $subjectExam->academicSubject?->name }}</span></div>
            @if($subjectExam->duration_in_minutes) <div class="info-item"><span class="ig-lbl">Duration</span><span class="ig-val">{{ $subjectExam->duration_in_minutes }} min</span></div> @endif
            <div class="info-item"><span class="ig-lbl">Total Marks</span><span class="ig-val">{{ number_format($subjectExam->getTotalMarks(), 1) }}</span></div>
        </div>

        @if($subjectExam->instructions || $subjectExam->mockExam->instructions)
        <div class="inst-wrap">
            <div class="inst-heading">Instructions</div>
            <div class="inst-body">{{ $subjectExam->instructions ?? $subjectExam->mockExam->instructions }}</div>
        </div>
        @endif

        @foreach($subjectExam->sections as $sectionIndex => $section)
        <div class="section-wrap {{ $sectionIndex > 0 ? 'page-break' : '' }}">
            <div class="section-header">
                <div class="section-title">{{ $section->title }}</div>
                <div class="section-meta">{{ $section->questions->count() }} questions • {{ number_format($section->getTotalMarks(), 1) }} marks</div>
            </div>

            @if($section->instructions)
            <div style="font-size: {{ ($fontSize ?? 11) - 0.5 }}pt; color: #4b5563; font-style: italic; margin-bottom: 0.75rem; padding: 0.5rem; background-color: #fef3c7; border-left: 2px solid #f59e0b;">
                {{ $section->instructions }}
            </div>
            @endif

            @foreach($section->questions as $question)
            <div class="question-block">
                <div class="question-header">
                    <span class="question-number">{{ $loop->iteration }}</span>
                    <span class="question-text">
                        <x-ui.math-display :content="$question->question_text" inline="true" />
                    </span>
                    <span class="question-marks">[{{ $question->marks }} mark{{ $question->marks != 1 ? 's' : '' }}]</span>
                </div>

                @if(in_array($question->source_type, ['multiple_choice', 'true_false']) && is_array($question->options) && !empty($question->options))
                <div class="options-list">
                    @foreach($question->options as $optionIndex => $option)
                    <div class="option-item">
                        <span class="option-label">{{ chr(65 + (int)$optionIndex) }}.</span>
                        <span class="option-text"><x-ui.math-display :content="$option" inline="true" /></span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endforeach
    </div>

    {{-- Browsershot Signal: Hides until Alpine/JS is fully ready --}}
    <div x-data x-init="$el.setAttribute('data-ready', 'true')" id="browsershot-ready" style="display: none;"></div>
</body>
</html>