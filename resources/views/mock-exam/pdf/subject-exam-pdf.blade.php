<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $subjectExam->getDisplayTitle() }} - {{ $subjectExam->mockExam->title }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
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
            max-width: 100%;
            margin: 0;
            padding: 0;
            background: white;
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
            display: table;
            width: 100%;
            margin-bottom: 1.25rem;
            padding: 0.75rem;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
        }

        .info-grid-inner {
            display: table-row;
        }

        .info-item {
            display: table-cell;
            width: 25%;
            vertical-align: top;
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
            font-size: {{ $fontSize ?? 11 }}pt;
            font-weight: 600;
            color: #1f2937;
        }

        /* Instructions */
        .inst-wrap {
            background-color: #eff6ff;
            border-left: 3px solid #3b82f6;
            padding: 0.75rem 1rem;
            margin-bottom: 1.25rem;
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
            margin-bottom: 0.75rem;
        }

        .section-title {
            font-size: {{ ($fontSize ?? 11) + 1 }}pt;
            font-weight: 600;
            color: #1f2937;
            /*text-transform: uppercase;*/
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
            /*border-left: 2px solid #f59e0b;*/
        }

        .question-block {
            margin-bottom: 12pt;
            page-break-inside: avoid;
        }

        table.question-header,
        table.options-list {
            width: 100%;
            border-collapse: collapse;
        }

        .question-header td,
        .options-list td {
            vertical-align: top;
            padding: 0;
        }

        .question-header {
            margin-bottom: 6pt;
        }

        .question-number {
            width: 24px;
            font-weight: 600;
            color: black;
        }

        .question-text {
            font-size: {{ $fontSize ?? 11 }}pt;
            color: #1f2937;
            line-height: 1.6;
        }

        .options-wrap {
            margin-left: 24px;
        }

        /* Vertical layout (default) — table rows */
        .options-wrap.layout-vertical .option-item {
            page-break-inside: avoid;
        }

        .options-wrap.layout-vertical .option-label {
            width: 24px;
            font-weight: 600;
            color: black;
            padding-bottom: 4pt;
        }

        .options-wrap.layout-vertical .option-text {
            font-size: {{ ($fontSize ?? 11) - 0.5 }}pt;
            color: #374151;
            line-height: 1.5;
            padding-bottom: 4pt;
        }

        /* Horizontal layout — inline options */
        .options-wrap.layout-horizontal {
            display: flex;
            flex-wrap: wrap;
            gap: 8pt 16pt;
            margin-left: 24px;
        }

        .options-wrap.layout-horizontal .option-item {
            display: inline-flex;
            align-items: baseline;
            gap: 4pt;
            page-break-inside: avoid;
        }

        .options-wrap.layout-horizontal .option-label {
            font-weight: 600;
            color: black;
            white-space: nowrap;
        }

        .options-wrap.layout-horizontal .option-text {
            font-size: {{ ($fontSize ?? 11) - 0.5 }}pt;
            color: #374151;
            line-height: 1.5;
        }

        /* Grid layout — 2 columns */
        .options-wrap.layout-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4pt 16pt;
            margin-left: 24px;
        }

        .options-wrap.layout-grid .option-item {
            display: flex;
            align-items: baseline;
            gap: 4pt;
            page-break-inside: avoid;
        }

        .options-wrap.layout-grid .option-label {
            font-weight: 600;
            color: black;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .options-wrap.layout-grid .option-text {
            font-size: {{ ($fontSize ?? 11) - 0.5 }}pt;
            color: #374151;
            line-height: 1.5;
        }

        /* Neutralise the block elements the markdown component emits */
        .question-text p,
        .option-text p,
        .question-text > div,
        .option-text > div {
            margin: 0;
            padding: 0;
        }

        .question-text p + p {
            margin-top: 4pt;
        }

        /* Page break helpers */
        .page-break {
            page-break-before: always;
        }

        /* Blank page — exactly one page, centered text (flexbox works in Browsershot) */
        .blank-page-wrap {
            page-break-before: always;
            page-break-inside: avoid;
            height: 267mm;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .blank-page-cell {
            text-align: center;
            width: 100%;
        }

        .prose-inline .katex-display {
            display: inline-block;
            margin: 0;
        }

        /* ─── Front Page (Cover) ─── */
        .front-page {
            page-break-after: always;
            page-break-inside: avoid;
            font-family: 'Georgia', 'Times New Roman', serif;
            color: #1a1a1a;
        }

        .front-page-frame {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 267mm;
            padding: 14mm 16mm;
        }

        .front-page-content {
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
            min-height: 5mm;
            padding-bottom: 1mm;
        }

        /* Uploaded PDF front page (mode: upload) — each source page rendered as-is */
        .front-page-pdf {
            page-break-after: always;
            page-break-inside: avoid;
            text-align: center;
        }

        .front-page-pdf-img {
            width: 180mm;
            max-height: 267mm;
            display: block;
            margin: 0 auto;
        }

        /* Identity section */
        .identity-section {
            text-align: center;
            padding-bottom: 8mm;
            margin-bottom: 8mm;
        }

        .identity-row {
            display: table;
            margin: 0 auto 2mm auto;
        }

        .identity-row:last-child {
            margin-bottom: 0;
        }

        .identity-row-cell {
            display: table-cell;
            vertical-align: middle;
            padding: 0 3mm;
        }

        .identity-logo {
            max-height: 18mm;
        }

        .identity-line {
            font-size: {{ ($fontSize ?? 11) + 1 }}pt;
            font-weight: 600;
            margin-bottom: 1.5mm;
        }

        .identity-pretext {
            font-weight: 400;
            color: #333;
        }

        /* KaTeX inline styles for PDF */
        .katex { font: normal 1.1em KaTeX_Main, 'Times New Roman', serif; }
        .katex-display { display: block; margin: 1em 0; text-align: center; }
        .katex-error { color: #cc0000; }

        /* FIX HUGE GAP: Remove top margins/padding from the very first content elements */
        body > div:first-child,
        .print-content > *:first-child {
            margin-top: 0;
            padding-top: 0;
        }

        /* FIX HUGE GAP: Collapse empty paragraphs and line breaks */
        p:empty,
        div:empty,
        br {
            display: none;
            height: 0;
            margin: 0;
            padding: 0;
            line-height: 0;
        }

        /* FIX HUGE GAP: Ensure the first heading/section doesn't have a massive top margin */
        h1:first-of-type,
        h2:first-of-type,
        h3:first-of-type,
        h4:first-of-type {
            margin-top: 0;
            padding-top: 0;
        }
    </style>
</head>

<body>

{{-- ── PDF Content ──────────────────────────────────────────────────────────── --}}
@php
    $paperSubjectName = $subjectExam->academicSubject->name ?? 'Subject Exam';
    $paperSectionCount = $subjectExam->sections->count();
    $paperQuestionCount = $subjectExam->sections->sum(fn($section) => $section->questions->count());
    $paperTotalMarks = number_format($subjectExam->sections->sum(fn($section) => $section->getTotalMarks()), 1);
    $paperDuration = $subjectExam->duration_in_minutes ?? null;
@endphp

<div class="print-content">
    {{-- Front Page if subject exam has template with front page config --}}
    @php
        $frontPageConfig = $subjectExam->template->front_page_config ?? null;
        $frontPageMode = $frontPageConfig['mode'] ?? (!empty($frontPageConfig['content']) ? 'editor' : null);
        $frontPagePdfImages = $frontPageMode === 'upload' ? $frontPageConfig['attachment_pdf_images'] ?? [] : [];
    @endphp

    @include('mock-exam.pdf.partials.identity-section', ['identity' => $identity, 'fontSize' => $fontSize ?? 11])

    @if ($frontPageMode === 'upload' && !empty($frontPagePdfImages))
        @foreach ($frontPagePdfImages as $imagePath)
            <div class="front-page-pdf">
                <img src="{{ $imagePath }}" class="front-page-pdf-img">
            </div>
        @endforeach
    @elseif($frontPageMode === 'editor' && !empty($frontPageConfig['content']))
        <div class="front-page">
            <div class="front-page-frame">
                <div class="front-page-content">
                    <div class="fp-block fp-richtext">
                        {!! $frontPageConfig['content'] !!}
                    </div>
                </div>
            </div>
        </div>
    @endif


    <div class="exam-container mt-0 pt-0">

        {{-- Sections and Questions --}}
        @php
            $sectionsLayoutConfig = $layoutConfig['sections'] ?? [];
        @endphp
        @foreach ($subjectExam->sections as $sectionIndex => $section)
            @php
                $sectionLayout = $sectionsLayoutConfig[$section->id] ?? [];
                $headerLayout = $sectionLayout['header'] ?? [];
                $instructionsLayout = $sectionLayout['instructions'] ?? [];
                $attachmentLayout = $sectionLayout['attachment'] ?? [];
                $blankPageLayout = $sectionLayout['blankPage'] ?? [];
            @endphp
            <div class="section-wrap {{ $sectionIndex >= 0 ? 'page-break' : '' }}">
                <div class="section-header"
                     @if($headerLayout) style="@php $s = ''; $s .= isset($headerLayout['textAlign']) ? 'text-align: ' . $headerLayout['textAlign'] . ';' : ''; $s .= isset($headerLayout['fontSize']) ? 'font-size: ' . $headerLayout['fontSize'] . ';' : ''; echo $s; @endphp" @endif>
                    <div class="section-title">
                        @if(!empty(trim($headerLayout['content'] ?? '')))
                            {!! $headerLayout['content'] !!}
                        @else
                            <x-ui.latex :content="$section->title" display="true"/>
                        @endif
                    </div>
{{--                    <div class="section-meta hidden">--}}
{{--                        {{ $section->questions->count() }} questions •--}}
{{--                        {{ number_format($section->getTotalMarks(), 1) }} marks--}}
{{--                        @if ($section->question_type !== 'mixed')--}}
{{--                            • {{ ucwords(str_replace('_', ' ', $section->question_type)) }}--}}
{{--                        @endif--}}
{{--                    </div>--}}
                </div>

                @if ($section->instructions)
                    <div class="section-instructions"
                         @if($instructionsLayout) style="@php $s = ''; $s .= isset($instructionsLayout['textAlign']) ? 'text-align: ' . $instructionsLayout['textAlign'] . ';' : ''; $s .= isset($instructionsLayout['fontSize']) ? 'font-size: ' . $instructionsLayout['fontSize'] . ';' : ''; echo $s; @endphp" @endif>
                        @if(!empty(trim($instructionsLayout['content'] ?? '')))
                            {!! $instructionsLayout['content'] !!}
                        @else
                            <x-ui.latex :content="$section->instructions" display="true" class="prose dark:prose-invert max-w-none"/>
                        @endif
                    </div>
                @endif

                @foreach ($section->questions as $question)
                    <div class="question-block">
                        <table class="question-header">
                            <tr>
                                <td class="question-number">{{ $loop->iteration }}.</td>
                                <td class="question-text">
                                    <x-ui.latex :content="$question->question_text" display="true" :pdf="true"/>
                                </td>
                            </tr>
                        </table>

                        @if (in_array($question->source_type, ['multiple_choice', 'true_false']) &&
                                is_array($question->options) &&
                                !empty($question->options))
                            @php
                                $choiceLayout = $layoutConfig['choiceLayout'] ?? 'vertical';
                            @endphp
                            <div class="options-wrap layout-{{ $choiceLayout }}">
                                @if ($choiceLayout === 'vertical')
                                    <table class="options-list">
                                        @foreach ($question->options as $option)
                                            <tr class="option-item">
                                                <td class="option-label">{{ chr(65 + $loop->index) }}.</td>
                                                <td class="option-text">
                                                    <x-ui.latex :content="$option" display="true" :pdf="true"/>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                @else
                                    @foreach ($question->options as $option)
                                        <div class="option-item">
                                            <span class="option-label">{{ chr(65 + $loop->index) }}.</span>
                                            <span class="option-text">
                                                <x-ui.latex :content="$option" display="true" :pdf="true"/>
                                            </span>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach

                {{-- Blank Page --}}
                @if($section->insert_blank_page)
                    <div class="blank-page-wrap">
                        <div class="blank-page-cell">
                            <h2 style="
                                {{ isset($blankPageLayout['fontSize']) ? 'font-size: ' . $blankPageLayout['fontSize'] . ';' : 'font-size: 16pt;' }}
                                font-weight: bold;
                                margin: 0;
                            ">
                                {!! isset($blankPageLayout['content']) && $blankPageLayout['content'] !== '' ? $blankPageLayout['content'] : ($section->blank_page_text ?: 'Do not turn the next page until you are told to do so') !!}
                            </h2>
                        </div>
                    </div>
                @endif

                {{-- Attachment --}}
                @if($section->hasAttachment())
                    @if($section->isTextAttachment())
                        <div class="page-break" style="white-space: pre-wrap; font-size: 10pt; line-height: 1.5; padding: 10px;">
                            {{ $section->attachment_text }}
                        </div>
                    @elseif($section->isPdfAttachment())
                        @foreach(($section->attachment_pdf_images ?? []) as $imagePath)
                            <div class="page-break" style="page-break-inside: avoid; {{ isset($attachmentLayout['align']) ? 'text-align: ' . $attachmentLayout['align'] : 'text-align: center;' }}">
                                <img src="{{ $imagePath }}" style="
                                    width: {{ isset($attachmentLayout['width']) ? $attachmentLayout['width'] : '100%'; }};
                                    max-height: 267mm;
                                ">
                            </div>
                        @endforeach
                    @elseif($section->isImageAttachment())
                        <div class="page-break" style="page-break-inside: avoid; {{ isset($attachmentLayout['align']) ? 'text-align: ' . $attachmentLayout['align'] : 'text-align: center;' }}">
                            <img src="{{ $section->attachment_image_path }}"
                                 style="max-width: 100%;
                                        max-height: 267mm;
                                        {{ isset($attachmentLayout['width']) ? 'width: ' . $attachmentLayout['width'] . ';' : '' }}
                                        {{ isset($attachmentLayout['height']) ? 'height: ' . $attachmentLayout['height'] . ';' : '' }}
                                 ">
                        </div>
                    @endif
                @endif
            </div>
        @endforeach
    </div>
</div>

</body>
</html>
