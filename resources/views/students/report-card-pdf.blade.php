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
<!-- Include selected letterhead (null-safe with fallback) -->
@php
    $schoolObj = isset($school) && $school ? $school : ($reportCard->school ?? $reportCard->student->school);
    // Ensure we always have a usable template name
    $template = ($schoolObj?->letterhead_template) ?: 'classic';
@endphp

{{-- Prefer Blade anonymous component usage to honor @props in the letterhead --}}
<x-letterheads.classic :school="$schoolObj" :title="'Student Report Card - ' . $reportCard->term" />

{{-- Minimal inline fallback (in case component cannot be resolved) --}}
@if(empty($schoolObj) || empty($schoolObj->name))
    <div style="padding: 20px 20px 0; border-bottom: 2px solid #111827; margin-bottom: 10px;">
        <h1 style="font-size: 24px; font-weight: bold; color: #111827;">{{ config('app.name') }}</h1>
        <p style="color:#6b7280;">Student Report Card - {{ $reportCard->term }}</p>
    </div>
@endif

<!-- Report Card Content -->
<div style="padding: 0 20px 20px;">
    <!-- Student Information -->
    @php
        $user = $reportCard->student->user;
        $candidatePhotoPaths = [];
        if (!empty($user->profile_photo_path ?? null)) {
            $candidatePhotoPaths[] = public_path('storage/' . ltrim($user->profile_photo_path, '/'));
        }
        if (!empty($user->avatar ?? null)) {
            $candidatePhotoPaths[] = public_path('storage/' . ltrim($user->avatar, '/'));
        }
        if (!empty($reportCard->student->photo_path ?? null)) {
            $candidatePhotoPaths[] = public_path('storage/' . ltrim($reportCard->student->photo_path, '/'));
        }
        $photoSrc = null;
        foreach ($candidatePhotoPaths as $p) {
            try {
                if ($p && file_exists($p)) { $photoSrc = $p; break; }
            } catch (\Throwable $e) { /* ignore */ }
        }
    @endphp

    <h3 style="margin: 20px 0 10px; color: #1f2937;">Student Information</h3>
    <table>
        <tr>
            <th style="width: 22%;">Student Name:</th>
            <td style="width: 28%;">{{ $reportCard->student->user->name }}</td>
            <th style="width: 22%;">Student ID:</th>
            <td style="width: 28%;">{{ $reportCard->student->student_id }}</td>
            <td rowspan="4" style="width: 110px; text-align: center; border: 0;">
                @if($photoSrc)
                    <img src="{{ $photoSrc }}" alt="Student Photo" style="width: 90px; height: 90px; object-fit: cover; border-radius: 6px; border:1px solid #e5e7eb;" />
                @else
                    <div style="width: 90px; height: 90px; display:flex; align-items:center; justify-content:center; background:#f3f4f6; color:#6b7280; font-size:10px; border:1px solid #e5e7eb; border-radius:6px;">No Photo</div>
                @endif
            </td>
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
            <td>{{ $reportCard->student->primary_teacher?->user?->name ?? 'N/A' }}</td>
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
            <th style="text-align: center;">Assignments<br>(40%)</th>
            <th style="text-align: center;">Quizzes<br>(10%)</th>
            <th style="text-align: center;">Final Exam<br>(50%)</th>
            <th style="text-align: center;">Total<br>(100%)</th>
            <th style="text-align: center;">Grade</th>
            <th>Remarks</th>
        </tr>
        </thead>
        <tbody>
        @php
            $totalScore = 0;
            $subjectCount = 0;
            $hasPersisted = isset($reportCard->grades) && $reportCard->grades->count() > 0;
        @endphp

        @if($hasPersisted)
            @foreach($reportCard->grades as $grade)
                @php
                    $totalScore += (float) $grade->total_score;
                    $subjectCount++;
                @endphp
                <tr>
                    <td>{{ $grade->subject->name }}</td>
                    <td style="text-align: center;">{{ number_format((float)($grade->assessments_score ?? 0), 1) }}</td>
                    <td style="text-align: center;">{{ number_format((float)($grade->quizzes_score ?? 0), 1) }}</td>
                    <td style="text-align: center;">{{ number_format((float)($grade->final_exam_score ?? 0), 1) }}</td>
                    <td style="text-align: center; font-weight: bold; background-color: #fef3c7;">
                        {{ number_format((float)($grade->total_score ?? 0), 1) }}
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
        @elseif(isset($gradesArray) && is_array($gradesArray) && count($gradesArray) > 0)
            @foreach($gradesArray as $subjectId => $g)
                @php
                    $totalScore += (float) ($g['total_score'] ?? 0);
                    $subjectCount++;
                @endphp
                <tr>
                    <td>{{ $g['subject_name'] ?? 'Subject' }}</td>
                    <td style="text-align: center;">{{ number_format((float)($g['assessments_score'] ?? 0), 1) }}</td>
                    <td style="text-align: center;">{{ number_format((float)($g['quizzes_score'] ?? 0), 1) }}</td>
                    <td style="text-align: center;">{{ number_format((float)($g['final_exam_score'] ?? 0), 1) }}</td>
                    <td style="text-align: center; font-weight: bold; background-color: #fef3c7;">
                        {{ number_format((float)($g['total_score'] ?? 0), 1) }}
                    </td>
                    <td style="text-align: center; font-weight: bold; font-size: 14px;">
                        {{ $g['grade_label'] ?? '' }}
                    </td>
                    <td style="font-size: 11px;">{{ $g['remarks'] ?? '-' }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="7" style="text-align:center; color:#6b7280; padding: 12px;">No grades available</td>
            </tr>
        @endif

        @if($subjectCount > 0)
            <tr style="background-color: #dbeafe; font-weight: bold;">
                <td colspan="4" style="text-align: right; padding-right: 10px;">AVERAGE:</td>
                <td style="text-align: center; font-size: 14px;">
                    {{ number_format($totalScore / max($subjectCount,1), 1) }}%
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

    <!-- Signatures (table-based for consistent PDF alignment) -->
    <div class="signature-section" style="margin-top: 40px;">
        <table style="width:100%; border-collapse: collapse;">
            <tr>
                <td style="width:33%; text-align:center; padding-top:40px;">
                    <div class="signature-line">&nbsp;</div>
                    <div class="font-bold" style="margin-top:5px;">Class Teacher</div>
                    <div style="font-size:10px; color:#6b7280;">{{ $reportCard->student->primary_teacher?->user?->name ?? '' }}</div>
                </td>
                <td style="width:33%; text-align:center; padding-top:40px;">
                    <div class="signature-line">&nbsp;</div>
                    <div class="font-bold" style="margin-top:5px;">Principal</div>
                </td>
                <td style="width:33%; text-align:center; padding-top:40px;">
                    <div class="signature-line">&nbsp;</div>
                    <div class="font-bold" style="margin-top:5px;">Date</div>
                    <div style="font-size:10px; color:#6b7280;">{{ now()->format('d/m/Y') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <div style="margin-top: 30px; padding-top: 15px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 10px; color: #6b7280;">
        <p>This is an official document from {{ $schoolObj->name ?? $reportCard->student->school->name ?? config('app.name') }}</p>
        <p>Generated on {{ now()->format('F d, Y \a\t H:i A') }}</p>
    </div>
</div>
</body>
</html>
