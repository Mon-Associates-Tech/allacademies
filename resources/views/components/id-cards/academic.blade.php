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
            font-family: 'Georgia', 'Times New Roman', serif;
            font-size: 8px;
            line-height: 1.4;
            -webkit-font-smoothing: antialiased;
        }
        .id-card {
            width: 85.6mm;
            height: 53.98mm;
            background: linear-gradient(180deg, #fafaf9 0%, #f5f5f4 100%);
            border-radius: 2.5mm;
            overflow: hidden;
            position: relative;
            border: 0.4mm solid #d6d3d1;
        }
        /* Elegant border frame */
        .card-frame {
            position: absolute;
            top: 1.5mm;
            left: 1.5mm;
            right: 1.5mm;
            bottom: 1.5mm;
            border: 0.3mm solid #a8a29e;
            border-radius: 1.5mm;
            pointer-events: none;
        }
        .card-content {
            position: relative;
            z-index: 1;
            height: 100%;
            padding: 3mm 4mm;
            display: flex;
            flex-direction: column;
        }
        /* Header with crest */
        .card-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 3mm;
            padding-bottom: 2mm;
            margin-bottom: 2mm;
            border-bottom: 0.3mm solid #d6d3d1;
        }
        .school-crest {
            width: 11mm;
            height: 11mm;
            background: linear-gradient(145deg, #78716c, #57534e);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0.5mm 1.5mm rgba(0,0,0,0.15);
            border: 0.3mm solid #44403c;
            overflow: hidden;
            flex-shrink: 0;
        }
        .school-crest img {
            width: 85%;
            height: 85%;
            object-fit: contain;
            border-radius: 50%;
        }
        .crest-placeholder {
            color: #fafaf9;
            font-size: 5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header-text {
            text-align: center;
        }
        .school-name {
            font-size: 9px;
            font-weight: bold;
            color: #292524;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5mm;
        }
        .card-title {
            font-size: 6px;
            color: #78716c;
            font-style: italic;
            letter-spacing: 0.8px;
        }
        .status-seal {
            position: absolute;
            top: 3mm;
            right: 4mm;
            width: 7mm;
            height: 7mm;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4px;
            font-weight: bold;
            text-transform: uppercase;
            font-family: -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .seal-active {
            background: linear-gradient(145deg, #166534, #15803d);
            color: #ffffff;
            box-shadow: 0 0.3mm 1mm rgba(22, 101, 52, 0.4);
        }
        .seal-inactive {
            background: linear-gradient(145deg, #991b1b, #b91c1c);
            color: #ffffff;
            box-shadow: 0 0.3mm 1mm rgba(153, 27, 27, 0.4);
        }
        /* Main content */
        .card-body {
            flex: 1;
            display: flex;
            gap: 3mm;
        }
        .photo-section {
            flex-shrink: 0;
        }
        .student-photo {
            width: 17mm;
            height: 21mm;
            background: #ffffff;
            border: 0.4mm solid #a8a29e;
            overflow: hidden;
            box-shadow: 0 0.3mm 1mm rgba(0,0,0,0.1);
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
            background: #f5f5f4;
            color: #a8a29e;
            font-size: 6px;
            font-family: -apple-system, sans-serif;
        }
        .info-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        .student-name {
            font-size: 11px;
            font-weight: bold;
            color: #1c1917;
            margin-bottom: 1.5mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .info-table {
            width: 100%;
        }
        .info-row {
            display: flex;
            margin-bottom: 1mm;
            font-size: 7px;
            font-family: -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .info-label {
            color: #78716c;
            width: 15mm;
            flex-shrink: 0;
            font-weight: 500;
        }
        .info-value {
            color: #292524;
            flex: 1;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        /* Footer */
        .card-footer {
            margin-top: auto;
            padding-top: 2mm;
            border-top: 0.3mm solid #d6d3d1;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .validity-section {
            font-size: 6px;
            color: #57534e;
            font-family: -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .validity-row {
            display: flex;
            gap: 3mm;
        }
        .validity-item {
            display: flex;
            gap: 1mm;
        }
        .validity-label {
            font-weight: 600;
            color: #78716c;
        }
        .validity-value {
            color: #44403c;
        }
        .barcode-section {
            text-align: right;
        }
        .barcode {
            height: 5mm;
            width: 18mm;
            background: repeating-linear-gradient(
                90deg,
                #292524 0px, #292524 0.8px,
                transparent 0.8px, transparent 1.6px,
                #292524 1.6px, #292524 2px,
                transparent 2px, transparent 3px
            );
            margin-bottom: 0.5mm;
            border-radius: 0.3mm;
        }
        .card-number {
            font-size: 5px;
            font-family: 'Courier New', monospace;
            color: #78716c;
            letter-spacing: 0.5px;
        }
        /* Subtle watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-15deg);
            font-size: 20px;
            color: rgba(168, 162, 158, 0.08);
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 3px;
            pointer-events: none;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="id-card">
        <div class="card-frame"></div>
        <div class="watermark">{{ strtoupper(substr($school->name ?? 'SCHOOL', 0, 12)) }}</div>

        <div class="card-content">
            {{-- Status Seal --}}
            <div class="status-seal {{ $idCard->status === 'active' ? 'seal-active' : 'seal-inactive' }}">
                {{ strtoupper(substr($idCard->status, 0, 3)) }}
            </div>

            {{-- Header --}}
            <div class="card-header">
                <div class="school-crest">
                    @if($school->logo)
                        <img src="{{ public_path('storage/' . $school->logo) }}" alt="Crest">
                    @else
                        <span class="crest-placeholder">Crest</span>
                    @endif
                </div>
                <div class="header-text">
                    <div class="school-name">{{ $labels['school_name'] ?? $school->name }}</div>
                    <div class="card-title">{{ $labels['card_title'] ?? '— Student Identity Card —' }}</div>
                </div>
            </div>

            {{-- Body --}}
            <div class="card-body">
                <div class="photo-section">
                    <div class="student-photo">
                        @if($student->user->avatar)
                            <img src="{{ public_path('storage/' . $student->user->avatar) }}" alt="{{ $student->user->name }}">
                        @else
                            <div class="photo-placeholder">Photo</div>
                        @endif
                    </div>
                </div>

                <div class="info-section">
                    <div class="student-name">{{ $student->user->name }}</div>

                    <div class="info-table">
                        <div class="info-row">
                            <span class="info-label">{{ $labels['student_id'] ?? 'Reg. No.' }}</span>
                            <span class="info-value">{{ $student->student_id }}</span>
                        </div>

                        @if(in_array('academic_level', $enabledFields) && $student->academicLevel)
                            <div class="info-row">
                                <span class="info-label">{{ $labels['academic_level'] ?? 'Class' }}</span>
                                <span class="info-value">{{ $student->academicLevel->name }}</span>
                            </div>
                        @endif

                        @if(in_array('student_group', $enabledFields) && $student->studentGroup)
                            <div class="info-row">
                                <span class="info-label">{{ $labels['student_group'] ?? 'Section' }}</span>
                                <span class="info-value">{{ $student->studentGroup->name }}</span>
                            </div>
                        @endif

                        @if(in_array('date_of_birth', $enabledFields) && $student->date_of_birth)
                            <div class="info-row">
                                <span class="info-label">{{ $labels['date_of_birth'] ?? 'D.O.B.' }}</span>
                                <span class="info-value">{{ $student->date_of_birth->format('d M Y') }}</span>
                            </div>
                        @endif

                        @if(in_array('blood_group', $enabledFields) && $student->blood_group)
                            <div class="info-row">
                                <span class="info-label">{{ $labels['blood_group'] ?? 'Blood Grp.' }}</span>
                                <span class="info-value">{{ $student->blood_group }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="card-footer">
                <div class="validity-section">
                    <div class="validity-row">
                        <div class="validity-item">
                            <span class="validity-label">{{ $labels['issue_date'] ?? 'Issued:' }}</span>
                            <span class="validity-value">{{ $idCard->issue_date->format('d/m/Y') }}</span>
                        </div>
                        <div class="validity-item">
                            <span class="validity-label">{{ $labels['expiry_date'] ?? 'Valid Till:' }}</span>
                            <span class="validity-value">{{ $idCard->expiry_date->format('d/m/Y') }}</span>
                        </div>
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
    </div>
</body>
</html>
