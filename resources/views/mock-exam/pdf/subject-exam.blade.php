<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $subjectExam->getDisplayTitle() }} - {{ $subjectExam->mockExam->title }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        {!! file_get_contents(public_path('vendor/katex/katex.min.css')) !!} * {
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
            /*border-bottom: 1.5px solid #3b82f6;*/
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
            /*background-color: #fef3c7;*/
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
            font-size: {{ $fontSize ?? 11 }}pt;
            color: #1f2937;
            line-height: 1.6;
        }

        .question-marks {
            width: 60px;
            text-align: right;
            font-size: {{ ($fontSize ?? 11) - 1 }}pt;
            color: #6b7280;
            font-weight: 600;
            white-space: nowrap;
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
            break-before: page;
        }

        /* Blank page — exactly one page, centered text */
        .blank-page-wrap {
            page-break-before: always;
            page-break-inside: avoid;
            break-before: page;
            break-inside: avoid;
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
            break-after: page;
            break-inside: avoid;
            font-family: 'Georgia', 'Times New Roman', serif;
            color: #1a1a1a;
        }

        .front-page-frame {
            display: table;
            width: 100%;
            height: 267mm;
            /*border: 1px solid #cbd5e1;*/
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
            /*border-bottom: 2px solid #1f2937;*/
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
            /*border-bottom: 1px solid #d1d5db;*/
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
            /*border-bottom: 1px solid #9ca3af;*/
            min-height: 5mm;
            padding-bottom: 1mm;
        }

        +
            /* Uploaded PDF front page (mode: upload) — each source page rendered as-is */
        .front-page-pdf {
            page-break-after: always;
            page-break-inside: avoid;
            break-after: page;
            break-inside: avoid;
            text-align: center;
        }

        .front-page-pdf-img {
            width: 180mm;
            max-height: 267mm;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

    </style>
</head>

<body>

{{-- ── SCREEN-ONLY HEADER / PRINT ACTION BAR (Hidden during print) ───────── --}}
@php
    $paperSubjectName = $subjectExam->academicSubject->name ?? 'Subject Exam';
    $paperSectionCount = $subjectExam->sections->count();
    $paperQuestionCount = $subjectExam->sections->sum(fn($section) => $section->questions->count());
    $paperTotalMarks = number_format($subjectExam->sections->sum(fn($section) => $section->getTotalMarks()), 1);
    $paperDuration = $subjectExam->duration_in_minutes ?? null;
@endphp

@php
    $user = auth()->user();

    $isOwnerOrAdmin = false;

    if ($user) {
        if (method_exists($user, 'hasAnyRole')) {
            $isOwnerOrAdmin = $user->hasAnyRole(['owner', 'admin']);
        } elseif (method_exists($user, 'hasRole')) {
            $isOwnerOrAdmin = $user->hasRole('owner') || $user->hasRole('admin');
        } elseif (isset($user->role)) {
            $isOwnerOrAdmin = in_array(strtolower((string) $user->role), ['owner', 'admin'], true);
        }
    }

    $candidateDashboardUrl = \Illuminate\Support\Facades\Route::has('mock-exams.subscriptions.dashboard')
        ? route('mock-exams.subscriptions.dashboard')
        : (\Illuminate\Support\Facades\Route::has('mock-exam.subscriptions.dashboard')
            ? route('mock-exam.subscriptions.dashboard')
            : url('/'));

    $adminBackUrl =
        $subjectExam->mockExam && \Illuminate\Support\Facades\Route::has('mock-exams.show')
            ? route('mock-exams.show', $subjectExam->mockExam)
            : $candidateDashboardUrl;

    $backUrl = $isOwnerOrAdmin ? $adminBackUrl : $candidateDashboardUrl;
@endphp

<style>
    @media print {
        .screen-only-header {
            display: none !important;
        }
    }
</style>

<header
    class="screen-only-header print:hidden sticky top-0 z-50 border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur">
    <div class="mx-auto w-full max-w-5xl px-4 py-4 lg:px-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            {{-- Left side: document identity --}}
            <div class="min-w-0">
                <div class="flex items-start gap-3">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-900 text-white shadow-sm">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-500">
                            Mock Exam Paper
                        </p>

                        <h1 class="truncate text-base font-bold text-slate-900 sm:text-lg"
                            title="{{ $subjectExam->getDisplayTitle() }} — {{ $subjectExam->mockExam->title }}">
                            {{ $subjectExam->getDisplayTitle() }}
                        </h1>

                        <p class="truncate text-xs text-slate-500 sm:text-sm">
                            {{ $subjectExam->mockExam->title }}
                        </p>
                    </div>
                </div>

                {{-- Meta chips --}}
                <div class="mt-3 flex flex-wrap items-center gap-2">
                        <span
                            class="inline-flex items-center rounded-md border border-slate-200 bg-slate-50 px-2.5 py-1 text-[11px] font-medium text-slate-700">
                            {{ $paperSubjectName }}
                        </span>

                    <span
                        class="inline-flex items-center rounded-md border border-slate-200 bg-slate-50 px-2.5 py-1 text-[11px] font-medium text-slate-700">
                            {{ $paperSectionCount }} Section{{ $paperSectionCount !== 1 ? 's' : '' }}
                        </span>

                    <span
                        class="inline-flex items-center rounded-md border border-slate-200 bg-slate-50 px-2.5 py-1 text-[11px] font-medium text-slate-700">
                            {{ $paperQuestionCount }} Question{{ $paperQuestionCount !== 1 ? 's' : '' }}
                        </span>

                    <span
                        class="inline-flex items-center rounded-md border border-slate-200 bg-slate-50 px-2.5 py-1 text-[11px] font-medium text-slate-700">
                            {{ $paperTotalMarks }} Marks
                        </span>

                    @if ($paperDuration)
                        <span
                            class="inline-flex items-center rounded-md border border-slate-200 bg-slate-50 px-2.5 py-1 text-[11px] font-medium text-slate-700">
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


                <x-exam.button
                    type="button"
                    variant="primary"
                    clientLoading
                    data-download-url="{{ route('mock-exams.subject-exams.pdf.download', [$subjectExam->mockExam, $subjectExam]) }}"
                    loading="Generating PDF..."
                    class="print:hidden">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download PDF
                </x-exam.button>
            </div>
        </div>
    </div>
</header>

<div class="print-content">
    {{-- Front Page if subject exam has template with front page config —
     'upload' mode renders the uploaded PDF's own pages as-is; 'editor'
     mode (or older templates saved before this field existed) falls
     back to the rich text content inside the standard header frame. --}}
    @php
        $frontPageConfig = $subjectExam->template->front_page_config ?? null;
        $frontPageMode = $frontPageConfig['mode'] ?? (!empty($frontPageConfig['content']) ? 'editor' : null);
        $frontPagePdfImages = $frontPageMode === 'upload' ? $frontPageConfig['attachment_pdf_images'] ?? [] : [];
    @endphp

    @include('mock-exam.pdf.partials.identity-section', ['identity' => $identity, 'fontSize' => $fontSize ?? 11])

    @if ($frontPageMode === 'upload' && !empty($frontPagePdfImages))
        @foreach ($frontPagePdfImages as $imagePath)
            <div class="front-page-pdf">
                <img src="{{ asset('storage/' . $imagePath) }}" class="front-page-pdf-img">
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
                                {{--                            <td class="question-marks"> --}}
                                {{--                                [{{ $question->marks }} mark{{ $question->marks != 1 ? 's' : '' }}] --}}
                                {{--                            </td> --}}
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
                    <div class="blank-page-wrap" style="
                         {{ isset($blankPageLayout['textAlign']) ? 'text-align: ' . $blankPageLayout['textAlign'] . ';' : 'text-align: center;' }}
                    ">
                        <div class="blank-page-cell" style="
                             {{ isset($blankPageLayout['verticalAlign']) ? 'vertical-align: ' . $blankPageLayout['verticalAlign'] . ';' : 'vertical-align: middle;' }}
                        ">
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
                            <div class="page-break" style="page-break-inside: avoid; break-inside: avoid; {{ isset($attachmentLayout['align']) ? 'text-align: ' . $attachmentLayout['align'] : 'text-align: center;' }}">
                                <img src="{{ asset('storage/'.$imagePath) }}" style="
                                    width: {{ isset($attachmentLayout['width']) ? $attachmentLayout['width'] : '100%'; }};
                                    max-height: 267mm;
                                    object-fit: contain;
                                ">
                            </div>
                        @endforeach
                    @elseif($section->isImageAttachment())
                        <div class="page-break" style="page-break-inside: avoid; break-inside: avoid; {{ isset($attachmentLayout['align']) ? 'text-align: ' . $attachmentLayout['align'] : 'text-align: center;' }}">
                            <img src="{{ asset('storage/'.$section->attachment_image_path) }}"
                                 style="max-width: 100%;
                                        max-height: 267mm;
                                        object-fit: contain;
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

    // proseMathRenderer for prose-content component (needed in iframe preview)
    window.mathRenderConfig = {
        delimiters: [
            {left: '$$', right: '$$', display: true},
            {left: '$', right: '$', display: false},
            {left: '\\[', right: '\\]', display: true},
            {left: '\\(', right: '\\)', display: false}
        ],
        throwOnError: false,
        errorColor: '#cc0000',
        strict: false,
        trust: true
    };

    function proseMathRenderer(markdownContent, htmlContent, isInline) {
        return {
            initRenderer() {
                this.$nextTick(() => { this.renderContent(); });
            },
            renderContent() {
                if (markdownContent && !htmlContent) {
                    if (typeof window.renderMarkdownWithMath === 'function') {
                        this.$el.innerHTML = window.renderMarkdownWithMath(markdownContent);
                    } else {
                        // Fallback: render plain text without markdown processing
                        this.$el.classList.remove('hidden');
                        this.$el.textContent = markdownContent;
                    }
                }
                if (this.$el && this.$el.innerHTML) {
                    let inner = this.$el.innerHTML;
                    inner = inner.replace(/`(\$\$[\s\S]+?\$\$)`/g, '$1')
                                 .replace(/`(\\\[[\s\S]+?\\\])`/g, '$1')
                                 .replace(/`(\\\([\s\S]+?\\\))`/g, '$1')
                                 .replace(/`(\$[^`$\n]+?\$)`/g, '$1')
                                 .replace(/`([^`$\n\\][^`\n]*)`/g, '$$1$')
                                 .replace(/`([^`\n])`/g, '$$1$');
                    this.$el.innerHTML = inner;
                }
                if (isInline) {
                    this.$el.querySelectorAll('p, h1, h2, h3, h4, h5, h6, div').forEach((el) => {
                        while (el.firstChild) el.parentNode.insertBefore(el.firstChild, el);
                        el.remove();
                    });
                }
                if (typeof window.renderMathInElement !== 'undefined') {
                    try {
                        window.renderMathInElement(this.$el, window.mathRenderConfig);
                    } catch (e) {
                        console.warn('KaTeX rendering error:', e);
                    }
                }
            }
        };
    }

    // Client loading buttons — show loading spinner before triggering download
    (function() {
        document.querySelectorAll('[data-client-loading="true"]').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const url = btn.dataset.downloadUrl;
                if (!url) return;

                // Show loading state
                const defaultEl = btn.querySelector('.client-btn-default');
                const loadingEl = btn.querySelector('.client-btn-loading');
                if (defaultEl) defaultEl.classList.add('hidden');
                if (loadingEl) loadingEl.classList.remove('hidden');
                btn.disabled = true;

                // Trigger download via hidden anchor so the page doesn't navigate away
                setTimeout(function() {
                    const a = document.createElement('a');
                    a.href = url;
                    a.style.display = 'none';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);

                    // Reset button state after a short delay
                    setTimeout(function() {
                        if (defaultEl) defaultEl.classList.remove('hidden');
                        if (loadingEl) loadingEl.classList.add('hidden');
                        btn.disabled = false;
                    }, 1000);
                }, 100);
            });
        });
    })();
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
        body > div:first-child,
        main > div:first-child,
        .print-content > *:first-child {
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
