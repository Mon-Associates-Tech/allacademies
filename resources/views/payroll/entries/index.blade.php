<x-layouts.app>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Payroll Entries
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Staff Payroll Management
                        </h3>
                        <a href="{{ route('payroll.entries.create') }}" 
                           class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                            Add Payroll Entry
                        </a>
                    </div>

                    @livewire('payroll.entry-index')
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
