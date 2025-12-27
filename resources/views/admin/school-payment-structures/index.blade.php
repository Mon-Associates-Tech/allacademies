<x-layouts.app>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Fee Structures') }}
            </h2>
            <a href="{{ route('admin.school-payment-structures.create') }}"
               class="bg-violet-600 hover:bg-violet-700 text-white px-4 py-2 rounded-lg inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Fee Structure
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
                <form method="GET" action="{{ route('admin.school-payment-structures.index') }}" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        <!-- Academic Year -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Academic Year</label>
                            <select name="academic_year_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="">All Years</option>
                                @foreach($filterOptions['academic_years'] as $year)
                                    <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Academic Period -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Academic Period</label>
                            <select name="academic_period_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="">All Periods</option>
                                @foreach($filterOptions['academic_periods'] as $period)
                                    <option value="{{ $period->id }}" {{ request('academic_period_id') == $period->id ? 'selected' : '' }}>
                                        {{ $period->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Payment Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Type</label>
                            <select name="payment_type" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="">All Types</option>
                                @foreach($filterOptions['payment_types'] as $key => $label)
                                    <option value="{{ $key }}" {{ request('payment_type') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                            <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <button type="submit" class="bg-violet-600 hover:bg-violet-700 text-white px-6 py-2 rounded-lg">
                            Apply Filters
                        </button>
                        <a href="{{ route('admin.school-payment-structures.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                            Clear Filters
                        </a>
                    </div>
                </form>
            </div>

            <!-- Fee Structures Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Amount
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Academic Context
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Due Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($feeStructures as $fee)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $fee->name }}
                                    </div>
                                    @if($fee->is_mandatory)
                                        <span class="text-xs text-red-600 dark:text-red-400">Mandatory</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ ucfirst(str_replace('_', ' ', $fee->payment_type)) }}
                                        </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $fee->currency }} {{ number_format($fee->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                    @if($fee->academicYear)
                                        <div>{{ $fee->academicYear->name }}</div>
                                    @endif
                                    @if($fee->academicPeriod)
                                        <div class="text-xs text-gray-500">{{ $fee->academicPeriod->name }}</div>
                                    @endif
                                    @if($fee->academicGroup)
                                        <div class="text-xs text-gray-500">{{ $fee->academicGroup->name }}</div>
                                    @endif
                                    @if($fee->academicLevel)
                                        <div class="text-xs text-gray-500">{{ $fee->academicLevel->name }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @if($fee->due_date)
                                        <div>{{ $fee->due_date->format('M d, Y') }}</div>
                                        @if($fee->isOverdue())
                                            <span class="text-xs text-red-600 dark:text-red-400">Overdue</span>
                                        @elseif($fee->isDueSoon())
                                            <span class="text-xs text-yellow-600 dark:text-yellow-400">Due Soon</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">No due date</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($fee->is_active)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                Active
                                            </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                                Inactive
                                            </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.school-payment-structures.show', $fee) }}"
                                           class="text-violet-600 hover:text-violet-900 dark:text-violet-400">
                                            View
                                        </a>
                                        <a href="{{ route('admin.school-payment-structures.edit', $fee) }}"
                                           class="text-blue-600 hover:text-blue-900 dark:text-blue-400">
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    No fee structures found.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $feeStructures->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
