<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Library Card - {{ $student->user->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        .card-container {
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
        }
        .library-card {
            border: 2px solid #2563eb;
            border-radius: 10px;
            padding: 20px;
            background: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #2563eb;
        }
        .card-title {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
            margin: 10px 0;
        }
        .card-body {
            padding: 20px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 10px;
            background: #f3f4f6;
            border-radius: 5px;
        }
        .info-label {
            font-weight: bold;
            color: #374151;
            width: 40%;
        }
        .info-value {
            color: #1f2937;
            width: 60%;
        }
        .student-photo {
            width: 120px;
            height: 120px;
            border-radius: 10px;
            object-fit: cover;
            border: 3px solid #2563eb;
            margin: 0 auto;
            display: block;
        }
        .barcode-section {
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            background: #f9fafb;
            border-radius: 5px;
        }
        .barcode {
            font-family: 'Courier New', monospace;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #111827;
            margin-top: 10px;
        }
        .card-footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 11px;
            color: #6b7280;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-active {
            background: #d1fae5;
            color: #065f46;
        }
        .status-expired {
            background: #fee2e2;
            color: #991b1b;
        }
        .card-type-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            background: #dbeafe;
            color: #1e40af;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        td {
            padding: 10px;
            border: 1px solid #e5e7eb;
        }
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 11px;
        }
    </style>
</head>
<body>
<!-- Include selected letterhead -->
@include('components.letterheads.' . ($student->school->letterhead_template ?? 'classic'), [
    'school' => $student->school,
    'title' => 'Library Card'
])

<div class="card-container">
    <div class="library-card">
        <!-- Card Header -->
        <div class="card-header">
            <h2 class="card-title">📚 LIBRARY MEMBERSHIP CARD</h2>
            <div>
                <span class="card-type-badge">{{ strtoupper($libraryCard->card_type) }} MEMBERSHIP</span>
                <span class="status-badge status-{{ $libraryCard->status }}">
                        {{ strtoupper($libraryCard->status) }}
                    </span>
            </div>
        </div>

        <!-- Card Body -->
        <div class="card-body">
            <table>
                <tr>
                    <td colspan="2" style="text-align: center; background: #f9fafb;">
                        @if($student->user->avatar)
                            <img src="{{ public_path('storage/' . $student->user->avatar) }}"
                                 alt="{{ $student->user->name }}"
                                 class="student-photo">
                        @else
                            <div style="width: 120px; height: 120px; background: #e5e7eb; border-radius: 10px; margin: 0 auto; display: flex; align-items: center; justify-content: center; border: 3px solid #2563eb;">
                                <svg style="width: 60px; height: 60px; color: #9ca3af;" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold; width: 35%; background: #f3f4f6;">Card Number:</td>
                    <td style="font-weight: bold; font-size: 14px; color: #1e40af;">{{ $libraryCard->card_number }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; background: #f3f4f6;">Member Name:</td>
                    <td style="font-weight: bold;">{{ $student->user->name }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; background: #f3f4f6;">Student ID:</td>
                    <td>{{ $student->student_id }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; background: #f3f4f6;">Academic Level:</td>
                    <td>{{ $student->academicLevel->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; background: #f3f4f6;">Email:</td>
                    <td>{{ $student->user->email }}</td>
                </tr>
                @if($student->user->phone)
                    <tr>
                        <td style="font-weight: bold; background: #f3f4f6;">Phone:</td>
                        <td>{{ $student->user->phone }}</td>
                    </tr>
                @endif
                <tr>
                    <td style="font-weight: bold; background: #f3f4f6;">Card Type:</td>
                    <td>{{ ucfirst($libraryCard->card_type) }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; background: #f3f4f6;">Issue Date:</td>
                    <td>{{ $libraryCard->issued_date->format('F d, Y') }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; background: #f3f4f6;">Expiry Date:</td>
                    <td style="color: {{ $libraryCard->expiry_date->isPast() ? '#dc2626' : '#059669' }}; font-weight: bold;">
                        {{ $libraryCard->expiry_date->format('F d, Y') }}
                        @if($libraryCard->expiry_date->isPast())
                            <span style="color: #dc2626; font-size: 10px;">(EXPIRED)</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold; background: #f3f4f6;">Status:</td>
                    <td>
                            <span class="status-badge status-{{ $libraryCard->status }}">
                                {{ ucfirst($libraryCard->status) }}
                            </span>
                    </td>
                </tr>
            </table>

            <!-- Barcode Section -->
            <div class="barcode-section">
                <p style="margin: 0; font-size: 11px; color: #6b7280; text-transform: uppercase;">Barcode</p>
                <div class="barcode">{{ $libraryCard->barcode }}</div>
                <p style="margin-top: 5px; font-size: 10px; color: #9ca3af;">
                    Present this card when borrowing books
                </p>
            </div>

            <!-- Library Rules -->
            <div style="margin-top: 20px; padding: 15px; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 5px;">
                <h4 style="margin: 0 0 10px 0; color: #92400e; font-size: 13px;">📖 Library Card Holder Responsibilities:</h4>
                <ul style="margin: 0; padding-left: 20px; font-size: 11px; color: #78350f; line-height: 1.6;">
                    <li>This card is non-transferable and must be presented when borrowing materials</li>
                    <li>Maximum borrowing period is 14 days (may vary by material type)</li>
                    <li>Report lost or damaged cards immediately to the library desk</li>
                    <li>Late returns may incur fines as per library policy</li>
                    <li>Keep your card safe - you are responsible for all items borrowed on it</li>
                    <li>Card renewal required before expiry date</li>
                </ul>
            </div>

            @if($libraryCard->card_type === 'premium')
                <!-- Premium Benefits -->
                <div style="margin-top: 15px; padding: 15px; background: #ede9fe; border-left: 4px solid #7c3aed; border-radius: 5px;">
                    <h4 style="margin: 0 0 10px 0; color: #5b21b6; font-size: 13px;">⭐ Premium Membership Benefits:</h4>
                    <ul style="margin: 0; padding-left: 20px; font-size: 11px; color: #6d28d9; line-height: 1.6;">
                        <li>Extended borrowing period (21 days)</li>
                        <li>Borrow up to 10 items simultaneously</li>
                        <li>Priority reservation on new arrivals</li>
                        <li>Access to premium digital resources</li>
                        <li>No late fees for first 3 days overdue</li>
                    </ul>
                </div>
            @endif

            <!-- Signatures -->
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line">Librarian Signature</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">Card Holder Signature</div>
                </div>
            </div>
        </div>

        <!-- Card Footer -->
        <div class="card-footer">
            <p style="margin: 5px 0;">
                <strong>{{ $student->school->name }}</strong>
            </p>
            <p style="margin: 5px 0;">
                {{ $student->school->address }}, {{ $student->school->city }}
            </p>
            <p style="margin: 5px 0;">
                📞 {{ $student->school->phone }} | 📧 {{ $student->school->email }}
            </p>
            <p style="margin: 10px 0 0 0; font-size: 10px; color: #9ca3af;">
                Generated on {{ now()->format('F d, Y \a\t h:i A') }}
            </p>
        </div>
    </div>
</div>
</body>
</html>
