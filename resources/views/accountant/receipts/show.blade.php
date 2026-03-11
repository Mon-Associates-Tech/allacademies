<x-layouts.app>

<div class="max-w-4xl mx-auto py-6">
    <!-- Header Actions -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Payment Receipt</h1>
            <p class="text-gray-600 dark:text-gray-400">Receipt #{{ $payment->reference }}</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="window.print()" 
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Receipt
            </button>
            <a href="{{ route('accountant.receipts.pdf', $payment) }}" 
               class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download PDF
            </a>
            <a href="{{ route('accountant.transactions.show', $payment) }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Transaction
            </a>
        </div>
    </div>

    <!-- Receipt Content -->
    <div id="receipt-content" class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden print:shadow-none print:rounded-none">
        <div class="p-8 print:p-6">
            <!-- School Header -->
            <div class="text-center border-b-2 border-gray-300 pb-6 mb-8">
                @if($payment->school->logo)
                    <img src="{{ Storage::url($payment->school->logo) }}" alt="{{ $payment->school->name }}" class="h-16 mx-auto mb-4">
                @endif
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $payment->school->name }}</h1>
                @if($payment->school->address)
                    <p class="text-gray-600 dark:text-gray-400">{{ $payment->school->address }}</p>
                @endif
                @if($payment->school->city || $payment->school->state)
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ $payment->school->city }}@if($payment->school->city && $payment->school->state), @endif{{ $payment->school->state }}
                    </p>
                @endif
                @if($payment->school->phone || $payment->school->email)
                    <p class="text-gray-600 dark:text-gray-400 mt-2">
                        @if($payment->school->phone)Tel: {{ $payment->school->phone }}@endif
                        @if($payment->school->phone && $payment->school->email) | @endif
                        @if($payment->school->email)Email: {{ $payment->school->email }}@endif
                    </p>
                @endif
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mt-4">OFFICIAL PAYMENT RECEIPT</h2>
            </div>

            <!-- Receipt Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Receipt Information</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Receipt No:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $payment->reference }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Date Issued:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $payment->created_at->format('F j, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Time:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $payment->created_at->format('g:i A') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Status:</span>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                @if($payment->status === 'succeeded') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Student Information</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Name:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $payment->student->user?->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Student ID:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $payment->student->student_id ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Level:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $payment->student->academicLevel?->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Group:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $payment->student->academicGroup?->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Details Table -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Details</h3>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-300 dark:border-gray-600">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                <th class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Description</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Payment Type</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Period</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-right text-sm font-semibold text-gray-900 dark:text-white">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-sm text-gray-900 dark:text-white">
                                    {{ $payment->description ?: ucfirst($payment->payment_type) . ' Fee' }}
                                </td>
                                <td class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-sm text-gray-900 dark:text-white">
                                    {{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}
                                </td>
                                <td class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-sm text-gray-900 dark:text-white">
                                    {{ $payment->academicPeriod?->name ?? $payment->payment_period ?? 'N/A' }}
                                </td>
                                <td class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-sm text-right text-gray-900 dark:text-white">
                                    {{ $payment->currency }} {{ number_format($payment->amount, 2) }}
                                </td>
                            </tr>
                            <tr class="bg-gray-100 dark:bg-gray-700 font-semibold">
                                <td colspan="3" class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-sm text-gray-900 dark:text-white">
                                    <strong>Total Amount Paid</strong>
                                </td>
                                <td class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-sm text-right text-gray-900 dark:text-white">
                                    <strong>{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Payment Information</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Paid By:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $payment->getPayerDisplayName() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Payment Method:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ ucfirst($payment->payment_method ?? 'Online Payment') }}</span>
                        </div>
                        @if($payment->paid_at)
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Payment Date:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $payment->paid_at->format('F j, Y g:i A') }}</span>
                        </div>
                        @endif
                        @if($payment->transaction_id)
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Transaction ID:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $payment->transaction_id }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                @if($payment->gateway_response && is_array($payment->gateway_response))
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Gateway Information</h3>
                    <div class="space-y-2">
                        @if(isset($payment->gateway_response['authorization']['authorization_code']))
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Authorization:</span>
                            <span class="font-medium text-gray-900 dark:text-white text-xs">{{ $payment->gateway_response['authorization']['authorization_code'] }}</span>
                        </div>
                        @endif
                        @if(isset($payment->gateway_response['channel']))
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Channel:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ ucfirst($payment->gateway_response['channel']) }}</span>
                        </div>
                        @endif
                        @if(isset($payment->gateway_response['fees']))
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Gateway Fees:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $payment->currency }} {{ number_format($payment->gateway_response['fees'] / 100, 2) }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="border-t border-gray-300 dark:border-gray-600 pt-6 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                    <strong>This is an official computer-generated receipt. No signature required.</strong>
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-500">
                    Generated on {{ now()->format('F j, Y g:i A') }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                    For inquiries, contact {{ $payment->school->email ?? $payment->school->phone ?? 'the school administration' }}
                </p>
                @if($payment->status === 'succeeded')
                <div class="mt-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                    <p class="text-sm text-green-800 dark:text-green-200 font-medium">
                        ✓ Payment Successfully Processed and Verified
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    
    #receipt-content, #receipt-content * {
        visibility: visible;
    }
    
    #receipt-content {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        background: white !important;
        box-shadow: none !important;
        border-radius: 0 !important;
    }
    
    .print\\:shadow-none {
        box-shadow: none !important;
    }
    
    .print\\:rounded-none {
        border-radius: 0 !important;
    }
    
    .print\\:p-6 {
        padding: 1.5rem !important;
    }
    
    /* Hide dark mode styles in print */
    .dark\\:bg-gray-800,
    .dark\\:bg-gray-700,
    .dark\\:text-white,
    .dark\\:text-gray-200,
    .dark\\:text-gray-400,
    .dark\\:border-gray-600 {
        background-color: white !important;
        color: black !important;
        border-color: #d1d5db !important;
    }
}
</style>
</x-layouts.app>