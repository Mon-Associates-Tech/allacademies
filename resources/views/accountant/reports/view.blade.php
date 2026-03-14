<x-layouts.app pageName="Financial Report - {{ ucwords(str_replace('_', ' ', $report_type)) }}">
    <x-slot name="title">{{ ucwords(str_replace('_', ' ', $report_type)) }} Report</x-slot>

    <div class="space-y-6">
        <!-- Report Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ ucwords(str_replace('_', ' ', $report_type)) }} Report</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Generated on {{ $generated_at->format('F j, Y \\a\\t g:i A') }}</p>
                </div>
                <div class="flex gap-2">
                    <form method="POST" action="{{ route('accountant.reports.generate') }}" class="inline">
                        @csrf
                        @foreach($filters as $key => $value)
                            @if($value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <input type="hidden" name="format" value="pdf">
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                            Download PDF
                        </button>
                    </form>
                    <form method="POST" action="{{ route('accountant.reports.generate') }}" class="inline">
                        @csrf
                        @foreach($filters as $key => $value)
                            @if($value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <input type="hidden" name="format" value="excel">
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                            Download Excel
                        </button>
                    </form>
                </div>
            </div>

            <!-- Applied Filters -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Applied Filters:</h3>
                <div class="flex flex-wrap gap-2">
                    <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 rounded text-xs">
                        Period: {{ $filters['start_date'] }} to {{ $filters['end_date'] }}
                    </span>
                    @if($filters['payment_type'] ?? false)
                        <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded text-xs">
                            Type: {{ ucfirst($filters['payment_type']) }}
                        </span>
                    @endif
                    @if($filters['status'] ?? false)
                        <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200 rounded text-xs">
                            Status: {{ ucfirst($filters['status']) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Report Content -->
        @if($report_type === 'payment_summary')
            @include('accountant.reports.partials.payment-summary')
        @elseif($report_type === 'student_payments')
            @include('accountant.reports.partials.student-payments')
        @elseif($report_type === 'outstanding_payments')
            @include('accountant.reports.partials.outstanding-payments')
        @elseif($report_type === 'revenue')
            @include('accountant.reports.partials.revenue')
        @elseif($report_type === 'financial_aid')
            @include('accountant.reports.partials.financial-aid')
        @elseif($report_type === 'custom')
            @include('accountant.reports.partials.custom')
        @endif
    </div>
</x-layouts.app>