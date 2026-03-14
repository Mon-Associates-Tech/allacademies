<x-reports.pdf-layout 
    :title="'Financial Aid Report'"
    :school="$school ?? null"
    :schoolName="$school_name ?? 'School Financial Report'"
    :reportTitle="'FINANCIAL AID REPORT'"
    :generatedAt="$generated_at"
    :filters="$filters ?? []"
    :footerDescription="'This report provides detailed information about financial aid programs and beneficiaries.'"
>
    @if(isset($summary_stats))
    <div class="summary-stats">
        <div class="stat-item">
            <div class="stat-value" style="color: #17a2b8;">{{ $summary_stats['total_programs'] ?? 0 }}</div>
            <div class="stat-label">Aid Programs</div>
        </div>
        <div class="stat-item">
            <div class="stat-value" style="color: #17a2b8;">{{ $summary_stats['total_beneficiaries'] ?? 0 }}</div>
            <div class="stat-label">Beneficiaries</div>
        </div>
        <div class="stat-item">
            <div class="stat-value" style="color: #17a2b8;">GHS {{ number_format($summary_stats['total_aid_amount'] ?? 0, 2) }}</div>
            <div class="stat-label">Total Aid</div>
        </div>
        <div class="stat-item">
            <div class="stat-value" style="color: #17a2b8;">GHS {{ number_format($summary_stats['average_aid'] ?? 0, 2) }}</div>
            <div class="stat-label">Average Aid</div>
        </div>
    </div>
    @endif

    @if(isset($data) && count($data) > 0)
        @foreach($data as $program)
        <div style="margin-bottom: 30px; page-break-inside: avoid;">
            <div style="background: #e8f4f8; padding: 10px; border: 1px solid #17a2b8; font-weight: bold; margin-bottom: 10px;">
                {{ $program['name'] ?? 'N/A' }}
            </div>
            <div style="font-size: 11px; margin-bottom: 15px; padding: 10px; background: #fff; border-left: 4px solid #17a2b8;">
                <strong>Type:</strong> {{ ucfirst($program['type'] ?? 'N/A') }} |
                <strong>Status:</strong> <span class="status-{{ strtolower($program['status'] ?? 'unknown') }}">{{ ucfirst($program['status'] ?? 'N/A') }}</span> |
                <strong>Budget:</strong> GHS {{ number_format($program['budget'] ?? 0, 2) }} |
                <strong>Allocated:</strong> GHS {{ number_format($program['allocated'] ?? 0, 2) }} |
                <strong>Beneficiaries:</strong> {{ $program['beneficiary_count'] ?? 0 }}
                @if($program['description'] ?? false)
                    <br><strong>Description:</strong> {{ $program['description'] }}
                @endif
            </div>
            
            @if(isset($program['beneficiaries']) && count($program['beneficiaries']) > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 20%">Student Name</th>
                        <th style="width: 15%">Student ID</th>
                        <th style="width: 15%">Academic Level</th>
                        <th style="width: 12%">Aid Amount</th>
                        <th style="width: 12%">Date Awarded</th>
                        <th style="width: 10%">Status</th>
                        <th style="width: 16%">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($program['beneficiaries'] as $beneficiary)
                    <tr>
                        <td>{{ $beneficiary['student_name'] ?? 'N/A' }}</td>
                        <td>{{ $beneficiary['student_id'] ?? 'N/A' }}</td>
                        <td>{{ $beneficiary['academic_level'] ?? 'N/A' }}</td>
                        <td class="amount">GHS {{ number_format($beneficiary['aid_amount'] ?? 0, 2) }}</td>
                        <td>{{ $beneficiary['date_awarded'] ?? 'N/A' }}</td>
                        <td class="status-{{ strtolower($beneficiary['status'] ?? 'unknown') }}">
                            {{ ucfirst($beneficiary['status'] ?? 'N/A') }}
                        </td>
                        <td style="font-size: 9px;">{{ $beneficiary['notes'] ?? '-' }}</td>
                    </tr>
                    @endforeach
                    <tr style="background-color: #e8f4f8; font-weight: bold;">
                        <td colspan="3"><strong>Program Total</strong></td>
                        <td class="amount"><strong>GHS {{ number_format($program['allocated'] ?? 0, 2) }}</strong></td>
                        <td colspan="3"><strong>{{ $program['beneficiary_count'] ?? 0 }} Beneficiaries</strong></td>
                    </tr>
                </tbody>
            </table>
            @else
            <p style="text-align: center; color: #666; font-style: italic; padding: 20px;">No beneficiaries found for this program.</p>
            @endif
        </div>
        @endforeach
    @else
    <div class="no-data">
        No financial aid data found for the selected criteria.
    </div>
    @endif

    @if(isset($aid_by_type) && count($aid_by_type) > 0)
    <div class="section-title">Financial Aid by Type</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Aid Type</th>
                <th style="text-align: right">Programs</th>
                <th style="text-align: right">Beneficiaries</th>
                <th style="text-align: right">Total Amount</th>
                <th style="text-align: right">Average per Student</th>
            </tr>
        </thead>
        <tbody>
            @foreach($aid_by_type as $type => $stats)
            <tr>
                <td>{{ ucfirst(str_replace('_', ' ', $type)) }}</td>
                <td class="amount">{{ $stats['programs'] ?? 0 }}</td>
                <td class="amount">{{ $stats['beneficiaries'] ?? 0 }}</td>
                <td class="amount">GHS {{ number_format($stats['total_amount'] ?? 0, 2) }}</td>
                <td class="amount">GHS {{ number_format($stats['average_per_student'] ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if(isset($aid_by_level) && count($aid_by_level) > 0)
    <div class="section-title">Financial Aid by Academic Level</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Academic Level</th>
                <th style="text-align: right">Beneficiaries</th>
                <th style="text-align: right">Total Aid</th>
                <th style="text-align: right">Percentage</th>
            </tr>
        </thead>
        <tbody>
            @foreach($aid_by_level as $level => $stats)
            <tr>
                <td>{{ $level }}</td>
                <td class="amount">{{ $stats['beneficiaries'] ?? 0 }}</td>
                <td class="amount">GHS {{ number_format($stats['total_aid'] ?? 0, 2) }}</td>
                <td class="amount">{{ number_format($stats['percentage'] ?? 0, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</x-reports.pdf-layout>