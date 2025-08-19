<div class="p-6 bg-white rounded-lg shadow-lg">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Academic Calendar Manager</h2>
    </div>

    <!-- Create Academic Period Form -->
    <div class="bg-gray-50 p-6 rounded-lg mb-6">
        <h3 class="text-lg font-semibold mb-4">Create New Academic Period</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" wire:model="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Type</label>
                <select wire:model="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select Type</option>
                    <option value="semester">Semester</option>
                    <option value="term">Term</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Start Date</label>
                <input type="date" wire:model="startDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">End Date</label>
                <input type="date" wire:model="endDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Academic Year</label>
                <input type="text" wire:model="academicYear" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea wire:model="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            </div>

            <div class="flex items-center">
                <input type="checkbox" wire:model="isActive" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <label class="ml-2 text-sm text-gray-700">Is Active</label>
            </div>
        </div>

        <div class="mt-4">
            <button wire:click="createPeriod" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Create Period
            </button>
        </div>
    </div>

    <!-- Academic Periods List -->
    <div class="mt-6">
        <h3 class="text-lg font-semibold mb-4">Academic Periods</h3>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Academic Year</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($periods as $period)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $period->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap capitalize">{{ $period->type }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $period->start_date->format('M d, Y') }} - {{ $period->end_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $period->academic_year }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $period->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $period->is_active ? 'Active' : 'Inactive' }}
                                </span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $periods->links() }}
        </div>
    </div>
</div>
