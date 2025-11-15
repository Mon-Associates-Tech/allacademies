<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Report - {{ $student->user->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }
        .content {
            padding: 0 20px 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin: 20px 0;
        }
        .summary-card {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        .summary-card.present {
            background: #d1fae5;
            border-color: #10b981;
        }
        .summary-card.absent {
            background: #fee2e2;
            border-color: #ef4444;
        }
        .summary-card.late {
            background: #fef3c7;
            border-color: #f59e0b;
        }
        .summary-card.rate {
            background: #dbeafe;
            border-color: #3b82f6;
        }
        .summary-number {
            font-size: 32px;
            font-weight: bold;
            margin: 10px 0;
        }
        .summary-label {
            font-size: 11px;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: 600;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-present {
            background: #d1fae5;
            color: #065f46;
        }
        .status-absent {
            background: #fee2e2;
            color: #991b1b;
        }
        .status-late {
            background: #fef3c7;
            color: #92400e;
        }
        .status-excused {
            background: #e0e7ff;
            color: #3730a3;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            margin: 25px 0 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid #3b82f6;
        }
        .info-box {
            background: #f9fafb;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .chart-placeholder {
            height: 200px;
            background: #f3f4f6;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 15px 0;
            color: #6b7280;
            font-style: italic;
        }
        .monthly-breakdown {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin: 15px 0;
        }
        .month-card {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px;
            background: #ffffff;
        }
        .month-name {
            font-weight: bold;
            color: #3b82f6;
            font-size: 11px;
            margin-bottom: 8px;
        }
        .month-stats {
            font-size: 10px;
            color: #6b7280;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
<!-- Include selected letterhead -->
@include('components.letterheads.' . ($data['letterhead_template'] ?? 'classic'), [
    'school' => $data['school'],
    'title' => 'Attendance Report'
])

<div class="content">
    <!-- Student Information -->
    <div class="info-box">
        <table style="border: none; margin: 0;">
            <tr style="border: none;">
                <td style="border: none; width: 25%; font-weight: bold;">Student Name:</td>
                <td style="border: none; width: 25%;">{{ $student->user->name }}</td>
                <td style="border: none; width: 25%; font-weight: bold;">Student ID:</td>
                <td style="border: none; width: 25%;">{{ $student->student_id }}</td>
            </tr>
            <tr style="border: none;">
                <td style="border: none; font-weight: bold;">Academic Level:</td>
                <td style="border: none;">{{ $student->academicLevel->name ?? 'N/A' }}</td>
                <td style="border: none; font-weight: bold;">Academic Year:</td>
                <td style="border: none;">{{ $academicYear->name ?? 'N/A' }}</td>
            </tr>
            <tr style="border: none;">
                <td style="border: none; font-weight: bold;">Class/Group:</td>
                <td style="border: none;">{{ $student->studentGroup->name ?? 'N/A' }}</td>
                <td style="border: none; font-weight: bold;">Report Date:</td>
                <td style="border: none;">{{ now()->format('F d, Y') }}</td>
            </tr>
        </table>
    </div>

    <!-- Attendance Summary Cards -->
    <h3 class="section-title">📊 Attendance Summary</h3>
    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-label">Total Sessions</div>
            <div class="summary-number" style="color: #6b7280;">{{ $data['summary']['total'] }}</div>
        </div>
        <div class="summary-card present">
            <div class="summary-label">Present</div>
            <div class="summary-number" style="color: #065f46;">{{ $data['summary']['present'] }}</div>
        </div>
        <div class="summary-card absent">
            <div class="summary-label">Absent</div>
            <div class="summary-number" style="color: #991b1b;">{{ $data['summary']['absent'] }}</div>
        </div>
        <div class="summary-card rate">
            <div class="summary-label">Attendance Rate</div>
            <div class="summary-number" style="color: #1e40af;">{{ $data['summary']['rate'] }}%</div>
        </div>
    </div>

    @if(isset($data['summary']['late']) && $data['summary']['late'] > 0)
        <div style="background: #fef3c7; border: 2px solid #f59e0b; border-radius: 8px; padding: 12px; margin-bottom: 20px; text-align: center;">
            <strong style="color: #92400e;">⏰ Late Arrivals:</strong>
            <span style="font-size: 18px; font-weight: bold; color: #92400e; margin-left: 10px;">
                {{ $data['summary']['late'] }} sessions
            </span>
        </div>
    @endif

    <!-- Attendance Performance Analysis -->
    <h3 class="section-title">📈 Performance Analysis</h3>
    <table>
        <tr>
            <th style="width: 30%;">Metric</th>
            <th style="width: 20%; text-align: center;">Value</th>
            <th style="width: 50%;">Status</th>
        </tr>
        <tr>
            <td><strong>Overall Attendance Rate</strong></td>
            <td style="text-align: center; font-size: 16px; font-weight: bold; color: {{ $data['summary']['rate'] >= 90 ? '#065f46' : ($data['summary']['rate'] >= 75 ? '#d97706' : '#991b1b') }};">
                {{ $data['summary']['rate'] }}%
            </td>
            <td>
                @if($data['summary']['rate'] >= 90)
                    <span class="status-badge status-present">Excellent</span>
                    <span style="font-size: 11px; color: #065f46;"> - Outstanding attendance record</span>
                @elseif($data['summary']['rate'] >= 75)
                    <span class="status-badge" style="background: #fef3c7; color: #92400e;">Good</span>
                    <span style="font-size: 11px; color: #d97706;"> - Room for improvement</span>
                @else
                    <span class="status-badge status-absent">Needs Attention</span>
                    <span style="font-size: 11px; color: #991b1b;"> - Requires immediate attention</span>
                @endif
            </td>
        </tr>
        <tr>
            <td><strong>Days Present</strong></td>
            <td style="text-align: center; font-weight: bold;">{{ $data['summary']['present'] }}</td>
            <td>Out of {{ $data['summary']['total'] }} total sessions</td>
        </tr>
        <tr>
            <td><strong>Days Absent</strong></td>
            <td style="text-align: center; font-weight: bold; color: #991b1b;">{{ $data['summary']['absent'] }}</td>
            <td>
                @if($data['summary']['absent'] == 0)
                    <span style="color: #065f46;">✓ Perfect attendance!</span>
                @elseif($data['summary']['absent'] <= 3)
                    <span style="color: #d97706;">⚠ Monitor closely</span>
                @else
                    <span style="color: #991b1b;">⚠ High absence rate</span>
                @endif
            </td>
        </tr>
        @if(isset($data['summary']['late']) && $data['summary']['late'] > 0)
            <tr>
                <td><strong>Late Arrivals</strong></td>
                <td style="text-align: center; font-weight: bold; color: #d97706;">{{ $data['summary']['late'] }}</td>
                <td>
                    @if($data['summary']['late'] <= 2)
                        <span style="color: #059669;">Acceptable</span>
                    @else
                        <span style="color: #d97706;">⚠ Punctuality needs improvement</span>
                    @endif
                </td>
            </tr>
        @endif
    </table>

    <!-- Monthly Breakdown (if we have enough data) -->
    @php
        // Group records by month
        $monthlyData = $data['records']->groupBy(function($record) {
            return $record->attendance->date->format('Y-m');
        })->map(function($records, $month) {
            return [
                'name' => \Carbon\Carbon::parse($month . '-01')->format('F Y'),
                'total' => $records->count(),
                'present' => $records->where('status', 'present')->count(),
                'absent' => $records->where('status', 'absent')->count(),
                'late' => $records->where('status', 'late')->count(),
                'rate' => $records->count() > 0 ? round(($records->where('status', 'present')->count() / $records->count()) * 100, 1) : 0
            ];
        });
    @endphp

    @if($monthlyData->count() > 0)
        <h3 class="section-title">📅 Monthly Breakdown</h3>
        <div class="monthly-breakdown">
            @foreach($monthlyData as $month)
                <div class="month-card">
                    <div class="month-name">{{ $month['name'] }}</div>
                    <div class="month-stats">
                        <div style="margin: 3px 0;">Total: <strong>{{ $month['total'] }}</strong></div>
                        <div style="margin: 3px 0; color: #065f46;">Present: <strong>{{ $month['present'] }}</strong></div>
                        <div style="margin: 3px 0; color: #991b1b;">Absent: <strong>{{ $month['absent'] }}</strong></div>
                        @if($month['late'] > 0)
                            <div style="margin: 3px 0; color: #d97706;">Late: <strong>{{ $month['late'] }}</strong></div>
                        @endif
                        <div style="margin: 5px 0; padding-top: 5px; border-top: 1px solid #e5e7eb;">
                            Rate: <strong style="color: {{ $month['rate'] >= 90 ? '#065f46' : ($month['rate'] >= 75 ? '#d97706' : '#991b1b') }};">{{ $month['rate'] }}%</strong>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Page Break for Detailed Records -->
    @if($data['records']->count() > 0)
        <div class="page-break"></div>

        <!-- Detailed Attendance Records -->
        <h3 class="section-title">📋 Detailed Attendance Records</h3>
        <table>
            <thead>
            <tr style="background-color: #3b82f6; color: white;">
                <th style="width: 12%;">Date</th>
                <th style="width: 15%;">Day</th>
                <th style="width: 20%;">Subject</th>
                <th style="width: 15%;">Session</th>
                <th style="width: 15%; text-align: center;">Status</th>
                <th style="width: 23%;">Remarks</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data['records'] as $record)
                <tr>
                    <td>{{ $record->attendance->date->format('M d, Y') }}</td>
                    <td>{{ $record->attendance->date->format('l') }}</td>
                    <td>{{ $record->attendance->academicSubject->name ?? 'General' }}</td>
                    <td>{{ ucfirst($record->attendance->session ?? 'Full Day') }}</td>
                    <td style="text-align: center;">
                        <span class="status-badge status-{{ $record->status }}">
                            {{ ucfirst($record->status) }}
                        </span>
                    </td>
                    <td style="font-size: 10px; color: #6b7280;">
                        {{ $record->remarks ?: $record->attendance->remarks ?: '-' }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <div style="padding: 40px; text-align: center; background: #f9fafb; border: 2px dashed #d1d5db; border-radius: 8px; margin: 20px 0;">
            <svg style="width: 64px; height: 64px; margin: 0 auto 15px; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h4 style="color: #6b7280; font-size: 16px; margin-bottom: 8px;">No Attendance Records Found</h4>
            <p style="color: #9ca3af; font-size: 12px;">No attendance has been recorded for this student in the selected period.</p>
        </div>
    @endif

    <!-- Recommendations Section -->
    <h3 class="section-title">💡 Recommendations</h3>
    <div style="background: #eff6ff; border-left: 4px solid #3b82f6; padding: 15px; border-radius: 4px;">
        @if($data['summary']['rate'] >= 95)
            <p style="color: #1e40af; margin-bottom: 8px;"><strong>✓ Excellent Attendance:</strong></p>
            <ul style="margin-left: 20px; color: #1e3a8a;">
                <li>Continue maintaining this outstanding attendance record</li>
                <li>Student demonstrates strong commitment to learning</li>
                <li>Encourage and recognize this achievement</li>
            </ul>
        @elseif($data['summary']['rate'] >= 85)
            <p style="color: #1e40af; margin-bottom: 8px;"><strong>✓ Good Attendance:</strong></p>
            <ul style="margin-left: 20px; color: #1e3a8a;">
                <li>Maintain current attendance levels</li>
                <li>Address any patterns of absence</li>
                <li>Encourage punctuality</li>
            </ul>
        @elseif($data['summary']['rate'] >= 75)
            <p style="color: #d97706; margin-bottom: 8px;"><strong>⚠ Attendance Needs Improvement:</strong></p>
            <ul style="margin-left: 20px; color: #92400e;">
                <li>Schedule a meeting with parents/guardians</li>
                <li>Identify and address barriers to attendance</li>
                <li>Develop an attendance improvement plan</li>
                <li>Monitor progress closely</li>
            </ul>
        @else
            <p style="color: #991b1b; margin-bottom: 8px;"><strong>⚠ Critical Attendance Issue:</strong></p>
            <ul style="margin-left: 20px; color: #7f1d1d;">
                <li><strong>Immediate action required</strong></li>
                <li>Urgent parent/guardian conference needed</li>
                <li>Investigate underlying causes</li>
                <li>Consider intervention programs</li>
                <li>Academic performance may be at risk</li>
                <li>May require administrative involvement</li>
            </ul>
        @endif

        @if(isset($data['summary']['late']) && $data['summary']['late'] > 5)
            <p style="color: #d97706; margin-top: 15px; padding-top: 15px; border-top: 1px solid #bfdbfe;">
                <strong>⏰ Punctuality Concern:</strong> {{ $data['summary']['late'] }} late arrivals recorded.
                Please address time management and morning routine with student and family.
            </p>
        @endif
    </div>

    <!-- Parent/Guardian Acknowledgment -->
    <h3 class="section-title">✍️ Acknowledgment</h3>
    <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 15px 0;">
        <p style="margin-bottom: 15px; color: #6b7280; font-size: 11px;">
            I acknowledge that I have received and reviewed this attendance report for my child.
        </p>
        <table style="border: none; margin: 0;">
            <tr style="border: none;">
                <td style="border: none; width: 50%; padding-right: 15px;">
                    <div style="margin-top: 40px; border-top: 2px solid #000; padding-top: 8px;">
                        <strong>Parent/Guardian Signature</strong>
                    </div>
                </td>
                <td style="border: none; width: 50%; padding-left: 15px;">
                    <div style="margin-top: 40px; border-top: 2px solid #000; padding-top: 8px;">
                        <strong>Date</strong>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>{{ $data['school']->name }}</strong></p>
        <p>{{ $data['school']->address }}, {{ $data['school']->city }}</p>
        <p>📞 {{ $data['school']->phone }} | 📧 {{ $data['school']->email }}</p>
        <p style="margin-top: 10px;">Generated on {{ now()->format('F d, Y \a\t h:i A') }}</p>
        <p style="margin-top: 5px; font-style: italic;">This is an official attendance report from {{ $data['school']->name }}</p>
    </div>
</div>
</body>
</html>
