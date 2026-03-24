@props(['student', 'idCard', 'school', 'customFields' => []])

@php
    $labels = $customFields['labels'] ?? [];
    $enabledFields = $customFields['enabled_optional_fields'] ?? ['academic_level', 'student_group', 'date_of_birth', 'qr_code'];
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student ID Card - {{ $student->user->name }}</title>
    <style>
        @page {
            size: 85.6mm 53.98mm;
            margin: 0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 8px;
            line-height: 1.4;
            -webkit-font-smoothing: antialiased;
        }
        .id-card {
            width: 85.6mm;
            height: 53.98mm;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-radius: 2.5mm;
            overflow: hidden;
            position: relative;
            color: #ffffff;
        }
        /* Subtle geometric pattern */
        .card-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image:
                radial-gradient(circle at 100% 0%, rgba(59, 130, 246, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 0% 100%, rgba(139, 92, 246, 0.06) 0%, transparent 50%);
            pointer-events: none;
        }
        .card-content {
            position: relative;
            z-index: 1;
            height: 100%;
            display: flex;
            flex-direction: column;
            padding: 3mm;
        }
        /* Header */
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2mm;
        }
        .school-section {
            display: flex;
            align-items: center;
            gap: 2mm;
        }
        .school-logo {
            width: 8mm;
            height: 8mm;
            background: rgba(255,255,255,0.1);
            border-radius: 1.5mm;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }
        .school-logo img {
            width: 85%;
            height: 85%;
            object-fit: contain;
        }
        .school-text {
            max-width: 40mm;
        }
        .school-name {
            font-size: 8px;
            font-weight: 600;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            letter-spacing: 0.2px;
        }
        .card-type {
            font-size: 5px;
            color: rgba(255,255,255,0.5);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 0.5mm;
        }
        .status-chip {
            display: inline-flex;
            align-items: center;
            gap: 1mm;
            padding: 1mm 2mm;
            border-radius: 1mm;
            font-size: 5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .status-active {
            background: rgba(34, 197, 94, 0.15);
            color: #4ade80;
            border: 0.2mm solid rgba(34, 197, 94, 0.3);
        }
        .status-inactive {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 0.2mm solid rgba(239, 68, 68, 0.3);
        }
        .status-dot {
            width: 1.2mm;
            height: 1.2mm;
            border-radius: 50%;
            background: currentColor;
        }
        /* Main content */
        .card-main {
            flex: 1;
            display: flex;
            gap: 3mm;
        }
        .photo-container {
            flex-shrink: 0;
        }
        .student-photo {
            width: 16mm;
            height: 20mm;
            background: rgba(255,255,255,0.05);
            border-radius: 1.5mm;
            overflow: hidden;
            border: 0.3mm solid rgba(255,255,255,0.1);
        }
        .student-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .photo-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255,255,255,0.3);
            font-size: 6px;
        }
        .info-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 0;
        }
        .student-name {
            font-size: 12px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.5mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            letter-spacing: 0.2px;
        }
        .student-id {
            font-size: 8px;
            color: #60a5fa;
            font-weight: 600;
            font-family: 'SF Mono', 'Consolas', monospace;
            margin-bottom: 2mm;
            letter-spacing: 0.8px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5mm 3mm;
        }
        .info-item {
            min-width: 0;
        }
        .info-label {
            font-size: 5px;
            color: rgba(255,255,255,0.4);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.3mm;
        }
        .info-value {
            font-size: 7px;
            color: rgba(255,255,255,0.9);
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .qr-section {
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .qr-code {
            width: 14mm;
            height: 14mm;
            background: #ffffff;
            border-radius: 1mm;
            padding: 1mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .qr-placeholder {
            width: 100%;
            height: 100%;
            background:
                repeating-conic-gradient(
                    #1e293b 0% 25%,
                    #ffffff 0% 50%
                ) 50% / 2mm 2mm;
            border-radius: 0.5mm;
        }
        /* Footer */
        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 2mm;
            margin-top: auto;
            border-top: 0.2mm solid rgba(255,255,255,0.1);
        }
        .validity {
            display: flex;
            gap: 4mm;
        }
        .validity-item {
            text-align: left;
        }
        .validity-label {
            font-size: 5px;
            color: rgba(255,255,255,0.4);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .validity-value {
            font-size: 6.5px;
            color: rgba(255,255,255,0.8);
            font-weight: 500;
        }
        .card-number {
            font-size: 6px;
            font-family: 'SF Mono', 'Consolas', monospace;
            color: rgba(255,255,255,0.4);
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>
    <div class="id-card">
        <div class="card-pattern"></div>

        <div class="card-content">
            {{-- Header --}}
            <div class="card-header">
                <div class="school-section">
                    <div class="school-logo">
                        @if($school->logo)
                            <img src="{{ public_path('storage/' . $school->logo) }}" alt="Logo">
                        @endif
                    </div>
                    <div class="school-text">
                        <div class="school-name">{{ $labels['school_name'] ?? $school->name }}</div>
                        <div class="card-type">{{ $labels['card_title'] ?? 'Student ID' }}</div>
                    </div>
                </div>
                <div class="status-chip {{ $idCard->status === 'active' ? 'status-active' : 'status-inactive' }}">
                    <span class="status-dot"></span>
                    {{ ucfirst($idCard->status) }}
                </div>
            </div>

            {{-- Main Content --}}
            <div class="card-main">
                <div class="photo-container">
                    <div class="student-photo">
                        @if($student->user->avatar)
                            <img src="{{ public_path('storage/' . $student->user->avatar) }}" alt="{{ $student->user->name }}">
                        @else
                            <div class="photo-placeholder">Photo</div>
                        @endif
                    </div>
                </div>

                <div class="info-container">
                    <div class="student-name">{{ $student->user->name }}</div>
                    <div class="student-id">{{ $student->student_id }}</div>

                    <div class="info-grid">
                        @if(in_array('academic_level', $enabledFields) && $student->academicLevel)
                            <div class="info-item">
                                <div class="info-label">{{ $labels['academic_level'] ?? 'Class' }}</div>
                                <div class="info-value">{{ $student->academicLevel->name }}</div>
                            </div>
                        @endif

                        @if(in_array('student_group', $enabledFields) && $student->studentGroup)
                            <div class="info-item">
                                <div class="info-label">{{ $labels['student_group'] ?? 'Section' }}</div>
                                <div class="info-value">{{ $student->studentGroup->name }}</div>
                            </div>
                        @endif

                        @if(in_array('date_of_birth', $enabledFields) && $student->date_of_birth)
                            <div class="info-item">
                                <div class="info-label">{{ $labels['date_of_birth'] ?? 'DOB' }}</div>
                                <div class="info-value">{{ $student->date_of_birth->format('d M Y') }}</div>
                            </div>
                        @endif

                        @if(in_array('blood_group', $enabledFields) && $student->blood_group)
                            <div class="info-item">
                                <div class="info-label">{{ $labels['blood_group'] ?? 'Blood' }}</div>
                                <div class="info-value">{{ $student->blood_group }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                @if(in_array('qr_code', $enabledFields))
                    <div class="qr-section">
                        <div class="qr-code">
                            <div class="qr-placeholder"></div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="card-footer">
                <div class="validity">
                    <div class="validity-item">
                        <div class="validity-label">{{ $labels['issue_date'] ?? 'Issued' }}</div>
                        <div class="validity-value">{{ $idCard->issue_date->format('M Y') }}</div>
                    </div>
                    <div class="validity-item">
                        <div class="validity-label">{{ $labels['expiry_date'] ?? 'Expires' }}</div>
                        <div class="validity-value">{{ $idCard->expiry_date->format('M Y') }}</div>
                    </div>
                </div>
                <div class="card-number">{{ $idCard->card_number }}</div>
            </div>
        </div>
    </div>
</body>
</html>
