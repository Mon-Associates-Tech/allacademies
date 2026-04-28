<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Payment Setup</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Configure fee structures for academic groups and levels
                </p>
            </div>
            <button wire:click="showCreateForm" 
                    class="px-4 py-2 bg-violet-600 text-white rounded-md hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-violet-500">
                <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Create Fee Structure
            </button>
        </div>
    </div>

    <!-- Fee Structures List -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
        @if($structures->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Applies To</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($structures as $structure)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $structure->name }}</div>
                                @if($structure->description)
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($structure->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                    {{ ucfirst($structure->payment_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                GHS {{ number_format($structure->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                @if($structure->academicGroup)
                                    {{ $structure->academicGroup->name }}
                                    @if($structure->academicLevel)
                                        - {{ $structure->academicLevel->name }}
                                    @endif
                                @else
                                    All Students
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $structure->due_date ? $structure->due_date->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($structure->is_active)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">Active</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button wire:click="edit({{ $structure->id }})" 
                                        class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                    Edit
                                </button>
                                <button wire:click="delete({{ $structure->id }})" 
                                        onclick="return confirm('Are you sure you want to delete this fee structure?')"
                                        class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $structures->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No fee structures</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new fee structure.</p>
            </div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 overflow-y-auto">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4 my-8">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ $editingId ? 'Edit Fee Structure' : 'Create Fee Structure' }}
                    </h3>
                </div>
                <form wire:submit.prevent="save" class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name *</label>
                            <input type="text" wire:model="name" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" required>
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Type *</label>
                            <select wire:model.live="paymentType" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" required>
                                <option value="">Select Type</option>
                                @foreach($paymentTypes as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                                <option value="custom">Custom Type</option>
                            </select>
                            @error('paymentType') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        @if($paymentType === 'custom')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Custom Type Name *</label>
                                <input type="text" wire:model="customPaymentType" 
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" required>
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Amount (GHS) *</label>
                            <input type="number" step="0.01" wire:model="amount" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" required>
                            @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Due Date</label>
                            <input type="date" wire:model="dueDate"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                            @error('dueDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Academic Year</label>
                            <select wire:model="academicYearId" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                <option value="">All Years</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}">{{ $year->getDisplayName() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Academic Period</label>
                            <select wire:model="academicPeriodId" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                <option value="">All Periods</option>
                                @foreach($academicPeriods as $period)
                                    <option value="{{ $period->id }}">{{ $period->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Academic Group</label>
                            <select wire:model="academicGroupId" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                <option value="">All Groups</option>
                                @foreach($academicGroups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Academic Level</label>
                            <select wire:model="academicLevelId" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                <option value="">All Levels</option>
                                @foreach($academicLevels as $level)
                                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                            <textarea wire:model="description" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"></textarea>
                        </div>

                        <div class="md:col-span-2 space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="isMandatory" class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Mandatory Payment</span>
                            </label>

                            <label class="flex items-center">
                                <input type="checkbox" wire:model.live="allowPartialPayment" class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Allow Partial Payment</span>
                            </label>

                            @if($allowPartialPayment)
                                <div class="ml-6">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Minimum Partial Amount</label>
                                    <input type="number" step="0.01" wire:model="minimumPartialAmount"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                </div>
                            @endif

                            <label class="flex items-center">
                                <input type="checkbox" wire:model="isActive" class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="submit" class="flex-1 px-4 py-2 bg-violet-600 text-white rounded-md hover:bg-violet-700">
                            {{ $editingId ? 'Update' : 'Create' }} Fee Structure
                        </button>
                        <button type="button" wire:click="$set('showModal', false)" 
                                class="flex-1 px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
