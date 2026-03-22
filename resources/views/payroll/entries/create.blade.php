<x-layouts.app title="New Payroll Entry" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Payroll' => route('payroll.entries.index'),
            'Entries' => route('payroll.entries.index'),
        ]" />
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-2xl font-bold text-white mb-1">Create Payroll Entry</h2>
                        <p class="text-blue-100">Add a new employee to the payroll system</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('payroll.entries.store') }}" class="p-8">
                @csrf

                <div class="space-y-6">
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Employee Information</h3>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <x-form.select name="user_id" label="Link to System User (Optional)">
                                <option value="">-- Select User --</option>
                                @foreach($systemUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </x-form.select>

                            <x-form.select name="payroll_role_id" label="Payroll Role (Optional)">
                                <option value="">-- Select Role --</option>
                                @foreach($payrollRoles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </x-form.select>

                            <x-form.input name="first_name" label="First Name" required />
                            <x-form.input name="last_name" label="Last Name" required />
                            <x-form.input name="email" type="email" label="Email" />
                            <x-form.input name="phone" label="Phone" />
                            <x-form.input name="gross_salary" type="number" step="0.01" label="Gross Salary (GH₵)" required />
                            
                            <x-form.select name="status" label="Status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="suspended">Suspended</option>
                            </x-form.select>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('payroll.entries.index') }}" class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Create Entry
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
