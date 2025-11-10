<x-layouts.app>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit Fee Structure') }}
            </h2>
            <a href="{{ route('admin.school-payment-structures.show', $feeStructure) }}"
               class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <form method="POST" action="{{ route('admin.school-payment-structures.update', $feeStructure) }}" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                        <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-400 p-4 rounded">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <ul class="list-disc list-inside text-sm">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Basic Information -->
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Fee Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="name"
                                       id="name"
                                       value="{{ old('name', $feeStructure->name) }}"
                                       required
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-violet-500 focus:border-violet-500">
                            </div>

                            <!-- Payment Type -->
                            <div>
                                <label for="payment_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Payment Type <span class="text-red-500">*</span>
                                </label>
                                <select name="payment_type"
                                        id="payment_type"
                                        required
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-violet-500 focus:border-violet-500">
                                    @foreach($payment_types as $key => $label)
                                        <option value="{{ $key }}" {{ old('payment_type', $feeStructure->payment_type) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Amount -->
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Amount (GHS) <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                       name="amount"
                                       id="amount"
                                       value="{{ old('amount', $feeStructure->amount) }}"
                                       step="0.01"
                                       min="0"
                                       required
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-violet-500 focus:border-violet-500">
                            </div>

                            <!-- Due Date -->
                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Due Date
                                </label>
                                <input type="date"
                                       name="due_date"
                                       id="due_date"
                                       value="{{ old('due_date', $feeStructure->due_date?->format('Y-m-d')) }}"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-violet-500 focus:border-violet-500">
                            </div>

                            <!-- Payment Period -->
                            <div>
                                <label for="payment_period" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Payment Period
                                </label>
                                <select name="payment_period"
                                        id="payment_period"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-violet-500 focus:border-violet-500">
                                    <option value="">Select period</option>
                                    @foreach($payment_periods as $key => $label)
                                        <option value="{{ $key }}" {{ old('payment_period', $feeStructure->payment_period) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Context -->
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Academic Context</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Academic Year -->
                            <div>
                                <label for="academic_year_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Academic Year
                                </label>
                                <select name="academic_year_id"
                                        id="academic_year_id"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-violet-500 focus:border-violet-500">
                                    <option value="">All Years</option>
                                    @foreach($academic_years as $year)
                                        <option value="{{ $year->id }}" {{ old('academic_year_id', $feeStructure->academic_year_id) == $year->id ? 'selected' : '' }}>
                                            {{ $year->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Academic Period -->
                            <div>
                                <label for="academic_period_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Academic Period
                                </label>
                                <select name="academic_period_id"
                                        id="academic_period_id"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-violet-500 focus:border-violet-500">
                                    <option value="">All Periods</option>
                                    @foreach($academic_periods as $period)
                                        <option value="{{ $period->id }}" {{ old('academic_period_id', $feeStructure->academic_period_id) == $period->id ? 'selected' : '' }}>
                                            {{ $period->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Academic Group -->
                            <div>
                                <label for="academic_group_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Academic Group
                                </label>
                                <select name="academic_group_id"
                                        id="academic_group_id"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-violet-500 focus:border-violet-500">
                                    <option value="">All Groups</option>
                                    @foreach($academic_groups as $group)
                                        <option value="{{ $group->id }}" {{ old('academic_group_id', $feeStructure->academic_group_id) == $group->id ? 'selected' : '' }}>
                                            {{ $group->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Academic Level -->
                            <div>
                                <label for="academic_level_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Academic Level
                                </label>
                                <select name="academic_level_id"
                                        id="academic_level_id"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-violet-500 focus:border-violet-500">
                                    <option value="">All Levels</option>
                                    @foreach($academic_levels as $level)
                                        <option value="{{ $level->id }}" {{ old('academic_level_id', $feeStructure->academic_level_id) == $level->id ? 'selected' : '' }}>
                                            {{ $level->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Settings -->
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Settings</h3>

                        <div class="space-y-4">
                            <!-- Is Active -->
                            <div class="flex items-center">
                                <input type="checkbox"
                                       name="is_active"
                                       id="is_active"
                                       value="1"
                                       {{ old('is_active', $feeStructure->is_active) ? 'checked' : '' }}
                                       class="rounded border-gray-300 dark:border-gray-600 text-violet-600 focus:ring-violet-500 dark:bg-gray-700">
                                <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    This fee structure is active
                                </label>
                            </div>

                            <!-- Is Mandatory -->
                            <div class="flex items-center">
                                <input type="checkbox"
                                       name="is_mandatory"
                                       id="is_mandatory"
                                       value="1"
                                       {{ old('is_mandatory', $feeStructure->is_mandatory) ? 'checked' : '' }}
                                       class="rounded border-gray-300 dark:border-gray-600 text-violet-600 focus:ring-violet-500 dark:bg-gray-700">
                                <label for="is_mandatory" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    This is a mandatory fee
                                </label>
                            </div>

                            <!-- Allow Partial Payment -->
                            <div class="flex items-center">
                                <input type="checkbox"
                                       name="allow_partial_payment"
                                       id="allow_partial_payment"
                                       value="1"
                                       {{ old('allow_partial_payment', $feeStructure->allow_partial_payment) ? 'checked' : '' }}
                                       class="rounded border-gray-300 dark:border-gray-600 text-violet-600 focus:ring-violet-500 dark:bg-gray-700">
                                <label for="allow_partial_payment" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    Allow partial payments
                                </label>
                            </div>

                            <!-- Minimum Partial Amount -->
                            <div id="partial_amount_field" style="display: {{ old('allow_partial_payment', $feeStructure->allow_partial_payment) ? 'block' : 'none' }};">
                                <label for="minimum_partial_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Minimum Partial Amount (GHS)
                                </label>
                                <input type="number"
                                       name="minimum_partial_amount"
                                       id="minimum_partial_amount"
                                       value="{{ old('minimum_partial_amount', $feeStructure->minimum_partial_amount) }}"
                                       step="0.01"
                                       min="0"
                                       class="w-full md:w-1/2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-violet-500 focus:border-violet-500">
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Description
                        </label>
                        <textarea name="description"
                                  id="description"
                                  rows="3"
                                  class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-violet-500 focus:border-violet-500">{{ old('description', $feeStructure->description) }}</textarea>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('admin.school-payment-structures.show', $feeStructure) }}"
                           class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-6 py-2 bg-violet-600 hover:bg-violet-700 text-white rounded-lg">
                            Update Fee Structure
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Show/hide minimum partial amount field
        document.getElementById('allow_partial_payment').addEventListener('change', function() {
            const partialField = document.getElementById('partial_amount_field');
            const partialInput = document.getElementById('minimum_partial_amount');

            if (this.checked) {
                partialField.style.display = 'block';
                partialInput.setAttribute('required', 'required');
            } else {
                partialField.style.display = 'none';
                partialInput.removeAttribute('required');
            }
        });
    </script>
</x-layouts.app>
