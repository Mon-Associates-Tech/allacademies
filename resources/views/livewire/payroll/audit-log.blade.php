<div>
    <div class="mb-4 flex flex-wrap gap-4">
        <input type="text" wire:model.live.debounce.300ms="filterAction" placeholder="Search action..." class="rounded-md border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900">
        <input type="date" wire:model.live="dateFrom" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
        <input type="date" wire:model.live="dateTo" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Timestamp</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Action</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Subject</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">IP Address</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                @forelse($logs as $log)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $log->user->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $log->action }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $log->ip_address }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No audit logs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $logs->links() }}</div>
</div>
