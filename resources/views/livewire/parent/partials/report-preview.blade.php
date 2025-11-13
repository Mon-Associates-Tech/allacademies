<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Report Preview</h2>
        <div class="text-sm text-gray-600 dark:text-gray-400">
            Generated on {{ $report['generated_at']->format('M d, Y \a\t h:i A') }}
        </div>
    </div>

    <!-- Report Header -->
    <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">
            {{ ucfirst($report['report_type']) }} Report - {{ $report['ward']->user->name }}
        </h3>
        <p class="text-gray-600 dark:text-gray-400">
            Period: {{ $report['date_range']['start']->format('M d, Y') }} - {{ $report['date_range']['end']->format('M d, Y') }}
        </p>
        @if(isset($report['subject']) && $report['subject'])
            <p class="text-gray-600 dark:text-gray-400">Subject: {{ $report['subject']->name }}</p>
        @endif
    </div>

    @if($report['report_type'] === 'performance')
        @include('livewire.parent.partials.performance-report', ['data' => $report])
    @elseif($report['report_type'] === 'attendance')
        @include('livewire.parent.partials.attendance-report', ['data' => $report])
    @elseif($report['report_type'] === 'progress')
        @include('livewire.parent.partials.progress-report', ['data' => $report])
    @elseif($report['report_type'] === 'comprehensive')
        @include('livewire.parent.partials.comprehensive-report', ['data' => $report])
    @endif
</div>
