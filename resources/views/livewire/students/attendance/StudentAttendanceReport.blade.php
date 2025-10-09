<!DOCTYPE html>
<html>
<head>
    <title>Attendance Report - {{ $student->user->name }}</title>
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
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
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
            margin-bottom: 20px;
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
        }
        .info-grid {
            display: flex;
            justify-content: space-between;
        }
        .info-item {
            margin-bottom: 5px;
        }
        .summary-cards {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .summary-card {
            width: 23%;
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
        }
        .summary-card .number {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .summary-card .label {
            font-size: 11px;
            color: #666;
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
        .status-present {
            color: #10B981;
            font-weight: bold;
        }
        .status-absent {
            color: #EF4444;
            font-weight: bold;
        }
        .status-late {
            color: #F59E0B;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <p class="school-name">{{ $student->school->name ?? 'School Name' }}</p>
        <p class="report-title">STUDENT ATTENDANCE REPORT</p>
        @if($academicYear)
            <p>Academic Year: {{ $academicYear->name }}</p>
        @endif
        <p>Generated: {{ now()->format('F d, Y') }}</p>
    </div>

    <div class="student-info">
        <div class="info-grid">
            <div>
                <div class="info-item"><strong>Student Name:</strong> {{ $student->user->name }}</div>
                <div class="info-item"><strong>Student ID:</strong> {{ $student->student_id ?? 'N/A' }}</div>
            </div>
            <div>
                <div class="info-item"><strong>Academic Level:</strong> {{ $student->academicLevel->name ?? 'N/A' }}</div>
                <div class="info-item"><strong>Group:</strong> {{ $student->studentGroup->name ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    <div class="summary-cards">
        <div class="summary-card">
            <div class="number">{{ $data['summary']['total'] }}</div>
            <div class="label">Total Sessions</div>
        </div>
        <div class="summary-card">
            <div class="number" style="color: #10B981;">{{ $data['summary']['present'] }}</div>
            <div class="label">Present</div>
        </div>
        <div class="summary-card">
            <div class="number" style="color: #EF4444;">{{ $data['summary']['absent'] }}</div>
            <div class="label">Absent</div>
        </div>
        <div class="summary-card">
            <div class="number" style="color: #6366F1;">{{ $data['summary']['rate'] }}%</div>
            <div class="label">Attendance Rate</div>
        </div>
    </div>

    <h3 style="margin-top: 30px; margin-bottom: 10px;">Detailed Attendance Records</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Session</th>
                <th>Level</th>
                <th>Subject</th>
                <th>Status</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['records'] as $record)
            <tr>
                <td>{{ $record->attendance->date->format('M d, Y') }}</td>
                <td>{{ ucfirst($record->attendance->session) }}</td>
                <td>{{ $record->attendance->academicLevel->name ?? 'N/A' }}</td>
                <td>{{ $record->attendance->academicSubject->name ?? 'All Subjects' }}</td>
                <td class="status-{{ $record->status }}">{{ ucfirst($record->status) }}</td>
                <td>{{ $record->remarks ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">No attendance records found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>This is a computer-generated document. No signature is required.</p>
        <p>© {{ now()->year }} {{ $student->school->name ?? 'School Name' }}. All rights reserved.</p>
    </div>
</body>
</html>
