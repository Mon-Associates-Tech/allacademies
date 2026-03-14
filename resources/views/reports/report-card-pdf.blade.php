<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report Card - {{ $reportCard->student->user->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; line-height: 1.4; color: #333; }
        .container { padding: 20px; }
        
        /* Header */
        .header { text-align: center; border-bottom: 3px solid #2563eb; padding-bottom: 15px; margin-bottom: 20px; }
        .school-logo { width: 60px; height: 60px; margin: 0 auto 10px; }
        .school-name { font-size: 20px; font-weight: bold; color: #1e40af; margin-bottom: 5px; }
        .school-info { font-size: 9px; color: #666; }
        .report-title { font-size: 16px; font-weight: bold; margin-top: 10px; color: #1e40af; }
        
        /* Student Info */
        .student-info { display: table; width: 100%; margin-bottom: 20px; border: 1px solid #ddd; }
        .info-row { display: table-row; }
        .info-label { display: table-cell; padding: 8px; background: #f3f4f6; font-weight: bold; width: 25%; border: 1px solid #ddd; }
        .info-value { display: table-cell; padding: 8px; border: 1px solid #ddd; }
        
        /* Grades Table */
        .grades-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .grades-table th { background: #2563eb; color: white; padding: 8px; text-align: left; font-size: 10px; border: 1px solid #1e40af; }
        .grades-table td { padding: 6px 8px; border: 1px solid #ddd; }
        .grades-table tr:nth-child(even) { background: #f9fafb; }
        .remarks-row td { background: #f3f4f6; font-style: italic; font-size: 9px; }
        
        /* Summary */
        .summary { display: table; width: 100%; margin-bottom: 20px; }
        .summary-item { display: table-cell; padding: 10px; text-align: center; border: 1px solid #ddd; background: #f9fafb; }
        .summary-label { font-size: 9px; color: #666; margin-bottom: 5px; }
        .summary-value { font-size: 16px; font-weight: bold; color: #1e40af; }
        
        /* Signatures */
        .signatures { display: table; width: 100%; margin-top: 30px; }
        .signature { display: table-cell; text-align: center; padding: 10px; }
        .signature-line { border-top: 1px solid #333; margin-top: 40px; padding-top: 5px; font-size: 10px; }
        
        /* Footer */
        .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 2px solid #ddd; font-size: 9px; color: #666; }
        
        .grade-badge { display: inline-block; padding: 3px 8px; background: #dbeafe; color: #1e40af; border-radius: 3px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            @if($school->logo)
                <img src="{{ public_path('storage/' . $school->logo) }}" alt="{{ $school->name }}" class="school-logo">
            @endif
            <div class="school-name">{{ $school->name }}</div>
            <div class="school-info">
                {{ $school->address }}, {{ $school->city }}, {{ $school->country }}<br>
                Tel: {{ $school->phone }} | Email: {{ $school->email }}
                @if($school->website) | {{ $school->website }} @endif
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
                    <th style="width: 12%;">Class Score</th>
                    <th style="width: 12%;">Test Score</th>
                    <th style="width: 12%;">Exam Score</th>
                    <th style="width: 12%;">Total (100)</th>
                    <th style="width: 10%;">Grade</th>
                    <th style="width: 17%;">Position</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportCard->grades as $grade)
                    <tr>
                        <td><strong>{{ $grade->subject->name }}</strong></td>
                        <td>{{ number_format($grade->scores['class_score'] ?? 0, 1) }}</td>
                        <td>{{ number_format($grade->scores['test_score'] ?? 0, 1) }}</td>
                        <td>{{ number_format($grade->scores['exam_score'] ?? 0, 1) }}</td>
                        <td><strong>{{ number_format($grade->total_score, 1) }}</strong></td>
                        <td><span class="grade-badge">{{ $grade->grade_label }}</span></td>
                        <td>-</td>
                    </tr>
                    @if($grade->remarks)
                        <tr class="remarks-row">
                            <td colspan="7">Remarks: {{ $grade->remarks }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary">
            <div class="summary-item">
                <div class="summary-label">Total Subjects</div>
                <div class="summary-value">{{ $reportCard->grades->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Score</div>
                <div class="summary-value">{{ number_format($reportCard->grades->sum('total_score'), 1) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Average Score</div>
                <div class="summary-value">{{ number_format($reportCard->grades->avg('total_score'), 1) }}%</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Overall Grade</div>
                <div class="summary-value">
                    @php
                        $avg = $reportCard->grades->avg('total_score');
                        $overallGrade = $avg >= 90 ? 'A+' : ($avg >= 80 ? 'A' : ($avg >= 70 ? 'B+' : ($avg >= 60 ? 'B' : ($avg >= 50 ? 'C' : 'D'))));
                    @endphp
                    {{ $overallGrade }}
                </div>
            </div>
        </div>

        <!-- Grading Scale -->
        <div style="margin-bottom: 20px; padding: 10px; background: #f9fafb; border: 1px solid #ddd;">
            <strong>Grading Scale:</strong> 
            A+ (90-100) | A (80-89) | B+ (70-79) | B (60-69) | C (50-59) | D (40-49) | F (0-39)
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature">
                <div class="signature-line">Class Teacher</div>
            </div>
            <div class="signature">
                <div class="signature-line">Head of School</div>
            </div>
            <div class="signature">
                <div class="signature-line">Parent/Guardian</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            This is an official document from {{ $school->name }}. Generated on {{ now()->format('F d, Y \a\t h:i A') }}
        </div>
    </div>
</body>
</html>
