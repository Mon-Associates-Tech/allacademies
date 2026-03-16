<x-reports.pdf-layout 
    :title="'Payment Summary Report'"
    :school="$school ?? null"
    :schoolName="$school_name ?? 'School Financial Report'"
    :reportTitle="'PAYMENT SUMMARY REPORT'"
    :generatedAt="$generated_at"
    :filters="$filters ?? []"
    :footerDescription="'This report provides an overview of all payment activities during the selected period.'"
>
    @if(isset($summary_stats))
    <div class="summary-stats">
        <div class="stat-item">
            <div class="stat-value">{{ $summary_stats['total_payments'] ?? 0 }}</div>
            <div class="stat-label">Total Payments</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">GHS {{ number_format($summary_stats['total_amount'] ?? 0, 2) }}</div>
            <div class="stat-label">Total Amount</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $summary_stats['successful_payments'] ?? 0 }}</div>
            <div class="stat-label">Successful</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">GHS {{ number_format($summary_stats['average_payment'] ?? 0, 2) }}</div>
            <div class="stat-label">Average Payment</div>
        </div>
    </div>
    @endif

    @if(isset($payment_by_type) && count($payment_by_type) > 0)
    <div class="section-title">Payments by Type</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40%">Payment Type</th>
                <th style="width: 20%; text-align: right">Count</th>
                <th style="width: 25%; text-align: right">Total Amount</th>
                <th style="width: 15%; text-align: right">Percentage</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payment_by_type as $type => $stats)
            <tr>
                <td style="width: 40%">{{ ucfirst(str_replace('_', ' ', $type)) }}</td>
                <td class="amount" style="width: 20%">{{ $stats['count'] ?? 0 }}</td>
                <td class="amount" style="width: 25%">GHS {{ number_format($stats['amount'] ?? 0, 2) }}</td>
                <td class="amount" style="width: 15%">{{ number_format($stats['percentage'] ?? 0, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if(isset($payment_by_status) && count($payment_by_status) > 0)
    <div class="section-title">Payments by Status</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40%">Status</th>
                <th style="width: 20%; text-align: right">Count</th>
                <th style="width: 25%; text-align: right">Total Amount</th>
                <th style="width: 15%; text-align: right">Percentage</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payment_by_status as $status => $stats)
            <tr>
                <td style="width: 40%">{{ ucfirst($status) }}</td>
                <td class="amount" style="width: 20%">{{ $stats['count'] ?? 0 }}</td>
                <td class="amount" style="width: 25%">GHS {{ number_format($stats['amount'] ?? 0, 2) }}</td>
                <td class="amount" style="width: 15%">{{ number_format($stats['percentage'] ?? 0, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if(isset($daily_trends) && count($daily_trends) > 0)
    <div class="section-title">Daily Payment Trends</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40%">Date</th>
                <th style="width: 30%; text-align: right">Payments</th>
                <th style="width: 30%; text-align: right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($daily_trends as $trend)
            <tr>
                <td style="width: 40%">{{ $trend['date'] ?? 'N/A' }}</td>
                <td class="amount" style="width: 30%">{{ $trend['count'] ?? 0 }}</td>
                <td class="amount" style="width: 30%">GHS {{ number_format($trend['amount'] ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</x-reports.pdf-layout>