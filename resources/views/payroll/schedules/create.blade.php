<x-layouts.app title="New Payroll Schedule" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Payroll' => route('payroll.entries.index'),
            'Schedules' => route('payroll.schedules.index'),
        ]" />
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-8 py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-2xl font-bold text-white mb-1">Create Payroll Schedule</h2>
                        <p class="text-green-100">Set up a new payment schedule</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('payroll.schedules.store') }}" class="p-8">
                @csrf

                <div class="space-y-6">
                    <x-form.input name="name" label="Schedule Name" placeholder="e.g., Monthly Salary - January 2024" required />
                    
                    <x-form.select name="frequency" label="Frequency" required>
                        <option value="one_time">One Time</option>
                        <option value="monthly" selected>Monthly</option>
                        <option value="weekly">Weekly</option>
                        <option value="bi_weekly">Bi-Weekly</option>
                        <option value="quarterly">Quarterly</option>
                    </x-form.select>

                    <x-form.input name="run_date" type="date" label="Run Date" :value="now()->format('Y-m-d')" required />
                </div>

                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('payroll.schedules.index') }}" class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Create Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
