<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report Card - {{ $reportCard->student->user->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10.5px; line-height: 1.3; color: #333; }
        .container { width: 100%; }
        
        /* Header */
        .header { text-align: center; border-bottom: 2px solid #2563eb; padding-bottom: 8px; margin-bottom: 10px; }
        .header-top { display: table; width: 100%; margin-bottom: 4px; }
        .header-logo { display: table-cell; width: 56px; vertical-align: middle; text-align: left; }
        .header-meta { display: table-cell; vertical-align: middle; text-align: left; padding-left: 8px; }
        .school-logo { width: 44px; height: 44px; object-fit: contain; }
        .school-name { font-size: 14px; font-weight: bold; color: #1e40af; margin-bottom: 3px; }
        .school-info { font-size: 8.5px; color: #666; }
        .report-title { font-size: 12px; font-weight: bold; margin-top: 6px; color: #1e40af; text-align: center; }
        
        /* Student Info */
        .student-info { display: table; width: 100%; margin-bottom: 10px; border: 1px solid #ddd; }
        .info-row { display: table-row; }
        .info-label { display: table-cell; padding: 5px 6px; background: #f3f4f6; font-weight: bold; width: 25%; border: 1px solid #ddd; }
        .info-value { display: table-cell; padding: 5px 6px; border: 1px solid #ddd; }
        
        /* Grades Table */
        .grades-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .grades-table th { background: #2563eb; color: white; padding: 5px; text-align: left; font-size: 8.5px; border: 1px solid #1e40af; }
        .grades-table td { padding: 4px 5px; border: 1px solid #ddd; font-size: 8.5px; }
        .grades-table tr:nth-child(even) { background: #f9fafb; }
        
        /* Signatures */
        .signatures { display: table; width: 100%; margin-top: 28px; }
        .signature { display: table-cell; text-align: center; padding: 4px; }
        .signature-line { border-top: 1px solid #333; margin-top: 20px; padding-top: 4px; font-size: 8.5px; }
        
        .grade-badge { display: inline-block; padding: 2px 4px; background: #dbeafe; color: #1e40af; border-radius: 2px; font-weight: bold; font-size: 8px; }
    </style>
</head>
<body>
    <div class="container">
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
                    (object) ['name' => 'Test Score', 'score_key' => 'test_score', 'weight_percentage' => 10, 'max_score' => 10],
                    (object) ['name' => 'Exam Score', 'score_key' => 'exam_score', 'weight_percentage' => 50, 'max_score' => 50],
                ]);
            }

            $assignedSubjectIds = $reportCard->student->individualSubjects()
                ->wherePivot('is_active', true)
                ->pluck('academic_subjects.id');

            $displayGrades = $reportCard->grades
                ->when($assignedSubjectIds->isNotEmpty(), fn ($collection) => $collection->whereIn('subject_id', $assignedSubjectIds))
                ->sortBy(fn ($grade) => strtolower((string) ($grade->subject->name ?? '')))
                ->values();

            if ($displayGrades->isEmpty()) {
                $displayGrades = $reportCard->grades
                    ->sortBy(fn ($grade) => strtolower((string) ($grade->subject->name ?? '')))
                    ->values();
            }

            $displayGrades = $displayGrades->unique('subject_id')->values();

            $configuredMax = (int) ($configuration?->max_subjects ?: 10);
            $renderSubjectLimit = max(1, min($configuredMax, 10));
            $displayGrades = $displayGrades->take($renderSubjectLimit)->values();
            $emptyRows = max(0, 10 - $displayGrades->count());

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
                $ordered = $rows->sortByDesc('total_score')->values();
                $rank = 0;
                $index = 0;
                $lastScore = null;
                foreach ($ordered as $row) {
                    $index++;
                    if ($lastScore === null || (float) $row->total_score !== (float) $lastScore) {
                        $rank = $index;
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
                    ($score >= 80 ? 'A' :
                        ($score >= 70 ? 'B+' :
                            ($score >= 60 ? 'B' :
                                ($score >= 50 ? 'C' :
                                    ($score >= 40 ? 'D' : 'F')))));
            };

            $resolveScore = function ($grade, $weighting) {
                $scoreKey = $weighting->score_key ?? \Illuminate\Support\Str::slug((string) ($weighting->name ?? ''), '_');
                $scores = (array) ($grade->scores ?? []);

                if (array_key_exists($scoreKey, $scores)) {
                    return (float) $scores[$scoreKey];
                }
                if (isset($weighting->name) && array_key_exists($weighting->name, $scores)) {
                    return (float) $scores[$weighting->name];
                }

                $key = strtolower((string) $scoreKey);
                if (str_contains($key, 'class') || str_contains($key, 'assess')) {
                    return (float) ($grade->assessments_score ?? 0);
                }
                if (str_contains($key, 'quiz') || str_contains($key, 'test')) {
                    return (float) ($grade->quizzes_score ?? 0);
                }
                if (str_contains($key, 'exam')) {
                    return (float) ($grade->final_exam_score ?? 0);
                }

                return 0.0;
            };
        @endphp

        <!-- Header -->
        <div class="header">
            <div class="header-top">
                <div class="header-logo">
                    <img src="{{ $logoPath }}" alt="{{ $school->name }}" class="school-logo">
                </div>
                <div class="header-meta">
                    <div class="school-name">{{ $school->name }}</div>
                    <div class="school-info">
                        {{ $school->address }}, {{ $school->city }}, {{ $school->country }}<br>
                        Tel: {{ $school->phone }} | Email: {{ $school->email }}
                        @if($school->website) | {{ $school->website }} @endif
                    </div>
                </div>
            </div>
            <div class="report-title">STUDENT REPORT CARD</div>
        </div>

        <!-- Student Information -->
        <div class="student-info">
            <div class="info-row">
                <div class="info-label">Student Name</div>
                <div class="info-value">{{ $reportCard->student->user->name }}</div>
                <div class="info-label">Student ID</div>
                <div class="info-value">{{ $reportCard->student->student_id }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Class/Level</div>
                <div class="info-value">{{ $reportCard->student->academicLevel->name }}</div>
                <div class="info-label">Academic Year</div>
                <div class="info-value">{{ $reportCard->configuration->academicPeriod->academic_year }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Term/Period</div>
                <div class="info-value">{{ $reportCard->term }}</div>
                <div class="info-label">Report Date</div>
                <div class="info-value">{{ $reportCard->generated_at->format('F d, Y') }}</div>
            </div>
        </div>

        <!-- Grades Table -->
        <table class="grades-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Subject</th>
                    @foreach($weightings as $weighting)
                        <th style="width: 12%;">
                            {{ $weighting->name }}<br>
                            ({{ rtrim(rtrim(number_format((float) $weighting->weight_percentage, 2, '.', ''), '0'), '.') }}%)
                        </th>
                    @endforeach
                    <th style="width: 12%;">Total (100)</th>
                    <th style="width: 10%;">Grade</th>
                    <th style="width: 17%;">Position</th>
                </tr>
            </thead>
            <tbody>
                @foreach($displayGrades as $grade)
                    <tr>
                        <td><strong>{{ $grade->subject->name }}</strong></td>
                        @foreach($weightings as $weighting)
                            <td>{{ number_format($resolveScore($grade, $weighting), 1) }}</td>
                        @endforeach
                        <td><strong>{{ number_format($grade->total_score, 1) }}</strong></td>
                        <td><span class="grade-badge">{{ $grade->grade_label ?: $fallbackGrade($grade->total_score) }}</span></td>
                        <td>
                            @if(isset($subjectPositions[$grade->subject_id]) && $classStudentCount > 0)
                                {{ $subjectPositions[$grade->subject_id] }}/{{ $classStudentCount }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
                @for($i = 0; $i < $emptyRows; $i++)
                    <tr>
                        <td>&nbsp;</td>
                        @foreach($weightings as $weighting)
                            <td>&nbsp;</td>
                        @endforeach
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                @endfor
            </tbody>
        </table>

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature">
                @if(!empty($configuration?->class_teacher_signature_path))
                    <img src="{{ public_path('storage/' . $configuration->class_teacher_signature_path) }}" style="height: 20px;" alt="Class Teacher Signature">
                @endif
                <div class="signature-line">{{ $configuration?->class_teacher_name ?: 'Class Teacher' }}</div>
            </div>
            <div class="signature">
                @if(!empty($configuration?->principal_signature_path))
                    <img src="{{ public_path('storage/' . $configuration->principal_signature_path) }}" style="height: 20px;" alt="Principal Signature">
                @endif
                <div class="signature-line">{{ $configuration?->principal_name ?: 'Head of School' }}</div>
            </div>
            <div class="signature">
                <div class="signature-line">Parent/Guardian</div>
            </div>
        </div>
    </div>
</body>
</html>
