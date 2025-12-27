<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report Card - {{ $reportCard->student->user->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.4; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .mt-4 { margin-top: 20px; }
        .signature-section { margin-top: 40px; }
        .signature-box { display: inline-block; width: 30%; text-align: center; }
        .signature-line { border-top: 1px solid #000; padding-top: 5px; margin-top: 40px; }
    </style>
</head>
<body>
<!-- Include selected letterhead -->
@include('components.letterheads.' . ($reportCard->school->letterhead_template ?? 'classic'), [
    'school' => $reportCard->school,
    'title' => 'Student Report Card - ' . $reportCard->term
])

<!-- Report Card Content -->
<div style="padding: 0 20px 20px;">
    <!-- Student Information -->
    <h3 style="margin: 20px 0 10px; color: #1f2937;">Student Information</h3>
    <table>
        <tr>
            <th style="width: 25%;">Student Name:</th>
            <td style="width: 25%;">{{ $reportCard->student->user->name }}</td>
            <th style="width: 25%;">Student ID:</th>
            <td style="width: 25%;">{{ $reportCard->student->student_id }}</td>
        </tr>
        <tr>
            <th>Academic Level:</th>
            <td>{{ $reportCard->student->academicLevel->name ?? 'N/A' }}</td>
            <th>Academic Year:</th>
            <td>{{ $reportCard->academicYear->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Class/Group:</th>
            <td>{{ $reportCard->student->studentGroup->name ?? 'N/A' }}</td>
            <th>Term:</th>
            <td>{{ $reportCard->term }}</td>
        </tr>
        <tr>
            <th>Class Teacher:</th>
            <td>{{ $reportCard->student->primaryTeacher->user->name ?? 'N/A' }}</td>
            <th>Date Generated:</th>
            <td>{{ $reportCard->generated_at->format('F d, Y') }}</td>
        </tr>
    </table>

    <!-- Grades -->
    <h3 style="margin: 25px 0 10px; color: #1f2937;">Academic Performance</h3>
    <table>
        <thead>
        <tr style="background-color: #3b82f6; color: white;">
            <th>Subject</th>
            <th style="text-align: center;">Assessments<br>(10%)</th>
            <th style="text-align: center;">Quizzes<br>(30%)</th>
            <th style="text-align: center;">Final Exam<br>(60%)</th>
            <th style="text-align: center;">Total<br>(100%)</th>
            <th style="text-align: center;">Grade</th>
            <th>Remarks</th>
        </tr>
        </thead>
        <tbody>
        @php
            $totalScore = 0;
            $subjectCount = 0;
        @endphp
        @foreach($reportCard->grades as $grade)
            @php
                $totalScore += $grade->total_score;
                $subjectCount++;
            @endphp
            <tr>
                <td>{{ $grade->subject->name }}</td>
                <td style="text-align: center;">{{ number_format($grade->assessments_score, 1) }}</td>
                <td style="text-align: center;">{{ number_format($grade->quizzes_score, 1) }}</td>
                <td style="text-align: center;">{{ number_format($grade->final_exam_score, 1) }}</td>
                <td style="text-align: center; font-weight: bold; background-color: #fef3c7;">
                    {{ number_format($grade->total_score, 1) }}
                </td>
                <td style="text-align: center; font-weight: bold; font-size: 14px;
                            @if($grade->grade_label === 'A+' || $grade->grade_label === 'A') color: #059669;
                            @elseif($grade->grade_label === 'B+' || $grade->grade_label === 'B') color: #0284c7;
                            @elseif($grade->grade_label === 'C') color: #f59e0b;
                            @else color: #dc2626;
                            @endif">
                    {{ $grade->grade_label }}
                </td>
                <td style="font-size: 11px;">{{ $grade->remarks ?: '-' }}</td>
            </tr>
        @endforeach
        @if($subjectCount > 0)
            <tr style="background-color: #dbeafe; font-weight: bold;">
                <td colspan="4" style="text-align: right; padding-right: 10px;">AVERAGE:</td>
                <td style="text-align: center; font-size: 14px;">
                    {{ number_format($totalScore / $subjectCount, 1) }}%
                </td>
                <td colspan="2"></td>
            </tr>
        @endif
        </tbody>
    </table>

    <!-- Grading Scale -->
    <div style="margin-top: 15px; padding: 10px; background-color: #f3f4f6; border-radius: 4px;">
        <strong>Grading Scale:</strong>
        A+ (90-100) | A (80-89) | B+ (70-79) | B (60-69) | C (50-59) | D (40-49) | F (Below 40)
    </div>

    <!-- Attendance Summary -->
    @if(isset($attendanceSummary))
        <h3 style="margin: 25px 0 10px; color: #1f2937;">Attendance Summary</h3>
        <table>
            <tr>
                <th style="background-color: #3b82f6; color: white;">Total Sessions:</th>
                <td>{{ $attendanceSummary['total'] }}</td>
                <th style="background-color: #10b981; color: white;">Present:</th>
                <td style="font-weight: bold; color: #059669;">{{ $attendanceSummary['present'] }}</td>
            </tr>
            <tr>
                <th style="background-color: #ef4444; color: white;">Absent:</th>
                <td style="font-weight: bold; color: #dc2626;">{{ $attendanceSummary['absent'] }}</td>
                <th style="background-color: #f59e0b; color: white;">Late:</th>
                <td style="font-weight: bold; color: #d97706;">{{ $attendanceSummary['late'] ?? 0 }}</td>
            </tr>
            <tr>
                <th colspan="3" style="text-align: right; background-color: #3b82f6; color: white;">Attendance Rate:</th>
                <td style="font-weight: bold; font-size: 14px; background-color: #dbeafe;">
                    {{ $attendanceSummary['rate'] }}%
                </td>
            </tr>
        </table>
    @endif

    <!-- Comments Section -->
    <h3 style="margin: 25px 0 10px; color: #1f2937;">Teacher's Comments</h3>
    <div style="border: 1px solid #ddd; padding: 15px; min-height: 80px; background-color: #fefce8;">
        <p style="font-style: italic; color: #6b7280;">
            {{ $reportCard->teacher_comments ?? 'No comments provided.' }}
        </p>
    </div>

    <!-- Signatures -->
    <div class="signature-section" style="display: flex; justify-content: space-between; margin-top: 50px;">
        <div style="text-align: center; width: 30%;">
            <div style="border-top: 2px solid #000; padding-top: 5px; margin-top: 40px; font-weight: bold;">
                Class Teacher
            </div>
            <div style="font-size: 10px; color: #6b7280; margin-top: 3px;">
                {{ $reportCard->student->primaryTeacher->user->name ?? '' }}
            </div>
        </div>
        <div style="text-align: center; width: 30%;">
            <div style="border-top: 2px solid #000; padding-top: 5px; margin-top: 40px; font-weight: bold;">
                Principal
            </div>
        </div>
        <div style="text-align: center; width: 30%;">
            <div style="border-top: 2px solid #000; padding-top: 5px; margin-top: 40px; font-weight: bold;">
                Date
            </div>
            <div style="font-size: 10px; color: #6b7280; margin-top: 3px;">
                {{ now()->format('d/m/Y') }}
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div style="margin-top: 30px; padding-top: 15px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 10px; color: #6b7280;">
        <p>This is an official document from {{ $reportCard->school->name }}</p>
        <p>Generated on {{ now()->format('F d, Y \a\t H:i A') }}</p>
    </div>
</div>
</body>
</html>
