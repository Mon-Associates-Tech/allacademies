<x-reports.pdf-layout 
    :title="'Payment Receipt - ' . $payment->reference"
    :school="$school"
    :reportTitle="'OFFICIAL PAYMENT RECEIPT'"
    :generatedAt="$generated_at"
    :footerDescription="'This is an official computer-generated receipt. No signature required.'"
>
    <!-- Receipt Information -->
    <div style="display: flex; justify-content: space-between; margin-bottom: 25px;">
        <div style="width: 48%;">
            <div style="font-size: 14px; font-weight: bold; margin-bottom: 10px; color: #333; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Receipt Information</div>
            <div style="display: flex; justify-content: space-between; margin: 8px 0; font-size: 12px;">
                <span style="font-weight: 600; color: #555;">Receipt No:</span>
                <span style="font-weight: normal; color: #333;">{{ $payment->reference }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin: 8px 0; font-size: 12px;">
                <span style="font-weight: 600; color: #555;">Date Issued:</span>
                <span style="font-weight: normal; color: #333;">{{ $payment->created_at->format('F j, Y') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin: 8px 0; font-size: 12px;">
                <span style="font-weight: 600; color: #555;">Time:</span>
                <span style="font-weight: normal; color: #333;">{{ $payment->created_at->format('g:i A') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin: 8px 0; font-size: 12px;">
                <span style="font-weight: 600; color: #555;">Status:</span>
                <span style="padding: 4px 12px; border-radius: 15px; color: white; font-size: 11px; font-weight: bold; text-transform: uppercase; background-color: {{ $payment->status === 'succeeded' ? '#28a745' : ($payment->status === 'pending' ? '#ffc107' : '#dc3545') }}; {{ $payment->status === 'pending' ? 'color: #333;' : '' }}">
                    {{ ucfirst($payment->status) }}
                </span>
            </div>
        </div>

        <div style="width: 48%;">
            <div style="font-size: 14px; font-weight: bold; margin-bottom: 10px; color: #333; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Student Information</div>
            <div style="display: flex; justify-content: space-between; margin: 8px 0; font-size: 12px;">
                <span style="font-weight: 600; color: #555;">Name:</span>
                <span style="font-weight: normal; color: #333;">{{ $student->user?->name ?? 'N/A' }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin: 8px 0; font-size: 12px;">
                <span style="font-weight: 600; color: #555;">Student ID:</span>
                <span style="font-weight: normal; color: #333;">{{ $student->student_id ?? 'N/A' }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin: 8px 0; font-size: 12px;">
                <span style="font-weight: 600; color: #555;">Level:</span>
                <span style="font-weight: normal; color: #333;">{{ $student->academicLevel?->name ?? 'N/A' }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin: 8px 0; font-size: 12px;">
                <span style="font-weight: 600; color: #555;">Group:</span>
                <span style="font-weight: normal; color: #333;">{{ $student->academicGroup?->name ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <!-- Payment Details Table -->
    <table class="data-table" style="margin: 20px 0;">
        <thead>
            <tr>
                <th style="width: 40%">Description</th>
                <th style="width: 20%">Payment Type</th>
                <th style="width: 20%">Period</th>
                <th style="width: 20%; text-align: right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $payment->description ?: ucfirst(str_replace('_', ' ', $payment->payment_type)) . ' Fee' }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</td>
                <td>{{ $payment->academicPeriod?->name ?? $payment->payment_period ?? 'N/A' }}</td>
                <td style="text-align: right">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="3"><strong>TOTAL AMOUNT PAID</strong></td>
                <td style="text-align: right; font-size: 16px; font-weight: bold; color: #1a1a1a;">
                    {{ $payment->currency }} {{ number_format($payment->amount, 2) }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Payment Information -->
    <div style="display: flex; justify-content: space-between; margin-bottom: 25px;">
        <div style="width: 48%;">
            <div style="font-size: 14px; font-weight: bold; margin-bottom: 10px; color: #333; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Payment Information</div>
            <div style="display: flex; justify-content: space-between; margin: 8px 0; font-size: 12px;">
                <span style="font-weight: 600; color: #555;">Paid By:</span>
                <span style="font-weight: normal; color: #333;">{{ $payment->getPayerDisplayName() }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin: 8px 0; font-size: 12px;">
                <span style="font-weight: 600; color: #555;">Payment Method:</span>
                <span style="font-weight: normal; color: #333;">{{ ucfirst($payment->payment_method ?? 'Online Payment') }}</span>
            </div>
            @if($payment->paid_at)
            <div style="display: flex; justify-content: space-between; margin: 8px 0; font-size: 12px;">
                <span style="font-weight: 600; color: #555;">Payment Date:</span>
                <span style="font-weight: normal; color: #333;">{{ $payment->paid_at->format('F j, Y g:i A') }}</span>
            </div>
            @endif
            @if($payment->transaction_id)
            <div style="display: flex; justify-content: space-between; margin: 8px 0; font-size: 12px;">
                <span style="font-weight: 600; color: #555;">Transaction ID:</span>
                <span style="font-weight: normal; color: #333;">{{ $payment->transaction_id }}</span>
            </div>
            @endif
        </div>

        @if($payment->gateway_response && is_array($payment->gateway_response))
        <div style="width: 48%;">
            <div style="font-size: 14px; font-weight: bold; margin-bottom: 10px; color: #333; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Gateway Information</div>
            @if(isset($payment->gateway_response['authorization']['authorization_code']))
            <div style="display: flex; justify-content: space-between; margin: 8px 0; font-size: 12px;">
                <span style="font-weight: 600; color: #555;">Authorization:</span>
                <span style="font-weight: normal; color: #333; font-size: 10px;">{{ $payment->gateway_response['authorization']['authorization_code'] }}</span>
            </div>
            @endif
            @if(isset($payment->gateway_response['channel']))
            <div style="display: flex; justify-content: space-between; margin: 8px 0; font-size: 12px;">
                <span style="font-weight: 600; color: #555;">Channel:</span>
                <span style="font-weight: normal; color: #333;">{{ ucfirst($payment->gateway_response['channel']) }}</span>
            </div>
            @endif
            @if(isset($payment->gateway_response['fees']))
            <div style="display: flex; justify-content: space-between; margin: 8px 0; font-size: 12px;">
                <span style="font-weight: 600; color: #555;">Gateway Fees:</span>
                <span style="font-weight: normal; color: #333;">{{ $payment->currency }} {{ number_format($payment->gateway_response['fees'] / 100, 2) }}</span>
            </div>
            @endif
        </div>
        @endif
    </div>

    @if($payment->status === 'succeeded')
    <div style="margin-top: 20px; padding: 15px; background: #e8f5e8; border: 2px solid #28a745; border-radius: 5px; text-align: center;">
        <div style="color: #155724; font-weight: bold; font-size: 13px;">
            ✓ PAYMENT SUCCESSFULLY PROCESSED AND VERIFIED
        </div>
    </div>
    @endif
</x-reports.pdf-layout>