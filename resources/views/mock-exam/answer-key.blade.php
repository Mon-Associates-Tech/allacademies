<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $mockExam->title }} – Answer Key</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 10.5pt;
            color: #1e293b;
            line-height: 1.5;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-bottom: 2px solid #7c3aed;
            padding-bottom: 10px;
            margin-bottom: 18px;
        }

        .title { font-size: 15pt; font-weight: bold; }
        .subtitle { font-size: 9pt; color: #7c3aed; font-weight: bold; text-transform: uppercase; letter-spacing: 0.1em; }
        .watermark { color: #dc2626; font-weight: bold; font-size: 11pt; text-align: right; }

        .subject-block { margin-bottom: 24px; }

        .subject-header {
            background: #7c3aed;
            color: white;
            padding: 7px 12px;
            font-weight: bold;
            font-size: 10.5pt;
            margin-bottom: 10px;
        }

        .section-title {
            font-weight: bold;
            font-size: 10pt;
            color: #4c1d95;
            border-bottom: 1px dashed #ddd6fe;
            padding-bottom: 4px;
            margin-bottom: 8px;
            margin-top: 12px;
        }

        .answer-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 6px;
            margin-bottom: 8px;
        }

        .answer-item {
            border: 1px solid #e2e8f0;
            border-radius: 2px;
            padding: 5px 8px;
            font-size: 9.5pt;
        }

        .answer-num {
            color: #64748b;
            font-size: 8.5pt;
            display: block;
        }

        .answer-val {
            font-weight: bold;
            color: #059669;
        }

        .essay-answer {
            margin-bottom: 10px;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #7c3aed;
            padding: 8px 12px;
            font-size: 9.5pt;
        }

        .essay-q { color: #64748b; font-size: 9pt; margin-bottom: 3px; }
        .essay-a { color: #1e293b; }
        .essay-kw { color: #7c3aed; font-size: 9pt; font-style: italic; margin-top: 3px; }

        .marks-badge {
            display: inline-block;
            background: #f1f5f9;
            color: #64748b;
            font-size: 8pt;
            padding: 1px 6px;
            border-radius: 10px;
            margin-left: 4px;
        }

        .page-break { page-break-before: always; }

        .footer {
            position: fixed;
            bottom: 10px;
            left: 0; right: 0;
            text-align: center;
            font-size: 8pt;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }
    </style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <div>
        <div class="title">{{ $mockExam->title }}</div>
        <div class="subtitle">Answer Key / Marking Scheme</div>
    </div>
    <div class="watermark">CONFIDENTIAL – INSTRUCTOR USE ONLY</div>
</div>

{{-- Subject exams --}}
@foreach($mockExam->subjectExams as $seIdx => $se)
    @if($seIdx > 0) <div class="page-break"></div> @endif

    <div class="subject-block">
        <div class="subject-header">
            {{ $se->getDisplayTitle() }} — Marking Scheme
        </div>

        @foreach($se->sections as $section)
            <div class="section-title">
                Section {{ $loop->iteration }}: {{ $section->title }}
                <span class="marks-badge">Total: {{ number_format($section->getTotalMarks(), 1) }} marks</span>
            </div>

            @php
                $mcqAndTf = $section->questions->filter(fn($q) => !$q->isEssay());
                $essays   = $section->questions->filter(fn($q) => $q->isEssay());
            @endphp

            {{-- MCQ / T-F grid --}}
            @if($mcqAndTf->isNotEmpty())
                <div class="answer-grid">
                    @foreach($mcqAndTf as $question)
                        <div class="answer-item">
                            <span class="answer-num">Q{{ $loop->iteration }}</span>
                            <span class="answer-val">
                                @if($question->isTrueFalse())
                                    {{ ucfirst(strtolower($question->correct_answer ?? '')) }}
                                @else
                                    {{ strtoupper($question->correct_answer ?? '—') }}
                                @endif
                            </span>
                            <span class="marks-badge">{{ $question->marks }}mk</span>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Essays --}}
            @foreach($essays as $qIdx => $question)
                <div class="essay-answer">
                    <div class="essay-q">
                        Q{{ $mcqAndTf->count() + $qIdx + 1 }}.
                        {{ \Illuminate\Support\Str::limit($question->question_text, 120) }}
                        <span class="marks-badge">{{ $question->marks }}mk</span>
                    </div>
                    @if($question->answer_explanation)
                        <div class="essay-a"><strong>Model Answer:</strong> {{ $question->answer_explanation }}</div>
                    @else
                        <div class="essay-a" style="color:#94a3b8;">No model answer stored.</div>
                    @endif
                    @if(!empty($question->answer_keywords))
                        <div class="essay-kw">
                            Key Terms: {{ implode(' · ', $question->answer_keywords) }}
                        </div>
                    @endif
                </div>
            @endforeach
        @endforeach
    </div>
@endforeach

<div class="footer">
    Answer Key: {{ $mockExam->title }} &nbsp;·&nbsp; Generated {{ now()->format('d M Y H:i') }} &nbsp;·&nbsp; CONFIDENTIAL
</div>

</body>
</html>
