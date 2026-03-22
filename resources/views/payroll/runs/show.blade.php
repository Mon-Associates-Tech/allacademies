<x-layouts.app title="Payroll Run Details" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Payroll' => route('payroll.entries.index'),
            'Runs' => route('payroll.runs.index'),
            'Details' => null,
        ]" />
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-2xl font-bold text-white mb-1">{{ $run->schedule->name }}</h2>
                            <p class="text-blue-100">Run #{{ $run->id }} • {{ $run->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                    <span class="px-4 py-2 rounded-lg text-sm font-semibold
                        @if($run->status === 'completed') bg-green-100 text-green-800
                        @elseif($run->status === 'processing') bg-blue-100 text-blue-800
                        @elseif($run->status === 'pending_approval') bg-yellow-100 text-yellow-800
                        @elseif($run->status === 'failed') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst(str_replace('_', ' ', $run->status)) }}
                    </span>
                </div>
            </div>

            <div class="p-8">
                <div class="grid md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4">
                        <div class="text-sm text-blue-600 dark:text-blue-400 font-medium">Total Amount</div>
                        <div class="text-2xl font-bold text-blue-900 dark:text-blue-100 mt-1">GH₵{{ number_format($run->total_amount, 2) }}</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4">
                        <div class="text-sm text-green-600 dark:text-green-400 font-medium">Recipients</div>
                        <div class="text-2xl font-bold text-green-900 dark:text-green-100 mt-1">{{ $run->recipient_count }}</div>
                    </div>
                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-xl p-4">
                        <div class="text-sm text-purple-600 dark:text-purple-400 font-medium">Initiated By</div>
                        <div class="text-lg font-semibold text-purple-900 dark:text-purple-100 mt-1">{{ $run->initiator->name }}</div>
                    </div>
                    <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl p-4">
                        <div class="text-sm text-amber-600 dark:text-amber-400 font-medium">Run Type</div>
                        <div class="text-lg font-semibold text-amber-900 dark:text-amber-100 mt-1">{{ ucfirst($run->run_type) }}</div>
                    </div>
                </div>

                @if($run->notes)
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 mb-6">
                        <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</div>
                        <p class="text-gray-600 dark:text-gray-400">{{ $run->notes }}</p>
                    </div>
                @endif

                <div class="flex gap-3">
                    @if($run->status === 'draft')
                        <form method="POST" action="{{ route('payroll.runs.submit', $run) }}">
                            @csrf
                            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Submit for Approval
                            </button>
                        </form>
                    @endif

                    @if($run->status === 'pending_approval')
                        <form method="POST" action="{{ route('payroll.runs.approve', $run) }}">
                            @csrf
                            <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                Approve & Process
                            </button>
                        </form>
                    @endif

                    @if(in_array($run->status, ['draft', 'pending_approval']))
                        <form method="POST" action="{{ route('payroll.runs.cancel', $run) }}" onsubmit="return confirm('Cancel this payroll run?');">
                            @csrf
                            <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                Cancel Run
                            </button>
                        </form>
                    @endif

                    @if(in_array($run->status, ['failed', 'completed']))
                        <form method="POST" action="{{ route('payroll.runs.retry', $run) }}">
                            @csrf
                            <button type="submit" class="px-6 py-3 bg-amber-600 text-white rounded-lg hover:bg-amber-700">
                                Retry Failed
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Disbursements</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Bank Account</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse($run->disbursements as $disbursement)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $disbursement->payrollEntry->full_name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $disbursement->payrollEntry->email }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    @if($disbursement->bankAccount)
                                        {{ $disbursement->bankAccount->bank_name }}<br>
                                        <span class="text-gray-500 dark:text-gray-400">{{ $disbursement->bankAccount->account_number }}</span>
                                    @else
                                        <span class="text-red-600 dark:text-red-400">No account</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    GH₵{{ number_format($disbursement->amount, 2) }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                        @if($disbursement->status === 'success') bg-green-100 text-green-800
                                        @elseif($disbursement->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($disbursement->status === 'failed') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($disbursement->status) }}
                                    </span>
                                    @if($disbursement->failure_reason)
                                        <div class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $disbursement->failure_reason }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="{{ route('payroll.disbursements.payslip', $disbursement) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">
                                        View Payslip
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No disbursements found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
