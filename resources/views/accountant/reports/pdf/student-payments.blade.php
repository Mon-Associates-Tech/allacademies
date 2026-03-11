<x-reports.pdf-layout 
    :title="'Student Payments Report'"
    :school="$school ?? null"
    :schoolName="$school_name ?? 'School Financial Report'"
    :reportTitle="'STUDENT PAYMENTS REPORT'"
    :generatedAt="$generated_at"
    :filters="$filters ?? []"
    :additionalInfo="isset($filters['student_id']) ? 'Student: ' . ($filters['student_name'] ?? $filters['student_id']) : ''"
    :footerDescription="'This report shows detailed payment information for students.'"
>
    @if(isset($data) && count($data) > 0)
        @if(isset($filters['student_id']))
            <!-- Single Student Report -->
            @php $student = $data->first(); @endphp
            <div style="margin-bottom: 30px; page-break-inside: avoid;">
                <div style="background: #f8f9fa; padding: 10px; border: 1px solid #ddd; font-weight: bold; margin-bottom: 10px;">
                    {{ $student['student_name'] ?? 'N/A' }} ({{ $student['student_id'] ?? 'N/A' }})
                </div>
                <div style="font-size: 11px; margin-bottom: 15px; padding: 10px; background: #fff; border-left: 4px solid #007bff;">
                    <strong>Academic Level:</strong> {{ $student['academic_level'] ?? 'N/A' }} |
                    <strong>Academic Group:</strong> {{ $student['academic_group'] ?? 'N/A' }} |
                    <strong>Total Payments:</strong> {{ $student['payment_count'] ?? 0 }} |
                    <strong>Total Amount:</strong> GHS {{ number_format($student['total_amount'] ?? 0, 2) }}
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 12%">Date</th>
                            <th style="width: 15%">Reference</th>
                            <th style="width: 15%">Payment Type</th>
                            <th style="width: 12%">Period</th>
                            <th style="width: 12%">Amount</th>
                            <th style="width: 10%">Status</th>
                            <th style="width: 12%">Method</th>
                            <th style="width: 12%">Paid By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($student['payments'] ?? [] as $payment)
                        <tr>
                            <td>{{ $payment['date'] ?? 'N/A' }}</td>
                            <td style="font-family: monospace; font-size: 9px;">{{ $payment['reference'] ?? 'N/A' }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $payment['payment_type'] ?? '')) }}</td>
                            <td>{{ $payment['period'] ?? 'N/A' }}</td>
                            <td class="amount">GHS {{ number_format($payment['amount'] ?? 0, 2) }}</td>
                            <td class="status-{{ $payment['status'] ?? 'unknown' }}">{{ ucfirst($payment['status'] ?? 'N/A') }}</td>
                            <td>{{ ucfirst($payment['method'] ?? 'N/A') }}</td>
                            <td>{{ $payment['payer'] ?? 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <!-- Multiple Students Report -->
            @foreach($data as $student)
            <div style="margin-bottom: 30px; page-break-inside: avoid;">
                <div style="background: #f8f9fa; padding: 10px; border: 1px solid #ddd; font-weight: bold; margin-bottom: 10px;">
                    {{ $student['student_name'] ?? 'N/A' }} ({{ $student['student_id'] ?? 'N/A' }})
                </div>
                <div style="font-size: 11px; margin-bottom: 15px; padding: 10px; background: #fff; border-left: 4px solid #007bff;">
                    <strong>Level:</strong> {{ $student['academic_level'] ?? 'N/A' }} |
                    <strong>Group:</strong> {{ $student['academic_group'] ?? 'N/A' }} |
                    <strong>Payments:</strong> {{ $student['payment_count'] ?? 0 }} |
                    <strong>Total:</strong> GHS {{ number_format($student['total_amount'] ?? 0, 2) }}
                </div>
                
                @if(isset($student['payments']) && count($student['payments']) > 0)
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Payment Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Method</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($student['payments'] as $payment)
                        <tr>
                            <td>{{ $payment['date'] ?? 'N/A' }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $payment['payment_type'] ?? '')) }}</td>
                            <td class="amount">GHS {{ number_format($payment['amount'] ?? 0, 2) }}</td>
                            <td class="status-{{ $payment['status'] ?? 'unknown' }}">{{ ucfirst($payment['status'] ?? 'N/A') }}</td>
                            <td>{{ ucfirst($payment['method'] ?? 'N/A') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p style="text-align: center; color: #666; font-style: italic; padding: 20px;">No payments found for this student.</p>
                @endif
            </div>
            @endforeach
        @endif
    @else
    <div class="no-data">
        No student payment data found for the selected criteria.
    </div>
    @endif
</x-reports.pdf-layout>