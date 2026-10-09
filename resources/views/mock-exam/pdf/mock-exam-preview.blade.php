<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $mockExam->title }} - Mock Exam Overview</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ public_path('vendor/katex/katex.min.css') }}">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        @page {
            size: A4;
            margin: 15mm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: {{ $fontSize ?? 11 }}pt;
            color: #000;
            line-height: 1.4;
            background: #fff;
        }

        /* Front Page Styles */
        .front-page {
            page-break-after: always;
        }

        .front-page-pdf {
            page-break-after: always;
            text-align: center;
        }

        .front-page-pdf-img {
            width: 180mm;
            display: block;
            margin: 0 auto;
        }

        .fp-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 12px;
        }

        .fp-school {
            font-size: {{ ($fontSize ?? 11) + 8 }}pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 8px;
        }

        .fp-title {
            font-size: {{ ($fontSize ?? 11) + 4 }}pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 6px;
        }

        .fp-subtitle {
            font-size: {{ ($fontSize ?? 11) + 1 }}pt;
            font-style: italic;
            color: #333;
        }

        .fp-info-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000;
            margin-bottom: 20px;
        }

        .fp-info-table td {
            padding: 8px 12px;
            border: 1px solid #000;
            vertical-align: top;
        }

        .fp-info-label {
            font-size: {{ ($fontSize ?? 11) - 2 }}pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #555;
            font-weight: bold;
            display: block;
            margin-bottom: 4px;
        }

        .fp-info-value {
            font-size: {{ $fontSize ?? 11 }}pt;
            font-weight: bold;
        }

        .fp-notice {
            font-size: {{ ($fontSize ?? 11) - 1 }}pt;
            font-style: italic;
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            border: 1px dashed #666;
        }

        /* Exam Content Styles */
        .exam-container {
            max-width: 210mm;
            margin: 0 auto;
        }

        .exam-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ccc;
        }

        .exam-title {
            font-size: {{ ($fontSize ?? 11) + 3 }}pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .exam-meta {
            font-size: {{ $fontSize ?? 11 }}pt;
            color: #555;
        }

        .subject-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .subject-header {
            font-size: {{ ($fontSize ?? 11) + 1 }}pt;
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-header {
            font-size: {{ $fontSize ?? 11 }}pt;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .question {
            margin-bottom: 15px;
        }

        .question-number {
            font-weight: bold;
            display: inline-block;
            min-width: 25px;
        }

        .question-content {
            display: inline-block;
            width: calc(100% - 25px);
        }

        .question-text {
            margin-bottom: 8px;
        }

        .options {
            margin-left: 25px;
        }

        .option {
            margin-bottom: 5px;
        }

        .option-letter {
            font-weight: bold;
            display: inline-block;
            width: 20px;
        }

        .answer-space {
            border-bottom: 1px solid #666;
            min-height: 20px;
            margin: 10px 0;
        }

        .instructions {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border-left: 3px solid #007cba;
        }

        .page-break {
            page-break-before: always;
        }

        .identity-section {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: {{ ($fontSize ?? 11) - 2 }}pt;
            color: #666;
            padding: 5px 0;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="exam-container">
        <!-- Front Page -->
        @php
            $hasFrontPageConfig = $mockExam->subjectExams->contains(function($subjectExam) {
                return $subjectExam->template && !empty($subjectExam->template->front_page_config);
            });
        @endphp
        
        @if($hasFrontPageConfig)
            <div class="front-page-pdf">
                @php
                    $firstSubjectExam = $mockExam->subjectExams->first();
                    $template = $firstSubjectExam ? $firstSubjectExam->template : null;
                    
                    // If no template with front page config exists, use basic info
                    $schoolName = $mockExam->school_name ?: $mockExam->user->name ?? 'School Name';
                    $title = $mockExam->title;
                    $subtitle = $mockExam->subtitle ?: 'Mock Examination Paper';
                @endphp
                
                <div class="fp-header">
                    <div class="fp-school">{{ $schoolName }}</div>
                    <div class="fp-title">{{ $title }}</div>
                    <div class="fp-subtitle">{{ $subtitle }}</div>
                </div>

                <table class="fp-info-table">
                    <tr>
                        <td style="width: 25%;">
                            <span class="fp-info-label">ACADEMIC GROUP</span>
                            <span class="fp-info-value">{{ $firstSubjectExam?->academicGroup?->name ?: 'N/A' }}</span>
                        </td>
                        <td style="width: 25%;">
                            <span class="fp-info-label">ACADEMIC LEVEL</span>
                            <span class="fp-info-value">{{ $firstSubjectExam?->academicLevel?->name ?: 'N/A' }}</span>
                        </td>
                        <td style="width: 25%;">
                            <span class="fp-info-label">DATE</span>
                            <span class="fp-info-value">{{ $mockExam->starts_at ? $mockExam->starts_at->format('M d, Y') : 'TBA' }}</span>
                        </td>
                        <td style="width: 25%;">
                            <span class="fp-info-label">DURATION</span>
                            <span class="fp-info-value">{{ $mockExam->duration_in_minutes ? $mockExam->duration_in_minutes . ' minutes' : 'N/A' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <span class="fp-info-label">INSTRUCTIONS</span>
                            <span class="fp-info-value">{{ $mockExam->instructions ?: 'Answer all questions as directed.' }}</span>
                        </td>
                    </tr>
                </table>

                <div class="fp-notice">
                    This is a mock examination paper. Answers provided are for practice purposes only.
                </div>
            </div>
        @else
            <!-- Basic header when no front page config exists -->
            <div class="exam-header">
                <div class="exam-title">{{ $mockExam->title }}</div>
                <div class="exam-meta">Mock Examination Overview</div>
            </div>
        @endif

        <!-- Identity Section -->
        @isset($identity)
            <div class="identity-section">
                @if(data_get($identity, 'show_name_field', false))
                    <div>Name: _________________________</div>
                @endif
                @if(data_get($identity, 'show_index_number_field', false))
                    <div>Index Number: _________________________</div>
                @endif
                @if(data_get($identity, 'show_class_field', false))
                    <div>Class: _________________________</div>
                @endif
                @if(data_get($identity, 'show_gender_field', false))
                    <div>Gender: _________________________</div>
                @endif
                @if(data_get($identity, 'show_age_field', false))
                    <div>Age: _________________________</div>
                @endif
            </div>
        @endisset

        <!-- Individual Subject Exams -->
        @foreach($mockExam->subjectExams as $subjectExam)
            <div class="subject-section">
                <div class="subject-header">{{ $subjectExam->getDisplayTitle() }}</div>

                @if($subjectExam->instructions)
                    <div class="instructions">{{ $subjectExam->instructions }}</div>
                @endif

                @foreach($subjectExam->sections as $section)
                    <div class="section">
                        <div class="section-header">
                            {{ $section->title }} ({{ $section->getTotalMarks() }} marks)
                        </div>

                        @foreach($section->questions as $question)
                            <div class="question">
                                <span class="question-number">{{ $loop->iteration }}.</span>
                                <div class="question-content">
                                    <div class="question-text">{!! $question->formatted_question !!}</div>

                                    @if($question->source_type === 'multiple_choice' && is_array($question->options) && !empty($question->options))
                                        <div class="options">
                                            @foreach($question->options as $option)
                                                <div class="option">
                                                    <span class="option-letter">{{ chr(65 + $loop->index) }}.</span>
                                                    <span>{!! $option !!}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="answer-space"></div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

            @unless($loop->last)
                <div class="page-break"></div>
            @endunless
        @endforeach
    </div>
</body>
</html>