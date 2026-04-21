<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report Card - {{ $reportCard->student->user->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9.5px;
            line-height: 1.4;
            color: #1a1a2e;
            background: #ffffff;
        }

        .page-wrapper {
            width: 100%;
            padding: 0;
        }

        /* ── TOP ACCENT BAR ── */
        .accent-bar {
            width: 100%;
            height: 6px;
            background: #16213e;
            margin-bottom: 0;
        }
        .gold-bar {
            width: 100%;
            height: 3px;
            background: #c9a84c;
            margin-bottom: 14px;
        }

        /* ── HEADER ── */
        .header-table {
            width: 100%;
            margin-bottom: 12px;
        }
        .header-table td {
            vertical-align: middle;
            padding: 0;
        }
        .logo-cell {
            width: 64px;
            text-align: center;
        }
        .school-logo {
            width: 52px;
            height: 52px;
            object-fit: contain;
        }
        .school-info-cell {
            text-align: center;
            padding: 0 8px;
        }
        .school-name {
            font-size: 16px;
            font-weight: bold;
            color: #16213e;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .school-details {
            font-size: 8px;
            color: #555;
            line-height: 1.5;
        }
        .report-badge-cell {
            width: 64px;
            text-align: center;
        }

        .report-title-wrapper {
            margin-top: 8px;
            text-align: center;
        }
        .report-title {
            display: inline-block;
            background: #16213e;
            color: #ffffff;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 2px;
            padding: 4px 20px;
        }

        /* ── DIVIDER ── */
        .divider {
            width: 100%;
            border: none;
            border-top: 2px solid #16213e;
            margin: 10px 0 10px 0;
        }
        .divider-gold {
            width: 60px;
            border: none;
            border-top: 2px solid #c9a84c;
            margin: 2px auto 10px auto;
        }

        /* ── STUDENT INFO ── */
        .info-outer {
            width: 100%;
            margin-bottom: 12px;
            border: 1.5px solid #16213e;
            border-radius: 2px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px 8px;
            border: 1px solid #d0d4de;
            font-size: 9px;
        }
        .info-header-row td {
            background: #16213e;
            color: #ffffff;
            font-weight: bold;
            font-size: 9px;
            letter-spacing: 1px;
            text-align: center;
            padding: 4px 8px;
        }
        .info-label {
            background: #f0f2f7;
            font-weight: bold;
            color: #16213e;
            width: 20%;
            white-space: nowrap;
        }
        .info-value {
            color: #222;
            width: 30%;
        }

        /* ── GRADES TABLE ── */
        .section-label {
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 1.5px;
            color: #16213e;
            text-transform: uppercase;
            margin-bottom: 4px;
            padding-left: 2px;
            border-left: 3px solid #c9a84c;
            padding-left: 6px;
        }

        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .grades-table thead tr th {
            background: #16213e;
            color: #ffffff;
            padding: 6px 5px;
            text-align: center;
            font-size: 8.5px;
            letter-spacing: 0.3px;
            border: 1px solid #0d1526;
        }
        .grades-table thead tr th:first-child {
            text-align: left;
            padding-left: 8px;
        }
        .grades-table thead tr.sub-header th {
            background: #c9a84c;
            color: #16213e;
            font-size: 7.5px;
            padding: 3px 5px;
            font-weight: bold;
        }
        .grades-table tbody tr td {
            padding: 5px 5px;
            border: 1px solid #d0d4de;
            font-size: 8.5px;
            text-align: center;
            color: #222;
        }
        .grades-table tbody tr td:first-child {
            text-align: left;
            font-weight: bold;
            padding-left: 8px;
            color: #16213e;
        }
        .grades-table tbody tr:nth-child(even) td {
            background: #f7f8fc;
        }
        .grades-table tbody tr:nth-child(odd) td {
            background: #ffffff;
        }
        .grades-table tbody tr.empty-row td {
            background: #fafafa;
            color: #ccc;
            height: 18px;
        }
        .grades-table tfoot tr td {
            background: #f0f2f7;
            font-weight: bold;
            font-size: 8.5px;
            padding: 5px 5px;
            border: 1px solid #b0b8d0;
            text-align: center;
        }
        .grades-table tfoot tr td:first-child {
            text-align: left;
            padding-left: 8px;
        }

        /* Grade badges */
        .grade-a  { background: #d4edda; color: #1a5c2a; font-weight: bold; padding: 2px 5px; border-radius: 2px; font-size: 8px; }
        .grade-b  { background: #cce5ff; color: #004085; font-weight: bold; padding: 2px 5px; border-radius: 2px; font-size: 8px; }
        .grade-c  { background: #fff3cd; color: #856404; font-weight: bold; padding: 2px 5px; border-radius: 2px; font-size: 8px; }
        .grade-d  { background: #ffe0b2; color: #7a3800; font-weight: bold; padding: 2px 5px; border-radius: 2px; font-size: 8px; }
        .grade-f  { background: #f8d7da; color: #721c24; font-weight: bold; padding: 2px 5px; border-radius: 2px; font-size: 8px; }
        .grade-default { background: #e2e8f0; color: #334155; font-weight: bold; padding: 2px 5px; border-radius: 2px; font-size: 8px; }

        /* ── GRADE SCALE & REMARKS SECTION ── */
        .bottom-section-table {
            width: 100%;
            margin-bottom: 16px;
            border-collapse: collapse;
        }
        .bottom-section-table > tbody > tr > td {
            vertical-align: top;
            padding: 0;
        }
        .bottom-left {
            width: 40%;
            padding-right: 8px;
        }
        .bottom-right {
            width: 60%;
            padding-left: 8px;
        }

        .scale-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #16213e;
        }
        .scale-table th {
            background: #16213e;
            color: #fff;
            font-size: 8px;
            padding: 4px 6px;
            letter-spacing: 0.5px;
            text-align: center;
        }
        .scale-table td {
            font-size: 8px;
            padding: 3px 6px;
            border: 1px solid #d0d4de;
            text-align: center;
        }
        .scale-table tr:nth-child(even) td { background: #f7f8fc; }

        .remarks-box {
            width: 100%;
            border: 1.5px solid #16213e;
            border-radius: 2px;
        }
        .remarks-header {
            background: #16213e;
            color: #fff;
            font-size: 8px;
            font-weight: bold;
            letter-spacing: 0.5px;
            padding: 4px 8px;
        }
        .remarks-body {
            height: 52px;
            padding: 6px 8px;
            font-size: 8.5px;
            color: #555;
            background: #fafbfd;
        }

        /* ── SIGNATURES ── */
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        .signatures-table td {
            text-align: center;
            vertical-align: bottom;
            padding: 0 12px;
            width: 33.33%;
        }
        .sig-image-wrap {
            height: 28px;
            text-align: center;
            margin-bottom: 0;
        }
        .sig-image-wrap img {
            height: 24px;
            object-fit: contain;
        }
        .sig-line {
            border-top: 1.5px solid #16213e;
            margin-top: 4px;
            padding-top: 4px;
            font-size: 8px;
            font-weight: bold;
            color: #16213e;
            letter-spacing: 0.5px;
        }
        .sig-role {
            font-size: 7.5px;
            color: #888;
            margin-top: 1px;
        }

        /* ── FOOTER ── */
        .page-footer {
            margin-top: 12px;
            text-align: center;
            font-size: 7.5px;
            color: #aaa;
            border-top: 1px solid #e0e0e0;
            padding-top: 6px;
        }

        .gold-accent { color: #c9a84c; }
    </style>
</head>
<body>
@php
    $configuration = $reportCard->configuration;
    $logoPath = (!empty($school?->logo) && file_exists(public_path('storage/' . $school->logo)))
        ? public_path('storage/' . $school->logo)
        : public_path('img/logo.png');

    $weightings = \App\Models\ScoreWeighting::where('school_id', $reportCard->school_id)
        ->where(function ($query) use ($reportCard) {
            $query->where('academic_level_id', $reportCard->student->academic_level_id)
                ->orWhere(function ($q) {
                    $q->whereNull('academic_level_id')->where('is_default', true);
                });
        })
        ->orderBy('sort_order')
        ->get();

    if ($weightings->isEmpty()) {
        $weightings = collect([
            (object) ['name' => 'Class Score', 'score_key' => 'class_score', 'weight_percentage' => 40, 'max_score' => 40],
            (object) ['name' => 'Test Score',  'score_key' => 'test_score',  'weight_percentage' => 10, 'max_score' => 10],
            (object) ['name' => 'Exam Score',  'score_key' => 'exam_score',  'weight_percentage' => 50, 'max_score' => 50],
        ]);
    }

    $assignedSubjectIds = $reportCard->student->individualSubjects()
        ->wherePivot('is_active', true)
        ->pluck('academic_subjects.id');

    $displayGrades = $reportCard->grades
        ->when($assignedSubjectIds->isNotEmpty(), fn ($c) => $c->whereIn('subject_id', $assignedSubjectIds))
        ->sortBy(fn ($g) => strtolower((string) ($g->subject->name ?? '')))
        ->values();

    if ($displayGrades->isEmpty()) {
        $displayGrades = $reportCard->grades
            ->sortBy(fn ($g) => strtolower((string) ($g->subject->name ?? '')))
            ->values();
    }

    $displayGrades = $displayGrades->unique('subject_id')->values();

    $configuredMax  = (int) ($configuration?->max_subjects ?: 10);
    $renderLimit    = max(1, min($configuredMax, 10));
    $displayGrades  = $displayGrades->take($renderLimit)->values();
    $emptyRows      = max(0, 10 - $displayGrades->count());

    $classPeerCardIds = \App\Models\ReportCard::query()
        ->where('school_id', $reportCard->school_id)
        ->where('term', $reportCard->term)
        ->where('academic_year_id', $reportCard->academic_year_id)
        ->when($reportCard->report_card_configuration_id, fn ($q) => $q->where('report_card_configuration_id', $reportCard->report_card_configuration_id))
        ->whereHas('student', function ($query) use ($reportCard) {
            $query->where('academic_level_id', $reportCard->student->academic_level_id);
        })
        ->pluck('id');

    $classStudentCount = \App\Models\ReportCard::query()
        ->whereIn('id', $classPeerCardIds)
        ->distinct('student_id')
        ->count('student_id');

    $subjectIdsForPosition = $displayGrades->pluck('subject_id')->unique()->values();
    $peerScores = \App\Models\ReportCardGrade::query()
        ->select(['report_card_id', 'subject_id', 'total_score'])
        ->whereIn('report_card_id', $classPeerCardIds)
        ->whereIn('subject_id', $subjectIdsForPosition)
        ->whereNotNull('total_score')
        ->get()
        ->groupBy('subject_id');

    $subjectPositions = [];
    foreach ($peerScores as $subjectId => $rows) {
        $ordered   = $rows->sortByDesc('total_score')->values();
        $rank = $index = 0;
        $lastScore = null;
        foreach ($ordered as $row) {
            $index++;
            if ($lastScore === null || (float) $row->total_score !== (float) $lastScore) {
                $rank      = $index;
                $lastScore = (float) $row->total_score;
            }
            if ((int) $row->report_card_id === (int) $reportCard->id) {
                $subjectPositions[$subjectId] = $rank;
                break;
            }
        }
    }

    $fallbackGrade = function ($score) {
        $score = (float) $score;
        return $score >= 90 ? 'A+' :
            ($score >= 80 ? 'A'  :
            ($score >= 70 ? 'B+' :
            ($score >= 60 ? 'B'  :
            ($score >= 50 ? 'C'  :
            ($score >= 40 ? 'D'  : 'F')))));
    };

    $gradeBadgeClass = function ($label) {
        $l = strtoupper(trim($label));
        if (str_starts_with($l, 'A')) return 'grade-a';
        if (str_starts_with($l, 'B')) return 'grade-b';
        if (str_starts_with($l, 'C')) return 'grade-c';
        if (str_starts_with($l, 'D')) return 'grade-d';
        if ($l === 'F')               return 'grade-f';
        return 'grade-default';
    };

    $resolveScore = function ($grade, $weighting) {
        $scoreKey = $weighting->score_key ?? \Illuminate\Support\Str::slug((string) ($weighting->name ?? ''), '_');
        $scores   = (array) ($grade->scores ?? []);

        if (array_key_exists($scoreKey, $scores)) return (float) $scores[$scoreKey];
        if (isset($weighting->name) && array_key_exists($weighting->name, $scores)) return (float) $scores[$weighting->name];

        $key = strtolower((string) $scoreKey);
        if (str_contains($key, 'class') || str_contains($key, 'assess')) return (float) ($grade->assessments_score ?? 0);
        if (str_contains($key, 'quiz')  || str_contains($key, 'test'))   return (float) ($grade->quizzes_score    ?? 0);
        if (str_contains($key, 'exam'))                                   return (float) ($grade->final_exam_score ?? 0);
        return 0.0;
    };

    $overallTotal = $displayGrades->avg('total_score');
    $overallGrade = $fallbackGrade($overallTotal);
@endphp

<div class="page-wrapper">

    {{-- TOP ACCENT --}}
    <div class="accent-bar"></div>
    <div class="gold-bar"></div>

    {{-- HEADER --}}
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="logo-cell">
                <img src="{{ $logoPath }}" alt="{{ $school->name }}" class="school-logo">
            </td>
            <td class="school-info-cell">
                <div class="school-name">{{ strtoupper($school->name) }}</div>
                <div class="school-details">
                    {{ $school->address }}, {{ $school->city }}, {{ $school->country }}<br>
                    Tel: {{ $school->phone }}&nbsp;&nbsp;|&nbsp;&nbsp;{{ $school->email }}
                    @if($school->website)&nbsp;&nbsp;|&nbsp;&nbsp;{{ $school->website }}@endif
                </div>
                <div class="report-title-wrapper">
                    <span class="report-title">STUDENT REPORT CARD</span>
                </div>
            </td>
            <td class="report-badge-cell">
                {{-- Mirror of logo cell for symmetry --}}
            </td>
        </tr>
    </table>

    <hr class="divider">

    {{-- STUDENT INFORMATION --}}
    <div class="info-outer">
        <table class="info-table" cellpadding="0" cellspacing="0">
            <tr class="info-header-row">
                <td colspan="4">STUDENT INFORMATION</td>
            </tr>
            <tr>
                <td class="info-label">Student Name</td>
                <td class="info-value">{{ $reportCard->student->user->name }}</td>
                <td class="info-label">Student ID</td>
                <td class="info-value">{{ $reportCard->student->student_id }}</td>
            </tr>
            <tr>
                <td class="info-label">Class / Level</td>
                <td class="info-value">{{ $reportCard->student->academicLevel->name }}</td>
                <td class="info-label">Academic Year</td>
                <td class="info-value">{{ $reportCard->configuration->academicPeriod->academic_year }}</td>
            </tr>
            <tr>
                <td class="info-label">Term / Period</td>
                <td class="info-value">{{ $reportCard->term }}</td>
                <td class="info-label">Report Date</td>
                <td class="info-value">{{ $reportCard->generated_at->format('F d, Y') }}</td>
            </tr>
        </table>
    </div>

    {{-- ACADEMIC PERFORMANCE --}}
    <div class="section-label">Academic Performance</div>

    <table class="grades-table" cellpadding="0" cellspacing="0">
        <thead>
        <tr>
            <th style="width:28%; text-align:left;">Subject</th>
            @foreach($weightings as $weighting)
                <th style="width:12%;">
                    {{ $weighting->name }}<br>
                    <span style="font-size:7px; font-weight:normal;">({{ rtrim(rtrim(number_format((float)$weighting->weight_percentage, 2, '.', ''), '0'), '.') }}%)</span>
                </th>
            @endforeach
            <th style="width:10%;">Total<br><span style="font-size:7px; font-weight:normal;">(100)</span></th>
            <th style="width:8%;">Grade</th>
            <th style="width:12%;">Position</th>
        </tr>
        </thead>
        <tbody>
        @foreach($displayGrades as $grade)
            @php $gradeLabel = $grade->grade_label ?: $fallbackGrade($grade->total_score); @endphp
            <tr>
                <td>{{ $grade->subject->name }}</td>
                @foreach($weightings as $weighting)
                    <td>{{ number_format($resolveScore($grade, $weighting), 1) }}</td>
                @endforeach
                <td><strong>{{ number_format($grade->total_score, 1) }}</strong></td>
                <td>
                    <span class="{{ $gradeBadgeClass($gradeLabel) }}">{{ $gradeLabel }}</span>
                </td>
                <td>
                    @if(isset($subjectPositions[$grade->subject_id]) && $classStudentCount > 0)
                        {{ $subjectPositions[$grade->subject_id] }}<span style="color:#999;">/{{ $classStudentCount }}</span>
                    @else
                        <span style="color:#bbb;">—</span>
                    @endif
                </td>
            </tr>
        @endforeach
        @for($i = 0; $i < $emptyRows; $i++)
            <tr class="empty-row">
                <td>&nbsp;</td>
                @foreach($weightings as $weighting)<td>&nbsp;</td>@endforeach
                <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
            </tr>
        @endfor
        </tbody>
        <tfoot>
        <tr>
            <td style="text-align:left;">Overall Average</td>
            @foreach($weightings as $weighting)<td>—</td>@endforeach
            <td>{{ number_format($overallTotal, 1) }}</td>
            <td>
                <span class="{{ $gradeBadgeClass($overallGrade) }}">{{ $overallGrade }}</span>
            </td>
            <td>—</td>
        </tr>
        </tfoot>
    </table>

    {{-- GRADE SCALE + REMARKS --}}
    <table class="bottom-section-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="bottom-left">
                <div class="section-label" style="margin-bottom:4px;">Grading Scale</div>
                <table class="scale-table" cellpadding="0" cellspacing="0">
                    <thead>
                    <tr>
                        <th>Score Range</th>
                        <th>Grade</th>
                        <th>Remark</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr><td>90 – 100</td><td><span class="grade-a">A+</span></td><td>Excellent</td></tr>
                    <tr><td>80 – 89</td> <td><span class="grade-a">A</span></td> <td>Very Good</td></tr>
                    <tr><td>70 – 79</td> <td><span class="grade-b">B+</span></td><td>Good</td></tr>
                    <tr><td>60 – 69</td> <td><span class="grade-b">B</span></td> <td>Credit</td></tr>
                    <tr><td>50 – 59</td> <td><span class="grade-c">C</span></td> <td>Average</td></tr>
                    <tr><td>40 – 49</td> <td><span class="grade-d">D</span></td> <td>Pass</td></tr>
                    <tr><td>0 – 39</td>  <td><span class="grade-f">F</span></td> <td>Fail</td></tr>
                    </tbody>
                </table>
            </td>
            <td class="bottom-right">
                <div class="section-label" style="margin-bottom:4px;">Class Teacher's Remarks</div>
                <div class="remarks-box">
                    <div class="remarks-header">REMARKS</div>
                    <div class="remarks-body">
                        @if(!empty($reportCard->teacher_remarks))
                            {{ $reportCard->teacher_remarks }}
                        @endif
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- SIGNATURES --}}
    <table class="signatures-table" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <div class="sig-image-wrap">
                    @if(!empty($configuration?->class_teacher_signature_path))
                        <img src="{{ public_path('storage/' . $configuration->class_teacher_signature_path) }}" alt="Class Teacher Signature">
                    @endif
                </div>
                <div class="sig-line">{{ $configuration?->class_teacher_name ?: 'Class Teacher' }}</div>
                <div class="sig-role">Class Teacher</div>
            </td>
            <td>
                <div class="sig-image-wrap">
                    @if(!empty($configuration?->principal_signature_path))
                        <img src="{{ public_path('storage/' . $configuration->principal_signature_path) }}" alt="Head of School Signature">
                    @endif
                </div>
                <div class="sig-line">{{ $configuration?->principal_name ?: 'Head of School' }}</div>
                <div class="sig-role">Head of School</div>
            </td>
            <td>
                <div class="sig-image-wrap"></div>
                <div class="sig-line">Parent / Guardian</div>
                <div class="sig-role">Acknowledgement</div>
            </td>
        </tr>
    </table>

    {{-- FOOTER --}}
    <div class="page-footer">
        This is an official document of {{ $school->name }}. Any alteration renders it invalid.
        &nbsp;&nbsp;|&nbsp;&nbsp; Generated: {{ $reportCard->generated_at->format('d M Y, H:i') }}
    </div>

</div>
</body>
</html>
