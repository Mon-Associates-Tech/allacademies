<x-layouts.app title="Payroll Schedules" page-name="Payroll Schedules">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="['Payroll' => null, 'Schedules' => null]" />
    </x-slot>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <x-academic-header>
            <x-slot:headerIcon>
                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </x-slot:headerIcon>

            <x-slot name="headerContent">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">Payroll Schedules</h1>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Manage payment schedules and frequencies</p>
                </div>
            </x-slot>

            <x-slot name="headerActions">
                <a href="{{ route('payroll.schedules.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    New Schedule
                </a>
            </x-slot>
        </x-academic-header>

        <div class="p-6">
            @livewire('payroll.schedule-index')
        </div>
    </div>
</x-layouts.app>
