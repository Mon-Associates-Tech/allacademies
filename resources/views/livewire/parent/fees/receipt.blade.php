<x-layouts.app>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto">
        <!-- Success Message -->
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6 mb-8">
            <div class="flex items-center">
                <svg class="w-12 h-12 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="ml-4">
                    <h2 class="text-2xl font-bold text-green-800 dark:text-green-200">Payment Successful!</h2>
                    <p class="text-green-600 dark:text-green-400 mt-1">Your payment has been processed successfully.</p>
                </div>
            </div>
        </div>

        <!-- Receipt -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg" id="receipt">
            <!-- Header with School Info -->
            <div class="border-b border-gray-200 dark:border-gray-700 p-8">
                <div class="flex justify-between items-start mb-6">
                    <div class="flex items-start space-x-4">
                        @if($payment->school && $payment->school->logo)
                            <img src="{{ asset('storage/' . $payment->school->logo) }}" alt="{{ $payment->school->name }}" class="w-16 h-16 object-contain">
                        @else
                            <div class="w-16 h-16 bg-violet-600 rounded-lg flex items-center justify-center">
                                <span class="text-2xl font-bold text-white">{{ $payment->school ? strtoupper(substr($payment->school->name, 0, 2)) : 'SC' }}</span>
                            </div>
                        @endif
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $payment->school->name ?? 'School Name' }}</h2>
                            @if($payment->school)
                                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1 space-y-0.5">
                                    @if($payment->school->address)
                                        <p>{{ $payment->school->address }}</p>
                                    @endif
                                    @if($payment->school->city || $payment->school->state)
                                        <p>{{ $payment->school->city }}{{ $payment->school->state ? ', ' . $payment->school->state : '' }}</p>
                                    @endif
                                    <div class="flex items-center space-x-4 mt-1">
                                        @if($payment->school->phone)
                                            <span>📞 {{ $payment->school->phone }}</span>
                                        @endif
                                        @if($payment->school->email)
                                            <span>✉️ {{ $payment->school->email }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Date</div>
                        <div class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $payment->created_at->format('M d, Y') }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">{{ $payment->created_at->format('h:i A') }}</div>
                    </div>
                </div>

                <div class="flex justify-between items-end">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Payment Receipt</h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-2">Reference: <span class="font-mono font-semibold">{{ $payment->reference }}</span></p>
                    </div>
                    <div class="px-4 py-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                        <span class="text-green-800 dark:text-green-200 font-semibold">PAID</span>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <!-- Payer Information -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-4 border-b pb-2">Paid By</h3>
                        <div class="space-y-2">
                            <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $payment->payer->name }}</p>
                            <p class="text-gray-600 dark:text-gray-400">{{ $payment->payer->email }}</p>
                            @if($payment->payer->phone)
                                <p class="text-gray-600 dark:text-gray-400">{{ $payment->payer->phone }}</p>
                            @endif
                            <p class="text-sm text-gray-500 dark:text-gray-400">Parent/Guardian</p>
                        </div>
                    </div>

                    <!-- Student Information -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-4 border-b pb-2">Student Details</h3>
                        <div class="space-y-2">
                            <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $payment->student->user->name }}</p>
                            <p class="text-gray-600 dark:text-gray-400">{{ $payment->student->academicLevel->name ?? 'N/A' }} - {{ $payment->student->academicGroup->name ?? 'N/A' }}</p>
                            @if($payment->student->student_id)
                                <p class="text-sm text-gray-500 dark:text-gray-400">Student ID: <span class="font-mono">{{ $payment->student->student_id }}</span></p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6 mb-8">
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-4">Payment Details</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Payment Type:</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-100">{{ ucwords(str_replace('_', ' ', $type)) }}</span>
                        </div>
                        @if($payment->academicPeriod)
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Academic Period:</span>
                                <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $payment->academicPeriod->name }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Currency:</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $payment->currency }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Payment Method:</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-100">Paystack (Card/Bank)</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Transaction ID:</span>
                            <span class="font-mono text-sm text-gray-800 dark:text-gray-100">{{ $payment->reference }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Payment Date:</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $payment->created_at->format('F d, Y \a\t h:i A') }}</span>
                        </div>
                        <div class="flex justify-between pt-3 border-t-2 border-gray-200 dark:border-gray-700">
                            <span class="text-lg font-semibold text-gray-700 dark:text-gray-300">Amount Paid:</span>
                            <span class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="flex items-center justify-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="text-lg font-semibold text-green-700 dark:text-green-300">Payment Verified & Confirmed</span>
                </div>
            </div>

            <!-- Footer -->
            <div class="border-t border-gray-200 dark:border-gray-700 p-8 bg-gray-50 dark:bg-gray-900">
                <div class="text-center space-y-3">
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Thank you for your payment!</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        This is an electronically generated receipt and does not require a physical signature.
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        For any queries regarding this payment, please contact the school administration.
                    </p>
                    @if($payment->school && ($payment->school->email || $payment->school->phone))
                        <div class="flex items-center justify-center space-x-4 text-sm text-gray-500 dark:text-gray-400 pt-2">
                            @if($payment->school->email)
                                <span>✉️ {{ $payment->school->email }}</span>
                            @endif
                            @if($payment->school->phone)
                                <span>📞 {{ $payment->school->phone }}</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-8 flex flex-col sm:flex-row justify-center gap-4">
            <button onclick="window.print()"
                    class="px-6 py-3 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition flex items-center justify-center space-x-2 shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Print Receipt</span>
            </button>
            <a href="{{ route('parent.fees.index') }}"
               class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition flex items-center justify-center space-x-2 shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Back to Payments</span>
            </a>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #receipt, #receipt * {
                visibility: visible;
            }
            #receipt {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            /* Hide buttons when printing */
            button, a {
                display: none !important;
            }
        }
    </style>
</x-layouts.app>

