<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="mx-auto">
        <!-- Header Section -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Payment Management</h1>
                <p class="mt-2 text-sm text-gray-600">Manage school fee structures and other payments</p>
            </div>
            <button
                wire:click="showCreateForm"
                class="inline-flex items-center px-6 py-3 border border-transparent rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add New Payment
            </button>
        </div>

        <!-- Success/Error Messages -->
        @if (session()->has('success'))
            <div class="rounded-lg bg-green-50 p-4 border border-green-200 animate-fadeIn mb-6">
                <div class="flex">
                    <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <p class="ml-3 text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="rounded-lg bg-red-50 p-4 border border-red-200 animate-fadeIn mb-6">
                <div class="flex">
                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <p class="ml-3 text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Section 1: School Fees (Tuition/Admission) -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <div class="bg-indigo-100 p-2 rounded-lg mr-3">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-800">School Fees</h2>
            </div>

            @if($schoolFees->count() > 0)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Period/Year</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Target</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($schoolFees as $fee)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $fee->name }}</div>
                                        @if($fee->description)
                                            <div class="text-xs text-gray-500 truncate max-w-xs">{{ $fee->description }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 capitalize">
                                            {{ str_replace('_', ' ', $fee->payment_type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900">₵{{ number_format($fee->amount, 2) }}</div>
                                        @if($fee->allow_partial_payment)
                                            <div class="text-xs text-green-600">Partial allowed</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $fee->academicPeriod?->name ?? 'All Periods' }}</div>
                                        <div class="text-xs text-gray-500">{{ $fee->academicYear?->name ?? 'All Years' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($fee->academic_group_id)
                                            <div class="text-sm text-gray-900">{{ $fee->academicGroup->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $fee->academicLevel?->name ?? 'All Levels' }}</div>
                                        @else
                                            <span class="text-sm text-gray-500 italic">All Students</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <button wire:click="edit({{ $fee->id }})" class="text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 p-1 rounded">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button wire:click="delete({{ $fee->id }})" wire:confirm="Are you sure?" class="text-red-600 hover:text-red-900 hover:bg-red-50 p-1 rounded">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-2xl shadow p-8 text-center border border-gray-100">
                    <p class="text-gray-500">No school fees configured yet.</p>
                </div>
            @endif
        </div>

        <!-- Section 2: Other Fees/Payments -->
        <div>
            <div class="flex items-center mb-4">
                <div class="bg-purple-100 p-2 rounded-lg mr-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-800">Other Fees & Payments</h2>
            </div>

            @if($otherFees->count() > 0)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Context</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($otherFees as $fee)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $fee->name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 capitalize">
                                            {{ str_replace('_', ' ', $fee->payment_type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900">₵{{ number_format($fee->amount, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-500">
                                            @if($fee->academic_group_id)
                                                {{ $fee->academicGroup->name }}
                                                @if($fee->academic_level_id) > {{ $fee->academicLevel->name }} @endif
                                            @else
                                                Global
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <button wire:click="edit({{ $fee->id }})" class="text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 p-1 rounded">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button wire:click="delete({{ $fee->id }})" wire:confirm="Are you sure?" class="text-red-600 hover:text-red-900 hover:bg-red-50 p-1 rounded">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-2xl shadow p-8 text-center border border-gray-100">
                    <p class="text-gray-500">No other fees configured.</p>
                </div>
            @endif
        </div>

        <!-- Form Modal -->
        @if($showFormModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xl font-semibold text-white">
                                    {{ $formMode === 'create' ? 'Add New Payment' : 'Edit Payment' }}
                                </h3>
                                <button wire:click="closeModal" class="text-white hover:text-gray-200 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <form wire:submit.prevent="save" class="px-6 py-6 space-y-6 max-h-[80vh] overflow-y-auto">

                            <!-- Basic Information -->
                            <div class="border-b border-gray-200 pb-6">
                                <h4 class="text-sm uppercase tracking-wide text-gray-500 font-bold mb-4">Basic Details</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
                                        <input type="text" wire:model="name" placeholder="e.g., Term 1 Tuition" class="block w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Payment Type Selection -->
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                                            Payment Type <span class="text-red-500">*</span>
                                        </label>
                                        
                                        <!-- Toggle between predefined and custom -->
                                        <div class="flex items-center space-x-6 mb-4 p-3 bg-gray-50 rounded-lg">
                                            <label class="flex items-center cursor-pointer">
                                                <input type="radio" wire:model.live="use_custom_payment_type" value="0" 
                                                       class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                                <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Use Predefined Types</span>
                                            </label>
                                            <label class="flex items-center cursor-pointer">
                                                <input type="radio" wire:model.live="use_custom_payment_type" value="1" 
                                                       class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                                <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Create Custom Type</span>
                                            </label>
                                        </div>

                                        <!-- Predefined Payment Types -->
                                        @if(!$use_custom_payment_type)
                                            <select wire:model="payment_type"
                                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('payment_type') border-red-500 @enderror">
                                                <option value="">Select Payment Type</option>
                                                @foreach($paymentTypes as $key => $label)
                                                    <option value="{{ $key }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('payment_type')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        @else
                                            <!-- Custom Payment Type Input -->
                                            <input type="text" wire:model="custom_payment_type"
                                                   placeholder="e.g., Sports Fee, Exam Fee, Lab Fee, Development Levy"
                                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('custom_payment_type') border-red-500 @enderror">
                                            @error('custom_payment_type')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        @endif
                                    </div>

                                    <!-- Account Selection -->
                                    <div class="md:col-span-2">
                                        <label for="subaccount_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Receiving Account <span class="text-amber-600 text-xs">(Optional - uses primary if not selected)</span>
                                        </label>
                                        <select wire:model="subaccount_id" id="subaccount_id"
                                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('subaccount_id') border-red-500 @enderror">
                                            <option value="">-- Use Primary Account --</option>
                                            @forelse($schoolSubaccounts as $account)
                                                <option value="{{ $account['id'] }}">
                                                    {{ $account['label'] }} - {{ $account['bank'] }} ({{ $account['account_number'] }})
                                                </option>
                                            @empty
                                                <option disabled>No payment accounts available</option>
                                            @endforelse
                                        </select>
                                        @error('subaccount_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        @if(count($schoolSubaccounts) === 0)
                                            <p class="mt-2 text-sm text-amber-600">
                                                ⚠️ No additional payment accounts configured. Payments will go to the school's primary account.
                                            </p>
                                        @endif
                                    </div>

                                    <!-- Amount -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount (GHS) <span class="text-red-500">*</span></label>
                                        <input type="number" wire:model="amount" step="0.01" class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('amount') border-red-500 @enderror">
                                        @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                                        <input type="date" wire:model="due_date" class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                        @error('due_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Period</label>
                                        <select wire:model="payment_period" class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">Select Period</option>
                                            @foreach($paymentPeriods as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Academic Context -->
                            <div class="border-b border-gray-200 pb-6">
                                <h4 class="text-sm uppercase tracking-wide text-gray-500 font-bold mb-4">Academic Context</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                    <!-- Academic Year -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Academic Year</label>
                                        @if($academicYears->isNotEmpty())
                                            <select wire:model.live="academic_year_id" class="block w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="">All Years</option>
                                                @foreach($academicYears as $year)
                                                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                                <p class="text-xs text-gray-500 mb-2">Create New Year:</p>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <input type="date" wire:model="new_year_start_date" class="text-xs rounded border-gray-300">
                                                    <input type="date" wire:model="new_year_end_date" class="text-xs rounded border-gray-300">
                                                </div>
                                                @error('new_year_start_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Academic Period -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Academic Period</label>
                                        @if(!empty($academicPeriods))
                                            <select wire:model="academic_period_id" class="block w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="">All Periods</option>
                                                @foreach($academicPeriods as $period)
                                                    <option value="{{ $period->id }}">{{ $period->name }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 space-y-2">
                                                <p class="text-xs text-gray-500">Create New Period:</p>
                                                <input type="text" wire:model="new_period_name" placeholder="Name" class="w-full text-xs rounded border-gray-300">
                                                <div class="grid grid-cols-2 gap-2">
                                                    <select wire:model="new_period_type" class="text-xs rounded border-gray-300">
                                                        <option value="term">Term</option>
                                                        <option value="semester">Semester</option>
                                                    </select>
                                                    <input type="number" wire:model="new_period_sequence" placeholder="Seq" class="text-xs rounded border-gray-300">
                                                </div>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <input type="date" wire:model="new_period_start_date" class="text-xs rounded border-gray-300">
                                                    <input type="date" wire:model="new_period_end_date" class="text-xs rounded border-gray-300">
                                                </div>
                                                @error('new_period_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Academic Group -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Academic Group</label>
                                        <select wire:model.live="academic_group_id" class="block w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">All Groups</option>
                                            @foreach($academicGroups as $group)
                                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Academic Level -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Academic Level</label>
                                        <select wire:model="academic_level_id" class="block w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">All Levels</option>
                                            @foreach($academicLevels as $level)
                                                <option value="{{ $level->id }}">{{ $level->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Settings & Description -->
                            <div>
                                <div class="flex space-x-6 mb-4">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" wire:model="is_mandatory" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-indigo-600 dark:text-indigo-500 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-600">Mandatory</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" wire:model.live="allow_partial_payment" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-indigo-600 dark:text-indigo-500 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-600">Allow Partial</span>
                                    </label>
                                </div>

                                @if($allow_partial_payment)
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Min. Partial Amount</label>
                                        <input type="number" wire:model="minimum_partial_amount" step="0.01" class="block w-1/2 rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                @endif

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                    <textarea wire:model="description" rows="3" class="block w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                </div>
                            </div>

                            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                                <button type="button" wire:click="closeModal" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">
                                    {{ $formMode === 'create' ? 'Create Payment' : 'Update Payment' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- View Modal -->
        @if($viewingFee)
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
                 aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                         wire:click="closeViewModal"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div
                        class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                        <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xl font-semibold text-white">Fee Structure Details</h3>
                                <button wire:click="closeViewModal"
                                        class="text-white hover:text-gray-200 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="px-6 py-6">
                            <dl class="grid grid-cols-1 gap-6">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Academic Year</dt>
                                    <dd class="mt-1 text-lg font-semibold text-gray-900">
                                        {{ $viewingFee->currentTerm->academicYear?->getDisplayName() ?? 'N/A' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Academic Group</dt>
                                    <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $viewingFee->academicGroup->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Academic Level</dt>
                                    <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $viewingFee->academicLevel->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Term/Period</dt>
                                    <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $viewingFee->currentTerm->getDisplayName() }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Amount</dt>
                                    <dd class="mt-1 text-2xl font-bold text-indigo-600">{{ $viewingFee->formatted_amount }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Due Date</dt>
                                    <dd class="mt-1 text-lg text-gray-900">{{ $viewingFee->formatted_due_date }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                                    <dd class="mt-1">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                            {{ $viewingFee->payment_method }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>

                            <div class="mt-8 flex justify-end">
                                <button
                                    wire:click="closeViewModal"
                                    class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-sm">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif


{{--
        <x-modal-component name="fee-structure-form">
            <x-slot:header>
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold">
                        {{ $formMode === 'create' ? 'Add New Fee Structure' : 'Edit Fee Structure' }}
                    </h3>
                </div>
            </x-slot:header>

            <form wire:submit.prevent="save" class="space-y-6">
                <div class="space-y-6">
                    <!-- Academic Year -->
                    <div class="form-group">
                        <label for="academic_year_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            Academic Year <span class="text-red-500">*</span>
                        </label>
                        <select
                            wire:model.live="academic_year_id"
                            id="academic_year_id"
                            class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 @error('academic_year_id') border-red-500 @enderror">
                            <option value="">Select Academic Year</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}">
                                    {{ $year->getDisplayName() }}
                                    @if($year->is_current)
                                        (Current)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('academic_year_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if(empty($academicYears) || count($academicYears) === 0)
                            <p class="mt-2 text-sm text-amber-600">
                                ⚠️ No academic years found. Please create an academic year first in School Settings.
                            </p>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Academic Group -->
                        <div class="form-group">
                            <label for="academic_group_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                Academic Group <span class="text-red-500">*</span>
                            </label>
                            <select
                                wire:model.live="academic_group_id"
                                id="academic_group_id"
                                class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 @error('academic_group_id') border-red-500 @enderror">
                                <option value="">Select Academic Group</option>
                                @foreach($academicGroups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                            @error('academic_group_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Academic Level -->
                        <div class="form-group">
                            <label for="academic_level_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                Academic Level <span class="text-red-500">*</span>
                            </label>
                            <select
                                wire:model="academic_level_id"
                                id="academic_level_id"
                                class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 @error('academic_level_id') border-red-500 @enderror"
                                {{ empty($academicLevels) ? 'disabled' : '' }}>
                                <option
                                    value="">{{ empty($academic_group_id) ? 'Select group first' : 'Select Academic Level' }}</option>
                                @foreach($academicLevels as $level)
                                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                            @error('academic_level_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Term -->
                        <div class="form-group">
                            <label for="current_term_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                Academic Period/Term <span class="text-red-500">*</span>
                            </label>
                            <select
                                wire:model="current_term_id"
                                id="current_term_id"
                                class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 @error('current_term_id') border-red-500 @enderror"
                                {{ empty($academicPeriods) ? 'disabled' : '' }}>
                                <option
                                    value="">{{ empty($academic_year_id) ? 'Select year first' : 'Select Term/Period' }}</option>
                                @foreach($academicPeriods as $period)
                                    <option value="{{ $period->id }}">
                                        {{ $period->getDisplayName() }}
                                        @if($period->is_current)
                                            (Current)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('current_term_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Amount -->
                        <div class="form-group">
                            <label for="amount" class="block text-sm font-semibold text-gray-700 mb-2">
                                Amount (₵) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative rounded-xl shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                    <span class="text-gray-500 text-lg">₵</span>
                                </div>
                                <input
                                    type="number"
                                    wire:model="amount"
                                    id="amount"
                                    step="0.01"
                                    placeholder="0.00"
                                    class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 @error('amount') border-red-500 @enderror">
                            </div>
                            @error('amount')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Due Date -->
                        <div class="form-group">
                            <label for="due_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                Due Date <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="date"
                                wire:model="due_date"
                                id="due_date"
                                class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 @error('due_date') border-red-500 @enderror">
                            @error('due_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </form>
            <x-slot:footer>

                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <x-button.white type="button" onclick="window.Modal.close('fee-structure-form')">
                        Cancel
                    </x-button.white>

                    <x-button class="primary" type="submit" form="fee-payment-form">
                        <x-slot:icon>
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 13l4 4L19 7"/>
                            </svg>
                        </x-slot:icon>
                        <span>  {{ $formMode === 'create' ? 'Create' : 'Update' }} Fee Structure</span>
                    </x-button>

                </div>
            </x-slot:footer>
        </x-modal-component>

        <x-modal-component name="new-payment-form">
            <x-slot:header>

            </x-slot:header>

            <x-slot:footer>

                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <x-button.white type="button" onclick="window.Modal.close('fee-structure-form')">
                        Cancel
                    </x-button.white>

                    <x-button class="primary" type="submit" form="new-payment-form">
                        <x-slot:icon>
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 13l4 4L19 7"/>
                            </svg>
                        </x-slot:icon>
                        <span>  {{ $formMode === 'create' ? 'Create' : 'Update' }} Payment</span>
                    </x-button>

                </div>
            </x-slot:footer>
        </x-modal-component>

        <x-modal-component name="fee-structure-details" title="Fee Structure Details">
            <div class="px-6 py-6">
                <dl class="grid grid-cols-1 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Academic Year</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">
                            <span
                                x-text="modalData?.viewingFee?.currentTerm?.academicYear?.getDisplayName || 'N/A'"></span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Academic Group</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">
                            <span x-text="modalData?.viewingFee?.academicGroup?.name || 'N/A'"></span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Academic Level</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">
                            <span x-text="modalData?.viewingFee?.academicLevel?.name || 'N/A'"></span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Term/Period</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">
                            <span x-text="modalData?.viewingFee?.currentTerm?.display_name || 'N/A'"></span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Amount</dt>
                        <dd class="mt-1 text-2xl font-bold text-indigo-600">
                            <span x-text="modalData?.viewingFee?.formatted_amount || '₵0.00'"></span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Due Date</dt>
                        <dd class="mt-1 text-lg text-gray-900">
                            <span x-text="modalData?.viewingFee?.formatted_due_date || 'N/A'"></span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                        <dd class="mt-1">
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    <span x-text="modalData?.viewingFee?.payment_method || 'N/A'"></span>
                </span>
                        </dd>
                    </div>
                </dl>
            </div>
        </x-modal-component>
--}}

    </div>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
</div>
