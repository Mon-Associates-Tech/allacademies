<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $title }} - Preview</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php
        $fontSize = 11;
        $paperSubjectName = $academicSubject->name ?? 'Subject Exam';
        $paperSectionCount = count($sections);
        $paperQuestionCount = collect($sections)->sum(fn($s) => count($s['questions'] ?? []));
        $paperTotalMarks = collect($sections)->sum(fn($s) => count($s['questions'] ?? []) * ($s['marks_per_question'] ?? 1));
        $paperDuration = $heading['duration'] ?? null;

        /* Build identity section from metadata */
        $identityRows = [];
        if (!empty($metadata['logo'])) {
            $identityRows[] = [['type' => 'image', 'value' => $metadata['logo'], 'font_size' => 14]];
        }
        if (!empty($metadata['group_name'])) {
            $identityRows[] = [['type' => 'text', 'value' => $metadata['group_name'], 'font_size' => 14, 'pretext' => null]];
        }
        if (!empty($metadata['level_name'])) {
            $identityRows[] = [['type' => 'text', 'value' => $metadata['level_name'], 'font_size' => 14, 'pretext' => null]];
        }
        if (!empty($metadata['subject_name'])) {
            $identityRows[] = [['type' => 'text', 'value' => $metadata['subject_name'], 'font_size' => 14, 'pretext' => null]];
        }
        $identity = !empty($identityRows) ? ['rows' => $identityRows] : null;
    @endphp

    <style>
        {!! file_get_contents(public_path('vendor/katex/katex.min.css')) !!}
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: {{ $fontSize }}pt;
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

            body {
                padding: 15mm;
            }
        }

        /* Header Section */
        .header-section {
            text-align: center;
            padding: 1rem 0 0.75rem;
            border-bottom: 2px solid #3b82f6;
            margin-bottom: 1.25rem;
        }

        .school-name {
            font-size: {{ ($fontSize) + 1 }}pt;
            font-weight: 600;
            color: #1f2937;
            letter-spacing: 0.025em;
            margin-bottom: 0.25rem;
        }

        .exam-main-title {
            font-size: {{ ($fontSize) + 3 }}pt;
            font-weight: 700;
            color: #1f2937;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.3rem;
        }

        .subject-title {
            font-size: {{ ($fontSize) + 1 }}pt;
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
            font-size: {{ $fontSize }}pt;
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
            font-size: {{ ($fontSize) - 0.5 }}pt;
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
            font-size: {{ ($fontSize) + 1 }}pt;
            font-weight: 600;
            color: #1f2937;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .section-meta {
            font-size: {{ ($fontSize) - 1 }}pt;
            color: #6b7280;
            margin-top: 0.25rem;
        }

        .section-instructions {
            font-size: {{ ($fontSize) - 0.5 }}pt;
            color: #4b5563;
            font-style: italic;
            margin-bottom: 0.75rem;
            padding: 0.5rem;
            border-left: 2px solid #f59e0b;
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
            font-size: {{ $fontSize }}pt;
            color: #1f2937;
            line-height: 1.6;
        }

        .question-marks {
            width: 60px;
            text-align: right;
            font-size: {{ ($fontSize) - 1 }}pt;
            color: #6b7280;
            font-weight: 600;
            white-space: nowrap;
        }

        .options-wrap {
            margin-left: 24px;
        }

        .option-item {
            page-break-inside: avoid;
        }

        .option-label {
            width: 24px;
            font-weight: 600;
            color: black;
            padding-bottom: 4pt;
        }

        .option-text {
            font-size: {{ ($fontSize) - 0.5 }}pt;
            color: #374151;
            line-height: 1.5;
            padding-bottom: 4pt;
        }

        /* Neutralise the block elements the markdown component emits */
        .question-text p,
        .option-text p,
        .question-text>div,
        .option-text>div {
            margin: 0;
            padding: 0;
        }

        .question-text p+p {
            margin-top: 4pt;
        }

        /* Page break helpers */
        .page-break {
            page-break-before: always;
        }

        .prose-inline .katex-display {
            display: inline-block;
            margin: 0;
        }

        /* ─── Front Page (Cover) ─── */
        .front-page {
            font-family: 'Georgia', 'Times New Roman', serif;
            color: #1a1a1a;
        }

        .front-page-frame {
            display: table;
            width: 100%;
            height: 267mm;
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
            font-size: {{ ($fontSize) + 9 }}pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #111827;
            line-height: 1.35;
            padding-bottom: 6mm;
        }

        .fp-heading-h2 {
            font-size: {{ ($fontSize) + 5 }}pt;
            font-weight: 600;
            color: #1f2937;
            letter-spacing: 0.02em;
            line-height: 1.4;
        }

        .fp-heading-h3 {
            font-size: {{ ($fontSize) + 1 }}pt;
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
            font-size: {{ $fontSize }}pt;
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

        /* Identity Section */
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
            font-size: {{ ($fontSize) + 1 }}pt;
            font-weight: 600;
            margin-bottom: 1.5mm;
        }

        .identity-pretext {
            font-weight: 400;
            color: #333;
        }

        /* Screen-only header */
        @media print {
            .screen-only-header {
                display: none !important;
            }
        }

        /* Confirm bar (screen only) */
        @media print {
            .confirm-bar {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    {{-- ── SCREEN-ONLY HEADER / PRINT ACTION BAR ───────── --}}
    <header class="screen-only-header print:hidden sticky top-0 z-50 border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur">
        <div class="mx-auto w-full max-w-5xl px-4 py-4 lg:px-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                {{-- Left side: document identity --}}
                <div class="min-w-0">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-900 text-white shadow-sm">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-500">
                                Custom Exam Preview
                            </p>

                            <h1 class="truncate text-base font-bold text-slate-900 sm:text-lg" title="{{ $title }}">
                                {{ $title }}
                            </h1>

                            <p class="truncate text-xs text-slate-500 sm:text-sm">
                                {{ $paperSubjectName }}
                            </p>
                        </div>
                    </div>

                    {{-- Meta chips --}}
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-50 px-2.5 py-1 text-[11px] font-medium text-slate-700">
                            {{ $paperSubjectName }}
                        </span>

                        <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-50 px-2.5 py-1 text-[11px] font-medium text-slate-700">
                            {{ $paperSectionCount }} Section{{ $paperSectionCount !== 1 ? 's' : '' }}
                        </span>

                        <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-50 px-2.5 py-1 text-[11px] font-medium text-slate-700">
                            {{ $paperQuestionCount }} Question{{ $paperQuestionCount !== 1 ? 's' : '' }}
                        </span>

                        <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-50 px-2.5 py-1 text-[11px] font-medium text-slate-700">
                            {{ number_format($paperTotalMarks, 1) }} Marks
                        </span>

                        @if ($paperDuration)
                            <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-50 px-2.5 py-1 text-[11px] font-medium text-slate-700">
                                {{ $paperDuration }} Minutes
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Right side: actions --}}
                <div class="flex shrink-0 flex-wrap items-center gap-3">
                    <div class="hidden text-right lg:block">
                        <p class="text-xs font-medium text-slate-700">
                            Print-ready exam paper
                        </p>
                        <p class="text-[11px] text-slate-400">
                            Action buttons are hidden when printing.
                        </p>
                    </div>

                    <x-exam.button type="button" onclick="window.print()">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print Exam
                    </x-exam.button>
                </div>
            </div>
        </div>
    </header>

    <div class="print-content">

        {{-- Identity Section --}}
        @if($identity && !empty($identity['rows']))
            <div class="identity-section">
                @foreach ($identity['rows'] as $row)
                    <div class="identity-row">
                        @foreach ($row as $item)
                            <div class="identity-row-cell">
                                @if ($item['type'] === 'image')
                                    <img alt="logo" src="{{ $item['value'] }}" class="identity-logo">
                                @else
                                    <div class="identity-line" style="font-size: {{ $item['font_size'] ?? 14 }}pt;">
                                        @if ($item['pretext'])
                                            <span class="identity-pretext">
                                                {!! $item['pretext'] !!}
                                            </span>
                                        @endif{{ $item['value'] }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Front Page from heading template --}}
        @if (!empty($heading['down']))
            <div class="front-page">
                <div class="front-page-frame">
                    <div class="front-page-content">
                        <div class="fp-block fp-richtext">
                            {!! $heading['down'] !!}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="exam-container mt-0 pt-0">

            {{-- Sections and Questions --}}
            @foreach ($sections as $sectionIndex => $section)
                <div class="section-wrap {{ $sectionIndex > 0 ? 'page-break' : '' }}">
                    <div class="section-header">
                        <div class="section-title">
                            <x-ui.latex :content="$section['name']" display="true" />
                        </div>
                        <div class="section-meta hidden">
                            {{ count($section['questions'] ?? []) }} questions •
                            {{ number_format(count($section['questions'] ?? []) * ($section['marks_per_question'] ?? 1), 1) }} marks
                        </div>
                    </div>

                    @if (isset($section['instructions']))
                        <div class="section-instructions">
                            @if (is_array($section['instructions']) && isset($section['instructions']['down']))
                                <x-ui.latex :content="$section['instructions']['down']" display="true" class="prose dark:prose-invert max-w-none" />
                            @else
                                <x-ui.latex :content="$section['instructions']" display="true" class="prose dark:prose-invert max-w-none" />
                            @endif
                        </div>
                    @endif

                    @if (isset($section['questions']) && is_countable($section['questions']))
                        @foreach ($section['questions'] as $question)
                            <div class="question-block">
                                <table class="question-header">
                                    <tr>
                                        <td class="question-number">{{ $loop->iteration }}.</td>
                                        <td class="question-text">
                                            @if (is_array($question) && isset($question['question']['down']))
                                                <x-ui.latex :content="$question['question']['down']" display="true" :pdf="true" />
                                            @elseif (is_object($question) && isset($question->question_text))
                                                <x-ui.latex :content="$question->question_text" display="true" :pdf="true" />
                                            @else
                                                <x-ui.latex :content="$question['question_text'] ?? ''" display="true" :pdf="true" />
                                            @endif
                                        </td>
                                    </tr>
                                </table>

                                @if (is_array($question) && isset($question['options']) && !empty($question['options']))
                                    <div class="options-wrap">
                                        <table class="options-list">
                                            @foreach ($question['options'] as $optionKey => $option)
                                                @if (!empty($option))
                                                    <tr class="option-item">
                                                        <td class="option-label">{{ is_numeric($optionKey) ? chr(65 + $loop->index) : $optionKey }}.</td>
                                                        <td class="option-text">
                                                            <x-ui.latex :content="$option" display="true" :pdf="true" />
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </table>
                                    </div>
                                @elseif (is_object($question) && isset($question->options) && is_array($question->options) && !empty($question->options))
                                    <div class="options-wrap">
                                        <table class="options-list">
                                            @foreach ($question->options as $option)
                                                <tr class="option-item">
                                                    <td class="option-label">{{ chr(65 + $loop->index) }}.</td>
                                                    <td class="option-text">
                                                        <x-ui.latex :content="$option" display="true" :pdf="true" />
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endif

                    {{-- Page break / blank page --}}
                    @if (isset($section['page']))
                        <div class="page-break">
                            <div class="h-screen w-full flex items-center justify-center">
                                <div class="text-center">
                                    <h2 style="font-size: 20pt; font-weight: bold">Do not turn the next page until you are told to do so</h2>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Attachments --}}
                    @if (isset($section['attachment_text']) && !empty($section['attachment_text']))
                        <div class="page-break" style="white-space: pre-wrap; font-size: 10pt; line-height: 1.5; padding: 10px;">
                            {{ $section['attachment_text'] }}
                        </div>
                    @endif
                    @if (isset($section['attachment_pdf_images']) && !empty($section['attachment_pdf_images']))
                        @foreach($section['attachment_pdf_images'] as $imagePath)
                            <div class="page-break">
                                <img src="{{ asset('storage/'.$imagePath) }}" style="width: 100%;">
                            </div>
                        @endforeach
                    @endif
                    @if (isset($section['attachment_image_path']) && !empty($section['attachment_image_path']))
                        <div class="page-break" style="text-align: center;">
                            <img src="{{ asset('storage/'.$section['attachment_image_path']) }}" style="max-width: 100%;">
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Confirm / Go Back Bar --}}
    <div class="confirm-bar sticky bottom-0 z-50 border-t border-slate-200 bg-white/95 shadow-sm backdrop-blur">
        <div class="mx-auto w-full max-w-5xl px-4 py-4 lg:px-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <a href="{{ route('mock-exams.generate.custom.configure', ['academicSubject' => $academicSubject]) }}"
                   class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                    Back to Configuration
                </a>

                <div class="flex items-center justify-center gap-4">
                    <a href="{{ route('mock-exams.generate.custom.configure', ['academicSubject' => $academicSubject]) }}"
                       class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300"
                       style="border-radius: 2px;">
                        ← Go Back and Edit
                    </a>
                    <form method="POST"
                          action="{{ route('mock-exams.generate.custom.confirm', ['academicSubject' => $academicSubject]) }}"
                          class="inline">
                        @csrf
                        <input type="hidden" name="team_id" value="{{ $previewData['team_id'] ?? '' }}">
                        <input type="hidden" name="creator_id" value="{{ $previewData['creator_id'] ?? '' }}">
                        <button type="submit"
                                class="px-6 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition"
                                style="border-radius: 2px;">
                            Confirm and Generate Exam
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function goBackFromPrintPage(fallbackUrl) {
            let hasSameSiteHistory = false;

            try {
                if (document.referrer) {
                    const referrerUrl = new URL(document.referrer);
                    hasSameSiteHistory = referrerUrl.origin === window.location.origin;
                }
            } catch (error) {
                hasSameSiteHistory = false;
            }

            if (window.history.length > 1 && hasSameSiteHistory) {
                window.history.back();
                return;
            }

            window.location.href = fallbackUrl;
        }
    </script>
</body>

<style>
    @media print {
        @page {
            margin: 15mm;
            size: auto;
        }

        /* 1. Reset root elements to flow naturally */
        html,
        body {
            height: auto !important;
            min-height: auto !important;
            max-height: none !important;
            overflow: visible !important;
            background: #fff !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* 2. Kill Tailwind's height/overflow classes that cause blank pages */
        main,
        #app,
        [id^="app"],
        [id^="root"],
        .min-h-screen,
        .h-screen,
        .h-full,
        .min-h-full,
        .max-h-screen,
        .flex-1,
        .flex-grow,
        .overflow-hidden,
        .overflow-y-auto,
        .overflow-auto,
        [class*="h-["],
        [class*="min-h-["],
        [class*="max-h-["] {
            height: auto !important;
            min-height: auto !important;
            max-height: none !important;
            overflow: visible !important;
        }

        /* 3. Reset all page breaks to auto (prevents accidental forced breaks) */
        * {
            page-break-after: auto !important;
            break-after: auto !important;
            page-break-before: auto !important;
            break-before: auto !important;
            page-break-inside: auto !important;
            break-inside: auto !important;
        }

        /* 4. Hide UI chrome */
        nav,
        aside,
        .sidebar,
        .no-print,
        .print\:hidden,
        button,
        [onclick*="print"] {
            display: none !important;
        }

        /* 5. FIX HUGE GAP: Remove top margins/padding from the very first content elements */
        body>div:first-child,
        main>div:first-child,
        .print-content>*:first-child {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }

        /* 6. FIX HUGE GAP: Collapse empty paragraphs and line breaks often left by Rich Text Editors */
        p:empty,
        div:empty,
        br {
            display: none !important;
            height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            line-height: 0 !important;
        }

        /* 7. FIX HUGE GAP: Ensure the first heading/section doesn't have a massive top margin */
        h1:first-of-type,
        h2:first-of-type,
        h3:first-of-type,
        h4:first-of-type {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }
    }
</style>

</html>
