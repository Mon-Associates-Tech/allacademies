@props(['student', 'idCard', 'school', 'customFields' => []])

@php
    $labels = $customFields['labels'] ?? [];
    $enabledFields = $customFields['enabled_optional_fields'] ?? ['academic_level', 'student_group', 'date_of_birth', 'barcode'];
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
            background: #ffffff;
            border-radius: 2.5mm;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
            position: relative;
        }
        /* Subtle top accent line */
        .accent-line {
            height: 1.2mm;
            background: linear-gradient(90deg, #2563eb 0%, #3b82f6 50%, #60a5fa 100%);
        }
        /* Header section */
        .card-header {
            background: #f8fafc;
            padding: 2.5mm 4mm;
            display: flex;
            align-items: center;
            gap: 3mm;
            border-bottom: 0.2mm solid #e2e8f0;
        }
        .school-logo {
            width: 10mm;
            height: 10mm;
            background: #ffffff;
            border-radius: 1.5mm;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 0.2mm solid #e2e8f0;
            flex-shrink: 0;
        }
        .school-logo img {
            width: 90%;
            height: 90%;
            object-fit: contain;
        }
        .school-info {
            flex: 1;
            min-width: 0;
        }
        .school-name {
            font-size: 9px;
            font-weight: 600;
            color: #1e293b;
            letter-spacing: 0.2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .card-type {
            font-size: 6px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-top: 0.5mm;
        }
        .status-badge {
            padding: 1mm 2mm;
            border-radius: 1mm;
            font-size: 5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .status-active {
            background: #dcfce7;
            color: #166534;
        }
        .status-inactive {
            background: #fee2e2;
            color: #991b1b;
        }
        /* Main content */
        .card-body {
            display: flex;
            padding: 3mm 4mm;
            gap: 4mm;
        }
        .photo-section {
            flex-shrink: 0;
        }
        .student-photo {
            width: 18mm;
            height: 22mm;
            background: #f1f5f9;
            border-radius: 1.5mm;
            overflow: hidden;
            border: 0.3mm solid #e2e8f0;
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
            color: #94a3b8;
            font-size: 6px;
        }
        .info-section {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }
        .student-name {
            font-size: 11px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .student-id-display {
            font-size: 8px;
            color: #2563eb;
            font-weight: 600;
            font-family: 'SF Mono', 'Consolas', monospace;
            margin-bottom: 2mm;
            letter-spacing: 0.5px;
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
            font-size: 5.5px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 0.3mm;
        }
        .info-value {
            font-size: 7px;
            color: #334155;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        /* Footer */
        .card-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: #f8fafc;
            padding: 2mm 4mm;
            border-top: 0.2mm solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .validity-dates {
            display: flex;
            gap: 4mm;
        }
        .validity-item {
            text-align: left;
        }
        .validity-label {
            font-size: 5px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .validity-value {
            font-size: 6.5px;
            color: #475569;
            font-weight: 500;
        }
        .barcode-section {
            text-align: right;
        }
        .barcode {
            height: 5mm;
            width: 18mm;
            background: repeating-linear-gradient(
                90deg,
                #1e293b 0px, #1e293b 0.8px,
                transparent 0.8px, transparent 1.6px,
                #1e293b 1.6px, #1e293b 2px,
                transparent 2px, transparent 3px
            );
            margin-bottom: 0.5mm;
            border-radius: 0.3mm;
        }
        .card-number {
            font-size: 5px;
            font-family: 'SF Mono', 'Consolas', monospace;
            color: #64748b;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>
    <div class="id-card">
        {{-- Top accent line --}}
        <div class="accent-line"></div>

        {{-- Header --}}
        <div class="card-header">
            <div class="school-logo">
                @if($school->logo)
                    <img src="{{ public_path('storage/' . $school->logo) }}" alt="Logo">
                @endif
            </div>
            <div class="school-info">
                <div class="school-name">{{ $labels['school_name'] ?? $school->name }}</div>
                <div class="card-type">{{ $labels['card_title'] ?? 'Student Identification Card' }}</div>
            </div>
            <div class="status-badge {{ $idCard->status === 'active' ? 'status-active' : 'status-inactive' }}">
                {{ ucfirst($idCard->status) }}
            </div>
        </div>

        {{-- Body --}}
        <div class="card-body">
            {{-- Photo --}}
            <div class="photo-section">
                <div class="student-photo">
                    @if($student->user->avatar)
                        <img src="{{ public_path('storage/' . $student->user->avatar) }}" alt="{{ $student->user->name }}">
                    @else
                        <div class="photo-placeholder">Photo</div>
                    @endif
                </div>
            </div>

            {{-- Info --}}
            <div class="info-section">
                <div class="student-name">{{ $student->user->name }}</div>
                <div class="student-id-display">{{ $student->student_id }}</div>

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
                            <div class="info-label">{{ $labels['date_of_birth'] ?? 'Date of Birth' }}</div>
                            <div class="info-value">{{ $student->date_of_birth->format('d M Y') }}</div>
                        </div>
                    @endif

                    @if(in_array('blood_group', $enabledFields) && $student->blood_group)
                        <div class="info-item">
                            <div class="info-label">{{ $labels['blood_group'] ?? 'Blood Group' }}</div>
                            <div class="info-value">{{ $student->blood_group }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="card-footer">
            <div class="validity-dates">
                <div class="validity-item">
                    <div class="validity-label">{{ $labels['issue_date'] ?? 'Issued' }}</div>
                    <div class="validity-value">{{ $idCard->issue_date->format('d M Y') }}</div>
                </div>
                <div class="validity-item">
                    <div class="validity-label">{{ $labels['expiry_date'] ?? 'Valid Until' }}</div>
                    <div class="validity-value">{{ $idCard->expiry_date->format('d M Y') }}</div>
                </div>
            </div>

            @if(in_array('barcode', $enabledFields))
                <div class="barcode-section">
                    <div class="barcode"></div>
                    <div class="card-number">{{ $idCard->card_number }}</div>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
