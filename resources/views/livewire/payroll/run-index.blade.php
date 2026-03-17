<div>
    <div class="mb-4 flex flex-wrap gap-4">
        <select wire:model.live="filterStatus" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
            <option value="">All Statuses</option>
            <option value="draft">Draft</option>
            <option value="pending_approval">Pending Approval</option>
            <option value="approved">Approved</option>
            <option value="processing">Processing</option>
            <option value="completed">Completed</option>
            <option value="failed">Failed</option>
            <option value="cancelled">Cancelled</option>
        </select>
        
        <select wire:model.live="filterType" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
            <option value="">All Types</option>
            <option value="immediate">Immediate</option>
            <option value="scheduled">Scheduled</option>
            <option value="recurring">Recurring</option>
        </select>
        
        <input type="date" wire:model.live="dateFrom" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
        <input type="date" wire:model.live="dateTo" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Schedule</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Recipients</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Initiated By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                @forelse($runs as $run)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $run->schedule->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $run->recipient_count }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">GH₵{{ number_format($run->total_amount, 2) }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full px-2 text-xs font-semibold">{{ ucfirst($run->status) }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $run->initiator->name }}</td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('payroll.runs.show', $run) }}" class="text-blue-600 hover:text-blue-900">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No payroll runs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $runs->links() }}</div>
</div>
