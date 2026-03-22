<x-layouts.app title="Edit Payroll Schedule" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Payroll' => route('payroll.entries.index'),
            'Schedules' => route('payroll.schedules.index'),
            'Edit' => null,
        ]" />
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-600 to-orange-600 px-8 py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-2xl font-bold text-white mb-1">Edit Payroll Schedule</h2>
                        <p class="text-orange-100">Update schedule information</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('payroll.schedules.update', $schedule) }}" class="p-8">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <x-form.input name="name" label="Schedule Name" :value="old('name', $schedule->name)" required />
                    
                    <x-form.select name="frequency" label="Frequency" required>
                        <option value="one_time" @selected(old('frequency', $schedule->frequency) === 'one_time')>One Time</option>
                        <option value="monthly" @selected(old('frequency', $schedule->frequency) === 'monthly')>Monthly</option>
                        <option value="weekly" @selected(old('frequency', $schedule->frequency) === 'weekly')>Weekly</option>
                        <option value="bi_weekly" @selected(old('frequency', $schedule->frequency) === 'bi_weekly')>Bi-Weekly</option>
                        <option value="quarterly" @selected(old('frequency', $schedule->frequency) === 'quarterly')>Quarterly</option>
                    </x-form.select>

                    <x-form.input name="run_date" type="date" label="Run Date" :value="old('run_date', $schedule->run_date?->format('Y-m-d'))" required />

                    <x-form.select name="status" label="Status" required>
                        <option value="active" @selected(old('status', $schedule->status) === 'active')>Active</option>
                        <option value="paused" @selected(old('status', $schedule->status) === 'paused')>Paused</option>
                        <option value="completed" @selected(old('status', $schedule->status) === 'completed')>Completed</option>
                        <option value="cancelled" @selected(old('status', $schedule->status) === 'cancelled')>Cancelled</option>
                    </x-form.select>
                </div>

                <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('payroll.schedules.destroy', $schedule) }}" onsubmit="return confirm('Are you sure you want to cancel this schedule?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Cancel Schedule
                        </button>
                    </form>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('payroll.schedules.index') }}" class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                            Back
                        </a>
                        <button type="submit" class="px-8 py-3 bg-amber-600 text-white rounded-lg hover:bg-amber-700">
                            Update Schedule
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
