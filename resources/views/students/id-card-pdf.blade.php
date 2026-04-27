<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Student ID Card - {{ $student->user->name }}</title>
    <style>
        /* DOMPDF-Friendly Reset & Base */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #111827; }
        
        /* CR80 Standard Card Dimensions */
        .card {
            width: 85.6mm;
            height: 53.98mm;
            background: #ffffff;
            border: 0.5pt solid #e5e7eb;
            page-break-after: always;
            position: relative;
            overflow: hidden;
        }

        /* ================= FRONT SIDE ================= */
        .header {
            background-color: #0f172a;
            height: 12mm;
            padding: 0 4mm;
        }
        .header table { width: 100%; height: 100%; border-collapse: collapse; }
        .logo-cell { width: 10mm; text-align: left; vertical-align: middle; }
        .logo { width: 8mm; height: 8mm; border-radius: 50%; background: #fff; overflow: hidden; }
        .logo img { width: 100%; height: 100%; object-fit: cover; }
        .title-cell { text-align: left; vertical-align: middle; padding-left: 2mm; }
        .school-name { color: #ffffff; font-size: 11pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }

        .body-section { height: 31.98mm; padding: 3mm 4mm; }
        .body-section table { width: 100%; height: 100%; border-collapse: collapse; }
        .photo-cell { width: 24mm; vertical-align: top; padding-right: 3mm; }
        .photo-frame {
            width: 22mm; height: 22mm; border-radius: 50%; border: 2px solid #d1d5db;
            background: #f9fafb; overflow: hidden; text-align: center; vertical-align: middle;
        }
        .photo-frame img { width: 100%; height: 100%; object-fit: cover; }
        
        .info-cell { vertical-align: top; }
        .info-row { margin-bottom: 2.5mm; }
        .label { font-size: 6.5pt; color: #6b7280; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 0.5mm; }
        .value { font-size: 9pt; font-weight: bold; color: #111827; border-bottom: 0.5pt solid #d1d5db; padding-bottom: 0.5mm; }

        .footer-section {
            position: absolute; bottom: 0; left: 0; right: 0; height: 10mm;
            padding: 0 4mm; background: #ffffff;
        }
        .footer-section table { width: 100%; height: 100%; border-collapse: collapse; }
        .validity-cell { vertical-align: middle; font-size: 7pt; color: #4b5563; }
        .codes-cell { vertical-align: middle; text-align: right; }
        .qr-box { width: 12mm; height: 12mm; display: inline-block; vertical-align: middle; margin-right: 2mm; }
        .qr-box img { width: 100%; height: 100%; }
        .barcode-box { display: inline-block; vertical-align: middle; }
        .barcode-strip { height: 5mm; width: 24mm; background: #000000; }
        .barcode-text { font-size: 5pt; font-family: 'Courier New', monospace; letter-spacing: 1px; text-align: center; margin-top: 0.5mm; }

        /* ================= BACK SIDE ================= */
        .back-content { padding: 4mm; height: 47.98mm; }
        .section-title {
            font-size: 9pt; font-weight: bold; color: #0f172a; text-align: center;
            border-bottom: 0.5pt solid #d1d5db; padding-bottom: 1mm; margin-bottom: 2mm;
        }
        .contact-row { font-size: 7pt; color: #374151; margin-bottom: 1mm; line-height: 1.4; }
        .terms-box { font-size: 5.5pt; color: #6b7280; line-height: 1.3; margin: 2mm 0; }
        .school-info { font-size: 6pt; color: #4b5563; line-height: 1.3; margin-bottom: 3mm; }
        
        .signature-area { margin-top: 3mm; }
        .sig-line {
            width: 35mm; border-top: 0.5pt solid #374151; padding-top: 1mm;
            font-size: 6pt; text-align: center; color: #374151;
        }
        .property-notice { font-size: 5pt; color: #9ca3af; text-align: right; margin-top: 1mm; }

        .footer-bar {
            position: absolute; bottom: 0; left: 0; right: 0; height: 6mm;
            background: #0f172a; color: #ffffff; text-align: center;
            font-size: 6pt; line-height: 6mm; letter-spacing: 0.5px;
        }
    </style>
</head>
<body>

    <!-- FRONT SIDE -->
    <div class="card">
        <!-- Header -->
        <div class="header">
            <table>
                <tr>
                    <td class="logo-cell">
                        <div class="logo">
                            <img src="{{ $student->school->logo_url ?? 'https://via.placeholder.com/50' }}" alt="School Logo">
                        </div>
                    </td>
                    <td class="title-cell">
                        <div class="school-name">ALL ACADEMIES</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Main Content -->
        <div class="body-section">
            <table>
                <tr>
                    <td class="photo-cell">
                        <div class="photo-frame">
                            <img src="{{ $student->user->avatar ?? 'https://via.placeholder.com/150' }}" alt="Student Photo">
                        </div>
                    </td>
                    <td class="info-cell">
                        <div class="info-row">
                            <div class="label">Student Name</div>
                            <div class="value">{{ $student->user->name }}</div>
                        </div>
                        <div class="info-row">
                            <div class="label">Student ID</div>
                            <div class="value">{{ $idCard->card_number }}</div>
                        </div>
                        <div class="info-row">
                            <div class="label">Academic Level</div>
                            <div class="value">{{ $student->academicLevel->name ?? 'N/A' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="label">Group</div>
                            <div class="value">{{ $student->studentGroup->name ?? 'N/A' }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer-section">
            <table>
                <tr>
                    <td class="validity-cell">
                        Valid From: <strong>{{ $idCard->issue_date->format('d M Y') }}</strong><br>
                        Valid Until: <strong>{{ $idCard->expiry_date->format('d M Y') }}</strong>
                    </td>
                    <td class="codes-cell">
                        <div class="qr-box">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ $idCard->card_number }}" alt="QR Code">
                        </div>
                        <div class="barcode-box">
                            <div class="barcode-strip"></div>
                            <div class="barcode-text">{{ $idCard->barcode }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- BACK SIDE -->
    <div class="card">
        <div class="back-content">
            <div class="section-title">Emergency Contact</div>
            
            <div class="contact-row">📞 <strong>Primary:</strong> {{ $student->emergency_contact ?? '+1 (555) 123-4567' }}</div>
            <div class="contact-row">📞 <strong>Secondary:</strong> {{ $student->emergency_contact_2 ?? '+1 (555) 987-6543' }}</div>
            
            <div class="terms-box">
                Terms and Conditions apply. Card usage governed by All Academies Student Code of Conduct.
            </div>
            
            <div class="school-info">
                {{ $student->school->address ?? '123 Education Street, Learning City' }}<br>
                {{ $student->school->email ?? 'info@allacademies.edu' }}
            </div>
            
            <div class="signature-area">
                <div class="sig-line">Cardholder Signature</div>
                <div class="property-notice">Property of All Academies</div>
            </div>
        </div>
        
        <div class="footer-bar">{{ $student->school->website ?? 'https://allacademies.edu' }}</div>
    </div>

</body>
</html>