<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $exam->title }} — Answer Sheet</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; line-height: 1.5; }

        /* ===== SHARED ===== */
        .page { padding: 20mm 18mm; }
        .page-break { page-break-after: always; }

        .header { border-bottom: 2px solid #111; padding-bottom: 8px; margin-bottom: 14px; }
        .header h1 { font-size: 16px; font-weight: bold; }
        .header .meta { font-size: 10px; color: #444; margin-top: 4px; }
        .header .meta span { margin-right: 16px; }

        .candidate-box { border: 1px solid #999; padding: 8px 12px; margin-bottom: 14px; }
        .candidate-box table { width: 100%; }
        .candidate-box td { padding: 3px 6px; font-size: 10px; }
        .candidate-box .field-line { border-bottom: 1px solid #555; display: inline-block; width: 180px; }

        /* ===== QUESTION PAPER ===== */
        .section-title { font-size: 12px; font-weight: bold; background: #f0f0f0; padding: 4px 8px;
                         border-left: 3px solid #333; margin: 12px 0 8px; }
        .question { margin-bottom: 12px; }
        .question .q-num { font-weight: bold; display: inline-block; width: 22px; }
        .question .q-text { display: inline; }
        .question .q-marks { float: right; font-size: 10px; color: #555; }
        .options { margin: 4px 0 0 22px; }
        .options .opt { margin-bottom: 2px; }
        .options .opt .opt-key { display: inline-block; width: 18px; font-weight: bold; }
        .essay-lines { margin: 6px 0 0 22px; }
        .essay-line { border-bottom: 1px solid #ccc; height: 18px; margin-bottom: 4px; }
        .short-answer-box { margin: 6px 0 0 22px; border: 1px solid #ccc; height: 40px; }

        /* ===== ANSWER SHEET ===== */
        .answer-sheet-title { font-size: 14px; font-weight: bold; text-align: center;
                              border: 2px solid #111; padding: 6px; margin-bottom: 14px; }
        .instructions { font-size: 10px; color: #444; margin-bottom: 12px; border: 1px solid #ccc;
                        padding: 6px 10px; background: #fafafa; }

        .answer-grid { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .answer-grid th { background: #222; color: #fff; font-size: 10px; padding: 4px 6px;
                          text-align: center; border: 1px solid #555; }
        .answer-grid td { border: 1px solid #bbb; padding: 5px 6px; text-align: center;
                          font-size: 10px; vertical-align: middle; }
        .answer-grid td.q-no { font-weight: bold; background: #f5f5f5; width: 32px; }
        .answer-grid td.bubble-cell { width: 28px; }
        .bubble { display: inline-block; width: 14px; height: 14px; border-radius: 50%;
                  border: 1.5px solid #555; }
        .answer-grid td.essay-answer { text-align: left; }
        .essay-answer-lines { }
        .essay-answer-line { border-bottom: 1px solid #ccc; height: 16px; margin-bottom: 3px; }

        .score-box { border: 2px solid #111; padding: 10px 14px; margin-top: 16px; }
        .score-box table { width: 100%; }
        .score-box td { padding: 4px 8px; font-size: 11px; }
        .score-box .score-field { border-bottom: 1px solid #555; display: inline-block; width: 80px; }
        .score-box .label { font-weight: bold; }

        .footer { margin-top: 20px; font-size: 9px; color: #888; text-align: center;
                  border-top: 1px solid #ddd; padding-top: 6px; }
    </style>
</head>
<body>

{{-- ============================================================ --}}
{{-- PAGE 1: QUESTION PAPER --}}
{{-- ============================================================ --}}
<div class="page">
    <div class="header">
        <h1>{{ strtoupper($exam->title) }}</h1>
        <div class="meta">
            <span><strong>Access Code:</strong> {{ $exam->access_code }}</span>
            @if($exam->duration_in_minutes)
                <span><strong>Duration:</strong> {{ $exam->duration_in_minutes }} minutes</span>
            @endif
            <span><strong>Total Marks:</strong> {{ $exam->total_marks }}</span>
            @if($exam->starts_at)
                <span><strong>Date:</strong> {{ $exam->starts_at->format('d M Y') }}</span>
            @endif
        </div>
    </div>

    @if($exam->instructions)
        <div class="instructions">
            <strong>Instructions:</strong> {{ $exam->instructions }}
        </div>
    @endif

    @if($exam->description)
        <p style="margin-bottom:10px; font-size:10px; color:#444;">{{ $exam->description }}</p>
    @endif

    {{-- Questions --}}
    @php $qNum = 1; @endphp

    @if($sections)
        @foreach($sections as $section)
            <div class="section-title">
                Section {{ $loop->iteration }}: {{ $section->title }}
                @if($section->description) — <span style="font-weight:normal;font-size:10px;">{{ $section->description }}</span> @endif
            </div>
            @if($section->instructions)
                <p style="font-size:10px;color:#555;margin-bottom:6px;">{{ $section->instructions }}</p>
            @endif
            @foreach($section->questions->sortBy('order') as $question)
                @include('general-exams.print.partials.question', ['question' => $question, 'qNum' => $qNum])
                @php $qNum++; @endphp
            @endforeach
        @endforeach
    @else
        @foreach($questions->sortBy('order') as $question)
            @include('general-exams.print.partials.question', ['question' => $question, 'qNum' => $qNum])
            @php $qNum++; @endphp
        @endforeach
    @endif

    <div class="footer">
        Generated by All Academies &mdash; {{ $generatedAt->format('d M Y H:i') }} &mdash; Do not distribute without authorisation.
    </div>
</div>

{{-- ============================================================ --}}
{{-- PAGE 2: ANSWER SHEET --}}
{{-- ============================================================ --}}
<div class="page page-break" style="page-break-before: always;">
    <div class="header">
        <h1>{{ strtoupper($exam->title) }}</h1>
        <div class="meta">
            <span><strong>Access Code:</strong> {{ $exam->access_code }}</span>
            <span><strong>Total Marks:</strong> {{ $exam->total_marks }}</span>
        </div>
    </div>

    <div class="answer-sheet-title">ANSWER SHEET — FOR EXAMINER USE</div>

    {{-- Candidate Info --}}
    <div class="candidate-box">
        <table>
            <tr>
                <td>Candidate Name: <span class="field-line">&nbsp;</span></td>
                <td>Index No: <span class="field-line" style="width:120px;">&nbsp;</span></td>
            </tr>
            <tr>
                <td>Date: <span class="field-line" style="width:120px;">&nbsp;</span></td>
                <td>Class/Group: <span class="field-line" style="width:120px;">&nbsp;</span></td>
            </tr>
        </table>
    </div>

    <div class="instructions">
        <strong>Examiner Instructions:</strong>
        For multiple choice and true/false questions, circle the correct answer bubble.
        For short answer and essay questions, write the score in the provided box.
        Total all section scores at the bottom.
    </div>

    {{-- Answer Grid --}}
    @php
        $mcqAndTf = $questions->filter(fn($q) => in_array($q->type, ['multiple_choice', 'true_false']));
        $openEnded = $questions->filter(fn($q) => in_array($q->type, ['short_answer', 'essay']));
        $qNum = 1;
        $questionNumbers = [];
        foreach ($questions->sortBy('order') as $q) {
            $questionNumbers[$q->id] = $qNum++;
        }
    @endphp

    @if($mcqAndTf->isNotEmpty())
        <p style="font-weight:bold;font-size:10px;margin-bottom:6px;">Section A — Objective Questions</p>
        <table class="answer-grid">
            <thead>
                <tr>
                    <th>Q#</th>
                    <th>A</th>
                    <th>B</th>
                    <th>C</th>
                    <th>D</th>
                    <th>Marks</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mcqAndTf->sortBy('order') as $question)
                    <tr>
                        <td class="q-no">{{ $questionNumbers[$question->id] }}</td>
                        @if($question->type === 'multiple_choice')
                            @foreach(['A','B','C','D'] as $opt)
                                <td class="bubble-cell"><span class="bubble"></span></td>
                            @endforeach
                        @else
                            <td class="bubble-cell"><span class="bubble"></span> T</td>
                            <td class="bubble-cell"><span class="bubble"></span> F</td>
                            <td></td>
                            <td></td>
                        @endif
                        <td>{{ $question->marks }}</td>
                        <td></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($openEnded->isNotEmpty())
        <p style="font-weight:bold;font-size:10px;margin:10px 0 6px;">Section B — Written Questions</p>
        <table class="answer-grid">
            <thead>
                <tr>
                    <th style="width:32px;">Q#</th>
                    <th>Type</th>
                    <th style="width:50px;">Max Marks</th>
                    <th style="width:60px;">Score Awarded</th>
                    <th>Examiner Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($openEnded->sortBy('order') as $question)
                    <tr>
                        <td class="q-no">{{ $questionNumbers[$question->id] }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $question->type)) }}</td>
                        <td style="text-align:center;">{{ $question->marks }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Score Summary --}}
    <div class="score-box">
        <table>
            <tr>
                <td class="label">Section A Total:</td>
                <td><span class="score-field">&nbsp;</span> / {{ $mcqAndTf->sum('marks') }}</td>
                <td class="label">Section B Total:</td>
                <td><span class="score-field">&nbsp;</span> / {{ $openEnded->sum('marks') }}</td>
            </tr>
            <tr>
                <td class="label" colspan="2">Grand Total:</td>
                <td colspan="2"><span class="score-field" style="width:100px;">&nbsp;</span> / {{ $exam->total_marks }}</td>
            </tr>
            <tr>
                <td class="label">Grade:</td>
                <td><span class="score-field">&nbsp;</span></td>
                <td class="label">Examiner Signature:</td>
                <td><span class="score-field" style="width:140px;">&nbsp;</span></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Generated by All Academies &mdash; {{ $generatedAt->format('d M Y H:i') }} &mdash; Confidential — Examiner Copy
    </div>
</div>

</body>
</html>
