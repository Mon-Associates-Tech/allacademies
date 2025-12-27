<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student ID Card - {{ $student->user->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .id-card {
            width: 350px;
            margin: 20px auto;
            border: 3px solid #1e40af;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .id-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 15px;
            text-align: center;
        }
        .id-body { padding: 20px; background: white; }
        .photo-section { text-align: center; margin-bottom: 15px; }
        .photo-placeholder {
            width: 120px;
            height: 140px;
            margin: 0 auto;
            border: 2px solid #3b82f6;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .info-row {
            margin: 8px 0;
            padding: 5px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .label {
            font-weight: bold;
            color: #1e40af;
            display: inline-block;
            width: 110px;
        }
        .barcode-section {
            text-align: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px dashed #cbd5e1;
        }
        .id-footer {
            background: #f3f4f6;
            padding: 10px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }
    </style>
</head>
<body>

@php
    $school = $student->school ?? $student->user->school;
@endphp
<!-- Include letterhead -->
@include('components.letterheads.' . ($school->letterhead_template ?? 'classic'), [
    'school' => $school,
    'title' => 'Student Identification Card'
])

<div style="padding: 20px;">
    <!-- Front of ID Card -->
    <div class="id-card">
        <div class="id-header">
            <h2 style="font-size: 18px; margin: 0;">STUDENT ID CARD</h2>
            <p style="font-size: 11px; margin-top: 5px;">{{ $school->name }}</p>
        </div>

        <div class="id-body">
            <div class="photo-section">
                @if($student->user->avatar)
                    <img src="{{ public_path('storage/' . $student->user->avatar) }}"
                         alt="{{ $student->user->name }}"
                         style="width: 120px; height: 140px; object-fit: cover; border: 2px solid #3b82f6;">
                @else
                    <div class="photo-placeholder">
                        <span style="color: #9ca3af;">Photo</span>
                    </div>
                @endif
            </div>

            <div class="info-row">
                <span class="label">Name:</span>
                <span style="font-weight: bold;">{{ $student->user->name }}</span>
            </div>

            <div class="info-row">
                <span class="label">Student ID:</span>
                <span style="font-weight: bold; color: #1e40af;">{{ $student->student_id }}</span>
            </div>

            <div class="info-row">
                <span class="label">Card Number:</span>
                <span>{{ $idCard->card_number }}</span>
            </div>

            <div class="info-row">
                <span class="label">Academic Level:</span>
                <span>{{ $student->academicLevel->name ?? 'N/A' }}</span>
            </div>

            <div class="info-row">
                <span class="label">Class/Group:</span>
                <span>{{ $student->studentGroup->name ?? 'N/A' }}</span>
            </div>

            <div class="info-row" style="border-bottom: none;">
                <span class="label">Date of Birth:</span>
                <span>{{ $student->date_of_birth ? $student->date_of_birth->format('d/m/Y') : 'N/A' }}</span>
            </div>

            <div class="barcode-section">
                <div style="background: repeating-linear-gradient(90deg, #000 0px, #000 2px, #fff 2px, #fff 4px);
                                height: 50px; width: 200px; margin: 0 auto;"></div>
                <p style="font-size: 10px; margin-top: 5px; font-family: monospace;">
                    {{ $idCard->barcode }}
                </p>
            </div>

            <div style="margin-top: 15px; text-align: center;">
                <div style="display: inline-block; text-align: left;">
                    <div style="font-size: 10px; color: #6b7280;">
                        <strong>Issue Date:</strong> {{ $idCard->issue_date->format('d/m/Y') }}<br>
                        <strong>Expiry Date:</strong> {{ $idCard->expiry_date->format('d/m/Y') }}<br>
                        <strong>Status:</strong> <span style="color: #059669; font-weight: bold;">{{ ucfirst($idCard->status) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="id-footer">
            <p><strong>Emergency Contact:</strong> {{ $school->phone }}</p>
            <p style="margin-top: 3px;">This card is property of {{ $school->name }}</p>
        </div>
    </div>

    <!-- Instructions -->
    <div style="margin-top: 30px; padding: 15px; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 4px;">
        <h4 style="color: #92400e; margin-bottom: 8px;">Important Instructions:</h4>
        <ul style="margin-left: 20px; color: #78350f; font-size: 11px; line-height: 1.6;">
            <li>This card must be carried at all times while on school premises</li>
            <li>Report any loss or damage immediately to the administration office</li>
            <li>This card is non-transferable and must not be shared</li>
            <li>Card must be renewed before expiry date</li>
            <li>Replacement fee applies for lost or damaged cards</li>
        </ul>
    </div>

    <!-- Footer -->
    <div style="margin-top: 20px; text-align: center; font-size: 10px; color: #6b7280;">
        <p>Generated on {{ now()->format('F d, Y \a\t H:i A') }}</p>
    </div>
</div>
</body>
</html>
