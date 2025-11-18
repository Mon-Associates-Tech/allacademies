<x-layouts.app title="Payment Details">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('admin.transactions.index') }}"
               class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 mb-4">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Transactions
            </a>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Payment Details</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Reference: {{ $payment->reference }}</p>
                </div>
                <div class="flex items-center gap-3">
                    @if($payment->status === 'succeeded')
                        <span class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Succeeded
                        </span>
                    @elseif($payment->status === 'pending')
                        <span class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-700">
                            <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Pending
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Failed
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Amount Card -->
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Amount {{ $payment->status === 'succeeded' ? 'Paid' : '' }}</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-gray-100">
                        {{ $payment->currency }} {{ number_format($payment->amount, 2) }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Payment Type</p>
                    <p class="mt-1 text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Pending Payment Alert -->
        @if($payment->status === 'pending' && $payment->authorization_url)
            <div class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg p-6 mb-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">Payment Pending</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            This payment is awaiting completion. The payer can complete the payment using the link below.
                        </p>
                        <div class="mt-4">
                            <a href="{{ $payment->authorization_url }}"
                               target="_blank"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                Complete Payment
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Payment Information -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Payment Information</h2>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-2 gap-x-6 gap-y-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reference</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-mono">{{ $payment->reference }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Transaction ID</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-mono">{{ $payment->transaction_id ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Payment Method</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $payment->payment_method ?? 'Online Payment' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Gateway</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($payment->gateway ?? 'N/A') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $payment->created_at->format('M d, Y') }}
                                    <span class="text-gray-500 dark:text-gray-400">at {{ $payment->created_at->format('h:i A') }}</span>
                                </dd>
                            </div>
                            @if($payment->paid_at)
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Paid</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $payment->paid_at->format('M d, Y') }}
                                        <span class="text-gray-500 dark:text-gray-400">at {{ $payment->paid_at->format('h:i A') }}</span>
                                    </dd>
                                </div>
                            @endif
                            @if($payment->fixed_amount && $payment->isCustomAmount())
                                <div class="col-span-2">
                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fixed Amount</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $payment->currency }} {{ number_format($payment->fixed_amount, 2) }}
                                        <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">(Partial Payment)</span>
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    <!-- Student Information -->
                    @if($payment->student && $payment->student->user)
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Student Information</h2>
                            </div>
                            <div class="p-6">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0">
                                        @if($payment->student->user->profile_photo_url)
                                            <img class="h-16 w-16 rounded-lg object-cover border border-gray-200 dark:border-gray-700"
                                                 src="{{ $payment->student->user->profile_photo_url }}"
                                                 alt="{{ $payment->student->user->name }}">
                                        @else
                                            <div class="h-16 w-16 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center border border-gray-200 dark:border-gray-700">
                                            <span class="text-xl font-semibold text-gray-600 dark:text-gray-400">
                                                {{ substr($payment->student->user->name, 0, 1) }}
                                            </span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $payment->student->user->name }}
                                        </h3>
                                        <dl class="mt-3 grid grid-cols-2 gap-x-4 gap-y-3">
                                            <div>
                                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Student ID</dt>
                                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $payment->student->student_id }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 truncate">{{ $payment->student->user->email }}</dd>
                                            </div>
                                            @if($payment->student->academicGroup)
                                                <div>
                                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Academic Group</dt>
                                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $payment->student->academicGroup->name }}</dd>
                                                </div>
                                            @endif
                                            @if($payment->student->academicLevel)
                                                <div>
                                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Academic Level</dt>
                                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $payment->student->academicLevel->name }}</dd>
                                                </div>
                                            @endif
                                        </dl>
                                    </div>
                                </div>
                            </div>
                    @endif

                    <!-- Payer Information -->

                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Payer Information</h2>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-2 gap-x-4 gap-y-3">
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Payer Type</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($payment->payer_type) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $payment->getPayerDisplayName() }}</dd>
                                </div>
                                @if($payment->payer_email)
                                    <div>
                                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $payment->payer_email }}</dd>
                                    </div>
                                @endif
                                @if($payment->payer_phone)
                                    <div>
                                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Phone</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $payment->payer_phone }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Academic Context -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Academic Context</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        @if($payment->academicYear)
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Academic Year</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $payment->academicYear->name }}</dd>
                            </div>
                        @endif
                        @if($payment->academicPeriod)
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Academic Period</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $payment->academicPeriod->name }}</dd>
                            </div>
                        @endif
                        @if($payment->payment_period)
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Payment Period</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ ucfirst(str_replace('_', ' ', $payment->payment_period)) }}</dd>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Actions</h2>
                    </div>
                    <div class="p-6 space-y-3">
                        @if($payment->status === 'pending' && $payment->authorization_url)
                            <a href="{{ $payment->authorization_url }}"
                               target="_blank"
                               class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-gray-900 dark:bg-gray-700 hover:bg-gray-800 dark:hover:bg-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Complete Payment
                            </a>
                        @endif
                        <button onclick="window.print()"
                                class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Print Receipt
                        </button>
                        <button class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Send Receipt
                        </button>
                        <button class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download PDF
                        </button>
                    </div>
                </div>

                <!-- Verification -->
                @if($payment->verified_at)
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Verification</h2>
                        </div>
                        <div class="p-6 space-y-3">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Verified By</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $payment->verifier->name ?? 'System' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Verified At</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $payment->verified_at->format('M d, Y h:i A') }}</dd>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
