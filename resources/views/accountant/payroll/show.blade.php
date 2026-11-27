<x-layouts.app pageName="Payroll run">
    <x-slot name="title">Payroll run review</x-slot>

    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <a href="{{ route('accountant.payroll.index') }}" class="text-sm font-medium text-violet-600 hover:text-violet-800 dark:text-violet-400">← Payroll runs</a>
                <h1 class="mt-2 text-xl font-semibold text-gray-900 dark:text-white">{{ $run->period_start->format('M d, Y') }} – {{ $run->period_end->format('M d, Y') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Status: <span class="font-medium capitalize">{{ $run->status }}</span> · Currency: {{ $run->currency }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500 dark:text-gray-400">Net total</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">GH₵ {{ $totalNetPay }}</p>
            </div>
        </div>

        @if(session('success'))
            <div class="rounded-lg bg-green-50 p-4 text-sm text-green-700 dark:bg-green-900/20 dark:text-green-300">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="rounded-lg bg-red-50 p-4 text-sm text-red-700 dark:bg-red-900/20 dark:text-red-300">
                <ul class="list-inside list-disc space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-800 dark:bg-amber-900/20 dark:text-amber-200">
            Confirm the correct salary cadence and every amount. Net pay is base pay + allowances − deductions; statutory tax is not calculated and marking paid does not transfer funds.
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Employee</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Base pay</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Allowance</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Deduction</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Net pay</th>
                            @if($run->status === 'draft')
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Review</th>
                            @else
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Confirmation</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($items as $item)
                            <tr>
                                <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    @if($run->status === 'draft')
                                        <form id="payroll-item-{{ $item->id }}" method="POST" action="{{ route('accountant.payroll.items.update', [$run, $item]) }}">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                    @endif
                                    <div class="font-medium">{{ $item->employee_name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $item->employee_id ?: 'No employee ID' }}{{ $item->department ? ' · '.$item->department : '' }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    @if($run->status === 'draft')
                                        <input form="payroll-item-{{ $item->id }}" type="number" name="base_salary" min="0" step="0.01" required value="{{ $item->base_salary }}" class="w-32 rounded border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white" aria-label="Base pay for {{ $item->employee_name }}">
                                    @else
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $run->currency }} {{ number_format($item->base_salary, 2) }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    @if($run->status === 'draft')
                                        <input form="payroll-item-{{ $item->id }}" type="number" name="allowances" min="0" step="0.01" required value="{{ $item->allowances }}" class="w-28 rounded border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white" aria-label="Allowance for {{ $item->employee_name }}">
                                    @else
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $run->currency }} {{ number_format($item->allowances, 2) }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    @if($run->status === 'draft')
                                        <input form="payroll-item-{{ $item->id }}" type="number" name="deductions" min="0" step="0.01" required value="{{ $item->deductions }}" class="w-28 rounded border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white" aria-label="Deduction for {{ $item->employee_name }}">
                                    @else
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $run->currency }} {{ number_format($item->deductions, 2) }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $run->currency }} {{ $item->net_pay }}</td>
                                <td class="px-4 py-4">
                                    @if($run->status === 'draft')
                                        <input form="payroll-item-{{ $item->id }}" type="text" name="notes" maxlength="2000" value="{{ $item->notes }}" placeholder="Note (optional)" class="mb-2 w-40 rounded border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white" aria-label="Review note for {{ $item->employee_name }}">
                                        <button form="payroll-item-{{ $item->id }}" type="submit" class="block rounded bg-violet-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-violet-700">{{ $item->confirmed_at ? 'Save & confirm' : 'Confirm line' }}</button>
                                    @elseif($item->confirmed_at)
                                        <span class="text-sm text-green-700 dark:text-green-400">Confirmed {{ $item->confirmed_at->format('M d, Y') }}</span>
                                    @else
                                        <span class="text-sm text-red-600">Not confirmed</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No employees in this run.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-end gap-3">
            @if($run->status === 'draft')
                @if($canApprove)
                    <form method="POST" action="{{ route('accountant.payroll.approve', $run) }}" onsubmit="return confirm('Approve this payroll run? Its lines will become read-only.');">
                        @csrf
                        <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 font-medium text-white hover:bg-green-700">Approve payroll</button>
                    </form>
                @else
                    <span class="text-sm text-gray-500 dark:text-gray-400">Confirm every employee line before approval.</span>
                @endif
            @elseif($run->status === 'approved')
                <span class="text-sm text-gray-500 dark:text-gray-400">Record this only after payment has been made outside the app.</span>
                <form method="POST" action="{{ route('accountant.payroll.paid', $run) }}" onsubmit="return confirm('Mark this payroll run as paid?');">
                    @csrf
                    <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 font-medium text-white hover:bg-green-700">Mark as paid</button>
                </form>
            @endif
        </div>
    </div>
</x-layouts.app>