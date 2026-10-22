<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Card - {{ $reportCard->student->user->name }}</title>

    {{-- Inline Tailwind CSS for Puppeteer/Browsershot --}}
    @if(!empty($tailwindCss ?? ''))
        <style>{!! $tailwindCss !!}</style>
    @endif

    <style>
        /* =========================================================
           DESIGN TOKENS
           Every colour, size and spacing value below is defined once
           and reused. Change a token and the whole document follows.
           ========================================================= */
        :root {
            /* Colour */
            --navy: #0f2a5c;
            --navy-tint: #e8edf6;
            --gold: #b8923a;          /* rules and decoration only */
            --gold-text: #8a6a1f;     /* gold used for text (print-safe contrast) */
            --ink: #1f2937;
            --body: #374151;
            --muted: #6b7280;
            --faint: #9ca3af;
            --line: #d5dbe6;
            --soft: #f4f6fa;

            /* Typography */
            --font-sans: "Helvetica Neue", Helvetica, Arial, "Liberation Sans", sans-serif;
            --font-serif: Georgia, "Times New Roman", "Liberation Serif", serif;
            --t-label: 8.5px;
            --t-small: 10px;
            --t-base: 11.5px;
            --t-medium: 12.5px;
            --t-large: 15px;
            --t-stat: 20px;
            --t-school: 22px;

            /* Spacing scale */
            --s1: 4px;
            --s2: 8px;
            --s3: 12px;
            --s4: 16px;
            --s5: 24px;

            /* Shape */
            --radius: 4px;
            --rule: 1px;
            --rule-strong: 2px;
        }

        @page { size: A4; margin: 0; }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        html, body {
            margin: 0;
            padding: 0;
            background: #fff;
            color: var(--ink);
            font-family: var(--font-sans);
            font-size: var(--t-base);
            line-height: 1.4;
        }

        /* =========================================================
           PAGE FRAME
           ========================================================= */
        .rc-page {
            position: relative;
            width: 210mm;
            min-height: 296mm;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            background: #fff;
        }
        .rc-topbar { height: 8px; background: var(--navy); }
        .rc-topbar-gold { height: 3px; background: var(--gold); }

        .rc-body {
            position: relative;
            z-index: 1;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: var(--s4);                 /* one vertical rhythm for every block */
            padding: var(--s5) 34px var(--s4);
        }

        .rc-watermark {
            position: absolute;
            top: 50%; left: 50%;
            width: 380px; height: 380px;
            transform: translate(-50%, -50%);
            opacity: 0.04;
            object-fit: contain;
            z-index: 0;
        }

        /* =========================================================
           LETTERHEAD
           ========================================================= */
        .rc-header {
            position: relative;
            display: flex;
            align-items: center;
            gap: var(--s4);
            padding-bottom: var(--s3);
            border-bottom: var(--rule-strong) solid var(--navy);
        }
        .rc-header::after {
            content: "";
            position: absolute;
            left: 0; right: 0; bottom: -5px;
            height: var(--rule);
            background: var(--gold);
        }
        .rc-logo { width: 72px; height: 72px; object-fit: contain; flex-shrink: 0; }
        .rc-header-text { flex: 1; text-align: center; }
        .rc-school {
            margin: 0;
            font-family: var(--font-serif);
            font-size: var(--t-school);
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--navy);
            line-height: 1.2;
        }
        .rc-tagline {
            margin: var(--s1) 0 0;
            font-family: var(--font-serif);
            font-style: italic;
            font-size: var(--t-base);
            color: var(--gold-text);
        }
        .rc-contact {
            margin: var(--s2) 0 0;
            font-size: var(--t-small);
            color: var(--muted);
            line-height: 1.5;
        }
        .rc-sep { color: var(--gold); padding: 0 var(--s1); }

        /* Document title */
        .rc-title-wrap {
            display: flex;
            align-items: center;
            gap: var(--s3);
            margin-top: var(--s1);
        }
        .rc-title-line { flex: 1; height: var(--rule); background: var(--line); }
        .rc-title {
            margin: 0;
            padding: 6px 28px;
            background: var(--navy);
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 4px;
            text-transform: uppercase;
            border-radius: 2px;
            border-bottom: var(--rule-strong) solid var(--gold);
        }

        /* =========================================================
           SECTION CARD  (the one reusable container)
           Every section: info, performance, attendance, remarks,
           signatures all use this same frame.
           ========================================================= */
        .rc-card {
            display: flex;
            flex-direction: column;
            border: var(--rule) solid var(--line);
            border-radius: var(--radius);
            background: #fff;
            overflow: hidden;
            break-inside: avoid;
            page-break-inside: avoid;
        }
        .rc-card-head {
            padding: 6px var(--s3);
            background: var(--navy);
            border-bottom: var(--rule-strong) solid var(--gold);
            color: #fff;
            font-size: var(--t-small);
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .rc-card-body { flex: 1; padding: var(--s3); }
        .rc-card-body.flush { padding: 0; }

        /* Shared label/value pair */
        .rc-label {
            display: block;
            font-size: var(--t-label);
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--muted);
        }
        .rc-value {
            display: block;
            margin-top: 2px;
            font-size: var(--t-medium);
            font-weight: 600;
            color: var(--ink);
        }

        /* Hairline grid (student info + summary stats share this) */
        .rc-info,
        .rc-stats {
            display: grid;
            gap: var(--rule);
            background: var(--line);
        }
        .rc-info { grid-template-columns: repeat(3, 1fr); }
        .rc-stats {
            grid-template-columns: repeat(4, 1fr);
            border: var(--rule) solid var(--line);
            border-radius: var(--radius);
            overflow: hidden;
            break-inside: avoid;
        }
        .rc-info-cell { padding: var(--s2) var(--s3); background: #fff; }

        /* Summary stats */
        .rc-stat {
            padding: 10px var(--s3);
            background: var(--soft);
            text-align: center;
        }
        .rc-stat-value {
            display: block;
            font-family: var(--font-serif);
            font-size: var(--t-stat);
            font-weight: 700;
            line-height: 1.1;
            color: var(--navy);
        }
        .rc-stat-label {
            display: block;
            margin-top: 3px;
            font-size: var(--t-label);
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--muted);
        }

        /* =========================================================
           GRADES TABLE
           ========================================================= */
        .rc-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            font-variant-numeric: tabular-nums;
        }
        .rc-table thead { display: table-header-group; }
        .rc-table th {
            padding: var(--s2);
            background: var(--navy-tint);
            color: var(--navy);
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            text-align: center;
            border-bottom: var(--rule-strong) solid var(--navy);
            border-right: var(--rule) solid var(--line);
        }
        .rc-table th:first-child { text-align: left; padding-left: var(--s3); width: 28%; }
        .rc-table th.rc-col-total { width: 11%; }
        .rc-table th.rc-col-grade { width: 11%; }
        .rc-table th:last-child { border-right: 0; }
        .rc-th-sub {
            display: block;
            margin-top: 1px;
            font-size: var(--t-label);
            font-weight: 400;
            letter-spacing: 0;
            text-transform: none;
            color: var(--muted);
        }
        .rc-table td {
            padding: 7px var(--s2);
            font-size: var(--t-base);
            text-align: center;
            color: var(--body);
            border-bottom: var(--rule) solid var(--line);
            border-right: var(--rule) solid var(--line);
        }
        .rc-table td:last-child { border-right: 0; }
        .rc-table td.rc-subject {
            text-align: left;
            padding-left: var(--s3);
            font-weight: 600;
            color: var(--navy);
        }
        .rc-table td.rc-total { font-weight: 700; color: var(--ink); }
        .rc-table tbody tr { page-break-inside: avoid; }
        .rc-table tbody tr:nth-child(even) td { background: #f8f9fc; }
        .rc-table tfoot td {
            padding: var(--s2);
            background: var(--navy-tint);
            border-top: var(--rule-strong) solid var(--navy);
            border-bottom: 0;
            font-weight: 700;
            color: var(--navy);
        }
        .rc-table tfoot td.rc-foot-label {
            text-align: right;
            padding-right: var(--s3);
            font-size: var(--t-small);
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .rc-empty {
            padding: var(--s5);
            text-align: center;
            font-style: italic;
            color: var(--faint);
        }

        /* Grade badges (single standard size) */
        .rc-badge {
            display: inline-block;
            min-width: 32px;
            padding: 3px var(--s2);
            border: var(--rule) solid transparent;
            border-radius: var(--radius);
            font-size: 11px;
            font-weight: 700;
            text-align: center;
            line-height: 1.3;
        }
        .rc-badge-a { background: #d1fae5; color: #047857; border-color: #a7f3d0; }
        .rc-badge-b { background: #e0f2fe; color: #0369a1; border-color: #bae6fd; }
        .rc-badge-c { background: #fef3c7; color: #b45309; border-color: #fde68a; }
        .rc-badge-d { background: #ffedd5; color: #c2410c; border-color: #fed7aa; }
        .rc-badge-f { background: #fee2e2; color: #b91c1c; border-color: #fecaca; }
        .rc-badge-x { background: #f3f4f6; color: #4b5563; border-color: #e5e7eb; }

        /* =========================================================
           ATTENDANCE + REMARKS
           ========================================================= */
        .rc-two-col { display: grid; gap: var(--s4); align-items: stretch; }
        .rc-two-col > .rc-card { height: 100%; }
        .rc-card-body.rc-min { min-height: 92px; }

        .rc-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            font-size: var(--t-base);
            border-bottom: 1px dashed var(--line);
        }
        .rc-row span:first-child { color: var(--muted); }
        .rc-row span:last-child { font-weight: 700; color: var(--navy); }
        .rc-rate {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-top: 6px;
            font-size: var(--t-base);
            font-weight: 700;
            color: var(--navy);
        }
        .rc-rate strong { font-size: var(--t-large); color: var(--gold-text); }
        .rc-bar {
            margin-top: 5px;
            height: 6px;
            background: #e5e7eb;
            border-radius: 99px;
            overflow: hidden;
        }
        .rc-bar > div { height: 100%; background: var(--navy); border-radius: 99px; }

        .rc-remarks {
            font-size: var(--t-base);
            line-height: 1.6;
            color: var(--body);
            white-space: pre-line;
        }
        .rc-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 70px;
            height: 100%;
            font-size: var(--t-base);
            font-style: italic;
            text-align: center;
            color: var(--faint);
        }

        /* =========================================================
           SIGNATURES
           ========================================================= */
        .rc-sign-grid { display: grid; gap: 28px; padding: var(--s1) var(--s2) 0; }
        .rc-sign { text-align: center; }
        .rc-sign-space {
            height: 46px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding-bottom: 2px;
        }
        .rc-sign-space img { max-height: 42px; max-width: 100%; object-fit: contain; }
        .rc-sign-line { border-top: 1.5px solid var(--navy); padding-top: var(--s1); }
        .rc-sign-name { margin: 0; font-size: var(--t-base); font-weight: 700; color: var(--navy); }
        .rc-sign-role {
            margin: 1px 0 0;
            font-size: var(--t-label);
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--muted);
        }

        /* =========================================================
           FOOTER
           ========================================================= */
        .rc-footer {
            margin-top: auto;
            padding-top: var(--s2);
            border-top: var(--rule-strong) solid var(--navy);
            text-align: center;
            font-size: 9px;
            line-height: 1.6;
            color: var(--muted);
        }
        .rc-footer strong { color: var(--navy); }
    </style>
</head>
<body>
@php
    $configuration = $reportCard->configuration;
    $template = $configuration?->template;
    $sections = $template ? $template->resolvedSections() : \App\Models\ReportCardTemplate::defaultSections();

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
        if (str_starts_with($l, 'A')) return 'rc-badge rc-badge-a';
        if (str_starts_with($l, 'B')) return 'rc-badge rc-badge-b';
        if (str_starts_with($l, 'C')) return 'rc-badge rc-badge-c';
        if (str_starts_with($l, 'D')) return 'rc-badge rc-badge-d';
        if ($l === 'F') return 'rc-badge rc-badge-f';
        return 'rc-badge rc-badge-x';
    };
    $overallTotal = $displayGrades->avg('total_score');
@endphp

<div class="rc-page">

    <div class="rc-topbar"></div>
    <div class="rc-topbar-gold"></div>

    @if($sections['header']['show_logo'] ?? true)
        <img src="{{ $logoUrl }}" alt="" class="rc-watermark">
    @endif

    <div class="rc-body">

        {{-- LETTERHEAD --}}
        <div class="rc-header">
            @if($sections['header']['show_logo'] ?? true)
                <img src="{{ $logoUrl }}" alt="{{ $school->name }}" class="rc-logo">
            @endif
            <div class="rc-header-text">
                @if($sections['header']['show_school_name'] ?? true)
                    <h1 class="rc-school">{{ $school->name }}</h1>
                @endif
                @if(($sections['header']['show_tagline'] ?? true) && !empty($sections['header']['tagline']))
                    <p class="rc-tagline">{{ $sections['header']['tagline'] }}</p>
                @endif
                @if($sections['header']['show_contact'] ?? true)
                    <p class="rc-contact">
                        {{ $school->address }}, {{ $school->city }}, {{ $school->country }}
                        <span class="rc-sep">&#9670;</span>
                        {{ $school->phone }}
                        <span class="rc-sep">&#9670;</span>
                        {{ $school->email }}
                        @if($school->website)
                            <span class="rc-sep">&#9670;</span> {{ $school->website }}
                        @endif
                    </p>
                @endif
            </div>
            @if($sections['header']['show_logo'] ?? true)
                {{-- Balance spacer keeps the school name optically centred --}}
                <div style="width:72px; flex-shrink:0;"></div>
            @endif
        </div>

        {{-- TITLE --}}
        <div class="rc-title-wrap">
            <div class="rc-title-line"></div>
            <h2 class="rc-title">Student Report Card</h2>
            <div class="rc-title-line"></div>
        </div>

        {{-- STUDENT INFORMATION --}}
        <section class="rc-card">
            <div class="rc-card-head">Student Information</div>
            <div class="rc-card-body flush">
                <div class="rc-info">
                    <div class="rc-info-cell">
                        <span class="rc-label">Student Name</span>
                        <span class="rc-value">{{ $reportCard->student->user->name }}</span>
                    </div>
                    <div class="rc-info-cell">
                        <span class="rc-label">Student ID</span>
                        <span class="rc-value">{{ $reportCard->student->student_id }}</span>
                    </div>
                    <div class="rc-info-cell">
                        <span class="rc-label">Class / Level</span>
                        <span class="rc-value">{{ $reportCard->student->academicLevel->name }}</span>
                    </div>
                    <div class="rc-info-cell">
                        <span class="rc-label">Academic Year</span>
                        <span class="rc-value">{{ $reportCard->configuration->academicPeriod->academic_year }}</span>
                    </div>
                    <div class="rc-info-cell">
                        <span class="rc-label">Term / Period</span>
                        <span class="rc-value">{{ $reportCard->term }}</span>
                    </div>
                    <div class="rc-info-cell">
                        <span class="rc-label">Report Date</span>
                        <span class="rc-value">{{ $reportCard->generated_at->format('F d, Y') }}</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- ACADEMIC PERFORMANCE --}}
        @if($sections['grades_table']['enabled'] ?? true)

            @if($displayGrades->isNotEmpty())
                {{-- Summary strip --}}
                <div class="rc-stats">
                    <div class="rc-stat">
                        <span class="rc-stat-value">{{ number_format($overallTotal, 1) }}</span>
                        <span class="rc-stat-label">Overall Average</span>
                    </div>
                    <div class="rc-stat">
                        <span class="rc-stat-value">{{ $fallbackGrade($overallTotal) }}</span>
                        <span class="rc-stat-label">Overall Grade</span>
                    </div>
                    <div class="rc-stat">
                        <span class="rc-stat-value">{{ $displayGrades->count() }}</span>
                        <span class="rc-stat-label">Subjects</span>
                    </div>
                    <div class="rc-stat">
                        <span class="rc-stat-value">
                            @if($reportCard->attendance_total_days)
                                {{ $reportCard->attendancePercentage() }}%
                                @else
                                    &mdash;
                            @endif
                        </span>
                        <span class="rc-stat-label">Attendance</span>
                    </div>
                </div>
            @endif

            <section class="rc-card" style="break-inside:auto; page-break-inside:auto;">
                <div class="rc-card-head">Academic Performance</div>
                <div class="rc-card-body flush">
                    @if($displayGrades->isNotEmpty())
                        <table class="rc-table">
                            <thead>
                            <tr>
                                <th>Subject</th>
                                @foreach($weightingColumns as $weighting)
                                    <th>
                                        {{ $weighting->name }}
                                        <span class="rc-th-sub">({{ rtrim(rtrim(number_format((float)$weighting->weight_percentage, 2, '.', ''), '0'), '.') }}%)</span>
                                    </th>
                                @endforeach
                                <th class="rc-col-total">
                                    Total
                                    <span class="rc-th-sub">(100)</span>
                                </th>
                                <th class="rc-col-grade">Grade</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($displayGrades as $grade)
                                @php $gradeLabel = $grade->grade_label ?: $fallbackGrade($grade->total_score); $scores = (array) ($grade->scores ?? []); @endphp
                                <tr>
                                    <td class="rc-subject">{{ $grade->subject->name }}</td>
                                    @foreach($weightingColumns as $weighting)
                                        @php $key = $weighting->score_key ?: \Illuminate\Support\Str::slug($weighting->name, '_'); @endphp
                                        <td>{{ number_format((float)($scores[$key] ?? 0), 1) }}</td>
                                    @endforeach
                                    <td class="rc-total">{{ number_format($grade->total_score, 1) }}</td>
                                    <td><span class="{{ $gradeBadgeClass($gradeLabel) }}">{{ $gradeLabel }}</span></td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td class="rc-foot-label" colspan="{{ 1 + $weightingColumns->count() }}">Overall Average</td>
                                <td>{{ number_format($overallTotal, 1) }}</td>
                                <td><span class="{{ $gradeBadgeClass($fallbackGrade($overallTotal)) }}">{{ $fallbackGrade($overallTotal) }}</span></td>
                            </tr>
                            </tfoot>
                        </table>
                    @else
                        <div class="rc-empty">No grades recorded for this report card.</div>
                    @endif
                </div>
            </section>
        @endif

        {{-- ATTENDANCE + REMARKS --}}
        @if(($sections['attendance']['enabled'] ?? true) || ($sections['remarks']['enabled'] ?? true))
            <div class="rc-two-col" style="grid-template-columns: {{ (($sections['attendance']['enabled'] ?? true) && ($sections['remarks']['enabled'] ?? true)) ? '5fr 7fr' : '1fr' }};">

                @if($sections['attendance']['enabled'] ?? true)
                    <section class="rc-card">
                        <div class="rc-card-head">{{ $sections['attendance']['label'] ?? 'Attendance' }}</div>
                        <div class="rc-card-body rc-min">
                            @if($reportCard->attendance_total_days)
                                <div class="rc-row"><span>Total School Days</span><span>{{ $reportCard->attendance_total_days }}</span></div>
                                <div class="rc-row"><span>Days Present</span><span>{{ $reportCard->attendance_days_present }}</span></div>
                                <div class="rc-row"><span>Days Absent</span><span>{{ $reportCard->attendanceDaysAbsent() }}</span></div>
                                <div class="rc-rate">
                                    <span>Attendance Rate</span>
                                    <strong>{{ $reportCard->attendancePercentage() }}%</strong>
                                </div>
                                <div class="rc-bar"><div style="width: {{ min(100, $reportCard->attendancePercentage()) }}%;"></div></div>
                            @else
                                <div class="rc-placeholder">Attendance not recorded</div>
                            @endif
                        </div>
                    </section>
                @endif

                @if($sections['remarks']['enabled'] ?? true)
                    <section class="rc-card">
                        <div class="rc-card-head">{{ $sections['remarks']['label'] ?? "Class Teacher's Remarks" }}</div>
                        <div class="rc-card-body rc-min">
                            @if(!empty($reportCard->teacher_remarks))
                                <div class="rc-remarks">{{ $reportCard->teacher_remarks }}</div>
                            @else
                                <div class="rc-placeholder">No remarks provided</div>
                            @endif
                        </div>
                    </section>
                @endif

            </div>
        @endif

        {{-- SIGNATURES --}}
        @php $slots = $sections['signatures']['slots'] ?? []; @endphp
        @if(!empty($slots))
            <section class="rc-card">
                <div class="rc-card-head">Signatures</div>
                <div class="rc-card-body">
                    <div class="rc-sign-grid" style="grid-template-columns: repeat({{ count($slots) }}, 1fr);">
                        @foreach($slots as $slot)
                            @php
                                $sigUri = match($slot['key']) {
                                    'class_teacher' => $classTeacherSigUri ?? '',
                                    'principal' => $principalSigUri ?? '',
                                    default => '',
                                };
                                $sigName = match($slot['key']) {
                                    'class_teacher' => $configuration?->class_teacher_name,
                                    'principal' => $configuration?->principal_name,
                                    default => null,
                                };
                            @endphp
                            <div class="rc-sign">
                                <div class="rc-sign-space">
                                    @if(!empty($sigUri))
                                        <img src="{{ $sigUri }}" alt="{{ $slot['label'] }} Signature">
                                    @endif
                                </div>
                                <div class="rc-sign-line">
                                    <p class="rc-sign-name">{{ $sigName ?: $slot['label'] }}</p>
                                    <p class="rc-sign-role">{{ $slot['label'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- FOOTER --}}
        @if($sections['footer']['enabled'] ?? true)
            <div class="rc-footer">
                <strong>{{ $school->name }}</strong>
                <span class="rc-sep">&#9670;</span>
                {{ $sections['footer']['text'] ?? 'This is an official document. Any alteration renders it invalid.' }}
                <span class="rc-sep">&#9670;</span>
                Generated: {{ $reportCard->generated_at->format('d M Y, H:i') }}
            </div>
        @endif

    </div>
</div>
</body>
</html>
