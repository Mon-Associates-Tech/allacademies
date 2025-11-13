<x-layouts.app>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Payment Details') }}
            </h2>
            <a href="{{ route('admin.payments.index') }}"
               class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                ← Back to Payments
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Payment Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Payment Status Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment Information</h3>
                            @if($payment->status === 'succeeded')
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    ✓ Succeeded
                                </span>
                            @elseif($payment->status === 'pending')
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    ⏱ Pending
                                </span>
                            @else
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    ✗ Failed
                                </span>
                            @endif
                        </div>

                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Reference</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white font-mono">{{ $payment->reference }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Transaction ID</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white font-mono">{{ $payment->transaction_id ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Amount</dt>
                                <dd class="mt-1 text-2xl font-bold text-violet-600 dark:text-violet-400">
                                    {{ $payment->currency }} {{ number_format($payment->amount, 2) }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Type</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}
                                </dd>
                            </div>
                            @if($payment->fixed_amount && $payment->isCustomAmount())
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fixed Amount</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $payment->currency }} {{ number_format($payment->fixed_amount, 2) }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Custom Payment</dt>
                                    <dd class="mt-1">
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            Partial Payment
                                        </span>
                                    </dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Method</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $payment->payment_method ?? 'Online' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Gateway</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ ucfirst($payment->gateway ?? 'N/A') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date Created</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $payment->created_at->format('M d, Y h:i A') }}
                                </dd>
                            </div>
                            @if($payment->paid_at)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date Paid</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $payment->paid_at->format('M d, Y h:i A') }}
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    <!-- Student Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Student Information</h3>
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                @if($payment->student->user->profile_photo_url)
                                    <img class="h-16 w-16 rounded-full" src="{{ $payment->student->user->profile_photo_url }}" alt="">
                                @else
                                    <div class="h-16 w-16 rounded-full bg-violet-500 flex items-center justify-center text-white font-bold text-xl">
                                        {{ substr($payment->student->user->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $payment->student->user->name }}
                                </h4>
                                <dl class="mt-2 grid grid-cols-2 gap-4">
                                    <div>
                                        <dt class="text-sm text-gray-500 dark:text-gray-400">Student ID</dt>
                                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $payment->student->student_id }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm text-gray-500 dark:text-gray-400">Email</dt>
                                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $payment->student->user->email }}</dd>
                                    </div>
                                    @if($payment->student->academicGroup)
                                        <div>
                                            <dt class="text-sm text-gray-500 dark:text-gray-400">Academic Group</dt>
                                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $payment->student->academicGroup->name }}</dd>
                                        </div>
                                    @endif
                                    @if($payment->student->academicLevel)
                                        <div>
                                            <dt class="text-sm text-gray-500 dark:text-gray-400">Academic Level</dt>
                                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $payment->student->academicLevel->name }}</dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>
                        </div>
                    </div>

                    <!-- Payer Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payer Information</h3>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Payer Type</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ ucfirst($payment->payer_type) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Payer Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $payment->getPayerDisplayName() }}</dd>
                            </div>
                            @if($payment->payer_email)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $payment->payer_email }}</dd>
                                </div>
                            @endif
                            @if($payment->payer_phone)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $payment->payer_phone }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Academic Context -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Academic Context</h3>
                        <dl class="space-y-3">
                            @if($payment->academicYear)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Academic Year</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $payment->academicYear->name }}</dd>
                                </div>
                            @endif
                            @if($payment->academicPeriod)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Academic Period</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $payment->academicPeriod->name }}</dd>
                                </div>
                            @endif
                            @if($payment->payment_period)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Period</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $payment->payment_period)) }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Actions</h3>
                        <div class="space-y-2">
                            <button onclick="window.print()" class="w-full bg-violet-600 hover:bg-violet-700 text-white px-4 py-2 rounded-lg">
                                Print Receipt
                            </button>
                            <button class="w-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-900 dark:text-white px-4 py-2 rounded-lg">
                                Send Receipt Email
                            </button>
                        </div>
                    </div>

                    <!-- Verification Info -->
                    @if($payment->verified_at)
                        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-green-900 dark:text-green-100 mb-4">Verification Info</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-green-700 dark:text-green-300">Verified By</dt>
                                    <dd class="mt-1 text-sm text-green-900 dark:text-green-100">{{ $payment->verifier->name ?? 'System' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-green-700 dark:text-green-300">Verified At</dt>
                                    <dd class="mt-1 text-sm text-green-900 dark:text-green-100">{{ $payment->verified_at->format('M d, Y h:i A') }}</dd>
                                </div>
                            </dl>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
