<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $mockExam->title }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 11pt;
            color: #1e293b;
            line-height: 1.5;
        }

        /* ── Header ── */
        .exam-header {
            border-bottom: 2px solid #1e293b;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .exam-title {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .exam-meta {
            display: flex;
            justify-content: space-between;
            margin-top: 8px;
            font-size: 9pt;
            color: #475569;
        }

        .candidate-box {
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
            margin-bottom: 16px;
        }

        .candidate-box table { width: 100%; }
        .candidate-box td { padding: 4px 0; font-size: 10pt; }
        .candidate-box .label { color: #64748b; width: 140px; }
        .candidate-field {
            border-bottom: 1px solid #94a3b8;
            min-width: 200px;
            display: inline-block;
        }

        /* ── Instructions ── */
        .instructions {
            background: #fefce8;
            border: 1px solid #fde047;
            border-left: 4px solid #eab308;
            padding: 10px 14px;
            margin-bottom: 20px;
            font-size: 10pt;
        }

        .instructions-title {
            font-weight: bold;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 4px;
        }

        /* ── Subject exam ── */
        .subject-exam { margin-bottom: 24px; page-break-inside: avoid; }

        .subject-header {
            background: #1e293b;
            color: white;
            padding: 8px 14px;
            font-weight: bold;
            font-size: 11pt;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 12px;
        }

        /* ── Section ── */
        .section { margin-bottom: 18px; }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }

        .section-title { font-weight: bold; font-size: 10.5pt; }
        .section-meta { font-size: 9pt; color: #64748b; }

        .section-instructions {
            font-style: italic;
            color: #475569;
            font-size: 9.5pt;
            margin-bottom: 10px;
        }

        /* ── Questions ── */
        .question { margin-bottom: 14px; }

        .question-row {
            display: flex;
            gap: 8px;
            align-items: flex-start;
        }

        .question-num {
            font-weight: bold;
            min-width: 22px;
            color: #7c3aed;
        }

        .question-marks {
            font-size: 8.5pt;
            color: #94a3b8;
            white-space: nowrap;
            margin-left: 4px;
        }

        .question-text { flex: 1; }

        /* MCQ options */
        .options {
            margin-top: 5px;
            margin-left: 30px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3px 16px;
        }

        .option { font-size: 10pt; }
        .option-key { font-weight: bold; margin-right: 4px; }

        /* True/False */
        .tf-options {
            margin-top: 5px;
            margin-left: 30px;
            display: flex;
            gap: 20px;
        }

        .tf-circle {
            display: inline-block;
            border: 1px solid #94a3b8;
            border-radius: 50%;
            width: 14px;
            height: 14px;
            vertical-align: middle;
            margin-right: 5px;
        }

        /* Essay answer box */
        .essay-box {
            margin-top: 6px;
            margin-left: 30px;
            border: 1px solid #e2e8f0;
            min-height: 60px;
            border-radius: 2px;
        }

        /* ── Footer ── */
        .page-footer {
            position: fixed;
            bottom: 12px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }

        .page-break { page-break-before: always; }
    </style>
</head>
<body>

{{-- ── Exam Header ──────────────────────────────────────────────────────────── --}}
<div class="exam-header">
    <div class="exam-title">{{ $mockExam->title }}</div>
    <div class="exam-meta">
        <span>
            @if($mockExam->starts_at) Date: {{ $mockExam->starts_at->format('d F Y') }} @endif
        </span>
        <span>Generated: {{ now()->format('d M Y') }}</span>
    </div>
</div>

{{-- ── Candidate Details Box ────────────────────────────────────────────────── --}}
<div class="candidate-box">
    <table>
        <tr>
            <td class="label">Candidate Name:</td>
            <td><span class="candidate-field">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
            <td class="label" style="padding-left:20px;">Index / ID No.:</td>
            <td><span class="candidate-field">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
        </tr>
        <tr>
            <td class="label">Centre / School:</td>
            <td colspan="3"><span class="candidate-field" style="min-width:350px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
        </tr>
    </table>
</div>

{{-- ── Global Instructions ──────────────────────────────────────────────────── --}}
@if($mockExam->instructions)
    <div class="instructions">
        <div class="instructions-title">General Instructions</div>
        {{ $mockExam->instructions }}
    </div>
@endif

{{-- ── Subject Exams ────────────────────────────────────────────────────────── --}}
@foreach($mockExam->subjectExams as $seIdx => $se)
    @if($seIdx > 0) <div class="page-break"></div> @endif

    <div class="subject-exam">
        <div class="subject-header">
            Subject {{ $loop->iteration }}: {{ $se->getDisplayTitle() }}
            @if($se->duration_in_minutes) &nbsp;|&nbsp; Time Allowed: {{ $se->duration_in_minutes }} minutes @endif
        </div>

        @if($se->instructions)
            <div class="instructions" style="margin-bottom:14px;">
                <div class="instructions-title">Subject Instructions</div>
                {{ $se->instructions }}
            </div>
        @endif

        @foreach($se->sections as $sIdx => $section)
            <div class="section">
                <div class="section-header">
                    <span class="section-title">Section {{ $sIdx + 1 }}: {{ $section->title }}</span>
                    <span class="section-meta">
                        {{ ucfirst(str_replace('_', ' ', $section->question_type)) }}
                        · {{ $section->questions->count() }} question(s)
                        · {{ number_format($section->getTotalMarks(), 1) }} marks
                    </span>
                </div>

                @if($section->instructions)
                    <p class="section-instructions">{{ $section->instructions }}</p>
                @endif

                @foreach($section->questions as $qIdx => $question)
                    <div class="question">
                        <div class="question-row">
                            <span class="question-num">{{ $qIdx + 1 }}.</span>
                            <span class="question-text">{{ $question->question_text }}</span>
                            <span class="question-marks">[{{ $question->marks }}mk]</span>
                        </div>

                        @if($question->isMultipleChoice() && !empty($question->options))
                            <div class="options">
                                @foreach($question->getOptionsForDisplay() as $letter => $text)
                                    <div class="option">
                                        <span class="option-key">{{ $letter }}.</span>{{ $text }}
                                    </div>
                                @endforeach
                            </div>

                        @elseif($question->isTrueFalse())
                            <div class="tf-options">
                                <span><span class="tf-circle"></span> True</span>
                                <span><span class="tf-circle"></span> False</span>
                            </div>

                        @elseif($question->isEssay())
                            <div class="essay-box"></div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
@endforeach

{{-- Footer --}}
<div class="page-footer">
    {{ $mockExam->title }} &nbsp;·&nbsp; {{ now()->format('Y') }} &nbsp;·&nbsp; Page <span class="pagenum"></span>
</div>

</body>
</html>
