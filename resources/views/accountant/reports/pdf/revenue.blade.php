<x-reports.pdf-layout 
    :title="'Revenue Report'"
    :school="$school ?? null"
    :schoolName="$school_name ?? 'School Financial Report'"
    :reportTitle="'REVENUE REPORT'"
    :generatedAt="$generated_at"
    :filters="$filters ?? []"
    :footerDescription="'This report provides comprehensive revenue analysis and trends.'"
>
    @if(isset($summary_stats))
    <div class="summary-stats">
        <div class="stat-item">
            <div class="stat-value">GHS {{ number_format($summary_stats['total_revenue'] ?? 0, 2) }}</div>
            <div class="stat-label">Total Revenue</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">GHS {{ number_format($summary_stats['monthly_average'] ?? 0, 2) }}</div>
            <div class="stat-label">Monthly Average</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $summary_stats['total_transactions'] ?? 0 }}</div>
            <div class="stat-label">Total Transactions</div>
        </div>
        <div class="stat-item">
            <div class="stat-value {{ ($summary_stats['growth_rate'] ?? 0) >= 0 ? 'growth-positive' : 'growth-negative' }}">
                {{ number_format($summary_stats['growth_rate'] ?? 0, 1) }}%
            </div>
            <div class="stat-label">Growth Rate</div>
        </div>
    </div>
    @endif

    @if(isset($revenue_by_type) && count($revenue_by_type) > 0)
    <div class="section-title">Revenue by Payment Type</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Payment Type</th>
                <th style="text-align: right">Transactions</th>
                <th style="text-align: right">Revenue</th>
                <th style="text-align: right">Percentage</th>
                <th style="text-align: right">Avg. Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($revenue_by_type as $type => $stats)
            <tr>
                <td>{{ ucfirst(str_replace('_', ' ', $type)) }}</td>
                <td class="amount">{{ $stats['count'] ?? 0 }}</td>
                <td class="amount">GHS {{ number_format($stats['revenue'] ?? 0, 2) }}</td>
                <td class="amount">{{ number_format($stats['percentage'] ?? 0, 1) }}%</td>
                <td class="amount">GHS {{ number_format($stats['average'] ?? 0, 2) }}</td>
            </tr>
            @endforeach
            @if(isset($summary_stats['total_revenue']))
            <tr class="total-row">
                <td><strong>TOTAL</strong></td>
                <td class="amount"><strong>{{ $summary_stats['total_transactions'] ?? 0 }}</strong></td>
                <td class="amount"><strong>GHS {{ number_format($summary_stats['total_revenue'] ?? 0, 2) }}</strong></td>
                <td class="amount"><strong>100.0%</strong></td>
                <td class="amount"><strong>GHS {{ number_format(($summary_stats['total_revenue'] ?? 0) / max(1, $summary_stats['total_transactions'] ?? 1), 2) }}</strong></td>
            </tr>
            @endif
        </tbody>
    </table>
    @endif

    @if(isset($monthly_revenue) && count($monthly_revenue) > 0)
    <div class="section-title">Monthly Revenue Trend</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Month</th>
                <th style="text-align: right">Transactions</th>
                <th style="text-align: right">Revenue</th>
                <th style="text-align: right">Growth</th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthly_revenue as $month)
            <tr>
                <td>{{ $month['month'] ?? 'N/A' }}</td>
                <td class="amount">{{ $month['transactions'] ?? 0 }}</td>
                <td class="amount">GHS {{ number_format($month['revenue'] ?? 0, 2) }}</td>
                <td class="amount {{ ($month['growth'] ?? 0) >= 0 ? 'growth-positive' : 'growth-negative' }}">
                    {{ $month['growth'] ? number_format($month['growth'], 1) . '%' : 'N/A' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if(isset($revenue_by_level) && count($revenue_by_level) > 0)
    <div class="section-title">Revenue by Academic Level</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Academic Level</th>
                <th style="text-align: right">Students</th>
                <th style="text-align: right">Transactions</th>
                <th style="text-align: right">Revenue</th>
                <th style="text-align: right">Avg. per Student</th>
            </tr>
        </thead>
        <tbody>
            @foreach($revenue_by_level as $level => $stats)
            <tr>
                <td>{{ $level }}</td>
                <td class="amount">{{ $stats['students'] ?? 0 }}</td>
                <td class="amount">{{ $stats['transactions'] ?? 0 }}</td>
                <td class="amount">GHS {{ number_format($stats['revenue'] ?? 0, 2) }}</td>
                <td class="amount">GHS {{ number_format($stats['avg_per_student'] ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if(isset($top_revenue_days) && count($top_revenue_days) > 0)
    <div class="section-title">Top Revenue Days</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Date</th>
                <th style="text-align: right">Transactions</th>
                <th style="text-align: right">Revenue</th>
            </tr>
        </thead>
        <tbody>
            @foreach($top_revenue_days as $day)
            <tr>
                <td>{{ $day['date'] ?? 'N/A' }}</td>
                <td class="amount">{{ $day['transactions'] ?? 0 }}</td>
                <td class="amount">GHS {{ number_format($day['revenue'] ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</x-reports.pdf-layout>