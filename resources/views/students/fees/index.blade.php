<x-layouts.app>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('School Fees & Payment History') }}
            </h2>
            @if($remainingAmount > 0)
                <a href="{{ route('students.fees.payment') }}"
                   class="inline-flex items-center px-4 py-2 bg-violet-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-700 focus:bg-violet-700 active:bg-violet-900 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    Make Payment
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Payment Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Total Fees Card -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium uppercase">Total Fees</p>
                            <p class="text-3xl font-bold mt-2">₵{{ number_format($termTotalAmount, 2) }}</p>
                            <p class="text-blue-100 text-xs mt-1">{{ $currentTerm->name ?? 'Current Term' }}</p>
                        </div>
                        <div class="bg-white/20 rounded-full p-3">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                                <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Amount Paid Card -->
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium uppercase">Amount Paid</p>
                            <p class="text-3xl font-bold mt-2">₵{{ number_format($totalPaid, 2) }}</p>
                            <p class="text-green-100 text-xs mt-1">
                                @if($termTotalAmount > 0)
                                    {{ number_format(($totalPaid/$termTotalAmount)*100, 1) }}%
                                @else
                                    0%
                                @endif
                            </p>
                        </div>
                        <div class="bg-white/20 rounded-full p-3">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Balance Card -->
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-100 text-sm font-medium uppercase">Balance</p>
                            <p class="text-3xl font-bold mt-2">₵{{ number_format($remainingAmount, 2) }}</p>
                            @if($remainingAmount > 0)
                                <p class="text-orange-100 text-xs mt-1">Outstanding</p>
                            @else
                                <p class="text-orange-100 text-xs mt-1">Fully Paid</p>
                            @endif
                        </div>
                        <div class="bg-white/20 rounded-full p-3">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Pending Payments Card -->
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium uppercase">Pending</p>
                            <p class="text-3xl font-bold mt-2">{{ $pendingPayments }}</p>
                            <p class="text-purple-100 text-xs mt-1">Transactions</p>
                        </div>
                        <div class="bg-white/20 rounded-full p-3">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Bar with Payment Button -->
            @if($termTotalAmount > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment Progress</h3>
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ number_format(($totalPaid/$termTotalAmount)*100, 1) }}%
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4 dark:bg-gray-700">
                        <div class="bg-gradient-to-r from-green-500 to-green-600 h-4 rounded-full transition-all duration-500"
                             style="width: {{ min(($totalPaid/$termTotalAmount)*100, 100) }}%">
                        </div>
                    </div>
                    <div class="flex justify-between items-center mt-3">
                        <div class="flex justify-between w-full text-xs text-gray-500 dark:text-gray-400">
                            <span>₵0</span>
                            <span>₵{{ number_format($termTotalAmount, 2) }}</span>
                        </div>
                    </div>
                    @if($remainingAmount > 0)
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('students.fees.payment') }}"
                               class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-violet-600 to-violet-700 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-wider hover:from-violet-700 hover:to-violet-800 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                Make Payment - ₵{{ number_format($remainingAmount, 2) }} Remaining
                            </a>
                        </div>
                    @else
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <svg class="w-6 h-6 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-green-700 dark:text-green-300 font-semibold">Payment Complete! Thank you.</span>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Payment History Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment History</h3>

                    <div class="flex items-center space-x-4">
                        <form method="GET" class="flex items-center space-x-2">
                            <select name="type" class="rounded-md border-gray-300 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-2 py-1">
                                <option value="">All Types</option>
                                <option value="school_fee" {{ request('type') === 'school_fee' ? 'selected' : '' }}>School Fee</option>
                                <option value="school_payment" {{ request('type') === 'school_payment' ? 'selected' : '' }}>Portal Payment</option>
                            </select>

                            <select name="status" class="rounded-md border-gray-300 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-2 py-1">
                                <option value="">Any Status</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="succeeded" {{ request('status') === 'succeeded' ? 'selected' : '' }}>Succeeded</option>
                                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>

                            <input type="date" name="from" value="{{ request('from') }}" class="rounded-md border-gray-300 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-2 py-1" />
                            <input type="date" name="to" value="{{ request('to') }}" class="rounded-md border-gray-300 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-2 py-1" />

                            <button type="submit" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded text-sm">Filter</button>
                        </form>

                        @if(in_array(auth()->user()->role ?? '', ['admin','accountant']))
                            <form method="POST" action="{{ route('admin.payments.manual') }}" class="inline-flex items-center space-x-2">
                                @csrf
                                <input type="hidden" name="student_id" value="{{ $student->id }}" />
                                <input name="amount" type="number" step="0.01" placeholder="Amount" class="rounded-md border-gray-300 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-2 py-1" required />
                                <select name="status" class="rounded-md border-gray-300 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-2 py-1">
                                    <option value="pending">Pending</option>
                                    <option value="succeeded">Succeeded</option>
                                    <option value="failed">Failed</option>
                                </select>
                                <button type="submit" class="px-3 py-1 bg-violet-600 text-white rounded text-sm">Create</button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reference</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Term</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Payer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($paymentHistory as $payment)
                            @php
                                $table = $payment->getTable();
                                $modelKey = $table === 'school_fees' ? 'school_fee' : 'school_payment';
                                $payerName = $payment->payer_name ?? ($payment->payer->name ?? 'N/A');
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $payment->created_at->format('M d, Y') }}
                                    <br>
                                    <span class="text-xs text-gray-500">{{ $payment->created_at->format('h:i A') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600 dark:text-gray-400">
                                    @php
                                    $shortRef = \Illuminate\Support\Str::limit($payment->reference ?? 'N/A', 12, '...');
                                @endphp
                                <x-copyable-text :text="$payment->reference" :show-text="$shortRef" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ ucfirst(str_replace('_', ' ', $modelKey)) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $payment->academicPeriod->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $payerName }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    ₵{{ number_format($payment->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($payment->status === 'succeeded')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Success
                                    </span>
                                    @elseif($payment->status === 'pending')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        Pending
                                    </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        Failed
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        @if($payment->status === 'succeeded')
                                            @if($modelKey === 'school_fee')
                                                <a href="{{ route('students.fees.receipt', $payment) }}"
                                                   class="text-violet-600 hover:text-violet-900 dark:text-violet-400 dark:hover:text-violet-300">View Receipt</a>
                                            @else
                                                <a href="{{ route('students.fees.receipt.payment', $payment) }}"
                                                   class="text-violet-600 hover:text-violet-900 dark:text-violet-400 dark:hover:text-violet-300">View Details</a>
                                            @endif
                                        @endif

                                        @if(in_array(auth()->user()->role ?? '', ['admin','accountant']))
                                            <form method="POST" action="{{ route('admin.payments.update-status', ['id' => $payment->id]) }}" style="display:inline-block;margin-right:6px;">
                                                @csrf
                                                <input type="hidden" name="model" value="{{ $modelKey }}" />
                                                <input type="hidden" name="status" value="succeeded" />
                                                <button type="submit" class="text-green-600 hover:text-green-900">Mark Paid</button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.payments.update-status', ['id' => $payment->id]) }}" style="display:inline-block;">
                                                @csrf
                                                <input type="hidden" name="model" value="{{ $modelKey }}" />
                                                <input type="hidden" name="status" value="failed" />
                                                <button type="submit" class="text-red-600 hover:text-red-900">Mark Failed</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No payment history found</p>
                                    @if($remainingAmount > 0)
                                        <a href="{{ route('students.fees.payment') }}"
                                           class="mt-4 inline-flex items-center px-4 py-2 bg-violet-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-700 focus:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                            </svg>
                                            Make Your First Payment
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $paymentHistory->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
