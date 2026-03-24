@props(['certificate', 'template' => null])

@php
    $defaultFields = $template?->default_fields ?? [];
    $customData = $certificate->custom_data ?? [];

    $title = $defaultFields['title'] ?? 'Certificate of Achievement';
    $subtitle = $defaultFields['subtitle'] ?? 'This is to certify that';
    $bodyText = $defaultFields['body_text'] ?? 'has successfully completed the professional course';
    $footerText = $defaultFields['footer_text'] ?? 'Date of Completion';
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
            font-family: 'Arial', 'Helvetica', sans-serif;
            background: #1e3a5f;
            width: 297mm;
            height: 210mm;
        }
        .certificate {
            width: 297mm;
            height: 210mm;
            position: relative;
            background: linear-gradient(135deg, #1e3a5f 0%, #0f2744 100%);
            overflow: hidden;
        }

        /* Background Pattern */
        .bg-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image:
                radial-gradient(circle at 20% 80%, rgba(59, 130, 246, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(16, 185, 129, 0.1) 0%, transparent 50%);
        }

        /* Gold Accent Lines */
        .accent-top {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5mm;
            background: linear-gradient(90deg, #b8860b, #daa520, #ffd700, #daa520, #b8860b);
        }
        .accent-bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 5mm;
            background: linear-gradient(90deg, #b8860b, #daa520, #ffd700, #daa520, #b8860b);
        }

        /* Inner Frame */
        .inner-frame {
            position: absolute;
            top: 12mm;
            left: 12mm;
            right: 12mm;
            bottom: 12mm;
            border: 1px solid rgba(218, 165, 32, 0.3);
        }

        .content {
            position: relative;
            z-index: 1;
            height: 100%;
            padding: 20mm 30mm;
            display: flex;
            flex-direction: column;
            color: white;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10mm;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 4mm;
        }

        .logo-icon {
            width: 18mm;
            height: 18mm;
            background: linear-gradient(135deg, #daa520, #b8860b);
            border-radius: 2mm;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            color: #1e3a5f;
        }

        .org-info {
            display: flex;
            flex-direction: column;
        }

        .org-name {
            font-size: 16px;
            font-weight: bold;
            color: #daa520;
            letter-spacing: 1px;
        }

        .org-tagline {
            font-size: 10px;
            color: rgba(255,255,255,0.6);
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .header-badge {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .badge-shield {
            width: 25mm;
            height: 30mm;
            background: linear-gradient(180deg, #daa520 0%, #b8860b 100%);
            clip-path: polygon(0 0, 100% 0, 100% 70%, 50% 100%, 0 70%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #1e3a5f;
        }

        .badge-icon {
            font-size: 18px;
            margin-bottom: 1mm;
        }

        .badge-text {
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Title Section */
        .title-section {
            text-align: center;
            margin-bottom: 8mm;
        }

        .certificate-title {
            font-size: 42px;
            font-weight: bold;
            color: #daa520;
            text-transform: uppercase;
            letter-spacing: 8px;
            margin-bottom: 2mm;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .title-underline {
            width: 120mm;
            height: 1px;
            background: linear-gradient(90deg, transparent, #daa520, transparent);
            margin: 0 auto;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .recipient-intro {
            font-size: 14px;
            color: rgba(255,255,255,0.8);
            margin-bottom: 5mm;
            font-style: italic;
        }

        .recipient-name {
            font-size: 36px;
            font-weight: bold;
            color: white;
            margin-bottom: 3mm;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }

        .name-decoration {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5mm;
            margin-bottom: 8mm;
        }

        .decoration-line {
            width: 30mm;
            height: 1px;
            background: #daa520;
        }

        .decoration-diamond {
            width: 3mm;
            height: 3mm;
            background: #daa520;
            transform: rotate(45deg);
        }

        .course-intro {
            font-size: 12px;
            color: rgba(255,255,255,0.7);
            margin-bottom: 3mm;
        }

        .course-name {
            font-size: 22px;
            font-weight: 600;
            color: #10b981;
            margin-bottom: 8mm;
            max-width: 200mm;
        }

        .achievement-badges {
            display: flex;
            justify-content: center;
            gap: 10mm;
            margin-bottom: 8mm;
        }

        .achievement-badge {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 3mm 5mm;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(218, 165, 32, 0.3);
            border-radius: 2mm;
        }

        .achievement-value {
            font-size: 18px;
            font-weight: bold;
            color: #daa520;
        }

        .achievement-label {
            font-size: 8px;
            color: rgba(255,255,255,0.6);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Footer */
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding-top: 8mm;
            border-top: 1px solid rgba(218, 165, 32, 0.2);
        }

        .signatures {
            display: flex;
            gap: 25mm;
        }

        .signature-block {
            text-align: center;
        }

        .signature-line {
            width: 55mm;
            height: 1px;
            background: rgba(255,255,255,0.5);
            margin-bottom: 2mm;
        }

        .signature-label {
            font-size: 9px;
            color: rgba(255,255,255,0.6);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .certificate-info {
            text-align: right;
        }

        .info-row {
            display: flex;
            justify-content: flex-end;
            gap: 2mm;
            margin-bottom: 1mm;
        }

        .info-label {
            font-size: 8px;
            color: rgba(255,255,255,0.5);
        }

        .info-value {
            font-size: 8px;
            color: rgba(255,255,255,0.8);
            font-family: 'Courier New', monospace;
        }

        /* Seal */
        .official-seal {
            position: absolute;
            bottom: 30mm;
            right: 35mm;
            width: 35mm;
            height: 35mm;
        }

        .seal-outer {
            width: 100%;
            height: 100%;
            background: linear-gradient(145deg, #daa520, #b8860b);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 3mm 6mm rgba(0,0,0,0.4);
        }

        .seal-inner {
            width: 28mm;
            height: 28mm;
            background: #1e3a5f;
            border-radius: 50%;
            border: 2px solid #daa520;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .seal-text {
            font-size: 6px;
            color: #daa520;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: center;
        }

        .seal-icon {
            font-size: 14px;
            color: #daa520;
            margin: 1mm 0;
        }

        /* Corner Accents */
        .corner-accent {
            position: absolute;
            width: 15mm;
            height: 15mm;
            border: 2px solid rgba(218, 165, 32, 0.3);
        }
        .corner-tl {
            top: 15mm;
            left: 15mm;
            border-right: none;
            border-bottom: none;
        }
        .corner-tr {
            top: 15mm;
            right: 15mm;
            border-left: none;
            border-bottom: none;
        }
        .corner-bl {
            bottom: 15mm;
            left: 15mm;
            border-right: none;
            border-top: none;
        }
        .corner-br {
            bottom: 15mm;
            right: 15mm;
            border-left: none;
            border-top: none;
        }
    </style>
</head>
<body>
    <div class="certificate">
        {{-- Background --}}
        <div class="bg-pattern"></div>
        <div class="accent-top"></div>
        <div class="accent-bottom"></div>
        <div class="inner-frame"></div>

        {{-- Corner Accents --}}
        <div class="corner-accent corner-tl"></div>
        <div class="corner-accent corner-tr"></div>
        <div class="corner-accent corner-bl"></div>
        <div class="corner-accent corner-br"></div>

        {{-- Content --}}
        <div class="content">
            {{-- Header --}}
            <div class="header">
                <div class="logo-section">
                    <div class="logo-icon">PRO</div>
                    <div class="org-info">
                        <span class="org-name">{{ $certificate->course?->school?->name ?? config('app.name') }}</span>
                        <span class="org-tagline">Professional Development</span>
                    </div>
                </div>

                <div class="header-badge">
                    <div class="badge-shield">
                        <span class="badge-icon">★</span>
                        <span class="badge-text">Certified</span>
                    </div>
                </div>
            </div>

            {{-- Title --}}
            <div class="title-section">
                <h1 class="certificate-title">{{ $title }}</h1>
                <div class="title-underline"></div>
            </div>

            {{-- Main Content --}}
            <div class="main-content">
                <p class="recipient-intro">{{ $subtitle }}</p>

                <h2 class="recipient-name">{{ $certificate->recipient_name }}</h2>

                <div class="name-decoration">
                    <div class="decoration-line"></div>
                    <div class="decoration-diamond"></div>
                    <div class="decoration-line"></div>
                </div>

                <p class="course-intro">{{ $bodyText }}</p>

                <h3 class="course-name">{{ $customData['course_title'] ?? $certificate->course?->title ?? 'Course Name' }}</h3>

                <div class="achievement-badges">
                    <div class="achievement-badge">
                        <span class="achievement-value">{{ $certificate->issue_date->format('M d, Y') }}</span>
                        <span class="achievement-label">{{ $footerText }}</span>
                    </div>

                    @if(isset($customData['final_grade']))
                        <div class="achievement-badge">
                            <span class="achievement-value">{{ number_format($customData['final_grade'], 1) }}%</span>
                            <span class="achievement-label">Final Score</span>
                        </div>
                    @endif

                    @if(isset($customData['duration']))
                        <div class="achievement-badge">
                            <span class="achievement-value">{{ $customData['duration'] }}h</span>
                            <span class="achievement-label">Duration</span>
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

                <div class="certificate-info">
                    <div class="info-row">
                        <span class="info-label">Certificate ID:</span>
                        <span class="info-value">{{ $certificate->certificate_number }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Verification:</span>
                        <span class="info-value">{{ substr($certificate->verification_code, 0, 8) }}...</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Official Seal --}}
        <div class="official-seal">
            <div class="seal-outer">
                <div class="seal-inner">
                    <span class="seal-text">Official</span>
                    <span class="seal-icon">✦</span>
                    <span class="seal-text">Seal</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
