<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bulk Payment Receipts</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
        .receipt { margin-bottom: 40px; page-break-inside: avoid; }
        .receipt-header { background-color: #f5f5f5; padding: 10px; margin-bottom: 15px; }
        .payment-details { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .payment-details th, .payment-details td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .payment-details th { background-color: #f9f9f9; }
        .status-badge { padding: 2px 6px; border-radius: 3px; color: white; font-size: 11px; }
        .status-succeeded { background-color: #10b981; }
        .status-pending { background-color: #f59e0b; }
        .status-failed { background-color: #ef4444; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $school->name }}</h1>
        <h2>BULK PAYMENT RECEIPTS</h2>
        <p>Generated on {{ $generated_at->format('F j, Y g:i A') }}</p>
    </div>

    @foreach($payments as $index => $payment)
        @if($index > 0)
            <div class="page-break"></div>
        @endif
        
        <div class="receipt">
            <div class="receipt-header">
                <h3>Receipt #{{ $payment->reference }}</h3>
                <p>
                    <strong>Student:</strong> {{ $payment->student?->user?->name ?? 'N/A' }} 
                    ({{ $payment->student?->student_id ?? 'N/A' }})
                </p>
                <p>
                    <strong>Date:</strong> {{ $payment->created_at->format('F j, Y g:i A') }}
                    <strong>Status:</strong> 
                    <span class="status-badge status-{{ $payment->status }}">
                        {{ ucfirst($payment->status) }}
                    </span>
                </p>
            </div>

            <table class="payment-details">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Paid By</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $payment->description ?: ucfirst($payment->payment_type) . ' Fee' }}</td>
                        <td>{{ ucfirst($payment->payment_type) }}</td>
                        <td>GHS {{ number_format($payment->amount, 2) }}</td>
                        <td>{{ $payment->getPayerDisplayName() }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endforeach

    <div style="text-align: center; margin-top: 30px; font-size: 12px; color: #666;">
        <p>These are computer-generated receipts. No signature required.</p>
        <p>Total Receipts: {{ $payments->count() }}</p>
    </div>
</body>
</html>