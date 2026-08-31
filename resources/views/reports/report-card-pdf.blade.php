<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report Card - {{ $reportCard->student->user->name }}</title>
    <style>
        @page {
            margin: 0;
        }

        html, body {
            margin: 0;
            padding: 0;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9.5px;
            line-height: 1.4;
            color: #1a1a2e;
            background: #ffffff;
        }

        .accent-bar { width: 100%; height: 6px; background: #0c1f3f; }
        .gold-bar { width: 100%; height: 3px; background: #c9a22a; margin-bottom: 14px; }

        /* Horizontal inset via MARGIN (Dompdf-safe). Vertical-only padding. */
        .page-wrapper {
            margin: 0 15mm;
            padding: 10mm 0 75mm 0; /* bottom reserves space for the fixed block */
        }

        /* Full-width fixed shell: NO horizontal padding here */
        .bottom-fixed {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
            padding: 0 0 8mm 0;
            background: #ffffff;
        }

        /* Horizontal inset for the fixed block via an inner margin wrapper */
        .bottom-inner {
            margin: 0 15mm;
        }

        .header-table { width: 100%; margin-bottom: 12px; }
        .header-table td { vertical-align: middle; padding: 0; }
        .logo-cell { width: 64px; text-align: center; }
        .school-logo { width: 52px; height: 52px; object-fit: contain; }
        .school-info-cell { text-align: center; padding: 0 8px; }
        .school-name { font-size: 16px; font-weight: bold; color: #0c1f3f; letter-spacing: 0.5px; margin-bottom: 2px; }
        .school-tagline { font-size: 8.5px; font-style: italic; color: #c9a22a; margin-bottom: 3px; }
        .school-details { font-size: 8px; color: #555; line-height: 1.5; }
        .report-badge-cell { width: 64px; text-align: center; }
        .report-title-wrapper { margin-top: 8px; text-align: center; }
        .report-title { display: inline-block; background: #0c1f3f; color: #ffffff; font-size: 10px; font-weight: bold; letter-spacing: 2px; padding: 4px 20px; }

        .divider { width: 100%; border: none; border-top: 2px solid #0c1f3f; margin: 10px 0; }

        .info-outer { width: 100%; margin-bottom: 12px; border: 1.5px solid #0c1f3f; border-radius: 2px; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 5px 8px; border: 1px solid #d0d4de; font-size: 9px; }
        .info-header-row td { background: #0c1f3f; color: #ffffff; font-weight: bold; font-size: 9px; letter-spacing: 1px; text-align: center; padding: 4px 8px; }
        .info-label { background: #f0f2f7; font-weight: bold; color: #0c1f3f; width: 20%; white-space: nowrap; }
        .info-value { color: #222; width: 30%; }

        .section-label { font-size: 9px; font-weight: bold; letter-spacing: 1.5px; color: #0c1f3f; text-transform: uppercase; margin-bottom: 4px; border-left: 3px solid #c9a22a; padding-left: 6px; }

        .grades-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .grades-table thead tr th { background: #0c1f3f; color: #ffffff; padding: 6px 5px; text-align: center; font-size: 8.5px; letter-spacing: 0.3px; border: 1px solid #081729; }
        .grades-table thead tr th:first-child { text-align: left; padding-left: 8px; }
        .grades-table tbody tr td { padding: 5px 5px; border: 1px solid #d0d4de; font-size: 8.5px; text-align: center; color: #222; }
        .grades-table tbody tr td:first-child { text-align: left; font-weight: bold; padding-left: 8px; color: #0c1f3f; }
        .grades-table tbody tr:nth-child(even) td { background: #f7f8fc; }
        .grades-table tfoot tr td { background: #f0f2f7; font-weight: bold; font-size: 8.5px; padding: 5px; border: 1px solid #b0b8d0; text-align: center; }
        .grades-table tfoot tr td:first-child { text-align: left; padding-left: 8px; }

        .grade-a { background: #d4edda; color: #1a5c2a; font-weight: bold; padding: 2px 5px; border-radius: 2px; font-size: 8px; }
        .grade-b { background: #cce5ff; color: #004085; font-weight: bold; padding: 2px 5px; border-radius: 2px; font-size: 8px; }
        .grade-c { background: #fff3cd; color: #856404; font-weight: bold; padding: 2px 5px; border-radius: 2px; font-size: 8px; }
        .grade-d { background: #ffe0b2; color: #7a3800; font-weight: bold; padding: 2px 5px; border-radius: 2px; font-size: 8px; }
        .grade-f { background: #f8d7da; color: #721c24; font-weight: bold; padding: 2px 5px; border-radius: 2px; font-size: 8px; }
        .grade-default { background: #e2e8f0; color: #334155; font-weight: bold; padding: 2px 5px; border-radius: 2px; font-size: 8px; }

        .bottom-section-table { width: 100%; margin-bottom: 10px; border-collapse: collapse; }
        .bottom-section-table > tbody > tr > td { vertical-align: top; padding: 0; }
        .bottom-left { width: 38%; padding-right: 8px; }
        .bottom-right { width: 62%; padding-left: 8px; }

        .attendance-box { width: 100%; border: 1.5px solid #0c1f3f; border-radius: 2px; }
        .attendance-header { background: #0c1f3f; color: #fff; font-size: 8px; font-weight: bold; letter-spacing: 0.5px; padding: 4px 8px; }
        .attendance-body { padding: 8px; }
        .attendance-row { display: table; width: 100%; margin-bottom: 4px; }
        .attendance-row .label { display: table-cell; font-size: 8.5px; color: #555; }
        .attendance-row .value { display: table-cell; text-align: right; font-size: 8.5px; font-weight: bold; color: #0c1f3f; }

        .remarks-box { width: 100%; border: 1.5px solid #0c1f3f; border-radius: 2px; }
        .remarks-header { background: #0c1f3f; color: #fff; font-size: 8px; font-weight: bold; letter-spacing: 0.5px; padding: 4px 8px; }
        .remarks-body { min-height: 52px; padding: 6px 8px; font-size: 8.5px; color: #555; background: #fafbfd; }

        .signatures-table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .signatures-table td { text-align: center; vertical-align: bottom; padding: 0 12px; }
        .sig-image-wrap { height: 28px; text-align: center; }
        .sig-image-wrap img { height: 24px; object-fit: contain; }
        .sig-line { border-top: 1.5px solid #0c1f3f; margin-top: 4px; padding-top: 4px; font-size: 8px; font-weight: bold; color: #0c1f3f; letter-spacing: 0.5px; }
        .sig-role { font-size: 7.5px; color: #888; margin-top: 1px; }

        .page-footer { text-align: center; font-size: 7.5px; color: #aaa; border-top: 1px solid #e0e0e0; padding-top: 6px; }
    </style>
</head>
<body>
@php
    $configuration = $reportCard->configuration;
    $template = $configuration?->template;
    $sections = $template ? $template->resolvedSections() : \App\Models\ReportCardTemplate::defaultSections();

    $logoPath = (!empty($school?->logo) && file_exists(public_path('storage/' . $school->logo)))
        ? public_path('storage/' . $school->logo)
        : public_path('img/logo.png');

    $resolveWeightings = function ($subjectId) use ($reportCard) {
        $level = $reportCard->student->academic_level_id;
        $subjectSpecific = \App\Models\ScoreWeighting::where('school_id', $reportCard->school_id)
            ->where('academic_level_id', $level)
            ->where('academic_subject_id', $subjectId)
            ->orderBy('sort_order')->get();
        if ($subjectSpecific->isNotEmpty()) return $subjectSpecific;

        return \App\Models\ScoreWeighting::where('school_id', $reportCard->school_id)
            ->where(fn ($q) => $q->where('academic_level_id', $level)
                ->orWhere(fn ($q2) => $q2->whereNull('academic_level_id')->where('is_default', true)))
            ->whereNull('academic_subject_id')
            ->orderBy('sort_order')->get();
    };

    $displayGrades = $reportCard->grades->sortBy(fn ($g) => strtolower((string) ($g->subject->name ?? '')))->unique('subject_id')->values();
    $weightingColumns = $displayGrades->isNotEmpty() ? $resolveWeightings($displayGrades->first()->subject_id) : collect();

    $fallbackGrade = fn ($s) => (float)$s >= 90 ? 'A+' : ((float)$s >= 80 ? 'A' : ((float)$s >= 70 ? 'B+' : ((float)$s >= 60 ? 'B' : ((float)$s >= 50 ? 'C' : ((float)$s >= 40 ? 'D' : 'F')))));
    $gradeBadgeClass = function ($label) {
        $l = strtoupper(trim((string) $label));
        return str_starts_with($l, 'A') ? 'grade-a' : (str_starts_with($l, 'B') ? 'grade-b' : (str_starts_with($l, 'C') ? 'grade-c' : (str_starts_with($l, 'D') ? 'grade-d' : ($l === 'F' ? 'grade-f' : 'grade-default'))));
    };
    $overallTotal = $displayGrades->avg('total_score');
@endphp

<div class="accent-bar"></div>
<div class="gold-bar"></div>

<div class="page-wrapper">

    {{-- HEADER --}}
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="logo-cell">
                @if($sections['header']['show_logo'] ?? true)
                    <img src="{{ $logoPath }}" alt="{{ $school->name }}" class="school-logo">
                @endif
            </td>
            <td class="school-info-cell">
                @if($sections['header']['show_school_name'] ?? true)
                    <div class="school-name">{{ strtoupper($school->name) }}</div>
                @endif
                @if(($sections['header']['show_tagline'] ?? true) && !empty($sections['header']['tagline']))
                    <div class="school-tagline">{{ $sections['header']['tagline'] }}</div>
                @endif
                @if($sections['header']['show_contact'] ?? true)
                    <div class="school-details">
                        {{ $school->address }}, {{ $school->city }}, {{ $school->country }}<br>
                        Tel: {{ $school->phone }}&nbsp;&nbsp;|&nbsp;&nbsp;{{ $school->email }}
                        @if($school->website)
                            &nbsp;&nbsp;|&nbsp;&nbsp;{{ $school->website }}
                        @endif
                    </div>
                @endif
                <div class="report-title-wrapper"><span class="report-title">STUDENT REPORT CARD</span></div>
            </td>
            <td class="report-badge-cell"></td>
        </tr>
    </table>

    <hr class="divider">

    {{-- STUDENT INFO --}}
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

    {{-- GRADES TABLE --}}
    @if($sections['grades_table']['enabled'] ?? true)
        <div class="section-label">Academic Performance</div>
        <table class="grades-table" cellpadding="0" cellspacing="0">
            <thead>
            <tr>
                <th style="width:28%; text-align:left;">Subject</th>
                @foreach($weightingColumns as $weighting)
                    <th style="width:{{ round(44 / max($weightingColumns->count(),1)) }}%;">
                        {{ $weighting->name }}<br>
                        <span style="font-size:7px; font-weight:normal;">({{ rtrim(rtrim(number_format((float)$weighting->weight_percentage, 2, '.', ''), '0'), '.') }}%)</span>
                    </th>
                @endforeach
                <th style="width:10%;">Total<br><span style="font-size:7px; font-weight:normal;">(100)</span></th>
                <th style="width:8%;">Grade</th>
            </tr>
            </thead>
            <tbody>
            @foreach($displayGrades as $grade)
                @php $gradeLabel = $grade->grade_label ?: $fallbackGrade($grade->total_score); $scores = (array) ($grade->scores ?? []); @endphp
                <tr>
                    <td>{{ $grade->subject->name }}</td>
                    @foreach($weightingColumns as $weighting)
                        @php $key = $weighting->score_key ?: \Illuminate\Support\Str::slug($weighting->name, '_'); @endphp
                        <td>{{ number_format((float)($scores[$key] ?? 0), 1) }}</td>
                    @endforeach
                    <td><strong>{{ number_format($grade->total_score, 1) }}</strong></td>
                    <td><span class="{{ $gradeBadgeClass($gradeLabel) }}">{{ $gradeLabel }}</span></td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td style="text-align:left;">Overall Average</td>
                @foreach($weightingColumns as $weighting)
                    <td>—</td>
                @endforeach
                <td>{{ number_format($overallTotal, 1) }}</td>
                <td><span class="{{ $gradeBadgeClass($fallbackGrade($overallTotal)) }}">{{ $fallbackGrade($overallTotal) }}</span></td>
            </tr>
            </tfoot>
        </table>
    @endif
</div>

{{-- FIXED BOTTOM BLOCK --}}
<div class="bottom-fixed">
    <div class="bottom-inner">

        {{-- ATTENDANCE + REMARKS --}}
        <table class="bottom-section-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="bottom-left">
                    @if($sections['attendance']['enabled'] ?? true)
                        <div class="section-label" style="margin-bottom:4px;">{{ $sections['attendance']['label'] ?? 'Attendance' }}</div>
                        <div class="attendance-box">
                            <div class="attendance-header">ATTENDANCE</div>
                            <div class="attendance-body">
                                @if($reportCard->attendance_total_days)
                                    <div class="attendance-row"><span class="label">Total School Days</span><span class="value">{{ $reportCard->attendance_total_days }}</span></div>
                                    <div class="attendance-row"><span class="label">Days Present</span><span class="value">{{ $reportCard->attendance_days_present }}</span></div>
                                    <div class="attendance-row"><span class="label">Days Absent</span><span class="value">{{ $reportCard->attendanceDaysAbsent() }}</span></div>
                                    <div class="attendance-row"><span class="label">Attendance Rate</span><span class="value">{{ $reportCard->attendancePercentage() }}%</span></div>
                                @else
                                    <span style="color:#bbb; font-size:8.5px;">Not recorded</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </td>
                <td class="bottom-right">
                    @if($sections['remarks']['enabled'] ?? true)
                        <div class="section-label" style="margin-bottom:4px;">{{ $sections['remarks']['label'] ?? "Class Teacher's Remarks" }}</div>
                        <div class="remarks-box">
                            <div class="remarks-header">REMARKS</div>
                            <div class="remarks-body">{{ $reportCard->teacher_remarks }}</div>
                        </div>
                    @endif
                </td>
            </tr>
        </table>

        {{-- SIGNATURES --}}
        @php $slots = $sections['signatures']['slots'] ?? []; @endphp
        @if(!empty($slots))
            <table class="signatures-table" cellpadding="0" cellspacing="0">
                <tr>
                    @foreach($slots as $slot)
                        @php
                            $sigPath = match($slot['key']) {
                                'class_teacher' => $configuration?->class_teacher_signature_path,
                                'principal' => $configuration?->principal_signature_path,
                                default => null,
                            };
                            $sigName = match($slot['key']) {
                                'class_teacher' => $configuration?->class_teacher_name,
                                'principal' => $configuration?->principal_name,
                                default => null,
                            };
                        @endphp
                        <td style="width: {{ round(100 / count($slots)) }}%;">
                            <div class="sig-image-wrap">
                                @if($sigPath)
                                    <img src="{{ public_path('storage/' . $sigPath) }}" alt="{{ $slot['label'] }} Signature">
                                @endif
                            </div>
                            <div class="sig-line">{{ $sigName ?: $slot['label'] }}</div>
                            <div class="sig-role">{{ $slot['label'] }}</div>
                        </td>
                    @endforeach
                </tr>
            </table>
        @endif

        {{-- FOOTER --}}
        @if($sections['footer']['enabled'] ?? true)
            <div class="page-footer">
                {{ $sections['footer']['text'] ?? ('This is an official document of ' . $school->name . '. Any alteration renders it invalid.') }}
                &nbsp;&nbsp;|&nbsp;&nbsp; Generated: {{ $reportCard->generated_at->format('d M Y, H:i') }}
            </div>
        @endif

    </div>
</div>
</body>
</html>
