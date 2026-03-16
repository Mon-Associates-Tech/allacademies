<x-reports.pdf-layout 
    :title="'Outstanding Payments Report'"
    :school="$school ?? null"
    :schoolName="$school_name ?? 'School Financial Report'"
    :reportTitle="'OUTSTANDING PAYMENTS REPORT'"
    :generatedAt="$generated_at"
    :filters="$filters ?? []"
    :footerDescription="'This report shows all pending payments that are due or overdue.'"
>
    @if(isset($summary_stats) && count($summary_stats) > 0)
    <div class="summary-stats">
        <div class="stat-item">
            <div class="stat-value" style="color: #dc3545;">{{ $summary_stats['total_outstanding'] ?? 0 }}</div>
            <div class="stat-label">Total Outstanding</div>
        </div>
        <div class="stat-item">
            <div class="stat-value" style="color: #dc3545;">GHS {{ number_format($summary_stats['total_amount'] ?? 0, 2) }}</div>
            <div class="stat-label">Total Amount</div>
        </div>
        <div class="stat-item">
            <div class="stat-value" style="color: #dc3545;">{{ $summary_stats['overdue_count'] ?? 0 }}</div>
            <div class="stat-label">Overdue Payments</div>
        </div>
        <div class="stat-item">
            <div class="stat-value" style="color: #dc3545;">GHS {{ number_format($summary_stats['overdue_amount'] ?? 0, 2) }}</div>
            <div class="stat-label">Overdue Amount</div>
        </div>
    </div>
    @endif

    @if(isset($data) && count($data) > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 15%">Student</th>
                <th style="width: 12%">Student ID</th>
                <th style="width: 10%">Level</th>
                <th style="width: 15%">Payment Type</th>
                <th style="width: 12%">Due Date</th>
                <th style="width: 12%">Amount</th>
                <th style="width: 12%">Days Overdue</th>
                <th style="width: 12%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $payment)
            <tr>
                <td>{{ $payment['student_name'] ?? 'N/A' }}</td>
                <td>{{ $payment['student_id'] ?? 'N/A' }}</td>
                <td>{{ $payment['academic_level'] ?? 'N/A' }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $payment['payment_type'] ?? '')) }}</td>
                <td>{{ $payment['due_date'] ?? 'N/A' }}</td>
                <td class="amount">GHS {{ number_format($payment['amount'] ?? 0, 2) }}</td>
                <td class="amount {{ ($payment['days_overdue'] ?? 0) > 0 ? 'overdue' : '' }}">
                    {{ $payment['days_overdue'] ?? 0 }} days
                </td>
                <td>
                    @if(($payment['days_overdue'] ?? 0) > 0)
                        <span class="overdue">Overdue</span>
                    @elseif(($payment['days_until_due'] ?? 0) <= 7)
                        <span class="due-soon">Due Soon</span>
                    @else
                        Pending
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        No outstanding payments found for the selected criteria.
    </div>
    @endif

    @if(isset($summary_by_type) && count($summary_by_type) > 0)
    <div class="section-title">Outstanding Payments by Type</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Payment Type</th>
                <th style="text-align: right">Count</th>
                <th style="text-align: right">Total Amount</th>
                <th style="text-align: right">Overdue Count</th>
                <th style="text-align: right">Overdue Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($summary_by_type as $type => $stats)
            <tr>
                <td>{{ ucfirst(str_replace('_', ' ', $type)) }}</td>
                <td class="amount">{{ $stats['count'] ?? 0 }}</td>
                <td class="amount">GHS {{ number_format($stats['total_amount'] ?? 0, 2) }}</td>
                <td class="amount">{{ $stats['overdue_count'] ?? 0 }}</td>
                <td class="amount">GHS {{ number_format($stats['overdue_amount'] ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</x-reports.pdf-layout>