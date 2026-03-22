<x-layouts.app title="New Payroll Role" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Payroll' => route('payroll.entries.index'),
            'Roles' => route('payroll.roles.index'),
        ]" />
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-8 py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-2xl font-bold text-white mb-1">Create Payroll Role</h2>
                        <p class="text-purple-100">Define a new employee role classification</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('payroll.roles.store') }}" class="p-8">
                @csrf

                <div class="space-y-6">
                    <x-form.input name="name" label="Role Name" placeholder="e.g., Teaching Staff, Administrative Staff" required />
                    <x-form.textarea name="description" label="Description" rows="4" placeholder="Describe this role and its responsibilities..." />
                </div>

                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('payroll.roles.index') }}" class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        Create Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
