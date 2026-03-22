<x-layouts.app title="Edit Payroll Entry" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Payroll' => route('payroll.entries.index'),
            'Entries' => route('payroll.entries.index'),
            'Edit' => null,
        ]" />
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-600 to-orange-600 px-8 py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-2xl font-bold text-white mb-1">Edit Payroll Entry</h2>
                        <p class="text-orange-100">Update employee payroll information</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('payroll.entries.update', $entry) }}" class="p-8">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Employee Information</h3>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <x-form.select name="user_id" label="Link to System User (Optional)">
                                <option value="">-- Select User --</option>
                                @foreach($systemUsers as $user)
                                    <option value="{{ $user->id }}" @selected(old('user_id', $entry->user_id) == $user->id)>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </x-form.select>

                            <x-form.select name="payroll_role_id" label="Payroll Role (Optional)">
                                <option value="">-- Select Role --</option>
                                @foreach($payrollRoles as $role)
                                    <option value="{{ $role->id }}" @selected(old('payroll_role_id', $entry->payroll_role_id) == $role->id)>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </x-form.select>

                            <x-form.input name="first_name" label="First Name" :value="old('first_name', $entry->first_name)" required />
                            <x-form.input name="last_name" label="Last Name" :value="old('last_name', $entry->last_name)" required />
                            <x-form.input name="email" type="email" label="Email" :value="old('email', $entry->email)" />
                            <x-form.input name="phone" label="Phone" :value="old('phone', $entry->phone)" />
                            <x-form.input name="gross_salary" type="number" step="0.01" label="Gross Salary (GH₵)" :value="old('gross_salary', $entry->gross_salary)" required />
                            
                            <x-form.select name="status" label="Status" required>
                                <option value="active" @selected(old('status', $entry->status) === 'active')>Active</option>
                                <option value="inactive" @selected(old('status', $entry->status) === 'inactive')>Inactive</option>
                                <option value="suspended" @selected(old('status', $entry->status) === 'suspended')>Suspended</option>
                            </x-form.select>
                        </div>
                    </div>

                    @if($entry->bankAccount)
                        <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-6 border border-green-200 dark:border-green-800">
                            <h3 class="text-lg font-semibold text-green-900 dark:text-green-100 mb-4">Bank Account</h3>
                            <div class="grid md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-green-700 dark:text-green-300 font-medium">Bank:</span>
                                    <span class="text-green-900 dark:text-green-100 ml-2">{{ $entry->bankAccount->bank_name }}</span>
                                </div>
                                <div>
                                    <span class="text-green-700 dark:text-green-300 font-medium">Account:</span>
                                    <span class="text-green-900 dark:text-green-100 ml-2">{{ $entry->bankAccount->account_number }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('payroll.entries.destroy', $entry) }}" onsubmit="return confirm('Are you sure you want to delete this entry?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Delete Entry
                        </button>
                    </form>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('payroll.entries.index') }}" class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                            Cancel
                        </a>
                        <button type="submit" class="px-8 py-3 bg-amber-600 text-white rounded-lg hover:bg-amber-700">
                            Update Entry
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
