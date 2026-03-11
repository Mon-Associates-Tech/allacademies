<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Financial Report' }}</title>
    <style>
        @page {
            margin: 10mm 15mm;
            size: A4 landscape;
        }
        
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        
        body { 
            font-family: 'Arial', sans-serif; 
            margin: 0; 
            padding: 0;
            line-height: 1.4;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .report-container {
            max-width: 100%;
            margin: 0;
            background: white;
            border: 2px solid #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .header { 
            text-align: center; 
            padding: 20px;
            border-bottom: 3px solid #333; 
            background: #f8f9fa;
            flex-shrink: 0;
        }
        
        .content {
            padding: 20px;
            flex: 1;
            min-height: 0;
        }
        
        .footer {
            text-align: center;
            margin-top: auto;
            padding: 20px;
            border-top: 2px solid #333;
            font-size: 11px;
            color: #666;
            flex-shrink: 0;
        }
        
        .school-logo {
            max-height: 60px;
            margin-bottom: 10px;
        }
        .school-name {
            font-size: 28px;
            font-weight: bold;
            margin: 10px 0;
            color: #1a1a1a;
        }
        .school-details {
            font-size: 12px;
            color: #666;
            margin: 5px 0;
        }
        .report-title {
            font-size: 20px;
            font-weight: bold;
            margin-top: 15px;
            color: #333;
            text-decoration: underline;
        }
        .report-info {
            font-size: 11px;
            color: #666;
            margin-top: 10px;
        }
        .summary-stats {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            table-layout: fixed;
        }
        .stat-item {
            display: table-cell;
            text-align: center;
            vertical-align: middle;
            width: 25%;
        }
        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
        }
        .stat-label {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
            table-layout: fixed;
        }
        .data-table th {
            background-color: #f1f3f4;
            border: 1px solid #333;
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            color: #333;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .data-table td {
            border: 1px solid #333;
            padding: 8px 6px;
            color: #333;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
            max-width: 0;
        }
        .data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .amount {
            text-align: right;
            font-weight: bold;
        }
        .status-succeeded { color: #28a745; font-weight: bold; }
        .status-pending { color: #ffc107; font-weight: bold; }
        .status-failed { color: #dc3545; font-weight: bold; }
        .status-active { color: #28a745; font-weight: bold; }
        .status-inactive { color: #dc3545; font-weight: bold; }
        .overdue { color: #dc3545; font-weight: bold; }
        .due-soon { color: #ffc107; font-weight: bold; }
        .growth-positive { color: #28a745; }
        .growth-negative { color: #dc3545; }
        .total-row {
            background-color: #e8f4f8 !important;
            font-weight: bold;
            font-size: 11px;
        }
        .footer {
            text-align: center;
            margin-top: auto;
            padding: 20px;
            border-top: 2px solid #333;
            font-size: 11px;
            color: #666;
            flex-shrink: 0;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin: 25px 0 15px 0;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .filters-applied {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
            border-left: 4px solid #007bff;
            font-size: 11px;
        }
        .filter-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        .filter-item {
            display: inline-block;
            background: #e9ecef;
            padding: 4px 8px;
            margin: 2px;
            border-radius: 3px;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <div class="header">
            @if(isset($school) && $school->logo)
                <img src="{{ public_path('storage/' . $school->logo) }}" alt="{{ $school->name }}" class="school-logo">
            @endif
            <div class="school-name">{{ $school->name ?? $schoolName ?? 'School Name' }}</div>
            @if(isset($school))
                @if($school->address)
                    <div class="school-details">{{ $school->address }}</div>
                @endif
                @if($school->city || $school->state)
                    <div class="school-details">
                        {{ $school->city }}@if($school->city && $school->state), @endif{{ $school->state }}
                        @if($school->postal_code) {{ $school->postal_code }}@endif
                    </div>
                @endif
                @if($school->phone || $school->email)
                    <div class="school-details">
                        @if($school->phone)Tel: {{ $school->phone }}@endif
                        @if($school->phone && $school->email) | @endif
                        @if($school->email)Email: {{ $school->email }}@endif
                    </div>
                @endif
            @endif
            <div class="report-title">{{ $reportTitle ?? 'FINANCIAL REPORT' }}</div>
            <div class="report-info">
                Generated on {{ ($generatedAt ?? now())->format('F j, Y \\a\\t g:i A') }}
                @if(isset($filters['start_date']) || isset($filters['end_date']))
                    <br>Period: {{ $filters['start_date'] ?? 'All time' }} to {{ $filters['end_date'] ?? 'Present' }}
                @endif
                @if(isset($additionalInfo))
                    <br>{{ $additionalInfo }}
                @endif
            </div>
        </div>

        <div class="content">
            {{ $slot }}
        </div>

        <div class="footer">
            <p><strong>{{ $reportTitle ?? 'Financial Report' }}</strong></p>
            <p>{{ $footerDescription ?? 'This report provides comprehensive financial information and analysis.' }}</p>
            <p>Generated by {{ config('app.name') }} Financial Management System</p>
            <p style="margin-top: 15px; font-size: 10px; color: #999;">
                This report is computer-generated and should be retained for your records.
            </p>
        </div>
    </div>
</body>
</html>