<x-layouts.app pageName="Transaction Details">
    <x-slot name="title">Transaction Details</x-slot>

    <div class="space-y-6">
        <!-- Transaction Info -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Transaction Information</h3>
                    @if($payment->status === 'succeeded')
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Success</span>
                    @elseif($payment->status === 'pending')
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">Pending</span>
                    @else
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Failed</span>
                    @endif
                </div>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Reference</label>
                    <p class="mt-1 text-sm font-mono text-gray-900 dark:text-white">{{ $payment->reference }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Amount</label>
                    <p class="mt-1 text-lg font-bold text-gray-900 dark:text-white">GH₵ {{ number_format($payment->amount, 2) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Payment Type</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ ucfirst($payment->payment_type ?? 'N/A') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Payment Method</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ ucfirst($payment->payment_method ?? 'N/A') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Transaction ID</label>
                    <p class="mt-1 text-sm font-mono text-gray-900 dark:text-white">{{ $payment->transaction_id ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Date</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $payment->created_at->format('M d, Y H:i:s') }}</p>
                </div>
            </div>
        </div>

        <!-- Student Info -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Student Information</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Name</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $payment->student->user->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Student ID</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $payment->student->student_id ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Academic Group</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $payment->academicGroup->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Academic Level</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $payment->academicLevel->name ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Payer Info -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payer Information</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Name</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $payment->getPayerDisplayName() }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $payment->payer_email ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Phone</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $payment->payer_phone ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Payer Type</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ ucfirst($payment->payer_type ?? 'N/A') }}</p>
                </div>
            </div>
        </div>

        <!-- Academic Period -->
        @if($payment->academicYear || $payment->academicPeriod)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Academic Period</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($payment->academicYear)
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Academic Year</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $payment->academicYear->name ?? 'N/A' }}</p>
                </div>
                @endif
                @if($payment->academicPeriod)
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Academic Period</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $payment->academicPeriod->name ?? 'N/A' }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="flex gap-4">
            <a href="{{ route('accountant.transactions.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                Back to Transactions
            </a>
            
            @if($payment->status === 'succeeded')
                <!-- View Receipt Button -->
                <a href="{{ route('accountant.receipts.show', $payment) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    View Receipt
                </a>
                
                <!-- Download PDF Receipt -->
                <a href="{{ route('accountant.receipts.pdf', $payment) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download Receipt
                </a>
            @endif
        </div>
    </div>
</x-layouts.app>
