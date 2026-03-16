<x-reports.pdf-layout 
    :title="'Custom Financial Report'"
    :school="$school ?? null"
    :schoolName="$school_name ?? 'School Financial Report'"
    :reportTitle="'CUSTOM FINANCIAL REPORT'"
    :generatedAt="$generated_at"
    :additionalInfo="'Custom report based on selected criteria'"
    :footerDescription="'This report was generated based on your specific criteria and filters.'"
>
    <!-- Applied Filters -->
    <div class="filters-applied">
        <div class="filter-title">Applied Filters:</div>
        @if(isset($filters) && count($filters) > 0)
            @foreach($filters as $key => $value)
                @if($value && $key !== 'report_type' && $key !== 'format')
                    <span class="filter-item">
                        {{ ucfirst(str_replace('_', ' ', $key)) }}: {{ is_array($value) ? implode(', ', $value) : $value }}
                    </span>
                @endif
            @endforeach
        @else
            <span class="filter-item">No specific filters applied</span>
        @endif
    </div>

    <!-- Summary Statistics -->
    @if(isset($summary_stats) && count($summary_stats) > 0)
    <div class="summary-stats">
        @foreach($summary_stats as $key => $value)
        <div class="stat-item">
            <div class="stat-value">
                @if(str_contains($key, 'amount') || str_contains($key, 'revenue') || str_contains($key, 'total'))
                    GHS {{ number_format($value, 2) }}
                @else
                    {{ $value }}
                @endif
            </div>
            <div class="stat-label">{{ ucfirst(str_replace('_', ' ', $key)) }}</div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Main Data Table -->
    @if(isset($data) && count($data) > 0)
    <table class="data-table">
        <thead>
            <tr>
                @if(isset($columns) && count($columns) > 0)
                    @foreach($columns as $column)
                    <th>{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
                    @endforeach
                @else
                    <!-- Default columns if not specified -->
                    <th>Date</th>
                    <th>Reference</th>
                    <th>Student</th>
                    <th>Payment Type</th>
                    <th>Amount</th>
                    <th>Status</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                @if(isset($columns) && count($columns) > 0)
                    @foreach($columns as $column)
                    <td @if(str_contains($column, 'amount') || str_contains($column, 'total')) class="amount" @endif>
                        @if(str_contains($column, 'amount') || str_contains($column, 'total'))
                            GHS {{ number_format($row[$column] ?? 0, 2) }}
                        @elseif($column === 'status')
                            <span class="status-{{ strtolower($row[$column] ?? 'unknown') }}">
                                {{ ucfirst($row[$column] ?? 'N/A') }}
                            </span>
                        @elseif(str_contains($column, 'date'))
                            {{ isset($row[$column]) ? \Carbon\Carbon::parse($row[$column])->format('M d, Y') : 'N/A' }}
                        @else
                            {{ $row[$column] ?? 'N/A' }}
                        @endif
                    </td>
                    @endforeach
                @else
                    <!-- Default row structure -->
                    <td>{{ isset($row['date']) ? \Carbon\Carbon::parse($row['date'])->format('M d, Y') : 'N/A' }}</td>
                    <td style="font-family: monospace; font-size: 9px;">{{ $row['reference'] ?? 'N/A' }}</td>
                    <td>{{ $row['student_name'] ?? $row['student'] ?? 'N/A' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $row['payment_type'] ?? '')) }}</td>
                    <td class="amount">GHS {{ number_format($row['amount'] ?? 0, 2) }}</td>
                    <td class="status-{{ strtolower($row['status'] ?? 'unknown') }}">
                        {{ ucfirst($row['status'] ?? 'N/A') }}
                    </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        No data found matching the selected criteria.
    </div>
    @endif

    <!-- Additional Analysis -->
    @if(isset($analysis) && count($analysis) > 0)
    @foreach($analysis as $section_name => $section_data)
        @if(is_array($section_data) && count($section_data) > 0)
        <div class="section-title">{{ ucfirst(str_replace('_', ' ', $section_name)) }}</div>
        <table class="data-table">
            <thead>
                <tr>
                    @foreach(array_keys($section_data[0] ?? []) as $header)
                    <th>{{ ucfirst(str_replace('_', ' ', $header)) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($section_data as $item)
                <tr>
                    @foreach($item as $key => $value)
                    <td @if(str_contains($key, 'amount') || str_contains($key, 'total')) class="amount" @endif>
                        @if(str_contains($key, 'amount') || str_contains($key, 'total'))
                            GHS {{ number_format($value, 2) }}
                        @elseif(str_contains($key, 'percentage'))
                            {{ number_format($value, 1) }}%
                        @else
                            {{ $value }}
                        @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    @endforeach
    @endif
</x-reports.pdf-layout>