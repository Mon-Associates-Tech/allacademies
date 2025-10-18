<!DOCTYPE html>
<html>
<head>
    <title>ID Card - {{ $student->user->name }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .id-card {
            width: 300px;
            height: 200px;
            border: 2px solid #333;
            border-radius: 10px;
            padding: 15px;
            position: relative;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .header {
            text-align: center;
            border-bottom: 1px solid #333;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .school-name {
            font-size: 14px;
            font-weight: bold;
            margin: 0;
        }
        .id-title {
            font-size: 12px;
            margin: 5px 0;
        }
        .photo {
            width: 80px;
            height: 80px;
            border: 1px solid #333;
            float: left;
            margin-right: 15px;
            background-color: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            text-align: center;
        }
        .details {
            font-size: 11px;
        }
        .detail-row {
            margin-bottom: 5px;
        }
        .label {
            font-weight: bold;
            display: inline-block;
            width: 70px;
        }
        .footer {
            position: absolute;
            bottom: 10px;
            left: 15px;
            right: 15px;
            text-align: center;
            font-size: 10px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        .card-number {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="id-card">
        <div class="header">
            <p class="school-name">{{ $student->school->name ?? 'School Name' }}</p>
            <p class="id-title">STUDENT IDENTITY CARD</p>
        </div>

        <div class="photo">
            PHOTO
        </div>

        <div class="details">
            <div class="detail-row">
                <span class="label">Name:</span>
                <span>{{ $student->user->name }}</span>
            </div>
            <div class="detail-row">
                <span class="label">ID No:</span>
                <span>{{ $student->student_id ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Level:</span>
                <span>{{ $student->academicLevel->name ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Group:</span>
                <span>{{ $student->studentGroup->name ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Expires:</span>
                <span>{{ $idCard->expiry_date->format('M d, Y') }}</span>
            </div>
        </div>

        <div class="footer">
            <div class="card-number">Card #: {{ $idCard->card_number }}</div>
            <div>Issued: {{ $idCard->issue_date->format('M d, Y') }}</div>
        </div>
    </div>
</body>
</html>
