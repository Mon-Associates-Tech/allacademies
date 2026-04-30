<x-layouts.app title="Payment Receipt">
    <!-- Print Styles -->
    <style>
        @media print {
            .print\:hidden { display: none !important; }
            .print\:block { display: block !important; }
            .print\:bg-white { background-color: white !important; }
            .print\:text-black { color: black !important; }
            .print\:border-gray-300 { border-color: #d1d5db !important; }
            .print\:shadow-none { box-shadow: none !important; }
            .print\:p-0 { padding: 0 !important; }
            .print\:m-0 { margin: 0 !important; }
            body { background: white !important; }
        }
    </style>

    <!-- Navigation (hidden in print) -->
    <div class="print:hidden max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <a href="{{ route('admin.transactions.index') }}"
           class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Transactions
        </a>
    </div>

    <!-- Receipt Container -->
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 print:bg-white print:min-h-0 py-8 print:py-0">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 print:px-8 print:max-w-none">
            <div class="bg-white dark:bg-gray-800 print:bg-white shadow-sm border border-gray-200 dark:border-gray-700 print:border-0 print:shadow-none">
                
                <!-- Receipt Header -->
                <div class="border-b border-gray-200 dark:border-gray-700 print:border-gray-300 px-8 py-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white print:text-black uppercase tracking-wide">Official Receipt</h1>
                            <p class="text-sm text-gray-600 dark:text-gray-400 print:text-gray-600 mt-1">Payment Confirmation</p>
                            @if($payment->status === 'succeeded')
                                <div class="mt-2 inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800 print:bg-green-50">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Payment Successful
                                </div>
                            @elseif($payment->status === 'pending')
                                <div class="mt-2 inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-800 print:bg-yellow-50">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    Payment Pending
                                </div>
                            @else
                                <div class="mt-2 inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800 print:bg-red-50">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    Payment Failed
                                </div>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500 dark:text-gray-400 print:text-gray-500 uppercase tracking-wider">Receipt Number</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white print:text-black mt-1 font-mono">{{ $payment->reference }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 print:text-gray-500 mt-2">{{ $payment->created_at->format('F d, Y') }}</p>
                            @if($payment->paid_at)
                                <p class="text-xs text-gray-500 dark:text-gray-400 print:text-gray-500">Paid: {{ $payment->paid_at->format('M d, Y h:i A') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Receipt Body -->
                <div class="px-8 py-8">
                    <!-- Payment Amount Section -->
                    <div class="mb-8">
                        <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 print:text-gray-500 uppercase tracking-wider mb-4">Payment Details</h2>
                        <div class="border-l-4 border-gray-900 dark:border-gray-100 print:border-gray-900 pl-4 mb-6">
                            <p class="text-sm text-gray-600 dark:text-gray-400 print:text-gray-600">Amount {{ $payment->status === 'succeeded' ? 'Paid' : 'Due' }}</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white print:text-black mt-1">
                                {{ $payment->currency }} {{ number_format($payment->amount, 2) }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 print:text-gray-600 mt-1">
                                {{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}
                            </p>
                        </div>

                        <table class="w-full">
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 print:divide-gray-300">
                                <tr>
                                    <td class="py-3 text-sm text-gray-700 dark:text-gray-300 print:text-gray-700">Transaction Reference</td>
                                    <td class="py-3 text-sm text-right font-mono text-gray-900 dark:text-white print:text-black">{{ $payment->reference }}</td>
                                </tr>
                                @if($payment->transaction_id)
                                <tr>
                                    <td class="py-3 text-sm text-gray-700 dark:text-gray-300 print:text-gray-700">Transaction ID</td>
                                    <td class="py-3 text-sm text-right font-mono text-gray-900 dark:text-white print:text-black">{{ $payment->transaction_id }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="py-3 text-sm text-gray-700 dark:text-gray-300 print:text-gray-700">Payment Method</td>
                                    <td class="py-3 text-sm text-right font-semibold text-gray-900 dark:text-white print:text-black">{{ $payment->payment_method ?? 'Online Payment' }}</td>
                                </tr>
                                <tr>
                                    <td class="py-3 text-sm text-gray-700 dark:text-gray-300 print:text-gray-700">Payment Gateway</td>
                                    <td class="py-3 text-sm text-right font-semibold text-gray-900 dark:text-white print:text-black">{{ ucfirst($payment->gateway ?? 'N/A') }}</td>
                                </tr>
                                <tr>
                                    <td class="py-3 text-sm text-gray-700 dark:text-gray-300 print:text-gray-700">Transaction Status</td>
                                    <td class="py-3 text-sm text-right font-semibold text-gray-900 dark:text-white print:text-black">{{ ucfirst($payment->status) }}</td>
                                </tr>
                                @if($payment->fixed_amount && $payment->isCustomAmount())
                                <tr>
                                    <td class="py-3 text-sm text-gray-700 dark:text-gray-300 print:text-gray-700">Original Amount</td>
                                    <td class="py-3 text-sm text-right font-semibold text-gray-900 dark:text-white print:text-black">{{ $payment->currency }} {{ number_format($payment->fixed_amount, 2) }}</td>
                                </tr>
                                @endif
                                <tr class="border-t-2 border-gray-900 dark:border-gray-100 print:border-gray-900">
                                    <td class="py-4 text-base font-semibold text-gray-900 dark:text-white print:text-black">Total Amount</td>
                                    <td class="py-4 text-lg text-right font-bold text-gray-900 dark:text-white print:text-black">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Student Information -->
                    @if($payment->student && $payment->student->user)
                    <div class="mb-8">
                        <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 print:text-gray-500 uppercase tracking-wider mb-4">Student Information</h2>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 print:text-gray-500 uppercase">Student Name</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white print:text-black mt-1">{{ $payment->student->user->name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 print:text-gray-500 uppercase">Student ID</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white print:text-black mt-1">{{ $payment->student->student_id }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 print:text-gray-500 uppercase">Email Address</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white print:text-black mt-1">{{ $payment->student->user->email }}</p>
                            </div>
                            @if($payment->student->academicGroup)
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 print:text-gray-500 uppercase">Academic Group</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white print:text-black mt-1">{{ $payment->student->academicGroup->name }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Payer Information -->
                    <div class="mb-8">
                        <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 print:text-gray-500 uppercase tracking-wider mb-4">Payer Information</h2>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 print:text-gray-500 uppercase">Payer Name</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white print:text-black mt-1">{{ $payment->getPayerDisplayName() }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 print:text-gray-500 uppercase">Payer Type</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white print:text-black mt-1">{{ ucfirst($payment->payer_type) }}</p>
                            </div>
                            @if($payment->payer_email)
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 print:text-gray-500 uppercase">Email Address</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white print:text-black mt-1">{{ $payment->payer_email }}</p>
                            </div>
                            @endif
                            @if($payment->payer_phone)
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 print:text-gray-500 uppercase">Phone Number</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white print:text-black mt-1">{{ $payment->payer_phone }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Academic Context -->
                    @if($payment->academicYear || $payment->academicPeriod || $payment->payment_period)
                    <div class="mb-8">
                        <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 print:text-gray-500 uppercase tracking-wider mb-4">Academic Context</h2>
                        <div class="grid grid-cols-2 gap-6">
                            @if($payment->academicYear)
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 print:text-gray-500 uppercase">Academic Year</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white print:text-black mt-1">{{ $payment->academicYear->name }}</p>
                            </div>
                            @endif
                            @if($payment->academicPeriod)
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 print:text-gray-500 uppercase">Academic Period</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white print:text-black mt-1">{{ $payment->academicPeriod->name }}</p>
                            </div>
                            @endif
                            @if($payment->payment_period)
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 print:text-gray-500 uppercase">Payment Period</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white print:text-black mt-1">{{ ucfirst(str_replace('_', ' ', $payment->payment_period)) }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Verification Info -->
                    @if($payment->verified_at)
                    <div class="mb-8">
                        <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 print:text-gray-500 uppercase tracking-wider mb-4">Verification</h2>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 print:text-gray-500 uppercase">Verified By</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white print:text-black mt-1">{{ $payment->verifier->name ?? 'System' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 print:text-gray-500 uppercase">Verified At</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white print:text-black mt-1">{{ $payment->verified_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Footer Note -->
                    <div class="border-t border-gray-200 dark:border-gray-700 print:border-gray-300 pt-6">
                        <p class="text-xs text-gray-500 dark:text-gray-400 print:text-gray-500 leading-relaxed">
                            This receipt serves as official confirmation of your payment transaction. Please retain this document for your records. 
                            For any inquiries regarding this transaction, please reference the receipt number above.
                        </p>
                    </div>
                </div>

                <!-- Pending Payment Alert (hidden in print) -->
                @if($payment->status === 'pending' && $payment->authorization_url)
                <div class="print:hidden bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 mx-8 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700 dark:text-yellow-200">
                                <strong>Payment Pending:</strong> This payment is awaiting completion. 
                                <a href="{{ $payment->authorization_url }}" target="_blank" class="font-medium underline hover:text-yellow-600">
                                    Complete payment now
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Actions Footer (hidden in print) -->
                <div class="print:hidden border-t border-gray-200 dark:border-gray-700 px-8 py-4 bg-gray-50 dark:bg-gray-900">
                    <div class="flex flex-col sm:flex-row gap-3 justify-between items-center">
                        <a href="{{ route('admin.transactions.index') }}"
                           class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                            ← Return to Transactions
                        </a>
                        <div class="flex gap-3">
                            @if($payment->status === 'pending' && $payment->authorization_url)
                            <a href="{{ $payment->authorization_url }}"
                               target="_blank"
                               class="px-4 py-2 text-sm bg-blue-600 text-white hover:bg-blue-700 rounded-md transition">
                                Complete Payment
                            </a>
                            @endif
                            <button onclick="window.print()"
                                    class="px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition">
                                Print Receipt
                            </button>
                            <button class="px-4 py-2 text-sm bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 hover:bg-gray-800 dark:hover:bg-gray-200 rounded-md transition">
                                Download PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>