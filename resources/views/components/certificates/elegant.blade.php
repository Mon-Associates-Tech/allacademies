@props(['certificate', 'template' => null])

@php
    $defaultFields = $template?->default_fields ?? [];
    $customData = $certificate->custom_data ?? [];

    $title = $defaultFields['title'] ?? 'Certificate of Completion';
    $subtitle = $defaultFields['subtitle'] ?? 'This is to certify that';
    $bodyText = $defaultFields['body_text'] ?? 'has successfully completed the course';
    $footerText = $defaultFields['footer_text'] ?? 'Awarded on';
    $signatureLabel = $defaultFields['signature_label'] ?? 'Authorized Signature';
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate - {{ $certificate->recipient_name }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Georgia', 'Times New Roman', serif;
            background: #fffbeb;
            width: 297mm;
            height: 210mm;
        }
        .certificate {
            width: 297mm;
            height: 210mm;
            position: relative;
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 50%, #fde68a 100%);
            overflow: hidden;
        }

        /* Ornate Border */
        .border-outer {
            position: absolute;
            top: 8mm;
            left: 8mm;
            right: 8mm;
            bottom: 8mm;
            border: 3px solid #92400e;
            border-radius: 5px;
        }
        .border-middle {
            position: absolute;
            top: 12mm;
            left: 12mm;
            right: 12mm;
            bottom: 12mm;
            border: 1px solid #b45309;
        }
        .border-inner {
            position: absolute;
            top: 15mm;
            left: 15mm;
            right: 15mm;
            bottom: 15mm;
            border: 2px double #d97706;
        }

        /* Corner Ornaments */
        .corner {
            position: absolute;
            width: 25mm;
            height: 25mm;
            border: 2px solid #92400e;
        }
        .corner-tl {
            top: 18mm;
            left: 18mm;
            border-right: none;
            border-bottom: none;
            border-top-left-radius: 15px;
        }
        .corner-tr {
            top: 18mm;
            right: 18mm;
            border-left: none;
            border-bottom: none;
            border-top-right-radius: 15px;
        }
        .corner-bl {
            bottom: 18mm;
            left: 18mm;
            border-right: none;
            border-top: none;
            border-bottom-left-radius: 15px;
        }
        .corner-br {
            bottom: 18mm;
            right: 18mm;
            border-left: none;
            border-top: none;
            border-bottom-right-radius: 15px;
        }

        .content {
            position: absolute;
            top: 25mm;
            left: 25mm;
            right: 25mm;
            bottom: 25mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        /* Decorative Header */
        .header-ornament {
            font-size: 24px;
            color: #92400e;
            letter-spacing: 8px;
            margin-bottom: 5mm;
        }

        .certificate-title {
            font-size: 42px;
            font-weight: bold;
            color: #78350f;
            text-transform: uppercase;
            letter-spacing: 6px;
            margin-bottom: 3mm;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }

        .certificate-subtitle {
            font-size: 16px;
            color: #92400e;
            font-style: italic;
            margin-bottom: 8mm;
            letter-spacing: 2px;
        }

        .divider {
            width: 80mm;
            height: 1px;
            background: linear-gradient(90deg, transparent, #92400e, transparent);
            margin: 5mm 0;
        }

        .recipient-label {
            font-size: 14px;
            color: #a16207;
            margin-bottom: 3mm;
            font-style: italic;
        }

        .recipient-name {
            font-size: 36px;
            font-weight: bold;
            color: #1e3a5f;
            font-family: 'Brush Script MT', 'Segoe Script', cursive;
            margin-bottom: 5mm;
            padding: 0 10mm;
            border-bottom: 2px solid #92400e;
            padding-bottom: 3mm;
        }

        .course-label {
            font-size: 14px;
            color: #a16207;
            margin-bottom: 2mm;
            font-style: italic;
        }

        .course-name {
            font-size: 22px;
            font-weight: bold;
            color: #78350f;
            margin-bottom: 8mm;
            max-width: 200mm;
        }

        .completion-info {
            font-size: 12px;
            color: #92400e;
            margin-bottom: 2mm;
        }

        .completion-date {
            font-size: 16px;
            color: #78350f;
            font-weight: 600;
            margin-bottom: 10mm;
        }

        .grade-section {
            display: flex;
            align-items: center;
            gap: 5mm;
            margin-bottom: 10mm;
        }

        .grade-badge {
            background: linear-gradient(145deg, #92400e, #b45309);
            color: #fef3c7;
            padding: 3mm 6mm;
            border-radius: 3mm;
            font-size: 14px;
            font-weight: bold;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            width: 100%;
            max-width: 220mm;
            margin-top: 10mm;
        }

        .signature-block {
            text-align: center;
            width: 70mm;
        }

        .signature-line {
            width: 60mm;
            height: 0.5mm;
            background: #92400e;
            margin: 0 auto 2mm;
        }

        .signature-label {
            font-size: 11px;
            color: #92400e;
        }

        .certificate-number {
            position: absolute;
            bottom: 20mm;
            left: 30mm;
            font-size: 9px;
            color: #a16207;
            font-family: 'Courier New', monospace;
        }

        .verification-code {
            position: absolute;
            bottom: 20mm;
            right: 30mm;
            font-size: 9px;
            color: #a16207;
            font-family: 'Courier New', monospace;
        }

        /* Seal */
        .seal {
            position: absolute;
            bottom: 35mm;
            right: 40mm;
            width: 30mm;
            height: 30mm;
            background: linear-gradient(145deg, #dc2626, #991b1b);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2mm 4mm rgba(0,0,0,0.3);
            border: 2px solid #7f1d1d;
        }

        .seal-inner {
            width: 24mm;
            height: 24mm;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fef2f2;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            line-height: 1.3;
        }

        /* Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 100px;
            color: rgba(146, 64, 14, 0.03);
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 10px;
            pointer-events: none;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="certificate">
        {{-- Borders --}}
        <div class="border-outer"></div>
        <div class="border-middle"></div>
        <div class="border-inner"></div>

        {{-- Corner Ornaments --}}
        <div class="corner corner-tl"></div>
        <div class="corner corner-tr"></div>
        <div class="corner corner-bl"></div>
        <div class="corner corner-br"></div>

        {{-- Watermark --}}
        <div class="watermark">CERTIFIED</div>

        {{-- Content --}}
        <div class="content">
            <div class="header-ornament">✦ ✦ ✦</div>

            <h1 class="certificate-title">{{ $title }}</h1>

            <div class="divider"></div>

            <p class="recipient-label">{{ $subtitle }}</p>

            <h2 class="recipient-name">{{ $certificate->recipient_name }}</h2>

            <p class="course-label">{{ $bodyText }}</p>

            <h3 class="course-name">{{ $customData['course_title'] ?? $certificate->course?->title ?? 'Course Name' }}</h3>

            @if(isset($customData['final_grade']))
                <div class="grade-section">
                    <span class="completion-info">with a grade of</span>
                    <span class="grade-badge">{{ number_format($customData['final_grade'], 1) }}%</span>
                </div>
            @endif

            <p class="completion-info">{{ $footerText }}</p>
            <p class="completion-date">{{ $certificate->issue_date->format('F d, Y') }}</p>

            <div class="signatures">
                <div class="signature-block">
                    <div class="signature-line"></div>
                    <p class="signature-label">{{ $signatureLabel }}</p>
                </div>
                <div class="signature-block">
                    <div class="signature-line"></div>
                    <p class="signature-label">Director / Principal</p>
                </div>
            </div>
        </div>

        {{-- Seal --}}
        <div class="seal">
            <div class="seal-inner">
                Official<br>Seal
            </div>
        </div>

        {{-- Certificate Info --}}
        <div class="certificate-number">
            Certificate No: {{ $certificate->certificate_number }}
        </div>
        <div class="verification-code">
            Verify at: {{ config('app.url') }}/verify/{{ $certificate->verification_code }}
        </div>
    </div>
</body>
</html>
