<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="mx-auto">
        <!-- Header Section -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Payment Management</h1>
                <p class="mt-2 text-sm text-gray-600">Manage school fee structures for different academic levels and
                    terms</p>
            </div>
            <button
                wire:click="showCreateForm"
                class="inline-flex items-center px-6 py-3 border border-transparent rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add New Fee Structure
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

        <!-- Fee Structures List -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Academic Year
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Academic Group
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Academic Level
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Term/Period
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Amount
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Due Date
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($feeStructures as $fee)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $fee->currentTerm->academicYear?->getDisplayName() ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $fee->academicGroup->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $fee->academicLevel->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $fee->currentTerm->getDisplayName() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ $fee->formatted_amount }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $fee->formatted_due_date }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <button
                                        wire:click="view({{ $fee->id }})"
                                        class="inline-flex items-center p-2 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition-colors"
                                        title="View">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <button
                                        wire:click="edit({{ $fee->id }})"
                                        class="inline-flex items-center p-2 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded-lg transition-colors"
                                        title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button
                                        wire:click="delete({{ $fee->id }})"
                                        wire:confirm="Are you sure you want to delete this fee structure?"
                                        class="inline-flex items-center p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No fee structures</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by creating a new fee structure.</p>
                                <div class="mt-6">
                                    <button
                                        wire:click="showCreateForm"
                                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Add Fee Structure
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($feeStructures->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $feeStructures->links() }}
                </div>
            @endif
        </div>

        <!-- Form Modal -->
        @if($showFormModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
                 aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                         wire:click="closeModal"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div
                        class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xl font-semibold text-white">
                                    {{ $formMode === 'create' ? 'Add New Fee Structure' : 'Edit Fee Structure' }}
                                </h3>
                                <button wire:click="closeModal"
                                        class="text-white hover:text-gray-200 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <form wire:submit.prevent="save" class="px-6 py-6 space-y-6">
                            <div class="space-y-6">
                                <!-- Academic Year -->
                                <div class="form-group">
                                    <label for="academic_year_id"
                                           class="block text-sm font-semibold text-gray-700 mb-2">
                                        Academic Year <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        wire:model.live="academic_year_id"
                                        id="academic_year_id"
                                        class="block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 @error('academic_year_id') border-red-500 @enderror">
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
                                            ⚠️ No academic years found. Please create an academic year first in School
                                            Settings.
                                        </p>
                                    @endif
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Academic Group -->
                                    <div class="form-group">
                                        <label for="academic_group_id"
                                               class="block text-sm font-semibold text-gray-700 mb-2">
                                            Academic Group <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            wire:model.live="academic_group_id"
                                            id="academic_group_id"
                                            class="block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 @error('academic_group_id') border-red-500 @enderror">
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
                                        <label for="academic_level_id"
                                               class="block text-sm font-semibold text-gray-700 mb-2">
                                            Academic Level <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            wire:model="academic_level_id"
                                            id="academic_level_id"
                                            class="block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 @error('academic_level_id') border-red-500 @enderror"
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
                                        <label for="current_term_id"
                                               class="block text-sm font-semibold text-gray-700 mb-2">
                                            Academic Period/Term <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            wire:model="current_term_id"
                                            id="current_term_id"
                                            class="block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 @error('current_term_id') border-red-500 @enderror"
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
                                            <div
                                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
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
                                            class="block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 @error('due_date') border-red-500 @enderror">
                                        @error('due_date')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                                <button
                                    type="button"
                                    wire:click="closeModal"
                                    class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-sm">
                                    Cancel
                                </button>

                                <button
                                    type="submit"
                                    class="inline-flex items-center px-8 py-3 border border-transparent rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                    {{ $formMode === 'create' ? 'Create' : 'Update' }} Fee Structure
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
                                    class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-sm">
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
                            class="block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 @error('academic_year_id') border-red-500 @enderror">
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
                                class="block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 @error('academic_group_id') border-red-500 @enderror">
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
                                class="block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 @error('academic_level_id') border-red-500 @enderror"
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
                                class="block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 @error('current_term_id') border-red-500 @enderror"
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
                                class="block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 @error('due_date') border-red-500 @enderror">
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
