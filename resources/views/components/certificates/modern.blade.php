@props(['certificate', 'template' => null])

@php
    $defaultFields = $template?->default_fields ?? [];
    $customData = $certificate->custom_data ?? [];

    $title = $defaultFields['title'] ?? 'Certificate of Completion';
    $subtitle = $defaultFields['subtitle'] ?? 'This certifies that';
    $bodyText = $defaultFields['body_text'] ?? 'has successfully completed';
    $footerText = $defaultFields['footer_text'] ?? 'Issued on';
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
            font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', sans-serif;
            background: #ffffff;
            width: 297mm;
            height: 210mm;
        }
        .certificate {
            width: 297mm;
            height: 210mm;
            position: relative;
            background: #ffffff;
            overflow: hidden;
        }

        /* Geometric Background Elements */
        .bg-shape-1 {
            position: absolute;
            top: -50mm;
            right: -30mm;
            width: 150mm;
            height: 150mm;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border-radius: 50%;
            opacity: 0.1;
        }
        .bg-shape-2 {
            position: absolute;
            bottom: -40mm;
            left: -40mm;
            width: 120mm;
            height: 120mm;
            background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
            border-radius: 50%;
            opacity: 0.08;
        }
        .bg-line {
            position: absolute;
            top: 0;
            left: 40mm;
            width: 3mm;
            height: 100%;
            background: linear-gradient(180deg, #3b82f6 0%, #8b5cf6 50%, #ec4899 100%);
        }

        .content {
            position: relative;
            z-index: 1;
            height: 100%;
            padding: 20mm 25mm 20mm 55mm;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .header {
            margin-bottom: 15mm;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 5mm;
            margin-bottom: 10mm;
        }

        .logo-placeholder {
            width: 15mm;
            height: 15mm;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 3mm;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 10px;
        }

        .org-name {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            letter-spacing: 1px;
        }

        .certificate-type {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .title-section {
            margin-bottom: 5mm;
        }

        .certificate-title {
            font-size: 48px;
            font-weight: 300;
            color: #1f2937;
            letter-spacing: -1px;
            line-height: 1.1;
        }

        .certificate-title span {
            font-weight: 700;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .recipient-section {
            margin-bottom: 10mm;
        }

        .recipient-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 3mm;
        }

        .recipient-name {
            font-size: 38px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 2mm;
        }

        .recipient-underline {
            width: 100mm;
            height: 2px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6, transparent);
        }

        .course-section {
            margin-bottom: 10mm;
        }

        .course-label {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 2mm;
        }

        .course-name {
            font-size: 20px;
            font-weight: 600;
            color: #374151;
        }

        .details-row {
            display: flex;
            gap: 15mm;
            margin-bottom: 10mm;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-size: 10px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1mm;
        }

        .detail-value {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
        }

        .grade-chip {
            display: inline-flex;
            align-items: center;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: white;
            padding: 2mm 5mm;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        /* Footer */
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding-top: 10mm;
            border-top: 1px solid #e5e7eb;
        }

        .signatures {
            display: flex;
            gap: 20mm;
        }

        .signature-block {
            text-align: left;
        }

        .signature-line {
            width: 50mm;
            height: 1px;
            background: #374151;
            margin-bottom: 2mm;
        }

        .signature-label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .certificate-meta {
            text-align: right;
        }

        .certificate-number {
            font-size: 9px;
            color: #9ca3af;
            font-family: 'Courier New', monospace;
            margin-bottom: 1mm;
        }

        .verification-qr {
            width: 20mm;
            height: 20mm;
            background: repeating-conic-gradient(#374151 0% 25%, #fff 0% 50%) 50% / 3mm 3mm;
            margin-left: auto;
        }

        .verification-text {
            font-size: 8px;
            color: #9ca3af;
            margin-top: 1mm;
        }

        /* Badge */
        .completion-badge {
            position: absolute;
            top: 20mm;
            right: 25mm;
            width: 35mm;
            height: 35mm;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 4mm 8mm rgba(59, 130, 246, 0.3);
        }

        .badge-icon {
            font-size: 16px;
            margin-bottom: 1mm;
        }

        .badge-text {
            font-size: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .badge-year {
            font-size: 12px;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="certificate">
        {{-- Background Elements --}}
        <div class="bg-shape-1"></div>
        <div class="bg-shape-2"></div>
        <div class="bg-line"></div>

        {{-- Completion Badge --}}
        <div class="completion-badge">
            <div class="badge-icon">✓</div>
            <div class="badge-text">Completed</div>
            <div class="badge-year">{{ $certificate->issue_date->format('Y') }}</div>
        </div>

        {{-- Content --}}
        <div class="content">
            {{-- Header --}}
            <div class="header">
                <div class="logo-section">
                    <div class="logo-placeholder">LMS</div>
                    <div>
                        <div class="org-name">{{ $certificate->course?->school?->name ?? config('app.name') }}</div>
                        <div class="certificate-type">Online Learning Platform</div>
                    </div>
                </div>

                <div class="title-section">
                    <h1 class="certificate-title">Certificate of<br><span>Completion</span></h1>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="main-content">
                <div class="recipient-section">
                    <p class="recipient-label">{{ $subtitle }}</p>
                    <h2 class="recipient-name">{{ $certificate->recipient_name }}</h2>
                    <div class="recipient-underline"></div>
                </div>

                <div class="course-section">
                    <p class="course-label">{{ $bodyText }}</p>
                    <h3 class="course-name">{{ $customData['course_title'] ?? $certificate->course?->title ?? 'Course Name' }}</h3>
                </div>

                <div class="details-row">
                    <div class="detail-item">
                        <span class="detail-label">{{ $footerText }}</span>
                        <span class="detail-value">{{ $certificate->issue_date->format('F d, Y') }}</span>
                    </div>

                    @if(isset($customData['duration']))
                        <div class="detail-item">
                            <span class="detail-label">Duration</span>
                            <span class="detail-value">{{ $customData['duration'] }} hours</span>
                        </div>
                    @endif

                    @if(isset($customData['final_grade']))
                        <div class="detail-item">
                            <span class="detail-label">Final Grade</span>
                            <span class="grade-chip">{{ number_format($customData['final_grade'], 1) }}%</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Footer --}}
            <div class="footer">
                <div class="signatures">
                    <div class="signature-block">
                        <div class="signature-line"></div>
                        <p class="signature-label">{{ $signatureLabel }}</p>
                    </div>
                    <div class="signature-block">
                        <div class="signature-line"></div>
                        <p class="signature-label">Program Director</p>
                    </div>
                </div>

                <div class="certificate-meta">
                    <p class="certificate-number">ID: {{ $certificate->certificate_number }}</p>
                    <div class="verification-qr"></div>
                    <p class="verification-text">Scan to verify</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
