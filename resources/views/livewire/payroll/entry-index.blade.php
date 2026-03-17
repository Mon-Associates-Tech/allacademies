<div>
    <div class="mb-4 flex flex-wrap gap-4">
        <input type="text" 
               wire:model.live.debounce.300ms="search" 
               placeholder="Search by name or email..."
               class="rounded-md border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900">
        
        <select wire:model.live="filterRole" 
                class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
            <option value="">All Roles</option>
            @foreach($payrollRoles as $role)
                <option value="{{ $role->id }}">{{ $role->name }}</option>
            @endforeach
        </select>
        
        <select wire:model.live="filterStatus" 
                class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
            <option value="">All Statuses</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="suspended">Suspended</option>
        </select>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Name
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Role
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Salary
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Bank Account
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                @forelse($entries as $entry)
                    <tr>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $entry->full_name }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $entry->email }}
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $entry->payrollRole?->name ?? $entry->system_role }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            GH₵{{ number_format($entry->gross_salary, 2) }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                            @if($entry->bankAccount)
                                <span class="text-green-600 dark:text-green-400">✓ Verified</span>
                            @else
                                <span class="text-red-600 dark:text-red-400">Not Set</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5
                                @if($entry->status === 'active') bg-green-100 text-green-800
                                @elseif($entry->status === 'inactive') bg-gray-100 text-gray-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($entry->status) }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                            <a href="{{ route('payroll.entries.edit', $entry) }}" 
                               class="text-blue-600 hover:text-blue-900 dark:text-blue-400">
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            No payroll entries found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $entries->links() }}
    </div>
</div>
