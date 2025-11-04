<x-layouts.app>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Payment Receipt') }}
            </h2>
            <button onclick="window.print()"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Receipt
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl overflow-hidden border-4 border-green-500 dark:border-green-600">
                <!-- Success Banner -->
                <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 text-center">
                    <svg class="w-16 h-16 mx-auto text-white mb-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <h3 class="text-2xl font-bold text-white">Payment Successful!</h3>
                    <p class="text-green-100 mt-1">Your payment has been processed successfully</p>
                </div>

                <div class="p-8">
                    <!-- Receipt Header -->
                    <div class="text-center mb-8 pb-8 border-b-2 border-gray-200 dark:border-gray-700">
                        <h4 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Official Receipt</h4>
                        <p class="text-gray-600 dark:text-gray-400">{{ $payment->student->school->name ?? 'School Name' }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">School Fees Payment</p>
                    </div>

                    <!-- Receipt Details -->
                    <div class="grid grid-cols-2 gap-6 mb-8">
                        <div>
                            <h5 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase mb-3">Student Information</h5>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-xs text-gray-500 dark:text-gray-500">Name</dt>
                                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $payment->student->user->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-gray-500 dark:text-gray-500">Student ID</dt>
                                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $payment->student->student_id }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-gray-500 dark:text-gray-500">Class/Level</dt>
                                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $payment->student->academicLevel->name ?? 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h5 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase mb-3">Payment Details</h5>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-xs text-gray-500 dark:text-gray-500">Receipt No.</dt>
                                    <dd class="text-sm font-mono font-medium text-gray-900 dark:text-white">{{ $payment->reference }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-gray-500 dark:text-gray-500">Date</dt>
                                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $payment->created_at->format('F d, Y h:i A') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-gray-500 dark:text-gray-500">Term</dt>
                                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $payment->academicPeriod->name ?? 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Amount Section -->
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6 mb-8">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-gray-700 dark:text-gray-300 font-medium">Amount Paid</span>
                            <span class="text-3xl font-bold text-green-600 dark:text-green-400">₵{{ number_format($payment->amount, 2) }}</span>
                        </div>
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Payment Method</span>
                                <span class="font-medium text-gray-900 dark:text-white">Paystack</span>
                            </div>
                            <div class="flex justify-between text-sm mt-2">
                                <span class="text-gray-600 dark:text-gray-400">Status</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Completed
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="text-center pt-8 border-t-2 border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            This is a computer-generated receipt and does not require a signature
                        </p>
                        <div class="flex justify-center space-x-4">
                            <a href="{{ route('students.fees.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-violet-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-700">
                                View All Payments
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
