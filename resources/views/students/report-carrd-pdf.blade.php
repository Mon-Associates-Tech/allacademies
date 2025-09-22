<!DOCTYPE html>
<html>
<head>
    <title>Report Card - {{ $reportCard->student->user->name }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .school-name {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }
        .report-title {
            font-size: 16px;
            margin: 5px 0;
        }
        .student-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .info-box {
            border: 1px solid #333;
            padding: 10px;
            width: 48%;
        }
        .info-box h3 {
            margin-top: 0;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .grades-table th, .grades-table td {
            text-align: center;
        }
        .grades-table td:first-child, .grades-table th:first-child {
            text-align: left;
        }
        .footer {
            margin-top: 30px;
        }
        .signature {
            width: 30%;
            float: left;
            text-align: center;
            margin-right: 3%;
        }
        .signature-line {
            margin-top: 40px;
            border-top: 1px solid #333;
        }
    </style>
</head>
<body>
    <div class="header">
        <p class="school-name">{{ $reportCard->school->name ?? 'School Name' }}</p>
        <p class="report-title">STUDENT REPORT CARD</p>
        <p>Academic Year: {{ $reportCard->academicYear->name ?? 'N/A' }} | Term: {{ $reportCard->term }}</p>
    </div>

    <div class="student-info">
        <div class="info-box">
            <h3>Student Information</h3>
            <p><strong>Name:</strong> {{ $reportCard->student->user->name }}</p>
            <p><strong>Student ID:</strong> {{ $reportCard->student->student_id ?? 'N/A' }}</p>
            <p><strong>Academic Level:</strong> {{ $reportCard->student->academicLevel->name ?? 'N/A' }}</p>
        </div>

        <div class="info-box">
            <h3>Class Information</h3>
            <p><strong>Class:</strong> {{ $reportCard->student->studentGroup->name ?? 'N/A' }}</p>
            <p><strong>Class Teacher:</strong> {{ $reportCard->student->primaryTeacher->user->name ?? 'N/A' }}</p>
            <p><strong>Report Date:</strong> {{ $reportCard->generated_at ? $reportCard->generated_at->format('M d, Y') : now()->format('M d, Y') }}</p>
        </div>
    </div>

    <table class="grades-table">
        <thead>
            <tr>
                <th>Subject</th>
                <th>Assessments (10%)</th>
                <th>Quizzes/Mocks (30%)</th>
                <th>Final Exam (60%)</th>
                <th>Total Score</th>
                <th>Grade</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportCard->grades as $grade)
            <tr>
                <td>{{ $grade->subject->name ?? 'N/A' }}</td>
                <td>{{ $grade->assessments_score ?? 'N/A' }}</td>
                <td>{{ $grade->quizzes_score ?? 'N/A' }}</td>
                <td>{{ $grade->final_exam_score ?? 'N/A' }}</td>
                <td>{{ $grade->total_score ?? 'N/A' }}</td>
                <td>{{ $grade->grade_label ?? 'N/A' }}</td>
                <td>{{ $grade->remarks ?? 'N/A' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">No grades available</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">
            <p>Class Teacher</p>
            <div class="signature-line"></div>
            <p>{{ $reportCard->student->primaryTeacher->user->name ?? 'N/A' }}</p>
        </div>

        <div class="signature">
            <p>Principal</p>
            <div class="signature-line"></div>
            <p>________________________</p>
        </div>

        <div class="signature">
            <p>Parent/Guardian</p>
            <div class="signature-line"></div>
            <p>________________________</p>
        </div>
    </div>
</body>
</html>

