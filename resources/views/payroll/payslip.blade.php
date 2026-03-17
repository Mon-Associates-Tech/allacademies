<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payslip</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 24px; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 5px; }
        .salary-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .salary-table th, .salary-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .salary-table th { background-color: #f2f2f2; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }
        .watermark { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 100px; color: rgba(0, 255, 0, 0.1); z-index: -1; }
    </style>
</head>
<body>
    @if($disbursement->status === 'success')
        <div class="watermark">PAID</div>
    @endif

    <div class="header">
        <h1>{{ $disbursement->school->name }}</h1>
        <p>{{ $disbursement->school->address }}</p>
        <h2>PAYSLIP</h2>
    </div>

    <table class="info-table">
        <tr>
            <td><strong>Employee Name:</strong></td>
            <td>{{ $disbursement->payrollEntry->full_name }}</td>
            <td><strong>Pay Period:</strong></td>
            <td>{{ $disbursement->payrollRun->created_at->format('F Y') }}</td>
        </tr>
        <tr>
            <td><strong>Role:</strong></td>
            <td>{{ $disbursement->payrollEntry->payrollRole?->name ?? $disbursement->payrollEntry->system_role }}</td>
            <td><strong>Payment Date:</strong></td>
            <td>{{ $disbursement->transferred_at?->format('Y-m-d') ?? 'Pending' }}</td>
        </tr>
        <tr>
            <td><strong>Bank Account:</strong></td>
            <td>{{ $disbursement->bankAccount->bank_name }} - {{ $disbursement->bankAccount->masked_account_number }}</td>
            <td><strong>Reference:</strong></td>
            <td>{{ $disbursement->paystack_reference }}</td>
        </tr>
    </table>

    <table class="salary-table">
        <thead>
            <tr>
                <th>Description</th>
                <th style="text-align: right;">Amount (GH₵)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Gross Salary</td>
                <td style="text-align: right;">{{ number_format($disbursement->amount, 2) }}</td>
            </tr>
            <tr>
                <td>Deductions</td>
                <td style="text-align: right;">0.00</td>
            </tr>
            <tr class="total-row">
                <td>Net Pay</td>
                <td style="text-align: right;">{{ number_format($disbursement->amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <p style="margin-top: 30px; font-size: 10px; color: #666;">
        This is a computer-generated payslip. No signature is required.
    </p>
</body>
</html>
