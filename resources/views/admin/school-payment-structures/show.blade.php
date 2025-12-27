<x-layouts.app>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Fee Structure Details') }}
            </h2>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.school-payment-structures.edit', $school_payment_structure) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Edit
                </a>
                <a href="{{ route('admin.school-payment-structures.index') }}"
                   class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                    ← Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div class="flex justify-between items-start mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Basic Information</h3>
                            @if($school_payment_structure->is_active)
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    Active
                                </span>
                            @else
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                    Inactive
                                </span>
                            @endif
                        </div>

                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fee Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white font-semibold">{{ $school_payment_structure->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Type</dt>
                                <dd class="mt-1">
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ ucfirst(str_replace('_', ' ', $school_payment_structure->payment_type)) }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Amount</dt>
                                <dd class="mt-1 text-2xl font-bold text-violet-600 dark:text-violet-400">
                                    {{ $school_payment_structure->currency }} {{ number_format($school_payment_structure->amount, 2) }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Period</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $school_payment_structure->payment_period ? ucfirst(str_replace('_', ' ', $school_payment_structure->payment_period)) : 'N/A' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Due Date</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    @if($school_payment_structure->due_date)
                                        {{ $school_payment_structure->due_date->format('M d, Y') }}
                                        @if($school_payment_structure->isOverdue())
                                            <span class="ml-2 text-xs text-red-600 dark:text-red-400">(Overdue)</span>
                                        @elseif($school_payment_structure->isDueSoon())
                                            <span class="ml-2 text-xs text-yellow-600 dark:text-yellow-400">(Due Soon)</span>
                                        @endif
                                    @else
                                        No due date set
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fee Type</dt>
                                <dd class="mt-1">
                                    @if($school_payment_structure->is_mandatory)
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            Mandatory
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                            Optional
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Partial Payments</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    @if($school_payment_structure->allow_partial_payment)
                                        Allowed (Min: {{ $school_payment_structure->currency }} {{ number_format($school_payment_structure->minimum_partial_amount, 2) }})
                                    @else
                                        Not Allowed
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $school_payment_structure->created_at->format('M d, Y') }}
                                    @if($school_payment_structure->creator)
                                        <div class="text-xs text-gray-500">by {{ $school_payment_structure->creator->name }}</div>
                                    @endif
                                </dd>
                            </div>
                        </dl>

                        @if($school_payment_structure->description)
                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Description</dt>
                                <dd class="text-sm text-gray-900 dark:text-white">{{ $school_payment_structure->description }}</dd>
                            </div>
                        @endif
                    </div>

                    <!-- Academic Context -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Academic Context</h3>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Academic Year</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $school_payment_structure->academicYear?->name ?? 'All Years' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Academic Period</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $school_payment_structure->academicPeriod?->name ?? 'All Periods' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Academic Group</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $school_payment_structure->academicGroup?->name ?? 'All Groups' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Academic Level</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $school_payment_structure->academicLevel?->name ?? 'All Levels' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Statistics Sidebar -->
                <div class="space-y-6">
                    <!-- Collection Statistics -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Collection Statistics</h3>

                        <div class="space-y-4">
                            <div>
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Applicable Students</dt>
                                <dd class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $stats['applicable_students'] }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Total Collected</dt>
                                <dd class="text-2xl font-bold text-green-600 dark:text-green-400">
                                    GHS {{ number_format($stats['total_collected'], 2) }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Pending</dt>
                                <dd class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                                    GHS {{ number_format($stats['total_pending'], 2) }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Collection Rate</dt>
                                <dd class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ number_format($stats['collection_rate'], 1) }}%
                                </dd>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Actions</h3>
                        <div class="space-y-2">
                            <a href="{{ route('admin.payments.index', ['school_payment_structure' => $school_payment_structure->id]) }}"
                               class="block w-full text-center bg-violet-600 hover:bg-violet-700 text-white px-4 py-2 rounded-lg">
                                View Payments
                            </a>
                            <form method="POST" action="{{ route('admin.school-payment-structures.destroy', $school_payment_structure) }}"
                                  onsubmit="return confirm('Are you sure you want to delete this fee structure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="block w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                                    Delete Fee Structure
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
