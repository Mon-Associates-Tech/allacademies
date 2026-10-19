<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $subjectExam->getDisplayTitle() }} - Layout Editor</title>

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/layout-editor.js'])

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
            max-width: 60rem;
            margin: 0 auto;
            padding: 0 1rem;
            background: white;
        }

        /* ─── Edit Mode Styles ─── */

        /* Section wrapper with drag handle */
        .section-wrap[data-section-id] {
            position: relative;
            margin-top: 1.5rem;
            border: 2px solid transparent;
            transition: border-color 0.2s;
        }

        .edit-mode .section-wrap[data-section-id] {
            border: 2px dashed #e5e7eb;
            margin-top: 1rem;
        }

        .edit-mode .section-wrap[data-section-id]:hover {
            border-color: #93c5fd;
        }

        .edit-mode .section-wrap[data-section-id].dragging {
            border-color: #3b82f6;
            background: #eff6ff;
        }

        .edit-mode .section-wrap[data-section-id].drag-over {
            border-color: #3b82f6;
            background: #dbeafe;
        }

        /* Drag handle */
        .section-drag-handle {
            position: absolute;
            top: 8px;
            left: -32px;
            width: 24px;
            height: 24px;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: grab;
            color: #9ca3af;
            border-radius: 4px;
            transition: all 0.15s;
            z-index: 10;
        }

        .section-drag-handle:hover {
            color: #3b82f6;
            background: #eff6ff;
        }

        .section-drag-handle:active {
            cursor: grabbing;
        }

        .edit-mode .section-drag-handle {
            display: flex;
        }

        /* Editable elements */
        .edit-mode [data-editable] {
            outline: 2px solid transparent;
            outline-offset: 2px;
            transition: outline-color 0.15s;
            cursor: text;
        }

        .edit-mode [data-editable]:hover {
            outline-color: #bfdbfe;
        }

        .edit-mode [data-editable]:focus {
            outline-color: #3b82f6;
        }

        /* Edit mode toolbar */
        .edit-mode-toolbar {
            position: sticky;
            top: 0;
            z-index: 100;
            display: none;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 8px 16px;
            background: #1e293b;
            border-bottom: 1px solid #334155;
        }

        .edit-mode .edit-mode-toolbar {
            display: flex;
        }

        .edit-mode-toolbar-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .edit-mode-toolbar-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .edit-mode-toolbar button {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s;
        }

        .edit-mode-toolbar .btn-save {
            background: #3b82f6;
            color: white;
        }

        .edit-mode-toolbar .btn-save:hover {
            background: #2563eb;
        }

        .edit-mode-toolbar .btn-save:disabled {
            background: #6b7280;
            cursor: not-allowed;
        }

        .edit-mode-toolbar .btn-exit {
            background: #374151;
            color: #d1d5db;
        }

        .edit-mode-toolbar .btn-exit:hover {
            background: #4b5563;
        }

        .edit-mode-toolbar .save-status {
            font-size: 11px;
            color: #9ca3af;
        }

        .edit-mode-toolbar .save-status.saving {
            color: #fbbf24;
        }

        .edit-mode-toolbar .save-status.saved {
            color: #34d399;
        }

        .edit-mode-toolbar .save-status.error {
            color: #f87171;
        }

        /* Image resize handle */
        .image-resize-handle {
            position: absolute;
            bottom: -4px;
            right: -4px;
            width: 12px;
            height: 12px;
            background: #3b82f6;
            border: 2px solid white;
            border-radius: 2px;
            cursor: nwse-resize;
            z-index: 100;
        }

        /* Blank page editing */
        .blank-page {
            min-height: 267mm;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            page-break-before: always;
            page-break-after: always;
        }

        .blank-page-text {
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
            padding: 2rem;
        }

        /* Attachment editing */
        .section-attachment {
            margin-top: 1rem;
            text-align: center;
        }

        .section-attachment img {
            max-width: 100%;
            transition: all 0.15s;
        }

        /* Questions area */
        .questions-area {
            margin-top: 0.75rem;
        }

        /* Floating toolbar styles */
        .floating-toolbar {
            position: absolute;
            display: none;
            gap: 2px;
            padding: 4px;
            background: #1e293b;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 10000;
            align-items: center;
        }

        .floating-toolbar button {
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            background: transparent;
            color: #cbd5e1;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.15s;
        }

        .floating-toolbar button:hover {
            background: #334155;
            color: #f1f5f9;
        }

        .floating-toolbar .separator {
            width: 1px;
            height: 20px;
            background: #475569;
            margin: 0 2px;
        }

        .floating-toolbar .font-size-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            display: none;
            flex-direction: column;
            padding: 4px;
            background: #1e293b;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 10001;
            min-width: 80px;
        }

        .floating-toolbar .font-size-dropdown button {
            width: auto;
            height: auto;
            padding: 4px 8px;
            text-align: left;
            font-size: 12px;
        }

        /* Print styles - hide edit chrome */
        @media print {
            .edit-mode-toolbar,
            .section-drag-handle,
            .image-resize-handle {
                display: none !important;
            }

            .section-wrap[data-section-id] {
                border: none !important;
            }

            [data-editable] {
                outline: none !important;
                cursor: default !important;
            }
        }

        /* Base styles matching subject-exam.blade.php */
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

        .info-item { flex: 1; }

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

        .section-header {
            padding: 0.5rem 0;
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

        .question-header { margin-bottom: 6pt; }

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

        .options-wrap { margin-left: 24px; }

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

        .question-text p,
        .option-text p,
        .question-text > div,
        .option-text > div {
            margin: 0;
            padding: 0;
        }

        .question-text p + p { margin-top: 4pt; }

        .page-break { page-break-before: always; }

        /* Blank page — exactly one page, centered text */
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

        /* Front page styles */
        .front-page {
            page-break-after: always;
            page-break-inside: avoid;
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

        .fp-block { margin-bottom: 10mm; }
        .fp-block:last-child { margin-bottom: 0; }

        .fp-richtext {
            max-width: 130mm;
            margin: 0 auto;
            text-align: left;
            font-size: {{ $fontSize ?? 11 }}pt;
            line-height: 1.7;
            color: #374151;
        }

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
    </style>
</head>

<body>
    {{-- Edit Mode Toolbar --}}
    <div class="edit-mode-toolbar">
        <div class="edit-mode-toolbar-left">
            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            <span class="text-xs text-slate-300">Layout Editor</span>
        </div>
        <div class="edit-mode-toolbar-right">
            <span id="saveStatus" class="save-status"></span>
            <button id="btnSaveLayout" class="btn-save" onclick="saveLayout()">
                Save Layout
            </button>
            <button class="btn-exit" onclick="exitEditMode()">
                Exit Edit Mode
            </button>
        </div>
    </div>

    <div id="examContainer" class="exam-container edit-mode" data-edit-mode="true">

        {{-- Front Page --}}
        @php
            $frontPageConfig = $subjectExam->template->front_page_config ?? null;
            $frontPageMode = $frontPageConfig['mode'] ?? (!empty($frontPageConfig['content']) ? 'editor' : null);
            $frontPagePdfImages = $frontPageMode === 'upload' ? $frontPageConfig['attachment_pdf_images'] ?? [] : [];
        @endphp

        @if ($frontPageMode === 'upload' && !empty($frontPagePdfImages))
            @foreach ($frontPagePdfImages as $imagePath)
                <div class="front-page-pdf">
                    <img src="{{ asset('storage/' . $imagePath) }}" class="front-page-pdf-img" style="max-height: 267mm;">
                </div>
            @endforeach
        @elseif($frontPageMode === 'editor' && !empty($frontPageConfig['content']))
            <div class="front-page">
                <div class="front-page-frame">
                    <div class="front-page-content">
                        <div class="fp-block fp-richtext" data-editable="frontPage" data-section-id="front" contenteditable="true">
                            {!! $frontPageConfig['content'] !!}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Identity Section --}}
        @include('mock-exam.pdf.partials.identity-section', ['identity' => $identity ?? null, 'fontSize' => $fontSize ?? 11])

        {{-- Sections and Questions --}}
        @foreach ($subjectExam->sections as $sectionIndex => $section)
            <div class="section-wrap {{ $sectionIndex >= 0 ? 'page-break' : '' }}"
                 data-section-id="{{ $section->id }}"
                 data-order="{{ $sectionIndex + 1 }}">

                {{-- Drag Handle --}}
                <div class="section-drag-handle" title="Drag to reorder">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 8h16M4 16h16"/>
                    </svg>
                </div>

                {{-- Section Header --}}
                <div class="section-header" data-editable="header">
                    <div class="section-title" contenteditable="true">
                        {{ $section->title }}
                    </div>
                    <div class="section-meta hidden">
                        {{ $section->questions->count() }} questions •
                        {{ number_format($section->getTotalMarks(), 1) }} marks
                        @if ($section->question_type !== 'mixed')
                            • {{ ucwords(str_replace('_', ' ', $section->question_type)) }}
                        @endif
                    </div>
                </div>

                {{-- Section Instructions --}}
                @if ($section->instructions)
                    <div class="section-instructions" data-editable="instructions">
                        <div class="instructions-content" contenteditable="true">
                            {{ $section->instructions }}
                        </div>
                    </div>
                @endif

                {{-- Questions (read-only in layout editor) --}}
                <div class="questions-area" data-editable="questions" data-columns="1">
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
                </div>

                {{-- Blank Page --}}
                @if($section->insert_blank_page)
                    <div class="blank-page-wrap" data-editable="blankPage">
                        <div class="blank-page-cell">
                            <h2 contenteditable="true" style="font-size: 16pt; font-weight: bold; margin: 0;">
                                {{ $section->blank_page_text ?: 'Do not turn the next page until you are told to do so' }}
                            </h2>
                        </div>
                    </div>
                @endif

                {{-- Attachment --}}
                @if($section->hasAttachment())
                    <div class="section-attachment" data-editable="attachment">
                        @if($section->isTextAttachment())
                            <div class="page-break" style="white-space: pre-wrap; font-size: 10pt; line-height: 1.5; padding: 10px;" contenteditable="true">
                                <p>{{ $section->attachment_text }}</p>
                            </div>
                        @elseif($section->isPdfAttachment())
                            @foreach(($section->attachment_pdf_images ?? []) as $imagePath)
                                <div class="page-break" style="page-break-inside: avoid;">
                                    <img src="{{ asset('storage/'.$imagePath) }}" style="width: 100%; max-height: 267mm;" data-resizable="true">
                                </div>
                            @endforeach
                        @elseif($section->isImageAttachment())
                            <div class="page-break" style="text-align: center; page-break-inside: avoid;">
                                <img src="{{ asset('storage/'.$section->attachment_image_path) }}"
                                     style="max-width: 100%; max-height: 267mm;"
                                     data-resizable="true">
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Scripts --}}
    <script>
        const saveLayoutUrl = '{{ route('mock-exams.subject-exams.layout.save', [$subjectExam->mockExam, $subjectExam]) }}';
        const previewRoute = '{{ isset($fromSubscription) && $fromSubscription
            ? route('mock-exams.subject-exams.subscription-preview.content', [$subjectExam->mockExam, $subjectExam])
            : route('mock-exams.subject-exams.pdf.preview', [$subjectExam->mockExam, $subjectExam]) }}';

        // Initialize layout editor
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('examContainer');

            // Initialize drag-and-drop
            if (window.LayoutEditor) {
                window.LayoutEditor.initSectionDragDrop(container);
                window.LayoutEditor.initImageResize(container);

                // Apply existing layout config
                @if($subjectExam->layout_config)
                    window.LayoutEditor.applyLayoutConfig(container, @json($subjectExam->layout_config));
                @endif
            } else {
                console.warn('[Editable] LayoutEditor NOT found on window');
            }

            // Make all editable elements actually contenteditable
            // This ensures contenteditable is set even if the template cache is stale
            container.querySelectorAll('[data-editable]').forEach(el => {
                // Find child elements that should be editable (leaf text elements)
                const editableChildren = el.querySelectorAll('[contenteditable="true"]');
                if (editableChildren.length > 0) {
                    // New template structure: contenteditable is on child elements
                    editableChildren.forEach(child => {
                        child.contentEditable = 'true';
                    });
                } else {
                    // Old template structure: set contenteditable on the container itself
                    el.contentEditable = 'true';
                }
            });

            // Initialize floating toolbar for contenteditable elements
            initFloatingToolbar(container);
        });

        function saveLayout() {
            const container = document.getElementById('examContainer');
            const statusEl = document.getElementById('saveStatus');
            const saveBtn = document.getElementById('btnSaveLayout');

            if (!window.LayoutEditor) {
                statusEl.textContent = 'Editor not loaded';
                statusEl.className = 'save-status error';
                return;
            }

            // Show saving state
            statusEl.textContent = 'Saving...';
            statusEl.className = 'save-status saving';
            saveBtn.disabled = true;

            // Collect layout config
            const config = window.LayoutEditor.collectLayoutConfig(container);

            // Save to backend
            window.LayoutEditor.saveLayout(
                saveLayoutUrl,
                config,
                function(data) {
                    statusEl.textContent = 'Saved!';
                    statusEl.className = 'save-status saved';
                    saveBtn.disabled = false;

                    // Clear status after 2 seconds
                    setTimeout(() => {
                        statusEl.textContent = '';
                        statusEl.className = 'save-status';
                    }, 2000);
                },
                function(error) {
                    statusEl.textContent = 'Save failed!';
                    statusEl.className = 'save-status error';
                    saveBtn.disabled = false;
                }
            );
        }

        function exitEditMode() {
            // Reload the page in view mode
            window.location.href = previewRoute + '?font_size={{ $fontSize }}';
        }

        // ─── Floating Toolbar ─────────────────────────────────────────────
        let floatingToolbar = null;
        let fontSizeDropdown = null;

        function initFloatingToolbar(container) {
            // Create floating toolbar
            floatingToolbar = document.createElement('div');
            floatingToolbar.className = 'floating-toolbar';
            floatingToolbar.style.display = 'none';

            // Bold button
            const boldBtn = createToolbarBtn(
                '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M6 4h8a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/><path d="M6 12h9a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/></svg>',
                'Bold',
                () => document.execCommand('bold', false, null)
            );
            floatingToolbar.appendChild(boldBtn);

            // Italic button
            const italicBtn = createToolbarBtn(
                '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="4" x2="10" y2="4"/><line x1="14" y1="20" x2="5" y2="20"/><line x1="15" y1="4" x2="9" y2="20"/></svg>',
                'Italic',
                () => document.execCommand('italic', false, null)
            );
            floatingToolbar.appendChild(italicBtn);

            // Strikethrough button
            const strikeBtn = createToolbarBtn(
                '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="12" x2="20" y2="12"/><path d="M17.5 7.5c0-2-1.5-3.5-5.5-3.5S6 5.5 6 7.5c0 4 12 4 12 8 0 2-2 3.5-6 3.5s-6-1.5-6-3.5"/></svg>',
                'Strikethrough',
                () => document.execCommand('strikeThrough', false, null)
            );
            floatingToolbar.appendChild(strikeBtn);

            // Separator
            floatingToolbar.appendChild(createSeparator());

            // Align Left
            const alignLeftBtn = createToolbarBtn(
                '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="15" y2="12"/><line x1="3" y1="18" x2="18" y2="18"/></svg>',
                'Align Left',
                () => document.execCommand('justifyLeft', false, null)
            );
            floatingToolbar.appendChild(alignLeftBtn);

            // Align Center
            const alignCenterBtn = createToolbarBtn(
                '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="2" y1="6" x2="22" y2="6"/><line x1="5" y1="12" x2="19" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>',
                'Align Center',
                () => document.execCommand('justifyCenter', false, null)
            );
            floatingToolbar.appendChild(alignCenterBtn);

            // Align Right
            const alignRightBtn = createToolbarBtn(
                '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="9" y1="12" x2="21" y2="12"/><line x1="6" y1="18" x2="21" y2="18"/></svg>',
                'Align Right',
                () => document.execCommand('justifyRight', false, null)
            );
            floatingToolbar.appendChild(alignRightBtn);

            // Separator
            floatingToolbar.appendChild(createSeparator());

            // Font size dropdown wrapper
            const fontSizeWrapper = document.createElement('div');
            fontSizeWrapper.style.position = 'relative';

            const fontSizeBtn = createToolbarBtn(
                '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7V4h16v3"/><line x1="12" y1="4" x2="12" y2="20"/><line x1="8" y1="20" x2="16" y2="20"/></svg>',
                'Font Size',
                (e) => {
                    e.stopPropagation();
                    const dropdown = fontSizeWrapper.querySelector('.font-size-dropdown');
                    dropdown.style.display = dropdown.style.display === 'flex' ? 'none' : 'flex';
                }
            );
            fontSizeWrapper.appendChild(fontSizeBtn);

            // Font size dropdown
            const dropdown = document.createElement('div');
            dropdown.className = 'font-size-dropdown';
            dropdown.style.display = 'none';
            const fontSizes = ['8pt', '9pt', '10pt', '10.5pt', '11pt', '12pt', '13pt', '14pt', '16pt', '18pt', '20pt', '22pt'];
            fontSizes.forEach(size => {
                const sizeBtn = document.createElement('button');
                sizeBtn.textContent = size;
                sizeBtn.onclick = (e) => {
                    e.stopPropagation();
                    document.execCommand('fontSize', false, '7');
                    const fontElements = document.querySelectorAll('font[size="7"]');
                    fontElements.forEach(el => {
                        el.removeAttribute('size');
                        el.style.fontSize = size;
                    });
                    dropdown.style.display = 'none';
                };
                dropdown.appendChild(sizeBtn);
            });
            fontSizeWrapper.appendChild(dropdown);
            fontSizeDropdown = dropdown;
            floatingToolbar.appendChild(fontSizeWrapper);

            document.body.appendChild(floatingToolbar);

            // Show/hide toolbar on selection change
            document.addEventListener('selectionchange', () => {
                setTimeout(() => {
                    const selection = window.getSelection();
                    if (!selection || selection.isCollapsed || !selection.rangeCount) {
                        floatingToolbar.style.display = 'none';
                        return;
                    }

                    // Check if selection is inside an editable element
                    // Use data-editable attribute since contenteditable may be stripped by TipTap
                    let node = selection.anchorNode;
                    let editableElement = null;
                    // Text nodes have nodeType 3, so start from their parent element
                    if (node.nodeType === Node.TEXT_NODE) {
                        node = node.parentElement;
                    }
                    let depth = 0;
                    while (node && depth < 20) {
                        // Check for data-editable attribute OR contenteditable attribute
                        const dataEditable = node.getAttribute('data-editable');
                        const ce = node.getAttribute('contenteditable');
                        if (dataEditable || ce === 'true' || ce === '') {
                            editableElement = node;
                            break;
                        }
                        node = node.parentElement;
                        depth++;
                    }

                    if (!editableElement) {
                        floatingToolbar.style.display = 'none';
                        return;
                    }

                    // Position toolbar above selection
                    const range = selection.getRangeAt(0);
                    const rect = range.getBoundingClientRect();
                    floatingToolbar.style.display = 'flex';
                    floatingToolbar.style.top = (rect.top + window.scrollY - floatingToolbar.offsetHeight - 8) + 'px';
                    floatingToolbar.style.left = (rect.left + (rect.width / 2) - (floatingToolbar.offsetWidth / 2)) + 'px';
                }, 10);
            });

            // Close dropdown on outside click
            document.addEventListener('click', (e) => {
                if (fontSizeDropdown && !fontSizeWrapper.contains(e.target)) {
                    fontSizeDropdown.style.display = 'none';
                }
            });
        }

        function createToolbarBtn(icon, title, onClick) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.title = title;
            btn.innerHTML = icon;
            btn.onclick = (e) => {
                e.preventDefault();
                onClick(e);
                // Keep focus on the editable element
                const selection = window.getSelection();
                if (selection && !selection.isCollapsed) {
                    floatingToolbar.style.display = 'none';
                    setTimeout(() => {
                        // Reposition after command
                        const range = selection.getRangeAt(0);
                        const rect = range.getBoundingClientRect();
                        floatingToolbar.style.top = (rect.top + window.scrollY - floatingToolbar.offsetHeight - 8) + 'px';
                        floatingToolbar.style.left = (rect.left + (rect.width / 2) - (floatingToolbar.offsetWidth / 2)) + 'px';
                        floatingToolbar.style.display = 'flex';
                    }, 10);
                }
            };
            return btn;
        }

        function createSeparator() {
            const sep = document.createElement('div');
            sep.className = 'separator';
            return sep;
        }
    </script>
</body>
</html>
